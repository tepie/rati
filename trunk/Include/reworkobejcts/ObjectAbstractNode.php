<?php
	
	include_once('SettingsGraph.php');
	include_once('SQLQueries.php');
	
	abstract class AbstractNode{
		
		private $query_runner;
		private $neighbor_limit;
		private $node_name;
		private $node_category;
		private $node_id;
		private $flag_up;
		private $flag_down;
		private $neighbors;
		
		public function AbstractNode($runner,$node_name,$neighbor_limit,$up,$down){
			$this->query_runner 	= $runner;
			//echo "node:".$node_name;
			$this->node_name 		= $node_name;
			$this->neighbor_limit	= $neighbor_limit;
			$this->flag_up 			= $up;
			$this->flag_down		= $down;
			$this->neighbors		= array();
			
			$this->calculateNodeId();
			$this->calculateNeighbors();
			$this->calculateCategory();
		}
		
		abstract public function __toString();
		//abstract protected function calculateCategory();
		
		public function __destruct(){
		}
		
		public function getNeighorLimit(){
			return $this->neighbor_limit;
		}
		
		public function getNodeName(){
			return $this->node_name;
		}
		
		public function getNodeCategory(){
			return $node_category;
		}
		
		public function getNodeId(){
			return $this->node_id;
		}
		
		public function getUpFlag(){
			return $this->flag_up;
		}
		
		public function getDownFlag(){
			return $this->flag_down;
		}
		
		public function getNeighbors(){
			return $this->neighbors;
		}
		
		private function setNodeId($id){
			$this->node_id = $id;
		}
		
		protected function setNodeCategory($category){
			$this->node_category = $category;
		}
		
		/** 
		* Calculate the name of a node based on its ID
		* @param $node_id the numeric id of a node in the database
		* return string name of node in database
		*/
		public function calculateNodeName($node_id){
			global $object_calculate_node_name;
			global $object_structure_name;
			
			$escaped 	= mysql_real_escape_string($node_id);
			$sql		= sprintf($object_calculate_node_name,$escaped);
			$res 		= $this->query_runner->runQuery($sql);
			$line 		= mysql_fetch_array($res,MYSQL_ASSOC);
			
			return $line[$object_structure_name];
		}
		
		public function getNodeNeighborDirectionTo($node_id){
			global $relationship_reference_count;
			global $all_structure_as_count;
			$sql 	= sprintf($relationship_reference_count,$this->getNodeId(),$node_id);
			$res 	= $this->query_runner->runQuery($sql);
			$line	= mysql_fetch_array($res ,MYSQL_ASSOC);
			//echo "Line: " . $line[$all_structure_as_count] ." <br />";
			mysql_free_result($res);
			if(isset($line[$all_structure_as_count]) and ($line[$all_structure_as_count] + 0) > 0){
				return True;
			} else {
				return False;
			}
		}
		
		public function calculateNodeValueRelationships(){
			global $relationship_value_attributes;
			global $attribute_structure_name;
			global $relationship_structure_relation_value;
			$return 	= array();
			$sql 		= sprintf($relationship_value_attributes,$this->getNodeId());
			$res 		= $this->query_runner->runQuery($sql);
			
			while($line = mysql_fetch_array($res ,MYSQL_ASSOC)){
				$return[$line[$attribute_structure_name]] = $line[$relationship_structure_relation_value];
			}
			
			mysql_free_result($res);
			return $return;
			
		}
		
		protected function calculateNeighbors(){
			global $object_structure_primary_id;
			global $object_structure_name;
			global $attribute_structure_name;
			global $relationship_structure_reference_fk;
			$node_id = $this->getNodeId();
			//echo $this->getNodeName()."<br />";
			$return_up 		= null;
			$return_down 	= null;
			//echo "calcing neighbors\n";
			if($this->getUpFlag()){
				//echo "getting up...";
				$results_up = $this->getUpNeighbors();
				//print_r($results_up);
				if($results_up != null){
					$return_up = $this->structureUpNeighbors();
				} 
			}
			
			if($this->getDownFlag()){
				$results_down = $this->getDownNeighbors();
				if($results_down != null){
					$return_down = $this->structureDownNeighbors();
				} 
			}
			
			$this->constructNeighborArray($return_up,$return_down);	
		}
		
		private function constructNeighborArray($return_up,$return_down){
			if($return_down != null and $return_up != null){
				//echo "merge<br />";
				foreach($return_up as $key => $value){
					$this->neighbors[$key] = $value;
				}
				
				foreach($return_down as $key => $value){
					$this->neighbors[$key] = $value;
				}
				
			} else if($return_down != null){
				//echo "down<br />";
				$this->neighbors = $return_down;
			} else if($return_up != null){
				//echo "up<br />";
				$this->neighbors = $return_up;
			} else {
				//echo "none<br />";
				$this->neighbors = null;
			}
		}
				
		private function structureUpNeighbors(){
			global $attribute_structure_name;
			global $relationship_structure_reference_fk;
			$results_up = $this->getUpNeighbors();
			$return = null;
			if($results_up != null){
				$return = array();
				while($line = mysql_fetch_array($results_up,MYSQL_ASSOC)){
					$return[$line["$relationship_structure_reference_fk"] + 0] = $line["$attribute_structure_name"];
				}
				
				mysql_free_result($results_up);
			} 
			return $return;
		}
		
		private function structureDownNeighbors(){
			global $object_structure_primary_id;
			global $object_structure_name;
			$return = null;
			$results_down = $this->getDownNeighbors();
			if($results_down != null){
				$return = array();
				while($line = mysql_fetch_array($results_down,MYSQL_ASSOC)){
					$return[$line["$object_structure_primary_id"] + 0] = $line["$object_structure_name"];
				}
				mysql_free_result($results_down);
			}
			
			return $return;
			
		}
		
		
		/**
		* Get the indirect neighbors of this node. 
		* Indirect neighbors are nodes referencing this one, not nodes this node is
		* referencing.
		* return query results of attribute keys and values as an array
		*/
		private function getUpNeighbors(){
			global $relationship_count_references_to_object;
			global $all_structure_as_count;
			global $relationship_reference_neighbors;
			
			/** Store this node id */
			$node_id 	= $this->getNodeId();
			//echo "node id = $node_id\n";
			/** Escape the node id */
			$escaped 	= mysql_real_escape_string($node_id);
			/** Create SQL to count references */
			$sql        = sprintf($relationship_count_references_to_object,$escaped);
			/** Run the query */
			$res 		= $this->query_runner->runQuery($sql);
			/** Fetch the result line */
			$line 		= mysql_fetch_array($res,MYSQL_ASSOC);
			/** Free the result of the query */
			mysql_free_result($res);
			/** If the result line is set and the count is greater the 0 */
			if(isset($line["$all_structure_as_count"]) and $line["$all_structure_as_count"] > 0){
				/** Create SQL to get the upward neighbors */
				//echo $line["$all_structure_as_count"];
				$sql = sprintf($relationship_reference_neighbors,$escaped,$this->getNeighorLimit());
				//echo "$sql<br />";
				/* Return the result */
				return $this->query_runner->runQuery($sql);
			} else { 
				/** Else, return null */
				return null;	
			}	
		}
		
		/**
		* Get the direct neighbors of this node.
		* Direct neighbors are neighors that this node references
		* return query results of attribute keys and values as an array
		*/
		private function getDownNeighbors(){
			global $all_structure_as_count;
			global $relationship_count_references_from_object;
			global $relationship_referencing_neighbors;
			$node_id = $this->getNodeId();
			$escaped 	= mysql_real_escape_string($node_id);
			$sql		= sprintf($relationship_count_references_from_object,$escaped);
			
			$res 		= $this->query_runner->runQuery($sql);
			$line 		= mysql_fetch_array($res,MYSQL_ASSOC);
			
			if(isset($line["$all_structure_as_count"]) and $line["$all_structure_as_count"] > 0){
				$sql = sprintf($relationship_referencing_neighbors,$escaped,$this->getNeighorLimit());
				//echo $sql."<br />";
				return $this->query_runner->runQuery($sql);
			} else { return null; }
		}
		
		private function calculateNodeId(){
			global $object_calculate_node_id;
			global $object_structure_primary_id;
			
			$escaped 	= mysql_real_escape_string($this->getNodeName());
			//$sql 		= "select id from object where name = \"$escaped\" limit 1";
			$sql 		= sprintf($object_calculate_node_id,$escaped);
			$res 		= $this->query_runner->runQuery($sql);
			$line 		= mysql_fetch_array($res,MYSQL_ASSOC);
			mysql_free_result($res);
			$this->setNodeId($line["$object_structure_primary_id"] + 0);
		}
		
	}
	
?>