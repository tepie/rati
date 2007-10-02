<?php
	
	include_once('SettingsWebApp.php');
	include_once('SettingsBranding.php');
	include_once('SettingsPerspectives.php');
	include_once('HtmlCommon.php');
	
	/** 
	* Generate the extra options table 
	* @param $node_name the name of the node to use for a link
	* @param $focus something to focus on, image or attributes
	* return html formatted string
	*/
	function createExtraOptions($node_name){
		global $web_app_page_post_back_name;
		global $url_rest_node_param;
		global $perspective_names;
		global $perspective_default_root_nodes;
		global $web_app_show_export_options;
		
		$html = "";
		
		$url_node = urlencode($node_name);
		$url_options_export = "http://localhost/rati/Export.php?q=".urlencode($node_name)."&type=options".commonUrlCustomizationValues();
		
		if($web_app_show_export_options){
			$options_export = file_get_contents($url_options_export);
			$html = $options_export;
			$html = $html . "<br />";
		}
		
		$html = $html . createJumpToPerspectiveTable();
		/*$html = $html . "<br />";
		$html = $html . "<table class=\"extra_options\">\n";
		$html = $html . "<tr><td class=\"extra_option\">";
		$html = $html . "<a href=\"#\" onClick=\"popup('Customize.php');\">Customize</a></td></tr>";
		$html = $html . "</table>\n";
		*/
		return $html;
	}
	
	
	/** 
	* Generate the attribute table HTML
	* @param $attributes a key=>value array with the attribute name and attribute value
	* return html formatted string 
	*/
	function createAttributeTableHtml($attributes){
		
		$html = "<table class=\"attributes\">\n";
		foreach($attributes as $attribute_name => $attribute_value){
			$html = $html . "<tr><td class=\"attribute_name\">$attribute_name</td>\n";
			if($attribute_value == "")
				$html = $html . "<td class=\"attribute_value\">&nbsp;</td></tr>\n";
			else{
				$special 		= htmlspecialchars($attribute_value);
				$parts 			= split(' ',$special);
				if(count($parts) == 1 and strlen($special) > 30){
					$special = substr($special,0,40) . "...";
				}
				$html 			= $html . "<td class=\"attribute_value\">$special</td></tr>\n";
			}
		}
		$html = $html . "</table>\n";
		
		return $html;
	}
	
	/** Create the jump to Perspective Table
	* This table contains links to the root notes of all 
	* the configured perspectives 
	* return formatted html
	*/
	function createJumpToPerspectiveTable(){
		global $perspective_names;
		global $perspective_default_root_nodes;
		global $web_app_page_post_back_name;
		global $url_rest_node_param;
		
		$html = "<table class=\"extra_options\">\n";
		$html = $html . "<tr><td class=\"extra_option\">jump to</td></tr>";
		foreach($perspective_names as $index => $name){
			$html = $html . "<tr><td class=\"extra_option\">";
			$html = $html . "<a href=\"$web_app_page_post_back_name?$url_rest_node_param=";
			$html = $html . $perspective_default_root_nodes[$index]."\">$name</a></td></tr>";
			//if($index < count($perspective_names) - 1){ $html = $html . " or "; }
		}
		$html = $html . "</table>\n";
		
		return $html;
	}
	
	
	/** Create the node color legend table
	* return formatted html
	*/
	function createNodeColorLegendTable($rootCategory){
		global $web_app_page_post_back_name;
		global $url_rest_node_param;
		//global $rootCategory;
		
		global $perspective_category_eregs;
		global $perspective_node_color_maps;
		global $perspective_category_prefixes;
		global $perspective_names;
		
		$perspective_index = -1;
		
		foreach($perspective_category_eregs as $index => $ereg){
			//echo "$index => $ereg against $rootCategory<br />";
			if(ereg($ereg,$rootCategory) != False){
				$perspective_index = $index;
				//echo "Matched! $perspective_index<br />";
				break;
			}
		}
		
		if($perspective_index == -1){ 
			//echo "Index was not set! $perspective_index<br />";
			return "";	
		} 
		
		//echo $perspective_index. "<br />";
		
		$web_app_default_node_color_map 	= $perspective_node_color_maps[$perspective_index];
		$graph_default_category_prefix 		= $perspective_category_prefixes[$perspective_index];
		
		$html = "<table class=\"node_legend_name\">";
		$html = $html . "<tr><td class=\"node_legend_name\">". $perspective_names[$perspective_index]."</td></tr>";
		$html = $html . "</table>";
		$html = $html . "<table class=\"node_color_legend\">";
		//$html = $html . "<tr>";
		
		/** Determine how many categories have been manually colored */
		$colored = count($web_app_default_node_color_map);
		
		/** If the count is not zero */
		if($colored != 0){
			/** The break up the count to give the width of each table cell */
			$percent_each = abs(floor(100 / $colored));
		} else {
			/** Otherwise, the default width is 10px */
			$percent_each = 10;
		}
		
		foreach($web_app_default_node_color_map as $category => $html_color){
			$basename = str_replace($graph_default_category_prefix . "/","",$category);
			$html = $html . "<tr><td class=\"node_legend_block\" style=\"background-color:$html_color;width:$percent_each%;\">";
			$html = $html . "<a href=\"$web_app_page_post_back_name?$url_rest_node_param=". urlencode($category)."\" class=\"node_legend_block\" >$basename</a></td></tr>";
			//$html = $html . "<td class=\"node_legend_text\"></td>";
		}
		
		$html = $html ."</table>";
		
		return $html;
	}
?>