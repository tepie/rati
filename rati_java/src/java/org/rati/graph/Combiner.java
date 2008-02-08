/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package org.rati.graph;

import java.util.Iterator;
import java.util.List;
import org.apache.cayenne.auto.rati.Object;
import org.apache.cayenne.auto.rati.Relationship;

/**
 *
 * @author terry
 */
public class Combiner extends Thread {
    
    public static String COMBINED_DELIMITER = ";";
    public static String COMBINED_EQUALS = "=";
    
    private Object object = null;
    private StringBuffer buffer = null;
    private RatiGraph graph = null;
    
    private List attrRels = null;
    private List directRels = null;
    
    private boolean runSwitch = false;
    
    public Combiner(Object from){
        this.object = null;
        this.buffer = new StringBuffer();
        this.graph = new RatiGraph();
        
        this.attrRels = graph.relationshipValuesGet(from);
        this.directRels = graph.relationshipDirectLinkGet(from);
    }
    
    @Override
    public void run(){
        if(runSwitch == false){
            runSwitch = true;
            Iterator mover = null;
            if(attrRels != null){
                mover = attrRels.iterator();
                while(mover.hasNext()){
                    Relationship next = (Relationship) mover.next();
                    String key = next.getAttributeRelationship().getName();
                    String value = next.getValue();
                    buffer.append(this.join(key,value));
                    Thread.yield();
                }
            }
            
            if(directRels != null){
                mover = directRels.iterator();
                while(mover.hasNext()){
                    Relationship next = (Relationship) mover.next();
                    String key = next.getAttributeRelationship().getName();
                    String value = next.getObjectReference().getName();
                    buffer.append(this.join(key,value));
                    Thread.yield();
                }
            }
        }
    }
    
    public String getBufferString(){
        return this.buffer.toString();
    }
    
    private String join(String key, String value){
        return key + COMBINED_EQUALS + value + COMBINED_DELIMITER;
    }

}
