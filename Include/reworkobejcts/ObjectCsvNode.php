<?php
	
	include_once('ObjectAbstractNode.php');
	include_once('Database.php');
	include_once('SettingsDatabase.php');
	
	class NodeCsv extends AbstractNode{
		
		public function NodeCsv($runner,$node_name,$neighbor_limit,$up,$down){
			parent::AbstractNode($runner,$node_name,$neighbor_limit,$up,$down);
		}
		
		public function __toString(){
			return  $this->getNodeName();
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
	
	$query_runner 	= new QueryRunner();
	$x 				= new NodeCsv($query_runner ,"/galileo/Metadata/Categories/category",10,true,true);
	echo  $x;
	if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 

?>