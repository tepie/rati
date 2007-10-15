<?php
	
	include_once('ObjectAbstractGraph.php');

	class GraphBasic extends AbstractGraph{
	
		public function GraphBasic($runner,$node_limit){
			parent::AbstractGraph($runner,$node_limit);
		}
		
		public function __toString(){
			$text = "";
			foreach($this->getGraph() as $key => $value){
				$text = $text . "$key => $value\n";
			}
			
			return $text;
		}
	}
	
	class GraphXml extends AbstractGraph{
		
		public function GraphXml($runner,$node_limit){
			parent::AbstractGraph($runner,$node_limit);
		}
		
		public function __toString(){
			$text = "";
			$text = $text . $this->getGraphXmlTop();
			$text = $text . $this->getGraphXmlDocumentHead();
			
			foreach($this->getGraph() as $key => $value){
				$text = $text . $value;
			}
			
			$text = $text . $this->getGraphXmlDocumentEnd();
			return $text;
		}
		
		private function getGraphXmlTop(){
			return utf8_encode("<?xml version='1.0' encoding='UTF-8' standalone='yes'?>\n");
		}
		
		private function getGraphXmlDocumentHead(){
			return utf8_encode("<rati>\n<datastore>\n");
		}
		
		private function getGraphXmlDocumentEnd(){
			return utf8_encode("</datastore>\n</rati>\n");
		}
	}
	
	class GraphCsv extends AbstractGraph{
		
		private $show_headings_flag;
		private $delimiter;
		private $end_line;
		
		public function GraphCsv($runner,$node_limit){
			parent::AbstractGraph($runner,$node_limit);
			$this->show_headings_flag = true;
			$this->setDelimiter("\t");
			$this->setLineEnder("\n");
		}
		
		public function __toString(){
			$text = "";
			
			if($this->getHeadingsFlag()){
				$text = $text . $this->getCsvColumnHeadings();
			}
			
			foreach($this->getGraph() as $key => $value){
				$text = $text . $value;
			}
			
			return $text;
		}
		
		public function getHeadingsFlag(){
			return $this->show_headings_flag;
		}
		
		public function getDelimiter(){
			return $this->delimiter;
		}
		
		public function setDelimiter($delimiter){
			$this->delimiter = $delimiter;
		}
		
		public function getLineEnder(){
			return $this->end_line;
		}
		
		private function setLineEnder($ender){
			$this->end_line = $ender;
		}
		
		private function getCsvColumnHeadings(){
			$text = "";
			
			$text = $text . "object name" . $this->getDelimiter();
			$text = $text . "relationship type" . $this->getDelimiter();
			$text = $text . "relationship rule" . $this->getDelimiter();
			$text = $text . "relationship value" . $this->getLineEnder();
			
			return $text;
		}
	}
	
	
	
	
	
	


?>