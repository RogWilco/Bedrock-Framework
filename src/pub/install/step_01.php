<?php
/**
 * Bedrock Framework Installer: Step 01
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 04/08/2009
 * @updated 04/08/2009
 */
?>

<div id="main">
	<div id="logo"></div>
	<h2>Step 1: Application Details</h2>
	<form id="form" method="post" action="">
		<div id="content">
			<p>
				Tell us a little about your application so we can customize your
				installation. The application's namespace should be formatted
				like any PHP class/namespace and should not contain spaces or
				special characters.
			</p>
			<table>
				<tr>
					<th><label for="01_app_name">Name:</label></th>
					<td><input type="text" id="01_app_name" name="app_name" value="<?php Installer::printValue('app_name') ?>" /></td>
				</tr>
				<tr>
					<th><label for="01_app_version">Version:</label></th>
					<td><input type="text" id="01_app_version" name="app_version" value="<?php Installer::printValue('app_version') ?>" /></td>
				</tr>
				<tr>
					<th><label for="01_app_namespace">Namespace:</label></th>
					<td><input type="text" id="01_app_namespace" name="app_namespace" value="<?php Installer::printValue('app_namespace') ?>" /></td>
				</tr>
			</table>
		</div>
		<div id="nav">
			<input type="hidden" id="step" name="step" value="1" />
			<input type="hidden" id="goto" name="goto" value="2" />
			<input type="button" id="goto_start" class="back" name="" value="" />
			<?php Installer::printNavButton(1, true) ?>
			<?php Installer::printNavButton(2) ?>
			<?php Installer::printNavButton(3) ?>
			<?php Installer::printNavButton(4) ?>
			<input type="submit" id="step_01_next" class="next" name="" value="" />
		</div>
	</form>
</div>