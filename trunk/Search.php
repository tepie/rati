<?php
	
	/** Provide user driven search functionality */
	
	include_once('Include/Database.php');
	include_once('Include/ObjectUtility.php');
	include_once('Include/ObjectSearch.php');
	include_once('Include/ObjectUsage.php');
	include_once('Include/SettingsWebApp.php');
	include_once('Include/SettingsDatabase.php');
	include_once('Include/HtmlCommon.php');
	
	/** Setup the database connection, provide the host, username and password */
	$db_connection 	= new DbConnectionHandler("$mysql_database_host",
		"$mysql_database_user",
		"$mysql_database_passwd"
	);
	
	// Verify our database connection link
	// If it isn't setup, set it up and select 
	// the desired database to work from, being "metawarehouse"
	if(!ISSET($db_connection->link)){
		$db_connection->setupDbLink();
		/** Select database result */
		$x = $db_connection->selectDb("$mysql_database_name");
	}
	
	if(isset($_GET["$url_rest_search_param"])){
		$q = urldecode($_GET["$url_rest_search_param"]);
	} else {
		$q = "";
	}
	
	if(isset($_GET["page"])){
		$page = abs($_GET["$url_rest_search_page"] + 0);
	} else {
		$page = 1;
	}
	
	$next_page = $page + 1;
	$show_total_results = 20;
	
	$upper_range = $show_total_results * $page;
	$lower_range = $upper_range - $show_total_results;
	
	//echo "pages: $page,$next_page<br />";
	//echo "range: $lower_range,$upper_range <br />";
	
	if($q == ""){	
		echo commonHtmlPageHead("Search"); 
		echo commonHtmlPageHeader(""); 
		/** Add the document header */
			
	}	else { 	
		echo commonHtmlPageHead("Search: ". htmlspecialchars($q) . " (Page " .  $page. ")"); 
		echo commonHtmlPageHeader(htmlspecialchars($q)); 
	}
		
	/** Create a new QueryRunner */	
	$query_runner 	= new QueryRunner();
	/** Create a new SearchObject using the QueryRunner */
	$searcher 		= new SearchObject($query_runner);
	
	
	/** The results of the search */
	//echo "range: $lower_range,$upper_range <br />";
	$search_results = $searcher->search($q,$lower_range,$upper_range);
	//$search_results = $searcher->search($q);
	echo "<br />" . $search_results;
	
	echo "<table class=\"search_forward_back\"><tr>";
	echo "<td class=\"search_back\">";
	if($page > 1){
		$back_one = $page - 1;
		echo "<a href=\"$web_app_page_search_name?$url_rest_search_param=".urlencode($q)."&amp;$url_rest_search_page=". $back_one ."\">Back</a></td>";
	} else {
		echo "&nbsp;";
	}
	echo "</td>";
	echo "<td class=\"search_forward\">";
	echo "<a href=\"$web_app_page_search_name?$url_rest_search_param=".urlencode($q)."&amp;$url_rest_search_page=". $next_page ."\">Next</a></td>";
	echo "</tr></table>";
	echo "<br />";
	echo commonHtmlPageFooter();
	
	/** the usage watcher */
	$watcher = new UsageObject();
	$bind_id = $watcher->bindIpToSearch($_SERVER["REMOTE_ADDR"],$q);
	//echo "BIND ID: $bind_id<br />";
	
	if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 

?>