<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/*
|-----------------------------------------
| API v1 endpoints For integrating with  other Information Management Systems
|-----------------------------------------
*/
Route::group(array('prefix' => 'api/v1'), function(){
    Route::resource('specimens', 'ApiV1Controller@getSpecimens');
    Route::resource('specimen_tests', 'ApiV1Controller@getSpecimenTests');
    Route::resource('sections', 'ApiV1Controller@getLabSections');
    Route::resource('users', 'ApiV1Controller@getUsers');
    Route::resource('visits', 'ApiV1Controller@getVisitTypes');
    Route::resource('order', 'ApiV1Controller@placeTestOrder');
});

/* Routes accessible before logging in */
Route::group(array("before" => "guest"), function()
{
    /*
    |-----------------------------------------
    | API route
    |-----------------------------------------
    | Proposed route for the BLIS api, we will receive api calls 
    | from other systems from this route.
    */
    Route::post('/api/receiver', array(
        "as" => "api.receiver",
        "uses" => "InterfacerController@receiveLabRequest"
    ));
    Route::any('/', array(
        "as" => "user.login",
        "uses" => "UserController@loginAction"
    ));

    /*
        Routes based on logic found in BLISInterfaceClient
    */
    Route::get('/api/get_test_types.php', array(
        "as" => "api.get.test.types",
        "uses" => "InterfacerController@getTestTypes"
    ));
    Route::get('/api/get_specimen.php', array(
        "as" => "api.get.specimen",
        "uses" => "InterfacerController@getSpecimen"
    ));
    Route::get('/api/update_result.php', array(
        "as" => "api.update.result",
        "uses" => "InterfacerController@updateResult"
    ));

    Route::get('/api/get_test_catalog.php', array(
        "as" => "api.get_test_catalog",
        "uses" => "InterfacerController@getTestCatalog"
    ));

});

/* Routes accessible before logging in */
Route::group(array("before" => "print"), function()
{
    Route::get('/api/get_test_catalog.php', array(
        "as" => "api.get_test_catalog",
        "uses" => "InterfacerController@getTestCatalog"
    ));

});

/*
 * *Routes accessible by cmd tools for generating printouts
*/

Route::group(array("before", "print"), function()
{
    Route::any("/patientreport/{id}/{visit}", array(
        "as" => "reports.patient.report",
        "uses" => "ReportController@viewPatientReport"
    ));
    

    Route::any("/track_patient_report_printing", array(
        "as" => "reports.patient.report",
        "uses" => "ReportController@trackPatientReportPrint"
    ));

    Route::any("/patientreport/{id}", array(
        "as" => "reports.patient.report",
        "uses" => "ReportController@viewPatientReport"
    ));

    Route::any("/print/{id}/{visit}", array(
        "as" => "reports.patient.print",
        "uses" => "ReportController@printReport"
    ));

    Route::any("/departments_summary_report", array(
        "as"   => "reports.departments_summary",
        "uses" => "ReportController@departments_summary"
    ));

    Route::match(['get', 'post'], "/departmentreport", array(
        "as"   => "reports.department",
        "uses" => "ReportController@department_report"
    ));

    Route::match(['get', 'post'], "/rejected", array(
        "as"   => "rejected.sample",
        "uses" => "ReportController@rejected_specimens"
    ));

    Route::match(['get', 'post'], "/tbreport", array(
        "as"   => "reports.tb",
        "uses" => "ReportController@tb_report"
    ));

    Route::match(['get', 'post'], "/turnaroundtime", array(
        "as"   => "turnaround.report",
        "uses" => "ReportController@getTat"
    ));

});
/* Routes accessible AFTER logging in */
Route::group(array("before" => "auth"), function()
{
    Route::any('/home', array(
        "as" => "user.home",
        "uses" => "UserController@homeAction"
        ));
    Route::group(array("before" => "checkPerms:manage_users"), function() {
        Route::resource('user', 'UserController');
        Route::get("/user/{id}/delete", array(
            "as"   => "user.delete",
            "uses" => "UserController@delete"
        ));
    });
    Route::get("/visittype/getWards", array(
        "as"   => "visittype.getwards",
        "uses" => "VisitTypeController@getWards"
    ));
    Route::get("/specimentype/getTestTypes", array(
        "as"   => "specimentype.testtypes",
        "uses" => "SpecimenTypeController@getTestTypes"
    ));
    Route::any("/logout", array(
        "as"   => "user.logout",
        "uses" => "UserController@logoutAction"
    ));
    Route::any('/user/change_location/{id}', array(
        "as" => "user.change_location",
        "uses" => "UserController@change_location"
    ));
    Route::any('/user/{id}/updateown', array(
        "as" => "user.updateOwnPassword",
        "uses" => "UserController@updateOwnPassword"
        ));
    Route::resource('patient', 'PatientController');
    Route::get("/patient/{id}/delete", array(
        "as"   => "patient.delete",
        "uses" => "PatientController@delete"
    ));
    Route::post("/patient/search", array(
        "as"   => "patient.search",
        "uses" => "PatientController@search"
    ));
    Route::any("/instrument/getresult", array(
        "as"   => "instrument.getResult",
        "uses" => "InstrumentController@getTestResult"
    ));
    Route::get("/instrument/checkresult", array(
        "as"   => "instrument.checkResult",
        "uses" => "InstrumentController@checkResult"
    ));
    Route::group(array("before" => "checkPerms:manage_test_catalog"), function()
    {
        Route::resource('specimentype', 'SpecimenTypeController');
        Route::get("/specimentype/{id}/delete", array(
            "as"   => "specimentype.delete",
            "uses" => "SpecimenTypeController@delete"
        ));

        Route::resource('testcategory', 'TestCategoryController');
        
        Route::get("/testcategory/{id}/delete", array(
            "as"   => "testcategory.delete",
            "uses" => "TestCategoryController@delete"
        ));
        Route::resource('measure', 'MeasureController');
    
        Route::get("/measure/{id}/delete", array(
            "as"   => "measure.delete",
            "uses" => "MeasureController@delete"
        ));
        Route::resource('testtype', 'TestTypeController');
        Route::get("/testtype/{id}/delete", array(
            "as"   => "testtype.delete",
            "uses" => "TestTypeController@delete"
        ));

        Route::resource('visittype', 'VisitTypeController');
        Route::get("/visittype/{id}/delete", array(
            "as"   => "visittype.delete",
            "uses" => "VisitTypeController@delete"
        ));

        Route::resource('testpanel', 'TestPanelController');
        Route::get("/testpanel/{id}/delete", array(
            "as"   => "testpanel.delete",
            "uses" => "TestPanelController@delete"
        ));

        Route::resource('specimenlifespan', 'SpecimenLifespanController');
        Route::get("/specimenlifespan/{id}/delete", array(
            "as"   => "specimenlifespan.delete",
            "uses" => "SpecimenLifespanController@delete"
        ));

        Route::resource('specimenrejection', 'SpecimenRejectionController');
        Route::any("/specimenrejection/{id}/delete", array(
            "as"   => "specimenrejection.delete",
            "uses" => "SpecimenRejectionController@delete"
        ));

        Route::resource('testnotdone', 'TestNotDoneController');
        Route::any("/testnotdone/{id}/delete", array(
            "as"   => "testnotdone.delete",
            "uses" => "TestNotDoneController@delete"
        ));

        Route::resource('drug', 'DrugController');       
        Route::get("/drug/{id}/delete", array(
            "as"   => "drug.delete",
            "uses" => "DrugController@delete"
        ));
        Route::resource('organism', 'OrganismController');
        
        Route::get("/organism/{id}/delete", array(
            "as"   => "organism.delete",
            "uses" => "OrganismController@delete"
        ));
    });
    Route::group(array("before" => "checkPerms:manage_lab_configurations"), function()
    {
        Route::resource('instrument', 'InstrumentController');
        Route::get("/instrument/{id}/delete", array(
            "as"   => "instrument.delete",
            "uses" => "InstrumentController@delete"
        ));
        Route::any("/instrument/importdriver", array(
            "as"   => "instrument.importDriver",
            "uses" => "InstrumentController@importDriver"
        ));
    });

    Route::get("/report/{id}/print_zebra_report", array(
        "as"   => "reports.print_zebra_report",
        "uses" => "ReportController@printZebraReport"
    ));

    Route::any("/test", array(
        "as"   => "test.index",
        "uses" => "TestController@index"
    ));
    Route::any("/test/remoteorder", array(
        "as"   => "test.remoteorder",
        "uses" => "TestController@remotePreview"
    ));
    Route::post("/test/resultinterpretation", array(
    "as"   => "test.resultinterpretation",
    "uses" => "TestController@getResultInterpretation"
    ));
     Route::any("/test/{id}/receive", array(
        "before" => "checkPerms:receive_external_test",
        "as"   => "test.receive",
        "uses" => "TestController@receive"
    ));
    Route::any("/test/create/{patient?}", array(
        "before" => "checkPerms:request_test",
        "as"   => "test.create",
        "uses" => "TestController@create"
    ));
     Route::post("/test/savenewtest", array(
        "before" => "checkPerms:request_test",
        "as"   => "test.saveNewTest",
        "uses" => "TestController@saveNewTest"
    ));
    Route::post("/test/appendnewtest", array(
        "before" => "checkPerms:request_test",
        "as"   => "test.appendNewTest",
        "uses" => "TestController@appendNewTest"
    ));
    Route::get("/test/{id}/print_tracking_number", array(
        "as"   => "test.print_tracking_number",
        "uses" => "TestController@printTrackingNumber"
    ));
     Route::post("/test/acceptspecimen", array(
        "before" => "checkPerms:accept_test_specimen",
        "as"   => "test.acceptSpecimen",
        "uses" => "TestController@accept"
    ));
     Route::get("/test/{id}/refer", array(
        "before" => "checkPerms:refer_specimens",
        "as"   => "test.refer",
        "uses" => "TestController@showRefer"
    ));
    Route::post("/test/referaction", array(
        "before" => "checkPerms:refer_specimens",
        "as"   => "test.referAction",
        "uses" => "TestController@referAction"
    ));
    Route::get("/test/{id}/reject", array(
        "before" => "checkPerms:reject_test_specimen",
        "as"   => "test.reject",
        "uses" => "TestController@reject"
    ));
    Route::get("/test/{id}/void", array(
        "as"   => "test.void",
        "uses" => "TestController@void"
    ));
    Route::get("/test/{id}/ignore", array(
        "as"   => "test.ignore",
        "uses" => "TestController@ignore"
    ));
    Route::get("/test/{id}/append_test", array(
        "as"   => "test.append_test",
        "uses" => "TestController@append_test"
    ));
    Route::post("/test/rejectaction", array(
        "before" => "checkPerms:reject_test_specimen",
        "as"   => "test.rejectAction",
        "uses" => "TestController@rejectAction"
    ));
     Route::post("/test/changespecimen", array(
        "before" => "checkPerms:change_test_specimen",
        "as"   => "test.changeSpecimenType",
        "uses" => "TestController@changeSpecimenType"
    ));
     Route::post("/test/updatespecimentype", array(
        "before" => "checkPerms:change_test_specimen",
        "as"   => "test.updateSpecimenType",
        "uses" => "TestController@updateSpecimenType"
    ));
    Route::post("/test/start", array(
        "before" => "checkPerms:start_test",
        "as"   => "test.start",
        "uses" => "TestController@start"
    ));
    Route::get("/test/{id}/machineid", array(
        "as"   => "test.machineid",
        "uses" => "TestController@printMachineId"
    ));

    Route::get("/test/{id}/mergeorupdate", array(
        "as"   => "test.mergeorupdate",
        "uses" => "TestController@mergeRemoteResults"
    ));

     Route::get("/notdone", array(
        "as"   => "test.notdone",
        "uses" => "TestController@notDone"
    ));

     Route::any("ignoreSpecimen", array(
        "as"   => "test.ignoreSpecimen",
        "uses" => "TestController@ignoreSpecimen"
    ));

     Route::any("/checkMachineResults", array(
        "as"   => "Instrument.checkMachineResults",
        "uses" => "InstrumentController@checkMachineResults"
    ));


    Route::any("/editOrganisms", array(
        "as"   => "test.editOrganisms",
        "uses" => "TestController@editOrganisms"
    ));
    Route::any("/cancelSelectedOrganims", array(
            "as"   => "organism.cancelSelectedOrganims",
            "uses" => "OrganismController@cancelSelectedOrganims"
        ));
    Route::get("/test/{id}/ignoretest", array(
         "as"   => "test.ignoreTest",
        "uses" => "TestController@ignoreTest"
    ));

    Route::any("ignoresingletest", array(
        "as"   => "test.ignoreSingleTest",
        "uses" => "TestController@ignoreSingleTest"
    ));


     Route::get("/test/{test}/enterresults", array(
        "before" => "checkPerms:enter_test_results",
        "as"   => "test.enterResults",
        "uses" => "TestController@enterResults"
    ));
    Route::get("/test/{test}/edit", array(
        "before" => "checkPerms:edit_test_results",
        "as"   => "test.edit",
        "uses" => "TestController@edit"
    ));

    Route::get("/test/{test}/print_pack_details", array(
        "as"   => "test.print_pack_details",
        "uses" => "TestController@printPackDetails"
    ));

     Route::post("/test/{test}/saveresults", array(
        "before" => "checkPerms:edit_test_results",
        "as"   => "test.saveResults",
        "uses" => "TestController@saveResults"
    ));

    Route::any("/postOrganisms", array(
            "as"   => "test.selectedOrganisms",
            "uses" => "TestController@selectedOrganisms"
    ));

    Route::get("/test/{test}/viewdetails", array(
        "as"   => "test.viewDetails",
        "uses" => "TestController@viewDetails"
    ));
    Route::any("/test/{test}/verify", array(
        "before" => "checkPerms:verify_test_results",
        "as"   => "test.verify",
        "uses" => "TestController@verify"
    ));
    Route::any("/culture/storeObservation", array(
        "as"   => "culture.worksheet",
        "uses" => "CultureController@store"
    ));
    Route::any("/susceptibility/saveSusceptibility", array(
        "as"   => "drug.susceptibility",
        "uses" => "SusceptibilityController@store"
    ));
    Route::group(array("before" => "admin"), function()
    {
        Route::resource("permission", "PermissionController");
        Route::get("role/assign", array(
            "as"   => "role.assign",
            "uses" => "RoleController@assign"
        ));
        Route::post("role/assign", array(
            "as"   => "role.assign",
            "uses" => "RoleController@saveUserRoleAssignment"
        ));
        Route::resource("role", "RoleController");
        Route::get("/role/{id}/delete", array(
            "as"   => "role.delete",
            "uses" => "RoleController@delete"
        ));
    });
    // Check if able to manage lab configuration
    Route::group(array("before" => "checkPerms:manage_lab_configurations"), function()
    {
        Route::resource("facility", "FacilityController");
        Route::get("/facility/{id}/delete", array(
            "as"   => "facility.delete",
            "uses" => "FacilityController@delete"
        ));

        Route::resource("facilityward", "FacilityWardController");
        Route::get("/facilityward/{id}/delete", array(
            "as"   => "ward.delete",
            "uses" => "FacilityWardController@delete"
        ));

        Route::any("/reportconfig/surveillance", array(
            "as"   => "reportconfig.surveillance",
            "uses" => "ReportController@surveillanceConfig"
        ));
        Route::any("/reportconfig/disease", array(
            "as"   => "reportconfig.disease",
            "uses" => "ReportController@disease"
        ));
    });
    
    //  Check if able to manage reports
    Route::group(array("before" => "checkPerms:view_reports"), function()
    {
        Route::any("/patientreport", array(
            "as"   => "reports.patient.index",
            "uses" => "ReportController@loadPatients"
        ));

        Route::any("/dailylog", array(
            "as"   => "reports.daily.log",
            "uses" => "ReportController@dailyLog"
        ));
        Route::get('reports/dropdown', array(
            "as"    =>  "reports.dropdown",
            "uses"  =>  "ReportController@reportsDropdown"
        ));
        Route::any("/prevalence", array(
            "as"   => "reports.aggregate.prevalence",
            "uses" => "ReportController@prevalenceRates"
        ));
        Route::any("/surveillance", array(
            "as"   => "reports.aggregate.surveillance",
            "uses" => "ReportController@surveillance"
        ));
        Route::any("/counts", array(
            "as"   => "reports.aggregate.counts",
            "uses" => "ReportController@countReports"
        ));
        
        Route::any("/tat", array(
            "as"   => "reports.aggregate.tat",
            "uses" => "ReportController@turnaroundTime"
        ));
        Route::any("/infection", array(
            "as"   => "reports.aggregate.infection",
            "uses" => "ReportController@infectionReport"
        ));

        Route::any("/culturesensitivitycounts", array(
            "as"   => "reports.aggregate.cultureSensitivityCounts",
            "uses" => "ReportController@cultureSensitivityCounts"
        ));

        Route::any("/positivenegativecounts",array(
            "as" => "reports.aggregate.positiveNegativeCounts",
            "uses" => "ReportController@positiveNegativeCounts"

        ));
        
        Route::any("/wardcounts",array(
            "as" => "reports.aggregate.cultureSensitivityCounts.wardscounts",
            "uses" => "ReportController@get_culture_sensitivity_counts_for_wards"

        ));
        
         Route::any("/organismcounts",array(
            "as" => "reports.aggregate.cultureSensitivityCounts.organismcounts",
            "uses" => "ReportController@get_organisms_counts"

        ));

         Route::any("/organisminwardscounts",array(
            "as" => "reports.aggregate.cultureSensitivityCounts.organisminwards",
            "uses" => "ReportController@getOrganismInWards"

        ));

         Route::any("/susceptibilitycounts",array(
            "as" => "reports.aggregate.cultureSensitivityCounts.susceptibilitycounts",
            "uses" => "ReportController@getsusceptibilitycount"
        ));

        Route::any("/checking",array(
            "as" => "reports.aggregate.checking",
            "uses" => "ReportController@positiveNegativeCounts"

        ));
        

        Route::any("/userstatistics", array(
            "as"   => "reports.aggregate.userStatistics",
            "uses" => "ReportController@userStatistics"
        ));

        Route::any("/moh706", array(
            "as"   => "reports.aggregate.moh706",
            "uses" => "ReportController@moh706"
        ));

        Route::any("/cd4", array(
            "as"   => "reports.aggregate.cd4",
            "uses" => "ReportController@cd4"
        ));
        
        Route::get("/qualitycontrol", array(
            "as"   => "reports.qualityControl",
            "uses" => "ReportController@qualityControl"
        ));
        Route::post("/qualitycontrol", array(
            "as"   => "reports.qualityControl",
            "uses" => "ReportController@qualityControlResults"
        ));
        Route::get("/inventory", array(
            "as"   => "reports.inventory",
            "uses" => "ReportController@stockLevel"
        ));
        Route::post("/inventory", array(
            "as"   => "reports.inventory",
            "uses" => "ReportController@stockLevel"
        ));

       /* Route::match(['get', 'post'], "/departments_summary_report", array(
            "as"   => "reports.departments_summary",
            "uses" => "ReportController@departments_summary"
        ));*/

       /* Route::match(['get', 'post'], "/departmentreport", array(
            "as"   => "reports.department",
            "uses" => "ReportController@department_report"
        ));*/



       /*  Route::match(['get', 'post'], "/rejected", array(
            "as"   => "rejected.sample",
            "uses" => "ReportController@rejected_specimens"
        ));*/

    });
    Route::group(array("before" => "checkPerms:manage_qc"), function()
    {
        Route::resource("lot", "LotController");
        Route::get('lot/{lotId}/delete', array(
            'uses' => 'LotController@delete'
        ));
        Route::any("controlresult/{id}/update",array(
            "as" => "controlresult.update",
            "uses" => "ControlResultsController@update"
            )
        );

        Route::get('controlresult/{controlTestId}/delete', array(
            'uses' => 'ControlResultsController@delete'
        ));
        Route::resource("control", "ControlController");
        Route::get("controlresults", array(
            "as"   => "control.resultsIndex",
            "uses" => "ControlController@resultsIndex"
        ));
        Route::get("controlresults/{controlId}/resultsEntry", array(
            "as" => "control.resultsEntry",
            "uses" => "ControlController@resultsEntry"
        ));
        Route::get("controlresults/{controlId}/resultsEdit", array(
            "as" => "control.resultsEdit",
            "uses" => "ControlController@resultsEdit"
        ));
    
        Route::get("controlresults/{controlId}/resultsList", array(
            "as" => "control.resultsList",
            "uses" => "ControlController@resultsList"
        ));
        Route::get('control/{controlId}/delete', array(
            'uses' => 'ControlController@destroy'
        ));
        Route::post('control/{controlId}/saveResults', array(
            "as" => "control.saveResults",
            'uses' => 'ControlController@saveResults'
        ));
        Route::post('control/{controlId}/resultsUpdate', array(
            "as" => "control.resultsUpdate",
            'uses' => 'ControlController@resultsUpdate'
        ));
    });
    
    Route::group(array("before" => "checkPerms:request_topup"), function()
    {
        //top-ups
        Route::resource('topup', 'TopUpController');
        Route::get("/topup/{id}/delete", array(
            "as"   => "topup.delete",
            "uses" => "TopUpController@delete"
        ));
        Route::get('topup/{id}/availableStock', array(
            "as"    =>  "issue.dropdown",
            "uses"  =>  "TopUpController@availableStock"
        ));
    });
    Route::group(array("before" => "checkPerms:manage_inventory"), function()
    {
        //Commodities
        Route::resource('commodity', 'CommodityController');
        Route::get("/commodity/{id}/delete", array(
            "as"   => "commodity.delete",
            "uses" => "CommodityController@delete"
        ));
        //issues
        Route::resource('issue', 'IssueController');
        Route::get("/issue/{id}/delete", array(
            "as"   => "issue.delete",
            "uses" => "IssueController@delete"
        ));
        Route::get("/issue/{id}/dispatch", array(
            "as"   => "issue.dispatch",
            "uses" => "IssueController@dispatch"
        ));
        //Metrics
        Route::resource('metric', 'MetricController');
        Route::get("/metric/{id}/delete", array(
            "as"   => "metric.delete",
            "uses" => "MetricController@delete"
        ));
        //Suppliers
        Route::resource('supplier', 'SupplierController');
        
        Route::get("/supplier/{id}/delete", array(
            "as"   => "supplier.delete",
            "uses" => "SupplierController@delete"
        ));
        //Receipts
        Route::resource('receipt', 'ReceiptController');
        Route::get("/receipt/{id}/delete", array(
            "as"   => "receipt.delete",
            "uses" => "ReceiptController@delete"
        ));
    });
});
