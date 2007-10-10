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