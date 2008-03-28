/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.rati.execute;

import java.util.Iterator;
import java.util.List;
import org.rati.graph.RatiGraph;
import org.apache.cayenne.auto.rati.Object;

/**
 *
 * @author terry
 */
public class WalkTheLine {

    public static void main(String[] argv) {
        RatiGraph graph = new RatiGraph();
        Iterator walker = graph.iterator();

        while (walker.hasNext()) {
            try {
                Object next = (Object) walker.next();

                List directReferences = graph.relationshipDirectLinkGet(next);
                List inDirectReferences = graph.relationshipInDirectLinkGet(next);
                List attributeValues = graph.relationshipValuesGet(next);
                System.out.println(next.getName());
                System.out.println("Direct references:" + directReferences.size());
                System.out.println("InDirect references:" + inDirectReferences.size());
                System.out.println("Attribute references:" + attributeValues.size());
            } catch (Exception ex) {
                ex.printStackTrace();
            }
        }

    }
}
