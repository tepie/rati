/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package org.rati.graph;

import java.util.Iterator;
import java.util.List;
import org.apache.cayenne.auto.rati.Attribute;
import org.apache.cayenne.auto.rati.Object;
import org.apache.cayenne.auto.rati.Relationship;

/**
 *
 * @author terry
 */
public class ObjectXmlWriter {
    
    public static String DOCUMENT_OPEN = "<rati>";
    public static String DOCUMENT_CLOSE = "</rati>";
    public static String OBJECT = "object";
    public static String ATTRIBUTE = "attribute";
    public static String REFERENCE = "reference";
    public static String RULE = "rule";
    public static String URI = "uri";
    public static String VALUE = "value";
    
    
    public static String createObjectXmlFromList(List objects){
        RatiGraph graph = new RatiGraph();
        Iterator walker = objects.iterator();
        StringBuffer buffer = new StringBuffer();
        
        buffer.append(DOCUMENT_OPEN);
        while(walker.hasNext()){
            Object next = (Object) walker.next();
            String objectOpen = ObjectXmlWriter.openObject(next);
            buffer.append(objectOpen);
            List attributes = graph.relationshipValuesGet(next);
            if(attributes != null && attributes.size() > 0){
                Iterator attributeWalker = attributes.iterator();
                while(attributeWalker.hasNext()){
                    Relationship attributeRelation = (Relationship) attributeWalker.next();
                    String attributeXml = ObjectXmlWriter.createAttribute(next, attributeRelation.getAttributeRelationship());
                    buffer.append(attributeXml);
                }
            }
            buffer.append(ObjectXmlWriter.closeObject());
            
        }
        buffer.append(DOCUMENT_CLOSE);
        return buffer.toString();
    }
    
    public static  String createReference(Object to, Attribute via){
        return "<" + REFERENCE + " " + RULE + "=\"" + via.getName() + "\" " + URI + "=\"" + to.getName() + "\" />";
    }
    
    public static  String createAttribute(Object on, Attribute with){
        return "<" + ATTRIBUTE + " " + RULE + "=\"" + with.getName() + "\" " + VALUE + "=\"" + on.getName() + "\" />";
    }
    
    public static String openObject(Object open){
        return "<" + OBJECT + " " + URI + "=\"" + open.getName() + "\" >";
    }
    
    public static String closeObject(){
        return "</" + OBJECT + ">";
    }

}
