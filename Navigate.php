<?php
		
	include_once('Include\\Database.php');
	include_once('Include\\ObjectGraph.php');
	include_once('Include\\ObjectUtility.php');
	include_once('Include\\ObjectUsage.php');

	include_once('Include\\SettingsWebApp.php');
	include_once('Include\\SettingsDatabase.php');
	include_once('Include\\SettingsGraph.php');
	include_once('Include\\SettingsPerspectives.php');

	include_once('Include\\HtmlCommon.php');
	include_once('Include\\HtmlNavigate.php');
	
	/** Setup the database connection, provide the host, username and password */
	$db_connection 	= new DbConnectionHandler("$mysql_database_host",
		"$mysql_database_user",
		"$mysql_database_passwd"
	);
	
	
	// Check the URL for a "node" parameter and set the $node_name
	if(isset($_GET["$url_rest_node_param"])){
		/** Set the node name to be used throughout this navigation */
		//$node_name = urldecode($_GET["$url_rest_node_param"]);
		$node_name = $_GET["$url_rest_node_param"];
		
	}
	
	// If the $node_name is not set, we didn't catch it in the URL, set the default
	// Default is the subject area
	if(!isset($node_name)){
		/** Set the node name to be used throughout this navigation */
		//$node_name = "$graph_default_root_node";
		header('Location: Index.php');
	}
	
	//if(isset($_GET["focus"])){
		/** Set the focus */
		//$focus = urldecode($_GET["focus"]);
	//}
	
	//if(!isset($focus) or $focus != "image" and $focus != "attributes"){
		/** Set the focus */
		//$focus = "none";
	//}
	
	
	// Verify our database connection link
	// If it isn't setup, set it up and select 
	// the desired database to work from, being "metawarehouse"
	if(!ISSET($db_connection->link)){
		$db_connection->setupDbLink();
		/** The select database results */
		$x = $db_connection->selectDb("$mysql_database_name");
	}
	
	/** Create a QueryRunner to run queries on the database */
	$query_runner 	= new QueryRunner();
	/** Create a UtilityObject */
	$utility 		= new UtilityObject();
	
	/** Setup the session */
	commonSessionSetup();
	
	/** Create a new graph object using the query runner to execute database calls
	* set the up and down flags for neighbors, and set the direction of the graph
	*/
	$temp_graph_direction = $_SESSION["$url_rest_custom_image_graph_direction"];
	$temp_arrow_direction = $_SESSION["$url_rest_custom_image_arrow_direction"];
	
	$g 	= new GraphObject($query_runner,true,true,1,$temp_graph_direction,$temp_arrow_direction);
	
	// Walk the graph given the node name as the root of the graph
	$g->walk($node_name);
	/** The attributes of the root node to show */
	$rootAttributes = $g->getRootNodeAttributes();
	$rootCategory 	= $g->getRootCategory();
	//echo "Root Name:	$node_name<br />";
	//echo "Root Category: $rootCategory<br />";
	
	//if($focus == "image" or $focus == "none"){
		// Fetch the graphviz directed graph string 
		if($_SESSION[$url_rest_custom_image_font_size] == "L"){
			/** The graphviz string LARGE */
			$graph =  $g->getGraphvizSring($fontsize="14");
		} else {
			/** The graphviz string */
			$graph 	=  $g->getGraphvizSring();
		}
		
		/** Generate a MD5 checksum against the graph graphviz string */
		$checksum 	= md5($graph);
		//echo $checksum."<br />";
		/** Escape the checksum value to be used in the shell */
		$escaped 	= escapeshellarg($checksum);
		/** Setup the file directories */
		$result_setup = $utility->setupFileDirectories();
		
		if(!$result_setup){
			echo "Problem creating needed directories to store files, check permissions.";
			exit(-1);
		}
		
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
			fwrite($handle,$graph);
			fclose($handle);
		} 
		
		if(!$utility->checkFile("$map_file",$filesystem_age_time)){
			if(!$utility->checkFile("$img_file",$filesystem_age_time)){
				$mapCmd	= "$command_executable_dot -Tcmap -o$map_file -T$graph_default_image_format -o$img_file $dot_file";
				exec($mapCmd,$output,$ret);
				//echo $mapCmd;
				
			}
		} 

		/** Read the contents of the map file */
		$map_contents 	= file_get_contents("$map_file");
		/** Clean the map file slashes */
		$cleanMap 		= ereg_replace('\\\"','"',$map_contents);
		
	//}
	/** Add the document header */
	echo commonHtmlPageHead($node_name);
	/** Add the page header */
	echo commonHtmlPageHeader($node_name);
	
	//echo createNodeColorLegendTable();
	
	/** Start the main table */
	echo "<table class=\"main\">";
	/** Headings for the main table */
	
	echo "<tr>";
	
	/** The image heading */
	echo "<th class=\"image_side_head\">";
	//if($focus == "image" or $focus == "none"){
	echo "<div class=\"section_heading\">Relationships of ". $utility->parsePathBaseName($node_name) . "</div>\n";
	//} else {
		//echo "<div class=\"section_heading\">Attributes of ". $utility->parsePathBaseName($node_name). "</div>\n";
	//}
	echo "</th>";
	/** The attribute head */
	//echo "<th class=\"attribute_side_head\">";
	//echo "<div class=\"section_heading\">Options</div>\n</th>";
	echo "</tr>";
	/** Done with the main headings */
	echo "<tr>";
	
	/** The left side contains the image */
	echo "<td class=\"image_side_content\">";
	//if($focus == "image" or $focus == "none"){	
		echo "<table width=\"99%\">";
		echo "<tr>";
		// legend
		echo "<td width=\"30%\" style=\"vertical-align:top;\">" . createNodeColorLegendTable($rootCategory) . "";
		// attributes
		echo "<br /><br />". createAttributeTableHtml($rootAttributes);

		echo "<br />". createExtraOptions($node_name,$focus). "</td>";
		//echo "<div id=\"scroll-legend\" style=\"position:absolute;\" >";
		
		// image
		echo "<td><center><img src=\"$img_url\" alt=\"Model\" ";
		echo "class=\"model\" usemap=\"#$img_url\" border=\"0\"></center>\n";
		echo "<map name=\"$img_url\">\n";
		echo $cleanMap;
		echo "</map></td>\n";
		
		
		//echo "</div>";
		echo "</tr></table>";
	//} else {
		//echo createAttributeTableHtml($rootAttributes);
	//}
	echo "</td>";
	/** Done with left side */
	
	//echo "<td class=\"attribute_side_content\">";
	
	//echo createExtraOptions($node_name,$focus);
	echo "</td>";//"</tr>";
	/** Done with the right side */
	/** Close the table element */
	echo "</table>\n";
	
	echo "<br />";
	/** Show the page footer */
	echo commonHtmlPageFooter();
	
	/** the usage watcher */
	$watcher = new UsageObject();
	$bind_id = $watcher->bindIpToObject($_SERVER["REMOTE_ADDR"],$node_name);
	/** Close the database link if it is still open */
	if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 

?>
