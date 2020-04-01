<?php

class ApiV1Controller extends \BaseController {

	public function getSpecimens()
	{
		$specimentypes = SpecimenType::select('id as specimen_id', 'name')->get();
		return Response::json(array (
			'error' => false,
			'data' => $specimentypes
		), 200);
	}

	public function getSpecimenTests($specimen_id)
	{
		$testtypes = DB::table('testtype_specimentypes')
						->select('test_type_id', 'test_types.name as name')
						->join('test_types', 'test_type_id', '=', 'test_types.id')
						->where('specimen_type_id', '=', $specimen_id)
						->get();
	    return Response::json(array (
			'error' => false,
			'data' => $testtypes
		), 200);
	}

	public function getVisitTypes()
	{
		$visittypes = VisitType::orderBy('name', 'ASC')->get();
		return Response::json(array (
			'error' => false,
			'data' => $visittypes
		), 200);
	}

	public function getLabSections()
	{
		$labsections = TestCategory::get();
		return Response::json(array (
			'error' => false,
			'data' => $labsections 
		), 200);
	}
	
	public function getUsers()
	{
		$users = User::select('id', 'username', 'email', 'name', 'designation')->get();
		return Response::json(array (
			'error' => false,
			'data' => $users 
		), 200);
	}

	public function placeTestOrder()
	{
		//Create New Test
		$rules = array(
			'user_id' => 'required',
			'visit_type' => 'required',
			'ward' => 'required',
			'physician' => 'required',
			'testtypes' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);

		// process the login
		if ($validator->fails()) {
			return Response::json(array (
				'error' => true,
				'data' => $validator->errors()
			), 400);
		} else {
			$visitType = ['Out-patient','In-patient'];
			$activeTest = array();

			/*
			* - Create a visit
			* - Fields required: visit_type, patient_id
			*/
			$visit = new Visit;
			$visit->patient_id = Input::get('patient_id');
			$visit->visit_type = VisitType::find(Input::get('visit_type'))->name;
			$visit->ward_or_location = Input::get('ward');
			$visit->save();

			/*
			* - Create tests requested
			* - Fields required: visit_id, test_type_id, specimen_id, test_status_id, created_by, requested_by
			*/
			$testTypes = Input::get('testtypes');
			$testTypeNames = TestType::whereIn('id', $testTypes)->lists('name');
			$panelNames = array_diff($testTypes, array_filter($testTypes, 'is_numeric'));
			$testTypeNames = array_merge($testTypeNames, $panelNames);

			$patient = $visit->patient;
			$split_name = explode(' ', $patient->name);
			$first_name = $split_name[0];
			$last_name = '';
			$middle_name = '';
			if(sizeof($split_name) > 1){
				$last_name = $split_name[sizeof($split_name) - 1];
			}

			if(sizeof($split_name) > 2){
				$middle_name = $split_name[1];
			}

			$json = Array( 'return_path' => "",
				'district' => Config::get('kblis.district'),
				'health_facility_name'=> Config::get('kblis.organization'),
				'first_name' => $first_name,
				'last_name' => $last_name,
				'middle_name' => $middle_name,
				'date_of_birth'=> $patient->dob,
				'gender'=> ($patient->gender == '1' ? "F" : "M"),
				'national_patient_id' => $patient->external_patient_number,
				'phone_number' => $patient->phone_number,
				'reason_for_test' => '',
				'sample_collector_last_name' => (isset(explode(' ', Input::get('physician'))[1]) ? explode(' ', Input::get('physician'))[1] : ''),
				'sample_collector_first_name' => explode(' ', Input::get('physician'))[0],
				'sample_collector_phone_number' => '',
				'sample_collector_id' => '',
				'sample_order_location' => Input::get('ward'),
				'sample_type' => SpecimenType::find(Input::get('specimen_type'))->name,
				'date_sample_drawn' => date('Y-m-d'),
				'tests' => $testTypeNames,
				'sample_priority' => (Input::get('priority') ? Input::get('priority') : 'Routine'),
				'target_lab' => Config::get('kblis.organization'),
				'tracking_number'  => "",
				'art_start_date'  => "",
				'date_dispatched'  => "",
				'date_received'  => date('Y-m-d'),
				'return_json' => 'true'
			);

			$data_string = json_encode($json);
			$ch = curl_init( Config::get('kblis.national-repo-node')."/create_hl7_order");
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'Accept: application/json',
					'Content-Length: ' . strlen($data_string))
			);
			$specimen = null;
			$response = json_decode(curl_exec($ch));
			if(is_array($testTypes) && count($testTypes) > 0){

				// Create Specimen - specimen_type_id, accepted_by, referred_from, referred_to
				$specimen = new Specimen;
				$specimen->specimen_type_id = Input::get('specimen_type');
				$specimen->accepted_by = Input::get('user_id');
				$specimen->tracking_number = $response->tracking_number;
				$specimen->accession_number = Specimen::assignAccessionNumber();
				$specimen->save();

				foreach ($testTypes as $value) {
					$testTypeID = (int)$value;

					if ($testTypeID == 0){
						$panelType = PanelType::where('name', '=', $value)->first()->id;

						$panelTests = DB::select("SELECT test_type_id FROM panels
											WHERE panel_type_id = $panelType"
										);

						if(count($panelTests) > 0) {

							$panel = new TestPanel;
							$panel->panel_type_id = $panelType;
							$panel->save();

							foreach ($panelTests AS $tType) {

								$duplicateCheck = DB::select("SELECT * FROM tests
											WHERE test_type_id = ".$tType->test_type_id
									." AND specimen_id = ".$specimen->id);

								if(count($duplicateCheck) == 0) {
									$test = new Test;
									$test->visit_id = $visit->id;
									$test->test_type_id = $tType->test_type_id;
									$test->specimen_id = $specimen->id;
									$test->test_status_id = Test::PENDING;
									$test->created_by =  Input::get('user_id');
									$test->panel_id = $panel->id;
									$test->requested_by = Input::get('physician');
									$test->save();

									$activeTest[] = $test->id;
								}
							}
						}

					}else {

						$duplicateCheck = DB::select("SELECT * FROM tests
											WHERE test_type_id = $testTypeID AND specimen_id = ".$specimen->id);

						if(count($duplicateCheck) == 0) {
							$test = new Test;
							$test->visit_id = $visit->id;
							$test->test_type_id = $testTypeID;
							$test->specimen_id = $specimen->id;
							$test->test_status_id = Test::PENDING;
							$test->created_by =  Input::get('user_id');
							$test->requested_by = Input::get('physician');
							$test->save();

							$activeTest[] = $test->id;
						}
					}
				}
			}

			Sender::send_data($patient, Specimen::find($specimen->id));
			return Response::json(array (
				'error' => false,
				'data' =>  $activeTest 
			), 201);
		}
	}
}
