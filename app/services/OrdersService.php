<?php

class OrdersService {
    const VALIDATION_RULES  = array(
        'accession_number' => 'required',
        'user_id' => 'required',
        'visit_type' => 'required',
        'ward' => 'required',
        'physician' => 'required',
        'specimen_type' => 'required',
        'specimen_tracking_number' => 'required',
        'testtypes' => 'required'
    );

    public static function validate($data) {
        $validator = Validator::make($data, OrdersService::VALIDATION_RULES);
        return $validator->fails() ? $validator->errors() : [];
    }

    public static function order($accession_number, $user, $visit, $test_types, 
        $test_category, $specimen, $status, $physician){
        $orders = [];
        foreach ($test_types as $test_type) { 
            $test_category_id = LabTestService::getTestCategory($test_category)->id;
            $test_type_id = LabTestService::getTestType($test_type, $specimen->specimen_type_id, $test_category_id)->id;
            $orders[] = LabTestService::createTest(
                $accession_number, 
                $visit,
                $user, 
                $test_type_id, 
                $specimen->id, 
                $status, 
                null, 
                $physician
            );
        }
        return $orders;
    }
}