<?php
	
	include_once('Include/SettingsWebApp.php');
	include_once('Include/SettingsBranding.php');
	include_once('Include/SettingsPerspectives.php');
	include_once('Include/HtmlCommon.php');
	
	echo commonHtmlPageHead("Index");
	echo commonHtmlPlainHeader();
	
	echo "<br /><center><img src=\"$web_app_default_logo_image\" alt=\"rati the rat\" /></center>\n";
	$html = "<div class=\"index_container\"><br />";
	$html = $html . "Search <b>$web_app_name_abbrv</b> for relationships...<br />";
	$html = $html .  "<form action=\"$web_app_page_search_name\" method=\"GET\">\n";
	$html = $html .  "&nbsp;&nbsp;<input type=\"text\" name=\"$url_rest_search_param\" size=\"80\" value=\"$search_box_value\" />\n";
	$html = $html .  "<br /><br /><input type=\"submit\" name=\"Search\" value=\"Search\" />\n";
	$html = $html .  "&nbsp;</form>";
	$html = $html . "<hr class=\"index\" /><br />\n";
	$html = $html .  " Or just start to <b>browse</b> a perspective<br /><br />\n";
	
	foreach($perspective_names as $index => $name){
		$html = $html . "<b><a href=\"$web_app_page_post_back_name?$url_rest_node_param=";
		$html = $html . urlencode($perspective_default_root_nodes[$index])."\">$name</a></b>\n";
		if($index < count($perspective_names) - 1){ $html = $html . " or "; }
	}
	
	$html = $html . "\n<br /><br /><hr class=\"index\"/><br /></div>";
	$html = $html .  "\n";
	
	echo $html;
	
	echo commonHtmlPageFooter();	
?>