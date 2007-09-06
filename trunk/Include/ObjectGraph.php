<?php

	include_once('SettingsDatabase.php');
	include_once('SettingsGraph.php');
	include_once('SQLQueries.php');
	include_once('ObjectNode.php');
	
	/**
	* A object representing a graphviz graph
	*/ 
	class GraphObject{
		/** A list of nodes that were visited by this graph */
		private $nodesVisited  	= null;
		/** The graph array */
		private $graph 			= null;
		/** The graphviz string */
		private $graphviz 		= null;
		/** The xml export string */
		private $xml_export		= null;
		/** Graphviz label array */
		private $graphviz_labels = null;
		/** The QueryRunner of this object */
		private $query_runner 	= null;
		/** Flag to determine to vist indirect neighbors */
		private $flag_up 		= null;
		/** Flag to determine to visit direct neighbors */
		private $flag_down 		= null;
		/** Level at which to display graph */
		private $limit 			= null;
		private $limit_track 	= null;
		/** Directions ? */
		private $directions		= null;
		/** The root attribute array */
		private $root_attributes = null;
		/** The category of this graphs root node */
		private $root_category 	= null;
		private $node_name		= null;
		/** Arrow truth flag for string */
		private $arrows;
		
		/**
		* Construct GraphObject
		* @param $runner a query runner object
		* @param $up boolean flag to determine if we are visiting upward neighbors
		* @param $down boolean flag to determine if we are visiting downward neighbors
		* @param $li numeric neighbor limit
		* @param $direction the direction to draw the graph (LR, TB)
		* @param $arrows the graph string arrow switch, true shows the truth, false shows lies
		*/
		public function GraphObject($runner,$up,$down,$li,$direction,$arrows){
			$res = $this->reset();
			$this->query_runner 	= $runner;
			$this->flag_up 			= $up;
			$this->flag_down 		= $down;
			$this->limit			= $li;
			$this->limit_track		= 0;
			$this->direction		= $direction;
			$this->arrows 			= $arrows;
			//echo $this->arrows;
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
			return $this->getGraphvizSring();
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
		
		/**
		* Get the attributes of the root of this graph
		* return the attributes of the root 
		*/
		public function getRootNodeAttributes(){
			return $this->root_attributes;
		}
		
		/**
		* Get the root category of this graph 
		*/
		public function getRootCategory(){
			return $this->root_category;
		}
		
		/**
		* Walk a node as root to determine is neighboring relationships
		* @param $node_name the name of the root node
		*/
		public function walk($node_name){
			global $mysql_database_neighbor_limit;
			$this->limit_track = $this->limit;
			// Create a new node object using this query runner and node name
			$node 		= new NodeObject($this->query_runner,$node_name,$mysql_database_neighbor_limit);
			$this->node_name = $node_name;
			// This is the root, get its attributes
			$this->setRootNodeAttributes($node);
			$this->setRootCategory($node->getNodeCategory());
			
			// Determine the node objects neighbors
			$neighbors 	= $node->getNeighbors();
			
			// Add this node to the graph list object
			$this->addGraphNode($node);
			
			// If this node has more then zero neighbors, foo it
			if(count($neighbors) > 0){
				$this->track_limit = 0;
				while($this->track_limit < $this->limit){
					$this->visitUnvisited();
					$this->track_limit++;
				}
				//$this->visitNode($node);
				/*if($this->limit_track > 0){
					$this->limit_track--;
					$this->bar($neighbors);
				}*/
			}		
		} 
		
		/** Export this graph as an xml string */
		public function getExportXml(){
			$xml = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>\n";
			$xml = $xml . "<rati><export>";
			if($this->getGraph() == null){
				$xml = $xml . "</export></rati>";
				return $xml;
			}
			
			$xml = $xml . "<object category='' oid='".$this->node_name."' >";
			
			foreach($this->getRootNodeAttributes() as $name => $value){ 
				$xml = $xml ."<annotation name='$name'>" . htmlspecialchars($value) ."</annotation>\n";
			}
			
			//$xml = $xml . "<!-- Reference annotations not exported -->";
			
			// Flag that the root relationships have been drawn
			$firstFlag = true;
			$copy 	= $this->getGraph();
			
			foreach($copy as $node_id => $nodeObject){
				// For some reason we are getting an empty node in the datatbase
				// This is a bug fix, temporary
				if($node_id > 0 and $firstFlag){
					// Add the links of the current node object (root) to the graph
					foreach($nodeObject->getNeighbors() as $path => $rule){
						$path_name = $this->calculateNodeName($path);
						$xml = $xml ."<reference name='$rule' oidref='".htmlentities($path_name,ENT_QUOTES)."' />\n";
					}
					// Swithc the first flag
					$firstFlag = False;
				}
			}
			return $xml . "</object></export></rati>\n";
		
		}
		
		/**
		* Get the graphviz string version of this object
		* @param $fontsize the font size to use in the node text of this graph
		* return the graphviz string to generate a dot graph
		*/
		public function getGraphvizSring($fontsize="0"){
			// If the graph is null, we have no work to do
			if($this->getGraph() == null){
				return null;
			}
			
			// Tell this object we are thinking global for variables
			global $graphviz_string_heading_line;
			global $graphviz_string_nodes_attribute;
			global $graphviz_string_graph_attributes;
			global $graphviz_string_footing_line;
			global $graphviz_string_nodes_fontsize;
			
			if($fontsize == "0"){
				$use_size = $graphviz_string_nodes_fontsize;
			} else {
				$use_size = $fontsize;
			}
			
			// Flag that the root relationships have been drawn
			$firstFlag = true;
			
			// Construct the graph with heading,graph attributes, and 
			$this->graphviz =  $graphviz_string_heading_line;
			$this->graphviz = $this->graphviz . "\t". sprintf($graphviz_string_graph_attributes,$this->direction) . "\n";
			$this->graphviz = $this->graphviz . sprintf($graphviz_string_nodes_attribute,$use_size);
			
			// Copy the graph for ease of code readablity
			$copy 					= $this->getGraph();
			// Clear the graphviz labels, create it as an empty array
			$this->graphviz_labels 	= array();
			$this->track_limit = 0;
			// For each copy of the graph as a key value pair of node id and its object
			foreach($copy as $node_id => $nodeObject){
				// For some reason we are getting an empty node in the datatbase
				// This is a bug fix, temporary
				if($node_id > 0){
					// Add the current node object to the graphviz string
					
					$this->graphviz = $this->graphviz . $nodeObject;
					// Push this node id on the label list to keep track that it has already been labeled
					//$this->addGraphvizLinks($node_id);
					// If this is the root loop, meaning we are showing the neighbors of the root only, enter
					//if($firstFlag){
						// Add the links of the current node object (root) to the graph
					$this->graphviz = $this->graphviz . $this->graphLinks($nodeObject,$fontsize);
						// Swithc the first flag
						//$firstFlag = false;
						
					//}
				}
			}
			
			// Add the footing to the graphviz string
			$this->graphviz = $this->graphviz . $graphviz_string_footing_line;
			// Return the graphviz string
			return $this->graphviz;
		}
		
		/**
 		* Graph the links for the graphviz string for a node object
		* @param $nodeObj the node object to graph links for 
		* @param $fontsize the font size to use for link labels in this graph
		* return the graphviz link text for this node object
		*/
		public function graphLinks($nodeObj,$fontsize="0"){
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
			$node_id 	= $nodeObj->getNodeId();
			// For each neighbor of the node object as a pair
			foreach($nodeObj->getNeighbors() as $key => $value){
				// Calculate the node name from its key (the node id)
				//$node_name = $this->calculateNodeName($key);
				if(array_key_exists($key,$this->getGraph())){
					if($this->arrows){
						$switch = $nodeObj->getNodeNeighborDirectionTo($key);
					} else {
						$switch = true;
					}
					//echo $this->arrows . " " . $switch . " ";
					
					if($switch){
						$left 	= $node_id;
						$right	= $key;
					} else {
						$left 	= $key;
						$right	= $node_id;
					}
					
					$html_value = htmlentities($value, ENT_QUOTES);
					
					// Formulate the link text
					$text	= $text . "\t". sprintf($graphviz_string_link_node,$left);
					$text 	= $text . $graphviz_string_link_lr_arrow;
					$text	= $text . sprintf($graphviz_string_link_node,$right);
					$text  	= $text . sprintf($graphviz_string_link_attributes,$html_value,$use_size) . "\n";
				}
			}
			
			// Return the generated graphviz link text
			return $text;
		
		}
		
		//*********************************************************
		//** Private Functions
		//*********************************************************

		private function setRootCategory($category){
			$this->root_category = $category;
		}
		
		/** 
		* Set the root node attributes of this graph
		*/
		private function setRootNodeAttributes($rootNodeObj){
			$this->root_attributes = $rootNodeObj->getNodeAttributes();
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
				$this->addGraphNode($neighborNodeObj);	
			}
		}
		
		private function visitUnvisited(){
			global $mysql_database_neighbor_limit;
			$graph_keys = array_keys($this->graph);
			$visited_keys = array_keys($this->nodesVisited);
			$unvisited = array_diff_key($graph_keys,$visited_keys);
			print_r($unvisited);
			
			foreach($unvisited as $index => $node_id){
				$node_name 			= $this->calculateNodeName($node_id);
				echo "<br />$node_name<br />";				
				$unvisitedNodeObj 	= new NodeObject($this->query_runner,$node_name,$mysql_database_neighbor_limit);
				$this->visitNode($unvisitedNodeObj);
				if($index > 0) break;
			}
		}
		
		/** 
		* Go to the bar
		* @param $neighbors a list of neighbors to walk
		*/
		/*private function bar($neighbors){
			if($this->limit_track > 0){
				foreach($neighbors as $node_id => $value){
					if(!array_key_exists($node_id,$this->getnodesVisited())){
						$node_name 			= $this->calculateNodeName($node_id);
						//echo "<!-- bar: $node_name($node_id) => $value -->\n";
						//$this->walk($this->calculateNodeName($node_id));
					} else {
						echo "<!-- skipped $node_id -->\n";
					}
					break;
				}
			}
		}*/
		
		/** 
		* Check if a node is in the visited list
		* @param $node the node to check if it is in the visited
		* return false if there are no visited nodes
		* return false is the node is not in the visited list
		* return true if the node is in the visited list
		*/
		/*private function inNodesVisited($node){
			if(count($this->getNodesVisited()) == 0){
				return FALSE;
			}
			$key = array_search($node,$this->nodesVisited);
			if($key === "" or $key === 0 or $key === FALSE){ 
				return FALSE;
			} else{
				return TRUE;
			}
		}*/
	
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
		
		private function inGraphvizLabels($node_id){
			if(!in_array($node_id,$this->graphviz_labels)){
				return True;
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
			}
		}
		
		private function addVisitedNode($nodeObj){
			$result = $this->inVisited($nodeObj);
			if($result){
				$id = $nodeObj->getNodeId();
				array_push($this->nodesVisited,$id);
			}
		}
		
		private function addGraphvizLabels($node_id){
			$result = $this->inGraphvizLabels($node_id);
			if($result){
				array_push($this->graphviz_labels,$node_id);
			}
		}
	}

?>