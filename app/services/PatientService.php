<?php 

class PatientService {
    const VALIDATION_RULES = array (
        'first_name' => 'required',
        'last_name' => 'required',
        'gender' => 'required',
        'dob' => 'required'  
    );

    public static function validate($person){
        $validator = Validator::make($person, PatientService::VALIDATION_RULES);
        return $validator->fails() ?  $validator->errors() : []; 
    }

    public static function createPerson($created_by, $person){
        $patient = new Patient;
        $patient->external_patient_number = $person['external_patient_number'];

        $first_name = $person['first_name'];
        $last_name = $person['last_name'];
        $patient->name = $first_name." ".$last_name;
        $patient->first_name_code = isset($first_name) ? Soundex::encode($first_name)  : null;
        $patient->last_name_code = isset($last_name) ? Soundex::encode($last_name)  : null;
        $patient->gender = $person['gender'];
        $patient->dob = $person['dob'];
        $patient->email = $person['email'];
        $patient->address = $person['address'];
        $patient->phone_number = $person['phone_number'];
        $patient->created_by = $created_by;
        $patient->patient_number = DB::table('patients')->max('id')+1;

        try{
            $patient->save();
            return $patient;
        }catch(QueryException $e){
            Log::error($e);
            return [];
        }
    }
    
    public static function getPatient($created_by, $person)
    {
        $patient = PatientService::getPatientByExternalIdNumber($person['external_patient_number']);
        if(isset($patient)){
            return $patient;
        }
        return PatientService::createPerson($created_by, $person);
    }

    protected static function getPatientByExternalIdNumber($id) {
        return DB::table('patients')->where('external_patient_number', $id)->first();    
    }
}
