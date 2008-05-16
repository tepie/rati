<?php
	
	include_once('Include/SettingsDatabase.php');
	include_once('Include/Database.php');
	include_once('Include/SQLQueries.php');
	include_once('Include/SQLSearch.php');
	
	function initiate_load(){
		global $query_runner;
		global $database_start_transaction;
		global $database_disable_autocommit;
		$query_runner->runQuery("$database_disable_autocommit");
		$query_runner->runQuery("$database_start_transaction");
	}
	
	function finalize_load(){
		global $query_runner;
		global $database_commit_transaction;
		$query_runner->runQuery($database_commit_transaction);
	}
	
	function optimize_tables(){
		global $query_runner;
		global $database_analyze_attribute;
		global $database_analyze_object;
		global $database_analyze_relationship;
		global $database_analyze_search_index;
		
		/** Analyze the tables */
		$query_runner->runQuery($database_analyze_attribute);
		$query_runner->runQuery($database_analyze_object);
		$query_runner->runQuery($database_analyze_relationship);
		$query_runner->runQuery($database_analyze_search_index);
	}
	
	function object_exists_prefix($object_prefix){
		global $query_runner;
		global $object_calculate_node_id;
		global $object_structure_primary_id;
		global $all_structure_as_count;
		global $object_structure_name;
		global $object_structure_table_name;
		
		$escaped 	= mysql_real_escape_string($object_prefix);
		//$check_sql 	= sprintf($object_calculate_node_id,$escaped); //"select id from `object` where name = \"$escaped\" limit 1";
		$check_sql  = "select count($object_structure_primary_id) as $all_structure_as_count from $object_structure_table_name where $object_structure_name	 like('$escaped%')";
		$res 		= $query_runner->runQuery($check_sql);
		$line		= mysql_fetch_array($res ,MYSQL_ASSOC);
		
		mysql_free_result($res);
		
		if(isset($line["$all_structure_as_count"])){
			return $line["$all_structure_as_count"] + 0;
		} else {
			return null;
		}
	}
	
	/**
	* Check to see if an object already exists 
	* @param $object_name the name of the object you want to check
	* return the id of the object if it exists, or null if it doesn't
	*/
	function object_exists_direct($object_name){
		global $query_runner;
		global $object_calculate_node_id;
		global $object_structure_primary_id ;
		
		$escaped 	= mysql_real_escape_string($object_name);
		$check_sql 	= sprintf($object_calculate_node_id,$escaped); //"select id from `object` where name = \"$escaped\" limit 1";
		$res 		= $query_runner->runQuery($check_sql);
		$line		= mysql_fetch_array($res ,MYSQL_ASSOC);
		
		mysql_free_result($res);
		
		if(isset($line[$object_structure_primary_id])){
			return $line[$object_structure_primary_id] + 0;
		} else {
			return null;
		}
	}
	
	/**
	* Delete an object's relationships
	* @param $object_id the object id to based the relationship deletions on
	*/
	function delete_object_relationships_prefix($object_prefix){
		global $query_runner;
		global $relationship_delete_on_object_id;
		/** Escape the input */
		$escaped 	= mysql_real_escape_string($object_prefix);
		/** Generate the sql */
		//$delete_sql = sprintf($relationship_delete_on_object_id,$escaped);//"delete from `relationship` where object_id = $escaped";
		$delete_sql = "delete relationship from relationship,object 
			where relationship.object_id = object.id and object.name like ('$escaped%')";
		/** Execute the delete */
		$res 		= $query_runner->runQuery($delete_sql);
		/** Return the number of affected rows */
		return mysql_affected_rows();
	}
	
	function delete_object_relationships_direct($object_id){
		global $query_runner;
		global $relationship_delete_on_object_id;
		/** Escape the input */
		$escaped 	= mysql_real_escape_string($object_id) + 0;
		/** Generate the sql */
		$delete_sql = sprintf($relationship_delete_on_object_id,$escaped);//"delete from `relationship` where object_id = $escaped";
		/** Execute the delete */
		$res 		= $query_runner->runQuery($delete_sql);
		/** Return the number of affected rows */
		return mysql_affected_rows();
	}
	
	function delete_object_direct($object_id){
		global $query_runner;
		$escaped 	= mysql_real_escape_string($object_id) + 0;
		$delete_sql = "delete from `object` where id = $escaped";
		$res 		= $query_runner->runQuery($delete_sql);
		return mysql_affected_rows();
	}
	
	function delete_object_prefix($object_prefix){
		global $query_runner;
		$escaped 	= mysql_real_escape_string($object_prefix);
		$delete_sql = "delete from `object` where name like ('$escaped%')";
		$res 		= $query_runner->runQuery($delete_sql);
		return mysql_affected_rows();
	}

?>