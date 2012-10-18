<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Weather extends CI_Controller 
{		
	public function __construct()
	{
		parent::__construct();
		
		// enable caching for 12 hours
		$this->output->cache(720);
		
		// set header data
		$this->output->set_content_type('application/json');
		$this->output->set_header('Access-Control-Allow-Origin: *');
		
		// load session library
		$this->load->library('session');
	}
	
	public function _output($output)
	{
		// if cache is enabled log file
		if ($this->output->cache_expiration > 0)
		{
			$this->output->_write_cache($output);
		}

		// finally print to screen
		echo $output;
	}

	public function index()
	{
		$this->output->set_output("{'response' : 'good'}");
	}

	public function full($lat, $long)
	{
		// set api url and key
		$url = 'http://api.wunderground.com/api/API_KEY/conditions/forecast/q/LAT,LONG.json';
		$key = 'f30ae1d34ad67d49';
		
		// start building url
		$source = str_replace("API_KEY", $key, $url);
		
		// check if lat and long are set
		if( $lat != null && $long != null)
		{
			$source = str_replace("LAT", $lat, $source);
			$source = str_replace("LONG", $long, $source);
		}
		// acquire IP from session and attempt to get weather
		else
		{
			$sessionData = $this->session->all_userdata();
			$source = str_replace("LAT,LONG.json", "autoip.json?geo_ip=" . $sessionData['ip_address'], $source);
		}
		
		$response = file_get_contents($source);
		
		// create data object
		$data = array(
			"json" => $response
		);
		
		$this->output->append_output("{");
		
		$this->output->append_output('"conditions": ');
		// load view
		$content = $this->load->view('conditions', $data, true);
		$this->output->append_output($content);	
		
		$this->output->append_output(",");
		
		$this->output->append_output('"forecast": ');
		$content = $this->load->view('forecast', $data, true);
		$this->output->append_output($content);	
		$this->output->append_output("}");
	}
	
	public function conditions($lat = null, $long = null)
	{
		// set api url and key
		$url = 'http://api.wunderground.com/api/API_KEY/conditions/q/LAT,LONG.json';
		$key = 'f30ae1d34ad67d49';
		
		// start building url
		$source = str_replace("API_KEY", $key, $url);
		
		// check if lat and long are set
		if( $lat != null && $long != null)
		{
			$source = str_replace("LAT", $lat, $source);
			$source = str_replace("LONG", $long, $source);
		}
		// acquire IP from session and attempt to get weather
		else
		{
			$sessionData = $this->session->all_userdata();
			$source = str_replace("LAT,LONG.json", "autoip.json?geo_ip=" . $sessionData['ip_address'], $source);
		}
		
		$response = file_get_contents($source);
		
		// create data object
		$data = array(
			"json" => $response
		);
		
		// load view
		$content = $this->load->view('conditions', $data, true);
		$this->output->append_output($content);	
	}
	
	public function forecast($lat = null, $long = null)
	{
		// set api url and key
		$url = 'http://api.wunderground.com/api/API_KEY/forecast/q/LAT,LONG.json';
		$key = 'f30ae1d34ad67d49';
		
		// start building url
		$source = str_replace("API_KEY", $key, $url);
		
		// check if lat and long are set
		if( $lat != null && $long != null)
		{
			$source = str_replace("LAT", $lat, $source);
			$source = str_replace("LONG", $long, $source);
		}
		// acquire IP from session and attempt to get weather
		else
		{
			$sessionData = $this->session->all_userdata();
			$source = str_replace("LAT,LONG.json", "autoip.json?geo_ip=" . $sessionData['ip_address'], $source);
		}
		
		$response = file_get_contents($source);
		
		// create data object
		$data = array(
			"json" => $response
		);
		
		// load view
		$content = $this->load->view('forecast', $data, true);
		$this->output->append_output($content);
	}
}