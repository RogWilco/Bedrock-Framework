<?php
/**
 * Installer Index Page
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 04/08/2009
 * @updated 04/08/2009
 */

// Imports
require_once 'Installer.php';

// Setup
Installer::init();

switch($_POST['goto']) {
	default:
	case 'start':
		Installer::reset();
		$page = 'step_start.php';
		$step = 'start';
		break;

	// Step 01: Application Details
	case 1:
		if(Installer::process()) {
			$page = 'step_01.php';
			$step = 1;
		}
		else {
			$page = 'step_' . str_pad($_POST['step'], 2, '0', STR_PAD_LEFT) . '.php';
			$step = $_POST['step'];
		}
		break;

	// Step 02: Directories
	case 2:
		if(Installer::process()) {
			$page = 'step_02.php';
			$step = 2;
		}
		else {
			$page = 'step_' . str_pad($_POST['step'], 2, '0', STR_PAD_LEFT) . '.php';
			$step = $_POST['step'];
		}
		
		break;

	// Step 03: Database
	case 3:
		if(Installer::process()) {
			$page = 'step_03.php';
			$step = 3;
		}
		else {
			$page = 'step_' . str_pad($_POST['step'], 2, '0', STR_PAD_LEFT) . '.php';
			$step = $_POST['step'];
		}

		break;

	// Step 04: Additional Settings
	case 4:
		if(Installer::process()) {
			$page = 'step_04.php';
			$step = 4;
		}
		else {
			$page = 'step_' . str_pad($_POST['step'], 2, '0', STR_PAD_LEFT) . '.php';
			$step = $_POST['step'];
		}

		break;
		
	// Installation
	case 'install':
		if(Installer::process()) {
			$page = 'step_install.php';
			$step = 'install';
		}
		else {
			$page = 'step_' . str_pad($_POST['step'], 2, '0', STR_PAD_LEFT) . '.php';
			$step = $_POST['step'];
		}
		break;

	// Launch
	case 'launch':
		Installer::process();
		break;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Bedrock Framework &raquo; Install</title>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link rel="stylesheet" type="text/css" href="js/jquery.ui.bubble.css" />
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/jquery.scrollto.js"></script>
		<script type="text/javascript" src="js/jquery.ui.js"></script>
		<script type="text/javascript" src="js/jquery.ui.bubble.js"></script>
		<script type="text/javascript">
			<!--

			$(document).ready(function() {
				/**
				 * Autoselect Input Text
				 */
				$('input, textarea').focus(function() {
					this.select();
				});

				/**
				 * Navigation Buttons
				 */
				$('#goto_start').click(function() {
					$('#goto').val('start');
					$('#form').submit();
				});

				$('#goto_01').click(function() {
					$('#goto').val(1);
					$('#form').submit();
				});

				$('#goto_01_back').click(function() {
					$('#goto_01').click()
				});

				$('#goto_02').click(function() {
					$('#goto').val(2);
					$('#form').submit();
				});

				$('#goto_02_back').click(function() {
					$('#goto_02').click()
				});

				$('#goto_03').click(function() {
					$('#goto').val(3);
					$('#form').submit();
				});

				$('#goto_03_back').click(function() {
					$('#goto_03').click()
				});

				$('#goto_04').click(function() {
					$('#goto').val(4);
					$('#form').submit();
				});

				/**
				 * Validation Errors
				 */
				<?php if(Installer::hasErrors($step)) { ?>
					<?php foreach(Installer::getErrors($step) as $field => $error) { ?>
						$('<?php echo '#' . str_pad($step, 2, '0', STR_PAD_LEFT) . '_' . $field ?>').bubble({
							msg: '<?php echo $error ?>',
							direction: 'e',
							width: 300,
							height: 100,
							type: 'error'
						});
					<?php } ?>
				<?php } ?>
			});

			//-->
		</script>
	</head>
	<body>
		<?php include $page ?>
	</body>
</html>
<?php Installer::clearErrors() ?>
<?php Installer::cache() ?>