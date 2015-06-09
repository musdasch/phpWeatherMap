<?php
include 'function.php';
$functions = new Functions( 'config.json' );
?>
<!DOCTYPE html>
<html>
	<head>
		<link href="css/foundation.min.css" rel="stylesheet" type="text/css">
		<link href="css/normalize.css" rel="stylesheet" type="text/css">
		<link href="css/style.css" rel="stylesheet" type="text/css">
		<title>Wetter Karte</title>
	</head>
	<body>
		<div class="row">
			<div id="main" class="medium-12 columns">
			
				<?php echo $functions->getDayNavHTML() ?>

			</div>
		</div>
		
		<script src="js/vendor/jquery.js"></script>
		<script src="js/vendor/fastclick.js"></script>
		<script src="js/foundation.min.js"></script>
		<script>$(document).foundation();</script>
	</body>
</html>