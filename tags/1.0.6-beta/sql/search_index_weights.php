<?php

	include_once('..\\Include\\SettingsDatabase.php');
	include_once('..\\Include\\SettingsPerspectives.php');
	include_once('..\\Include\\Database.php');
	include_once('..\\Include\\SQLQueries.php');
	include_once('..\\Include\\SQLSearch.php');
		
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
	
	function apply_category_weights($max){
		global $query_runner;
		global $perspective_names;
		global $perspective_category_reference_rules;
		global $perspective_node_color_maps;
		global $search_index_select_category_for_weight;
		global $search_index_update_weight_value;
		global $object_structure_name;
		
		foreach($perspective_names as $index => $name){
			$cats 			= array_reverse(array_keys($perspective_node_color_maps[$index]));
			$reference_rule = $perspective_category_reference_rules[$index];
			
			$adjust = 0;
			if(count($cats) < $max){
				$adjust = $max - count($cats);
			}
			//echo $adjust."\n";
			//print_r($cats);
			//echo $reference_rule."\n";
			
			/*$sql = 'select obj_rel.name,object.name as category, i.rank from object,
search_index as i,
(select object.name,relationship.reference from relationship,
object,attribute where attribute.name = "'.$reference_rule.'"
and attribute.id = relationship.attribute_id and
relationship.object_id = object.id and
relationship.reference != 0) as obj_rel 
where obj_rel.reference = object.id and i.object_name = obj_rel.name';*/

			$sql = sprintf($search_index_select_category_for_weight,mysql_escape_string($reference_rule));
				
			//echo "$sql\n\n";
			$res = $query_runner->runQuery($sql);
			
			while($line	= mysql_fetch_array($res ,MYSQL_ASSOC)){
				foreach($cats as $obj_index => $obj_name){
					if($line["category"] == $obj_name){
						$weight = $obj_index + $adjust;
						//$update_sql = "update search_index set weight=$weight where object_name = '". $line["name"]."';";	
						$update_sql = sprintf($search_index_update_weight_value,mysql_escape_string("$weight"),mysql_escape_string($name),mysql_escape_string($line["$object_structure_name"]));
						$update_res = $query_runner->runQuery($update_sql);
						//echo $update_sql."\n";
						break;
					}
				}
			}
			
			mysql_free_result($res);
		}
	}
	
	/*function apply_sa_category_weight(){
		global $query_runner;
		global $web_app_sa_node_color_map;
		global $sa_category_reference_rule;
		//global $object_calculate_node_id;
		//global $object_structure_primary_id ;
		$cats = array_reverse(array_keys($web_app_sa_node_color_map));
		//$groups = array();
		
		$sql = 'select obj_rel.name,object.name as category, i.rank from object,
			search_index as i,
			(select object.name,relationship.reference from relationship,
			object,attribute where attribute.name = "'.$sa_category_reference_rule.'"
			and attribute.id = relationship.attribute_id and
			relationship.object_id = object.id and
			relationship.reference != 0) as obj_rel 
			where obj_rel.reference = object.id and i.object_name = obj_rel.name';
		
		$res 		= $query_runner->runQuery($sql);
		while($line	= mysql_fetch_array($res ,MYSQL_ASSOC)){
			foreach($cats as $index => $name){
				if($line["category"] == $name){
					$update_sql = "update search_index set weight=$index where object_name = '". $line["name"]."';";	
					$update_res = $query_runner->runQuery($update_sql);
					
					break;
				}
			}
		}
		
		mysql_free_result($res);
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
	$query_runner->runQuery("$database_disable_autocommit");
	$query_runner->runQuery("$database_start_transaction");
	
	$top_index = determine_max_category_index();
	
	apply_category_weights($top_index);
	
	$query_runner->runQuery("$database_commit_transaction");
	
	if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 
?>