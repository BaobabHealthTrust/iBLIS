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
}