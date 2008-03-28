/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.rati.execute;

import java.util.Iterator;
import org.apache.cayenne.auto.rati.SearchIndex;
import org.apache.log4j.Logger;
import org.rati.graph.RatiGraph;

/**
 *
 * @author terry
 */
public class Search {

    public static void main(String args[]) {
        RatiGraph graph = new RatiGraph();

        Iterator mover = graph.searchResultsIterator("yahoo");
        while (mover.hasNext()) {
            SearchIndex next = (SearchIndex) mover.next();
            Logger.getLogger(Search.class).debug(next.getObjectName());
        }
    }
}
