<?php

class OrdersService {
    const VALIDATION_RULES  = array(
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

    public static function order($user, $visit, $test_types, $specimen, $status, $physician){
        $orders = [];
        if(is_array($test_types) && count($test_types) > 0){
            foreach ($test_types as $test_type) { 
                $test_type_id = (int) $test_type;
                if ($test_type_id == 0)
                {
                    $panel_type = LabTestService::getPanelType($test_type);
                    $panel_tests = LabTestService::getPanelTests($panel_type);
                    if(count($panel_tests) > 0) {
                        $panel = LabTestService::createPanel($panel_type);
                        foreach ($panel_tests AS $t_type) { 
                            if(LabTestService::isTestDuplicate($t_type->test_type_id, $specimen)){
                               $orders[] = LabTestService::createTest(
                                    $visit, $user, $t_type->test_type_id, $specimen, $status, $panel,$physician
                                );
                            }
                        }
                    }
                }else{
                    if(LabTestService::isTestDuplicate($test_type_id, $specimen)){
                        $orders[] = LabTestService::createTest(
                            $visit, $user, $test_type_id, $specimen, $status, null, $physician
                        ); 
                    } 
                }
            }
        }
        return $orders;
    }


}