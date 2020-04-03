<?php
class Sender
{
    /**
     * funtion for querying orders from central data repo
     * By Kenneth Kapundi
     */
    public static function search_from_remote($trackingNumber){

        $ch = curl_init( Config::get('kblis.national-repo-node')."/query_results/".$trackingNumber);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($ch));
        return $result;
    }

    /*
     *
     */
    public static function get_name($order, $panels_only = false){
        $rawNames = $order->test_types;
        $panels = array();
        $to_negate = array();

        foreach($rawNames AS $name){
            $panel = PanelType::where('name', $name)->first();
            if($panel) {
                array_push($panels, $name);
                $to_negate = array_merge($to_negate, DB::table('panels')
                    ->join('test_types', 'test_types.id', '=', 'panels.test_type_id')
                    ->where('panel_type_id', $panel->id)
                    ->select('name')
                    ->lists('name'));
            }
        }
        if($panels_only){
            return $panels;
        }else {
            return array_unique(array_diff(array_merge($panels, $rawNames), $to_negate));
        }
    }
    /**
     * Function for sending updated results to couch layer
     *
     */
    public static function send_data($patient, $specimen, $tests=[])
    {
        $order = array(
            '_id' => $specimen->tracking_number,
            'sample_status' => SpecimenStatus::find($specimen->specimen_status_id)->name,
            'results' => array()
        );

        if (Config::get('kblis.nlims_controller') == true)
        {       $tracking_number = $order['_id'];
                $url      = config::get('kblis.nlims_controller_ip');           
                $version  = config::get('kblis.nlims_api_version');
                $username = config::get('kblis.nlims_custome_username');
                $password = config::get('kblis.nlims_custome_password');
                $token = File::get(public_path().'/token.txt');

                if(sizeof($tests) == 0){
                    $tests = Test::where('specimen_id', $specimen->id)->get();
                }
                $details = array();
                $results = array();
                foreach($tests AS $test){
                        $details['tracking_number'] = $tracking_number;
                        $test_name = $test->testType->name;                         
                        $who = Auth::user();
                        $name = explode(' ', $who->name);
               
                        $details['test_name'] = $test_name;
                        $details['test_status'] = $test->testStatus->name;
                        $details['time_update'] = '';

                        $updater = array(
                          'first_name' => isset($name[0]) ? $name[0]  : '',
                          'last_name' => isset($name[1]) ? $name[1]  : '',
                          'id_number' => $who->id);                        
                        $details['who_updated'] = $updater;

                        foreach ($test->testResults AS $result){
                            $measure = Measure::find($result->measure_id);
                            if($result->result) {
                                $results[$measure->name] =  $result->result . " " . $measure->unit;                 
                            }else{
                                $results[$measure->name] =  $result->result;
                            }
                        }

                        $details['results'] = $results;

                }

                    $data_string = json_encode($details);
                    $ch = curl_init($url.'/api/'.$version.'/'.'update_test/'.$token);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Accept: application/json',
                            'Content-Length: ' . strlen($data_string))
                    );

                    $response = json_decode(curl_exec($ch));
        }
        else {


            if(sizeof($tests) == 0){
                $tests = Test::where('specimen_id', $specimen->id)->get();
            }

            foreach($tests AS $test){

                $test_name = $test->testType->name;
                $order['results'][$test_name] = array();
                $h = array();
                $h['test_status'] = $test->testStatus->name;
                $h['remarks'] = $test->interpretation;
                $h['datetime_started'] = $test->time_started;
                $h['datetime_completed'] = $test->time_completed;

                $h['who_updated'] = array();
                $who = Auth::user();
                $name = explode(' ', $who->name);
                $h['who_updated']['first_name'] = isset($name[0]) ? $name[0]  : '';
                $h['who_updated']['last_name'] = isset($name[1]) ? $name[1]  : '';
                $h['who_updated']['ID_number'] = $who->id;

                $r = array();
                foreach ($test->testResults AS $result){
                    $measure = Measure::find($result->measure_id);
                    if($result->result) {
                        $r[$measure->name] = $result->result . " " . $measure->unit;
                    }else{
                        $r[$measure->name] = $result->result;
                    }
                }
                $h['results'] = $r;
                $order['results'][$test_name] = $h;
            }

            #$order =  urldecode(http_build_query($order));
            #dd($order);
            $data_string = json_encode($order);

            $ch = curl_init( Config::get('kblis.central-repo')."/pass_json/");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_string))
            );

            $response = json_decode(curl_exec($ch));
          
        }

    }

    public static function merge_or_create($tracking_number){
        $order = Sender::search_from_remote($tracking_number);
        $specimen = Specimen::where('tracking_number', $order->_id)->first();
        $patient = Patient::where('external_patient_number', $order->patient->national_patient_id)->first();

        if(!$patient){
            $patient = new Patient;
            $patient->external_patient_number = $order->patient->national_patient_id;
            $patient->name = $order->patient->first_name.' '.$order->patient->middle_name.' '.$order->patient->last_name;
            $patient->dob = date_create($order->patient->date_of_birth);
            $patient->gender = preg_match("/m/i", $order->patient->gender) ? 0 : 1;
            $patient->phone_number = $order->patient->phone_number;
            $patient->patient_number = DB::table('patients')->max('id')+1;
            $patient->save();
        }

        if(!$specimen){
            $specimen = new Specimen;
            $specimen->specimen_type_id = SpecimenType::where('name', $order->sample_type)->first()->id;
            $specimen->accession_number = Specimen::assignAccessionNumber();
            $specimen->tracking_number = $order->_id;
            $specimen->drawn_by_name = $order->who_order_test->first_name.' '.$order->who_order_test->last_name;
            $specimen->drawn_by_id = $order->who_order_test->id_number;
        }

        $specimen->specimen_status_id = SpecimenStatus::where('name', 'specimen-accepted')->first()->id;
        $specimen->accepted_by = Auth::user()->id;
        $specimen->time_accepted = $order->date_received ? $order->date_received : time();
        $specimen->save();

        $panels_available = Sender::get_name($order, true);
        $testPanel = new TestPanel;
        $panel = array();
        $panel_type = null;

        if(count($panels_available) > 0){
            $panel_type = PanelType::where('name', $panels_available[0])->first();
            $panel = Panel::where('panel_type_id', $panel_type->id)->lists('test_type_id');
            $testPanel->panel_type_id = $panel_type->id;
        }

        foreach($order->test_types AS $name) {
            $type = TestType::where('name', $name)->first();
            if (!$type) continue;

            $test = Test::where('specimen_id', $specimen->id)->where('test_type_id', $type->id)->first();
            if (!$test) {
                $test = new Test;
                $test->test_type_id = $type->id;
                $test->specimen_id = $specimen->id;
                $test->test_status_id = 2;
                $test->created_by = Auth::user()->id;
                $test->requested_by = $specimen->drawn_by_name;

                if ($panel_type && in_array($test->test_type_id, $panel)) {
                    $testPanel->save();
                    $test->panel_id = $testPanel->id;
                }
            }

            $visit = $test->visit;
            if (!$visit) {
                $visit = new Visit;
            }
            $visit->patient_id = $patient->id;
            if (!$visit->visit_type) {
                $visit->visit_type = VisitType::where('name', 'Referral')->first()->id;
            }
            $visit->ward_or_location = $order->order_location;
            $visit->save();

            $test->visit_id = $visit->id;
            $test->save();

            if (isset($order->results->$name)) {
                foreach ($order->results->$name as $timestamp => $results) {
                    if ($results->results) {
                        foreach ($results->results AS $measureName => $resultVal) {
                            $measure = Measure::where('name', $measureName)->first();

                            $result = TestResult::where('measure_id', $measure->id)->where('test_id', $test->id)->first();
                            if (!$result) {
                                $result = new TestResult;
                                $result->measure_id = $measure->id;
                                $result->test_id = $test->id;
                            }

                            $result->result = $resultVal;
                            $result->save();

                            $test->interpretation = $results->remarks;
                            $test->test_status_id = TestStatus::where('name', 'completed')->first()->id;
                            $test->time_started = $results->datetime_started;
                            $test->time_completed = $results->datetime_completed;
                            if (!$test->tested_by) {
                                $test->tested_by = User::first()->id;
                            }
                            $test->save();
                        }
                    }
                }
            }
        }

        return $specimen;
    }
}