<?php

class ResultsService {
    public static function getResultsByAccessionNumber($test_id){
        return DB::table('test_results')->where('test_id', $test_id)->first();
    }
}