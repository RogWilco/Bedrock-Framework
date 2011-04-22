<?php
/**
 * Bedrock Framework Installer: Installation Script
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

	function installComplete() {
		$('#goto_launch').removeClass('disabled').removeAttr('disabled');
		$('#delete_msg').removeClass('disabled');
	}

	//-->
</script>

<div id="main">
	<div id="logo"></div>
	<h2>Installing</h2>
	<form method="post" action="">
		<div id="content">
			<p>

			</p>
			<iframe src="install.php" id="output_iframe" frameborder="0">
				<p>Installation Complete!</p>
			</iframe>
			<div id="delete_msg" class="disabled">
				You may now delete the installer directory.
			<div>
		</div>
		<div id="nav">
			<input type="hidden" id="step" name="step" value="install" />
			<input type="hidden" id="goto" name="goto" value="launch" />
			<input type="submit" id="goto_launch" class="launch disabled" disabled="disabled" name="" value="" />
		</div>
	</form>
</div>