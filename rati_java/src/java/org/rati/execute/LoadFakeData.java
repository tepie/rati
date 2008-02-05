/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package org.rati.execute;

import org.apache.cayenne.auto.rati.Attribute;
import org.rati.graph.RatiGraph;
import org.apache.cayenne.auto.rati.Object;
import org.apache.cayenne.auto.rati.Relationship;

/**
 *
 * @author terry
 */
public class LoadFakeData {
        
    private static String fakeNamePrefix = "/I/Am/A/Fake/Object_";
    private static String pathRule = "path";
    private static String selfRule = "self";
    private static int thisManyFakes = 50;
    
     public static void main(String[] argv){
         int startingAt = 0;
         RatiGraph graph = new RatiGraph();
         
         Attribute rule = graph.attributeMake(pathRule);
         Attribute self = graph.attributeMake(selfRule);
         
         while(startingAt < thisManyFakes){
             String newPath = fakeNamePrefix + startingAt;
             Object added = graph.objectMake(newPath);
             System.out.println("Added: " + added.getName());
             Relationship link = graph.attributeSet(added, rule, newPath);
             System.out.println("Linked Attribute: " + added.getName() + "," + pathRule + "=" + link.getValue());
             Relationship selfRef = graph.relationshipSet(added, self, added);
             if(selfRef != null){
                System.out.println("Linked Reference: " + 
                     selfRef.getObjectRelationship().getName() + 
                     "," + selfRef.getAttributeRelationship().getName() + 
                     "=" + selfRef.getObjectReference().getName() );
             }
             startingAt++;
         }
         
     }

}
