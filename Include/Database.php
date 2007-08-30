<?php
	
	/**
	* A connection handler class for databases
	*/
	class DbConnectionHandler{
		
		/** the database user */
		private $user;
		/** the database host name */
		private $host;
		/** the database user password */
		private $password;
		/** the database to work from */
		private $database;
		/** the database connection link */
		private $link;
		
		/**
		* Constructor
		* @param $host the database host name
		* @param $user the database user name
		* @param $password the database password for the $user
		*/
		public function DbConnectionHandler($host,$user,$password){
			$this->user = $user;
			$this->host = $host;
			$this->password = $password;
		}
		
		/**
		* Select the database to use for this handler
		* @param $database the name of the database to select 
		* return the result of selecting the database, die if could not select 
		*/
		public function selectDb($database){
			$this->database = $database;
			return mysql_select_db($database) 
				or die('Could not select database');
		}
		
		/**
		* Get the link object of this database handler
		* return the link
		*/ 
		public function getDbLink(){
			return $this->link;
		}
		
		/**
		* Close the link that this database is handling
		* return the result of closing the link
		*/
		public function closeLink(){
			return mysql_close($this->link);	
		}
		
		/** 
		* Setup the link to the database based on the arguments provided to the constructer of this handler
		*/ 
		public function setupDbLink(){
			if(!isset($this->link)){
				$this->link = mysql_connect($this->host,$this->user,$this->password)
					or die('Could not connect: ' . mysql_error());
			}
		}
		
	}
	
	/**
	* A class to run queries on a database
	*/
	class QueryRunner{
		/**
		* Construct the query runner 
		*/
		public function QueryRunner(){}
		
		/**
		* Run a query 
		* @param $query the query string to run
		* return the result of the query
		*/
		public function runQuery($query){
			$result = mysql_query($query) 
				or die("\nQuery failed: " . mysql_error() . " for query, \"$query\"");
			return $result;
		}
	}

?>