/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.rati.graph;

import java.util.Iterator;
import java.util.List;
import java.util.logging.Level;
import org.apache.cayenne.auto.rati.Attribute;
import org.apache.cayenne.auto.rati.Object;
import org.apache.cayenne.auto.rati.Relationship;
import org.apache.cayenne.exp.Expression;
import org.apache.cayenne.exp.ExpressionFactory;
import org.apache.cayenne.query.SelectQuery;
import org.apache.log4j.Logger;
import org.rati.global.Context;

/**
 *
 * @author terry
 */
public class RatiGraph implements Iterable {

    public RatiGraph() {
        //GraphSetup.setupGraph();
    }

    public Object objectExists(String name) {
        Expression qual = ExpressionFactory.likeIgnoreCaseExp(Object.NAME_PROPERTY, name);
        SelectQuery query = new SelectQuery(Object.class, qual);

        query.setFetchLimit(1);

        List objectResults = Context.getContext().performQuery(query);

        if (objectResults.size() == 1) {
            //Context.getContext().commitChanges();
            return (Object) objectResults.get(0);
        } else {
            //Context.getContext().rollbackChanges();
            return null;
        }
    }
    
    public List objectsStartingWith(String prefix){
        Expression qual = ExpressionFactory.likeIgnoreCaseExp(Object.NAME_PROPERTY, 
                prefix + GraphSetup.RATI_URI_WILDCARD );
        
        SelectQuery query = new SelectQuery(Object.class, qual);
        query.setFetchLimit(10);
        List objectResults = Context.getContext().performQuery(query);

        if (objectResults.size() > 0) {
            //Context.getContext().commitChanges();
            return objectResults;
        } else {
            //Context.getContext().rollbackChanges();
            return null;
        }
        
    }

    public Attribute attributeExists(String name) {
        Expression qual = ExpressionFactory.likeIgnoreCaseExp(Attribute.NAME_PROPERTY, name);
        SelectQuery query = new SelectQuery(Attribute.class, qual);

        query.setFetchLimit(1);

        List objectResults = Context.getContext().performQuery(query);

        if (objectResults.size() == 1) {
            //Context.getContext().commitChanges();
            return (Attribute) objectResults.get(0);
        } else {
            //Context.getContext().rollbackChanges();
            return null;
        }
    }

    public Object objectMake(String name) {
        Object exists = this.objectExists(name);
        if (exists == null) {
            Object newMake = (Object) Context.getContext().newObject(Object.class);
            newMake.setName(name);
            //Context.getContext().commitChanges();
            return newMake;
        } else {
            //Context.getContext().rollbackChanges();
            return exists;
        }

    }

    public Attribute attributeMake(String name) {
        Attribute exists = this.attributeExists(name);
        if (exists == null) {
            Attribute newMake = (Attribute) Context.getContext().newObject(Attribute.class);
            newMake.setName(name);
            //Context.getContext().commitChanges();
            return newMake;
        } else {

            //Context.getContext().rollbackChanges();
            return exists;
        }
    }

    public Relationship attributeSet(Object object, Attribute attribute, String value) {
        if (object != null && attribute != null) {
            Relationship link = this.attributeSetExists(object, attribute, value);
            if (link == null) {

                Relationship newMake = (Relationship) Context.getContext().newObject(Relationship.class);

                newMake.setAttributeRelationship(attribute);
                newMake.setObjectRelationship(object);
                newMake.setValue(value);

                //Context.getContext().commitChanges();
                return newMake;
            } else {
                return link;
            }
        } else {
            return null;
        }

    }

    public Relationship attributeSetExists(Object object, Attribute attribute, String value) {
        Expression qual = ExpressionFactory.matchExp(Relationship.OBJECT_RELATIONSHIP_PROPERTY, object);
        qual = qual.andExp(ExpressionFactory.matchExp(Relationship.ATTRIBUTE_RELATIONSHIP_PROPERTY, attribute));
        qual = qual.andExp(ExpressionFactory.likeIgnoreCaseExp(Relationship.VALUE_PROPERTY, value));

        SelectQuery query = new SelectQuery(Relationship.class, qual);

        query.setFetchLimit(1);

        List objectResults = Context.getContext().performQuery(query);

        if (objectResults.size() == 1) {
            //Context.getContext().commitChanges();
            return (Relationship) objectResults.get(0);
        } else {
            //Context.getContext().rollbackChanges();
            return null;
        }

    }

    public Relationship relationshipSetExists(Object source, Attribute attribute, Object target) {
        Expression qual = ExpressionFactory.matchExp(Relationship.OBJECT_RELATIONSHIP_PROPERTY, source);
        qual = qual.andExp(ExpressionFactory.matchExp(Relationship.ATTRIBUTE_RELATIONSHIP_PROPERTY, attribute));
        qual = qual.andExp(ExpressionFactory.matchExp(Relationship.OBJECT_REFERENCE_PROPERTY, target));
        qual = qual.andExp(ExpressionFactory.matchExp(Relationship.VALUE_PROPERTY, null));

        SelectQuery query = new SelectQuery(Relationship.class, qual);

        query.setFetchLimit(1);

        List objectResults = Context.getContext().performQuery(query);

        if (objectResults.size() > 0) {
            //System.err.println("objectResults Size: " + objectResults.size());
            //Context.getContext().commitChanges();
            return (Relationship) objectResults.get(0);
        } else {
            //System.err.println("relationshipSetExists: No relationship found!");
            //Context.getContext().rollbackChanges();
            return null;
        }

    }

    public Relationship relationshipSet(Object source, Attribute attribute, Object target) {
        if (source != null && attribute != null && target != null) {
            Relationship link = this.relationshipSetExists(source, attribute, target);
            if (link == null) {
                //System.err.println("Creating new link");
                Relationship newMake = (Relationship) Context.getContext().newObject(Relationship.class);
                newMake.setObjectRelationship(source);
                newMake.setObjectReference(target);
                newMake.setAttributeRelationship(attribute);
                //Context.getContext().commitChanges();
                return newMake;
            } else {
                //System.err.println("Using existing link");
                return link;
            }
        } else {
            //System.err.println("Something is not right, " +
              //      "provide a valid object attribute and object reference.");
            return null;
        }
    }

    public List relationshipValuesGet(Object source) {
        Expression qual = ExpressionFactory.matchExp(Relationship.OBJECT_RELATIONSHIP_PROPERTY, source);
        qual = qual.andExp(ExpressionFactory.noMatchExp(Relationship.VALUE_PROPERTY, null));
        qual = qual.andExp(ExpressionFactory.matchExp(Relationship.OBJECT_REFERENCE_PROPERTY, null));
        SelectQuery query = new SelectQuery(Relationship.class, qual);

        List objectResults = Context.getContext().performQuery(query);

        if (objectResults.size() > 0) {
            //System.err.println("objectResults Size: " + objectResults.size());
            //Context.getContext().commitChanges();
            return objectResults;
        } else {
            //System.err.println("relationshipSetExists: No relationship found!");
            //Context.getContext().rollbackChanges();
            return null;
        }
    }
    
    public List relationshipValuesGet(Object source, Attribute attribute) {
        if(source == null || attribute == null){
            return null;
        }
        Expression qual = ExpressionFactory.matchExp(Relationship.OBJECT_RELATIONSHIP_PROPERTY, source);
        qual = qual.andExp(ExpressionFactory.matchExp(Relationship.ATTRIBUTE_RELATIONSHIP_PROPERTY, attribute));
        qual = qual.andExp(ExpressionFactory.matchExp(Relationship.OBJECT_REFERENCE_PROPERTY, null));
        SelectQuery query = new SelectQuery(Relationship.class, qual);

        List objectResults = Context.getContext().performQuery(query);

        if (objectResults.size() > 0) {
            //System.err.println("objectResults Size: " + objectResults.size());
            //Context.getContext().commitChanges();
            return objectResults;
        } else {
            //System.err.println("relationshipSetExists: No relationship found!");
            //Context.getContext().rollbackChanges();
            return null;
        }
    }


    public List relationshipDirectLinkGet(Object source) {
        Expression qual = ExpressionFactory.matchExp(Relationship.OBJECT_RELATIONSHIP_PROPERTY, source);
        qual = qual.andExp(ExpressionFactory.matchExp(Relationship.VALUE_PROPERTY, null));
        qual = qual.andExp(ExpressionFactory.noMatchExp(Relationship.OBJECT_REFERENCE_PROPERTY, null));
        SelectQuery query = new SelectQuery(Relationship.class, qual);

        List objectResults = Context.getContext().performQuery(query);

        if (objectResults.size() > 0) {
            //System.err.println("objectResults Size: " + objectResults.size());
            //Context.getContext().commitChanges();
            return objectResults;
        } else {
            //System.err.println("relationshipSetExists: No relationship found!");
            //Context.getContext().rollbackChanges();
            return null;
        }
    }

    public List relationshipInDirectLinkGet(Object source) {
        Expression qual = ExpressionFactory.matchExp(Relationship.OBJECT_REFERENCE_PROPERTY, source);
        qual = qual.andExp(ExpressionFactory.matchExp(Relationship.VALUE_PROPERTY, null));

        SelectQuery query = new SelectQuery(Relationship.class, qual);

        List objectResults = Context.getContext().performQuery(query);

        if (objectResults.size() > 0) {
            //System.err.println("objectResults Size: " + objectResults.size());
            //Context.getContext().commitChanges();
            return objectResults;
        } else {
            //System.err.println("relationshipSetExists: No relationship found!");
            //Context.getContext().rollbackChanges();
            return null;
        }
    }
    
    public int countDirectRelations(Object object){
        return object.getRelationships().size();
    }
    
    public int countReferences(Object object){
        return object.getReference().size();
    }
    
    public int countTotalRelationships(Object object){
        return this.countDirectRelations(object) + this.countReferences(object);
    }
    
    public List searchGraph(String query){
        try {
            SearchGraph searcher = new SearchGraph(query);
            searcher.start();
            searcher.join();

            return searcher.getResults();
        } catch (InterruptedException ex) {
            Logger.getLogger(RatiGraph.class).fatal(ex.getMessage());
            return null;
        }
    }
    
    public Iterator searchResultsIterator(String query){
        try {
            SearchGraph searcher = new SearchGraph(query);
            searcher.start();
            searcher.join();
            return searcher.iterator();
        } catch (InterruptedException ex) {
             Logger.getLogger(RatiGraph.class).fatal(ex.getMessage());
             return null;
        }
    }

    public Iterator iterator() {
        SelectQuery query = new SelectQuery(Object.class);
        List objectResults = Context.getContext().performQuery(query);
        return objectResults.iterator();
    }
}
