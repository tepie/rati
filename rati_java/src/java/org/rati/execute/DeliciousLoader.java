/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package org.rati.execute;

import del.icio.us.Delicious;
import del.icio.us.beans.Post;
import java.util.Iterator;
import java.util.List;
import org.apache.cayenne.auto.rati.Attribute;
import org.rati.graph.GraphSetup;
import org.rati.graph.RatiGraph;
import org.apache.cayenne.auto.rati.Object;

/**
 *
 * @author terry
 */
public class DeliciousLoader {
    
    public static String PREFIX = GraphSetup.RATI_URI_SEP + "delicious";
    
     public static void main(String[] argv){
         
         String user = "";
         
         Delicious delicious = new Delicious(user,"");
         RatiGraph graph = new RatiGraph();
         List posts = delicious.getAllPosts();
         
         Iterator mover = posts.iterator();
         
         Attribute description = graph.attributeMake("description");
         Attribute time = graph.attributeMake("time");
         Attribute href = graph.attributeMake("href");
         
         
         while(mover.hasNext()){
             Post next = (Post) mover.next();
             String uri = PREFIX + GraphSetup.RATI_URI_SEP + next.getHash();
             Object added = graph.objectMake(uri);
             graph.attributeSet(added, time, next.getTime());
             graph.attributeSet(added, href, next.getHref());
             graph.attributeSet(added, description, next.getDescription());
             
         }
         
         
     }

}
