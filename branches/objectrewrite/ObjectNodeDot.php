<?php

	include_once('ObjectAbstractNode.php');
	
	include_once('../SettingsPerspectives.php');
	include_once('../SettingsGraph.php');
	include_once('../SettingsWebApp.php');
	include_once('../SQLQueries.php');
	
	class NodeDot extends AbstractNode{
		
		private $category;
		private $font_size;
		
		public function NodeXml($runner,$neighbor_limit,$up,$down){
			parent::AbstractNode($runner,$neighbor_limit,$up,$down);
		}
		
		public function __toString(){
			return $this->getDotNodeText($this->getNodeName());
		}
		
		public function getFontSize(){
			return $this->font_size;
		}
		
		public function setFontSize($size){
			$this->font_size = $size;
		}
		
		public function __clone(){
			parent::__clone();
			$this->setFontSize("8");
		}
		
		/*private function getReferenceDotText(){
			$text 		= "";
			$references = $this->getNeighbors();
			
			foreach($references as $node_id => $rule){
				$this->getDotNodeText($this->calculateNodeName($node_id));
				$this->getDotLinkText($node_id,$rule,"8");
				//$text = $text . "<reference name='".$rule."' oidref='".$this->calculateNodeName($node_id)."'/>\n";
			}
			
			//return $text;
		}*/
		
		private function getDotNodeText($node_name){
			global $url_rest_node_param;
			global $graphviz_string_node_attributes;
			global $graphviz_string_link_node;
			global $web_app_page_post_back_name;
			
			$text = "";
			$label 	= $node_name; //$this->getNodeName();
			$url 	= $web_app_page_post_back_name . "?$url_rest_node_param=" . urlencode($label);
			$parts 	= split("\/",$label);
			$show 	= $parts[count($parts) - 1];
			
			$color  = $this->calculateNodeColor($node_name);
			
			$text = $text . "\t" . sprintf($graphviz_string_link_node,$this->calculateNodeId($node_name));
			$text = $text . sprintf($graphviz_string_node_attributes,$show,$url,$color,$label) . "\n";
			//echo $this->getReferenceDotText();
			return $text;
		}
		
		private function calculateNodeColor($node_name){
			global $web_app_default_node_color;
			global $web_app_default_cat_color;
			
			global $perspective_category_eregs;
			global $perspective_node_color_maps;
			
			foreach($perspective_category_eregs as $index => $ereg){
				if(ereg($ereg,$node_name)){
					return $web_app_default_cat_color;
				} 
			}
			$this->calculateCategory();
			foreach($perspective_node_color_maps as $index => $map){
				if(in_array($this->getNodeCategory(),array_keys($map))){
					return $map[$this->getNodeCategory()];
				}
			}

			return $web_app_default_node_color;
		}
		
		public function getNodeCategory(){
			return $this->category;
		}
		
		/**
		* Calculate the category of this node.
		* Every object is in a category, even categories.
		* return category string
		*/
		private function calculateCategory(){	
			global $object_calculate_node_name;
			global $object_structure_name;			
			global $perspective_category_reference_rules;
			
			//echo "CATEGORY\n";
			
			$found = False;
			$temp_neighbors = $this->structureUpNeighbors();
			
			if($temp_neighbors != null){
				foreach($temp_neighbors as $nodeId => $ruleName){
					foreach($perspective_category_reference_rules as $index => $rule){
						if($ruleName == $rule){
							//echo "$ruleName vs. $rule <br />";
							$sql 	= sprintf($object_calculate_node_name,$nodeId);
							$res 	= $this->getQueryRunner()->runQuery($sql);
							$line 	= mysql_fetch_array($res,MYSQL_ASSOC);
							mysql_free_result($res);
							$this->category = $line["$object_structure_name"];
							//echo $this->name . " - " .$line["$object_structure_name"] . "<br />";
							$found = True;
							break;
						}
					}
				}
			}
			//$this->flag_up = $temp_this_flag;
			if(!$found) $this->category = "Unknown";
		}
		
	
		public function getLinkedText($link_to){
			$references = $this->getNeighbors();
			$rule = $references[$link_to];
			//echo "$link_to => $rule\n";
			return $this->getDotLinkText($link_to,$rule);
		}
		
		private function getDotLinkText($link_to,$rule){
			global $graphviz_string_link_node;
			global $graphviz_string_link_lr_arrow;
			global $graphviz_string_link_attributes;
			global $graphviz_string_links_fontsize;
			
			$use_size = $this->getFontSize();
			//echo "FONT: $use_size\n";
			// Return text container
			$text 		= "";
			$key 		= $link_to;
			$value 		= $rule;
			$node_id 	= $this->getNodeId();
			
			$switch = $this->getNodeNeighborDirectionTo($key);
			
			$left 	= $node_id;
			$right	= $key;
			
			if($switch){
				$dirType = "forward";
			} else {
				$dirType = "back";
			}
			
			$html_value = htmlentities($value, ENT_QUOTES);
			
			// Formulate the link text
			$text = $text . "\t". sprintf($graphviz_string_link_node,$left);
			$text = $text . $graphviz_string_link_lr_arrow;
			$text = $text . sprintf($graphviz_string_link_node,$right);
			$text = $text . sprintf($graphviz_string_link_attributes,$html_value,$use_size,$dirType) . "\n";

			return $text;
		}
		
		private function calculateNodeId($node_name){
			global $object_calculate_node_id;
			global $object_structure_primary_id;
			
			$escaped 	= mysql_real_escape_string($node_name);
			//$sql 		= "select id from object where name = \"$escaped\" limit 1";
			$sql 		= sprintf($object_calculate_node_id,$escaped);
			$res 		= $this->getQueryRunner()->runQuery($sql);
			$line 		= mysql_fetch_array($res,MYSQL_ASSOC);
			mysql_free_result($res);
			//$this->setNodeId($line["$object_structure_primary_id"] + 0);\
			return $line["$object_structure_primary_id"] + 0;
			
		}
	}
	
?>