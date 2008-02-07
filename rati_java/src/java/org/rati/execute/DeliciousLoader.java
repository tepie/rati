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
    public static String POST_PREFIX = PREFIX + GraphSetup.RATI_URI_SEP + "posts";
    public static String TAG_PREFIX = PREFIX + GraphSetup.RATI_URI_SEP + "tags";

    public static void main(String[] argv) {

        String user = java.util.ResourceBundle.getBundle("delicious").getString("username");
        String passwd = java.util.ResourceBundle.getBundle("delicious").getString("password");

        Delicious delicious = new Delicious(user,passwd);
        RatiGraph graph = new RatiGraph();

        Attribute name = graph.attributeMake("name");
        Attribute description = graph.attributeMake("description");
        Attribute time = graph.attributeMake("time");
        Attribute href = graph.attributeMake("href");
        Attribute tagAttr = graph.attributeMake("tag");

        Iterator mover = null;

        List posts = delicious.getAllPosts();
        mover = posts.iterator();
        while (mover.hasNext()) {
            Post next = (Post) mover.next();
            String uri = POST_PREFIX + GraphSetup.RATI_URI_SEP + next.getHash();
            Object added = graph.objectMake(uri);
            
            graph.attributeSet(added, time, next.getTime());
            graph.attributeSet(added, href, next.getHref());
            graph.attributeSet(added, description, next.getDescription());
            
            String[] tagList = next.getTagsAsArray(" ");
            for(String t: tagList){
                String tUri = TAG_PREFIX + GraphSetup.RATI_URI_SEP + t;
                Object addedTag = graph.objectMake(tUri);
                graph.attributeSet(addedTag, name, t);
                graph.relationshipSet(added, tagAttr, addedTag);
            }

        }



    }
}
