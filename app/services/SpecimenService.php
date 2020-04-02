<?php

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