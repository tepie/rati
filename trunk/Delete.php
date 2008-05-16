<?php
	set_time_limit(60);
	
	include_once('Include/SettingsWebApp.php');
	include_once('Include/SettingsDatabase.php');
	include_once('Include/Database.php');
	include_once('Include/DatabaseCommon.php');
	include_once('Include/SQLQueries.php');
	
	$valid_deletion_types = array("direct","prefix");
	
	header("Content-type: application/xml");
	
	echo "<delete>";
	
	if(isset($_GET["$url_rest_node_param"])){
		$delete_target = $_GET["$url_rest_node_param"];
		echo "<target>$delete_target</target>";
	} else {
		die("<error>Delete target is not set, set using \"$url_rest_node_param\" parameter</error></delete>");
	}
	
	if(isset($_GET["type"])){
		if(in_array($_GET["type"],$valid_deletion_types)){
			$delete_type = $_GET["type"];
			echo "<type>$delete_type</type>";
		} else {
			die("<error>Delete type is not a valid option</error></delete>");
		}
	} else {
		die("<error>Delete type is not set, set using \"type\" parameter</error></delete>");
	}
	
	if(isset($_GET["$url_rest_user_name_security"])){
		$user_name = $_GET["$url_rest_user_name_security"];
	} else {
		die("<error>User name not give, use \"$url_rest_user_name_security\" parameter</error></delete>");
	}
	
	if(isset($_GET["$url_rest_user_passwd_security"])){
		$user_passwd = $_GET["$url_rest_user_passwd_security"];
	} else {
		die("<error>User name not give, use \"$url_rest_user_passwd_security\" parameter</error></delete>");
	}
	
	/** Setup the database connection, provide the host, username and password */
	/*$db_connection 	= new DbConnectionHandler("$mysql_database_host",
		"$mysql_database_import_user",
		"$mysql_database_import_passwd"
	);*/
	
	$db_connection 	= new DbConnectionHandler("$mysql_database_host",
		"$user_name",
		"$user_passwd"
	);
	
	// Verify our database connection link
	// If it isn't setup, set it up and select 
	// the desired database to work from, being "metawarehouse"
	if(!ISSET($db_connection->link)){
		$db_connection->setupDbLink();
		/** The select database results */
		$x = $db_connection->selectDb("$mysql_database_name");
	} 
	
	if($db_connection->getDbLink() == null){
		die("<error>Not connected! Check user name and password!</error></delete>");
	}
	
	/** Create a QueryRunner to run queries on the database */
	$query_runner 	= new QueryRunner();
	initiate_load();
	if($delete_type == "direct"){
		$existence = object_exists_direct($delete_target);
		//echo "Existence check result: \"$existence\"";
		if($existence != null){	
			$deleted_relationships 	= delete_object_relationships_direct($existence);
			echo "<relationships-deleted-direct>$deleted_relationships</relationships-deleted-direct>";
			$deleted_objects		= delete_object_direct($existence);
			echo "<objects-deleted-direct>$deleted_objects</objects-deleted-direct>";
			//echo "Existence check result: \"$existence\"";
		} else{
			die("<error>\"$delete_target\" does not exist as an object</error></delete>");
		}	
	} elseif($delete_type == "prefix"){
		$existence = object_exists_prefix($delete_target);
		//echo "Existence check result: \"$existence\"";
		if($existence != null and $existence > 0){
			//echo "\"$delete_target\" prefixes $existence objects";
			$deleted_relationships = delete_object_relationships_prefix($delete_target);
			echo "<relationships-deleted-prefix>$deleted_relationships</relationships-deleted-prefix>";
			$deleted_objects = delete_object_prefix($delete_target);
			echo "<objects-deleted-prefix>$deleted_objects</objects-deleted-prefix>";
		} else {
			die("<error>\"$delete_target\" does not prefix any objects</error></delete>");
		}
	} else {
		die("<error>Delete type is not a valid option. By the way, you should have not gotten here</error></delete>");
	}
	
	finalize_load();
	if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 
	echo "</delete>";
?>