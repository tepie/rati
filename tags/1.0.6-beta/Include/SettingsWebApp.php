<?php
	
	// Folder Path Separator
	/** The native file system path separator. 
	* Use "\\" for windows and "/" for Unix like systems.
	*/
	$filesystem_path_separator  = "\\";
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
	
	$url_rest_import_xml_string_param 		= "xml_string";
	//$url_rest_custom_image_arrow_direction 	= "image_arrow_direction";
	$url_rest_custom_image_graph_direction 	= "image_graph_direction";
	$url_rest_custom_image_font_size 		= "image_font_size";
	$url_rest_custom_image_graph_levels		= "image_graph_levels";
	$url_rest_custom_image_graph_neighbors  = "image_neighbor_limit";
	
	$url_rest_custom_array = array(
		$url_rest_custom_image_graph_direction,
		$url_rest_custom_image_font_size,
		$url_rest_custom_image_graph_levels,
		$url_rest_custom_image_graph_neighbors);
		
	//$custom_image_arrow_direction_accepted 	= array("0" => "Same Line Directions", "1" => "Actual Line Directions");
	$custom_image_graph_direction_accepted 	= array("LR" => "Left to Right", "TB" => "Top to Bottom", "CIRCO" => "Circular");
	$custom_image_font_size_accepted		= array("N" => "Normal", "L" => "Large", "S" => "Small" );
	$custom_image_graph_levels_accepted		= array("1" => "One", "2" => "Two");
	$custom_image_neighbor_limit_accepted	= array("5" => "Five", "10" => "Ten", 
												"30" => "Thirty", "50" => "Fifty", "100" => "One Hundred",
												"300" => "Three Hundred");
	
	$custom_accepted_array = array(
		$custom_image_graph_direction_accepted,
		$custom_image_font_size_accepted,
		$custom_image_graph_levels_accepted,
		$custom_image_neighbor_limit_accepted);

?>