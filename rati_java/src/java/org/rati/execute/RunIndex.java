/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package org.rati.execute;

import java.util.logging.Level;
import java.util.logging.Logger;
import org.rati.graph.IndexSearch;

/**
 *
 * @author terry
 */
public class RunIndex {
    
    public static void main(String[] argv){
        try {
            IndexSearch indexer = new IndexSearch();
            indexer.start();
            indexer.join();
        } catch (InterruptedException ex) {
            Logger.getLogger(RunIndex.class.getName()).log(Level.SEVERE, null, ex);
        }
    }

}
