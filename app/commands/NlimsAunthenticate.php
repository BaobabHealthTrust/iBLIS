<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class NlimsAunthenticate extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nlims:authenticate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'command for authentication in order to create nlims account';

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
			$default_pass = config::get('kblis.nlims_default_password');
			$default_user = config::get('kblis.nlims_default_username');			
			$version = config::get('kblis.nlims_api_version');

			$ch = curl_init($url."/api/".$version."/authenticate/".$default_user."/".$default_pass);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'Accept: application/json',
					'Content-Length: ' . 0)
			);
		
			$response = json_decode(curl_exec($ch));
			$token = $response->data->token;

			if ($response->error == false)
			{
				File::put(public_path()."/token.txt",$token);
				var_dump("authenticated successfuly! can now create user");
			}
			else
			{
				var_dump($response->message);
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
