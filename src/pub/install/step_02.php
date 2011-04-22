<?php
/**
 * Bedrock Framework Installer: Step 02
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
		$('#02_root').change(function() {
			switch($(this).val()) {
				case 'default':
					$('#02_root_web').attr('disabled', 'disabled');
					$('#02_root_system').attr('disabled', 'disabled');
					$('#02_root_log').attr('disabled', 'disabled');
					$('#02_root_pub').attr('disabled', 'disabled');

					$('#02_root_web').val('<?php Installer::printValue('root_web') ?>');
					$('#02_root_system').val('<?php Installer::printValue('root_system') ?>');
					$('#02_root_log').val('<?php Installer::printValue('root_log') ?>');
					$('#02_root_pub').val('<?php Installer::printValue('root_pub') ?>');
					break;

				case 'custom':
					$('#02_root_web').removeAttr('disabled');
					$('#02_root_system').removeAttr('disabled');
					$('#02_root_log').removeAttr('disabled');
					$('#02_root_pub').removeAttr('disabled');
					break;
			}
		});
	});
	
	//-->
</script>

<div id="main">
	<div id="logo"></div>
	<h2>Step 2: Directories</h2>
	<form id="form" method="post" action="">
		<div id="content">
			<p>
				It is recommended that you use the default directory settings,
				though some customization is possible.
			</p>
			<table>
				<tr>
					<th>Directory Settings:</th>
					<td>
						<select id="02_root" name="root">
							<option value="default"<?php echo Installer::getValue('root') == 'default' ? ' selected="selected"' : '' ?>>Use Defaults</option>
							<option value="custom"<?php echo Installer::getValue('root') == 'custom' ? ' selected="selected"' : '' ?>>Custom Settings</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Web Root:</th>
					<td><input type="text" id="02_root_web" name="root_web" value="<?php Installer::printValue('root_web') ?>"<?php echo Installer::getValue('root') == 'default' ? ' disabled="disabled"' : '' ?> /></td>
				</tr>
				<tr>
					<th>Application (system):</th>
					<td><input type="text" id="02_root_system" name="root_system" value="<?php Installer::printValue('root_system') ?>"<?php echo Installer::getValue('root') == 'default' ? ' disabled="disabled"' : '' ?> /></td>
				</tr>
				<tr>
					<th>Logs (log):</th>
					<td><input type="text" id="02_root_log" name="root_log" value="<?php Installer::printValue('root_log') ?>"<?php echo Installer::getValue('root') == 'default' ? ' disabled="disabled"' : '' ?> /></td>
				</tr>
				<tr>
					<th>Public Root (pub):</th>
					<td><input type="text" id="02_root_pub" name="root_pub" value="<?php Installer::printValue('root_pub') ?>"<?php echo Installer::getValue('root') == 'default' ? ' disabled="disabled"' : '' ?> /></td>
				</tr>
			</table>
		</div>
		<div id="nav">
			<input type="hidden" id="step" name="step" value="2" />
			<input type="hidden" id="goto" name="goto" value="3" />
			<input type="button" id="goto_01_back" class="back" name="" value="" />
			<?php Installer::printNavButton(1) ?>
			<?php Installer::printNavButton(2, true) ?>
			<?php Installer::printNavButton(3) ?>
			<?php Installer::printNavButton(4) ?>
			<input type="submit" id="step_02_next" class="next" name="" value="" />
		</div>
	</form>
</div>