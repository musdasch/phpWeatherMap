<?php

/**
 * The Config class handels all config datas
 * @author  Tim Vögtli [tim.voegtli@gibmit.ch]
 * @version 1.0 [firs version]
 */
class Config {

	/**
	 * Holds the config data
	 * @var Array
	 */
	var $config;

	/**
	 * Holds the config.json dir.
	 * @var String
	 */
	var $file;

	/**
	 * The constructor set the the json paht and load the data form the config json.
	 * @param String $file config.json path
	 */
	function __construct($file){
		$this->file = $file;
		$this->config = json_decode( file_get_contents( $file ), true );
	}

	/**
	 * Echo all Configs.
	 */
	function echoAll(){
		echo "<pre>";
		print_r($this->config);
		echo "</pre>";
	}

	/**
	 * The getConfig method return a option value
	 * @param  String $pointer nagihation to config array
	 * @return Mixed           option value
	 */
	function getConfig($pointer){
		return $this->config[$pointer];
	}

	/**
	 * The setConfig method set a option
	 * @param Stirng $pointer Pointer
	 * @param mixed  $value   Value
	 */
	function setConfig($pointer, $value){
		$this->config[$pointer] = $value;
	}

	/**
	 * The saveConfig method write the config to the json.
	 */
	function saveConfig(){
		$configFile = fopen( $this->file, "w" ) or die( "Unable to open file!" );
		fwrite( $configFile, json_encode( $this->config ) );
		fclose( $configFile );
	}

	/**
	 * The loadConfig method lods the configs from the json.
	 */
	function loadConfig(){
		$this->config = json_decode( file_get_contents( $file ), true );
	}
	
}