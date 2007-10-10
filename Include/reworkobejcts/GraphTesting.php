<?php

	include_once('../Database.php');
	include_once('../SettingsDatabase.php');
	include_once('ObjectNodes.php');
	include_once('ObjectGraphs.php');
	include_once('ObjectNodeDot.php');
	include_once('ObjectGraphDot.php');
	
	
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
	
	$node_name 		= "/galileo/Metadata/Elements/PAYMENT_ASSET_BASED_LOAN_PROCESSED_THIS_CYCLE";
	$query_runner 	= new QueryRunner();
	//$csvnode 		= new NodeCsv($query_runner,10);
	//$csvgraph 		= new GraphCsv($query_runner,1);
	
	//$csvnode->setNodeName($node_name);
	//$csvgraph->walk($csvnode);
	
	//echo $csvgraph;
	//$xmlgraph 	= new GraphXml($query_runner,1);
	//$xmlnode 	= new NodeXml($query_runner,10);
	
	//$xmlnode->setNodeName($node_name);
	//$xmlgraph->walk($xmlnode);
	//echo $xmlnode;
	
	//echo $xmlgraph;
	
	$dotnode = new NodeDot($query_runner,10,true,true);
	$dotgraph = new GraphDot($query_runner,1);
	
	$dotnode->setNodeName($node_name);
	
	$dotgraph->walk($dotnode);
	
	echo $dotgraph;
	//echo $dotnode;
	
	
	if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 

?>