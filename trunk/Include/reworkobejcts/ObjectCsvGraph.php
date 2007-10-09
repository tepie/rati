<?php
	
	include_once('ObjectAbstractGraph.php');
	include_once('SettingsDatabase.php');
	include_once('SettingsGraph.php');
	include_once('SQLQueries.php');
	
	
	class GraphCsv extends AbstractGraph{
	
		public function GraphCsv($runner,$up,$down){
			parent::AbstractGraph($runner,$up,$down);
		}
		
		public function walk($node_name,$neighbor_limit){
			global $mysql_database_neighbor_limit;
			global $directory_dot_graph;
			
			// Create a new node object using this query runner and node name
			if($neighbor_limit != null){
				//echo $neighbor_limit;
				$mysql_database_neighbor_limit = $neighbor_limit;
				//$node 	= new NodeObject($this->query_runner,$node_name,$neighbor_limit);
			}
		}
	}
	
	$x = new GraphCsv(null,true,true,100);
	echo $x->getUpFlag();
	echo $x->getLimit();


?>