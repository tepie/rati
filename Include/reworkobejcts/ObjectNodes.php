<?php
	
	/*private function getCsvColumnHeadings(){
		$text = "";
		
		$text = $text . "object name" . $this->getDelimiter();
		$text = $text . "relationship type" . $this->getDelimiter();
		$text = $text . "relationship rule" . $this->getDelimiter();
		$text = $text . "relationship value" . $this->getLineEnder();
		
		return $text;
	}*/
	
	include_once('ObjectAbstractNode.php');
	
	class NodeBasic extends AbstractNode{
		
		public function NodeBasic($runner,$neighbor_limit,$up,$down){
			parent::AbstractNode($runner,$neighbor_limit,$up,$down);
		}
		
		public function __toString(){
			return  $this->getNodeName();
		}
	
	}
	
	class NodeCsv extends AbstractNode{
		
		private $delimiter;
		private $end_line;
		
		public function NodeCsv($runner,$neighbor_limit){
			parent::AbstractNode($runner,$neighbor_limit,true,false);
			$this->setDelimiter("\t");
			$this->setLineEnder("\n");
		}
		
		public function __toString(){
			$text = "";
			$text = $text . $this->getAnnotationCsvText();			
			$text = $text . $this->getReferenceCsvText();
			
			return  $text;
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
		
		
		private function getReferenceCsvText(){
			$text 		= "";
			$references = $this->getNeighbors();
			
			foreach($references as $node_id => $rule){
				$text = $text . $this->getNodeName() . $this->getDelimiter();
				$text = $text . "reference" . $this->getDelimiter();
				$text = $text . $rule . $this->getDelimiter();
				$text = $text . $this->calculateNodeName($node_id) . $this->getLineEnder();
			}
			
			return $text;
		}
		
		private function getAnnotationCsvText(){
			$text 		= "";
			$values 	= $this->calculateNodeValueRelationships();
			
			foreach($values as $rule => $value){
				$text = $text . $this->getNodeName() . $this->getDelimiter();
				$text = $text . "annotation" . $this->getDelimiter();
				$text = $text . $rule . $this->getDelimiter();
				$text = $text . $value . $this->getLineEnder();
			}
			
			return $text;
		}
		
		
	
	}
	
	

?>