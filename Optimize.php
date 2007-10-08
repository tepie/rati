<?php

	include_once('Include/Database.php');
	include_once('Include/DatabaseCommon.php');
	include_once('Include/SettingsDatabase.php');
	
	if(!($argv[0] == $_SERVER['PHP_SELF'])){
		die("This is a command line script used to optimize tables. Since the execution takes a bit, it will bomb out in the
		browser.");
	}
	
	/** Setup the database connection, provide the host, username and password */
	$db_connection 	= new DbConnectionHandler("$mysql_database_host",
		"$mysql_database_import_user",
		"$mysql_database_import_passwd"
	);
	
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
	initiate_load();
	
	optimize_tables();
	
	finalize_load();
	if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 

?>