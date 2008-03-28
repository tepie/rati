/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.rati.graph;

import org.apache.cayenne.auto.rati.Object;
import java.util.Iterator;
import org.apache.cayenne.auto.rati.SearchIndex;
import org.apache.log4j.Logger;
import org.rati.global.Context;

/**
 *
 * @author terry
 */
public class IndexSearch extends Thread {

    //public static int SLEEP_TIME = 1000;
    public IndexSearch() {
        this.setPriority(MIN_PRIORITY);
        this.setName("org.rati.graph.IndexSearch");
        
    }

    @Override
    public void run() {
        Logger.getLogger(IndexSearch.class).debug(this.getName() + " running...");
        RatiGraph graph = new RatiGraph();
        Iterator mover = graph.iterator();
        
        while (mover.hasNext()) {
            try {
                Object next = (Object) mover.next();
                Integer rank = graph.countTotalRelationships(next);
                SearchIndex index = next.getSearchIndex();

                if (index == null) {
                    index = (SearchIndex) Context.getContext().newObject(SearchIndex.class);
                }

                index.setObjectName(next.getName());
                index.setRank(rank.longValue());
                index.setWeight(new Integer(0).longValue());
                Combiner combiner = new Combiner(next);
                combiner.start();
                combiner.join();
                String combined = combiner.getBufferString();
                index.setCombinedAttributes(combined);
                Context.commit();
                Thread.yield();

            } catch (InterruptedException ex) {
                Logger.getLogger(IndexSearch.class).fatal(ex.getMessage());
            }
        }
    }
}
