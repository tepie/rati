<?php

	include_once("SettingsWebApp.php");
	include_once('SettingsBranding.php');
	include_once("SettingsPerspectives.php");
	include_once('SQLQueries.php');
	
	/**
	* A searching object 
	*/
	class SearchObject{
		
		/** The QueryRunner of this object used to run datbase queries */
		private $query_runner 	= null;
		
		/**
		* Constructor
		* @param $runner the query runner object to use for running searches
		*/
		public function SearchObject($runner){
			$this->query_runner = $runner;
		}
		
		/**
		* Search for something
		* @param $search_string the search phrase
		* @param $return_as the formatting to return the results of the search as, only HTML as this time
		* @param $lower_xx the lower bound index of the search result to return
		* @param $upper the upper bound index of the search result to return
		* return formatted search results
		*/
		public function search($search_string,$lower_xx,$upper,$return_as="html"){
			$detected_perspective = $this->verifyIfPerspectiveBased($search_string);
			if($detected_perspective != False){
				//return "Ah Ha!@!!@@!~ You are trying a perspective search!<br />";
				//echo "$detected_perspective<br />";
				$search_string = $detected_perspective[1];
				$results = $this->executePerspectiveSearch($search_string,$detected_perspective[0],$lower_xx,$upper);
			} else {
				$results = $this->executeSearch($search_string,$lower_xx,$upper);
			}
			
			if($return_as == "html") {
				$formatted = $this->formatResultsAsHtml($search_string,$results);
			} else {
				$formatted = $this->formatResultsAsHtml($search_string,$results);
			}
			
			return $formatted;
		}
		
		private function verifyIfPerspectiveBased($search_string){
			global $perspective_names;
			
			$parts = split(":",$search_string);
			if(count($parts) == 2 and in_array(strtolower($parts[0]),$perspective_names)){
				return array(strtolower($parts[0]),$parts[1]);
			} else {
				return False;
			}
		}
		
		/**
		* Format the seach results as HTML
		* @param $search_string the search string used to find information
		* @param $results the search results
		* @param $highlighting flag to highlight the matched parts of the results in the HTML
		* return a html formatted string
		*/
		private function formatResultsAsHtml($search_string,$results,$highlighting=True){
			global $web_app_page_post_back_name;
			global $url_rest_node_param;
			$html = "<div class=\"search_result_heading\">\n";
			$html = $html . "Search results for: <font class=\"search_phrase\">" . htmlspecialchars($search_string) . "</font><br />\n";
			$html = $html . "</div><br />\n";
			
			if(count($results) > 0){
				foreach($results as $key => $value){
					$html = $html . "<table class=\"search_result_item\">\n";
					$html = $html . "<tr><td class=\"search_result_item\">\n";
					$html = $html . "<a href=\"$web_app_page_post_back_name?$url_rest_node_param=". urlencode($key) . "\">\n";
					//$html = $html . $results[$key]["name"];
					//$display_name = preg_replace('/\//',' ',$key);
					//$html = $html . $display_name;
					//$html = $html . $key;
					if(isset($value["perspective"])){
						$html = $html . strtoupper($value["perspective"]) . " - " . $key;
					} else {
						$html = $html . $key;
					}
					$html = $html . "</a></td></tr>\n";
					$html = $html . "<tr><td class=\"search_result_content\">";
					//$html = $html . $results[$key]["matched"];
					//$highlight = $this->highlightHtml($search_string,$value);
					$highlight = $this->highlightHtml($search_string,htmlspecialchars($value["rule"]));
					$broken_apart = $this->trimHighlightedHtml($highlight);
					//$html = $html . "$value";
					$html = $html . "$broken_apart";
					$html = $html . "</td></tr>\n";
					$html = $html . "</table>\n<br />";
					
				}
			} else {

				$html = $html . "<table class=\"search_result_item\">\n";
				$html = $html . "<tr><td class=\"search_result_item\">Nothing found</td></tr>\n";
				$html = $html . "</table>\n<br />";
			}
			
			
			return $html;
		}
		
		private function highlightHtml($search_string,$value){
			//echo htmlspecialchars($search_string);
			$parts = preg_split("/\s+/",$search_string);
			//print_r($parts);
			foreach($parts as $index => $ereg){
				//echo "Reg: $ereg"."<br />";
				if($ereg != ""){
					$quote_ereg = preg_quote($ereg);
					$value = eregi_replace($quote_ereg,"<font style=\"font-weight:bold;\">$ereg</font>",$value);
				}
			}
			
			return $value;
			
		}
		
		private function trimHighlightedHtml($highlighted){
			$font_open 		= "<font style=\"font-weight:bold;\">";
			$font_close 	= "</font>";
			$end			= strlen($highlighted);
			$pad			= 80;
			$sections_of_interest = array();
			
			$open_position = strpos($highlighted,$font_open);
			//print "starting fresh....<br />";
			while(!($open_position === False)){
				//print "open: $open_position<br />";
				$close_position = strpos($highlighted,$font_close,$open_position);
				
				//print "close: $close_position<br />";
				if($close_position === false){
					$close_position = $open_position;
				}
				
				$sub_start_pos 	= 0;
				$sub_stop_pos 	= 0;
				
				if($open_position - $pad >= 0){
					$sub_start_pos = $open_position - $pad;
				} else {
					$sub_start_pos 	= 0;
				}
				
				if($close_position + $pad + strlen($font_close) <= $end){
					$sub_stop_pos = $close_position + $pad + strlen($font_close) ;
				} else {
					$sub_stop_pos = $end;
				}
				
				//print "sub open: $sub_start_pos<br />";
				//print "sub close: $sub_stop_pos<br />";
				
				$sub_length = $sub_stop_pos - $sub_start_pos;
				$sub_text = substr($highlighted,$sub_start_pos,$sub_length);
				
				//print "sub lenght: $sub_length<br />";
				//print "sub text: ". htmlspecialchars($sub_text)."<br />";
				
				array_push($sections_of_interest,$sub_text);
				
				$open_position = strpos($highlighted,$font_open,$sub_stop_pos);
			}
			//print_r($sections_of_interest);
			return join("... ",$sections_of_interest);
		}
		
		private function executePerspectiveSearch($search_string,$perspective,$li_lower,$li_upper){
			$return = array();
			$trimmed = trim($search_string);
			$string = mysql_real_escape_string($trimmed);
			$escaped = ereg_replace("[ \t\n\r\f\v]+","%",$string);
			$escaped_p = mysql_real_escape_string($perspective);
			
			$exact_sql = "SELECT object_name as object,combined_attributes as rule, perspective FROM `search_index` ";
			$exact_sql = $exact_sql . "WHERE object_name = \"$string\" and perspective = '$escaped_p' limit 1";
			$exact_res = $this->query_runner->runQuery($exact_sql);
			$line = mysql_fetch_array($exact_res ,MYSQL_ASSOC);
			if(isset($line["object"])){
				//$return[$line["object"]] = $line["rule"];
				
				$return[$line["object"]] = array();
				$return[$line["object"]]["rule"] 		= $line["rule"];
				$return[$line["object"]]["perspective"] = $line["perspective"];
				
			}	
			
			mysql_free_result($exact_res);
			
			$sql = "SELECT object_name as object,combined_attributes as rule, perspective FROM `search_index` ";
			$sql = $sql . "WHERE combined_attributes like ('%$escaped%') collate latin1_general_ci and ";
			$sql = $sql . "perspective = '$escaped_p' order by weight desc,rank desc limit $li_lower,$li_upper";
			
			$res 		= $this->query_runner->runQuery($sql);
			while($line = mysql_fetch_array($res ,MYSQL_ASSOC)){
				//$return[$line["object"]] = $line["rule"];
				
				$return[$line["object"]] = array();
				$return[$line["object"]]["rule"] 		= $line["rule"];
				$return[$line["object"]]["perspective"] = $line["perspective"];
				
			}
		
			mysql_free_result($res);
			return $return;
		}
		
		private function executeSearch($search_string,$li_lower=0,$li_upper=30){
			$return = array();
			$trimmed = trim($search_string);
			$string = mysql_real_escape_string($trimmed);
			$escaped = ereg_replace("[ \t\n\r\f\v]+","%",$string);
			
			$exact_sql = "SELECT object_name as object,combined_attributes as rule, perspective FROM `search_index` ";
			$exact_sql = $exact_sql . "WHERE object_name = \"$string\" limit 1";
			$exact_res = $this->query_runner->runQuery($exact_sql);
			$line = mysql_fetch_array($exact_res ,MYSQL_ASSOC);
			if(isset($line["object"])){
				//$return[$line["object"]] = $line["rule"];
				
				$return[$line["object"]] = array();
				$return[$line["object"]]["rule"] 		= $line["rule"];
				$return[$line["object"]]["perspective"] = $line["perspective"];
				
			}	
			
			mysql_free_result($exact_res);
			
			$sql = "SELECT object_name as object,combined_attributes as rule, perspective FROM `search_index` ";
			$sql = $sql . "WHERE combined_attributes like ('%$escaped%') collate latin1_general_ci ";
			$sql = $sql . "order by weight desc,rank desc limit $li_lower,$li_upper";
			
			$res 		= $this->query_runner->runQuery($sql);
			while($line = mysql_fetch_array($res ,MYSQL_ASSOC)){
				//$return[$line["object"]] = $line["rule"];
				
				$return[$line["object"]] = array();
				$return[$line["object"]]["rule"] 		= $line["rule"];
				$return[$line["object"]]["perspective"] = $line["perspective"];
				
			}
		
			mysql_free_result($res);
			return $return;
		}
	
	}

?>