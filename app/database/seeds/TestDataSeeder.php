<?php

class TestDataSeeder extends DatabaseSeeder
{
    public function run()
    {
        /* Users table */
        $administrator = User::find(1);
        if ($administrator) { 
        	//if user administrator exists, execute this block.
        	# code...
        	$this->command->info('Admin user already exists.');
        }
        else {
        	//if user administrator does not exist, execute this block.
	        $administrator = DB::table('users')->insert(
	        	array(
	        		"username" => "administrator", "password" => Hash::make("password"), "email" => "admin@kblis.org",
	               	"name" => "kBLIS Administrator", "designation" => "Programmer"
	        	)
            );

            $mizu = DB::table('users')->insert(
                array(
                    "username" => "mizu", "password" => Hash::make("password"), "email" => "mizu@kblis.org",
                    "name" => "Mizu Application", "designation" => "Administrator"
                )
            );
            
        	$this->command->info('users seeded');
        }
        //$this->command->info('users seeded');


        $locations = TestCategory::find(1);
        if ($locations) {
        	# code...
        	$this->command->info('Locations already exist.');
        }
        else {
	        /* Test Categories table - These map on to the lab sections */
	        $test_categories = TestCategory::create(array("name" => "Parasitology","description" => ""));
	        $lab_section_microbiology = TestCategory::create(array("name" => "Microbiology","description" => ""));

	        $this->command->info('test_categories seeded');

	        /* Test Categories table - These map on to the lab sections */
	        $lab_section_hematology = TestCategory::create(array("name" => "Haematology","description" => ""));
	        $lab_section_serology = TestCategory::create(array("name" => "Serology","description" => ""));
	        $lab_section_trans = TestCategory::create(array("name" => "Blood Transfusion","description" => ""));
            $lab_section_rec = TestCategory::create(array("name" => "Lab Reception","description" => ""));
	        $this->command->info('Lab Sections seeded'); 	
        }

        $user_test_categories = DB::table('user_testcategory')->select('id')->first();
        if ($user_test_categories) {
        	# code...
        	$this->command->info('Locations already mapped to user Administrator.');
        }
        else {
	        /* User Test Category Table */
	        DB::table('user_testcategory')->insert(array("user_id"=>"1","test_category_id"=>$test_categories->id));
	        DB::table('user_testcategory')->insert(array("user_id"=>"1","test_category_id"=>$lab_section_microbiology->id));
	        DB::table('user_testcategory')->insert(array("user_id"=>"1","test_category_id"=>$lab_section_hematology->id));
	        DB::table('user_testcategory')->insert(array("user_id"=>"1","test_category_id"=>$lab_section_serology->id));
	        DB::table('user_testcategory')->insert(array("user_id"=>"1","test_category_id"=>$lab_section_trans->id));
            DB::table('user_testcategory')->insert(array("user_id"=>"1","test_category_id"=>$lab_section_rec->id));

	        $this->command->info("User Test Category seeded.");  	
        }

        // ++++++++++++++++++++++++++++++  
        /* Test Phase table */
        $test_phases = TestPhase::find(1);
        if ($test_phases) {
            # code...
            $this->command->info('Test Phases already exist.');
        }
        else {
            $test_phases = array(
              array("id" => "1", "name" => "Pre-Analytical"),
              array("id" => "2", "name" => "Analytical"),
              array("id" => "3", "name" => "Post-Analytical")
            );
            foreach ($test_phases as $test_phase)
            {
                TestPhase::create($test_phase);
            }
            $this->command->info('test_phases seeded');  
        }

        /* Test Status table */
        $test_statuses = TestStatus::find(1);
        if ($test_statuses) {
        	# code...
        	$this->command->info('Test Statuses already seeded.');
        }
        else {
	        $test_statuses = array(
                array("id" => "1","name" => "not-received","test_phase_id" => "1"),//Pre-Analytical
                array("id" => "2","name" => "pending","test_phase_id" => "1"),//Pre-Analytical
                array("id" => "3","name" => "started","test_phase_id" => "2"),//Analytical
                array("id" => "4","name" => "completed","test_phase_id" => "3"),//Post-Analytical
                array("id" => "5","name" => "verified","test_phase_id" => "3"),//Post-Analytical
                array("id" => "6","name" => "voided","test_phase_id" => "2"),//Analytical
                array("id" => "7","name" => "not-done","test_phase_id" => "2")//Analytical
	        );
	        foreach ($test_statuses as $test_status)
	        {
	            TestStatus::create($test_status);
	        }
	        $this->command->info('test_statuses seeded');
        }
  
        $wards = array("name" => "Facilities");

        FacilityWard::create($wards);

        /* Measure Types */
        $measureTypes = MeasureType::find(1);
        if ($measureTypes) {
            # code...
            $this->command->info('Measure Types already exist.');
        }
        else {
            $measureTypes = array(
                array("id" => "1", "name" => "Numeric Range"),
                array("id" => "2", "name" => "Alphanumeric Values"),
                array("id" => "3", "name" => "Autocomplete"),
                array("id" => "4", "name" => "Free Text")
            );

            foreach ($measureTypes as $measureType)
            {
                MeasureType::create($measureType);
            }
            $this->command->info('measure_types seeded');
        }

        // ================= Seed for Organisms and Drugs (+ Organism_Drugs) ========
        	// DRUGS
        $drugs = Drug::find(1);
        if ($drugs) {
            # code...
            $this->command->info('Drugs already exist.');
        }
        else {
            $amoxicillin = Drug::create(array("name" => "Amoxicillin/Clavulanate"));
            $ampicillin = Drug::create(array("name" => "Ampicillin"));
            $ceftriaxone = Drug::create(array("name" => "Ceftriaxone"));
            $chloramphenicol = Drug::create(array("name" => "Chloramphenicol"));
            $ciprofloxacin = Drug::create(array("name" => "Ciprofloxacin"));
            $tetracyline = Drug::create(array("name" => "Tetracyline"));
            $trimethoprim = Drug::create(array("name" => "Trimethoprim/Sulfamethoxazole"));
            $clindamycin = Drug::create(array("name" => "Clindamycin"));
            $erythromycin = Drug::create(array("name" => "Erythromycin"));
            $gentamicin = Drug::create(array("name" => "Gentamicin"));
            $penicillin = Drug::create(array("name" => "Penicillin"));
            $oxacillin = Drug::create(array("name" => "Oxacillin"));
            $tetracycline = Drug::create(array("name" => "Tetracycline"));
            $ceftazidime = Drug::create(array("name" => "Ceftazidime"));
            $piperacillin = Drug::create(array("name" => "Piperacillin"));
            $piperacillin_tazobactam = Drug::create(array("name" => "Piperacillin/Tazobactam"));
            $ceftriaxon = Drug::create(array("name" => "Ceftriaxon"));
            $cefotaxim = Drug::create(array("name" => "Cefotaxim"));

            $this->command->info('Drugs seeded.');  
        }


			// ORGANISM
        $organism = Organism::find(1);
        if ($organism) {
            # code...
            $this->command->info('Organisms already exist.');
        }
        else {
            $haemophilus = Organism::create(array("name" => "Haemophilus influenza"));
            $staphylococci = Organism::create(array("name" => "Staphylococci"));
            $streptococcus = Organism::create(array("name" => "Streptococcus pneumoniae"));
            $pseudomonas = Organism::create(array("name" => "Pseudomonas aeruginosa"));
            $neisseria = Organism::create(array("name" => "Neisseria meningitides"));

            $this->command->info('Organisms seeded.');    
            // ORGANISM DRUGS
        
            //++++++++ Haemophilus ++++++++++++++++
            DB::table('organism_drugs')->insert(array("organism_id" => $haemophilus->id, "drug_id" => $amoxicillin->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $haemophilus->id, "drug_id" => $ampicillin->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $haemophilus->id, "drug_id" => $ceftriaxon->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $haemophilus->id, "drug_id" => $chloramphenicol->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $haemophilus->id, "drug_id" => $ciprofloxacin->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $haemophilus->id, "drug_id" => $trimethoprim->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $haemophilus->id, "drug_id" => $tetracycline->id));

            //++++++++ Staphylococci ++++++++++++++
            DB::table('organism_drugs')->insert(array("organism_id" => $staphylococci->id, "drug_id" => $ampicillin->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $staphylococci->id, "drug_id" => $chloramphenicol->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $staphylococci->id, "drug_id" => $ciprofloxacin->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $staphylococci->id, "drug_id" => $trimethoprim->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $staphylococci->id, "drug_id" => $clindamycin->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $staphylococci->id, "drug_id" => $erythromycin->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $staphylococci->id, "drug_id" => $gentamicin->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $staphylococci->id, "drug_id" => $penicillin->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $staphylococci->id, "drug_id" => $oxacillin->id));

            //++++++++ Streptococcus ++++++++++++++
            DB::table('organism_drugs')->insert(array("organism_id" => $streptococcus->id, "drug_id" => $chloramphenicol->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $streptococcus->id, "drug_id" => $trimethoprim->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $streptococcus->id, "drug_id" => $clindamycin->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $streptococcus->id, "drug_id" => $erythromycin->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $streptococcus->id, "drug_id" => $oxacillin->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $streptococcus->id, "drug_id" => $tetracycline->id));

            //++++++++ Pseudomonas   ++++++++++++++
            DB::table('organism_drugs')->insert(array("organism_id" => $pseudomonas->id, "drug_id" => $piperacillin->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $pseudomonas->id, "drug_id" => $ciprofloxacin->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $pseudomonas->id, "drug_id" => $gentamicin->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $pseudomonas->id, "drug_id" => $ceftazidime->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $pseudomonas->id, "drug_id" => $piperacillin_tazobactam->id));

            //++++++++ Neisseria     ++++++++++++++
            DB::table('organism_drugs')->insert(array("organism_id" => $neisseria->id, "drug_id" => $ceftriaxone->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $neisseria->id, "drug_id" => $chloramphenicol->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $neisseria->id, "drug_id" => $ciprofloxacin->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $neisseria->id, "drug_id" => $trimethoprim->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $neisseria->id, "drug_id" => $ceftriaxon->id));
            DB::table('organism_drugs')->insert(array("organism_id" => $neisseria->id, "drug_id" => $cefotaxim->id));

            $this->command->info("organism_Drugs seeded.");
            // ================= ++++++++++++++++++++++++++++++++++ ======= */    
        }

        //Extra organisms from file
        $file = fopen(public_path()."/organisms.csv", "r");
        if ($file){
            while (($line = fgets($file)) !== false) {
                $organism = DB::table('organisms')->where('name', trim($line))->first();
                if (!$organism){
                    DB::table('organisms')->insert(array("name" => trim($line)));
                }
            }
            fclose($file);
        } else {}

        $measures = Measure::find(1);
        if ($measures) {
            # code...
            $this->command->info('Measures already exist.');
        }
        else {
            /* Measures table */
            $measureBSforMPS = Measure::create(
                array("measure_type_id" => "2",
                    "name" => "BS for mps", 
                    "unit" => ""));
            $measure1 = Measure::create(array("measure_type_id" => "2", "name" => "Grams stain", "unit" => ""));
            $measure2 = Measure::create(array("measure_type_id" => "2", "name" => "SERUM AMYLASE", "unit" => ""));
            $measure3 = Measure::create(array("measure_type_id" => "2", "name" => "calcium", "unit" => ""));
            $measure4 = Measure::create(array("measure_type_id" => "2", "name" => "SGOT", "unit" => ""));
            $measure5 = Measure::create(array("measure_type_id" => "2", "name" => "Indirect COOMBS test", "unit" => ""));
            $measure6 = Measure::create(array("measure_type_id" => "2", "name" => "Direct COOMBS test", "unit" => ""));
            $measure7 = Measure::create(array("measure_type_id" => "2", "name" => "Du test", "unit" => ""));
            
            MeasureRange::create(array("measure_id" => $measureBSforMPS->id, "alphanumeric" => "No mps seen", "interpretation" => "Negative"));
            MeasureRange::create(array("measure_id" => $measureBSforMPS->id, "alphanumeric" => "+", "interpretation" => "Positive"));
            MeasureRange::create(array("measure_id" => $measureBSforMPS->id, "alphanumeric" => "++", "interpretation" => "Positive"));
            MeasureRange::create(array("measure_id" => $measureBSforMPS->id, "alphanumeric" => "+++", "interpretation" => "Positive"));
            
            MeasureRange::create(array("measure_id" => $measure1->id, "alphanumeric" => "Negative"));
            MeasureRange::create(array("measure_id" => $measure1->id, "alphanumeric" => "Positive"));

            MeasureRange::create(array("measure_id" => $measure2->id, "alphanumeric" => "Low"));
            MeasureRange::create(array("measure_id" => $measure2->id, "alphanumeric" => "High"));
            MeasureRange::create(array("measure_id" => $measure2->id, "alphanumeric" => "Normal"));

            MeasureRange::create(array("measure_id" => $measure3->id, "alphanumeric" => "High"));
            MeasureRange::create(array("measure_id" => $measure3->id, "alphanumeric" => "Low"));
            MeasureRange::create(array("measure_id" => $measure3->id, "alphanumeric" => "Normal"));

            MeasureRange::create(array("measure_id" => $measure4->id, "alphanumeric" => "High"));
            MeasureRange::create(array("measure_id" => $measure4->id, "alphanumeric" => "Low"));
            MeasureRange::create(array("measure_id" => $measure4->id, "alphanumeric" => "Normal"));
            
            MeasureRange::create(array("measure_id" => $measure5->id, "alphanumeric" => "Positive"));
            MeasureRange::create(array("measure_id" => $measure5->id, "alphanumeric" => "Negative"));

            MeasureRange::create(array("measure_id" => $measure6->id, "alphanumeric" => "Positive"));
            MeasureRange::create(array("measure_id" => $measure6->id, "alphanumeric" => "Negative"));

            MeasureRange::create(array("measure_id" => $measure7->id, "alphanumeric" => "Positive"));
            MeasureRange::create(array("measure_id" => $measure7->id, "alphanumeric" => "Negative"));
            $measures = array(
                array("measure_type_id" => "1", "name" => "URIC ACID", "unit" => "mg/dl"),
                array("measure_type_id" => "4", "name" => "CSF for biochemistry", "unit" => ""),
                array("measure_type_id" => "4", "name" => "PSA", "unit" => ""),
                array("measure_type_id" => "1", "name" => "Total", "unit" => "mg/dl"),
                array("measure_type_id" => "1", "name" => "Alkaline Phosphate", "unit" => "u/l"),
                array("measure_type_id" => "1", "name" => "Direct", "unit" => "mg/dl"),
                array("measure_type_id" => "1", "name" => "Total Proteins", "unit" => ""),
                array("measure_type_id" => "4", "name" => "LFTS", "unit" => "NULL"),
                array("measure_type_id" => "1", "name" => "Chloride", "unit" => "mmol/l"),
                array("measure_type_id" => "1", "name" => "Potassium", "unit" => "mmol/l"),
                array("measure_type_id" => "1", "name" => "Sodium", "unit" => "mmol/l"),
                array("measure_type_id" => "4", "name" => "Electrolytes", "unit" => ""),
                array("measure_type_id" => "1", "name" => "Creatinine", "unit" => "mg/dl"),
                array("measure_type_id" => "1", "name" => "Urea", "unit" => "mg/dl"),
                array("measure_type_id" => "4", "name" => "RFTS", "unit" => ""),
                array("measure_type_id" => "4", "name" => "TFT", "unit" => ""),
            );

            foreach ($measures as $measure)
            {
                Measure::create($measure);
            }
            $measureGXM = Measure::create(array("measure_type_id" => "4", "name" => "GXM", "unit" => ""));
            $measureBG = Measure::create(
                array("measure_type_id" => "2",
                    "name" => "Blood Grouping", 
                    "unit" => ""));
            MeasureRange::create(array("measure_id" => $measureBG->id, "alphanumeric" => "O-"));
            MeasureRange::create(array("measure_id" => $measureBG->id, "alphanumeric" => "O+"));
            MeasureRange::create(array("measure_id" => $measureBG->id, "alphanumeric" => "A-"));
            MeasureRange::create(array("measure_id" => $measureBG->id, "alphanumeric" => "A+"));
            MeasureRange::create(array("measure_id" => $measureBG->id, "alphanumeric" => "B-"));
            MeasureRange::create(array("measure_id" => $measureBG->id, "alphanumeric" => "B+"));
            MeasureRange::create(array("measure_id" => $measureBG->id, "alphanumeric" => "AB-"));
            MeasureRange::create(array("measure_id" => $measureBG->id, "alphanumeric" => "AB+"));        
            $measureHB = Measure::create(array("measure_type_id" => Measure::NUMERIC, "name" => "HB", 
                "unit" => "g/dL"));

            $measuresUrinalysisData = array(
                array("measure_type_id" => "4", "name" => "Urine microscopy", "unit" => ""),
                array("measure_type_id" => "4", "name" => "Pus cells", "unit" => ""),
                array("measure_type_id" => "4", "name" => "S. haematobium", "unit" => ""),
                array("measure_type_id" => "4", "name" => "T. vaginalis", "unit" => ""),
                array("measure_type_id" => "4", "name" => "Yeast cells", "unit" => ""),
                array("measure_type_id" => "4", "name" => "Red blood cells", "unit" => ""),
                array("measure_type_id" => "4", "name" => "Bacteria", "unit" => ""),
                array("measure_type_id" => "4", "name" => "Spermatozoa", "unit" => ""),
                array("measure_type_id" => "4", "name" => "Epithelial cells", "unit" => ""),
                array("measure_type_id" => "4", "name" => "ph", "unit" => ""),
                array("measure_type_id" => "4", "name" => "Urine chemistry", "unit" => ""),
                array("measure_type_id" => "4", "name" => "Glucose", "unit" => ""),
                array("measure_type_id" => "4", "name" => "Ketones", "unit" => ""),
                array("measure_type_id" => "4", "name" => "Proteins", "unit" => ""),
                array("measure_type_id" => "4", "name" => "Blood", "unit" => ""),
                array("measure_type_id" => "4", "name" => "Bilirubin", "unit" => ""),
                array("measure_type_id" => "4", "name" => "Urobilinogen Phenlpyruvic acid", "unit" => ""),
                array("measure_type_id" => "4", "name" => "pH", "unit" => "")
                );

            foreach ($measuresUrinalysisData as $measureU) {
                $measuresUrinalysis[] = Measure::create($measureU);
            }

            $measuresWBCData = array(
                array("measure_type_id" => Measure::NUMERIC, "name" => "WBC", 
                    "unit" => "x10³/µL"),
                array("measure_type_id" => Measure::NUMERIC, "name" => "Lym", "unit" => "L"),
                array("measure_type_id" => Measure::NUMERIC, "name" => "Mon", "unit" => "*"),
                array("measure_type_id" => Measure::NUMERIC, "name" => "Neu", "unit" => "*"),
                array("measure_type_id" => Measure::NUMERIC, "name" => "Eos", "unit" => ""),
                array("measure_type_id" => Measure::NUMERIC, "name" => "Baso", "unit" => ""),
                );

            foreach ($measuresWBCData as $value) {
                $measuresWBC[] = Measure::create($value);
            }

            $measureRangesWBC = array(
                array("measure_id" => $measuresWBC[0]->id, "age_min" => 0, "age_max" => 100, "gender" => MeasureRange::BOTH,
                    "range_lower" => 4, "range_upper" => 11),
                array("measure_id" => $measuresWBC[1]->id, "age_min" => 0, "age_max" => 100, "gender" => MeasureRange::BOTH,
                    "range_lower" => 1.5, "range_upper" => 4),
                array("measure_id" => $measuresWBC[2]->id, "age_min" => 0, "age_max" => 100, "gender" => MeasureRange::BOTH,
                    "range_lower" => 0.1, "range_upper" => 9),
                array("measure_id" => $measuresWBC[3]->id, "age_min" => 0, "age_max" => 100, "gender" => MeasureRange::BOTH,
                    "range_lower" => 2.5, "range_upper" => 7),
                array("measure_id" => $measuresWBC[4]->id, "age_min" => 0, "age_max" => 100, "gender" => MeasureRange::BOTH,
                    "range_lower" => 0, "range_upper" => 6),
                array("measure_id" => $measuresWBC[5]->id, "age_min" => 0, "age_max" => 100, "gender" => MeasureRange::BOTH,
                    "range_lower" => 0, "range_upper" => 2),
                );

            foreach ($measureRangesWBC as $value) {
                MeasureRange::create($value);
            }

            $this->command->info('measures seeded');
        }

        /* Specimen Status table */
        $specimen_statuses = SpecimenStatus::find(1);
        if ($specimen_statuses) {
            # code...
            $this->command->info('Specimen Statuses already exist.');
        }
        else {
            $specimen_statuses = array(
              array("id" => "1", "name" => "specimen-not-collected"),
              array("id" => "2", "name" => "specimen-accepted"),
              array("id" => "3", "name" => "specimen-rejected")
            );
            foreach ($specimen_statuses as $specimen_status)
            {
                SpecimenStatus::create($specimen_status);
            }
            $this->command->info('specimen_statuses seeded');            
        }

        /* Rejection Reasons table */
        $rejection_reasons = RejectionReason::find(1);
        if ($rejection_reasons) {
            # code...
            $this->command->info('Rejection reason table already has content.');
        }
        else {
            $rejection_reasons_array = array(
              array("reason" => "Poorly labelled"),
              array("reason" => "Over saturation"),
              array("reason" => "Insufficient Sample"),
              array("reason" => "Scattered"),
              array("reason" => "Clotted Blood"),
              array("reason" => "Two layered spots"),
              array("reason" => "Serum rings"),
              array("reason" => "Scratched"),
              array("reason" => "Haemolysis"),
              array("reason" => "Spots that cannot elute"),
              array("reason" => "Leaking"),
              array("reason" => "Broken Sample Container"),
              array("reason" => "Mismatched sample and form labelling"),
              array("reason" => "Missing Labels on container and tracking form"),
              array("reason" => "Empty Container"),
              array("reason" => "Samples without tracking forms"),
              array("reason" => "Poor transport"),
              array("reason" => "Lipaemic"),
              array("reason" => "Wrong container/Anticoagulant"),
              array("reason" => "Request form without samples"),
              array("reason" => "Missing collection date on specimen / request form."),
              array("reason" => "Name and signature of requester missing"),
              array("reason" => "Mismatched information on request form and specimen container."),
              array("reason" => "Request form contaminated with specimen"),
              array("reason" => "Duplicate specimen received"),
              array("reason" => "Delay between specimen collection and arrival in the laboratory"),
              array("reason" => "Inappropriate specimen packing"),
              array("reason" => "Inappropriate specimen for the test"),
              array("reason" => "Inappropriate test for the clinical condition"),
              array("reason" => "No Label"),
              array("reason" => "Leaking"),
              array("reason" => "No Sample in the Container"),
              array("reason" => "No Request Form"),
              array("reason" => "Missing Information Required"),
            );
            foreach ($rejection_reasons_array as $rejection_reason)
            {
                $rejection_reasons[] = RejectionReason::create($rejection_reason);
            }
            $this->command->info('rejection_reasons seeded');            
        }


        /* Specimen table */
       
        // no specimen yet. $this->command->info('specimens seeded');
        $now = new DateTime();
        
        /* Permissions table */
        $permissions = Permission::find(1);
        if ($permissions) {
            # code...
            $this->command->info('Permissions already exist.');
        }
        else {
            $permissions = array(
                array("name" => "view_names", "display_name" => "Can view patient names"),
                array("name" => "manage_patients", "display_name" => "Can add patients"),

                array("name" => "receive_external_test", "display_name" => "Can receive test requests"),
                array("name" => "request_test", "display_name" => "Can request new test"),
                array("name" => "accept_test_specimen", "display_name" => "Can accept test specimen"),
                array("name" => "reject_test_specimen", "display_name" => "Can reject test specimen"),
                array("name" => "change_test_specimen", "display_name" => "Can change test specimen"),
                array("name" => "start_test", "display_name" => "Can start tests"),
                array("name" => "enter_test_results", "display_name" => "Can enter tests results"),
                array("name" => "edit_test_results", "display_name" => "Can edit test results"),
                array("name" => "verify_test_results", "display_name" => "Can verify test results"),
                array("name" => "send_results_to_external_system", "display_name" => "Can send test results to external systems"),
                array("name" => "refer_specimens", "display_name" => "Can refer specimens"),

                array("name" => "manage_users", "display_name" => "Can manage users"),
                array("name" => "manage_test_catalog", "display_name" => "Can manage test catalog"),
                array("name" => "manage_lab_configurations", "display_name" => "Can manage lab configurations"),
                array("name" => "view_reports", "display_name" => "Can view reports"),
                array("name" => "manage_inventory", "display_name" => "Can manage inventory"),
                array("name" => "request_topup", "display_name" => "Can request top-up"),
                array("name" => "manage_qc", "display_name" => "Can manage Quality Control"),
                array("name" => "void_test", "display_name" => "Can void test"),
                array("name" => "ignore_test", "display_name" => "Can claim test not done")
            );

            foreach ($permissions as $permission) {
                Permission::create($permission);
            }
            $this->command->info('Permissions table seeded');            
        }

        /* Roles table */
        $roles = Role::find(1);
        if ($roles) {
            # code...
            $this->command->info('Roles already exist.');
        }
        else {
            $roles = array(
                array("name" => "Superadmin"),
                array("name" => "Technologist"),
                array("name" => "Receptionist"),
            );
            foreach ($roles as $role) {
                Role::create($role);
            }
            $this->command->info('Roles table seeded.');

            $user1 = User::find(1);
            $role1 = Role::find(1);
            $permissions = Permission::all(); // originally before checks[$permissions = Permission::find(1);]
            if ($permissions) {
                # code...
                //Assign all permissions to role administrator
                foreach ($permissions as $permission) {
                    $role1->attachPermission($permission);
                }
                //Assign role Administrator to user 1 administrator
                $user1->attachRole($role1);
            }            
        }

        /* Instruments table */
        $instrumentsData = Instrument::find(1);
        if ($instrumentsData) {
            # code...
            $this->command->info('Instruments already exist.');
        }
        else {
            $instrumentsData = array(
                "name" => "Celltac F Mek 8222",
                "description" => "Automatic analyzer with 22 parameters and WBC 5 part diff Hematology Analyzer",
                "driver_name" => "KBLIS\\Plugins\\CelltacFMachine",
                "ip" => "192.168.1.12",
                "hostname" => "HEMASERVER"
            );
            
            $instrument = Instrument::create($instrumentsData);
            // $instrument->testTypes()->attach(array($testTypeWBC->id));

            $this->command->info('Instruments table seeded');            
        }

        $external_dump = DB::table('external_dump')->select('id')->first();
        if ($external_dump) {
            # code...
            $this->command->info('External json already dumped for labRequestUrinalysis.');
        }
        else {
            $labRequestUrinalysis[] = 
                json_decode('{"cost":null,"receiptNumber":null,"receiptType":null,"labNo":596699,"parentLabNo":0,"requestingClinician":"frankenstein Dr",
            "investigation":"Urinalysis","requestDate":"2014-10-14 10:20:35","orderStage":"ip","patientVisitNumber":643660,"patient":{"id":326983,
            "fullName":"Macau Macau","dateOfBirth":"1996-10-09 00:00:00","gender":"Female"},"address":{"address":null,"postalCode":null,"phoneNumber":"","city":null}}');
            $labRequestUrinalysis[] = 
                json_decode('{"cost":null,"receiptNumber":null,"receiptType":null,"labNo":596700,"parentLabNo":596699,"requestingClinician":"frankenstein Dr",
            "investigation":"Urine microscopy","requestDate":"2014-10-14 10:20:35","orderStage":"ip","patientVisitNumber":643660,"patient":{"id":326983,
            "fullName":"Macau Macau","dateOfBirth":"1996-10-09 00:00:00","gender":"Female"},"address":{"address":null,"postalCode":null,"phoneNumber":"","city":null}}');
            $labRequestUrinalysis[] = 
                json_decode('{"cost":null,"receiptNumber":null,"receiptType":null,"labNo":596701,"parentLabNo":596700,"requestingClinician":"frankenstein Dr",
            "investigation":"Pus cells","requestDate":"2014-10-14 10:20:35","orderStage":"ip","patientVisitNumber":643660,"patient":{"id":326983,
            "fullName":"Macau Macau","dateOfBirth":"1996-10-09 00:00:00","gender":"Female"},"address":{"address":null,"postalCode":null,"phoneNumber":"","city":null}}');
            $labRequestUrinalysis[] = 
                json_decode('{"cost":null,"receiptNumber":null,"receiptType":null,"labNo":596702,"parentLabNo":596700,"requestingClinician":"frankenstein Dr",
            "investigation":"S. haematobium","requestDate":"2014-10-14 10:20:35","orderStage":"ip","patientVisitNumber":643660,"patient":{"id":326983,
            "fullName":"Macau Macau","dateOfBirth":"1996-10-09 00:00:00","gender":"Female"},"address":{"address":null,"postalCode":null,"phoneNumber":"","city":null}}');
            $labRequestUrinalysis[] = 
                json_decode('{"cost":null,"receiptNumber":null,"receiptType":null,"labNo":596703,"parentLabNo":596700,"requestingClinician":"frankenstein Dr",
            "investigation":"T. vaginalis","requestDate":"2014-10-14 10:20:35","orderStage":"ip","patientVisitNumber":643660,"patient":{"id":326983,
            "fullName":"Macau Macau","dateOfBirth":"1996-10-09 00:00:00","gender":"Female"},"address":{"address":null,"postalCode":null,"phoneNumber":"","city":null}}');
            $labRequestUrinalysis[] = 
                json_decode('{"cost":null,"receiptNumber":null,"receiptType":null,"labNo":596704,"parentLabNo":596700,"requestingClinician":"frankenstein Dr",
            "investigation":"Yeast cells","requestDate":"2014-10-14 10:20:35","orderStage":"ip","patientVisitNumber":643660,"patient":{"id":326983,
            "fullName":"Macau Macau","dateOfBirth":"1996-10-09 00:00:00","gender":"Female"},"address":{"address":null,"postalCode":null,"phoneNumber":"","city":null}}');
            $labRequestUrinalysis[] = 
                json_decode('{"cost":null,"receiptNumber":null,"receiptType":null,"labNo":596705,"parentLabNo":596700,"requestingClinician":"frankenstein Dr",
            "investigation":"Red blood cells","requestDate":"2014-10-14 10:20:35","orderStage":"ip","patientVisitNumber":643660,"patient":{"id":326983,
            "fullName":"Macau Macau","dateOfBirth":"1996-10-09 00:00:00","gender":"Female"},"address":{"address":null,"postalCode":null,"phoneNumber":"","city":null}}');
            $labRequestUrinalysis[] = 
                json_decode('{"cost":null,"receiptNumber":null,"receiptType":null,"labNo":596706,"parentLabNo":596700,"requestingClinician":"frankenstein Dr",
            "investigation":"Bacteria","requestDate":"2014-10-14 10:20:36","orderStage":"ip","patientVisitNumber":643660,"patient":{"id":326983,
            "fullName":"Macau Macau","dateOfBirth":"1996-10-09 00:00:00","gender":"Female"},"address":{"address":null,"postalCode":null,"phoneNumber":"","city":null}}');
            $labRequestUrinalysis[] = 
                json_decode('{"cost":null,"receiptNumber":null,"receiptType":null,"labNo":596707,"parentLabNo":596700,"requestingClinician":"frankenstein Dr",
            "investigation":"Spermatozoa","requestDate":"2014-10-14 10:20:36","orderStage":"ip","patientVisitNumber":643660,"patient":{"id":326983,
            "fullName":"Macau Macau","dateOfBirth":"1996-10-09 00:00:00","gender":"Female"},"address":{"address":null,"postalCode":null,"phoneNumber":"","city":null}}');
            $labRequestUrinalysis[] = 
                json_decode('{"cost":null,"receiptNumber":null,"receiptType":null,"labNo":596708,"parentLabNo":596700,"requestingClinician":"frankenstein Dr",
            "investigation":"Epithelial cells","requestDate":"2014-10-14 10:20:36","orderStage":"ip","patientVisitNumber":643660,"patient":{"id":326983,
            "fullName":"Macau Macau","dateOfBirth":"1996-10-09 00:00:00","gender":"Female"},"address":{"address":null,"postalCode":null,"phoneNumber":"","city":null}}');
            $labRequestUrinalysis[] = 
                json_decode('{"cost":null,"receiptNumber":null,"receiptType":null,"labNo":596709,"parentLabNo":596700,"requestingClinician":"frankenstein Dr",
            "investigation":"ph","requestDate":"2014-10-14 10:20:36","orderStage":"ip","patientVisitNumber":643660,"patient":{"id":326983,
            "fullName":"Macau Macau","dateOfBirth":"1996-10-09 00:00:00","gender":"Female"},"address":{"address":null,"postalCode":null,"phoneNumber":"","city":null}}');
            $labRequestUrinalysis[] = 
                json_decode('{"cost":null,"receiptNumber":null,"receiptType":null,"labNo":596710,"parentLabNo":596699,"requestingClinician":"frankenstein Dr",
            "investigation":"Urine chemistry","requestDate":"2014-10-14 10:20:36","orderStage":"ip","patientVisitNumber":643660,"patient":{"id":326983,
            "fullName":"Macau Macau","dateOfBirth":"1996-10-09 00:00:00","gender":"Female"},"address":{"address":null,"postalCode":null,"phoneNumber":"","city":null}}');
            $labRequestUrinalysis[] = 
                json_decode('{"cost":null,"receiptNumber":null,"receiptType":null,"labNo":596711,"parentLabNo":596710,"requestingClinician":"frankenstein Dr",
            "investigation":"Glucose","requestDate":"2014-10-14 10:20:36","orderStage":"ip","patientVisitNumber":643660,"patient":{"id":326983,
            "fullName":"Macau Macau","dateOfBirth":"1996-10-09 00:00:00","gender":"Female"},"address":{"address":null,"postalCode":null,"phoneNumber":"","city":null}}');
            $labRequestUrinalysis[] = 
                json_decode('{"cost":null,"receiptNumber":null,"receiptType":null,"labNo":596712,"parentLabNo":596710,"requestingClinician":"frankenstein Dr",
            "investigation":"Ketones","requestDate":"2014-10-14 10:20:36","orderStage":"ip","patientVisitNumber":643660,"patient":{"id":326983,
            "fullName":"Macau Macau","dateOfBirth":"1996-10-09 00:00:00","gender":"Female"},"address":{"address":null,"postalCode":null,"phoneNumber":"","city":null}}');
            $labRequestUrinalysis[] = 
                json_decode('{"cost":null,"receiptNumber":null,"receiptType":null,"labNo":596713,"parentLabNo":596710,"requestingClinician":"frankenstein Dr",
            "investigation":"Proteins","requestDate":"2014-10-14 10:20:36","orderStage":"ip","patientVisitNumber":643660,"patient":{"id":326983,
            "fullName":"Macau Macau","dateOfBirth":"1996-10-09 00:00:00","gender":"Female"},"address":{"address":null,"postalCode":null,"phoneNumber":"","city":null}}');
            $labRequestUrinalysis[] = 
                json_decode('{"cost":null,"receiptNumber":null,"receiptType":null,"labNo":596714,"parentLabNo":596710,"requestingClinician":"frankenstein Dr",
            "investigation":"Blood","requestDate":"2014-10-14 10:20:36","orderStage":"ip","patientVisitNumber":643660,"patient":{"id":326983,
            "fullName":"Macau Macau","dateOfBirth":"1996-10-09 00:00:00","gender":"Female"},"address":{"address":null,"postalCode":null,"phoneNumber":"","city":null}}');
            $labRequestUrinalysis[] = 
                json_decode('{"cost":null,"receiptNumber":null,"receiptType":null,"labNo":596715,"parentLabNo":596710,"requestingClinician":"frankenstein Dr",
            "investigation":"Bilirubin","requestDate":"2014-10-14 10:20:36","orderStage":"ip","patientVisitNumber":643660,"patient":{"id":326983,
            "fullName":"Macau Macau","dateOfBirth":"1996-10-09 00:00:00","gender":"Female"},"address":{"address":null,"postalCode":null,"phoneNumber":"","city":null}}');
            $labRequestUrinalysis[] = 
                json_decode('{"cost":null,"receiptNumber":null,"receiptType":null,"labNo":596716,"parentLabNo":596710,"requestingClinician":"frankenstein Dr",
            "investigation":"Urobilinogen Phenlpyruvic acid","requestDate":"2014-10-14 10:20:37","orderStage":"ip","patientVisitNumber":643660,"patient":{"id":326983,
            "fullName":"Macau Macau","dateOfBirth":"1996-10-09 00:00:00","gender":"Female"},"address":{"address":null,"postalCode":null,"phoneNumber":"","city":null}}');
            $labRequestUrinalysis[] = 
                json_decode('{"cost":null,"receiptNumber":null,"receiptType":null,"labNo":596717,"parentLabNo":596710,"requestingClinician":"frankenstein Dr",
            "investigation":"pH","requestDate":"2014-10-14 10:20:37","orderStage":"ip","patientVisitNumber":643660,"patient":{"id":326983,
            "fullName":"Macau Macau","dateOfBirth":"1996-10-09 00:00:00","gender":"Female"},"address":{"address":null,"postalCode":null,"phoneNumber":"","city":null}}');

             for ($i=0; $i < count($labRequestUrinalysis); $i++) { 

                $dumper = new ExternalDump();
                $dumper->lab_no = $labRequestUrinalysis{$i}->labNo;
                $dumper->parent_lab_no = $labRequestUrinalysis{$i}->parentLabNo;
                // $dumper->test_id = ($i == 0) ? $test_urinalysis_accepted_completed->id : null;
                $dumper->requesting_clinician = $labRequestUrinalysis{$i}->requestingClinician;
                $dumper->investigation = $labRequestUrinalysis{$i}->investigation;
                $dumper->provisional_diagnosis = '';
                $dumper->request_date = $labRequestUrinalysis{$i}->requestDate;
                $dumper->order_stage = $labRequestUrinalysis{$i}->orderStage;
                $dumper->patient_visit_number = $labRequestUrinalysis{$i}->patientVisitNumber;
                $dumper->patient_id = $labRequestUrinalysis{$i}->patient->id;
                $dumper->full_name = $labRequestUrinalysis{$i}->patient->fullName;
                $dumper->dob = $labRequestUrinalysis{$i}->patient->dateOfBirth;
                $dumper->gender = $labRequestUrinalysis{$i}->patient->gender;
                $dumper->address = $labRequestUrinalysis{$i}->address->address;
                $dumper->postal_code = '';
                $dumper->phone_number = $labRequestUrinalysis{$i}->address->phoneNumber;
                $dumper->city = $labRequestUrinalysis{$i}->address->city;
                $dumper->cost = $labRequestUrinalysis{$i}->cost;
                $dumper->receipt_number = $labRequestUrinalysis{$i}->receiptNumber;
                $dumper->receipt_type = $labRequestUrinalysis{$i}->receiptType;
                $dumper->waiver_no = '';
                $dumper->system_id = "sanitas";
                $dumper->save();
            }
            $this->command->info('ExternalDump table seeded');            
        }

        //  Begin seed for prevalence rates report
        //Seed for suppliers
        $supplier = Supplier::find(1);
        if ($supplier) {
            # code...
            $this->command->info('Suppliers already added.');
        }
        else {
            $supplier = Supplier::create(
                array(
                    "name" => "UNICEF",
                    "phone_no" => "0775112233",
                    "email" => "uni@unice.org",
                    "physical_address" => "un-hqtr"

                )
            );
            $this->command->info('Suppliers table seeded');            
        }

        //Seed for metrics
        $metric = Metric::find(1);
        if ($metric) {
            # code...
            $this->command->info('Metrics already exist.');
        }
        else {
            $metric = Metric::create(
                array(
                    "name" => "mg",
                    "description" => "milligram"
                )
            );
            $this->command->info('Metrics table seeded');
        }

        //Seed for commodities
        $commodity = Commodity::find(1);
        if ($commodity) {
            # code...
            $this->command->info('Commodities already exist.');
        }
        else {
            $commodity = Commodity::create(
                array(
                    "name" => "Ampicillin",
                    "description" => "Capsule 250mg",
                    "metric_id" => $metric->id,
                    "unit_price" => "500",
                    "item_code" => "no clue",
                    "storage_req" => "no clue",
                    "min_level" => "100000",
                    "max_level" => "400000")
            );
            $this->command->info('Commodities table seeded');            
        }
        
        //Seed for receipts
        $receipt = Receipt::find(1);
        if ($receipt) {
            # code...
            $this->command->info('Receipts already exist.');
        }
        else {
            $receipt = Receipt::create(
                array(
                    "commodity_id" => $commodity->id,
                    "supplier_id" => $supplier->id, 
                    "quantity" => "130000",
                    "batch_no" => "002720",
                    "expiry_date" => "2018-10-14", 
                    "user_id" => "1")
            );
            $this->command->info('Receipts table seeded');         
        }
        
        //Seed for Top Up Request
        $topUpRequest = TopupRequest::find(1);
        if ($topUpRequest) {
            # code...
            $this->command->info('Top Up Requests already exist.');
        }
        else {
            $topUpRequest = TopupRequest::create(
                array(
                    "commodity_id" => $commodity->id,
                    "test_category_id" => 1,
                    "order_quantity" => "1500",
                    "user_id" => 1,
                    "remarks" => "-")
            );
            $this->command->info('Top Up Requests table seeded');     
        }

        //Seed for Issues
        $issue = Issue::find(1);
        if ($issue) {
            # code...
            $this->command->info('Issues already exist.');
        }
        else {
            Issue::create(
                array(
                    "receipt_id" => $receipt->id,
                    "topup_request_id" => $topUpRequest->id,
                    "quantity_issued" => "1700",
                    "issued_to" => 1,
                    "user_id" => 1,
                    "remarks" => "-")
            );
            $this->command->info('Issues table seeded');            
        }

        //Seed for diseases
        $diseases = Disease::find(1);
        if ($diseases) {
            # code...
            $this->command->info('Diseases already exist.');
        }
        else {
            # create diseases...
            $malaria = Disease::create(array('name' => "Malaria"));
            $typhoid = Disease::create(array('name' => "Typhoid"));
            $dysentry = Disease::create(array('name' => "Shigella Dysentry"));

            $this->command->info("Dieases table seeded");

            # create report diseases...
            $reportDiseases = array();

            foreach ($reportDiseases as $reportDisease) {
                ReportDisease::create($reportDisease);
            }
            $this->command->info("Report Disease table seeded");
        }


        //Seeding for QC
        $lots = Lot::find(1);
        if ($lots) {
            # code...
            $this->command->info('Seeing for Control Relations already done.');
        }
        else {
            $lots = array(
                array('number'=> '0001',
                    'description' => 'First lot',
                    'expiry' => date('Y-m-d H:i:s', strtotime("+6 months")),
                    'instrument_id' => 1),
                array('number'=> '0002',
                    'description' => 'Second lot',
                    'expiry' => date('Y-m-d H:i:s', strtotime("+7 months")),
                    'instrument_id' => 1));
            foreach ($lots as $lot) {
                $lot = Lot::create($lot);
            }
            $this->command->info("Lot table seeded");

            //Control seeding
            $controls = array(
                array('name'=>'Humatrol P', 
                        'description' =>'HUMATROL P control serum has been designed to provide a suitable basis for the quality control (imprecision, inaccuracy) in the clinical chemical laboratory.', 
                        'lot_id' => 1),
                array('name'=>'Full Blood Count', 'description' => 'Né pas touchér', 'lot_id' => 1,)
                );
            foreach ($controls as $control) {
                Control::create($control);
            }
            $this->command->info("Control table seeded");

            //Control measures
            $controlMeasures = array(
                        //Humatrol P
                        array('name' => 'ca', 'unit' => 'mmol', 'control_id' => 1, 'control_measure_type_id' => 1),
                        array('name' => 'pi', 'unit' => 'mmol', 'control_id' => 1, 'control_measure_type_id' => 1),
                        array('name' => 'mg', 'unit' => 'mmol', 'control_id' => 1, 'control_measure_type_id' => 1),
                        array('name' => 'na', 'unit' => 'mmol', 'control_id' => 1, 'control_measure_type_id' => 1),
                        array('name' => 'K', 'unit' => 'mmol', 'control_id' => 1, 'control_measure_type_id' => 1),

                        //Full Blood Count
                        array('name' => 'WBC', 'unit' => 'x 103/uL', 'control_id' => 2, 'control_measure_type_id' => 1),
                        array('name' => 'RBC', 'unit' => 'x 106/uL', 'control_id' => 2, 'control_measure_type_id' => 1),
                        array('name' => 'HGB', 'unit' => 'g/dl', 'control_id' => 2, 'control_measure_type_id' => 1),
                        array('name' => 'HCT', 'unit' => '%', 'control_id' => 2, 'control_measure_type_id' => 1),
                        array('name' => 'MCV', 'unit' => 'fl', 'control_id' => 2, 'control_measure_type_id' => 1),
                        array('name' => 'MCH', 'unit' => 'pg', 'control_id' => 2, 'control_measure_type_id' => 1),
                        array('name' => 'MCHC', 'unit' => 'g/dl', 'control_id' => 2, 'control_measure_type_id' => 1),
                        array('name' => 'PLT', 'unit' => 'x 103/uL', 'control_id' => 2, 'control_measure_type_id' => 1),
                );
            foreach ($controlMeasures as $controlMeasure) {
                ControlMeasure::create($controlMeasure);
            }
            $this->command->info("Control Measure table seeded");

            //Control measure ranges
            $controlMeasureRanges = array(
                    //Humatrol P
                    array('upper_range' => '2.63', 'lower_range' => '7.19', 'control_measure_id' => 1),
                    array('upper_range' => '11.65', 'lower_range' => '15.43', 'control_measure_id' => 2),
                    array('upper_range' => '12.13', 'lower_range' => '19.11', 'control_measure_id' => 3),
                    array('upper_range' => '15.73', 'lower_range' => '25.01', 'control_measure_id' => 4),
                    array('upper_range' => '17.63', 'lower_range' => '20.12', 'control_measure_id' => 5),

                    //Full blood count
                    array('upper_range' => '6.5', 'lower_range' => '7.5', 'control_measure_id' => 6),
                    array('upper_range' => '4.36', 'lower_range' => '5.78', 'control_measure_id' => 7),
                    array('upper_range' => '13.8', 'lower_range' => '17.3', 'control_measure_id' => 8),
                    array('upper_range' => '81.0', 'lower_range' => '95.0', 'control_measure_id' => 9),
                    array('upper_range' => '1.99', 'lower_range' => '2.63', 'control_measure_id' => 10),
                    array('upper_range' => '27.6', 'lower_range' => '33.0', 'control_measure_id' => 11),
                    array('upper_range' => '32.8', 'lower_range' => '36.4', 'control_measure_id' => 12),
                    array('upper_range' => '141', 'lower_range' => ' 320.0', 'control_measure_id' => 13)
                );
            foreach ($controlMeasureRanges as $controlMeasureRange) {
                ControlMeasureRange::create($controlMeasureRange);
            }
            $this->command->info("Control Measure ranges table seeded");

            //Control Tests
            $controlTests = array(
                    array('entered_by'=> 1, 'control_id'=> 2, 'created_at'=>date('Y-m-d', strtotime('-3 days'))),
                    array('entered_by'=> 1, 'control_id'=> 2, 'created_at'=>date('Y-m-d', strtotime('-2 days'))),
                );
            foreach ($controlTests as $controltest) {
                ControlTest::create($controltest);
            }
            $this->command->info("Control test table seeded");

            //Control results
            $controlResults = array(
                    //Results fro Humatrol P
                    array('results' => '2.78', 'control_measure_id' => 1, 'control_test_id' => 1, 'created_at'=>date('Y-m-d', strtotime('-10 days'))),
                    array('results' => '13.56', 'control_measure_id' => 2, 'control_test_id' => 1, 'created_at'=>date('Y-m-d', strtotime('-10 days'))),
                    array('results' => '14.77', 'control_measure_id' => 3, 'control_test_id' => 1, 'created_at'=>date('Y-m-d', strtotime('-10 days'))),
                    array('results' => '25.92', 'control_measure_id' => 4, 'control_test_id' => 1, 'created_at'=>date('Y-m-d', strtotime('-10 days'))),
                    array('results' => '18.87', 'control_measure_id' => 5, 'control_test_id' => 1, 'created_at'=>date('Y-m-d', strtotime('-10 days'))),

                     //Results fro Humatrol P
                    array('results' => '6.78', 'control_measure_id' => 1, 'control_test_id' => 2, 'created_at'=>date('Y-m-d', strtotime('-9 days'))),
                    array('results' => '15.56', 'control_measure_id' => 2, 'control_test_id' => 2, 'created_at'=>date('Y-m-d', strtotime('-9 days'))),
                    array('results' => '18.77', 'control_measure_id' => 3, 'control_test_id' => 2, 'created_at'=>date('Y-m-d', strtotime('-9 days'))),
                    array('results' => '30.92', 'control_measure_id' => 4, 'control_test_id' => 2, 'created_at'=>date('Y-m-d', strtotime('-9 days'))),
                    array('results' => '17.87', 'control_measure_id' => 5, 'control_test_id' => 2, 'created_at'=>date('Y-m-d', strtotime('-9 days'))),
                );
            
            foreach ($controlResults as $controlResult) {
                ControlMeasureResult::create($controlResult);
            }
            $this->command->info("Control results table seeded");
        }
    }

    public function createSpecimen(
            $testStatus,
            $specimenStatus,
            $specimenTypeID,
            $acceptor = 0, $rejector = 0, $rejectReason = ""){

        $values["specimen_type_id"] = $specimenTypeID;
        $values["specimen_status_id"] = $specimenStatus;

        if($specimenStatus == Specimen::ACCEPTED){
            $values["accepted_by"] = $acceptor;
            $values["time_accepted"] = date('Y-m-d H:i:s');
        }
        if($specimenStatus == Specimen::REJECTED){
            $values["rejected_by"] = $rejector;
            $values["rejection_reason_id"] = $rejectReason;
            $values["time_rejected"] = date('Y-m-d H:i:s');
        }
        
        $specimen = Specimen::create($values);

        return $specimen->id;
    }

}
