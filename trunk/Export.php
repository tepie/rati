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
	//$type_dict 				= array( "xml" => "text/xml", "dot" => "text/plain", "img" => "image/png", "html" => "text/html");
	$type_dict 				= array("dot" 	=> "text/plain",
								"html"		=>	"text/html",
								"img" 		=> 	"image/png", 
								"legend" 	=> "text/html",
								"attributes" => "text/html",
								"options" => "text/html");
	
	$export_is_web_based  	= True;
	
	

	function detect_is_web_export(){
		//global $argc;
		global $argv;
		//echo $argc."\n";
		if($argv[0] == "Export.php"){
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
	//print_r($argv);
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
		//echo "This is a command line export\n";
		//echo $argc;
		if($argc != 6){
			echo "Wrong number of arguments.\n";
			echo "usage: " . $argv[0] . " <export type> <node name> <graph direction> <image size> <graph levels>";
			exit();
		} else {
			$export_type 	= $argv[1];
			$node_name 		= $argv[2];
			
			//$_GET["$url_rest_custom_image_arrow_direction"] = $argv[3];
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
	$temp_arrow_direction 	= $_SESSION["$url_rest_custom_image_arrow_direction"];
	$temp_graph_levels		= $_SESSION["$url_rest_custom_image_graph_levels"] + 0;
	
	if($temp_graph_direction == "CIRCO"){
		$command_executable_dot = "circo";
	}
	
	if($export_type == "img" or $export_type == "html" or $export_type == "dot"){
		$g 	= new GraphObject($query_runner,
				true,true,$temp_graph_levels,
				$temp_graph_direction,$temp_arrow_direction);
								
		$g->walk($node_name);
		
		$temp_font_size = commonGraphvizFontSize();
		if($temp_font_size == null){
			$graph_string = $g->getGraphvizSring();
		} else {
			$graph_string = $g->getGraphvizSring($fontsize="$temp_font_size");
			//$graph_string = $g->getGraphvizSring();
		}
	} else if($export_type == "legend" or $export_type == "attributes"){
		$node = new NodeObject($query_runner,$node_name,$mysql_database_neighbor_limit);
	}
	
	if($export_type == "img" or $export_type == "html"){
		$result_setup 	= $utility->setupFileDirectories();
		if(!$result_setup){
			echo "Problem creating needed directories to store files, check permissions.";
			exit(-1);
		}
		
		$escaped 		= escapeshellarg($graph_string);
		$checksum 		= md5($graph_string);
		
		$dot_file 		= "$directory_dot_graph" . "$filesystem_path_separator" . "$checksum";
		
		/*if($url_rest_custom_image_graph_direction == "CIRCO"){
			echo "Changing command line...";
			$command_executable_dot = "circo";
		}*/
		
		if(!file_exists($dot_file) or $utility->checkFile($dot_file,$filesystem_age_time)){
			/** File Handle */
			$handle 	= fopen($dot_file,"w+");
			fwrite($handle,$graph_string);
			fclose($handle);
		} 
		
		if($export_type == "html"){
			$img_file 	= "$directory_dot_img" . "$filesystem_path_separator" . "$checksum" . "." . "$graph_default_image_extension";
			$map_file 	= "$directory_dot_map" . "$filesystem_path_separator" . "$checksum" . "." . "$graph_default_map_extension";	
			$img_url 	= "$directory_dot_img" . "$url_path_separator" . "$checksum" . "." . "$graph_default_image_extension";
		}
		
		if($export_type == "img"){
			$mapCmd	= "$command_executable_dot -T$graph_default_image_format $dot_file";
			header("Content-type: $header");
			passthru($mapCmd,$ret);
		} else if($export_type == "html"){
			header("Content-type: $header");
			//echo $command_executable_dot;
			if(!$utility->checkFile("$map_file",$filesystem_age_time)){
				if(!$utility->checkFile("$img_file",$filesystem_age_time)){
					$mapCmd	= "$command_executable_dot -Tcmap -o$map_file -T$graph_default_image_format -o$img_file $dot_file";
					exec($mapCmd,$output,$ret);			
				}
			} 
			//echo $ret;
			//print_r( $output);
			//echo "<pre>$mapCmd</pre>";
			/** Read the contents of the map file */
			//echo "exists" .file_exists($dot_file) == true;
			
			if(file_exists($map_file)){
				$handle 	= fopen("$map_file","rb");
				$cleanMap 	= "";
				
				while (!feof($handle)) {
					$cleanMap .= ereg_replace('\\\"','"',fread($handle, 8192));
				}
				
				$map_lines = split("\n",$cleanMap);
				/*$new_lines = array();
				foreach($map_lines as $index => $line){				
					if(ereg("href=\".+\"",$line)){
						$area_parts = explode(" ",$line);
						$new_parts = array();
						foreach($area_parts as $area_index => $part){
							if(ereg("href=\".+\"",$part)){
								array_push($new_parts,$part . " target=\"_parent\"");
							} else{
								array_push($new_parts,$part);
							}
						}
						$new_lines[$index] = "";
						foreach($new_parts as $new_index => $part){
							$new_lines[$index] = $new_lines[$index] . $part . " ";
						}
					} else {
						$new_lines[$index] = $line;
					}
				}
				$newMap		= "";
				foreach($new_lines as $index => $line){	
					$newMap = $newMap . "$line\n";
				}
				*/
				//print_r(htmlspecialchars($newMap));
				
				fclose($handle);
			} else {
				die("<br />$map_file not found!<br />");
			}
			
			//echo "<!-- Setup done -->";
			echo "<center><img src=\"$img_url\" alt=\"Model\" ";
			echo "class=\"model\" usemap=\"#$img_url\" border=\"0\"></center>\n";
			echo "<map name=\"$img_url\">\n";
			echo $cleanMap;
			//echo $newMap;
			echo "</map>";			
		}
		
	} else if($export_type == "dot"){
		header("Content-type: $header");
		echo $graph_string;
	} else if($export_type == "legend"){
		header("Content-type: $header");
		echo createNodeColorLegendTable($node->getNodeCategory());
	} else if($export_type == "attributes"){
		header("Content-type: $header");
		echo createAttributeTableHtml($node->getNodeAttributes());
	} else if($export_type == "options"){
		$html = "<table class=\"extra_options\">\n";
		$html = $html . "<tr><td class=\"extra_option\">&nbsp;</td></tr>\n";
		$html = $html . "<tr><td class=\"extra_option\">export as</td></tr>\n";
		
		$keys = array_keys($type_dict);
		foreach($keys as $index => $option){
			$url = $url_html_export = "Export.php?q=".urlencode($node_name)."&type=$option".commonUrlCustomizationValues();
			$html = $html . "<tr><td class=\"extra_option\">";
			$html = $html . "<a href=\"$url\">$option</a></td></tr>\n";
		}
		$html = $html . "</table>\n";
		header("Content-type: $header");
		echo $html;
	} else {
		
	}
	
	if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 
?>