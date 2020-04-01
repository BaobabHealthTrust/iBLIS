<?php

class ApiV1Controller extends \BaseController {

	public function getSpecimens()
	{
		$specimentypes = SpecimenType::select('id as specimen_id', 'name')->get();
		return json_encode($specimentypes);
	}

	public function getSpecimenTests($specimen_id)
	{
		$testtypes = DB::table('testtype_specimentypes')
						->select('test_type_id', 'test_types.name as name')
						->join('test_types', 'test_type_id', '=', 'test_types.id')
						->where('specimen_type_id', '=', $specimen_id)
						->get();
	    return json_encode($testtypes);
	}

	public function getLabSections()
	{
		$labsections = TestCategory::get();
		return json_encode($labsections);
	}
	
	public function getUsers()
	{
		$users = User::select('id', 'username', 'email', 'name', 'designation')->get();
		return json_encode($users);
	}
}
