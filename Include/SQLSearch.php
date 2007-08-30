<?php
	include_once('SQLQueries.php');
	
	$search_index_structrue_name 			= "search_index";
	$search_index_structure_primary_key 	= "id";
	$search_index_structure_object 			= "object_name";
	$search_index_structure_combined_attributes = "combined_attributes";
	$search_index_structure_rank			= "rank";
	$search_index_structure_weight			= "weight";
	$search_index_structure_perspective		= "perspective";
	
	
	$search_index_left_union = "select o.$object_structure_name as object,
a.$attribute_structure_name as attribute,
r.$relationship_structure_relation_value as result,
0 as $all_structure_as_count 
from $relationship_structure_table_name as r,
$object_structure_table_name as o, 
$attribute_structure_table_name as a 
where o.$object_structure_primary_id = r.$relationship_structure_object_fk and 
a.$attribute_structure_primary_id = r.$relationship_structure_attribute_fk and 
r.$relationship_structure_relation_value is not null";
		
	$search_index_right_union = "select o.$object_structure_name as object,
a.$attribute_structure_name as attribute,
oo.$object_structure_name as result, 
1 as $all_structure_as_count from 
$relationship_structure_table_name as r,
$object_structure_table_name as o, 
$attribute_structure_table_name as a,
(select * from $object_structure_table_name) as oo where 
o.$object_structure_primary_id = r.$relationship_structure_object_fk and 
a.$attribute_structure_primary_id = r.$relationship_structure_attribute_fk and 
r.$relationship_structure_relation_value is null and 
r.$relationship_structure_reference_fk = oo.$object_structure_primary_id";
	
	$search_index_concat_select = "select results.object,
group_concat(
concat_ws(\"=\",results.attribute,results.result) separator '; '
) 
as rule, sum(results.$all_structure_as_count) as $all_structure_as_count 
from 
(($search_index_left_union) union ($search_index_right_union)) 
as results group by results.object";
		
	$search_index_replace = "replace into $search_index_structrue_name 
($search_index_structure_object,$search_index_structure_combined_attributes,$search_index_structure_rank) 
$search_index_concat_select";
		
	$search_index_select_category_for_weight = "select obj_rel.$object_structure_name,
object.$object_structure_name as category,
i.$search_index_structure_rank 
from $object_structure_table_name,
$search_index_structrue_name as i,
(select $object_structure_table_name.$object_structure_name,
$relationship_structure_table_name.$relationship_structure_reference_fk from 
$relationship_structure_table_name,
$object_structure_table_name,$attribute_structure_table_name 
where $attribute_structure_table_name.$attribute_structure_name = '%s'
and $attribute_structure_table_name.$attribute_structure_primary_id = $relationship_structure_table_name.$relationship_structure_attribute_fk and
$relationship_structure_table_name.$relationship_structure_object_fk = object.$object_structure_primary_id and
$relationship_structure_table_name.$relationship_structure_reference_fk != 0) as obj_rel 
where obj_rel.$relationship_structure_reference_fk = $object_structure_table_name.$object_structure_primary_id and 
i.$search_index_structure_object  = obj_rel.$object_structure_name";

	$search_index_update_weight_value = "update $search_index_structrue_name set $search_index_structure_weight=%s, 
$search_index_structure_perspective='%s' where $search_index_structure_object = '%s'";


?>