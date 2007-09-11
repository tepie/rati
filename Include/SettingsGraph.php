<?php
	
	/** The default node color used in the graph images */ 
	$web_app_default_node_color		=   "lightblue";
	/** The default node color used for categories */
	$web_app_default_cat_color		=	"gold";
	
	/**  The default graph output direction (left to right) */
	$graph_default_direction		= 	"LR";
	/** The default graph output image format */
	$graph_default_image_format		= 	"png";
	/** The default graph image output formate extension */
	$graph_default_image_extension	=	"png";
	/** The default graph map output extension */
	$graph_default_map_extension	=	"map";
	/** The default graph dot output extension */
	$graph_default_dot_extension	=	"";
	
	// Filesystem directory names
	/** The output directory name for dot files */
	$directory_dot_graph		= "dot";
	/** The output directory name for map files */
	$directory_dot_map			= "map";
	/** The output directory name for graphic files */
	$directory_dot_img			= "img";
	
	// Command line tools
	/** The path to the dot executable for graph, map and image generation */
	//$command_executable_dot		= "circo";
	$command_executable_dot		= "dot";
	
	//Graphviz string settings
	/** The graphviz heading line to start the graph */
	$graphviz_string_heading_line		= "digraph G{\n";
	/** The graphviz string graph attributes */
	$graphviz_string_graph_attributes	= 'graph [rankdir = "%s",label="%s"]';
	/** The default font size for node text in the graph */
	$graphviz_string_nodes_fontsize		= "8";
	/** The default font size for link text in the graph */
	$graphviz_string_links_fontsize		= "8";
	/** The graphviz node attributes string, expects a font size when printing */
	$graphviz_string_nodes_attribute	= "\tnode [shape=box,color=lightblue2,style=\"filled,rounded\",fontsize=%s,fontname=\"verdana\"];\n";
	/** The graphviz string footer to end the graph */
	$graphviz_string_footing_line		= "}\n";
	/** The graphviz string left to right link arrow */
	$graphviz_string_link_lr_arrow		= " -> ";
	/** The graphviz string for link attributes, expects a text label and a font size */
	$graphviz_string_link_attributes	= '[label = "%s",fontname="Verdana",fontsize=%s,dir="%s"];';
	/** The graphviz string link node text, expects an identifier when printing */
	$graphviz_string_link_node			= 'node%s'; 
	$graphviz_string_node_attributes	= '[label="%s",URL="%s",color="%s",target="_parent",tooltip="%s"];'
	
?>