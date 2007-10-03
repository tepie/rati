-- Delete the relationships on the objects we are about to remove
-- in this case, they start with "/web/"
delete relationship from relationship,object where relationship.object_id = object.id and object.name like ("/web/%");
-- Actually delete the objects
delete object from object where object.name like ("/web/%");