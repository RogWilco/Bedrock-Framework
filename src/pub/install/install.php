<?php
/**
 * Installer Execution Script
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 04/13/2009
 * @updated 04/13/2009
 */

// Imports
require_once 'Installer.php';

// Setup
Installer::init();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Bedrock Framework &raquo; Install</title>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<link rel="stylesheet" type="text/css" href="style.css" />
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/jquery.scrollto.js"></script>
		<script type="text/javascript">
			<!--

			$(document).ready(function() {
				parent.installComplete();
			});

			//-->
		</script>
	</head>
	<body id="output">
		<?php Installer::install() ?>
	</body>
</html>