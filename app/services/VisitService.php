<?php 

class VisitService {
    public static function createVisit($patient, $visity_type, $ward)
    {
        $visit = new Visit;
        $visit->patient_id = $patient;
        $visit->visit_type = VisitType::find($visity_type)->name;
        $visit->ward_or_location = $ward;
        $visit->save();
        return $visit;
    }

    // public static function getVisitTypeByName($visit_name)
    // {
    //     $visit_type = DB::table('visit_types')->where('name', $visit_name)->first();
    //     return $visit_type ? $visit_type : VisitService::createVisitType($visit_name, null);
    // }

    public static function createVisitType($visitType)
    {
        $rules = array(['name' => 'required|unique:visit_types,name'], ['visit_type_id'=> 'required|unique:visit_types,visit_type_id']);
		$validator = Validator::make(['name' => $visitType['name'], 'visit_type_id'=> $visitType['visit_type_id']], $rules);
		if (!$validator->fails()) {
			$visittype = new VisitType;
            $visittype->name = trim($visitType['name']);	
            $visittype->description = trim($visitType['description']);
            $visittype->reference_id = trim($visitType['visit_type_id']);
            $visittype->save();
			return $visittype;
        }
        return false;
    }

    public static function editVisitType($id, $data){
        $visitType = DB::table('visit_types')->where('reference_id', $id)
        ->update(['name' => trim($data['name']),
                 'description'=>trim($data['description'])]);
        return $visitType;
    }

    public static function getVisitTypes()
	{
		return VisitType::orderBy('name', 'ASC')->get();
    }
    
    public static function deleteVisitType($id){
        return DB::table('visit_types')->where('reference_id', $id)->delete();
    }
}