<?php
class ApiV1Controller extends \BaseController {

	public function getSpecimens()
	{
		return Response::json(array (
			'error' => false,
			'data' => SpecimenService::getSpecimens()
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
			return Response::json(array (
				'error' => true, 'data' => ['order' => $orderValidationErrors, 
				'person'=> $patientValidationErrors]), 
				400);
		}
		$patient = PatientService::getPatient(Input::get('user_id'), Input::get('person'));
		$specimen = SpecimenService::createSpecimen(
				Input::get('specimen_type'), Input::get('user_id'), 
				Input::get('specimen_tracking_number')
			);
		$visit = VisitService::createVisit(
			$patient->id, Input::get('visit_type'), Input::get('ward')
		);
		$order = OrdersService::order(
			Input::get('accession_number'), Input::get('user_id'), $visit->id, Input::get('testtypes'),
			$specimen->id, Test::PENDING, Input::get('physician')
		);
		$code = 201;
		$error = false;
		
		if (!$order){
			$error = true;
			$code = 400;
		}
		return Response::json(array ('error' => $error, 'data' => $order ), $code);
	}

	public function getTestResults($accession_number)
	{
		$test = LabTestService::getTestByAccessionNumber($accession_number);
		$error = false;
		$code = 200;
		$data = [];
		if ($test){
			$data = ResultsService::getResultsByAccessionNumber($test->id);
		}else{
			$code = 404;
			$error = true;
		}
		return Response::json(array (
			'error'=> $error, 
			'data' => $data
		), $code);
	}
}
