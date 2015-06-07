<?php
	/**
	 * Include config.php for edit configs
	 */
	include 'config.php';

	/**
	 * The $config holds the Config objekt.
	 * @var Config
	 */
	$config = new Config( 'config.json' );

	/**
	 * Reset the config.
	 * !!!! For activate the proxy authenticate set PROXY_ACTI of true. !!!!
	 */
	
	// Proxy address
	$config->setConfig( 'PROXY_ADDR', '172.20.10.24' );
	
	// Proxy prot
	$config->setConfig( 'PROXY_PORT', '3128' );

	// The Password for nwe use the base64_encode and reomofe the hash.
	$config->setConfig( 'PROXY_AUTH', base64_encode('LOGIN:PASSWORD') );
	
	// you need a proxy authenticate
	$config->setConfig( 'PROXY_ACTI', false);

	// The weather api URL
	$config->setConfig( 'WETTE_ADDR', 'http://home.gibm.ch/m307/wetter.php' );

	// Paht to the OpenSans font
	$config->setConfig( 'TTF_TEXT', 'fonts/OpenSans-Regular.ttf' );

	// Paht to the Weathericons font for drawing the icons to the Maps
	$config->setConfig( 'TTF_WEAT', 'fonts/weathericons-regular-webfont.ttf' );

	// Cordinates of Zürich
	$config->setConfig( 'ZUER_XCOO', 505 );
	$config->setConfig( 'ZUER_YCOO', 160 );

	// Cordinates of Genf
	$config->setConfig( 'GENF_XCOO', 20 );
	$config->setConfig( 'GENF_YCOO', 490 );

	// Cordinates of Bern
	$config->setConfig( 'BERN_XCOO', 300 );
	$config->setConfig( 'BERN_YCOO', 300 );

	// Cordinates of Basel
	$config->setConfig( 'BASE_XCOO', 290 );
	$config->setConfig( 'BASE_YCOO', 120 );

	// Cordinates of Graubünden
	$config->setConfig( 'GRAU_XCOO', 700 );
	$config->setConfig( 'GRAU_YCOO', 360 );

	// Cordinates of Wallis
	$config->setConfig( 'WALL_XCOO', 300 );
	$config->setConfig( 'WALL_YCOO', 490 );

	// Cordinates of Tessin
	$config->setConfig( 'TESS_XCOO', 500 );
	$config->setConfig( 'TESS_YCOO', 450 );

	// Save the config
	$config->saveConfig();