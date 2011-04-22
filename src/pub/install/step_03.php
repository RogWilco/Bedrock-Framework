<?php
/**
 * Bedrock Framework Installer: Step 03
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 04/08/2009
 * @updated 04/08/2009
 */
?>

<script type="text/javascript">
	<!--

	$(document).ready(function() {
		$('#03_database_type').change(function() {
			switch($(this).val()) {
				case 'none':
					$('#03_database_address').attr('disabled', 'disabled');
					$('#03_database_name').attr('disabled', 'disabled');
					$('#03_database_prefix').attr('disabled', 'disabled');
					$('#03_database_username').attr('disabled', 'disabled');
					$('#03_database_password').attr('disabled', 'disabled');

					$('#03_database_address').val('');
					$('#03_database_name').val('');
					$('#03_database_prefix').val('');
					$('#03_database_username').val('');
					$('#03_database_password').val('');
				break;

				case 'mysql_5':
					$('#03_database_address').removeAttr('disabled');
					$('#03_database_name').removeAttr('disabled');
					$('#03_database_prefix').removeAttr('disabled');
					$('#03_database_username').removeAttr('disabled');
					$('#03_database_password').removeAttr('disabled');

					$('#03_database_address').val('<?php Installer::printValue('database_address') ?>');
					$('#03_database_name').val('<?php Installer::printValue('database_name') ?>');
					$('#03_database_prefix').val('<?php Installer::printValue('database_prefix') ?>');
					$('#03_database_username').val('<?php Installer::printValue('database_username') ?>');
					$('#03_database_password').val('<?php Installer::printValue('database_password') ?>');
					break;
			}
		});
	});

	//-->
</script>

<div id="main">
	<div id="logo"></div>
	<h2>Step 3: Database Settings</h2>
	<form id="form" method="post" action="">
		<div id="content">
			<p></p>
			<table>
				<tr>
					<th>Database Type:</th>
					<td>
						<select id="03_database_type" name="database_type">
							<option value="none"<?php echo Installer::getValue('database_type') == 'none' ? ' selected="selected"' : '' ?>>No Database</option>
							<option value="mysql_5"<?php echo Installer::getValue('database_type') == 'mysql_5' ? ' selected="selected"' : '' ?>>MySQL 5</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Address:</th>
					<td><input type="text" id="03_database_address" name="database_address" value="<?php echo Installer::getValue('database_type') != 'none' ? Installer::getValue('database_address') : '' ?>"<?php echo Installer::getValue('database_type') == 'none' ? ' disabled="disabled"' : '' ?> /></td>
				</tr>
				<tr>
					<th>Database:</th>
					<td><input type="text" id="03_database_name" name="database_name" value="<?php echo Installer::getValue('database_type') != 'none' ? Installer::getValue('database_name') : '' ?>"<?php echo Installer::getValue('database_type') == 'none' ? ' disabled="disabled"' : '' ?> /></td>
				</tr>
				<tr>
					<th>Table Prefix:</th>
					<td><input type="text" id="03_database_prefix" name="database_prefix" value="<?php echo Installer::getValue('database_type') != 'none' ? Installer::getValue('database_prefix') : '' ?>"<?php echo Installer::getValue('database_type') == 'none' ? ' disabled="disabled"' : '' ?> /></td>
				</tr>
				<tr>
					<th>Username:</th>
					<td><input type="text" id="03_database_username" name="database_username" value="<?php echo Installer::getValue('database_type') != 'none' ? Installer::getValue('database_username') : '' ?>"<?php echo Installer::getValue('database_type') == 'none' ? ' disabled="disabled"' : '' ?> /></td>
				</tr>
				<tr>
					<th>Password:</th>
					<td><input type="password" id="03_database_password" name="database_password" value="<?php echo Installer::getValue('database_type') != 'none' ? Installer::getValue('database_password') : '' ?>"<?php echo Installer::getValue('database_type') == 'none' ? ' disabled="disabled"' : '' ?> /></td>
				</tr>
			</table>
		</div>
		<div id="nav">
			<input type="hidden" id="step" name="step" value="3" />
			<input type="hidden" id="goto" name="goto" value="4" />
			<input type="button" id="goto_02_back" class="back" name="" value="" />
			<?php Installer::printNavButton(1) ?>
			<?php Installer::printNavButton(2) ?>
			<?php Installer::printNavButton(3, true) ?>
			<?php Installer::printNavButton(4) ?>
			<input type="submit" id="step_03_next" class="next" name="" value="" />
		</div>
	</form>
</div>