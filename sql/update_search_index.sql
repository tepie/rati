
-- To update the search index, exexute this SQL
-- mysql -u root -p metawarehouse < sql\update_search_index.sql
-- root access is needed, truncate will not be given to the graphviz user 

-- /testing/testing/123
-- 69601
-- insert into search_index (`object_name`,`combined_attributes`) VALUES ("/testing/testing/123","I am testing this table")

SET AUTOCOMMIT=0;
START TRANSACTION;

replace into search_index (object_name,combined_attributes,rank)
	select results.object,group_concat(
		concat_ws("=",results.attribute,results.result) separator '; '
	) as rule, sum(results.count) as count 
	from 
	(
		(select o.name as object,a.name as attribute,r.value as result,0 as count from relationship as r,
			object as o, attribute as a where o.id = r.object_id and a.id =r.attribute_id and 
			r.value is not null
		) union (select o.name as object,a.name as attribute,oo.name as result, 1 as count from 
			relationship as r,object as o, attribute as a,
			(select * from object) as oo where o.id = r.object_id and 
			a.id = r.attribute_id and r.value is null and r.reference = oo.id )
	) as results group by results.object;

COMMIT;