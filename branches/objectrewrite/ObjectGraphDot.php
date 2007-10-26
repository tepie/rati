<?php

	include_once('ObjectAbstractGraph.php');
	include_once('../SettingsDatabase.php');
	include_once('../SettingsGraph.php');
	
	class GraphDot extends AbstractGraph{
		
		private $font_size;
		private $graph_direction;
		
		public function GraphDot($runner,$node_limit){
			parent::AbstractGraph($runner,$node_limit);
			$this->setFontSize("8");
			$this->setGraphDirection("LR");
		}
		
		public function __toString(){
			$text = "";
			
			$text = $text . $this->getGraphDotDocumentHead();
			
			$nodes = $this->getGraph();
			$keys = array_keys($nodes);
			$root = $nodes[$keys[0]];
			$text = $text . $root;
			array_shift($keys);
			foreach($keys as $index=>$node_id){
				$text = $text . $nodes[$node_id]; //$value;
				$text = $text . $root->getLinkedText($node_id);
			}
			
			$text = $text . $this->getGraphDotDocumentEnd();
			
			return $text;
		}
		
		public function getFontSize(){
			return $this->font_size;
		}
		
		public function setFontSize($size){
			$this->font_size = $size;
		}
		
		public function setGraphDirection($direction){
			$this->graph_direction = $direction;
		}
		
		public function getGraphDirection(){
			return $this->graph_direction;
		}
		
		private function getGraphDotDocumentHead(){
			global $graphviz_string_heading_line;
			global $graphviz_string_graph_attributes;
			global $graphviz_string_nodes_attribute;
			
			$heading = $graphviz_string_heading_line;
			$heading = $heading . "\t". sprintf($graphviz_string_graph_attributes,
				$this->getGraphDirection(),
				$this->getCurrentRoot()) . "\n";
			$heading = $heading . sprintf($graphviz_string_nodes_attribute,$this->getFontSize());
			
			return $heading;
		}
		
		private function getGraphDotDocumentEnd(){
			global $graphviz_string_footing_line;
			return $graphviz_string_footing_line;
		}
	}

?>