<?php
	
	/**
	* PLEASE READ
	* Persepectives are a selective way to apply color to a group of information.
	* This group likely shares a domain of interest, such that it is already organized in
	* some way, and you want to capture that for visualization and apply colors too it.
	* 
	* In order to do this, there are some rules to follow when editing this file, 
	* meaning there is a way to configure a perpective properly. The following explains how.
	* 
	* 1. Define the category prefix:
	* 	This is the folder prefix in front of all your data objects. It is smart
	* 	to organize the objects of a perspective in a contained area so they do 
	* 	not impact other perspectives.
	* 2. Define the regular expression string to detect what is a category of 
	* your perspective.
	* 	You'll need to just escape characters and such
	* 3. Define the default root node
	* 	This tool prvodes the ability to start browsing a perspective at
	* 	any time. Pick the top most point to start at. 
	* 4. Define the category reference rule
	* 	This is the rule that says "hey, I am referencing a category"
	* 5. Define a lable for unknown categories. 
	* 6. Define a display name for this perspective
	* 7. Define a color map for your perspective.
	* 	This is a key value array where the keys are the paths to the categories
	* 	and the values are the colors to color the nodes in the images.
	* 8. Add your perspective to the combined application perspective area at the bottom of this file.
	* 	The combined perspectives are just arrays joining the parts to collect and present
	* 	perspectives for use. 
	*/
	
	/**
	* Ab Initio Application Persepective
	*/

	$abinitio_category_prefix			= 	"/abinitio/categories";
	$abinitio_category_ereg				= 	"\/abinitio\/categories\/";
	$abi_default_root_node				= 	"$abinitio_category_prefix/Subject Area";
	$abinitio_category_reference_rule 	= 	"categoryOID";
	$abinitio_category_unknown			= 	"Unknown";
	$abinitio_perspective_name			= 	"ab initio";
	
	$web_app_abi_node_color_map			=   array();
	$web_app_abi_node_color_map["$abinitio_category_prefix/Subject Area"] 		= "LightCoral";
	$web_app_abi_node_color_map["$abinitio_category_prefix/Conceptual Entity"] 	= "orange";
	$web_app_abi_node_color_map["$abinitio_category_prefix/Logical Element"] 	= "yellowgreen";
	$web_app_abi_node_color_map["$abinitio_category_prefix/File"]                           = "gray";
	$web_app_abi_node_color_map["$abinitio_category_prefix/Column"] 			= "violet";
	$web_app_abi_node_color_map["$abinitio_category_prefix/DML"] 				= "cadetblue";
	$web_app_abi_node_color_map["$abinitio_category_prefix/Graph"] 		= "deeppink";
	$web_app_abi_node_color_map["$abinitio_category_prefix/category"] 			= $web_app_default_cat_color;
	
	
	//*************************************************************************
	//*	System Architect Application 
	//*	Persepective
	//*************************************************************************

	$sa_category_prefix					= 	"/system architect/categories";
	$sa_category_ereg					= 	"\/system\ architect\/categories\/";
	$sa_default_root_node				= 	"$sa_category_prefix/Encyclopedia";
	$sa_category_reference_rule			= 	"category";
	$sa_category_unknown				= 	"Unknown";
	$sa_perspective_name				= 	"system architect";
	
	$web_app_sa_node_color_map			=   array();
	$web_app_sa_node_color_map["/system architect/categories/Encyclopedia"] 	= "tan";
	$web_app_sa_node_color_map["/system architect/categories/Model"] 			= "LightCoral";
	$web_app_sa_node_color_map["/system architect/categories/Entity"] 			= "orange";
	$web_app_sa_node_color_map["/system architect/categories/Attribute"] 		= "yellowgreen";
	$web_app_sa_node_color_map["/system architect/categories/Data Element"] 	= "cadetblue";
	$web_app_sa_node_color_map["/system architect/categories/Column"] 			= "blueviolet";
	$web_app_sa_node_color_map["/system architect/categories/Table"] 			= "plum";
	$web_app_sa_node_color_map["/system architect/categories/Database"] 		= "seagreen";
	$web_app_sa_node_color_map["/system architect/categories/category"] 		= $web_app_default_cat_color;
	
	//*************************************************************************
	//*	MDR Application (galileo)
	//*	Persepective
	//*************************************************************************

	$mdr_category_prefix			= 	"/galileo/Metadata/Categories";
	$mdr_category_ereg				= 	"\/galileo\/Metadata\/Categories";
	$mdr_default_root_node			= 	"$mdr_category_prefix/category";
	$mdr_category_reference_rule 	= 	"category";
	$mdr_category_unknown			= 	"Unknown";
	$mdr_perspective_name			= 	"galileo";
	
	$web_app_mdr_node_color_map		=   array();
	$web_app_mdr_node_color_map["$mdr_category_prefix/Subject Area"] 		= "LightCoral";
	$web_app_mdr_node_color_map["$mdr_category_prefix/Entity"] 				= "orange";
	$web_app_mdr_node_color_map["$mdr_category_prefix/Element"] 			= "yellowgreen";
	$web_app_mdr_node_color_map["$mdr_category_prefix/Column"] 				= "violet";
	$web_app_mdr_node_color_map["$mdr_category_prefix/Source Structure"]	= "gray";
	$web_app_mdr_node_color_map["$mdr_category_prefix/File Group"]			= "tan";
	$web_app_mdr_node_color_map["$mdr_category_prefix/category"] 			= $web_app_default_cat_color;
	
	//*************************************************************************
	//*	Exeros
	//*	Persepective
	//*************************************************************************

	$exeros_category_prefix				= 	"/exeros/categories";
	$exeros_category_ereg				= 	"\/exeros\/categories";
	$exeros_default_root_node			= 	"$exeros_category_prefix/category";
	$exeros_category_reference_rule 	= 	"category";
	$exeros_category_unknown			= 	"Unknown";
	$exeros_perspective_name			= 	"exeros";
	
	$web_app_exeros_node_color_map		=   array();
	$web_app_exeros_node_color_map["$exeros_category_prefix/dataset"]	= "gray";
	$web_app_exeros_node_color_map["$exeros_category_prefix/column"] 	= "violet";
	$web_app_exeros_node_color_map["$exeros_category_prefix/map"] 		= "orange";
	$web_app_exeros_node_color_map["$exeros_category_prefix/category"] 	= $web_app_default_cat_color;
	
	//*************************************************************************
	//*	Combined Application 
	//*	Persepectives (Ordering matters in arrays)
	//*	Order matters because the position of the perspective name needs to match in all
	//* 	other positions of the collection arrays
	//*************************************************************************
	
	/** A collection of all the perspective names */
	$perspective_names				= array($abinitio_perspective_name,
		$sa_perspective_name,
		$mdr_perspective_name,
		$exeros_perspective_name
	);
	/** A collection of all the perspective category prefixes */
	$perspective_category_prefixes 	= array($abinitio_category_prefix,
		$sa_category_prefix,
		$mdr_category_prefix,
		$exeros_category_prefix
	);
	/** A collection of all the perspective category prefix regular expressions */
	$perspective_category_eregs		= array($abinitio_category_ereg,
		$sa_category_ereg,
		$mdr_category_ereg,
		$exeros_category_ereg
	);
	/** A collection of all the perspective default root nodes */
	$perspective_default_root_nodes = array($abi_default_root_node,
		$sa_default_root_node,
		$mdr_default_root_node,
		$exeros_default_root_node
	);
	/** A collection of all the perspective category reference rules */
	$perspective_category_reference_rules = array($abinitio_category_reference_rule,
		$sa_category_reference_rule,
		$mdr_category_reference_rule,
		$exeros_category_reference_rule
	);
	/** A collection of all the perspective node color maps */
	$perspective_node_color_maps = array($web_app_abi_node_color_map,
		$web_app_sa_node_color_map,
		$web_app_mdr_node_color_map,
		$web_app_exeros_node_color_map
	);
?>
