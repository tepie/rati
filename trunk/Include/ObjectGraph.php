<?php

	include_once('SettingsDatabase.php');
	include_once('SettingsGraph.php');
	include_once('SQLQueries.php');
	include_once('ObjectNode.php');
	
	/**
	* A object representing a graphviz graph
	*/ 
	class GraphObject{
		
		/**
		* Construct GraphObject
		* @param $runner a query runner object
		* @param $up boolean flag to determine if we are visiting upward neighbors
		* @param $down boolean flag to determine if we are visiting downward neighbors
		* @param $li numeric neighbor limit
		* @param $direction the direction to draw the graph (LR, TB)
		*/
		public function GraphObject($runner,$up,$down,$li,$direction){
			$res = $this->reset();
			$this->query_runner 	= $runner;
			$this->flag_up 			= $up;
			$this->flag_down 		= $down;
			$this->limit			= $li;
			$this->limit_track		= 0;
			$this->graph_direction	= $direction;
		}
		/**
		* Walk a node as root to determine is neighboring relationships
		* @param $node_name the name of the root node
		* @param $fontsize the font size of the node text in the graph
		* @param $neighbor_limit the number of neighbors per node to generate in the graph
		*/
		public function walk($node_name,$fontsize="8",$neighbor_limit=null){
			global $mysql_database_neighbor_limit;
			global $directory_dot_graph;
			
			// Create a new node object using this query runner and node name
			if($neighbor_limit != null){
				//echo $neighbor_limit;
				$mysql_database_neighbor_limit = $neighbor_limit;
				//$node 	= new NodeObject($this->query_runner,$node_name,$neighbor_limit);
			}
			
			$node 	= new NodeObject($this->query_runner,$node_name,$mysql_database_neighbor_limit);
			$this->node_name 				= $node_name;
			$this->graphviz_temp_filename 	= tempnam("$directory_dot_graph","graphviz");
			$this->graphviz_temp_filehandle = fopen($this->graphviz_temp_filename,"w+");
			
			$this->putGraphvizText($this->getGraphvizHeading($fontsize));
			
			// Determine the node objects neighbors
			$neighbors 			= $node->getNeighbors();
			$this->graphviz 	= "";
			
			// Add this node to the graph list object
			$this->addGraphNode($node);
			
			// If this node has more then zero neighbors, foo it
			if(count($neighbors) > 0){
				$this->track_limit 	= 0;
				while($this->track_limit < $this->limit){
					$this->visitUnvisited();
					$this->track_limit++;
				}
			}
			
			$this->putGraphvizText($this->getGraphvizFooting());
			fclose($this->graphviz_temp_filehandle);
			return $this->graphviz_temp_filename;
		} 
		
		/**
		* Get the graph object 
		*/
		public function getGraph(){
			return $this->graph;
		}

		/**
		* Get the root listing 
		*/
		public function getNodesVisited(){
			return $this->nodesVisited;
		}
		
		/**
		* Override the string formatting of this object
		* return $this->getGraphvizSring()
		*/
		public function __toString(){
			if(file_exists($this->graphviz_temp_filename)){
				$local_handle = fopen($this->graphviz_temp_filename,"rb");
				while (!feof($local_handle)) {
					echo fread($local_handle, 4096);
				}
				fclose($local_handle);
			} else {
				echo "You need to walk this graph to print it.";
			} 
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
		
		
		//*********************************************************
		//** Private Functions
		//*********************************************************
		
		private function getGraphvizHeading($fontsize="8"){
			global $graphviz_string_heading_line;
			global $graphviz_string_graph_attributes;
			global $graphviz_string_nodes_attribute;
			
			$heading = $graphviz_string_heading_line;
			$heading = $heading . "\t". sprintf($graphviz_string_graph_attributes,$this->graph_direction,$this->node_name) . "\n";
			$heading = $heading . sprintf($graphviz_string_nodes_attribute,$fontsize);
			
			return $heading;
		}
		
		private function getGraphvizFooting(){
			global $graphviz_string_footing_line;
			return $graphviz_string_footing_line;
		}
		
		private function putGraphvizText($graphviz_text){
			return fwrite($this->graphviz_temp_filehandle,$graphviz_text);
		}
		
		/**
 		* Graph the links for the graphviz string for a node object
		* @param $nodeObj the node object to graph links for 
		* @param $fontsize the font size to use for link labels in this graph
		* return the graphviz link text for this node object
		*/
		private function graphLink($nodeObj,$link_to,$rule,$fontsize="0"){
			global $graphviz_string_link_node;
			global $graphviz_string_link_lr_arrow;
			global $graphviz_string_link_attributes;
			global $graphviz_string_links_fontsize;
			
			if($fontsize == "0"){
				$use_size =$graphviz_string_links_fontsize;
			} else {
				$use_size = $fontsize;
			}
			
			// Return text container
			$text 		= "";
			$key 		= $link_to;
			$value 		= $rule;
			$node_id 	= $nodeObj->getNodeId();
			
			if(array_key_exists($key,$this->getGraph())){
				$switch = $nodeObj->getNodeNeighborDirectionTo($key);
				
				$left 	= $node_id;
				$right	= $key;
				if($switch){
					$dirType = "forward";
				} else {
					$dirType = "back";
				}
				
				$html_value = htmlentities($value, ENT_QUOTES);
				
				// Formulate the link text
				$text	= $text . "\t". sprintf($graphviz_string_link_node,$left);
				$text 	= $text . $graphviz_string_link_lr_arrow;
				$text	= $text . sprintf($graphviz_string_link_node,$right);
				$text  	= $text . sprintf($graphviz_string_link_attributes,$html_value,$use_size,$dirType) . "\n";
			}
			
			$this->putGraphvizText($text);
		}
		
		
		/** 
		*Reset the graph object 
		*/
		private function reset(){
			$this->nodesVisited 	= array();
			$this->graph 			= array(); 
			$this->graphviz_labels 	= array();
			$this->graphviz 		= "";
			$this->flag_up 			= True;
			$this->flag_down 		= True;
			$this->limit_track 		= 0;
			$this->limit 			= 1;
		}
		
		/** Foo on a node object
		* @param $nodeObj the node object to foo on
		*/
		private function visitNode($nodeObj){
			global $mysql_database_neighbor_limit;
			$this->addVisitedNode($nodeObj);
			foreach($nodeObj->getNeighbors() as $node_id => $value){
				$node_name 			= $this->calculateNodeName($node_id);
				$neighborNodeObj 	= new NodeObject($this->query_runner,$node_name,$mysql_database_neighbor_limit);
				if($this->addGraphNode($neighborNodeObj)){
					$this->graphLink($nodeObj,$node_id,$value);
				}
			}
		}
		
		private function visitUnvisited(){
			global $mysql_database_neighbor_limit;
			$graph_keys 	= array_keys($this->graph);
			$visited_keys 	= array_keys($this->nodesVisited);
			$unvisited 		= array_diff_key($graph_keys,$visited_keys);
			//print_r($unvisited);
			
			foreach($unvisited as $index => $node_id){
				$node_name 			= $this->calculateNodeName($node_id);				
				$unvisitedNodeObj 	= new NodeObject($this->query_runner,$node_name,$mysql_database_neighbor_limit);
				$this->visitNode($unvisitedNodeObj);
				//if($index > 0) break;
			}
		}
		
		/** 
		* Check if a node has been visited
		* @param $nodeObj the node to check if it is in the graph already
		* return false if the node does not exist, true otherwise
		*/
		private function inVisited($nodeObj){
			if(!array_key_exists($nodeObj->getNodeId(),$this->nodesVisited)){
				return True; //array_push($this->nodesVisited,$nodeObj->getNodeId());
			} else {
				return False;
			}

		}	
		
		/** 
		* Check if a node is in the graph
		* @param $nodeObj the node to check if it is in the graph already
		* return false if the node does not exist, true otherwise
		*/
		private function inGraph($nodeObj){
			if(!array_key_exists($nodeObj->getNodeId(),$this->getGraph())){
				return True; //array_push($this->nodesVisited,$nodeObj->getNodeId());
			} else {
				return False;
			}
		}	

		/** 
		* Adds a node to the graph object 
		* @param $nodeObj a node object to be added to the graph object
		*/
		private function addGraphNode($nodeObj){
			$result = $this->inGraph($nodeObj);
			if($result){
				$id 	= $nodeObj->getNodeId();
				$this->graph[$id] = $nodeObj;
				//$this->graphviz = $this->graphviz . $nodeObj;
				$this->putGraphvizText($nodeObj);
				return true;
			}
			return false;
		}
		
		private function addVisitedNode($nodeObj){
			$result = $this->inVisited($nodeObj);
			if($result){
				$id = $nodeObj->getNodeId();
				array_push($this->nodesVisited,$id);
			}
		}
		
		/** The graphviz string */
		//private $graphviz 		= null;
		/** The xml export string */
		//private $xml_export		= null;
		/** Graphviz label array */
		//private $graphviz_labels = null;
		
		
		
		/** The root attribute array */
		//private $root_attributes = null;
		/** The category of this graphs root node */
		//private $root_category 	= null;
		
		/** Arrow truth flag for string */
		//private $arrows;
		
		private $graphviz_temp_filename 	= null;
		private $graphviz_temp_filehandle 	= null;
		private $node_name					= null;
		/** the graph direction */
		private $graph_direction			= null;
		/** Level at which to display graph */
		private $limit 			= null;
		private $limit_track 	= null;
		/** The QueryRunner of this object */
		private $query_runner 	= null;
		/** Flag to determine to vist indirect neighbors */
		private $flag_up 		= null;
		/** Flag to determine to visit direct neighbors */
		private $flag_down 		= null;
		
		/** A list of nodes that were visited by this graph */
		private $nodesVisited  	= null;
		/** The graph array */
		private $graph 			= null;
	}

?>