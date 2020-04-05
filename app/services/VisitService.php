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

    public static function getVisitTypeByName($visit_name)
    {
        $visit_type = DB::table('visit_types')->where('name', $visit_name)->first();
        return $visit_type ? $visit_type : VisitService::createVisitType($visit_name, null);
    }

    public static function createVisitType($name)
    {
        $rules = array('name' => 'required|unique:visit_types,name');
		$validator = Validator::make(['name' => $name], $rules);
		if (!$validator->fails()) {
			$visittype = new VisitType;
			$visittype->name = trim($name);			;
            $visittype->save();
			return $visittype;
		} 
    }

    public static function getVisitTypes()
	{
		return VisitType::orderBy('name', 'ASC')->get();
	}
}