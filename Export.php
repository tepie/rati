<?php
	include_once('Include\\Database.php');
	include_once('Include\\ObjectGraph.php');
	include_once('Include\\ObjectNode.php');
	include_once('Include\\SettingsWebApp.php');
	include_once('Include\\SettingsDatabase.php');
	include_once('Include\\ObjectUsage.php');
	include_once('Include\\ObjectUtility.php');
	include_once('Include\\HtmlNavigate.php');
	include_once('Include\\HtmlCommon.php');
	
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
			$url 	= "Export.php?q=".urlencode($node_name)."&type=$option".commonUrlCustomizationValues();
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
		if($argc != 6){
			echo "Wrong number of arguments.\n";
			echo "usage: " . $argv[0] . " <export type> <node name> <graph direction> <image size> <graph levels>";
			exit();
		} else {
			$export_type 	= $argv[1];
			$node_name 		= $argv[2];
			
			$_GET["$url_rest_custom_image_graph_direction"]	= $argv[3];
			$_GET["$url_rest_custom_image_font_size"]		= $argv[4];
			$_GET["$url_rest_custom_image_graph_levels"]	= $argv[5];
			ini_set("memory_limit","512M");
		}
	}
	
	if(!acceptable_type($export_type)){
		echo "Unsupported Export Type!";
		exit(-1);
	}
	
	$header = select_header($export_type);
	
	commonSessionSetup();
	commonValidationCustomizationValues();	
	
	$query_runner 			= new QueryRunner();
	$utility 				= new UtilityObject();
	$temp_graph_direction 	= $_SESSION["$url_rest_custom_image_graph_direction"];
	$temp_graph_levels		= $_SESSION["$url_rest_custom_image_graph_levels"] + 0;
	
	if($temp_graph_direction == "CIRCO"){
		$command_executable_dot = "circo";
	}
	
	if($export_type == "img" or $export_type == "html" or $export_type == "dot"){
		$g 	= new GraphObject($query_runner,
				true,true,$temp_graph_levels,
				$temp_graph_direction);
		
		$temp_font_size = commonGraphvizFontSize();
		if($temp_font_size == null){
			$temp_graphviz_filename = $g->walk($node_name);
		} else {
			$temp_graphviz_filename = $g->walk($node_name,$fontsize=$temp_font_size);
		}
	} else if($export_type == "legend" or $export_type == "attributes"){
		$local_node = new NodeObject($query_runner,$node_name,$mysql_database_neighbor_limit);
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
			//echo "Command Output:" . print_r($output). "<br />";
						
			if(file_exists($map_file)){
				$handle 	= fopen("$map_file","rb");
				
				echo "<center><img src=\"$img_url\" alt=\"Model\" ";
				echo "class=\"model\" usemap=\"#$img_url\" border=\"0\"></center>\n";
				echo "<map name=\"$img_url\">\n";
				
				while (!feof($handle)) {
					echo ereg_replace('\\\"','"',fread($handle, 4096));
				}
				
				echo "</map>";		
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
	
	if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 
?>