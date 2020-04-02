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
			Input::get('user_id'), $visit->id, Input::get('testtypes'),
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
}
