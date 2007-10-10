<?php
	
	include_once('ObjectAbstractGraph.php');
	include_once('ObjectCsvNode.php');
	//include_once('SettingsDatabase.php');
	//include_once('SettingsGraph.php');
	//include_once('SQLQueries.php');
	
	include_once('../Database.php');
	include_once('../SettingsDatabase.php');
	
	
	class GraphCsv extends AbstractGraph{
	
		public function GraphCsv($runner,$up,$down,$node_limit,$neighbor_limit){
			parent::AbstractGraph($runner,$up,$down,$node_limit,$neighbor_limit);
		}
		
		public function __toString(){
			$text = "";
			foreach($this->getGraph() as $key => $value){
				$text = $text . "$key => $value\n";
			}
			
			return $text;
		}
	}
	
	
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
	
	$node_name 		= "/galileo/Metadata/Categories/category";
	$query_runner 	= new QueryRunner();
	$node 			= new NodeBasic($query_runner,10,true,true);
	$node->setNodeName($node_name);
	
	echo "root node: $node\n";
		
	$graph 	= new GraphCsv($query_runner,true,true,1,10);
	
	$graph->walk($node);
	
	echo "graph: $graph\n";
	
	if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 
	
	
	


?>