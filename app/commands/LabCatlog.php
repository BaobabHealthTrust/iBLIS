<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class LabCatlog extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'LabCatlog:pull';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'This pulls lab catlog and push to NLIMS';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{ 	$short_names = [];
		$panels = [];
		$tests_in_panels = [];
		$rst = DB::select("SELECT * FROM panel_types");
		$specimen_types = [];
		$specimen_types_with_tests = [];
		$tests_in_panels_keep = [];
		$testss = [];
		if (count($rst) >0)
		{
			foreach ($rst as $value)
			{	$panel_name = $value->name;
				$tst = DB::select("SELECT test_types.name
											   FROM test_types INNER JOIN panels ON panels.test_type_id = test_types.id INNER JOIN panel_types ON panel_types.id = panels.panel_type_id
											   WHERE panel_types.name ='$panel_name'"
											 );
				foreach ($tst  as $tests )
				{
					array_push($tests_in_panels, $tests->name);
					if (in_array($tests->name,$tests_in_panels_keep))
					{}
					else
					{
						array_push($tests_in_panels_keep,$tests->name);
					}
				}
				$panels[$panel_name] = $tests_in_panels;
				$tests_in_panels = [];

				$short_names[$value->name] = $value->short_name;
			}
				$specimen = DB::select("SELECT specimen_types.name FROM specimen_types");
				if (count($specimen)>0)
				{
					foreach ($specimen as $sp)
					{
						array_push($specimen_types,$sp->name);
						$tst = DB::select("SELECT test_types.name FROM test_types INNER JOIN testtype_specimentypes ON
								test_types.id = testtype_specimentypes.test_type_id INNER JOIN specimen_types ON specimen_types.id = testtype_specimentypes.specimen_type_id WHERE specimen_types.name='$sp->name'");

						foreach ($tst as $key)
						{
							if (in_array($key->name, $tests_in_panels_keep))
							{
								$pa_keys = array_keys($panels);
								foreach ($pa_keys as $v)
								{
									if (in_array($key->name, $panels[$v]))
									{
										if (in_array($v, $testss))
										{ }
										else
										{
											array_push($testss, $v);
										}
									}

								}
									
							}
							else
							{ 
								array_push($testss,$key->name);
							}
						}

						$specimen_types_with_tests[$sp->name] = $testss;
						$testss = [];
					}
				}

				$sh = DB::select("SELECT test_types.name,test_types.short_name FROM test_types");
				if (count($sh) >0)
				{
					foreach ($sh as $key)
					{
						$short_names[$key->name] = $key->short_name;
					}
				}

			$json = array(
						"KCH2" => array(
								"tests" => $specimen_types_with_tests,
								"samples" => $specimen_types,
								"test_panels" => $panels,
								"test_short_names" => $short_names,
							)
			);


			/*
				"HELO": [{
							"tests" => [{"blood":["bob_sc","dddd_sn"],"sputum":["mayi_s","add_jf","yefe_oop"]}],
							"samples"=> ["blood","sputum"],
							"test_panels"=> $panels,
							"test_short_names" => ["CultureSesnsi"=>"CS","DDD"=>"D"]
				}]
			*/

			$data_string = json_encode($json);
			$ch = curl_init("localhost:3005/push_lab_catalog");
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'Accept: application/json',
					'Content-Length: ' . strlen($data_string))
			);
			$specimen = null;
			$response = json_decode(curl_exec($ch));
			var_dump($response);
			
		}
		
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
