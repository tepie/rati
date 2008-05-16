<?php
	
	/** Apply the search index weights */
	
	include_once('../Include/SettingsDatabase.php');
	include_once('../Include/SettingsPerspectives.php');
	include_once('../Include/Database.php');
	include_once('../Include/SQLQueries.php');
	include_once('../Include/SQLSearch.php');
	
	/** Determine the max number of categories
	* For each perspective, determine how many categories it has
	* What ever category has the most, that count is returned.
	* return the max
	*/
	function determine_max_category_index(){
		global $perspective_names;
		global $perspective_node_color_maps;
		$max = 0;
		foreach($perspective_names as $index => $name){
			$cats = array_keys($perspective_node_color_maps[$index]);
			$count = count($cats);
			if($count > $max) $max = $count;
		}
		
		return $max;
	}
	
	/** Apply the category weights to the database 
	*/
	function apply_category_weights($max){
		global $query_runner;
		global $perspective_names;
		global $perspective_category_reference_rules;
		global $perspective_node_color_maps;
		global $search_index_select_category_for_weight;
		global $search_index_update_weight_value;
		global $object_structure_name;
		
		foreach($perspective_names as $index => $name){
			echo "Search index weight: applying weights to perspective \"$name\"\n";
			$cats 			= array_reverse(array_keys($perspective_node_color_maps[$index]));
			$reference_rule = $perspective_category_reference_rules[$index];
			
			$adjust = 0;
			if(count($cats) < $max){
				$adjust = $max - count($cats);
			}
			
			$sql_object_reference_pair = "select object.name,relationship.reference from relationship,object,attribute where attribute.name = '%s' and attribute.id = relationship.attribute_id and relationship.object_id = object.id and relationship.reference != 0";
			$sql_object_select_name_by_id = "select name from object where id = %s limit 1";
			
			$sql = sprintf($sql_object_reference_pair,$reference_rule);
			$res = $query_runner->runQuery($sql);
			
			while($line	= mysql_fetch_array($res ,MYSQL_ASSOC)){
				$object_name = $line["name"];
				$reference_id = $line["reference"];
				
				$sql_ref = sprintf($sql_object_select_name_by_id,$reference_id);
				$res_ref = $query_runner->runQuery($sql_ref);
				$line_ref	= mysql_fetch_array($res_ref,MYSQL_ASSOC);
				
				$refernece_name = $line_ref["name"];
				$object_category = $refernece_name;
				
				$obj_index = array_search($object_category,$cats);
				
				if($obj_index != FALSE){
					//echo "Search index weight: $object_name --> $object_category\n";
					$weight = $obj_index + $adjust;
					
					$update_sql = sprintf($search_index_update_weight_value,
						mysql_escape_string("$weight"),
						mysql_escape_string($name),
						mysql_escape_string($object_name));
					
					// you dont need to free an update result
					$update_res = $query_runner->runQuery($update_sql);
					
				} 
				
				mysql_free_result($res_ref);
				
			}
			
			mysql_free_result($res);
			
		}
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
	//$query_runner->runQuery("$database_disable_autocommit");
	//$query_runner->runQuery("$database_start_transaction");
	
	echo "Search index weight: determining max category weight...\n";
	$top_index = determine_max_category_index();
	echo "Search index weight: max category weight is \"$top_index\"\n";
	
	echo "Search index weight: applying category weight to all search index objects...\n";
	apply_category_weights($top_index);
	
	//$query_runner->runQuery("$database_commit_transaction");
	echo "Search index weight: analyzing search index table...\n";
	$query_runner->runQuery("$database_analyze_search_index");
	
	if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 
?>