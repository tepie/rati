<?php
	
	//include_once("Settings.php");
	include_once("SettingsPerspectives.php");
	include_once('SettingsWebApp.php');
	include_once('SettingsBranding.php');
	
	
	/**
	* Initalize a common session
	* If the common session variables are not set, give them their defaults
	* These are assigned to the $_SESSION global variable
	*/
	function commonSessionSetup(){
		global $url_rest_custom_image_arrow_direction;
		global $url_rest_custom_image_graph_direction;
		global $url_rest_custom_image_font_size;
		global $url_rest_custom_image_graph_levels;
		global $url_rest_custom_image_graph_neighbors;
		global $url_rest_page_show_jumpto_links;
		global $url_rest_page_show_graph_legend;
		global $url_rest_page_show_export_options;
		global $_SESSION;
		
		session_start();
		//echo  "global test:$url_rest_page_show_jumpto_links<br />";
		/*if(!isset($_SESSION["$url_rest_custom_image_arrow_direction"])){
			$_SESSION["$url_rest_custom_image_arrow_direction"] = "0";
		}*/
		
		// set the image direction
		if(!isset($_SESSION["$url_rest_custom_image_graph_direction"])){
			$_SESSION["$url_rest_custom_image_graph_direction"] = "LR";
		}
		
		// set the image font size
		if(!isset($_SESSION["$url_rest_custom_image_font_size"])){
			$_SESSION["$url_rest_custom_image_font_size"] = "N";
		}
		
		// set the image graph levels
		if(!isset($_SESSION["$url_rest_custom_image_graph_levels"])){
			$_SESSION["$url_rest_custom_image_graph_levels"] = "1";
		}
		
		// set the graph neighbor limit
		if(!isset($_SESSION["$url_rest_custom_image_graph_neighbors"])){
			$_SESSION["$url_rest_custom_image_graph_neighbors"] = "500";
		}
		
		// set the show export option
		if(!isset($_SESSION["$url_rest_page_show_export_options"])){
			$_SESSION["$url_rest_page_show_export_options"] = "True";
		}
		
		// set the show graph legend option
		if(!isset($_SESSION["$url_rest_page_show_graph_legend"])){
			$_SESSION["$url_rest_page_show_graph_legend"] = "True";
		}
		
		// set the show graph legend option
		if(!isset($_SESSION["$url_rest_page_show_jumpto_links"])){
			$_SESSION["$url_rest_page_show_jumpto_links"] = "True";
		}
	}
	
	/**
	*  Validation the customization values given through the $_GET and $_SESSION variables
	* $_GET should be set with the desired values
	* These are checked and then assigned to the $_SESSION
	*/
	function commonValidationCustomizationValues($ignore=false){
		global $url_rest_custom_array;
		global $custom_accepted_array;
		global $_SESSION;
		global $_GET;
		
		$should_close_flag = false;
		
		foreach($url_rest_custom_array as $index => $parameter){
			$accepted_array = $custom_accepted_array[$index];
			$accepted_keys = array_keys($accepted_array);
			
			if(isset($_GET["$parameter"])){
				if($ignore){
					//echo "session $parameter before:".$_SESSION["$parameter"]."\n";
					$_SESSION["$parameter"] = $_GET["$parameter"];
					//echo "session $parameter after:".$_SESSION["$parameter"]."\n";
				} else {
					
					foreach($accepted_array as $inner_index => $value){
						if(isset($_GET["$parameter"]) == $value){
							$_SESSION["$parameter"] = $_GET["$parameter"];
							$should_close_flag = True;
							break;
						} else {
							//echo "Accepted". $accepted_keys[0];
							/** Assign the first value of the acceptable values as the default */
							$_SESSION["$parameter"] = $accepted_keys[0];
						}
					}
				}
			} /*else {
				$_SESSION["$parameter"] = $accepted_keys[0];
			}*/
		}
		
		return $should_close_flag;
	}
	
	function commonValidationCustomizationValuesCmd(){
		global $argv;
		global $url_rest_custom_array;
		global $custom_accepted_array;
	}
	
	function commonUrlCustomizationValues($for_html=False){
		global $url_rest_custom_array;
		global $custom_accepted_array;
		
		$url_string = "";
		
		foreach($url_rest_custom_array as $index => $parameter){
			if($for_html){
				$url_string .= "&amp;$parameter=" . urlencode($_SESSION[$parameter]);
			} else {
				$url_string .= "&$parameter=" . $_SESSION[$parameter];
			}
		}
		
		return $url_string;
		
	}
	
	function commonGraphvizFontSize(){
		global $url_rest_custom_image_font_size;
		//echo $_SESSION[$url_rest_custom_image_font_size];
		if($_SESSION[$url_rest_custom_image_font_size] == "L"){
			return "14";
		} else if($_SESSION[$url_rest_custom_image_font_size] == "S"){
			return "8";
		} else {
			return null;
		}
	}
	
	function commonCloseWindow(){
		return "<html><body onload=\"javascript:opener.window.location.reload();window.close();\"></body></html>";
	}
	
	function commonHtmlPageHead($page_title_tail){
		global $web_app_page_title_prefix;
		global $web_app_author_full_name;
		global $web_app_default_style_sheet;
		global $web_app_page_javascript;
		global $web_app_name_full;
		global $web_app_organizational_dept;
		
		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">';
		$html = $html . "\n<html>\n<head>\n<title>$web_app_page_title_prefix - $page_title_tail</title>\n";
		$html = $html . "<meta NAME=\"author\" CONTENT=\"$web_app_author_full_name\">\n";
		$html = $html . "<link rel=\"schema.DC\" href=\"http://purl.org/dc/elements/1.1/\" />\n";
		$html = $html . "<link rel=\"schema.DCTERMS\" href=\"http://purl.org/dc/terms/\" />\n";
		$html = $html . "<meta name=\"DCTERMS.audience\" content=\"data architects, data modelers, data\" />\n";
		$html = $html . "<meta name=\"DC.creator\" content=\"$web_app_author_full_name - $web_app_organizational_dept\" />\n";
		$html = $html . "<meta name=\"DC.title\" lang=\"en\" content=\"$web_app_name_full\" />\n";
		$html = $html . "<meta name=\"DCTERMS.abstract\" content=\"$page_title_tail\" />\n";
		$html = $html . "<meta name=\"DC.format\" scheme=\"DCTERMS.IMT\" content=\"text/html\" />\n";
		$html = $html . "<meta name=\"DC.type\" scheme=\"DCTERMS.DCMIType\" content=\"Text\" />\n";
		$html = $html . "<link rel=\"stylesheet\" type=\"text/css\" href=\"$web_app_default_style_sheet\" />\n";
		$html = $html . "<script language=\"javascript\" type=\"text/javascript\" src=\"$web_app_page_javascript\"></script>\n";
		$html = $html . "</head>\n<body onload=\"detectBrowser();\">\n";
		
		return $html;
	}
	
	/**
	* Generate the common page header html
	* @param $search_box_value the value to place in the search box when the page loads
	* return formatted html string
	*/
	function commonHtmlPageHeader($search_box_value="Wouldn't it be nice to search?"){
		global $web_app_page_title_prefix;
		global $web_app_author_name;
		global $web_app_default_style_sheet;
		global $web_app_page_post_back_name;
		global $web_app_name_full;
		global $url_rest_search_param;
		global $web_app_page_search_name;
		global $web_app_page_index_name;
		global $perspective_names;
		//global $web_app_page_post_back_name;
		$html = "";
		//$html = "<div style=\"text-align:right; padding-right:0.2cm;\"><a href=\"#\" onClick=\"popup('Customize.php');\"><small>Customize</small></a></div>";
		$html = $html . "<div class=\"top-bar-full\">\n";
		$html = $html .  "<form action=\"Search.php\" method=\"get\" style=\"margin: 0px; padding: 0px;\">";
		
		$html = $html .  "<a class=\"top-bar-full\" href=\"$web_app_page_index_name\">";
		$html = $html .  "$web_app_name_full</a><br />";
		
		$html = $html .  "<input type=\"text\" id=\"$url_rest_search_param\" name=\"$url_rest_search_param\" size=\"80\" value=\"$search_box_value\" />";
		$html = $html .  "&nbsp;<input type=\"submit\" name=\"Search\" value=\"Search\" />";
		
		/*foreach($perspective_names	as $index => $name){
			$html = $html . "<input type=\"checkbox\" name=\"perspective\" value=\"".urlencode($name)."\" checked disabled /><small>$name</small>&nbsp;";
		}
		$html = $html . "<input type=\"checkbox\" name=\"perspective\" value=\"all\" checked disabled /><small>all</small>&nbsp;";
		*/
		
		
		$html = $html .  "</form>\n";
		$html = $html . "</div>\n";		
		return $html;
	
	}
	
	function commonHtmlPlainHeader(){
		global $web_app_page_post_back_name;
		global $web_app_page_index_name;
		global $web_app_name_full;
		$html = "<div class=\"top-bar-full-about\"><br />";
		$html = $html .  "&nbsp;<a class=\"top-bar-full-about\"href=\"$web_app_page_index_name\">";
		$html = $html .  "$web_app_name_full</a><br />&nbsp;\n";
		$html = $html .  "</div>";
		return $html;
	}
	
	/**
	* Generate the common page footer html
	* return formatted html string
	*/
	function commonHtmlPageFooter(){
		global $web_app_organizational_url;
		global $web_app_organizational_dept;
		global $web_app_author_email;
		global $web_app_author_short_name;
		global $web_app_page_doxygen;
		global $web_app_page_about_name;
		global $web_app_page_usage_name;
		global $web_app_name_full;
		global $web_app_name_abbrv;
		
		$html = "<div class=\"contact\"><br />developed by ";
		$html = $html . "<a class=\"contact\"href=\"$web_app_organizational_url\">$web_app_organizational_dept</a>\n";
		$html = $html . "&nbsp;|&nbsp;contact <a class=\"contact\" href=\"mailto:$web_app_author_email\">$web_app_author_short_name</a> with questions\n";
		$html = $html . "&nbsp;|&nbsp;more <a class=\"contact\" href=\"$web_app_page_about_name\">about</a> $web_app_name_abbrv";
		$html = $html . "&nbsp;|&nbsp;some <a class=\"contact\" href=\"$web_app_page_usage_name\">usage</a> insight\n";
		$html = $html . "<br /><br /></div>\n";
		$html = $html . "</body>\n</html>\n";
		
		return $html;
	}


?>