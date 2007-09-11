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
	
	
	// Check the URL for a "node" parameter and set the $node_name
	if(isset($_GET["$url_rest_node_param"])){
		/** Set the node name to be used throughout this navigation */
		$node_name = $_GET["$url_rest_node_param"];
	}
	
	// If the $node_name is not set, we didn't catch it in the URL, set the default
	// Default is the subject area
	if(!isset($node_name)){
		/** Set the node name to be used throughout this navigation */
		header('Location: Index.php');
	}

	/** Setup the session */
	commonSessionSetup();
	
	/** Create a new graph object using the query runner to execute database calls
	* set the up and down flags for neighbors, and set the direction of the graph
	*/
	/*$temp_graph_direction 	= $_SESSION["$url_rest_custom_image_graph_direction"];
	$temp_arrow_direction 	= $_SESSION["$url_rest_custom_image_arrow_direction"];
	$temp_graph_levels		= $_SESSION["$url_rest_custom_image_graph_levels"] + 0;
	$temp_neighbor_limit   	= $_SESSION["$url_rest_custom_image_graph_neighbors"] + 0;
	*/
	
	/** Add the document header */
	echo commonHtmlPageHead($node_name);
	/** Add the page header */
	echo commonHtmlPageHeader($node_name);
	
	/** Start the main table */
	echo "<table class=\"main\">";
	/** Headings for the main table */
	
	echo "<tr>";
	
	/** The image heading */
	$utility = new UtilityObject();
	echo "<th class=\"image_side_head\">";
	echo "<div class=\"section_heading\">Relationships of ". $utility->parsePathBaseName($node_name);
	echo "&nbsp;<a href=\"#\" onClick=\"popup('Customize.php');\"><small>(Customize)</small></a>". "</div>\n";
	echo "</th>";	
	echo "</tr>";
	/** Done with the main headings */
	echo "<tr>";
	
	/** The left side contains the image */
	echo "<td class=\"image_side_content\">";
	echo "<table width=\"99%\">";
	echo "<tr>";
	// legend
	$url_legend_export = "http://localhost/rati/Export.php?q=".urlencode($node_name)."&type=legend".commonUrlCustomizationValues();
	$legend_export = file_get_contents($url_legend_export);
	echo "<td width=\"20%\" style=\"vertical-align:top;\">" . $legend_export . "<br />";
	// attributes
	$url_attributes_export = "http://localhost/rati/Export.php?q=".urlencode($node_name)."&type=attributes".commonUrlCustomizationValues();
	$attributes_export = file_get_contents($url_attributes_export);
	echo "$attributes_export";
	// extra options
	echo "<br />". createExtraOptions($node_name). "</td>";
	// image
	$url_html_export = "http://csc06pocdvpa01s.keybank.com/rati/Export.php?q=".urlencode($node_name)."&amp;type=html".commonUrlCustomizationValues($for_html=True);
	//$html_export = file_get_contents($url_html_export);
	//echo "<td>$html_export</td>\n";
	echo "<td><iframe name=\"html\" src=\"$url_html_export\" width=\"100%\" height=\"450\" frameborder=\"0\"></iframe></td>\n";
	echo "</tr></table>";
	
	//echo "</td>";
	/** Done with left side */
	//echo "</td>";//"</tr>";
	/** Done with the right side */
	/** Close the table element */
	echo "</table>\n";
	echo "<br />";
	/** Show the page footer */
	//print_r($_SESSION);
	echo commonHtmlPageFooter();
	
	/** Setup the database connection, provide the host, username and password */
	$db_connection 	= new DbConnectionHandler("$mysql_database_host",
		"$mysql_database_user",
		"$mysql_database_passwd"
	);
	
	if(!ISSET($db_connection->link)){
		$db_connection->setupDbLink();
		/** The select database results */
		$x = $db_connection->selectDb("$mysql_database_name");
	}
	
	/** the usage watcher */
	$watcher = new UsageObject();
	$bind_id = $watcher->bindIpToObject($_SERVER["REMOTE_ADDR"],$node_name);
	/** Close the database link if it is still open */
	if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 

?>
