<?php 

	include_once('SettingsDatabase.php');
	include_once('Database.php');
	
	/** A usage object
	* Makes it easier to track what people are doing 
	*/
	class UsageObject{
	
		private $connection 	= null;
		private $database		= null;
		private $query_runner 	= null;
		
		/**
		* Construct a new usage object 
		*/
		public function UsageObject(){
			global $mysql_database_host;
			global $mysql_database_import_user;
			global $mysql_database_import_passwd;
			global $mysql_database_name;
			
			$this->connection = new DbConnectionHandler("$mysql_database_host",
				"$mysql_database_import_user",
				"$mysql_database_import_passwd"
			);
			
			if(!ISSET($this->connection->link)){
				$this->connection->setupDbLink();
				$this->database = $this->connection->selectDb("$mysql_database_name");
			}
			
			$this->query_runner 	= new QueryRunner();
		
		}
		
		public function lookupIpAddress($address){
			$escaped = mysql_real_escape_string($address);
			$find 		= "select id from usage_ip_log where ip_address = '$escaped' limit 1";
			$find_res 	= $this->query_runner->runQuery($find);
			$line 		= mysql_fetch_array($find_res ,MYSQL_ASSOC);
			if(isset($line["id"])){
				$id 		= $line["id"];
				mysql_free_result($find_res);
				return $id;
			} else {
				return False;
			}
			
		}
		
		/** 
		* Log an IP address
		* @param $address the ip address to capture
		*/
		private function logIpAddress($address){
			$escaped = mysql_real_escape_string($address);
			$sql 	= "insert ignore into usage_ip_log (`ip_address`) values (\"$escaped\");";
			$res 	= $this->query_runner->runQuery($sql);
			$id 	= mysql_insert_id($this->connection->getDbLink());
			if($id == 0){
				$id = $this->lookupIpAddress($address);
			}
			
			return $id;
		}
		
		/**
		* Log a search query 
		* @param $text the search text to log
		*/
		private function logSearchText($text){
			$escaped = mysql_real_escape_string($text);
			$sql 	= "insert ignore into usage_search_log (`search_text`) values (\"$escaped\");";
			$res 	= $this->query_runner->runQuery($sql);
			$id 	= mysql_insert_id($this->connection->getDbLink());
			if($id == 0){
				$find 		= "select id from usage_search_log where search_text = '$escaped' limit 1";
				$find_res 	= $this->query_runner->runQuery($find);
				$line 		= mysql_fetch_array($find_res ,MYSQL_ASSOC);
				$id 		= $line["id"];
				mysql_free_result($find_res);
			}
			
			return $id;
		}
		
		public function bindIpToSearch($address,$text){
			$ip_id = $this->logIpAddress($address);
			$se_id = $this->logSearchText($text);
			//echo "$ip_id,$se_id<br />";
			$sql = "insert ignore into usage_assoc_ip_search (`ip_id`,`search_id`) values ($ip_id,$se_id);";
			//echo "$sql<br />";
			$res 	= $this->query_runner->runQuery($sql);
			$id 	= mysql_insert_id($this->connection->getDbLink());
			if($id == 0){
				$find 		= "select id from usage_assoc_ip_search where ip_id=$ip_id and search_id=$se_id limit 1";
				$find_res 	= $this->query_runner->runQuery($find);
				$line 		= mysql_fetch_array($find_res ,MYSQL_ASSOC);
				$id 		= $line["id"];
				mysql_free_result($find_res);
			}
			
			return $id;
		}
		
		public function bindIpToObject($address,$node){
			$ip_id = $this->logIpAddress($address);
			$escaped = mysql_real_escape_string($node);
			$find_object = "select id from object where name = \"$escaped\" limit 1;";
			$find_res 	= $this->query_runner->runQuery($find_object);
			$line 		= mysql_fetch_array($find_res ,MYSQL_ASSOC);
			
			if(isset($line["id"])){
				$object_id 	= $line["id"];
			} else {
				return False;
			}
			mysql_free_result($find_res);
			
			$sql = "insert ignore into usage_assoc_ip_object (`ip_id`,`object_id`) values ($ip_id,$object_id);";
			$res 	= $this->query_runner->runQuery($sql);
			$id 	= mysql_insert_id($this->connection->getDbLink());
			
			if($id == 0){
				$find 		= "select id from usage_assoc_ip_object where ip_id=$ip_id and object_id=$object_id limit 1";
				$find_res 	= $this->query_runner->runQuery($find);
				$line 		= mysql_fetch_array($find_res ,MYSQL_ASSOC);
				$id 		= $line["id"];
				mysql_free_result($find_res);
			}
			
			return $id;
			
		}
		
		public function countIpAddresses(){
			$sql 	= "select count(id) as count from usage_ip_log";
			$res 	= $this->query_runner->runQuery($sql);
			$line 	= mysql_fetch_array($res ,MYSQL_ASSOC);
			$count 	= $line["count"];
			mysql_free_result($res);
			
			return $count;
		}
		
		
		public function countSearches(){
			$sql 	= "select count(id) as count from usage_search_log";
			$res 	= $this->query_runner->runQuery($sql);
			$line 	= mysql_fetch_array($res ,MYSQL_ASSOC);
			$count 	= $line["count"];
			mysql_free_result($res);
			
			return $count;
		}
		
		public function countBoundSearches(){
			$sql 	= "select count(id) as count from usage_assoc_ip_search";
			$res 	= $this->query_runner->runQuery($sql);
			$line 	= mysql_fetch_array($res ,MYSQL_ASSOC);
			$count 	= $line["count"];
			mysql_free_result($res);
			
			return $count;
		}
		
		public function countBoundObjects(){
			$sql 	= "select count(id) as count from usage_assoc_ip_object";
			$res 	= $this->query_runner->runQuery($sql);
			$line 	= mysql_fetch_array($res ,MYSQL_ASSOC);
			$count 	= $line["count"];
			mysql_free_result($res);
			
			return $count;
		}
		
		public function countAttributes(){
			$sql = "select count(id) as count from attribute";
			$res 	= $this->query_runner->runQuery($sql);
			$line 	= mysql_fetch_array($res ,MYSQL_ASSOC);
			$count 	= $line["count"];
			mysql_free_result($res);
			
			return $count;
		}
		
		public function countObjects(){
			$sql = "select count(id) as count from object";
			$res 	= $this->query_runner->runQuery($sql);
			$line 	= mysql_fetch_array($res ,MYSQL_ASSOC);
			$count 	= $line["count"];
			mysql_free_result($res);
			
			return $count;
		}
		
		public function countRelationships(){
			$sql = "select count(id) as count from relationship";
			$res 	= $this->query_runner->runQuery($sql);
			$line 	= mysql_fetch_array($res ,MYSQL_ASSOC);
			$count 	= $line["count"];
			mysql_free_result($res);
			
			return $count;
		}
		
		public function recentUsers($limit="10"){
			$escaped = mysql_real_escape_string($limit);
			$sql  	= "select ip_address from usage_ip_log order by id desc limit $escaped";
			$res 	= $this->query_runner->runQuery($sql);
			
			$users 	= array();
			
			while($line 	= mysql_fetch_array($res ,MYSQL_ASSOC)){
				array_push($users,$line["ip_address"]);
			}
			mysql_free_result($res);
			
			return $users;
		}
		
		public function recentSearches($limit="10"){
			$escaped = mysql_real_escape_string($limit);
			$sql 	= "select search_text from usage_search_log order by id desc limit $escaped";
			$res 	= $this->query_runner->runQuery($sql);
			
			$searches = array();
			
			while($line 	= mysql_fetch_array($res ,MYSQL_ASSOC)){
				array_push($searches,$line["search_text"]);
			}
			mysql_free_result($res);
			
			return $searches;
		}
		
		public function recentUserSearches($ip_address,$limit="10"){
			$escaped = mysql_real_escape_string($ip_address);
			$es_limit = mysql_real_escape_string($limit);
			
			$sql = "select se.search_text from usage_search_log as se, usage_ip_log as ip, usage_assoc_ip_search as assoc
			where se.id = assoc.search_id and ip.id = assoc.ip_id and ip.ip_address = '$escaped' group by se.id order by assoc.id desc limit $es_limit";
			$res = $this->query_runner->runQuery($sql);
			
			$search = array();
			
			while($line = mysql_fetch_array($res ,MYSQL_ASSOC)){
				array_push($search,$line["search_text"]);
			}
			mysql_free_result($res);
			return $search;
		}
		
		public function recentNavigated($limit="10"){
			$escaped = mysql_real_escape_string($limit);
			$sql 	= "select o.name from usage_assoc_ip_object as u,
				object as o where o.id = u.object_id 
				group by u.object_id order by u.id desc limit $escaped";
			$res 	= $this->query_runner->runQuery($sql);
			
			$objects = array();
			
			while($line 	= mysql_fetch_array($res ,MYSQL_ASSOC)){
				array_push($objects,$line["name"]);
			}
			mysql_free_result($res);
			
			
			return $objects;
		}
		
		public function recentUserNavigated($ip_address,$limit="10"){
			$escaped = mysql_real_escape_string($ip_address);
			$es_limit = mysql_real_escape_string($limit);
			
			$sql = "select o.name from object as o, usage_ip_log as ip, usage_assoc_ip_object as assoc
			where o.id = assoc.object_id and ip.id = assoc.ip_id and ip.ip_address = '$escaped' 
			group by o.id order by assoc.id desc limit $es_limit";
			$res = $this->query_runner->runQuery($sql);
			
			$navigate = array();
			
			while($line = mysql_fetch_array($res ,MYSQL_ASSOC)){
				array_push($navigate,$line["name"]);
			}
			mysql_free_result($res);
			return $navigate;
		}
		
		/** 
		* Deconstruct this object 
		*/
		public function __destruct() {
			if($this->connection->getDbLink() and $this->database){ 
				$this->connection->closeLink();
			} 
		}
	}

?>