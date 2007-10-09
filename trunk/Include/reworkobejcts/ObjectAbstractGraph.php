<?php 

	abstract class AbstractGraph{
		
		private $graph;
		private $query_runner;
		private $flag_up;
		private $flag_down;
		private $limit;
		private $limit_track;
		
		public function AbstractGraph($runner,$up,$down,$limit){
			$this->query_runner = $runner;
			$this->flag_up 		= $up;
			$this->flag_down	= $down;
			$this->limit 		= $limit;
			$this->limit_track	= 0;
			
		}
		
		abstract public function walk($node_name,$neighbor_limit);
		
		public function getGraph(){
			return $this->graph;
		}
		
		public function getUpFlag(){
			return $this->flag_up;
		}
		
		public function getDownFlag(){
			return $this->flag_down;
		}
		
		public function getLimit(){
			return $this->limit;
		}
		
		public function __destruct(){
		
		}
	
	}

?>