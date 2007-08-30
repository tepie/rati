<?php
	
	include_once('..\\Include\\SettingsDatabase.php');
	include_once('..\\Include\\Database.php');
	include_once('..\\Include\\SQLQueries.php');
	include_once('..\\Include\\SQLSearch.php');
	
	/** This should outter join to remove old items in the search index that are no longer in the
	* object table 
	*/
	function remove_old_objects(){
		global $query_runner;
	}
	
	/** 
	* Perform the needed SQL to replace the current search index on the table 
	*/
	function replace_search_index(){
		global $query_runner;
		global $search_index_replace;
		$sql = $search_index_replace;
		
		/*$sql = "replace into search_index (object_name,combined_attributes,rank)
			select results.object,group_concat(
				concat_ws(\"=\",results.attribute,results.result) separator '; '
			) as rule, sum(results.count) as count 
			from 
			(
				(select o.name as object,a.name as attribute,r.value as result,0 as count from relationship as r,
					object as o, attribute as a where o.id = r.object_id and a.id =r.attribute_id and 
					r.value is not null
				) union (select o.name as object,a.name as attribute,oo.name as result, 1 as count from 
					relationship as r,object as o, attribute as a,
					(select * from object) as oo where o.id = r.object_id and 
					a.id = r.attribute_id and r.value is null and r.reference = oo.id )
			) as results group by results.object;";
		*/
		
		
		
		$res = $query_runner->runQuery($sql);
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
	/** Start a transaction */
	$query_runner->runQuery("$database_start_transaction");
	/** Replace the search index */
	replace_search_index();
	/** Complete a transaction */
	$query_runner->runQuery("$database_commit_transaction");
	/** Close the link */
	if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 

?>