<?php
	
	//*************************************************************************
	//*	Ab Initio Application 
	//*	Persepective
	//*************************************************************************

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
	$web_app_abi_node_color_map["$abinitio_category_prefix/Column"] 			= "violet";
	$web_app_abi_node_color_map["$abinitio_category_prefix/File"] 				= "gray";
	$web_app_abi_node_color_map["$abinitio_category_prefix/DML"] 				= "cadetblue";
	//$web_app_abi_node_color_map["$abinitio_category_prefix/File Group"] 		= "aquamarine";
	//$web_app_abi_node_color_map["$abinitio_category_prefix/Steward Group"] 		= "deeppink";
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
	//*	EDA Org Chart
	//*	Persepective
	//*************************************************************************

	$edaorg_category_prefix				= 	"/eda/organization/categories";
	$edaorg_category_ereg				= 	"\/eda\/organization\/categories";
	$edaorg_default_root_node			= 	"$edaorg_category_prefix/category";
	$edaorg_category_reference_rule 	= 	"category";
	$edaorg_category_unknown			= 	"Unknown";
	$edaorg_perspective_name			= 	"eda org chart";
	
	$web_app_edaorg_node_color_map		=   array();
	$web_app_edaorg_node_color_map["$edaorg_category_prefix/team"]	= "gray";
	$web_app_edaorg_node_color_map["$edaorg_category_prefix/manager"] 				= "violet";
	$web_app_edaorg_node_color_map["$edaorg_category_prefix/employee"] 				= "orange";
	$web_app_edaorg_node_color_map["$edaorg_category_prefix/contractor"] 			= "yellowgreen";
	$web_app_edaorg_node_color_map["$edaorg_category_prefix/location"]			= "tan";
	$web_app_edaorg_node_color_map["$edaorg_category_prefix/category"] 			= $web_app_default_cat_color;
	
	//*************************************************************************
	//*	Combined Application 
	//*	Persepectives (Ordering matters in arrays)
	//*************************************************************************
	
	$perspective_names				= array($abinitio_perspective_name,$sa_perspective_name,$mdr_perspective_name,$edaorg_perspective_name);
	$perspective_category_prefixes 	= array($abinitio_category_prefix,$sa_category_prefix,$mdr_category_prefix,$edaorg_category_prefix);
	$perspective_category_eregs		= array($abinitio_category_ereg,$sa_category_ereg,$mdr_category_ereg,$edaorg_category_ereg);
	$perspective_default_root_nodes = array($abi_default_root_node,$sa_default_root_node,$mdr_default_root_node,$edaorg_default_root_node);
	$perspective_category_reference_rules = array($abinitio_category_reference_rule,$sa_category_reference_rule,$mdr_category_reference_rule,$edaorg_category_reference_rule);
	$perspective_node_color_maps = array($web_app_abi_node_color_map,$web_app_sa_node_color_map,$web_app_mdr_node_color_map,$web_app_edaorg_node_color_map);
?>
