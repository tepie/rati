<?php
	ignore_user_abort(true);
	
	include_once('Include/Database.php');
	include_once('Include/ObjectGraph.php');
	include_once('Include/ObjectNode.php');
	include_once('Include/SettingsWebApp.php');
	include_once('Include/SettingsBranding.php');
	include_once('Include/SettingsDatabase.php');
	include_once('Include/ObjectUsage.php');
	include_once('Include/ObjectUtility.php');
	include_once('Include/HtmlNavigate.php');
	include_once('Include/HtmlCommon.php');
	
	$type_param 			= "type";
	$type_default_export 	= "dot";
	$type_dict 				= array("dot" 	=> "text/plain",
								"html"		=>	"text/html",
								"img" 		=> 	"image/png", 
								"legend" 	=> "text/html",
								"attributes" => "text/html",
								"options" => "text/html");
	
	$export_is_web_based  	= True;
	
	

	function exportImageType(){
		global $command_executable_dot;
		global $graph_default_image_format;
		global $temp_graphviz_filename;
		global $header;
		
		$mapCmd	= "$command_executable_dot -T$graph_default_image_format $temp_graphviz_filename";
		header("Content-type: $header");
		passthru($mapCmd,$ret);
		
		return $ret;
	}
	
	function exportDotType(){
		global $header;
		global $temp_graphviz_filename;
		
		header("Content-type: $header");	
		if(file_exists($temp_graphviz_filename)){
			$h = fopen($temp_graphviz_filename,"rb");
			while (!feof($h)) {
				echo fread($h, 4096);
			}
			
			fclose($h);
		} else {
			echo "IOError: File \"$temp_graphviz_filename\" not found!.";
		}
	}
	
	function exportLegendType(){
		global $header;
		global $local_node;
		header("Content-type: $header");
		echo createNodeColorLegendTable($local_node->getNodeCategory());
	}
	
	function exportOptionsType(){
		global $type_dict;
		global $header;
		global $node_name;
		
		$html = "<table class=\"extra_options\">\n";
		$html = $html . "<tr><td class=\"extra_option\">&nbsp;</td></tr>\n";
		$html = $html . "<tr><td class=\"extra_option\">export as</td></tr>\n";
		
		$keys = array_keys($type_dict);
		foreach($keys as $index => $option){
			$url 	= "Export.php?q=".urlencode($node_name)."&amp;type=$option".commonUrlCustomizationValues($for_html=True);
			$html 	= $html . "<tr><td class=\"extra_option\">";
			$html 	= $html . "<a href=\"$url\">$option</a></td></tr>\n";
		}
		
		$html = $html . "</table>\n";
		header("Content-type: $header");
		echo $html;
	}
	
	function exportAttributesType(){
		global $header;
		global $local_node;
		
		header("Content-type: $header");
		$attributes = $local_node->getNodeAttributes();
		echo createAttributeTableHtml($attributes);
	}
	
	function detect_is_web_export(){
		global $argv;
		if($argv[0] == $_SERVER['PHP_SELF']){
			return False;
		} else {
			return True;
		}
	}
	
	function acceptable_type($type){
		global $type_dict;
		foreach($type_dict as $accepted => $header){
			if($type == $accepted) return true;
		}
		return false;
	}
	
	function select_header($type){
		global $type_dict;
		foreach($type_dict as $accepted => $header){
			if($type == $accepted) return $header;
		}
		return false;
	}
	
	$db_connection 	= new DbConnectionHandler("$mysql_database_host",
		"$mysql_database_user",
		"$mysql_database_passwd"
	);
		
	if(!ISSET($db_connection->link)){
		$db_connection->setupDbLink();
		/** The select database results */
		$x = $db_connection->selectDb("$mysql_database_name");
	}
	
	$export_is_web_based = detect_is_web_export();
	//echo $export_is_web_based;
	if($export_is_web_based){
		if(isset($_GET["$type_param"])){
			$export_type = $_GET["$type_param"];
		} else {
			$export_type = "$type_default_export";
		}
		
		if(isset($_GET["$url_rest_node_param"])){
			$node_name 	= $_GET["$url_rest_node_param"];
		} else {
			header('Location: Index.php');
		} 
	} else {
		if($argc != 7){
			echo "Wrong number of arguments.\n";
			echo "usage: " . $argv[0] . " <export type> <node name> <graph direction> <image size> <graph levels> <neighbor limit>";
			exit();
		} else {
			$export_type 	= $argv[1];
			$node_name 		= $argv[2];
			
			// set the $_GET global variable with our command line arguments to reuse 
			// our session checking functions
			$_GET["$url_rest_custom_image_graph_direction"]	= $argv[3];
			$_GET["$url_rest_custom_image_font_size"]		= $argv[4];
			$_GET["$url_rest_custom_image_graph_levels"]	= $argv[5];
			$_GET["$url_rest_custom_image_graph_neighbors"]	= $argv[6];
			
			// expand our memory limit for a command line run
			// the point is to use the command line for large processing that 
			// should not take place in a web browser
			ini_set("memory_limit","512M");
		}
	}
	
	if(!acceptable_type($export_type)){
		echo "Unsupported Export Type!";
		exit(-1);
	}
	
	$header = select_header($export_type);
	
	// initalize a common session setup
	commonSessionSetup();
	// validation the common session variables, make sure they are acceptable
	// right now, we will ignore the validation from the web and accept the user input
	if($export_is_web_based){
		//echo "This is web based";
		commonValidationCustomizationValues($ignore=false);
	} else {
		//echo "This is not web based\n";
		commonValidationCustomizationValues($ignore=true);
	}
	
	$query_runner 			= new QueryRunner();
	$utility 				= new UtilityObject();
	
	// store our session values as temps so we don't need to use the $_SESSION reference
	$temp_graph_direction 	= $_SESSION["$url_rest_custom_image_graph_direction"];
	$temp_graph_levels		= $_SESSION["$url_rest_custom_image_graph_levels"] + 0;
	$temp_neighbor_limit   	= $_SESSION["$url_rest_custom_image_graph_neighbors"] + 0;
	
	//echo "web:$export_is_web_based\n\n\n";
	//print_r($_GET);
	//print_r($_SESSION);
	
	//echo "neighbor limit: $temp_neighbor_limit\n";
	//echo "graph levels: $temp_graph_levels";
	//
	//exit();
	// if we want to be circular, we have to change the executable from dot to circo
	if($temp_graph_direction == "CIRCO"){
		$command_executable_dot = "circo";
	}
	
	if($export_type == "img" or $export_type == "html" or $export_type == "dot"){
		$g 	= new GraphObject($query_runner,
				true,true,$temp_graph_levels,
				$temp_graph_direction);
		
		$temp_font_size = commonGraphvizFontSize();
		if($temp_font_size == null){
			$temp_graphviz_filename = $g->walk($node_name,$fontsize="8",$neighbor_limit=$temp_neighbor_limit);
		} else {
			$temp_graphviz_filename = $g->walk($node_name,$fontsize=$temp_font_size,$neighbor_limit=$temp_neighbor_limit);
		}
	} else if($export_type == "legend" or $export_type == "attributes"){
		$local_node = new NodeObject($query_runner,$node_name,$temp_neighbor_limit);
	}
	
	if($export_type == "img" or $export_type == "html"){
		$result_setup 	= $utility->setupFileDirectories();
		if(!$result_setup){
			echo "Problem creating needed directories to store files, check permissions.";
			exit(-1);
		}
		
		$checksum 		= md5($temp_graphviz_filename . time() . "");
		
		if($export_type == "html"){
			$img_file 	= "$directory_dot_img" . "$filesystem_path_separator" . "$checksum" . "." . "$graph_default_image_extension";
			$map_file 	= "$directory_dot_map" . "$filesystem_path_separator" . "$checksum" . "." . "$graph_default_map_extension";	
			$img_url 	= "$directory_dot_img" . "$url_path_separator" . "$checksum" . "." . "$graph_default_image_extension";
		}
		
		if($export_type == "img"){
			$ret = exportImageType();
		} else if($export_type == "html"){
			header("Content-type: $header");
			if(!$utility->checkFile("$map_file",$filesystem_age_time)){
				if(!$utility->checkFile("$img_file",$filesystem_age_time)){
					$mapCmd	= "$command_executable_dot -Tcmap -o$map_file -T$graph_default_image_format -o$img_file $temp_graphviz_filename";
					
					exec($mapCmd,$output,$ret);			
				}
			} 
			
			//echo "Return Code: $ret<br />";
			//print_r($output); 
			//echo "<br />";
						
			if(file_exists($map_file)){
				$handle 	= fopen("$map_file","rb");
				//echo "<div class=\"image_html_export\">Loading...";
				echo "<center><img src=\"$img_url\" alt=\"Model\" ";
				echo "class=\"model\" usemap=\"#$img_url\" border=\"0\"></center>\n";
				echo "<map name=\"$img_url\">\n";
				
				while (!feof($handle)) {
					echo ereg_replace('\\\"','"',fread($handle, 4096));
				}
				
				echo "</map>";
				//echo "</div>";
				fclose($handle);
			} else {
				die("<br />$map_file not found!<br />");
			}			
		}
		
	} else if($export_type == "dot"){
		exportDotType();
	} else if($export_type == "legend"){
		exportLegendType();
	} else if($export_type == "attributes"){
		exportAttributesType();
	} else if($export_type == "options"){
		exportOptionsType();
	} else {
		
	}
	//print_r($_SESSION);
	if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 
?>