<?php

	include_once('Include\\SettingsWebApp.php');
	include_once('Include\\HtmlCommon.php');
	
	$should_close_flag = false;
	
	echo commonSessionSetup();
	
	foreach($url_rest_custom_array as $index => $parameter){
		if(isset($_GET["$parameter"])){
			$accepted_array = $custom_accepted_array[$index];
			foreach($accepted_array as $inner_index => $value){
				if(isset($_GET["$parameter"]) == $value){
					$_SESSION["$parameter"] = $_GET["$parameter"];
					$should_close_flag = True;
					break;
				}
			}
		
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
<table class="custom_table">
<tr>
<td class="custom_table_left">
Relationship line direction
</td>
<td class="custom_table_right">
<?php 
	echo "<select class=\"custom_option\" name=\"$url_rest_custom_image_arrow_direction\">\n";
	foreach($custom_image_arrow_direction_accepted as $value => $display){
		if($_SESSION["$url_rest_custom_image_arrow_direction"] == "$value"){
			echo '<option value="'.$value.'" selected="selected">'.$display.'</option>';
		} else {
			echo '<option value="'.$value.'">'.$display.'</option>';
		}
		
	}
	echo "</select>\n";
?>
</td>
</tr>
<tr>
<td class="custom_table_left">
Default graph direction
</td>
<td class="custom_table_right">
<?php 
	echo "<select class=\"custom_option\" name=\"$url_rest_custom_image_graph_direction\">\n";
	foreach($custom_image_graph_direction_accepted as $value => $display){
		if($_SESSION["$url_rest_custom_image_graph_direction"] == "$value"){
			echo '<option value="'.$value.'" selected="selected">'.$display.'</option>';
		} else {
			echo '<option value="'.$value.'">'.$display.'</option>';
		}	
	}
	echo "</select>\n";
?>
</td>
</tr>
<td class="custom_table_left">
Default graph size
</td>
<td <td class="custom_table_right">
<?php 
	echo "<select class=\"custom_option\" name=\"$url_rest_custom_image_font_size\">\n";
	foreach($custom_image_font_size_accepted as $value => $display){
		if($_SESSION["$url_rest_custom_image_font_size"] == "$value"){
			echo '<option value="'.$value.'" selected="selected">'.$display.'</option>';
		} else {
			echo '<option value="'.$value.'">'.$display.'</option>';
		}	
	}
	echo "</select>\n";
?>
</td>
</tr>
</table>
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