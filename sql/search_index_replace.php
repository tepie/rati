<?php
	
	/** Replace the search index */
	
	define("SEARCH_INDEXING",true);
	
	include_once('../Include/SettingsDatabase.php');
	include_once('../Include/Database.php');
	include_once('../Include/SQLQueries.php');
	include_once('../Include/SQLSearch.php');
	
	/** This should outter join to remove old items in the search index that are no longer in the
	* object table 
	*/
	function remove_old_objects(){
		global $query_runner;
		global $search_index_delete_search_index;
		$sql = "$search_index_delete_search_index";
		$res = $query_runner->runQuery($sql);
	}
	
	function insert_select_all_objects_plus_combine_relationships(){
		global $query_runner;
		global $search_index_left_union;
		global $search_index_right_union;
		
		$sql = "insert into search_index (object_name,combined_attributes,rank)
select results.object,
group_concat(
concat_ws(\"=\",results.attribute,results.result) separator '; '
) 
as rule, sum(results.count) as count
from 
($search_index_right_union) 
as results group by results.object";
		
		$res = $query_runner->runQuery($sql);
		
	}
	
	function get_current_combined_attributes($object_name){
		global $query_runner;
		
		$sql = "select combined_attributes,rank from search_index where object_name = \"" . mysql_escape_string($object_name) . "\" limit 1";
		$res = $query_runner->runQuery($sql);
		$line = mysql_fetch_array($res,MYSQL_ASSOC);
		
		//$return_value = $line["combined_attributes"];
		
		mysql_free_result($res);
		//return $return_value;
		return $line;
	}
	
	function set_current_combined_attributes($object_name,$combined,$rank){
		global $query_runner;

		$sql = "update search_index set combined_attributes=\"" . 
			mysql_escape_string($combined) . 
			"\", rank=".mysql_escape_string("$rank") . 
			" where object_name = \"" . 
			mysql_escape_string($object_name) .
			"\" limit 1";
			
		$res = $query_runner->runQuery($sql);
	}
	
	function concat_object_attributes($select_sql,$update_rank=false){
		global $query_runner;
		
		$selected_results = $query_runner->runQuery($select_sql);
		
		while($line	= mysql_fetch_array($selected_results ,MYSQL_ASSOC)){
			$object_name = $line["object"];
			$attribue_name = $line["attribute"];
			$attribute_value = $line["result"];
		
			$current_index_line = get_current_combined_attributes($object_name);
			
			$current_concat_value = $current_index_line["combined_attributes"];
			$current_concat_value .= "$attribue_name=$attribute_value;";
			
			$current_index_rank = $current_index_line["rank"];
			if($update_rank){
				$current_index_rank++;
			}
			set_current_combined_attributes($object_name,$current_concat_value,"$current_index_rank");
		}
		
		mysql_free_result($selected_results);
	}
	
	/** 
	* Perform the needed SQL to replace the current search index on the table 
	*/
	/*function replace_search_index(){
		global $query_runner;
		global $search_index_replace;
		global $search_index_left_union;
		global $search_index_right_union;
		
		// combine the text based attributes
		//echo "Search index replace: building left side...\n";
		//concat_object_attributes($search_index_left_union,$update_rank=false);
		// combine the reference attributes and determine rank
		//echo "Search index replace: building right side...\n";
		//concat_object_attributes($search_index_right_union,$inital_replace=false,$update_rank=true);
		
		//$sql = $search_index_replace;
		//$res = $query_runner->runQuery($sql);
	}*/
	
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
	/** Set the max length for the group concat results */
	$query_runner->runQuery("$search_index_set_concat_max");
	
	/** Delete the contents of the search table */
	echo "Search index replace: deleting all search index objects...\n";
	remove_old_objects();
	
	echo "Search index replace: selecting and inserting object and relationships...\n";
	insert_select_all_objects_plus_combine_relationships();
	
	echo "Search index replace: updating search index combined attributes with values...\n";
	concat_object_attributes($search_index_left_union,$update_rank=false);
	
	/** Replace the search index */
	//replace_search_index();
	
	echo "Search index replace: analyzing search index table...\n";
	$query_runner->runQuery("$database_analyze_search_index");
	
	/** Close the link */
	if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 

?>
