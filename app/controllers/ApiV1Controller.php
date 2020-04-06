<?php

use Illuminate\Support\Facades\Hash;

class ApiV1Controller extends \BaseController {

	public static function login(){

        if (Input::server("REQUEST_METHOD") == "POST") 
        {
            $validator = Validator::make(Input::all(), array(
                "username" => "required|min:4",
                "password" => "required|min:6"
            ));

            $username = Input::get("username");

            $message = trans('messages.invalid-login');


			if ($validator->passes()) {
				$credentials = array(
					"username" => Input::get("username"),
					"password" => Input::get("password")
				);

				if (Auth::attempt($credentials)) {
					$auth_key = Hash::make($username);
					Session::set("mizu_auth_key", $auth_key);

					return Response::json(array (
						'error' => false,
						'data' => ['auth_key' => $auth_key]
					), 200);
				}

				return Response::json(array (
					'error' => true,
					'data' => ['message' => 'Bad request']
				), 401);
			}

        }
    }


	public static function logout() {

	}


	public function getSpecimens()
	{
		return Response::json(array (
			'error' => false,
			'data' => SpecimenService::getSpecimens()
		), 200);
	}

	public function getTestResults($accession_number)
	{
		return Response::json(array (
			'error'=> false, 
			'data' => LabTestService::getTestResults($accession_number)
		), 200);
	}

	public function getSpecimenTests($specimen_id)
	{
	    return Response::json(array (
			'error' => false,
			'data' => SpecimenService::getSpecimenTests($specimen_id)
		), 200);
	}

	public function getVisitTypes()
	{
		return Response::json(array (
			'error' => false,
			'data' => VisitService::getVisitTypes()
		), 200);
	}

	public function getLabSections()
	{
		return Response::json(array (
			'error' => false,
			'data' => TestCategory::get() 
		), 200);
	}
	
	public function getUsers()
	{
		return Response::json(array (
			'error' => false,
			'data' => User::select('id', 'username', 'email', 'name', 'designation')->get() 
		), 200);
	}

	public function placeTestOrder()
	{
		$orderValidationErrors = OrdersService::validate(Input::all());
		$patientValidationErrors = PatientService::validate(Input::get('person'));

		if (count($orderValidationErrors) > 0 or count($patientValidationErrors) > 0) {
			return Response::json(array ('error' => true, 
				'data' => ['order' => $orderValidationErrors, 'person'=> $patientValidationErrors]),
				 400);
		}
		
		$patient = PatientService::getPatient(Input::get('user_id'), Input::get('person'));

		$specimen_type = SpecimenService::getSpecimenTypeByName(Input::get('specimen_type'));
		
		$visit_type = VisitService::getVisitTypeByName(Input::get('visit_type'));
		
		$specimen = SpecimenService::createSpecimen($specimen_type->id, Input::get('user_id'), 
			Input::get('specimen_tracking_number'));
		
		$visit = VisitService::createVisit($patient->id, $visit_type->id, Input::get('ward'));
		
		$order = OrdersService::order(Input::get('accession_number'), Input::get('user_id'), 
			$visit->id, Input::get('testtypes'), Input::get('test_category'), 
			$specimen, Test::PENDING, Input::get('physician'));

		$code = 201;
		$error = false;

		if (!$order){
			$error = true;
			$code = 400;
		}
		return Response::json(array ('error' => $error, 'data' => $order ), $code);
	}

}
