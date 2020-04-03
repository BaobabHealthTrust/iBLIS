<?php

class Specimen extends Eloquent
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'specimens';

	public $timestamps = false;

	/**
	 * Specimen status constants
	 */
	const NOT_COLLECTED = 1;
	const ACCEPTED = 2;
	const REJECTED = 3;
	
	/**
	 * Enabling soft deletes for specimen details.
	 *
	 * @var boolean
	 */
	// protected $softDelete = true;//it wants deleted at fills,

	/**
	 * Test Phase relationship
	 */
	public function testPhase()
	{
		return $this->belongsTo('TestPhase');
	}
	
	/**
	 * Specimen Status relationship
	 */
	public function specimenStatus()
	{
		return $this->belongsTo('SpecimenStatus');
	}
	
	/**
	 * Specimen Type relationship
	 */
	public function specimenType()
	{
		return $this->belongsTo('SpecimenType');
	}
	
	/**
	 * Rejection Reason relationship
	 */
	public function rejectionReason()
	{
		return $this->belongsTo('RejectionReason');
	}

	/**
	 * Test relationship
	 */
	public function test()
    {
        return $this->hasOne('Test');
    }

    /**
	 * referrals relationship
	 */
	public function referral()
    {
        return $this->belongsTo('Referral');
    }
    
    /**
	 * User (accepted) relationship
	 */
	public function acceptedBy()
	{
		return $this->belongsTo('User', 'accepted_by', 'id');
	}

	public function getUserAcceptedSpecimen($spec)
	{
		$record = DB::select("SELECT name AS user_na FROM  users INNER JOIN specimens ON specimens.accepted_by = users.id WHERE specimens.id ='$spec'");
		if (COUNT($record) >0 )
		{
			
			return $record[0]->user_na;
		}
		else
		{
			return "";
		}

	}

	/**
	 * User (rejected) relationship
	 */
	public function rejectedBy()
	{
		return $this->belongsTo('User', 'rejected_by', 'id');
	}

    /**
	 * Check if specimen is referred
	 *
	 * @return boolean
	 */
    public function isReferred()
    {
    	if(is_null($this->referral))
    	{
    		return false;
    	}
    	else {
    		return true;
    	}
    }

    /**
    * Check if specimen is NOT_COLLECTED
    *
    * @return boolean
    */
    public function isNotCollected()
    {
        if($this->specimen_status_id == Specimen::NOT_COLLECTED)
        {
            return true;
        }
        else {
            return false;
        }
    }
    
    /**
    * Check if specimen is ACCEPTED
    *
    * @return boolean
    */
    public function isAccepted()
    {
        if($this->specimen_status_id == Specimen::ACCEPTED)
        {
            return true;
        }
        else {
            return false;
        }
    }
    
    /**
    * Check if specimen is rejected
    *
    * @return boolean
    */
    public function isRejected()
    {
        if($this->specimen_status_id == Specimen::REJECTED)
        {
            return true;
        }
        else {
            return false;
        }
    }

	public function testTypes(){

		$types = Specimen::join('tests', 'tests.specimen_id', '=', 'specimens.id')
			->join('test_panels', 'test_panels.id', '=', 'tests.panel_id')
			->join('panel_types', 'panel_types.id', '=', 'test_panels.panel_type_id')
			->where('specimens.id', $this->id)
			->whereRaw(' tests.test_status_id NOT IN ('.Test::VOIDED.','.Test::NOT_DONE.')')
			->whereNotNull('tests.panel_id')
			->selectRaw('distinct panel_types.name')->get();

		$tnames = "";
		foreach($types AS $type){
			$tnames = !$tnames ? $type->name : ($tnames.', '.$type->name);
		}

		$types = Specimen::join('tests', 'tests.specimen_id', '=', 'specimens.id')
			->join('test_types', 'test_types.id', '=', 'tests.test_type_id')
			->where('specimens.id', $this->id)
			->whereRaw(' tests.test_status_id NOT IN ('.Test::VOIDED.','.Test::NOT_DONE.')')
			->whereNull('tests.panel_id')
			->selectRaw('distinct test_types.name')->get();

		foreach($types AS $type){
			$tnames = !$tnames ? $type->name : ($tnames.', '.$type->name);
		}
		return $tnames;
	}

	public function printSmallLabels()
	{

		$tests = Test::where("specimen_id", $this->id)->get();
		$result = false;
		foreach($tests AS $test){
			if(count(TestType::where("id", $test->test_type_id)
				->where("print_device", "1")->get()) > 0){
				$result = true;
				break;
			}
		}

		return $result;
	}

	public function testTypesShortNamed(){

		$types = Specimen::join('tests', 'tests.specimen_id', '=', 'specimens.id')
			->join('test_panels', 'test_panels.id', '=', 'tests.panel_id')
			->join('panel_types', 'panel_types.id', '=', 'test_panels.panel_type_id')
			->where('specimens.id', $this->id)
			->whereRaw(' tests.test_status_id NOT IN ('.Test::VOIDED.','.Test::NOT_DONE.')')
			->whereNotNull('tests.panel_id')
			->selectRaw('distinct COALESCE(panel_types.short_name, panel_types.name) As short_name')->get();

		$tnames = "";
		foreach($types AS $type){
			$tnames = !$tnames ? $type->short_name : ($tnames.', '.$type->short_name);
		}

		$types = Specimen::join('tests', 'tests.specimen_id', '=', 'specimens.id')
			->join('test_types', 'test_types.id', '=', 'tests.test_type_id')
			->where('specimens.id', $this->id)
			->whereRaw(' tests.test_status_id NOT IN ('.Test::VOIDED.','.Test::NOT_DONE.')')
			->whereNull('tests.panel_id')
			->selectRaw('distinct COALESCE(test_types.short_name, test_types.name) AS short_name')->get();

		foreach($types AS $type){
			$tnames = !$tnames ? $type->short_name : ($tnames.', '.$type->short_name);
		}
		return $tnames;
	}

	public function labSections(){
		$sections = Specimen::join('tests', 'tests.specimen_id', '=', 'specimens.id')
			->join('test_types', 'test_types.id', '=', 'tests.test_type_id')
			->join('test_categories', 'test_categories.id', '=', 'test_types.test_category_id')
			->where('specimens.id', $this->id)
			->whereRaw(' tests.test_status_id NOT IN ('.Test::VOIDED.','.Test::NOT_DONE.')')
			->selectRaw('distinct test_categories.name')->get();

		$names = "";

		foreach($sections AS $name){
			$names = !$names ? $name->name : $names.$name->name;
		}

		return $names;
	}

	public static function assignAccessionNumber(){
		# Generate the next accession number for specimen registration

		$max_acc_num = null;
		$return_value = null;
		$sentinel = 99999999;

		$code = Config::get('kblis.facility-code');

		$record = DB::select("SELECT * FROM specimens  WHERE accession_number IS NOT NULL ORDER BY id DESC LIMIT 1");

		if(COUNT($record) > 0){
			$max_acc_num = (int)substr($record[0]->accession_number, -8);
			if ($max_acc_num < $sentinel){
				$max_acc_num += 1;
			}else{
				$max_acc_num = 1;
			}
		}else{
			$max_acc_num = 1;
		}

		$max_acc_num = str_pad($max_acc_num, 8, '0', STR_PAD_LEFT);
		$year = date('y');
		$return_value = $code.$year.$max_acc_num;
		return $return_value;
	}

}