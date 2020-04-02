<?php
class OrdersService {
    const VALIDATION_RULES  = array(
        'user_id' => 'required',
        'visit_type' => 'required',
        'ward' => 'required',
        'physician' => 'required',
        'testtypes' => 'required',
    );

    public static function validate($data) {
        $validator = Validator::make($data, OrdersService::VALIDATION_RULES);
        return $validator->fails() ? $validator->errors() : [];
    }

    public static function order($patient, $user, $visit_type, $test_types, $specimen_type, 
        $specimen_tracking_number, $status, $physician, $ward){

        $orders = [];
        $visit = OrdersService::createVisit($patient, $visit_type, $ward);
        
        if(is_array($test_types) && count($test_types) > 0){
            $specimen = OrdersService::createSpecimen($specimen_type, $user, $specimen_tracking_number);
            foreach ($test_types as $test_type) { 
                $test_type_id = (int) $test_type;
                if ($test_type_id == 0)
                {
                    $panel_type = OrdersService::getPanelType($test_type);
                    $panel_tests = OrdersService::getPanelTests($panel_type);
                    
                    if(count($panel_tests) > 0) {
                        $panel = OrdersService::createPanel($panel_type);

                        foreach ($panel_tests AS $t_type) { 
                            if(OrdersService::isTestDuplicate($t_type->test_type_id, $specimen->id)){
                               $orders[] = OrdersService::createTest(
                                    $visit->id, $user, $t_type->test_type_id, $specimen->id, $status, $panel,$physician
                                );
                            }
                        }
                    }
                }else{
                    if(OrdersService::isTestDuplicate($test_type_id, $specimen->id)){
                        $orders[] = OrdersService::createTest(
                            $visit->id, $user, $test_type_id, $specimen->id, $status, $panel, $physician
                        ); 
                    }
                }
            }
        }
        return $orders;
    }

    protected static function createPanel($panel_type)
    {
        $panel = new TestPanel;
        $panel->panel_type_id = $panel_type;
        $panel->save();
        return $panel;
    }

    protected static function createSpecimen($specimen_type, $user, $tracking_number)
    {
        $specimen = new Specimen;
        $specimen->specimen_type_id = $specimen_type;
        $specimen->accepted_by = $user;
        $specimen->tracking_number = $tracking_number;
        $specimen->accession_number = Specimen::assignAccessionNumber();
        $specimen->save();
        return $specimen;
    }

    protected static function createVisit($patient, $visity_type, $ward)
    {
        $visit = new Visit;
        $visit->patient_id = $patient;
        $visit->visit_type = VisitType::find($visity_type)->name;
        $visit->ward_or_location = $ward;
        $visit->save();
        return $visit;
    }

    protected static function createTest($visit, $user, $test_type, $specimen, $status, $panel, $physician)
    {
        $test = new Test;
        $test->visit_id = $visit;
        $test->test_type_id = $test_type;
        $test->specimen_id = $specimen;
        $test->test_status_id = $status;
        $test->created_by =  $user;
        $test->panel_id = $panel;
        $test->requested_by = $physician;
        $test->save();
        return $test;
    }

    protected static function getPanelType($test_type)
    {
        return PanelType::where('name', '=', $test_type)->first()->id;
    }

    protected static function isTestDuplicate($test_type, $specimen)
    {
        $duplicateCheck = DB::select("SELECT * FROM tests WHERE test_type_id = ".$test_type." AND specimen_id = ".$specimen);
        return count($duplicateCheck) > 0;
    }

    protected static function getPanelTests($panelType)
    {
        return DB::select("SELECT test_type_id FROM panels WHERE panel_type_id = $panelType");
    }

}