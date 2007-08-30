<?php
	
	include_once('SQLQueries.php');
	include_once('SettingsPerspectives.php');
	include_once('SettingsGraph.php');
	include_once('SettingsWebApp.php');
	
	/**
	* Represents a node of a graphviz dot graph
	*/
	class NodeObject{
		/** Internal application generated ID */
		private $idInternal		= null;
		/** Database id of this node */
		private $idDatabase		= null;
		/** The name of this node object */
		private $name			= null;
		/** The neighbor array of this node */
		private $neighbors		= null;
		/** The category of this node */
		private $category		= null;
		/** The QueryRunner object of this node */
		private $query_runner 	= null;
		/** Flag to tell this node to look upward for neighbors */
		private $flag_up 		= null;
		/** Flag to tell this node to look downward for neighbors */
		private $flag_down 		= null;
		/** The limit ? */
		private $limit 			= null;
		/** The database select neighbor limit value */
		private $dblimit		= null;
		/** URL Page name? */
		private $urlPageName	= "";
		
		/**
		* Construct a new node object 
		* @param $runner QueryRunner object to run database queries
		* @param $node_name the name of this node
		* @param $dblimit the limit to use when visiting this node's neighbors in the graph
		*/
		public function NodeObject($runner,$node_name,$dblimit){
			//echo "Creating new node $node_name<br />";
			global $web_app_page_post_back_name;
			$this->query_runner	= $runner;
			$this->flag_up 		= True;
			$this->flag_down 	= True;
			$this->dblimit 		= $dblimit;
			$this->links		= array();
			$this->name 		= $node_name;
			$this->urlPageName	= $web_app_page_post_back_name;
			
			$this->calculateNodeId();
			$this->calculateNeighbors();
			$this->calculateCategory();
		}
		
		/**
		* Output this object as a string.
		* return a graphviz string representation of this node
		*/
		public function __toString(){
			global $url_rest_node_param;
			global $graphviz_string_node_attributes;
			global $graphviz_string_link_node;
			
			$text = "";
			$label 	= $this->name;
			$url 	= $this->urlPageName . "?$url_rest_node_param=" . urlencode($label);
			$parts 	= split("\/",$label);
			$show 	= $parts[count($parts) - 1];
			$color  = $this->calculateNodeColor();
			
			$text 	= "\t" . sprintf($graphviz_string_link_node,$this->idDatabase);
			$text   = $text . " " . sprintf($graphviz_string_node_attributes,$show,$url,$color) . "\n";
			
			return $text;
		}
		
		/**
		* Get the attributes of this node
		* return array structure containing the attribute names as keys and the values of the attributes as values
		*/
		public function getNodeAttributes(){
			return $this->calculateNodeValueRelationships();
		}
		
		/**
		* Get the unique numeric id of this node
		* return numeric id of this node
		*/
		public function getNodeId(){
			return $this->idDatabase;
		}
		
		/** 
		* Get the name of this node 
		* return string name of node
		*/
		public function getNodeName(){
			return $this->name;
		}
		
		/**
		* Get the category of this node
		* return string name of category
		*/
		public function getNodeCategory(){
			return $this->category;
		}
		
		/** 
		* Get the neighbors of the node 
		* return array structure containing a referencing numeric key as a key and the linking attribute value as the value
		*/
		public function getNeighbors(){
			return $this->neighbors;
		}
		
		/**
		* Override this node's neighbors and give it new ones
		*/
		public function setNeighbors($new_neighbors){
			$this->neighbors = $new_neighbors;
		}
		
		/**
		* Get the link direction of this node to a give neighbor id
		* @param $node_id the numeric node id to check the direction of the link on
		* return True if a direct neighbor, False if an indirect neighbor
		*/
		public function getNodeNeighborDirectionTo($node_id){
			global $relationship_reference_count;
			global $all_structure_as_count;
			$sql 	= sprintf($relationship_reference_count,$this->idDatabase,$node_id);
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
		
		//*************************************************************************************************************
		//** Private Functions
		//*************************************************************************************************************
		
		/** 
		* Calculate the value relationships of this node.
		* Value relationships are non-referencing, meaning the content of the attribute
		* is just a value, and is not linked to anything. 
		* return key, value array of attribute name, value
		*/
		private function calculateNodeValueRelationships(){
			global $relationship_value_attributes;
			global $attribute_structure_name;
			global $relationship_structure_relation_value;
			$return = array();
			$sql = sprintf($relationship_value_attributes,$this->idDatabase);
			$res 		= $this->query_runner->runQuery($sql);
			while($line = mysql_fetch_array($res ,MYSQL_ASSOC)){
				$return[$line[$attribute_structure_name]] = $line[$relationship_structure_relation_value];
			}
			mysql_free_result($res);
			return $return;
			
		}
		
		/** 
		* Calculate the numeric node id of this node
		* return the numeric node id of this node
		*/
		private function calculateNodeId(){
			global $object_calculate_node_id;
			global $object_structure_primary_id;
			
			$escaped 	= mysql_real_escape_string($this->name);
			//$sql 		= "select id from object where name = \"$escaped\" limit 1";
			$sql 		= sprintf($object_calculate_node_id,$escaped);
			$res 		= $this->query_runner->runQuery($sql);
			$line 		= mysql_fetch_array($res,MYSQL_ASSOC);
			mysql_free_result($res);
			$this->idDatabase = $line["$object_structure_primary_id"] + 0;
		}
		
		/**
		* Calculate the node color of this node to show in the graphviz graph
		* return A valid graphviz color string
		*/
		private function calculateNodeColor(){
			global $web_app_default_node_color;
			global $web_app_default_cat_color;
			
			global $perspective_category_eregs;
			global $perspective_node_color_maps;
			
			foreach($perspective_category_eregs as $index => $ereg){
				if(ereg($ereg,$this->getNodeName())){
					return $web_app_default_cat_color;
				} 
			}
			
			foreach($perspective_node_color_maps as $index => $map){
				if(in_array($this->getNodeCategory(),array_keys($map))){
					return $map[$this->getNodeCategory()];
				}
			}
				
			return $web_app_default_node_color;
				
			
		}
		
		/**
		* Calculate the category of this node.
		* Every object is in a category, even categories.
		* return category string
		*/
		private function calculateCategory(){	
			global $object_calculate_node_name;
			global $object_structure_name;			
			global $perspective_category_reference_rules;
			
			$found = False;
			
			foreach($this->getNeighbors() as $nodeId => $ruleName){
				foreach($perspective_category_reference_rules as $index => $rule){
					if($ruleName == $rule){
						//echo "$ruleName vs. $rule <br />";
						$sql 	= sprintf($object_calculate_node_name,$nodeId);
						$res 	= $this->query_runner->runQuery($sql);
						$line 	= mysql_fetch_array($res,MYSQL_ASSOC);
						mysql_free_result($res);
						$this->category = $line["$object_structure_name"];
						//echo $this->name . " - " .$line["$object_structure_name"] . "<br />";
						$found = True;
						break;
					}
				}
			}
			
			if(!$found) $this->category = "Unknown";
		}
		
		/** 
		* Calculate the neighbors of this node.
		* Nodes have both direct and indirect neighbors, meaning a direct node directly references
		* another object and indiret neighbors mean another node references this node. This function
		* calculate both. Upward neighbors are indirect.
		* return combined indirect and direct neighbors as array of numeric id as key and attribute link name as value
		*/
		private function calculateNeighbors(){
			global $relationship_structure_reference_fk;
			global $object_structure_primary_id;
			global $object_structure_name;
			global $attribute_structure_name;
			$node_id = $this->idDatabase;
			
			$return = array();
			if($this->flag_up){
				$results_up = $this->getUpNeighbors();
				if($results_up != null){
					while($line = mysql_fetch_array($results_up,MYSQL_ASSOC)){
						$return[$line["$relationship_structure_reference_fk"] + 0] = $line["$attribute_structure_name"];
					}
					mysql_free_result($results_up);
				} 
				
			}
			
			if($this->flag_down){
				$results_down = $this->getDownNeighbors();
				if($results_down != null){
					while($line = mysql_fetch_array($results_down,MYSQL_ASSOC)){
						$return[$line["$object_structure_primary_id"] + 0] = $line["$object_structure_name"];
					}
					mysql_free_result($results_down);
				}
			}
			
			$this->neighbors = $return;
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
			$node_id 	= $this->idDatabase;
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
				$sql = sprintf($relationship_reference_neighbors,$escaped,$this->dblimit);
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
			$node_id = $this->idDatabase;
			$escaped 	= mysql_real_escape_string($node_id);
			$sql		= sprintf($relationship_count_references_from_object,$escaped);
			
			$res 		= $this->query_runner->runQuery($sql);
			$line 		= mysql_fetch_array($res,MYSQL_ASSOC);
			
			if(isset($line["$all_structure_as_count"]) and $line["$all_structure_as_count"] > 0){
				$sql = sprintf($relationship_referencing_neighbors,$escaped,$this->dblimit);
				//echo $sql."<br />";
				return $this->query_runner->runQuery($sql);
			} else { return null; }
		}
	}
	
?>