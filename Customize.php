<?php

	include_once('Include\\SettingsWebApp.php');
	include_once('Include\\HtmlCommon.php');
	
	echo commonSessionSetup();
	
	if(isset($_GET["$url_rest_custom_image_arrow_direction"])){
		if($_GET["$url_rest_custom_image_arrow_direction"] == "0" or $_GET["$url_rest_custom_image_arrow_direction"] == "1"){
			$_SESSION["$url_rest_custom_image_arrow_direction"] = $_GET["$url_rest_custom_image_arrow_direction"];
			echo commonCloseWindow();
		}
	} 
	
	echo commonHtmlPageHead("Customize");
	echo commonHtmlPlainHeader();
	
?>
<br />
<form action="<?php echo $web_app_page_custom_name; ?>" method="get">
<div class="about_section_heading">Image Settings</div>
<div class="about_section_content">
<br />
Setup how the relationship lines are drawn for the images you will see...<br /><br />
<?php 
	if($_SESSION["$url_rest_custom_image_arrow_direction"] == "1"){
		echo '<input type="radio" name="'.$url_rest_custom_image_arrow_direction.'" value="1" checked />Actual Line Directions<br />';
		echo '<input type="radio" name="'.$url_rest_custom_image_arrow_direction.'" value="0" />Same Line Directions<br />';
	} else {
		echo '<input type="radio" name="'.$url_rest_custom_image_arrow_direction.'" value="1" />Actual Line Directions<br />';
		echo '<input type="radio" name="'.$url_rest_custom_image_arrow_direction.'" value="0" checked />Same Line Directions<br />';
	}
?>
<br />
<!--
Setup the default image size for the images that you will see...<br /><br />
<?php 
	/*if($_SESSION["image_size"] == "large"){
		echo '<input type="radio" name="image_size" value="large" checked disabled />Large<br />';
		echo '<input type="radio" name="image_size" value="normal" disabled />Normal<br />';
	} else {
		echo '<input type="radio" name="image_size" value="large" disabled />Large<br />';
		echo '<input type="radio" name="image_size" value="normal" checked disabled />Normal<br />';
	}*/
?>
-->
<br />
</div>
<div class="about_section_heading">Save?</div>
<div class="about_section_content">
<br />
<center><input type="submit" name="submit" value="Save Settings" /></center>
<br />
</div>
<div class="contact">
&nbsp;
</div>
</form>
</body></html>