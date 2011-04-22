<?php
/**
 * Bedrock Framework Installer: Step 00
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
		
	});

	//-->
</script>

<div id="main">
	<div id="logo"></div>
	<h2>Welcome!</h2>
	<form id="form" method="post" action="">
		<div id="content">
			<p>
				We're glad you have decided to build your project on
				<a href="http://www.bedrockframework.com/" target="_blank">Bedrock</a>!
				This installer will help you get things set up so you can get right
				to developing your application. We just need to gather some
				information about your setup to make sure everything will work as
				expected.
			</p>
			<table class="check">
				<tr id="check_php">
					<td class="icon"><?php echo Installer::checkReq('php') ? Installer::getIcon('success') : Installer::getIcon('error') ?></td>
					<th>PHP 5.2 or Newer</th>
				</tr>
				<tr id="check_bedrock">
					<td class="icon"><?php echo Installer::checkReq('bedrock') ? Installer::getIcon('success') : Installer::getIcon('warn') ?></td>
					<th>Latest Bedrock Version<?php echo Installer::checkReq('bedrock') ? '' : ' <em>(New Version Available)</em>' ?></th>
				</tr>
				<tr id="check_os">
					<td class="icon"><?php echo Installer::checkReq('os') ? Installer::getIcon('success') : Installer::getIcon('warn') ?></td>
					<th>Supported Operating System</th>
				</tr>
				<tr id="check_filesystem">
					<td class="icon"><?php echo Installer::checkReq('filesystem') ? Installer::getIcon('success') : Installer::getIcon('error') ?></td>
					<th>Filesystem Permissions</th>
				</tr>
			</table>
		</div>
		<div id="nav">
			<input type="hidden" id="step" name="step" value="start" />
			<input type="hidden" id="goto" name="goto" value="1" />
			<input type="submit" id="goto_01" class="start" name="" value="" />
		</div>
	</form>
</div>