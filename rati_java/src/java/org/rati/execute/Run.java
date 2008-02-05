/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.rati.execute;

import org.rati.graph.RatiGraph;
import org.apache.cayenne.auto.rati.Object;

/**
 *
 * @author terry
 */
public class Run {

    public static void main(String[] argv){
        RatiGraph graph = new RatiGraph();
        
        Object found = graph.objectExists("test");
        if(found != null){
            found.getName();
        }
        
    }

}
