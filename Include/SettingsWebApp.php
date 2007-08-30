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
	$url_rest_custom_image_arrow_direction 	= "image_arrow_direction";
	
	$web_app_default_style_sheet	= 	"./$web_app_dir_include_name/style.css";
	/** The application logo (not used) */
	$web_app_default_logo_image		= 	"./$web_app_dir_include_name/rati.gif";
	/** The application author full name */
	$web_app_author_full_name		= 	"Terrence A Pietrondi";
	/** The application author short name */
	$web_app_author_short_name		= 	"terry";
	/** The application author e-mail address */
	$web_app_author_email			= 	"terrence_a_pietrondi@keybank.com";
	/** The organizational unit that this application is being developed under */
	$web_app_organizational_dept	= 	"enterprise data architecture";
	/** The organizational unit main web site */
	$web_app_organizational_url		= 	"http://csc06shpntpw01s/team/EDA/default.aspx";
	/** The source code of this application */
	$web_app_source_code_url	 	= 	"http://docsum.svn.sourceforge.net/viewvc/docsum/rati/";
	/** The full name of this application */
	$web_app_name_full				= 	"relational analysis through images";
	/** The short name of this application */
	$web_app_name_abbrv				= 	"rati";
	/** The prefix used on page titles in this application */
	$web_app_page_title_prefix 		= 	$web_app_name_abbrv;
	/** The version number of this application */
	$web_app_version_number			= 	"1.0";

?>