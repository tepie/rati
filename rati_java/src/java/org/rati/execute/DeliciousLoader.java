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
import org.apache.log4j.Logger;
import org.rati.global.Context;
import org.rati.graph.IndexSearch;

/**
 *
 * @author terry
 */
public class DeliciousLoader extends Thread {

    public static String PREFIX = GraphSetup.RATI_URI_SEP + "delicious";
    public static String POST_PREFIX = PREFIX + GraphSetup.RATI_URI_SEP + "posts";
    public static String TAG_PREFIX = PREFIX + GraphSetup.RATI_URI_SEP + "tags";

    public DeliciousLoader(){
    }
    
    public static void main(String args[]){
        try {
            DeliciousLoader loader = new DeliciousLoader();
            loader.run();
            loader.join();
            
            IndexSearch indexer = new IndexSearch();
            indexer.run();
            indexer.join();
        } catch (InterruptedException ex) {
            Logger.getLogger(DeliciousLoader.class).fatal(ex.getMessage());
        }
    }
    
    @Override
    public void run() {

        String user = java.util.ResourceBundle.getBundle("delicious").getString("username");
        String passwd = java.util.ResourceBundle.getBundle("delicious").getString("password");

        Delicious delicious = new Delicious(user,passwd);
        List posts = delicious.getAllPosts();
        Iterator mover = null;
        RatiGraph graph = new RatiGraph();

        Attribute name = graph.attributeMake("name");
        Attribute description = graph.attributeMake("description");
        Attribute time = graph.attributeMake("time");
        Attribute href = graph.attributeMake("href");
        Attribute tagAttr = graph.attributeMake("tag");
        
        Context.commit();
        
        mover = posts.iterator();
        while (mover.hasNext()) {
            Post post = (Post) mover.next();
            String uri = POST_PREFIX + GraphSetup.RATI_URI_SEP + post.getHash();
            Logger.getLogger(DeliciousLoader.class).debug("Post URI: " + uri);
            Object added = graph.objectMake(uri);
            Context.commit();
            graph.attributeSet(added, time, post.getTime());
            graph.attributeSet(added, href, post.getHref());
            graph.attributeSet(added, description, post.getDescription());
            
            String[] tagList = post.getTagsAsArray(" ");
            for(String tag: tagList){
                String tUri = TAG_PREFIX + GraphSetup.RATI_URI_SEP + tag;
                Logger.getLogger(DeliciousLoader.class).debug("Tag URI: " + tUri);
                Object addedTag = graph.objectMake(tUri);
                Context.commit();
                graph.attributeSet(addedTag, name, tag);
                graph.relationshipSet(added, tagAttr, addedTag);
            }
            Context.commit();

        }
    }
}
