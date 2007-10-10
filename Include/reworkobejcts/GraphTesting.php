<?php

	include_once('../Database.php');
	include_once('../SettingsDatabase.php');
	include_once('ObjectNodes.php');
	include_once('ObjectGraphs.php');
	
	
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
	
	$node_name 		= "/galileo/Metadata/Souce Structures/CLS.P.NR0**00.CLEXT003.MULT.EXT(0)/ABL_NBR_THIS_CYCLE";
	$query_runner 	= new QueryRunner();
	$csvnode 		= new NodeCsv($query_runner,10);
	$csvgraph 		= new GraphCsv($query_runner,1);
	
	$csvnode->setNodeName($node_name);
	$csvgraph->walk($csvnode);
	
	echo $csvgraph;
	
	//echo $node;
		
	/*$graph 	= new GraphBasic($query_runner,1);
	
	$graph->walk($node);
	
	echo "graph: $graph\n";
	*/
	
	
	
	if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 

?>