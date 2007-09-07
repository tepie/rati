<?php
	include_once('Include\\Database.php');
	include_once('Include\\ObjectGraph.php');
	include_once('Include\\SettingsWebApp.php');
	include_once('Include\\SettingsDatabase.php');
	include_once('Include\\ObjectUsage.php');
	include_once('Include\\ObjectUtility.php');
	include_once('Include\\HtmlNavigate.php');
	include_once('Include\\HtmlCommon.php');
	
	$type_param 			= "type";
	$type_default_export 	= "xml";
	$type_dict 				= array( "xml" => "text/xml", "dot" => "text/plain", "img" => "image/png", "html" => "text/html");
	
	function howto_export(){
		
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
	
	if(isset($_GET["$type_param"])){
		$export_type = $_GET["$type_param"];
	} else {
		$export_type = "$type_default_export";
	}
	
	if(!acceptable_type($export_type)){
		header("Content-type: ". $type_dict[$type_default_export]);
		echo "<rati><export>Unsupported Export Type! xml,dot, and img are the options!</export></rati>";
		exit(-1);
	}
	
	$header = select_header($export_type);
	commonSessionSetup();
	header("Content-type: $header");
		
	// Check the URL for a "node" parameter and set the $node_name
	if(isset($_GET["$url_rest_node_param"])){
		/** Setup the database connection, provide the host, username and password */	
		/** Set the node name to be used throughout this navigation */
		$node_name = $_GET["$url_rest_node_param"];
		
		$query_runner 	= new QueryRunner();
		$temp_graph_direction = $_SESSION["$url_rest_custom_image_graph_direction"];
		$temp_arrow_direction = $_SESSION["$url_rest_custom_image_arrow_direction"];
		$g = new GraphObject($query_runner,true,true,1,$temp_graph_direction,$temp_arrow_direction);
		$g->walk($node_name);
		
		if($_SESSION[$url_rest_custom_image_font_size] == "L"){
		/** The graphviz string LARGE */
			$temp_graph =  $g->getGraphvizSring($fontsize="14");
		} else {
		/** The graphviz string */
			$temp_graph 	=  $g->getGraphvizSring();
		}
		
		if($export_type == "xml"){
			echo $g->getExportXml();
		} elseif ($export_type == "dot"){
			echo $temp_graph;
		} elseif ($export_type == "img"){
			$graph_string 	= $temp_graph;
			$utility 		= new UtilityObject();
			$result_setup 	= $utility->setupFileDirectories();
		
			if(!$result_setup){
				echo "Problem creating needed directories to store files, check permissions.";
				exit(-1);
			}
			
			$escaped 		= escapeshellarg($graph_string);
			$checksum 		= md5($graph_string);
			$dot_file 		= "$directory_dot_graph" . "$filesystem_path_separator" . "$checksum";
			$img_file 		= "$directory_dot_img" . "$filesystem_path_separator" . "$checksum" . "." . "$graph_default_image_extension";
			
			if($utility->checkFile($dot_file,$filesystem_age_time)){
				/** File Handle */
				$handle 	= fopen($dot_file,"w+");
				fwrite($handle,$graph_string);
				fclose($handle);
			} 
			
			$mapCmd	= "$command_executable_dot -T$graph_default_image_format $dot_file";
			//echo $mapCmd;
			passthru($mapCmd,$ret);
			
		} elseif($export_type == "html"){
			$graph_string 	= $g->getGraphvizSring();
			$utility 		= new UtilityObject();
			$result_setup = $utility->setupFileDirectories();
			
			/** Generate a MD5 checksum against the graph graphviz string */
			$checksum 	= md5($graph_string);
				//echo $checksum."<br />";
				/** Escape the checksum value to be used in the shell */
			$escaped 	= escapeshellarg($checksum);
			
			if(!$result_setup){
				echo "Problem creating needed directories to store files, check permissions.";
				exit(-1);
			}
			
			/** The attributes of the root node to show */
			$rootAttributes = $g->getRootNodeAttributes();
			$rootCategory 	= $g->getRootCategory();
			
			/** The output file for the dot graph */
			$dot_file = "$directory_dot_graph" . "$filesystem_path_separator" . "$checksum";
			/** The output file for the map file */
			$map_file = "$directory_dot_map" . "$filesystem_path_separator" . "$checksum" . "." . "$graph_default_map_extension";
			/** The image output file */
			$img_file = "$directory_dot_img" . "$filesystem_path_separator" . "$checksum" . "." . "$graph_default_image_extension";
			/** The URL to the image */
			$img_url = "$directory_dot_img" . "$url_path_separator" . "$checksum" . "." . "$graph_default_image_extension";
			
			if(!$utility->checkFile($dot_file,$filesystem_age_time)){
				/** File Handle */
				$handle 	= fopen($dot_file,"w+");
				fwrite($handle,$graph_string);
				fclose($handle);
			} 
			
			if(!$utility->checkFile("$map_file",$filesystem_age_time)){
				if(!$utility->checkFile("$img_file",$filesystem_age_time)){
					$mapCmd	= "$command_executable_dot -Tcmap -o$map_file -T$graph_default_image_format -o$img_file $dot_file";
					exec($mapCmd,$output,$ret);
				}
			} 

			/** Read the contents of the map file */
			$map_contents 	= file_get_contents("$map_file");
			/** Clean the map file slashes */
			$cleanMap 		= ereg_replace('\\\"','"',$map_contents);
			
			$html = "<table width=\"99%\">";
			$html = $html . "<tr><td>";
			$html = $html . "<img src=\"$img_url\" alt=\"Model\" ";
			$html = $html . "class=\"model\" usemap=\"#$img_url\" border=\"0\">\n";
			$html = $html . "<map name=\"$img_url\">\n";
			$html = $html . $cleanMap;
			$html = $html . "</map>\n";
			$html = $html . "</td><td width=\"20%\" style=\"vertical-align:top;\">";
			//echo "<div id=\"scroll-legend\" style=\"position:absolute;\" >";
			$html = $html . createNodeColorLegendTable($rootCategory);
			//echo "</div>";
			$html = $html . "</td></tr></table>";
			echo $html ;
		} else {
			echo $g->getExportXml();
		}
	} else {
		echo "<rati><export>Error, no node selected</export></rati>";
	}
	
	if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 
?>