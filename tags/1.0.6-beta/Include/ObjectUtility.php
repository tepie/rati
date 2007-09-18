<?php

	include_once("SettingsGraph.php");
	
	/**
	* A set of utility functions for this and that 
	*/
	class UtilityObject{
	
		/**
		* Constructor
		*/
		public function UtilityObject(){}
		
		
		/**
		* Determine the base path of a "/" seperated path,
		* the base being the part part of the string seperated by "/"
		* @param $full_path the node to determine the base name of
		* return the last part of the split path
		*/
		function parsePathBaseName($full_path){
			$parts = split("\/",$full_path);
			return $parts[count($parts) -1 ];
		
		}
		
		/**
		* Set up the needed directories to create files in 
		*/
		function setupFileDirectories(){
			global $directory_dot_graph;
			global $directory_dot_map;
			global $directory_dot_img;	
			
			if(!file_exists($directory_dot_graph)){
				$result_dot = mkdir($directory_dot_graph,0755);
			}
			
			if(!file_exists($directory_dot_map)){
				$result_map = mkdir($directory_dot_map,0755);
			}
			
			if(!file_exists($directory_dot_img)){
				$result_img = mkdir($directory_dot_img,0755);
			}
			
			//if($result_dot and $result_map and $result_img) return True;
			//else return False;
			return True;
		}
		
		/** Check a file for existence or age 
		* @param $name the name of the file to check
		* @param $time the time to compare the file age against, default 1000
		* return If a file does not exist return false
		* return If a file exists and its age is younger then the time, return false
		* return If a file exists and its age is older then the time, return true
		*/
		function checkFile($name,$time){
			// Either the file doesn't exist, or it does and the time is greater then a time
			if(file_exists($name) and (filemtime($name) - time()) < $time){
				return True;
			} else{
				False;
			}
		}
		
	}

?>