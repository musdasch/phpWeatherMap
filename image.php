<?php
/**
 * includes the function.php
 */
include 'function.php';

/**
 * Set the content type to a jpag image.
 */
header( 'Content-Type: image/jpeg' );

/**
 * $functions olds the Function objekt for requst the map.
 * @var Functions
 */
$functions = new Functions( 'config.json' );

/**
 * $img holds the map
 * @var resource
 */
$img = $functions->getMap( $_GET['type'], $_GET['date'] );

/**
 * Send the image and destroy dem affter.
 */
imagejpeg( $img );
imagedestroy( $img );
	