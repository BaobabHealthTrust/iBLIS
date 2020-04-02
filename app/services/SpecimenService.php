<?php

class SpecimenService {
    public static function createSpecimen($specimen_type, $user, $tracking_number)
    {
        $specimen = new Specimen;
        $specimen->specimen_type_id = $specimen_type;
        $specimen->accepted_by = $user;
        $specimen->tracking_number = $tracking_number;
        $specimen->accession_number = Specimen::assignAccessionNumber();
        $specimen->save();
        return $specimen;
    }
}