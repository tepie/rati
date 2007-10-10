<?php
	
	include_once('ObjectAbstractNode.php');
	
	class NodeBasic extends AbstractNode{
		
		public function NodeCsv($runner,$neighbor_limit,$up,$down){
			parent::AbstractNode($runner,$neighbor_limit,$up,$down);
		}
		
		public function __toString(){
			return  $this->getNodeName();
		}
	
	}
	
	

?>