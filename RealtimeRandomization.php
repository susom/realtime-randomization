<?php
namespace Stanford\RealtimeRandomization;

require_once "emLoggerTrait.php";

use REDCap;
use Randomization;
use Records;

class RealtimeRandomization extends \ExternalModules\AbstractExternalModule {

    use emLoggerTrait;

    public $triggerForm;
    public $triggerEvent;
    public $triggerLogic;

    public function __construct() {
		parent::__construct();
		// Other code to run when object is instantiated
	}

	public function load() {
        $this->triggerForm  = $this->getProjectSetting('trigger-form');
        $this->triggerEvent = $this->getProjectSetting('trigger-event');
        $this->triggerLogic = $this->getProjectSetting('trigger-logic');
    }


	public function redcap_save_record( $project_id, $record, $instrument, $event_id, $group_id,$survey_hash,$response_id,$repeat_instance=1) {
        global $Proj;

        // Make sure randomization is enabled
        if (! $Proj->project['randomization']) {
            REDCap::logEvent($this->getModuleName(),"Randomization is not enabled for this project.");
            return;
        }

        $this->load();

        // Validation
        if (!empty($this->triggerForm) && $this->triggerForm !== $instrument) {
            $this->emDebug("$this->triggerForm is not the same form as $instrument - skipping...");
            return;
        }
        if (!empty($this->triggerEvent) && $this->triggerEvent !== $event_id) {
            $this->emDebug("$this->triggerEvent is not the same event as $event_id - skipping...");
            return;
        }
        if (!empty($this->triggerLogic) &&
            // Do piping?
            !REDCap::evaluateLogic($this->triggerLogic,$project_id,$record,$event_id,$repeat_instance,"",$instrument)) {
            $this->emDebug("$this->triggerLogic evaluated as false - skipping...");
            return;
        }


        // Are they already randomized
        list($randField, $randValue) = Randomization::getRandomizedValue($record);
        if (!empty($randValue)) {
            $this->emDebug($record . " already randomized: " . $randValue);
            return;
        };


        // Get randomization setup values first
        $randAttr = Randomization::getRandomizationAttributes();
        $this->emDebug('randAttr', $randAttr);
        $targetField = $randAttr['targetField'];
        $targetEvent = $randAttr['targetEvent'];

        // Aggregate the criteria fields and their values into array
        $fields = array();
        foreach ($randAttr['strata'] as $field_name => $event_id) {
            $q = REDCap::getData($project_id,'array',$record, $field_name, $event_id);
            if (empty($q[$record][$event_id][$field_name])) {
                $this->emDebug("Missing required strata value for $field_name");
                return;
            } else {
                $fields[$field_name] = $q[$record][$event_id][$field_name];
            }
        }

        // Randomize and return aid key
        $aid = Randomization::randomizeRecord($record, $fields, $group_id);

        if ($aid === "0") {
            $this->emDebug("$record could not be randomized.  A matching unused allocation was not found.");
            REDCap::logEvent($this->getModuleName(), "Error randomizing $record -- unable to find unused allocation slot","",$record,$event_id);
            return;
        }
        if ($aid === false) {
            $this->emError("An error occurred randomizing record $record");
            return;
        }


        // Save the newly randomized value to the record
        list($randField, $randValue) = Randomization::getRandomizedValue($record);

        // I am unable to get the value to save using normal methods... So, I'm doing it manually
        $result = $this->query("SELECT * FROM redcap_data WHERE project_id=? and record=? and event_id=? and field_name=?", [$project_id, $record, $targetEvent, $targetField]);

        if ($result->num_rows > 0) {
            // Field exists
            $this->emError("It appears there is already a value for the random field.", $result->fetch_assoc());
            REDCap::logEvent($this->getModuleName(), "Error randomizing $record -- already has a value for $targetField", "", $record, $event_id);
        } else {
            // Do an insert
            $result = $this->query("INSERT INTO redcap_data (project_id, event_id, record, field_name, value) VALUES (? , ? , ? , ? , ?)", [$project_id, $event_id, $record, $targetField, $randValue]);
            $this->emDebug("Insert Result", $result);
            REDCap::logEvent($this->getModuleName(),"$targetField = '$randValue'",$result,$record, $event_id);
        }

        return true;
    }




}
