<?php

	include_once('Include\\SettingsWebApp.php');
	include_once('Include\\HtmlCommon.php');
	
	$should_close_flag = false;
	
	echo commonSessionSetup();
	
	if(isset($_GET["$url_rest_custom_image_arrow_direction"])){
		if($_GET["$url_rest_custom_image_arrow_direction"] == "0" or $_GET["$url_rest_custom_image_arrow_direction"] == "1"){
			$_SESSION["$url_rest_custom_image_arrow_direction"] = $_GET["$url_rest_custom_image_arrow_direction"];
			$should_close_flag = True;
		}
	}
	
	if(isset($_GET["$url_rest_custom_image_graph_direction"])){
		if($_GET["$url_rest_custom_image_graph_direction"] == "LR" or $_GET["$url_rest_custom_image_graph_direction"] == "TB"){
			$_SESSION["$url_rest_custom_image_graph_direction"] = $_GET["$url_rest_custom_image_graph_direction"];
			$should_close_flag = True;
		}
	}
	
	if(isset($_GET["$url_rest_custom_image_font_size"])){
		if($_GET["$url_rest_custom_image_font_size"] == "L" or $_GET["$url_rest_custom_image_font_size"] == "N"){
			$_SESSION["$url_rest_custom_image_font_size"] = $_GET["$url_rest_custom_image_font_size"];
			$should_close_flag = True;
		}
	}
	
	if($should_close_flag){
		echo commonCloseWindow();
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
	echo "<select name=\"$url_rest_custom_image_arrow_direction\">\n";
	if($_SESSION["$url_rest_custom_image_arrow_direction"] == "1"){
		echo '<option value="1" selected="selected">Actual Line Directions</option>';
		echo '<option value="0">Same Line Directions</option>';
	} else {
		echo '<option value="1">Actual Line Directions</option>';
		echo '<option value="0" selected="selected">Same Line Directions</option>';
	}
	echo "</select>\n";
?>
<br />
<br />
Setup the default graph direction...<br /><br />
<?php 
	echo "<select name=\"$url_rest_custom_image_graph_direction\">\n";
	if($_SESSION["$url_rest_custom_image_graph_direction"] == "LR"){
		echo '<option value="LR" selected="selected">Left to Right</option>';
		echo '<option value="TB" >Top to Bottom</option>';
	} else {
		echo '<option value="LR">Left to Right</option>';
		echo '<option value="TB" selected="selected">Top to Bottom</option>';
	}
	echo "</select>\n";
?>
<br /><br />
Setup the default graph size...<br /><br />
<?php 
	echo "<select name=\"$url_rest_custom_image_font_size\">\n";
	if($_SESSION["$url_rest_custom_image_font_size"] == "L"){
		echo '<option value="L" selected="selected">Large</option>';
		echo '<option value="N" >Normal</option>';
	} else {
		echo '<option value="L">Large</option>';
		echo '<option value="N" selected="selected">Normal</option>';
	}
	echo "</select>\n";
?>
<br /><br />
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