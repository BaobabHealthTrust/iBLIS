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

    public static function order($patient, $user, $visit_type, $test_types, $specimen_type, 
        $specimen_tracking_number, $status, $physician, $ward){

        $orders = [];
        $visit = VisitService::createVisit($patient, $visit_type, $ward);

        if(is_array($test_types) && count($test_types) > 0){
            $specimen = SpecimenService::createSpecimen($specimen_type, $user, $specimen_tracking_number);
            foreach ($test_types as $test_type) { 
                $test_type_id = (int) $test_type;
                if ($test_type_id == 0)
                {
                    $panel_type = LabTestService::getPanelType($test_type);
                    $panel_tests = LabTestService::getPanelTests($panel_type);
                    if(count($panel_tests) > 0) {
                        $panel = LabTestService::createPanel($panel_type);
                        foreach ($panel_tests AS $t_type) { 
                            if(LabTestService::isTestDuplicate($t_type->test_type_id, $specimen->id)){
                               $orders[] = LabTestService::createTest(
                                    $visit->id, $user, $t_type->test_type_id, $specimen->id, $status, $panel,$physician
                                );
                            }
                        }
                    }
                }else{
                    if(LabTestService::isTestDuplicate($test_type_id, $specimen->id)){
                        $orders[] = LabTestService::createTest(
                            $visit->id, $user, $test_type_id, $specimen->id, $status, null, $physician
                        ); 
                    } 
                }
            }
        }
        return $orders;
    }


}