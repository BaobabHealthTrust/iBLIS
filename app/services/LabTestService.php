<?php

class LabTestService {
    
    public static function getTestType($test_name, $specimen_type_id, $test_category)
    {
        $test_type = DB::table('test_types')->where('name', $test_name)->first();
        if (!$test_type){
            $test_type = LabTestService::createTestType($test_name, $test_category);
        }
        $test_specimen_type = DB::select(
            'SELECT * FROM testtype_specimentypes WHERE 
            specimen_type_id = '.$specimen_type_id.' AND test_type_id='.$test_type->id
        );
        if(!$test_specimen_type){
            SpecimenService::mapTestToSpecimenType($test_type->id, $specimen_type_id);
        }
        return $test_type;
    }
    
    public static function getTestResults($accession_number)
    {
        return DB::select("SELECT 
            test_statuses.name as test_status,
            interpretation as result, 
            test_types.name as test_type,
            not_done_reasons, time_completed, time_started
            FROM tests 
            JOIN test_types on test_types.id = tests.test_type_id
            JOIN test_statuses on test_statuses.id
            WHERE accession_number= '".$accession_number."'");
    }

    public static function getTestCategory($name, $description='N/A')
    {
        $test_cat = DB::table('test_categories')->where('name', $name)->first();
        return $test_cat ? $test_cat : LabTestService::createTestCategory($name, $description);
    }
    
    public static function getTestByAccessionNumber($accession_number)
    {
        return DB::table('tests')->where('accession_number', $accession_number)->first();
    }
    
    public static function createTest($accession_number, $visit, $user, $test_type, $specimen, $status, $panel, $physician)
    {
        $test = new Test;
        $test->visit_id = $visit;
        $test->test_type_id = $test_type;
        $test->specimen_id = $specimen;
        $test->test_status_id = $status;
        $test->created_by =  $user;
        $test->panel_id = $panel;
        $test->requested_by = $physician;
        $test->accession_number = $accession_number;
        $test->save();
        return $test;
    }

    public static function createTestType($name, $test_category)
    {
        $rules = array('name' => 'required|unique:test_types,name');
        $validator = Validator::make(['name' => $name], $rules);
        
        if (!$validator->fails()) {
            $testtype = new TestType;
            $testtype->name = trim($name);
            $testtype->short_name = 'N/A';
            $testtype->description = 'N/A';
            $testtype->test_category_id = $test_category;
            $testtype->save();
            return $testtype;
        }
    }
   
    public static function createTestCategory($name, $description="N/A")
    {
        $rules = array('name' => 'required|unique:test_categories,name');
        $validator = Validator::make(['name' => $name], $rules);
    
        if(!$validator->fails()){
            $testcategory = new TestCategory;
            $testcategory->name = $name;
            $testcategory->description = $description;
            $testcategory->save();
            return $testcategory;
        }
    }
}
