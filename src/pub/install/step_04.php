<?php
/**
 * Bedrock Framework Installer: Step 04
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
	<h2>Step 4: Additional Settings</h2>
	<form id="form" method="post" action="">
		<div id="content">
			<p></p>
			<table>
				<tr>
					<th>Friendly URLs:</th>
					<td>
						<input type="radio" id="04_friendly_true" name="friendly" value="yes"<?php echo Installer::getValue('friendly') ? ' checked="checked"' : '' ?> />
						<label for="04_friendly_true">Yes <em>"/account/edit/132"</em></label>
						<br />
						<input type="radio" id="04_friendly_false" name="friendly" value="no"<?php echo Installer::getValue('friendly') ? '' : ' checked="checked"' ?> />
						<label for="04_friendly_false">No <em>"/index.php?route=account/edit/132"</em></label>
					</td>
				</tr>
				<tr>
					<th>Timezone:</th>
					<td>
						<select id="04_timezone" name="timezone">
							<option value="">-- Server Default --</option>
							<option value="Pacific/Kwajalein"<?php echo Installer::getValue('timezone') == 'Pacific/Kwajalein' ? ' selected="selected"' : '' ?>>(-12:00) Eniwetok, Kwajalein</option>
							<option value="Pacific/Samoa"<?php echo Installer::getValue('timezone') == 'Pacific/Samoa' ? ' selected="selected"' : '' ?>>(-11:00) Midway Island, Samoa</option>
							<option value="Pacific/Honolulu"<?php echo Installer::getValue('timezone') == 'Pacific/Honolulu' ? ' selected="selected"' : '' ?>>(-10:00) Hawaii</option>
							<option value="America/Juneau"<?php echo Installer::getValue('timezone') == 'America/Juneau' ? ' selected="selected"' : '' ?>>(-9:00) Alaska</option>
							<option value="America/Los_Angeles"<?php echo Installer::getValue('timezone') == 'America/Los_Angeles' ? ' selected="selected"' : '' ?>>(-8:00) Pacific Time (US &amp; Canada)</option>
							<option value="America/Denver"<?php echo Installer::getValue('timezone') == 'America/Denver' ? ' selected="selected"' : '' ?>>(-7:00) Mountain Time (US &amp; Canada)</option>
							<option value="America/Mexico_City"<?php echo Installer::getValue('timezone') == 'America/Mexico_City' ? ' selected="selected"' : '' ?>>(-6:00) Central Time (US &amp; Canada), Mexico City</option>
							<option value="America/New_York"<?php echo Installer::getValue('timezone') == 'America/New_York' ? ' selected="selected"' : '' ?>>(-5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
							<option value="America/Caracas"<?php echo Installer::getValue('timezone') == 'America/Caracas' ? ' selected="selected"' : '' ?>>(-4:00) Atlantic Time (Canada), Caracas, La Paz</option>
							<option value="America/St_Johns"<?php echo Installer::getValue('timezone') == 'America/St_Johns' ? ' selected="selected"' : '' ?>>(-3:30) Newfoundland</option>
							<option value="America/Argentina/Buenos_Aires"<?php echo Installer::getValue('timezone') == 'America/Argentina/Buenos_Aires' ? ' selected="selected"' : '' ?>>(-3:00) Brazil, Buenos Aires, Georgetown</option>
							<option value="Atlantic/Azores"<?php echo Installer::getValue('timezone') == 'Atlantic/Azores' ? ' selected="selected"' : '' ?>>(-2:00) Mid-Atlantic</option>
							<option value="Atlantic/Azores"<?php echo Installer::getValue('timezone') == 'Atlantic/Azores' ? ' selected="selected"' : '' ?>>(-1:00 hour) Azores, Cape Verde Islands</option>
							<option value="Europe/London"<?php echo Installer::getValue('timezone') == 'Europe/London' ? ' selected="selected"' : '' ?>>(GMT) Western Europe Time, London, Casablanca</option>
							<option value="Europe/Paris"<?php echo Installer::getValue('timezone') == 'Europe/Paris' ? ' selected="selected"' : '' ?>>(+1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>
							<option value="Europe/Helsinki"<?php echo Installer::getValue('timezone') == 'Europe/Helsinki' ? ' selected="selected"' : '' ?>>(+2:00) Kaliningrad, South Africa</option>
							<option value="Europe/Moscow"<?php echo Installer::getValue('timezone') == 'Europe/Moscow' ? ' selected="selected"' : '' ?>>(+3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
							<option value="Asia/Tehran"<?php echo Installer::getValue('timezone') == 'Asia/Tehran' ? ' selected="selected"' : '' ?>>(+3:30) Tehran</option>
							<option value="Asia/Baku"<?php echo Installer::getValue('timezone') == 'Asia/Baku' ? ' selected="selected"' : '' ?>>(+4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
							<option value="Asia/Kabul"<?php echo Installer::getValue('timezone') == 'Asia/Kabul' ? ' selected="selected"' : '' ?>>(+4:30) Kabul</option>
							<option value="Asia/Karachi"<?php echo Installer::getValue('timezone') == 'Asia/Karachi' ? ' selected="selected"' : '' ?>>(+5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
							<option value="Asia/Calcutta"<?php echo Installer::getValue('timezone') == 'Asia/Calcutta' ? ' selected="selected"' : '' ?>>(+5:30) Bombay, Calcutta, Madras, New Delhi</option>
							<option value="Asia/Colombo"<?php echo Installer::getValue('timezone') == 'Asia/Colombo' ? ' selected="selected"' : '' ?>>(+5:45) Kathmandu</option>
							<option value="Asia/Colombo"<?php echo Installer::getValue('timezone') == 'Asia/Colombo' ? ' selected="selected"' : '' ?>>(+6:00) Almaty, Dhaka, Colombo</option>
							<option value="Asia/Bangkok"<?php echo Installer::getValue('timezone') == 'Asia/Bangkok' ? ' selected="selected"' : '' ?>>(+7:00) Bangkok, Hanoi, Jakarta</option>
							<option value="Asia/Singapore"<?php echo Installer::getValue('timezone') == 'Asia/Singapore' ? ' selected="selected"' : '' ?>>(+8:00) Beijing, Perth, Singapore, Hong Kong</option>
							<option value="Asia/Tokyo"<?php echo Installer::getValue('timezone') == 'Asia/Tokyo' ? ' selected="selected"' : '' ?>>(+9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
							<option value="Australia/Darwin"<?php echo Installer::getValue('timezone') == 'Australia/Darwin' ? ' selected="selected"' : '' ?>>(+9:30) Adelaide, Darwin</option>
							<option value="Pacific/Guam"<?php echo Installer::getValue('timezone') == 'Pacific/Guam' ? ' selected="selected"' : '' ?>>(+10:00) Eastern Australia, Guam, Vladivostok</option>
							<option value="Asia/Magadan"<?php echo Installer::getValue('timezone') == 'Asia/Magadan' ? ' selected="selected"' : '' ?>>(+11:00) Magadan, Solomon Islands, New Caledonia</option>
							<option value="Asia/Kamchatka"<?php echo Installer::getValue('timezone') == 'Asia/Kamchatka' ? ' selected="selected"' : '' ?>>(+12:00) Auckland, Wellington, Fiji, Kamchatka</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Logging:</th>
					<td>
						<input type="checkbox" id="04_logto_system" name="logto[]" value="system"<?php echo in_array('system', Installer::getValue('logto')) ? ' checked="checked"' : '' ?> />
						<label for="04_logto_system">Log to System Log</label>
						<br />
						<input type="checkbox" id="04_logto_file" name="logto[]" value="file"<?php echo in_array('file', Installer::getValue('logto')) ? ' checked="checked"' : '' ?> />
						<label for="04_logto_file">Log to Text Files</label>
						<br />
						<input type="checkbox" id="04_logto_firephp" name="logto[]" value="firephp"<?php echo in_array('firephp', Installer::getValue('logto')) ? ' checked="checked"' : '' ?> />
						<label for="04_logto_firephp">Log to Firebug (via FirePHP)</label>
						<br />
						<input type="checkbox" id="04_logto_growl" name="logto[]" value="growl"<?php echo in_array('growl', Installer::getValue('logto')) ? ' checked="checked"' : '' ?> />
						<label for="04_logto_growl">Log to Growl</label>
					</td>
				</tr>
				<tr>
					<th>Growl Address:</th>
					<td>
						<input type="text" id="04_growl_address" name="growl_address" value="<?php Installer::printValue('growl_address') ?>" />
					</td>
				</tr>
			</table>
		</div>
		<div id="nav">
			<input type="hidden" id="step" name="step" value="4" />
			<input type="hidden" id="goto" name="goto" value="install" />
			<input type="button" id="goto_03_back" class="back" name="" value="" />
			<?php Installer::printNavButton(1) ?>
			<?php Installer::printNavButton(2) ?>
			<?php Installer::printNavButton(3) ?>
			<?php Installer::printNavButton(4, true) ?>
			<input type="submit" id="step_04_next" class="finish" name="" value="" />
		</div>
	</form>
</div>