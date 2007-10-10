<?php

	include_once('ObjectAbstractNode.php');
	
	include_once('../SettingsPerspectives.php');
	include_once('../SettingsGraph.php');
	include_once('../SettingsWebApp.php');
	include_once('../SQLQueries.php');
	
	class NodeDot extends AbstractNode{
		
		private $category;
		
		public function NodeXml($runner,$neighbor_limit,$up,$down){
			parent::AbstractNode($runner,$neighbor_limit,$up,$down);
			$this->calculateCategory();
		}
		
		public function __toString(){
		
			global $url_rest_node_param;
			global $graphviz_string_node_attributes;
			global $graphviz_string_link_node;
			global $web_app_page_post_back_name;
			
			$text = "";
			$label 	= $this->getNodeName();
			$url 	= $web_app_page_post_back_name . "?$url_rest_node_param=" . urlencode($label);
			$parts 	= split("\/",$label);
			$show 	= $parts[count($parts) - 1];
			$color  = $this->calculateNodeColor();
			
			$text 	= "\t" . sprintf($graphviz_string_link_node,$this->idDatabase);
			$text   = $text . " " . sprintf($graphviz_string_node_attributes,$show,$url,$color,$label) . "\n";
			
			return $text;
		}
		
		private function calculateNodeColor(){
			global $web_app_default_node_color;
			global $web_app_default_cat_color;
			
			global $perspective_category_eregs;
			global $perspective_node_color_maps;
			
			foreach($perspective_category_eregs as $index => $ereg){
				if(ereg($ereg,$this->getNodeName())){
					return $web_app_default_cat_color;
				} 
			}
			
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
	}
	
?>