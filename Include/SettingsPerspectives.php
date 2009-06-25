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
	* Rosetta IBM Commerce & Java Solution Center Persepective
	*/

	$ibmjcs_category_prefix				= 	"/ibmjcs/categories";
	$ibmjcs_category_ereg				= 	"\/ibmjcs\/categories\/";
	$ibmjcs_default_root_node			= 	"$ibmjcs_category_prefix/Aspect";
	$ibmjcs_category_reference_rule 		= 	"category";
	$ibmjcs_category_unknown			= 	"Unknown";
	$ibmjcs_perspective_name			= 	"IBM Commerce & Java Solution Center";
	
	$web_app_ibmjcs_node_color_map			=   array();
	$web_app_ibmjcs_node_color_map["$ibmjcs_category_prefix/Resource"] 		= "LightCoral";
	$web_app_ibmjcs_node_color_map["$ibmjcs_category_prefix/Team"] 			= "orange";
	$web_app_ibmjcs_node_color_map["$ibmjcs_category_prefix/Project"] 		= "yellowgreen";
	$web_app_ibmjcs_node_color_map["$ibmjcs_category_prefix/Title"]			= "gray";
	$web_app_ibmjcs_node_color_map["$ibmjcs_category_prefix/category"]		= $web_app_default_cat_color;
	
	//*************************************************************************
	//*	Combined Application 
	//*	Persepectives (Ordering matters in arrays)
	//*	Order matters because the position of the perspective name needs to match in all
	//* 	other positions of the collection arrays
	//*************************************************************************
	
	/** A collection of all the perspective names */
	$perspective_names				= array($ibmjcs_perspective_name);

	/** A collection of all the perspective category prefixes */
	$perspective_category_prefixes 	= array($ibmjcs_category_prefix);

	/** A collection of all the perspective category prefix regular expressions */
	$perspective_category_eregs		= array($ibmjcs_category_ereg);

	/** A collection of all the perspective default root nodes */
	$perspective_default_root_nodes = array($ibmjcs_default_root_node);

	/** A collection of all the perspective category reference rules */
	$perspective_category_reference_rules = array($ibmjcs_category_reference_rule);

	/** A collection of all the perspective node color maps */
	$perspective_node_color_maps = array($web_app_ibmjcs_node_color_map);
?>
