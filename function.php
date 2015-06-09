<?php
/**
 * Inport the config.php
 */
include 'config.php';

/**
 * The Functions class holds all methods for processing the weather map
 * also includes the Config class
 * @see  config.php [hods the config methods]
 * @author  Tim Vögtli [tim.voegtli@gibmit.ch]
 * @version 1.0 [firs version]
 */

class Functions {
	/**
	 * Holds the config object vor loding the configs.
	 * @var Config
	 */
	var $config;

	/**
	 * set the configfile and loads the Config obj.
	 * @param Stiring $configFile holds the path to the configfile.
	 */
	function __construct( $configFile ){
		$this->config = new Config( $configFile );//initialize the $config
	}

	/**
	 * The loadeData method lods the weatherdata from the gibm.ch server
	 * and handels the proxy data
	 * @return String weatherdata
	 */
	function loadeData(){
		$out = null;
		
		if( $this->config->getConfig( 'PROXY_ACTI' ) ){
			//Tihs part is onli for proxy.
			$proxy_data = array(
				'http' => array(
					'proxy' => 'tcp://'.$this->config->getConfig( 'PROXY_ADDR' ).':'.$this->config->getConfig( 'PROXY_PORT' ),
					'request_fulluri' => true,
					'header' => 'Proxy-Authorization: Basic '.$this->config->getConfig( 'PROXY_AUTH' ),
				),
			);

			$out =  file_get_contents( $this->config->getConfig( 'WETTE_ADDR' ), false, stream_context_create( $proxy_data ) );

		} else {

			$out =  file_get_contents( $this->config->getConfig( 'WETTE_ADDR' ) );

		}
		return $out;
	}

	/**
	 * The structuredData method converts the data form gibm.ch server to an
	 * array.
	 * @return Array all data in a array.
	 */
	function structuredData(){
		$data = str_getcsv( $this->loadeData(),"\n" );
		
		for( $i=0; $i<count( $data ); $i++ ){
			$data[$i] = explode( ";", $data[$i] );
		}
		
		return $data;
	}

	/**
	 * The getDataByDate method removs all datas with the rong date.
	 * @param  String $date a date 'YYYY-MM-DD'
	 * @return Array        only the data from one date.
	 */
	function getDataByDate( $date ){
		$data = $this->structuredData();
		/**
		 * Set all objects to null with the rong date.
		 */
		for ( $i=0; $i < count( $data ) ; $i++ ) {
			if( $data[$i][0] != $date ){
				$data[$i] = null;
			}
		}

		/**
		 * remove all objects with null, set a new index and return it.
		 */
		return array_values( array_filter( $data ) );
	}

	/**
	 * The getDates methot gets all dates in a Array.
	 * @return Array all dates as String by 'YYYY-MM-DD'
	 */
	function getDates(){
		$days = $this->structuredData();
		
		/**
		 * Set all dates in the parent element.
		 */
		for ( $i=0; $i < count( $days ) ; $i++ ) { 
			$days[$i] = $days[$i][0];
		}

		/**
		 * Remove all duplicate items, index new and return them.
		 */
		return array_values( array_unique( $days ) );
	}

	/**
	 * The getDays method returns the next six, including the current day in full name and the corresponding date.
	 * @return Array next six, including the current day in full name and corresponding date.
	 */
	function getDays(){
		$days = $this->getDates();
		
		/**
		 * Translate the date to a days name and include the corresponding date.
		 */
		for ( $i=0; $i < count( $days ) ; $i++ ) { 
			$days[$i] = array( $this->toDayName( date( 'w', strtotime( $days[$i] ) ) ), $days[$i] );
		}

		return $days;
	}

	/**
	 * The toDayName method translate an integere ( 0 - 6 ) to a full name.
	 * @param  Integer $day integer ( 0 - 6 ) for monday to sunday.
	 * @return String       name of day.
	 */
	function toDayName( $day ){
		/**
		 * The $days is an array that holds the names by nummbers of days.
		 * @var array
		 */
		$days = array(
			'Sontag',
			'Montag',
			'Dinstag',
			'Mittwoch',
			'Donerstag',
			'Freitag',
			'Samstag'
		);
		
		return $days[$day];
	}

	/**
	 * The getTypeNavHTML method create nav for switching to oder typs of maps
	 * @param  String   $date The date in the format 'YYYY-MM-DD'
	 * @return String         HTML stucture
	 */
	function getTypeNavHTML( $date ){
		$out = null;

		$out .= '<ul class="tabs" data-tab>';

		$out .= '<li class="tab-title active"><a href="#panel-general-'.$date .'">Allgemeine Wetterlage</a></li>';
		$out .= '<li class="tab-title"><a href="#panel-temperature-'.$date .'">Temperatur</a></li>';
		$out .= '<li class="tab-title"><a href="#panel-wind-'.$date .'">Wind</a></li>';
		$out .= '<li class="tab-title"><a href="#panel-pollen-'.$date .'">Pollenflug</a></li>';

		$out .= '</ul>';

		$out .= '<div class="tabs-content">';

		$out .= '<div class="content active" id="panel-general-'.$date .'">';
		$out .= '<div class="row">';
		$out .= '<div class="medium-12 columns">';
		$out .= '<h1>Allgemeine Wetterlage - '.$date.'</h1>';
		$out .= '</div>';
		$out .= '</div>';
		$out .= '<div class="row">';
		$out .= '<div class="medium-12 columns">';
		$out .= '<img src="image.php?date='.$date.'&type=0">';
		$out .= '</div>';
		$out .= '</div>';
		$out .= '<div class="row">';
		$out .= '<div class="medium-3 columns right">';
		$out .= '<a href="image.php?date='.$date.'&type=0" class="button radius" download="map_general_'.$date .'.jpg">Download</a>';
		$out .= '</div>';
		$out .= '</div>';
		$out .= '</div>';


		$out .= '<div class="content" id="panel-temperature-'.$date .'">';
		$out .= '<div class="row">';
		$out .= '<div class="medium-12 columns">';
		$out .= '<h1>Temperatur - '.$date.'</h1>';
		$out .= '</div>';
		$out .= '</div>';
		$out .= '<div class="row">';
		$out .= '<div class="medium-12 columns">';
		$out .= '<img src="image.php?date='.$date.'&type=1">';
		$out .= '</div>';
		$out .= '</div>';
		$out .= '<div class="row">';
		$out .= '<div class="medium-3 columns right">';
		$out .= '<a href="image.php?date='.$date.'&type=1" class="button radius" download="map_temperature_'.$date .'.jpg">Download</a>';
		$out .= '</div>';
		$out .= '</div>';
		$out .= '</div>';

		$out .= '<div class="content" id="panel-wind-'.$date .'">';
		$out .= '<div class="row">';
		$out .= '<div class="medium-12 columns">';
		$out .= '<h1>Wind - '.$date.'</h1>';
		$out .= '</div>';
		$out .= '</div>';
		$out .= '<div class="row">';
		$out .= '<div class="medium-12 columns">';
		$out .= '<img src="image.php?date='.$date.'&type=2">';
		$out .= '</div>';
		$out .= '</div>';
		$out .= '<div class="row">';
		$out .= '<div class="medium-3 columns right">';
		$out .= '<a href="image.php?date='.$date.'&type=2" class="button radius" download="map_wind_'.$date .'.jpg">Download</a>';
		$out .= '</div>';
		$out .= '</div>';
		$out .= '</div>';


		$out .= '<div class="content" id="panel-pollen-'.$date .'">';
		$out .= '<div class="row">';
		$out .= '<div class="medium-12 columns">';
		$out .= '<h1>Pollenflug - '.$date.'</h1>';
		$out .= '</div>';
		$out .= '</div>';
		$out .= '<div class="row">';
		$out .= '<div class="medium-12 columns">';
		$out .= '<img src="image.php?date='.$date.'&type=3">';
		$out .= '</div>';
		$out .= '</div>';
		$out .= '<div class="row">';
		$out .= '<div class="medium-3 columns right">';
		$out .= '<a href="image.php?date='.$date.'&type=3" class="button radius" download="map_pollen_'.$date .'.jpg">Download</a>';
		$out .= '</div>';
		$out .= '</div>';
		$out .= '</div>';

		$out .= '</div>';


		return $out;
	}

	/**
	 * The getWeatherMapHTML method create and return the weathermap page.
	 * @return String a div structure with the navigation of days
	 */
	function getDayNavHTML(){
		$out	= null;

		$out .= '<ul class="tabs" data-tab>';
			$firs	= true;
			foreach ( $this->getDays() as $key => $value ) {
				$out .= '<li class="tab-title';

				if( $firs ){
					$out .= ' active';
					$firs	= false;
				}

				$out .= '" role="presentational" >';
				$out .= '<a href="#panel-'.$value[1].'">'.$value[0].'</a>';
				$out .= '</li>';
			}
		$out .= '</ul>';

		$out .= '<div class="tabs-content">';
			$firs	= true;
			foreach ( $this->getDays() as $key => $value) {
				$out .= '<div class="content';

				if( $firs ){
					$out .= ' active';
					$firs	= false;
				}

				$out .= '" id="panel-'.$value[1].'">';
				$out .= $this->getTypeNavHTML( $value[1] );
				$out .= '</div>';
			}
		$out .= '</div>';

		return $out;
	}

	/**
	 * The getMaps method returns all maps in a list.
	 * @return String HTML for a list.
	 */
	function getMaps(){
		$out = null;

		$out .= '<ul>';

		foreach ( $this->getDays() as $key => $value) {
			$out .= '<li>';
			$out .= '<ul>';
			$out .= '<li><img src="image.php?date='.$value[1].'&type=0"></li>';
			$out .= '<li><img src="image.php?date='.$value[1].'&type=1"></li>';
			$out .= '<li><img src="image.php?date='.$value[1].'&type=2"></li>';
			$out .= '<li><img src="image.php?date='.$value[1].'&type=3"></li>';
			$out .= '</ul>';
			$out .= '</li>';
		}

		$out .= '</ul>';

		return $out;
	}

	/**
	 * the getWeathericon method get the icons by coling the icon name.
	 * @param  String $name name of icon.
	 * @return Char       	Wetericon for the waethericons ttf.
	 */
	function getWeathericon( $name ){
		$weathericons = array(
			'1'			=> '&#xF00D;',	// sunny
			'2'			=> '&#xF002;',	// cloudy
			'3'			=> '&#xF019;',	// rain
			'4'			=> '&#xF016;',	// lightning
			'5'			=> '&#xF016;',	// snow
			'NN'		=> '&#xF04c;',	// no wind
			'N'			=> '&#xF058;',	// north
			'NO'		=> '&#xF057;',	// north east
			'O'			=> '&#xF04d;',	// east
			'SO'		=> '&#xF088;',	// south east
			'S'			=> '&#xF044;',	// south
			'SW'		=> '&#xF043;',	// south west
			'W'			=> '&#xF048;',	// west
			'NW'		=> '&#xF087;',	// north west
			'celsius'	=> '&#xF03c;',	// celsius simbol
			'pollen'	=> '&#xF077;'	// pollen simbol
		);
		return $weathericons[$name];
	}

	/**
	 * The getRegionName method returns the full name of the region caled bei nummber.
	 * 1 = Zürich
	 * 2 = Genf
	 * 3 = Bern
	 * 4 = Basel
	 * 5 = Graubünden
	 * 6 = Wallis
	 * 7 = Tessin
	 * @param  Integer $index nummber of region
	 * @return String         Full name of region
	 */
	function getRegionName( $index ){
		$regionName = array(
			'1'			=> 'Zürich',
			'2'			=> 'Genf',
			'3'			=> 'Bern',
			'4'			=> 'Basel',
			'5'			=> 'Graubünden',
			'6'			=> 'Wallis',
			'7'			=> 'Tessin'
		);
		return $regionName[$index];
	}

	/**
	 * The getPollenIntensity method returns a full text
	 * of the intensity.
	 * 1 = keine Belastung
	 * 2 = schwach
	 * 3 = mässig
	 * 4 = stark
	 * @param  Integer $index Index of intensity.
	 * @return String         Full name of intensity.
	 */
	function getPollenIntensity( $index ){
		$pollenIntensity = array(
			'keine Belastung',
			'schwach',
			'mässig',
			'stark'
		);
		return $pollenIntensity[$index];
	}


	/**
	 * The drayIcon method draw a simbol in the image.
	 * @param  Resource $img   The image resource
	 * @param  Stiring  $name  The Name of simbol
	 * @param  Integer  $x     The x coordinate
	 * @param  Integer  $y     The y coordinate
	 * @param  Integer  $size  The size of icon
	 */
	function drawIcon( $img, $name, $x, $y, $size ){
		ImageTTFText( 
			$img,
			$size,
			0,
			$x,
			$y,
			0x000000,
			$this->config->getConfig( 'TTF_WEAT' ),
			$this->getWeathericon( $name )
		);
	}

	/**
	 * The getMapImage method lods the basic map.
	 * @return resource returns the map.
	 */
	function getMapImage(){
		$out	= null;
		
		/**
		 * Check of image type.
		 * @var Integer hods the typ of image ([1] Graphics Interchange, [2] Joint Photographic Experts Group, [3] Portable Network Graphics)
		 */
		$type	= exif_imagetype( $this->config->getConfig( 'MAP_PATH' ) );

		/**
		 * Loding the image with the applicable method.
		 */
		switch ($type) { 
			case 1 : 
				$out = imageCreateFromGif( $this->config->getConfig( 'MAP_PATH' ) ); 
			break; 
			case 2 : 
				$out = imageCreateFromJpeg( $this->config->getConfig( 'MAP_PATH' ) ); 
			break; 
			case 3 : 
				$out = imageCreateFromPng( $this->config->getConfig( 'MAP_PATH' ) ); 
			break; 
		}  

		return $out;
	}

	/**
	 * The dataOfRegion method is for filtering by region code,
	 * 1 = Zürich
	 * 2 = Genf
	 * 3 = Bern
	 * 4 = Basel
	 * 5 = Graubünden
	 * 6 = Wallis
	 * 7 = Tessin
	 * @param  Integer $code Code of region
	 * @param  Array   $data A array with weather data
	 * @return Array         Weather data from a region
	 */
	function dataOfRegion( $code, $data ){
		
		for( $i=0; $i < count($data); $i++ ) { 
			if( $data[$i][1] == $code){
				$data = $data[$i];
				break;
			}
		}

		return $data;
	}

	function drawWeatherSituation( $img, $name, $x, $y, $size, $region ){
		/**
		 * Draw a filled rectangle to the coordinates
		 * for beder reading.
		 */
		imagefilledrectangle(
			$img,
			$x - 10,
			$y - 100,
			$x + 170,
			$y + 45,
			0xFFFFFF
		);

		/**
		 * Draw the frame to the coordinates.
		 */
		imagerectangle(
			$img,
			$x - 10,
			$y - 100,
			$x + 170,
			$y + 45,
			0x000000
		);

		/**
		 * Draw the frame for the lable.
		 */
		imagerectangle(
			$img,
			$x - 10,
			$y - 100,
			$x + 170,
			$y - 70,
			0x000000
		);

		/**
		 * draw the regon name.
		 */
		ImageTTFText( 
			$img,
			20,
			0,
			$x,
			$y - 75,
			0x000000,
			$this->config->getConfig( 'TTF_TEXT' ),
			$this->getRegionName( $region )
		);

		/**
		 * Draw the icon
		 */
		$this->drawIcon(
			$img,
			$name,
			$x + 25,
			$y + 20,
			$size
		);
	}

	/**
	 * The getWeatherSituationMap draw the WeatherSituationMap by date and return
	 * the resource
	 * @param  String   $date The date in the format 'YYYY-MM-DD'
	 * @return resource       it returns the WeatherSituationMap
	 */
	function getWeatherSituationMap( $date ){
		$img	= $this->getMapImage();
		$data	= $this->getDataByDate( $date );

		$this->drawWeatherSituation( $img, $this->dataOfRegion( '1', $data )[2], $this->config->getConfig( 'ZUER_XCOO' ), $this->config->getConfig( 'ZUER_YCOO' ), 60, $this->dataOfRegion( '1', $data )[1] );	// Weather Zürich
		$this->drawWeatherSituation( $img, $this->dataOfRegion( '2', $data )[2], $this->config->getConfig( 'GENF_XCOO' ), $this->config->getConfig( 'GENF_YCOO' ), 60, $this->dataOfRegion( '2', $data )[1] );	// Weather Genf
		$this->drawWeatherSituation( $img, $this->dataOfRegion( '3', $data )[2], $this->config->getConfig( 'BERN_XCOO' ), $this->config->getConfig( 'BERN_YCOO' ), 60, $this->dataOfRegion( '3', $data )[1] );	// Weather Bern
		$this->drawWeatherSituation( $img, $this->dataOfRegion( '4', $data )[2], $this->config->getConfig( 'BASE_XCOO' ), $this->config->getConfig( 'BASE_YCOO' ), 60, $this->dataOfRegion( '4', $data )[1] );	// Weather Basel
		$this->drawWeatherSituation( $img, $this->dataOfRegion( '5', $data )[2], $this->config->getConfig( 'GRAU_XCOO' ), $this->config->getConfig( 'GRAU_YCOO' ), 60, $this->dataOfRegion( '5', $data )[1] );	// Weather Graubünden
		$this->drawWeatherSituation( $img, $this->dataOfRegion( '6', $data )[2], $this->config->getConfig( 'WALL_XCOO' ), $this->config->getConfig( 'WALL_YCOO' ), 60, $this->dataOfRegion( '6', $data )[1] );	// Weather Wallis
		$this->drawWeatherSituation( $img, $this->dataOfRegion( '7', $data )[2], $this->config->getConfig( 'TESS_XCOO' ), $this->config->getConfig( 'TESS_YCOO' ), 60, $this->dataOfRegion( '7', $data )[1] );	// Weather Tessin

		return $img;
	}

	/**
	 * The drawTemperature method drawas the Temperature on the map.
	 * @param  resource $img Map resource
	 * @param  Integer  $max Max temperture in celsius
	 * @param  Integer  $min Min temperture in celsius
	 * @param  Integer  $x   The x coordinate
	 * @param  Integer  $y   The x coordinate
	 * @param  Integer  $region  Region code
	 */
	function drawTemperature( $img, $max, $min, $x, $y, $region){
		/**
		 * Set $colMax to the right color
		 * over 30 celsius to read
		 * under 0 celsius to blue
		 * betwin to withe
		 */
		if( $max>30 ){
			$colMax = 0xFFAAAA;
		} elseif ( $max<0 ) {
			$colMax = 0xAAAAFF;
		} else {
			$colMax = 0xFFFFFF;
		}

		/**
		 * Set $colMin to the right color
		 * over 30 celsius to read
		 * under 0 celsius to blue
		 * betwin to withe
		 */
		if( $min>30 ){
			$colMin = 0xFFAAAA;
		} elseif ( $min<0 ) {
			$colMin = 0xAAAAFF;
		} else {
			$colMin = 0xFFFFFF;
		}

		/**
		 * Draw the filled rectangle for max temperature.
		 * filled with the colMax
		 */
		imagefilledrectangle(
			$img,
			$x - 10,
			$y - 70,
			$x + 170,
			$y - 40,
			0xFFFFFF
		);

		/**
		 * Draw the frame for max temperature.
		 */
		imagerectangle(
			$img,
			$x - 10,
			$y - 70,
			$x + 170,
			$y - 40,
			0x000000
		);
		
		/**
		 * Draw the filled rectangle for max temperature.
		 * filled with the colMax
		 */
		imagefilledrectangle(
			$img,
			$x - 10,
			$y - 40,
			$x + 170,
			$y - 10,
			$colMax
		);

		/**
		 * Draw the frame for max temperature.
		 */
		imagerectangle(
			$img,
			$x - 10,
			$y - 40,
			$x + 170,
			$y - 10,
			0x000000
		);

		/**
		 * Draw the filled rectangle for min temperature.
		 * filled with the $colMin.
		 */
		imagefilledrectangle(
			$img,
			$x - 10,
			$y - 10,
			$x + 170,
			$y + 20,
			$colMin
		);

		/**
		 * Draw the frame for min temperature.
		 */
		imagerectangle(
			$img,
			$x - 10,
			$y - 10,
			$x + 170,
			$y + 20,
			0x000000
		);

		/**
		 * draw the regon name.
		 */
		ImageTTFText( 
			$img,
			20,
			0,
			$x,
			$y - 45,
			0x000000,
			$this->config->getConfig( 'TTF_TEXT' ),
			$this->getRegionName( $region )
		);

		/**
		 * draw the max lable.
		 */
		ImageTTFText( 
			$img,
			20,
			0,
			$x,
			$y - 15,
			0x000000,
			$this->config->getConfig( 'TTF_TEXT' ),
			'MAX:'
		);

		/**
		 * draw the max temperature.
		 */
		ImageTTFText( 
			$img,
			20,
			0,
			$x + 80,
			$y - 15,
			0x000000,
			$this->config->getConfig( 'TTF_TEXT' ),
			$max
		);

		/**
		 * draw the celsius icon for maxTemperature.
		 */
		$this->drawIcon(
			$img,
			'celsius',
			$x + 140,
			$y - 10,
			28
		);

		/**
		 * draw the min lable.
		 */
		ImageTTFText( 
			$img,
			20,
			0,
			$x,
			$y + 15,
			0x000000,
			$this->config->getConfig( 'TTF_TEXT' ),
			'MIN:'
		);

		/**
		 * draw the min temperature.
		 */
		ImageTTFText( 
			$img,
			20,
			0,
			$x + 80,
			$y + 15,
			0x000000,
			$this->config->getConfig( 'TTF_TEXT' ),
			$min
		);

		/**
		 * draw the celsius icon for minTemperature.
		 */
		$this->drawIcon(
			$img,
			'celsius',
			$x+140,
			$y+21,
			28
		);
	}

	/**
	 * The getTemperatureMap method create the TemperatureMap and return them.
	 * @param  String   $date The date in the format 'YYYY-MM-DD'
	 * @return resource       it returns the TemperatureMap
	 */
	function getTemperatureMap( $date ){
		$img	= $this->getMapImage();
		$data	= $this->getDataByDate( $date );

		$this->drawTemperature( $img, explode( '/', $this->dataOfRegion( '1', $data )[3] )[1], explode( '/', $this->dataOfRegion( '1', $data )[3] )[0], $this->config->getConfig( 'ZUER_XCOO' ), $this->config->getConfig( 'ZUER_YCOO' ), $this->dataOfRegion( '1', $data )[1] );	// Temperature Zürich
		$this->drawTemperature( $img, explode( '/', $this->dataOfRegion( '2', $data )[3] )[1], explode( '/', $this->dataOfRegion( '2', $data )[3] )[0], $this->config->getConfig( 'GENF_XCOO' ), $this->config->getConfig( 'GENF_YCOO' ), $this->dataOfRegion( '2', $data )[1] );	// Temperature Genf
		$this->drawTemperature( $img, explode( '/', $this->dataOfRegion( '3', $data )[3] )[1], explode( '/', $this->dataOfRegion( '3', $data )[3] )[0], $this->config->getConfig( 'BERN_XCOO' ), $this->config->getConfig( 'BERN_YCOO' ), $this->dataOfRegion( '3', $data )[1] );	// Temperature Bern
		$this->drawTemperature( $img, explode( '/', $this->dataOfRegion( '4', $data )[3] )[1], explode( '/', $this->dataOfRegion( '4', $data )[3] )[0], $this->config->getConfig( 'BASE_XCOO' ), $this->config->getConfig( 'BASE_YCOO' ), $this->dataOfRegion( '4', $data )[1] );	// Temperature Basel
		$this->drawTemperature( $img, explode( '/', $this->dataOfRegion( '5', $data )[3] )[1], explode( '/', $this->dataOfRegion( '5', $data )[3] )[0], $this->config->getConfig( 'GRAU_XCOO' ), $this->config->getConfig( 'GRAU_YCOO' ), $this->dataOfRegion( '5', $data )[1] );	// Temperature Graubünden
		$this->drawTemperature( $img, explode( '/', $this->dataOfRegion( '6', $data )[3] )[1], explode( '/', $this->dataOfRegion( '6', $data )[3] )[0], $this->config->getConfig( 'WALL_XCOO' ), $this->config->getConfig( 'WALL_YCOO' ), $this->dataOfRegion( '6', $data )[1] );	// Temperature Wallis
		$this->drawTemperature( $img, explode( '/', $this->dataOfRegion( '7', $data )[3] )[1], explode( '/', $this->dataOfRegion( '7', $data )[3] )[0], $this->config->getConfig( 'TESS_XCOO' ), $this->config->getConfig( 'TESS_YCOO' ), $this->dataOfRegion( '7', $data )[1] );	// Temperature Tessin

		return $img;

	}

	/**
	 * The drawWind method draw the wind information to the map.
	 * @param  resource $img       Map resource
	 * @param  String   $direction Wind direction
	 * @param  Integer  $speed     wind speed
	 * @param  Integer  $x         The x coordinate
	 * @param  Integer  $y         The y coordinate
	 * @param  Integer  $region    Region code
	 */
	function drawWind( $img, $direction, $speed, $x, $y, $region ){
		/**
		 * Draw a filled rectangle to the coordinates
		 * for beder reading.
		 */
		imagefilledrectangle(
			$img,
			$x - 10,
			$y - 100,
			$x + 160,
			$y + 45,
			0xFFFFFF
		);

		/**
		 * Draw the frame to the coordinates.
		 */
		imagerectangle(
			$img,
			$x - 10,
			$y - 100,
			$x + 160,
			$y + 45,
			0x000000
		);

		/**
		 * Draw the frame for the lable.
		 */
		imagerectangle(
			$img,
			$x - 10,
			$y - 100,
			$x + 160,
			$y - 70,
			0x000000
		);

		/**
		 * Draw the frame for the value.
		 */
		imagerectangle(
			$img,
			$x - 10,
			$y + 15,
			$x + 160,
			$y + 45,
			0x000000
		);

		/**
		 * Draw the wind direction to the map.
		 */
		$this->drawIcon(
			$img,
			$direction,
			$x + 60,
			$y,
			60
		);

		/**
		 * Draw the name of region.
		 */
		ImageTTFText( 
			$img,
			20,
			0,
			$x - 5,
			$y - 75,
			0x000000,
			$this->config->getConfig( 'TTF_TEXT' ),
			$this->getRegionName( $region ) 
		);

		/**
		 * Draw the wind speed to the map.
		 */
		ImageTTFText( 
			$img,
			15,
			0,
			$x - 5,
			$y + 37,
			0x000000,
			$this->config->getConfig( 'TTF_TEXT' ),
			$speed.' km/h'
		);
	}

	/**
	 * The getWindMap method creats the Windmap and return it.
	 * @param  String   $date The date in the format 'YYYY-MM-DD'
	 * @return resource       it returns the WindMap
	 */
	function getWindMap( $date ){
		$img	= $this->getMapImage();
		$data	= $this->getDataByDate( $date );

		$this->drawWind( $img, explode( '/', $this->dataOfRegion( '1', $data )[4] )[0], explode( '/', $this->dataOfRegion( '1', $data )[4] )[1], $this->config->getConfig( 'ZUER_XCOO' ), $this->config->getConfig( 'ZUER_YCOO' ), $this->dataOfRegion( '1', $data )[1] );	// Wind Zürich
		$this->drawWind( $img, explode( '/', $this->dataOfRegion( '2', $data )[4] )[0], explode( '/', $this->dataOfRegion( '2', $data )[4] )[1], $this->config->getConfig( 'GENF_XCOO' ), $this->config->getConfig( 'GENF_YCOO' ), $this->dataOfRegion( '2', $data )[1] );	// Wind Genf
		$this->drawWind( $img, explode( '/', $this->dataOfRegion( '3', $data )[4] )[0], explode( '/', $this->dataOfRegion( '3', $data )[4] )[1], $this->config->getConfig( 'BERN_XCOO' ), $this->config->getConfig( 'BERN_YCOO' ), $this->dataOfRegion( '3', $data )[1] );	// Wind Bern
		$this->drawWind( $img, explode( '/', $this->dataOfRegion( '4', $data )[4] )[0], explode( '/', $this->dataOfRegion( '4', $data )[4] )[1], $this->config->getConfig( 'BASE_XCOO' ), $this->config->getConfig( 'BASE_YCOO' ), $this->dataOfRegion( '4', $data )[1] );	// Wind Basel
		$this->drawWind( $img, explode( '/', $this->dataOfRegion( '5', $data )[4] )[0], explode( '/', $this->dataOfRegion( '5', $data )[4] )[1], $this->config->getConfig( 'GRAU_XCOO' ), $this->config->getConfig( 'GRAU_YCOO' ), $this->dataOfRegion( '5', $data )[1] );	// Wind Graubünden
		$this->drawWind( $img, explode( '/', $this->dataOfRegion( '6', $data )[4] )[0], explode( '/', $this->dataOfRegion( '6', $data )[4] )[1], $this->config->getConfig( 'WALL_XCOO' ), $this->config->getConfig( 'WALL_YCOO' ), $this->dataOfRegion( '6', $data )[1] );	// Wind Wallis
		$this->drawWind( $img, explode( '/', $this->dataOfRegion( '7', $data )[4] )[0], explode( '/', $this->dataOfRegion( '7', $data )[4] )[1], $this->config->getConfig( 'TESS_XCOO' ), $this->config->getConfig( 'TESS_YCOO' ), $this->dataOfRegion( '7', $data )[1] );	// Wind Tessin
		
		return $img;
	}

	/**
	 * The drawPollen method draw the pollen intensity to the map. 
	 * @param  resource $img       The map resource
	 * @param  Integer  $intensity The value of pollen intensity 
	 * @param  Integer  $x         The x coordinate
	 * @param  Integer  $y         The y coordinate
	 * @param  Integer  $region    Region code
	 */
	function drawPollen( $img, $intensity, $x, $y, $region){
		$color = 0xFFAAAA;

		switch ($intensity) { 
			case 0 : 
				$color = 0x55FF55;
			break; 
			case 1 : 
				$color = 0xFFFFAA;
			break; 
			case 2 : 
				$color = 0xFFAAAA;
			break; 
			case 3 : 
				$color = 0xFF5555;
			break; 
		}

		/**
		 * Draw a filled rectangle to the coordinates
		 * for beder reading.
		 */
		imagefilledrectangle(
			$img,
			$x - 10,
			$y - 100,
			$x + 160,
			$y + 50,
			0xFFFFFF
		);

		/**
		 * Draw the frame to the coordinates.
		 */
		imagerectangle(
			$img,
			$x - 10,
			$y - 100,
			$x + 160,
			$y + 50,
			0x000000
		);

		/**
		 * Draw the frame for the lable.
		 */
		imagerectangle(
			$img,
			$x - 10,
			$y - 100,
			$x + 160,
			$y - 70,
			0x000000
		);

		/**
		 * Draw the frame for the value.
		 */
		imagerectangle(
			$img,
			$x - 10,
			$y + 20,
			$x + 160,
			$y + 50,
			0x000000
		);

		/**
		 * Draw the background of pollen with the rigt color.
		 */
		imagefilledarc(
			$img,
			$x + 74,
			$y - 26,
			80,
			80,
			0,
			360,
			$color,
			0
		);

		/**
		 * Draw the from for pollen icon.
		 */
		imagearc(
			$img,
			$x + 74,
			$y - 26,
			80,
			80,
			0,
			360,
			0x000000
		);

		/**
		 * Draw the pollen icon
		 */
		$this->drawIcon(
			$img,
			'pollen',
			$x + 51,
			$y - 5,
			40
		);

		/**
		 * Draw the name of region.
		 */
		ImageTTFText( 
			$img,
			20,
			0,
			$x - 5,
			$y - 75,
			0x000000,
			$this->config->getConfig( 'TTF_TEXT' ),
			$this->getRegionName( $region ) 
		);

		/**
		 * Draw the name of region.
		 */
		ImageTTFText( 
			$img,
			15,
			0,
			$x - 5,
			$y + 42,
			0x000000,
			$this->config->getConfig( 'TTF_TEXT' ),
			$this->getPollenIntensity( $intensity )
		);
	}

	/**
	 * The getPollenMap method create and return the Pollen map
	 * @param  String   $date The date in the format 'YYYY-MM-DD'
	 * @return resource       Resource of map
	 */
	function getPollenMap( $date ){
		$img	= $this->getMapImage();
		$data	= $this->getDataByDate( $date );

		$this->drawPollen( $img, $this->dataOfRegion( '1', $data )[5], $this->config->getConfig( 'ZUER_XCOO' ), $this->config->getConfig( 'ZUER_YCOO' ), $this->dataOfRegion( '1', $data )[1] );	// Pollen Zürich
		$this->drawPollen( $img, $this->dataOfRegion( '2', $data )[5], $this->config->getConfig( 'GENF_XCOO' ), $this->config->getConfig( 'GENF_YCOO' ), $this->dataOfRegion( '2', $data )[1] );	// Pollen Genf
		$this->drawPollen( $img, $this->dataOfRegion( '3', $data )[5], $this->config->getConfig( 'BERN_XCOO' ), $this->config->getConfig( 'BERN_YCOO' ), $this->dataOfRegion( '3', $data )[1] );	// Pollen Bern
		$this->drawPollen( $img, $this->dataOfRegion( '4', $data )[5], $this->config->getConfig( 'BASE_XCOO' ), $this->config->getConfig( 'BASE_YCOO' ), $this->dataOfRegion( '4', $data )[1] );	// Pollen Basel
		$this->drawPollen( $img, $this->dataOfRegion( '5', $data )[5], $this->config->getConfig( 'GRAU_XCOO' ), $this->config->getConfig( 'GRAU_YCOO' ), $this->dataOfRegion( '5', $data )[1] );	// Pollen Graubünden
		$this->drawPollen( $img, $this->dataOfRegion( '6', $data )[5], $this->config->getConfig( 'WALL_XCOO' ), $this->config->getConfig( 'WALL_YCOO' ), $this->dataOfRegion( '6', $data )[1] );	// Pollen Wallis
		$this->drawPollen( $img, $this->dataOfRegion( '7', $data )[5], $this->config->getConfig( 'TESS_XCOO' ), $this->config->getConfig( 'TESS_YCOO' ), $this->dataOfRegion( '7', $data )[1] );	// Pollen Tessin

		return $img;
	}

	/**
	 * The getMap method get the map by date and the type.
	 * Types:
	 * 0 = Weather Situation Map
	 * 1 = Temperature Map
	 * 2 = Wind Map
	 * 3 = Pollen Map
	 * @param  Integer  $typ  The index nummber of map type
	 * @param  String   $date The date in the format 'YYYY-MM-DD'
	 * @return resource       Resource of map returns null if it not work
	 */
	function getMap( $typ, $date ){
		$img = null;
		switch ($typ) { 
			case 0 : 
				$img = $this->getWeatherSituationMap( $date );
			break; 
			case 1 : 
				$img = $this->getTemperatureMap( $date );
			break; 
			case 2 : 
				$img = $this->getWindMap( $date );
			break; 
			case 3 : 
				$img = $this->getPollenMap( $date );
			break; 
		}

		return $img;
	}
}
