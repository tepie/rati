<?php 

	
	abstract class AbstractGraph{
		
		private $graph;
		private $query_runner;
		//private $flag_up;
		//private $flag_down;
		private $limit;
		private $limit_track;
		private $nodesVisited;
		private $local_node_object;
		private $current_root_node;
		
		public function AbstractGraph($runner,$limit){
			$this->query_runner = $runner;
			//$this->flag_up 		= $up;
			//$this->flag_down	= $down;
			$this->limit 		= $limit;
			$this->limit_track	= 0;
			$this->nodesVisited = array();
			$this->graph		= array();
			//$this->neighbor_limit = $neighbor_limit;
		}
		
		public function walk(AbstractNode $node){
			/*$node = new NodeObject($this->getQueryRunner(),
				$node_name,
				$this->getNeighborLimit());
			*/	
			$this->setLocalNodeObject($node);
			$this->addGraphNode($this->getLocalNodeObject());
			
			$neighbors = $this->getLocalNodeObject()->getNeighbors();
			
			if(count($neighbors) > 0){
				$this->track_limit 	= 0;
				while($this->track_limit < $this->limit){
					$this->visitUnvisited();
					$this->track_limit++;
				}
			}
		}
		
		abstract public function __toString();
		
		public function __destruct(){}
		
		private function setLocalNodeObject(AbstractNode $node){
			$this->local_node_object = $node;
		}
		
		private function getLocalNodeObject(){
			return $this->local_node_object;
		}
		
		/*private function getNeighborLimit(){
			return $this->neighbor_limit;
		}*/
		
		public function getGraph(){
			return $this->graph;
		}
		
		/*private function getUpFlag(){
			return $this->flag_up;
		}
		
		private function getDownFlag(){
			return $this->flag_down;
		}*/
		
		private function getLimit(){
			return $this->limit;
		}
		
		private function getNodesVisited(){
			return $this->nodesVisited;
		}
		
		private function getQueryRunner(){
			return $this->query_runner;
		}
		
		private function inGraph(AbstractNode $nodeObj){
			if(!array_key_exists($nodeObj->getNodeId(),$this->getGraph())){
				return True;
			} else {
				return False;
			}
		}
		
		private function inVisited(AbstractNode $nodeObj){
			if(!array_key_exists($nodeObj->getNodeId(),$this->getNodesVisited())){
				return True;
			} else {
				return False;
			}
		}
		
		private function addGraphNode(AbstractNode $nodeObj){
			$result = $this->inGraph($nodeObj);
			if($result){
				$id = $nodeObj->getNodeId();
				$this->graph[$id] = $nodeObj;
				return true;
			}
			return false;
		}
		
		private function addVisitedNode(AbstractNode $nodeObj){
			$result = $this->inVisited($nodeObj);
			if($result){
				$id = $nodeObj->getNodeId();
				array_push($this->nodesVisited,$id);
			}
		}
		
		private function visitNode(AbstractNode $nodeObj){
			$this->addVisitedNode($nodeObj);
			foreach($nodeObj->getNeighbors() as $node_id => $value){
				$node_name 	= $this->getLocalNodeObject()->calculateNodeName($node_id);
				/*$neighborNodeObj 	= new NodeObject($this->getQueryRunner(),
					$node_name,$this->getLimit());
				*/
				
				$neighborNodeObj = clone $this->getLocalNodeObject();
				$neighborNodeObj->setNodeName($node_name);
				
				$this->addGraphNode($neighborNodeObj);
				/*if($this->addGraphNode($neighborNodeObj)){
					$this->graphLink($nodeObj,$node_id,$value);
				}*/
			}
		}
		
		private function visitUnvisited(){
			$graph_keys 	= array_keys($this->getGraph());
			$visited_keys 	= array_keys($this->getNodesVisited());
			$unvisited 		= array_diff_key($graph_keys,$visited_keys);
			
			foreach($unvisited as $index => $node_id){
				$node_name 			= $this->getLocalNodeObject()->calculateNodeName($node_id);				
				$unvisitedNodeObj = clone $this->getLocalNodeObject();
				$unvisitedNodeObj->setNodeName($node_name);
				/*$unvisitedNodeObj 	= new NodeObject($this->getQueryRunner(),
					$node_name,$this->getLimit());
				*/
				//$unvisitedNodeObj = $this->getLocalNodeObject();
				$this->visitNode($unvisitedNodeObj);
			}
		}
	}

?>