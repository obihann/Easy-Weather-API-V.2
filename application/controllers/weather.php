<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Weather extends CI_Controller {

	public function index()
	{
		echo '{"response" : "good"}';
	}
	
	public function data($lat, $long)
	{
		header('Access-Control-Allow-Origin: *');
		$this->load->model('weather_model');
		$data['conditions'] = $this->weather_model->load_current_conditions($lat, $long);
		echo $data['conditions'];
	}
	
	public function forecast($lat = null, $long = null)
	{
		// check if lat and long are set
		if( $lat != null && $long != null)
		{
			// create data object
			$data = array(
				"lat" => $lat,
				"long" => $long,
				"response" => "json"
			);
			
			$this->load->view('forecast', $data);
		}
		else
		{
			// ToDo: create view for errors
			$this->output->set_output("Lat and Long must be provided.");
		}
	}
}