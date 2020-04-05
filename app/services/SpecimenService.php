<?php

use Illuminate\Support\Facades\DB;

class SpecimenService {
    public static function createSpecimen($specimen_type, $user, $tracking_number)
    {
        $specimen = new Specimen;
        $specimen->specimen_type_id = $specimen_type;
        $specimen->accepted_by = $user;
        $specimen->tracking_number = $tracking_number;
        $specimen->accession_number = Specimen::assignAccessionNumber();
        $specimen->save();
        return $specimen;
    }

    public static function mapTestToSpecimenType($test_type_id, $specimen_type){

		return DB::table('testtype_specimentypes')->insert(array (
            'test_type_id' => $test_type_id, 'specimen_type_id' => $specimen_type
            ));
    }
    
    public static function getSpecimenTypeByName($specimen_name)
    {
        $specimen_type = DB::table('specimen_types')->where('name', $specimen_name)->first();
        return $specimen_type ? $specimen_type : SpecimenService::createSpecimenType($specimen_name, null);
    }

    public static function createSpecimenType($name, $description) 
    {
        $rules = array('name' => 'required|unique:specimen_types,name');
		$validator = Validator::make(['name' => $name], $rules);

		if (!$validator->fails()) {
			$specimentype = new SpecimenType;
			$specimentype->name = $name;
			$specimentype->description = $description;
            $specimentype->save();
			return $specimentype;
        } 
    }
    
    public static function getSpecimens()
	{
		$specimentypes = SpecimenType::select('id as specimen_id', 'name')->get();
		return $specimentypes;
    }
    
    public static function getSpecimenTests($specimen_id)
	{
		$testtypes = DB::table('testtype_specimentypes')
						->select('test_type_id', 'test_types.name as name')
						->join('test_types', 'test_type_id', '=', 'test_types.id')
						->where('specimen_type_id', '=', $specimen_id)
						->get();
	    return $testtypes;
	}
}