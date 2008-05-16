<?php
	
	//** All structure variables
	// The label to give count queries
	/** Queries that return count use this variable in the as clause */
	$all_structure_as_count				= "count";
	
	//** The Object Table Structure
	/** The Object Table Name */
	$object_structure_table_name		=	"object";
	/** The Object Table Primary Key */
	$object_structure_primary_id 		=	"id";
	/** The Object Table Name */
	$object_structure_name				= 	"name";
	/** The Object table primary key index name */
	$object_structure_index_primary		= "primary";
	/** The object table unique name index */
	$object_structure_index_unique_name 	= "unique name";
	/** The object table full text index on name */
	$object_structure_index_full_text_name = "name";

	/** The Object Table Structure Array */
	$object_structure_table				= array($object_structure_primary_id ,$object_structure_name);
	
	// ** The Attribute Table Structure
	/** The Attribute Table Name */
	$attribute_structure_table_name		= "attribute";
	/** The Attribute table primary key */
	$attribute_structure_primary_id 	=	"id";
	/** The Attribute table name */
	$attribute_structure_name			= 	"name";
	/** The Attributetable primary key index name */
	$attribute_structure_index_primary		= "primary";
	/** TheAttribute table unique name index */
	$attribute_structure_index_unique_name 	= "unique name";
	/** The Attribute table full text index on name */
	$attribute_structure_index_full_text_name = "name";
	/** The Attribute Table Structure Array */
	$attribute_structure_table			= array($attribute_structure_primary_id,$attribute_structure_name); 
	
	// ** Relationship table structures
	/** The relationship table name */
	$relationship_structure_table_name		= "relationship";
	/** The relationship table primary id column */
	$relationship_structure_primary_id		= "id";
	/** The relationship object id key */
	$relationship_structure_object_fk   	= "object_id";
	/** The relationship attribute id key */
	$relationship_structure_attribute_fk	= "attribute_id";
	/** The relationship value */
	$relationship_structure_relation_value 	= "value";
	/** The relationship reference key */
	$relationship_structure_reference_fk 	= "reference";
	/** The relationship primary key index */
	$relationship_structure_index_primary	= "primary";
	/** the relationship table reference key index */
	$relationship_structure_index_reference = "reference";
	/** the relationship table object id index */
	$relationship_structure_index_object	= "object_index";
	/** the relationship table attribute index */
	$relationship_structure_index_attribute = "attribute_index";
	/** the relationship table full text value index */
	$relationship_structure_index_full_text_value = "value";
	
	/** The relationship table structure array */
	$relationship_structure_table			= array($relationship_structure_primary_id,$relationship_structure_object_fk,$relationship_structure_attribute_fk,$relationship_structure_relation_value,$relationship_structure_reference_fk);
	
	//** Database wide queries
	/** Start a datbase transaction */
	$database_disable_autocommit 	= "SET AUTOCOMMIT=0;";
	$database_start_transaction 	= "START TRANSACTION;";
	$database_commit_transaction 	= "COMMIT;";
	$database_analyze_attribute		= "OPTIMIZE TABLE `$attribute_structure_table_name`;";
	$database_analyze_object		= "OPTIMIZE TABLE `$object_structure_table_name`;";
	$database_analyze_relationship	= "OPTIMIZE TABLE `$relationship_structure_table_name`;";
	
	//** Object table queries
	/** Calculate an object node name, expects an id as input */
	$object_calculate_node_name = 'select ' . $object_structure_name .' from '. $object_structure_table_name . ' use index ('.$object_structure_index_primary.')where '.$object_structure_primary_id.' = %s limit 1';
	/** Calculate the node id, expects a name as input */
	$object_calculate_node_id   = 'select ' . $object_structure_primary_id .' from ' . $object_structure_table_name. ' use index (`'.$object_structure_index_unique_name.'`) where ' . $object_structure_name . ' = "%s" limit 1';
	/** Inser a new object */
	$object_insert_new 			= 'insert into `'.$object_structure_table_name.'` (`'.$object_structure_primary_id .'`,`'.$object_structure_name.'`) values (NULL , \'%s\')';
	
	//** Attribute table queries
	/** Calculate an attribute id from name */
	$attribute_calculate_id 	= 'select '.$attribute_structure_primary_id .' from `'.$attribute_structure_table_name.'` use index (`'.$attribute_structure_index_unique_name.'`) where '.$attribute_structure_name.' = "%s" limit 1';
	/** Insert new attribute */
	$attribute_insert_new 		= 'insert into `'.$attribute_structure_table_name.'` (`'.$attribute_structure_primary_id.'`,`'.$attribute_structure_name.'`) values (NULL , \'%s\')';
	/** Select all attributes */
	$attribute_select_all		= 'select * from '.$attribute_structure_table_name.' use index ('.$attribute_structure_index_primary.')';
	
	//** Relationship import queries
	/** Delete relationships with matching object id */
	$relationship_delete_on_object_id 	= 'delete from `'.$relationship_structure_table_name.'` where object_id = %s';
	/** Count the relationships with an object id match */
	$relationship_count_on_object_id 	= 'select count('.$relationship_structure_primary_id.') as '.$all_structure_as_count.' from '.$relationship_structure_table_name.' use index ('.$relationship_structure_index_primary.','.$relationship_structure_index_object.') where '.$relationship_structure_object_fk .' = %s';
	/** Insert relational reference */
	$relationship_reference_insert	= 'insert into `'.$relationship_structure_table_name.'` (`'.$relationship_structure_primary_id.'`,`'.$relationship_structure_object_fk .'`,`'.$relationship_structure_attribute_fk.'`,`'.$relationship_structure_relation_value.'`,`'.$relationship_structure_reference_fk.'`)';
	$relationship_reference_insert	= $relationship_reference_insert . "values (NULL,%s,%s,NULL,%s)";
	/**Insert relational value */
	$relationship_value_insert		= 'insert into `'.$relationship_structure_table_name.'` (`'.$relationship_structure_primary_id.'`,`'.$relationship_structure_object_fk .'`,`'.$relationship_structure_attribute_fk.'`,`'.$relationship_structure_relation_value.'`,`'.$relationship_structure_reference_fk.'`)';
	$relationship_value_insert 		= $relationship_value_insert . 'values (NULL,%s,%s,\'%s\',0)';
	
	//** Relationship count references queries
	/** Count the references to an object */
	$relationship_count_references_to_object = 'select count('.$relationship_structure_primary_id.') as '.$all_structure_as_count.' from '.$relationship_structure_table_name.' use index ('.$relationship_structure_index_primary.','.$relationship_structure_index_object.') where '.$relationship_structure_object_fk.' = %s and '.$relationship_structure_reference_fk.' != 0 and '.$relationship_structure_relation_value.' is NULL';
	
	/** Count the reference from an object */
	$relationship_count_references_from_object = 'select count('.$relationship_structure_object_fk .') as '.$all_structure_as_count.' from '.$relationship_structure_table_name.' use index ('.$relationship_structure_index_object.','.$relationship_structure_index_reference.') where '.$relationship_structure_reference_fk.' = %s and '.$relationship_structure_relation_value.' is NULL';
	
	/** Count the value relationships of a object attribute pair */
	$relationship_value_object_attribute_pair = 'select count('.$relationship_structure_primary_id.') as '.$all_structure_as_count.' from '.$relationship_structure_table_name.' use index ('.$relationship_structure_index_primary.','.$relationship_structure_index_object.','.$relationship_structure_index_attribute.') where '.$relationship_structure_object_fk.'= %s ';
	$relationship_value_object_attribute_pair = $relationship_value_object_attribute_pair . 'and '.$relationship_structure_attribute_fk.' = %s and '.$relationship_structure_relation_value.' is not NULL and '.$relationship_structure_reference_fk.' = 0';
	
	
	//** Relationship value queries
	/** Calculate the value attributes of an object */
	$relationship_value_attributes = 'select a.'.$attribute_structure_name.',r.'.$relationship_structure_relation_value.' from '.$relationship_structure_table_name.' as r,('.$attribute_select_all.') as a where r.'.$relationship_structure_relation_value.' is not NULL and r.'.$relationship_structure_reference_fk.' = 0 and r.'.$relationship_structure_object_fk.' = %s and a.'.$attribute_structure_primary_id .' = r.'.$relationship_structure_attribute_fk .' order by a.'.$attribute_structure_name; 
	// ** Relationship neighbor queries
	/** Calculate the reference neighbors of an object */
	$relationship_reference_neighbors 	= 'select result.'.$relationship_structure_reference_fk.',attr.'.$attribute_structure_name.' from (select '.$relationship_structure_attribute_fk.','.$relationship_structure_reference_fk.' from '.$relationship_structure_table_name.' use index ('.$relationship_structure_index_object.','.$relationship_structure_index_reference.') where '.$relationship_structure_object_fk.' = %s and '.$relationship_structure_reference_fk.' != 0) as result,('.$attribute_select_all.') as attr, object as o where result.'.$relationship_structure_attribute_fk.' = attr.'.$attribute_structure_primary_id.' and attr.'.$attribute_structure_name.' != "arulesOID" and result.reference = o.id order by o.name limit %s';
	//echo "<br />$relationship_reference_neighbors";
	/** Calculate the neighbors referencing an object */
	$relationship_referencing_neighbors = 'select o.'.$object_structure_primary_id.',a.'.$attribute_structure_name.' from '.$object_structure_table_name.' as o use index ('.$object_structure_index_primary.'), (select '.$relationship_structure_object_fk.','.$relationship_structure_attribute_fk.' from '.$relationship_structure_table_name.' use index ('.$relationship_structure_index_reference.') where '.$relationship_structure_reference_fk.' = %s) as r, ('.$attribute_select_all.') as a  where r.'.$relationship_structure_object_fk.' = o.'.$object_structure_primary_id.' and r.'.$relationship_structure_attribute_fk.' = a.'.$attribute_structure_primary_id.' and a.'.$attribute_structure_name.' != "arulesOID" order by o.name limit %s'; 
	//echo "<br />$relationship_referencing_neighbors";
	/** Calculate the reference count */
	$relationship_reference_count		= 'select count(o.'.$object_structure_primary_id.') as '.$all_structure_as_count.' from '.$object_structure_table_name.' as o use index ('.$object_structure_index_primary.'), '.$relationship_structure_table_name.' as r use index ('.$relationship_structure_index_object.','.$relationship_structure_index_reference.') where o.'.$object_structure_primary_id .' = %s and r.'.$relationship_structure_object_fk.' = o.'.$object_structure_primary_id .' and r.'.$relationship_structure_reference_fk .' = %s limit 1';
?>