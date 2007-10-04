<?php
	
	/** Web application Settings */
	
	/** Path Separator */
	$filesystem_path_separator  = "/";
	/** The native file system age time use to determine if files written to disk need to be refreshed */
	$filesystem_age_time		= 1000;
	
	// Web Application Variables
	/** The main image navigation page */
	$web_app_page_post_back_name	=	"Navigate.php";
	/** The main content search page */
	$web_app_page_search_name		= 	"Search.php";
	/** The about page */
	$web_app_page_about_name		= 	"About.php";
	/** The how it works page */
	$web_app_page_how_name			= 	"How.php";
	/** The index */
	$web_app_page_index_name		= 	"Index.php";
	/** The import page */
	$web_app_page_import_name		= 	"Import.php";
	/** The import page */
	$web_app_page_export_name		= 	"Export.php";
	/** The customization page */
	$web_app_page_custom_name		= 	"Customize.php";
	/** The usage page */
	$web_app_page_usage_name		=	"Usage.php";

	/** The include directory */
	$web_app_dir_include_name		= 	"Include";
	
	/** The javascript file */
	$web_app_page_javascript	 	= 	"./$web_app_dir_include_name/Javascript.js";
	/** The Doxygen HTML pages */
	$web_app_page_doxygen			= "./Doxygen/html/";
	
	/** URL path separator used when making URLs */
	$url_path_separator			= "/";
	/** URL parameter used when requesting a node */
	$url_rest_node_param		= "q";
	/** URL parameter used when performing a search */
	$url_rest_search_param		= "q";
	$url_rest_search_page		= "page";
	/** The import REST xml string parameter */
	$url_rest_import_xml_string_param 		= "xml_string";
	/** The graph direction REST parm */
	$url_rest_custom_image_graph_direction 	= "image_graph_direction";
	/** The image font size REST param */
	$url_rest_custom_image_font_size 		= "image_font_size";
	/** The image graph levels REST parm */
	$url_rest_custom_image_graph_levels		= "image_graph_levels";
	/** The image graph neighbor REST param */
	$url_rest_custom_image_graph_neighbors  = "image_neighbor_limit";
	
	$url_rest_page_show_export_options  = "page_show_export_options";
	$url_rest_page_show_graph_legend  	= "page_show_graph_legend";
	$url_rest_page_show_jumpto_links  	= "page_show_jump_to_links";
	
	/** A collection of the customization REST parameters */
	$url_rest_custom_array = array(
		$url_rest_custom_image_graph_direction,
		$url_rest_custom_image_font_size,
		$url_rest_custom_image_graph_levels,
		$url_rest_custom_image_graph_neighbors);
		
	$custom_true_false_accepted = array("True" =>"True", "False" => "False");
		
	/** The acceptable graph direction map settings */
	$custom_image_graph_direction_accepted 	= array("LR" => "Left to Right", "TB" => "Top to Bottom", "CIRCO" => "Circular");
	/** The acceptable image font sizes */
	$custom_image_font_size_accepted		= array("N" => "Normal", "L" => "Large", "S" => "Small" );
	/** The acceptable graph levels map */
	$custom_image_graph_levels_accepted		= array("1" => "One", "2" => "Two");
	/** The acceptable neighbor limits map */
	$custom_image_neighbor_limit_accepted	= array("5" => "Five", "10" => "Ten", 
												"30" => "Thirty", "50" => "Fifty", "100" => "One Hundred",
												"300" => "Three Hundred","500" => "Five Hundred","1000" => "One Thousand");
	/** A collection of all the acceptable map values */
	$custom_accepted_array = array(
		$custom_image_graph_direction_accepted,
		$custom_image_font_size_accepted,
		$custom_image_graph_levels_accepted,
		$custom_image_neighbor_limit_accepted);
	
	/** Used to determined if the export options are displayed */
	$web_app_show_export_options = false;

?>