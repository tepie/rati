<?php

	ini_set( 'memory_limit', "32M" );
	
	include_once("Include/SettingsWebApp.php");
	include_once('Include/SettingsBranding.php');
	include_once("Include/HtmlCommon.php");
	include_once('Include/SettingsDatabase.php');
	include_once('Include/Database.php');
	include_once('Include/SQLQueries.php');
	
		
	/**
	* Transform relational XML documentents into datbase objects, attributes and relationships
	*/
	
	//http://www.w3schools.com/php/php_xml_parser_expat.asp
	
	/**
	* Delete an object's relationships
	* @param $object_id the object id to based the relationship deletions on
	*/
	function delete_object_relationships($object_id){
		global $query_runner;
		global $relationship_delete_on_object_id;
		/** Escape the input */
		$escaped 	= mysql_real_escape_string($object_id) + 0;
		/** Generate the sql */
		$delete_sql = sprintf($relationship_delete_on_object_id,$escaped);//"delete from `relationship` where object_id = $escaped";
		/** Execute the delete */
		$res 		= $query_runner->runQuery($delete_sql);
	}
	
	/**
	* Check to see if relationships exist to an object 
	* @param $object_id the object id to check on 
	* return true if the object has relationships, false if not
	*/
	function relationship_object_exists($object_id){
		global $query_runner;
		global $all_structure_as_count;
		global $relationship_count_on_object_id;
		
		/** Escape the input */
		$escaped 	= mysql_real_escape_string($object_id);
		/** Generate the check sql */
		$check_sql 	= sprintf($relationship_count_on_object_id,$escaped);//"select count(id) as count from relationship where object_id = $escaped";
		/** Execute the SQL */
		$res 		= $query_runner->runQuery($check_sql);
		/** Fetch the result line */
		$line		= mysql_fetch_array($res ,MYSQL_ASSOC);
		/** Free the result */
		mysql_free_result($res);
		/** Return */
		if(isset($line["$all_structure_as_count"]) and $line["$all_structure_as_count"] + 0 > 0){
			return True;
		} else {
			return False;
		}
	}
	
	/**
	* Check to make sure a value relationship exists between an object and an attribute 
	* @param $object_id the object id
	* @param $attribute_id the attribute id 
	* return true if there is a relationship, false otherwise
	*/
	function relationship_value_exists($object_id,$attribute_id){
		global $query_runner;
		global $all_structure_as_count;
		global $relationship_value_object_attribute_pair;
		
		$escaped_object_id 		= mysql_real_escape_string($object_id) + 0;
		$escaped_attribute_id 	= mysql_real_escape_string($attribute_id) + 0;
		$check_sql 				= sprintf($relationship_value_object_attribute_pair,$escaped_object_id,$escaped_attribute_id);
		$res 					= $query_runner->runQuery($check_sql);
		$line					= mysql_fetch_array($res ,MYSQL_ASSOC);
		
		mysql_free_result($res);
		
		if(isset($line["$all_structure_as_count"]) and $line["$all_structure_as_count"] + 0 > 0){
			return True;
		} else {
			return False;
		}
	}
	
	/**
	* Add a reference relationship based on an object, attribute and the reference value of the relationship
	* @param $object_id the object id
	* @param $attribute_id the attribute id
	* @param $reference_id the reference id (pointer to another object 
	* return the relationship id of the newly inserted relationship
	*/
	function relationship_reference_add($object_id,$attribute_id,$reference_id){
		global $query_runner;
		global $relationship_reference_insert;
		
		$escaped_object_id 		= mysql_real_escape_string($object_id) + 0;
		$escaped_attribute_id 	= mysql_real_escape_string($attribute_id) + 0;
		$escaped_reference_id 	= mysql_real_escape_string($reference_id) + 0;
		$insert_sql 			= sprintf($relationship_reference_insert,$escaped_object_id,$escaped_attribute_id,$escaped_reference_id);
		$res 					= $query_runner->runQuery($insert_sql);
		$relationship_id 		= mysql_insert_id();
		
		return $relationship_id;
	}
	
	/**
	* Add a value relationship based on an object, an attribute and the value of the attribute (Text)
	* @param $object_id the object id
	* @param $attribute_id the attribute id
	* @param $value the value of the relationship
	* return the relationship id of the newly inserted relationship 
	*/
	function relationship_value_add($object_id,$attribute_id,$value){
		global $query_runner;
		global $relationship_value_insert;
		
		$escaped_object_id 		= mysql_real_escape_string($object_id) + 0;
		$escaped_attribute_id 	= mysql_real_escape_string($attribute_id) + 0;
		$escaped_value 			= mysql_real_escape_string($value);
		$insert_sql 			= sprintf($relationship_value_insert,$escaped_object_id,$escaped_attribute_id,$escaped_value);
		$res 					= $query_runner->runQuery($insert_sql);
		$relationship_id 		= mysql_insert_id();
		
		return $relationship_id + 0;
	}
	
	/**
	* Check to see if an object already exists 
	* @param $object_name the name of the object you want to check
	* return the id of the object if it exists, or null if it doesn't
	*/
	function object_exists($object_name){
		global $query_runner;
		global $object_calculate_node_id;
		global $object_structure_primary_id ;
		
		$escaped 	= mysql_real_escape_string($object_name);
		$check_sql 	= sprintf($object_calculate_node_id,$escaped); //"select id from `object` where name = \"$escaped\" limit 1";
		$res 		= $query_runner->runQuery($check_sql);
		$line		= mysql_fetch_array($res ,MYSQL_ASSOC);
		
		mysql_free_result($res);
		
		if(isset($line[$object_structure_primary_id])){
			return $line[$object_structure_primary_id] + 0;
		} else {
			return null;
		}
	}
	
	/**
	* Add an object to the database
	* @param $object_name the name of the object to add
	* return the object id of the newly inserted object 
	*/
	function object_add($object_name){
		global $query_runner;
		global $object_insert_new;
		/** Escape the input */
		$escaped 		= mysql_real_escape_string($object_name);
		/** Generate the insert SQL */
		$insert_sql 	= sprintf($object_insert_new,$escaped); //"insert into `object` (`ID`,`NAME`) values (NULL , '$escaped');";
		/** Execute the insert */
		$res 			= $query_runner->runQuery($insert_sql);
		/** Get the insert id result */
		$object_id 		= mysql_insert_id();
		/** Return the id */
		return $object_id + 0;
	}
	
	/**
	* Check to see if an attribute exists
	* @param $attribute_name the name of the attribute to add
	* return the attribute id of the newly created attribute
	*/
	function attribute_exists($attribute_name){
		global $query_runner;
		global $attribute_structure_primary_id ;
		global $attribute_calculate_id;
		
		/** Escape the input */
		$escaped 	= mysql_real_escape_string($attribute_name);
		/** Create the check SQL */
		$check_sql	= sprintf($attribute_calculate_id,$escaped); //"select id from `attribute` where name = \"$escaped\" limit 1";
		//echo "\n$check_sql";
		/** Execute the Query */
		$res 		= $query_runner->runQuery($check_sql);
		/** fetch the result line */
		$line		= mysql_fetch_array($res ,MYSQL_ASSOC);
		/** Free the result */
		mysql_free_result($res);
		
		/** Return the result of the check */
		if(isset($line[$attribute_structure_primary_id])){
			$id = $line[$attribute_structure_primary_id] + 0;
			if($id > 0){
				return $id;
			} else {
				return null;
			}
		} else {
			return null;
		}
	}
	
	/** 
	* Add an attribute to the database 
	* @param $attribute_name the name of the attribute
	* return the attribute id of the new inserted attribute 
	*/
	function attribute_add($attribute_name){
		global $query_runner;
		global $attribute_insert_new;
		/** Escape the input */
		$escaped 		= mysql_real_escape_string($attribute_name);
		/** Create the insert SQL */
		$insert_sql 	= sprintf($attribute_insert_new, $escaped); //"insert into `attribute` (`ID`,`NAME`) values (NULL , '$escaped')";'
		/** Execute the insert */
		$res 			= $query_runner->runQuery($insert_sql);
		/** Fetch the id of the last insert */
		$attribute_id 	= mysql_insert_id();
		/** Return the id */
		return $attribute_id + 0;
	}
	
	/** 
	*Handle the start tags in an the Ab Initio XML 
	* @param $parser the parser xml object
	* @param $name the name of the current element
	* @param $attrs the attributes of the current element
	*/
	function handle_start_tag($parser,$name,$attrs){
		global $query_runner;
		global $attribute_id;
		global $object_id;
		
		/** for each attribute, add it to the database */
		foreach($attrs as $key => $value){
			/** Add the current attrubute name to the annotations array */
			
			/** The attribute id of the current attribute */
			$attribute_id = attribute_exists($key);
			/** If the id is null, it doesn't exist in the database, so add it */
			//echo "\n$key -> $attribute_id";
			if($attribute_id == null){
				$attribute_id = attribute_add($key);
			}
			
			/* Check for non-path values of the attributes */
			if(!ereg("^/",$value)){
				$attribute_id = attribute_exists($value);
				//echo "\n$value -> $attribute_id";
				if($attribute_id == null){ 
					//echo "\nThis is the reality of your logic";
					$attribute_id = attribute_add($value);
				}
			}
		}
		
		/** Hitting the "object" element in the document */
		if($name == "object" and isset($attrs["oid"]) and isset($attrs["category"])){
			/** Set the current object */
			$CURRENT_OBJECT = $attrs["oid"];
			/** Check for this object's existence in the database */
			$object_id = object_exists($CURRENT_OBJECT);
			/** If the object doesn't exist, the create it */
			if($object_id == null){
				$object_id = object_add($CURRENT_OBJECT);
			}
			/** If relationships exist for this object, remove them */
			if(relationship_object_exists($object_id)){
				delete_object_relationships($object_id);
			}
		}
		
		/** Hitting the "reference" element in the document */
		if($name == "reference" and isset($attrs["oidref"])){
			/** Determine the value of the reference */
			$reference_name = $attrs["oidref"];
			/** Calculate the existence of this value reference */
			$reference_id = object_exists($reference_name);
			if($reference_id == null){
				$reference_id = object_add($reference_name);
			}
			
			/** Determine the attribute */
			$attribute_id = attribute_exists($attrs["name"]);
			if($attribute_id == null){
				$attribute_id = attribute_add($attrs["name"]);
			}
			
			/** Add the relationship between the object, attribute and its reference */
			$relationship_id = relationship_reference_add($object_id,$attribute_id,$reference_id);
		}
	}
	
	/** 
	* Handle the end tag of the Ab Initio XML 
	* @param $parser the parser object
	* @param $name the name of the element
	*/
	function handle_end_tag($parser,$name){
		// Do nothing on the end tag
		global $attribute_id;
		global $object_id;
		global $object_data;
		
		$data = $object_data[$object_id];
		if($data != ""){
			if(isset($object_id) and isset($attribute_id) and !relationship_value_exists($object_id,$attribute_id)){
				/*if($object_id == "268558"){
					print "final: " .$data ."\n";
				}*/
				
				/** Ignore bad values, not sure why this is happening */
				if(preg_match("/(^\\n)/",$data) == 0){
					$relationship_id = relationship_value_add($object_id,$attribute_id,$data);
				} else{
					echo "Bad char_data: $value\n";
				}
			}
		}
	}
	
	/** 
	* Handle the attribute values of the XML data 
	* @param $parser the parser object
	* @param $data the data contained in the element tags, not attributes
	*/
	function char_data($parser,$data){
		//global $query_runner;
		global $attribute_id;
		global $object_id;
		global $object_data;
		
		if(!isset($object_data[$object_id])){
			$object_data[$object_id] = "";
		}
		
		$object_data[$object_id] = $object_data[$object_id] . "$data";		
	}
	
	function initiate_load(){
		global $query_runner;
		global $database_start_transaction;
		global $database_disable_autocommit;
		$query_runner->runQuery("$database_disable_autocommit");
		$query_runner->runQuery("$database_start_transaction");
	}
	
	function finalize_load(){
		global $query_runner;
		global $database_commit_transaction;
		global $database_analyze_attribute;
		global $database_analyze_object;
		global $database_analyze_relationship;
		
		/** Commit the current transaction */
		$query_runner->runQuery($database_commit_transaction);
		/** Analyze the tables */
		//$query_runner->runQuery($database_analyze_attribute);
		//$query_runner->runQuery($database_analyze_object);
		//$query_runner->runQuery($database_analyze_relationship);
	}
	
	function handle_http_string_load($xml_string){
		global $FILEENCODING;
		global $_GET;
		/** Initialize the XML parser */
		$parser = xml_parser_create($FILEENCODING);
		/** Set the case folding option off */
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		/** Specify element handler */
		xml_set_element_handler($parser,"handle_start_tag","handle_end_tag");
		/** Specify data handler */
		xml_set_character_data_handler($parser,"char_data");
		
		$cleanData			= ereg_replace("\\\'","'",$xml_string);
		$moreCleanData		= ereg_replace('\\\"','"',$cleanData);
		//echo "<!-- ". $xml_string ."-->";
	
		xml_parse($parser,$moreCleanData,true) or 
			die (sprintf("<Result>XML Error: %s at line %d at column %d\nXML Error: %s</Result>", 
				xml_error_string(xml_get_error_code($parser)),
				xml_get_current_line_number($parser),xml_get_current_column_number($parser),$data));
		
		//header("HTTP/1.0 202 Import XML Accepted");
		//echo "<Result>Success</Result>";
		return true;
	}
	
	function handle_command_line_load(){
		global $argv;
		global $argc;
		global $query_runner;
		global $FILEENCODING;
		
		foreach($argv as $index => $input_xml){
			/** skip the name of this command */
			if($index > 0){
				/** Initialize the XML parser */
				$parser = xml_parser_create($FILEENCODING);
				/** Set the case folding option off */
				xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
				/** Specify element handler */
				xml_set_element_handler($parser,"handle_start_tag","handle_end_tag");
				/** Specify data handler */
				xml_set_character_data_handler($parser,"char_data");
				
				/** Open XML file stream*/
				$fp = fopen($input_xml,"rb") or die("IOError: Failed opening \"$input_xml\"");
				
				/** Read data */
				//						     8388608
				while ($data = fread($fp,1048576)){
					/** strip the backslaches from the string */
					$stripped 		= stripslashes($data);
					unset($data);
					/** remove the special html character encoding */
					//$html_removed 	= html_entity_decode($stripped,ENT_QUOTES);
					//unset($stripped);
					/** remove the whitespace between xml entities >\s+< */
					$between_xml_str = preg_replace('/>\s+</', '><', $stripped);
					/** Remove other white space in the xml */
					//unset($html_removed);
					$str 	= preg_replace('/\s+/', ' ', $between_xml_str);
					unset($between_xml_str);
					// Escape ampersands that aren't part of entities.
				    $Contents = preg_replace('/&(?!\w{2,6};)/', '&amp;', $str);

				    // Remove all non-visible characters except SP, TAB, LF and CR.
				    $str = preg_replace('/[^\x20-\x7E\x09\x0A\x0D]/', "\n", $Contents);
					//$quot 		= preg_replace('/\&quot\;/','"',$all_white);
					//unset($all_white);
					//$str 		= preg_replace('/\&quote\;/','"',$quot);
					//echo $str;
					//exit();
					if(!xml_parse($parser,$str,feof($fp))){
						$handle = fopen("error", "wb");
						fwrite($handle,$str);
						fclose($handle);
						die (sprintf("XML Error: %s at line %d at column %d\nXML Error: %s", 
							xml_error_string(xml_get_error_code($parser)),
							xml_get_current_line_number($parser),xml_get_current_column_number($parser),$str));
					}
					//unset($str);
					
				}
				/** Close the file stream */
				fclose($fp);
				/** Free the XML parser */
				xml_parser_free($parser);
			}
		}
	}
	
	/** Setup the database connection, provide the host, username and password */
	$db_connection 	= new DbConnectionHandler("$mysql_database_host",
		"$mysql_database_import_user",
		"$mysql_database_import_passwd"
	);
	
	// Verify our database connection link
	// If it isn't setup, set it up and select 
	// the desired database to work from, being "metawarehouse"
	if(!ISSET($db_connection->link)){
		$db_connection->setupDbLink();
		/** The select database results */
		$x = $db_connection->selectDb("$mysql_database_name");
	}
	
	/** Create a QueryRunner to run queries on the database */
	$query_runner 	= new QueryRunner();
	
	$FILEENCODING 			= "iso-8859-1";
	$attribute_id			= null;
	$object_id				= null;
	$object_data			= array();
	
	if($argc > 1){
		initiate_load();
		/** this is a command line based load, handle it */
		handle_command_line_load();
		/** finalize the load process */
		finalize_load();
		if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 
		exit();
	} elseif(isset($_GET["$url_rest_import_xml_string_param"])) {
		initiate_load();
		/** this is a http load */
		header('Content-type: text/xml');
		$res = handle_http_string_load($_GET["$url_rest_import_xml_string_param"]);
		if($res) {
			echo "<Result>Success</Result>";
		}
		finalize_load();
		if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 
		exit();
	} else {
		if($db_connection->getDbLink() and $x){ $db_connection->closeLink();} 
		/** there seems to be no importing going on, explain the process to import */
		echo commonHtmlPageHead("Web XML Import API");
		echo commonHtmlPlainHeader();	
?>
<br />
<div class="about_section_heading">How do I import data into Rati?</div>
<div class="about_section_content">
<br />
It would make sense to have the ability to allow users to add their own content
to a relational system in order to allow them to build off of existing concepts
to give them more meaning, or add more content to again give more meaning. Rati
allows this functionality via a web based service. This page documents how to use
that service. 
<br /><br />
</div>
<div class="about_section_heading">Import XML Schema</div>
<div class="about_section_content">
<br />
The following is a link to a sample XML Schema. This sample is specific to 
Ab Initio and its XML extract structure. This basic structure applies to all
relational data, regardless of the annotation rules and reference rule names are.
To fit this schema to your own needs, just change the annotation and reference
rule names. Just be sure to manage your relationships accordingly. 
<br /><br />
<ol>
	<li><a href="./Doc/AbInitioCategorySchema.xsd" alt="Ab Initio Import XML Schema">
	Ab Initio Import XML Schema</a>
	</li>
</ol>
</div>
<div class="about_section_heading">Representational State Transfer Interface</div>
<div class="about_section_content">
<br />
Representational State Transfer (REST) is a way to interact with a web service 
by means of manipulating the URL string of a web resource. Rati accepts imports
into the system in this fashion in the following mannor:
<br /><br />
<ol>
	<li><code><?php echo "$web_app_page_import_name"; ?>?<?php echo "$url_rest_import_xml_string_param"; ?>=<?php echo htmlspecialchars("<valid xml document string>"); ?></code></li>
	<li><code><?php echo "$web_app_page_import_name"; ?>?xml_url=<?php echo htmlspecialchars("<url to a valid xml document>"); ?></code> (Coming Soon!)</li>
</ol>
Where the "<code>valid xml document</code>" follows the acceptable import XML schema mentioned above.
<br /><br />
Only <code>GET</code> HTTP operations are supported via this REST interface.
<br /><br />
</div>
<div class="about_section_heading">Import Example</div>
<div class="about_section_content">
<br />
The following is the XML document to be imported:
<pre><code>
<?php 
	$sample_xml = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>
<!-- Document Name -->
<eda_department><datastore>
<!-- Enterprise Data Architecture Main Object -->
<object category='Department' oid='Enterprise Data Architecture'>
<!-- Give this object a reference to a category object -->
<reference name='categoryOID' oidref='Department'/>
<!-- Give this object a reference to a manager object -->
<reference name='managerOID' oidref='Thomas R Mitchell'/>
<!-- Give this object a reference to a department object -->
<reference name='departmentOID' oidref='EDA - PROJECT DEVELOPMENT CLE' />
<!-- Give this object a value assignment for its name -->
<annotation name='name'>Enterprise Data Architecture</annotation>
<!-- Give this object a value assignment for its path -->
<annotation name='path'>Enterprise Data Architecture</annotation>
</object>
</datastore></eda_department>";
	
	echo htmlspecialchars("$sample_xml");
?>
</code></pre>
The following is the URL string to import this XML:<br /><br />
<code>
<?php 
	echo "$web_app_page_import_name?$url_rest_import_xml_string_param=" . urlencode($sample_xml); 
?>
</code>
<br /><br />
</div>

<?php 
	echo commonHtmlPageFooter(); 
}
?>