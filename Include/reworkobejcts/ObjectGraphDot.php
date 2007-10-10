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
			/*$text = $text . $this->getGraphDotDocumentHead();
			
			foreach($this->getGraph() as $key => $value){
				$text = $text . $value;
			}
			
			$text = $text . $this->getGraphDotDocumentEnd();
			*/
			
			//print_r($this->getNodesVisited());
			//return $text;
			foreach($this->getGraph() as $key => $value){
				echo $key."\n";
			}
			return "";
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
		
		public function getFontSize(){
			return $this->font_size;
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
