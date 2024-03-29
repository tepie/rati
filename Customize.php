<?php

	include_once('Include/SettingsWebApp.php');
	include_once('Include/SettingsBranding.php');
	include_once('Include/HtmlCommon.php');

	echo commonSessionSetup();
	
	$should_close_flag = commonValidationCustomizationValues();
	
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
Default Graph Neighbor Limit
</td>
<td class="custom_table_right">
<?php 
	echo "<select class=\"custom_option\" name=\"$url_rest_custom_image_graph_neighbors\">\n";
	foreach($custom_image_neighbor_limit_accepted as $value => $display){
		if($_SESSION["$url_rest_custom_image_graph_neighbors"] == "$value"){
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
<tr>
<td class="custom_table_left">
Default graph size
</td>
<td class="custom_table_right">
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
<tr>
<td class="custom_table_left">
Default graph levels
</td>
<td class="custom_table_right">
<?php 
	echo "<select class=\"custom_option\" name=\"$url_rest_custom_image_graph_levels\">\n";
	foreach($custom_image_graph_levels_accepted as $value => $display){
		if($_SESSION["$url_rest_custom_image_graph_levels"] == "$value"){
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
Show category links
</td>
<td class="custom_table_right">
<?php 
	echo "<select class=\"custom_option\" name=\"$url_rest_custom_image_category_show\">\n";
	foreach($custom_true_false_accepted as $value => $display){
		if($_SESSION["$url_rest_custom_image_category_show"] == "$value"){
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
<br />
<div class="about_section_heading">Navigation Page Settings</div>
<div class="about_section_content">
<br />
<table class="custom_table">
<tr>
<td class="custom_table_left">
Show Export Options
</td>
<td class="custom_table_right">
<?php 
	echo "<select class=\"custom_option\" name=\"$url_rest_page_show_export_options\">\n";
	foreach($custom_true_false_accepted as $value => $display){
		if($_SESSION["$url_rest_page_show_export_options"] == "$value"){
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
Show Graph Legend
</td>
<td class="custom_table_right">
<?php 
	echo "<select class=\"custom_option\" name=\"$url_rest_page_show_graph_legend\">\n";
	foreach($custom_true_false_accepted as $value => $display){
		if($_SESSION["$url_rest_page_show_graph_legend"] == "$value"){
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
Show Jump To Links
</td>
<td class="custom_table_right">
<?php 
	echo "<select class=\"custom_option\" name=\"$url_rest_page_show_jumpto_links\">\n";
	foreach($custom_true_false_accepted as $value => $display){
		if($_SESSION["$url_rest_page_show_jumpto_links"] == "$value"){
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