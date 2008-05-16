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
		public function GraphObject($runner,$up,$down,$li,$direction,$rule_filter=null){
			$res = $this->reset();
			$this->query_runner 	= $runner;
			$this->flag_up 			= $up;
			$this->flag_down 		= $down;
			$this->limit			= $li;
			$this->limit_track		= 0;
			$this->graph_direction	= $direction;
			$this->rule_filter_array = $rule_filter;
			
		}

		public function bfsWalk($vertex,$fontsize="8",$neighbor_limit=null){
			global $mysql_database_neighbor_limit;
			global $directory_dot_graph;
			
			if($neighbor_limit != null){
				$mysql_database_neighbor_limit = $neighbor_limit;
			}
			
			if($fontsize == "0"){
				$use_size =$graphviz_string_links_fontsize;
			} else {
				$use_size = $fontsize;
			}
			
			$this->node_name 				= $vertex;
			$this->graphviz_temp_filename 	= tempnam("$directory_dot_graph","graphviz");
			$this->graphviz_temp_filehandle = fopen($this->graphviz_temp_filename,"w+");
			
			$this->putGraphvizText($this->getGraphvizHeading($fontsize));
			
			$this->graphviz = "";
			
			$treeT = array();
			$this->graphUnVisited = array();
			$this->graphVisited = array();
			$this->graphRules = array();
			$this->graphDirection = array();
			$this->graphPrinted = array();
			
			$starting_vertex = new NodeObject($this->query_runner,$vertex,$mysql_database_neighbor_limit);
			
			$this->search($starting_vertex);
			
			$v_last = $starting_vertex->getNodeName();
			$w_last = null;
			$v = null;
			$w = null;
			$this->currentDepth = 1;
			// while unvisited list is not empty
			while(count($this->graphUnVisited) > 0){
				// remove edge v,w from the front of the unvisited list
				$popped_pair = array_shift($this->graphUnVisited);
				// expand pair to v,w
				$v = $popped_pair [0];
				$w = $popped_pair [1];
				//echo "$v - $w <br />";
				if($v != $v_last){
					$this->currentDepth++;
					//echo $this->currentDepth . " <br />";
				}
				
				// if w is not yet visited
				if(!in_array($w,$this->graphVisited)){
					// add v,w to tree 
					array_push($treeT,$popped_pair);
					// display the edge
					$this->displayEdge($popped_pair,$use_size);
					// create a node object for w
					$next_vertex = new NodeObject($this->query_runner,$w,$mysql_database_neighbor_limit);
					if($this->currentDepth < $this->limit){
						// search w
						$this->search($next_vertex);
					} else {
						$this->putGraphvizText($next_vertex);
					}
				}
				
				$v_last = $v;
				$w_last = $w;
			}
			
			$this->putGraphvizText($this->getGraphvizFooting());
			$this->putGraphvizText($track_limit);
			fclose($this->graphviz_temp_filehandle);
			return $this->graphviz_temp_filename;
		}
	
		public function search($vertex){
			// vist vertex
			array_push($this->graphVisited,$vertex->getNodeName());
			// display the node text for vertext
			$this->putGraphvizText($vertex);
			// get the edges for vertex
			$neighbors = $vertex->getNeighbors();
			// for each edge (v,w)
			
			if($neighbors != null){
				foreach($neighbors as $key => $value){
					if(($this->rule_filter_array != null and in_array($value,$this->rule_filter_array) == False) 
						or 
						($this->rule_filter_array == null)){
						// grab the node name for w
						$w = $this->calculateNodeName($key);
						// new edge pair v,w
						$new_pair = array($vertex->getNodeName(),$w);
						// push the new edge to the end of the unvisited list
						array_push($this->graphUnVisited,$new_pair);
						// create a text based lookup key for pair
						$array_key = $this->arrayKey($vertex->getNodeName(),$w);
						// store the rule that connects v,w
						$this->graphRules[$array_key] = $value;
						// store the direction of v,w
						$this->graphDirection[$array_key] = $vertex->getNodeNeighborDirectionTo($key);
					}
				}
			}
		}
		
		public function arrayKey($v,$w){
			return md5("$v$w");
		}
		
		public function displayEdge($edge,$fontsize){
			global $graphviz_string_link_node;
			global $graphviz_string_link_lr_arrow;
			global $graphviz_string_link_attributes;
			global $graphviz_string_links_fontsize;
			
			$v = $edge[0];
			$w = $edge[1];
			$array_key = $this->arrayKey($v,$w);
			$r = $this->graphRules[$array_key];
			$text = "";

			$switch = $this->graphDirection[$array_key];
			$left 	= md5($v);
			$right	= md5($w);
			if($switch){
				$dirType = "forward";
			} else {
				$dirType = "back";
			}
			
			$html_value = htmlentities($r, ENT_QUOTES);
			
			// Formulate the link text
			$text	= $text . "\t". sprintf($graphviz_string_link_node,$left);
			$text 	= $text . $graphviz_string_link_lr_arrow;
			$text	= $text . sprintf($graphviz_string_link_node,$right);
			$text  	= $text . sprintf($graphviz_string_link_attributes,$html_value,$fontsize,$dirType) . "\n";
			
			$this->putGraphvizText($text);
		}
		
		
		/**
		* Walk a node as root to determine is neighboring relationships
		* @param $node_name the name of the root node
		* @param $fontsize the font size of the node text in the graph
		* @param $neighbor_limit the number of neighbors per node to generate in the graph
		*/
		public function walk($node_name,$fontsize="8",$neighbor_limit=null){
			return $this->bfsWalk($node_name,$fontsize=$fontsize,$neighbor_limit=$neighbor_limit);
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
		/*public function getNodesVisited(){
			return $this->nodesVisited;
		}*/
		
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
			
			$wrapped_graph_label = wordwrap($this->node_name,75,"\\n",True);
			
			$heading = $graphviz_string_heading_line;
			$heading = $heading . "\t". sprintf($graphviz_string_graph_attributes,$this->graph_direction,$wrapped_graph_label) . "\n";
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
		/*private function graphLink($nodeObj,$link_to,$rule,$fontsize="0"){
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
		}*/
		
		
		/** 
		*Reset the graph object 
		*/
		private function reset(){
			
			//$this->relationships_shown = array();
			
			//$this->nodesVisited 	= array();
			$this->graph 			= array(); 
			$this->graphviz_labels 	= array();
			$this->graphviz 		= "";
			$this->flag_up 			= True;
			$this->flag_down 		= True;
			//$this->limit_track 		= 0;
			//$this->limit 			= 1;
		}
		
		/** Foo on a node object
		* @param $nodeObj the node object to foo on
		*/
		/*private function visitNode($nodeObj){
			global $mysql_database_neighbor_limit;
			$this->addVisitedNode($nodeObj);
			foreach($nodeObj->getNeighbors() as $node_id => $value){
				$node_name 			= $this->calculateNodeName($node_id);
				$neighborNodeObj 	= new NodeObject($this->query_runner,$node_name,$mysql_database_neighbor_limit);
				// TODO: rule filter add
				//	print_r($this->rule_filter_array);
				if($this->rule_filter_array == null){
					//echo "rule filter null<br />";
					if($this->addGraphNode($neighborNodeObj)){
						$this->graphLink($nodeObj,$node_id,$value);
					}
				} else {
					//echo "rule filter not null<br />";
					if(in_array($value,$this->rule_filter_array) == True){
						//echo "rule ($value) is in filter array<br />";
					} else {
						//echo "rule ($value) is not in filter array<br />";
						if($this->addGraphNode($neighborNodeObj)){
							$this->graphLink($nodeObj,$node_id,$value);
						}
					}
					
				}

			}
		}*/
		
		/*private function visitUnvisited(){
			global $mysql_database_neighbor_limit;
			$graph_keys 	= array_keys($this->graph);
			
			/**
			TODO: Lines drawn bug is here!
			If node A -> B is drawn, then C -> B will never been drawn since B 
			is considered "visited"
			*/
			/*
			$visited_keys 	= array_keys($this->nodesVisited);
			$unvisited 		= array_diff_key($graph_keys,$visited_keys);
			//print_r($unvisited);
			
			foreach($unvisited as $index => $node_id){
				$node_name 			= $this->calculateNodeName($node_id);				
				$unvisitedNodeObj 	= new NodeObject($this->query_runner,$node_name,$mysql_database_neighbor_limit);
				$this->visitNode($unvisitedNodeObj);
				//if($index > 0) break;
			}
		}*/
		
		/** 
		* Check if a node has been visited
		* @param $nodeObj the node to check if it is in the graph already
		* return false if the node does not exist, true otherwise
		*/
		/*private function inVisited($nodeObj){
			if(!array_key_exists($nodeObj->getNodeId(),$this->nodesVisited)){
				return True; //array_push($this->nodesVisited,$nodeObj->getNodeId());
			} else {
				return False;
			}

		}*/	
		
		/** 
		* Check if a node is in the graph
		* @param $nodeObj the node to check if it is in the graph already
		* return false if the node does not exist, true otherwise
		*/
		/*private function inGraph($nodeObj){
			if(!array_key_exists($nodeObj->getNodeId(),$this->getGraph())){
				return True; //array_push($this->nodesVisited,$nodeObj->getNodeId());
			} else {
				return False;
			}
		}	*/

		/** 
		* Adds a node to the graph object 
		* @param $nodeObj a node object to be added to the graph object
		*/
		/*private function addGraphNode($nodeObj){
			$result = $this->inGraph($nodeObj);
			if($result){
				$id 	= $nodeObj->getNodeId();
				$this->graph[$id] = $nodeObj;
				//$this->graphviz = $this->graphviz . $nodeObj;
				$this->putGraphvizText($nodeObj);
				return true;
			}
			return false;
		}*/
		
		/*private function addVisitedNode($nodeObj){
			$result = $this->inVisited($nodeObj);
			if($result){
				$id = $nodeObj->getNodeId();
				array_push($this->nodesVisited,$id);
			}
		}*/
		
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
		private $rule_filter_array = null;
		/** A list of nodes that were visited by this graph */
		//private $nodesVisited  	= null;
		/** The graph array */
		private $graph 			= null;
	}

?>