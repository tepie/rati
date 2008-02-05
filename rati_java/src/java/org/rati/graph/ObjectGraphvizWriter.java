/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.rati.graph;

import java.util.Iterator;
import java.util.List;
import java.util.Set;
import java.util.TreeSet;
import org.apache.cayenne.auto.rati.Attribute;
import org.apache.cayenne.auto.rati.Object;
import org.apache.cayenne.auto.rati.Relationship;
import org.apache.commons.collections.list.TreeList;

/**
 *
 * @author terry
 */
public class ObjectGraphvizWriter {

    public static final String DIR_LR = "LR";
    public static final String DIR_TB = "TB";
    public static final String LINE_TERM = ";";
    public static final String DIRECTION_ARROW = "->";
    public static final String NEW_LINE = "\n";
    public static final String LF_BRACKET = "[";
    public static final String RG_BRACKET = "]";
    public static final String DOCUMENT_OPEN = "digraph G {";
    public static final String DOCUMENT_CLOSE = "}";
    public static final String RANK_SEP = "ranksep=3" + LINE_TERM;
    public static final String RATIO = "ratio=auto" + LINE_TERM;
    public static final String RANK_DIR = "rankdir=" + DIR_LR + LINE_TERM;
    public static final String LINK_LABEL = "label";

    public static String createObjectGraphviz(List objects){
        StringBuffer buffer = new StringBuffer();
        buffer.append(DOCUMENT_OPEN + NEW_LINE);
        buffer.append(RANK_SEP + NEW_LINE);
        buffer.append(RATIO + NEW_LINE);
        buffer.append(RANK_DIR + NEW_LINE);
        Iterator mover = objects.iterator();
        while(mover.hasNext()){
            Object next = (Object) mover.next();
            buffer.append(bufferRelationships(next).toString());
        }
        buffer.append(DOCUMENT_CLOSE + NEW_LINE);
        return buffer.toString();
    }
    
    public static String createObjectGraphviz(Object from) {
        StringBuffer buffer = new StringBuffer();
        buffer.append(DOCUMENT_OPEN + NEW_LINE);
        buffer.append(RANK_SEP + NEW_LINE);
        buffer.append(RATIO + NEW_LINE);
        buffer.append(RANK_DIR + NEW_LINE);

        buffer.append(bufferRelationships(from).toString());
        
        buffer.append(DOCUMENT_CLOSE + NEW_LINE);
        return buffer.toString();
    }

    private static List getCombinedRelationshipSet(Object from) {
        RatiGraph graph = new RatiGraph();
        List whole = new TreeList();

        List direct = graph.relationshipDirectLinkGet(from);
        List inDirect = graph.relationshipInDirectLinkGet(from);

        whole.addAll(direct);
        whole.addAll(inDirect);

        //Set wholeSet = new TreeSet(whole);

        return whole;
    }
    
    private static StringBuffer bufferRelationships(Object from){
        StringBuffer buffer = new StringBuffer();
        try {
            List rels = getCombinedRelationshipSet(from);
            Iterator relWalker = rels.iterator();

            while (relWalker.hasNext()) {
                Relationship next = (Relationship) relWalker.next();
                String linkLine = createReference(next);
                buffer.append(linkLine + NEW_LINE);
            }
        //buffer.append((rels.size()));
        } catch (Exception ex) {
            ex.printStackTrace();
        }
        return buffer;
    }

    public static String createReference(Relationship fromMe) {
        Object leftSide = fromMe.getObjectRelationship();
        Object rightSide = fromMe.getObjectReference();
        Attribute connection = fromMe.getAttributeRelationship();
        return leftSide.hashCode() + DIRECTION_ARROW +
                rightSide.hashCode() + LF_BRACKET + LINK_LABEL + "=\"" +
                connection.getName() + "\"" + RG_BRACKET +
                LINE_TERM;
    }
}
