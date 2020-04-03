<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class NlimsCreateUser extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nlims:create_user';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'command for creating nlims user acccount';

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
	{			
			$url = config::get('kblis.nlims_controller_ip');			
			$version = config::get('kblis.nlims_api_version');

			$acc_details = array(
				'partner' 	=> config::get('kblis.partner_name'),
				'app_name' 		=> config::get('kblis.app_name'),
				'location'		=> config::get('kblis.district'),
				'username'		=> config::get('kblis.nlims_custome_username'),
				'password'		=> config::get('kblis.nlims_custome_password'),

			);

			$acc = json_encode($acc_details);
			$token = File::get(public_path().'/token.txt');
			$ch = curl_init($url."/api/".$version."/create_user/".$token);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $acc);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'Accept: application/json',
					'Content-Length: ' . strlen($acc))
			);
		
			$response = json_decode(curl_exec($ch));

			$token = $response->data->token;
			File::put(public_path().'/token.txt', $token);
			var_dump($response);
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
