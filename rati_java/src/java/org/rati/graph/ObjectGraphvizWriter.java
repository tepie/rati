/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.rati.graph;

import java.util.HashMap;
import java.util.Iterator;
import java.util.LinkedList;
import java.util.List;
import java.util.Map;
import org.apache.cayenne.auto.rati.Attribute;
import org.apache.cayenne.auto.rati.Object;
import org.apache.cayenne.auto.rati.Relationship;
import org.apache.cayenne.auto.rati.Relationship;
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
    private List<Map<Object, Object>> drawn = null;
    private List<Object> labeled = null;
    private Object object = null;
    private List objects = null;
    //private Map<Object,Object> drawn = null;
    public ObjectGraphvizWriter(List objects) {
        this.objects = objects;
        this.drawn = new LinkedList<Map<Object, Object>>();
        this.labeled = new LinkedList<Object>();
    }

    public ObjectGraphvizWriter(Object object) {
        this.object = object;
        this.drawn = new LinkedList<Map<Object, Object>>();
        this.labeled = new LinkedList<Object>();
    }

    public synchronized String createObjectGraphviz() {

        StringBuffer buffer = new StringBuffer();
        buffer.append(DOCUMENT_OPEN + NEW_LINE);
        buffer.append(RANK_SEP + NEW_LINE);
        buffer.append(RATIO + NEW_LINE);
        buffer.append(RANK_DIR + NEW_LINE);
        Iterator mover = this.objects.iterator();
        while (mover.hasNext()) {
            Object next = (Object) mover.next();
            buffer.append(bufferRelationships(next).toString() + NEW_LINE);
        }
        buffer.append(DOCUMENT_CLOSE + NEW_LINE);
        return buffer.toString();
    }

    public synchronized String createOneObjectGraphviz() {
        return this.privateCreateObjectGraphviz(this.object);
    }

    private synchronized String privateCreateObjectGraphviz(Object object) {
        StringBuffer buffer = new StringBuffer();
        buffer.append(DOCUMENT_OPEN + NEW_LINE);
        buffer.append(RANK_SEP + NEW_LINE);
        buffer.append(RATIO + NEW_LINE);
        buffer.append(RANK_DIR + NEW_LINE);
        buffer.append(bufferRelationships(object).toString() + NEW_LINE);
        buffer.append(DOCUMENT_CLOSE + NEW_LINE);
        return buffer.toString();
    }

    private List getCombinedRelationshipSet(Object from) {
        RatiGraph graph = new RatiGraph();
        List whole = new TreeList();

        List direct = graph.relationshipDirectLinkGet(from);
        List inDirect = graph.relationshipInDirectLinkGet(from);

        if (direct != null) {
            whole.addAll(direct);
        }

        if (inDirect != null) {
            whole.addAll(inDirect);
        }

        return whole;
    }

    private StringBuffer bufferRelationships(Object from) {
        StringBuffer buffer = new StringBuffer();
        try {
            List rels = getCombinedRelationshipSet(from);
            Iterator relWalker = rels.iterator();

            while (relWalker.hasNext()) {
                Relationship next = (Relationship) relWalker.next();
                String linkLine = this.createReference(next);
                buffer.append(linkLine + NEW_LINE);
            }
        //buffer.append((rels.size()));
        } catch (Exception ex) {
            ex.printStackTrace();
        }
        return buffer;
    }

    private synchronized String createReference(Relationship fromMe) {
        StringBuffer buffer = new StringBuffer();
        Object leftSide = fromMe.getObjectRelationship();
        Object rightSide = fromMe.getObjectReference();

        Map<Object, Object> local = new HashMap<Object, Object>();
        local.put(leftSide, rightSide);
        
        if (!this.drawn.contains(local)) {
            this.drawn.add(local);

            Attribute connection = fromMe.getAttributeRelationship();
            buffer.append(leftSide.hashCode() + DIRECTION_ARROW +
                    rightSide.hashCode() + LF_BRACKET + LINK_LABEL + "=\"" +
                    connection.getName() + "\"" + RG_BRACKET +
                    LINE_TERM);
        }

        if (!this.labeled.contains(leftSide)) {
            this.labeled.add(leftSide);
            buffer.append(createObjectLabel(leftSide) + NEW_LINE);
        }

        if (!this.labeled.contains(rightSide)) {
            this.labeled.add(rightSide);
            buffer.append(createObjectLabel(rightSide) + NEW_LINE);
        }
        return buffer.toString();
    }

    private String createObjectLabel(Object fromMe) {
        if (fromMe == null) {
            return null;
        }

        RatiGraph graph = new RatiGraph();
        Attribute label = graph.attributeExists(GraphSetup.RATI_ATTRIBUTE_LABEL);
        if (label != null && fromMe != null) {
            List rels = graph.relationshipValuesGet(fromMe, label);
            if (rels != null) {
                Relationship firstRel = (Relationship) rels.get(0);
                return fromMe.hashCode() + " " + LF_BRACKET + "label=\"" +
                        firstRel.getValue() + "\"" + RG_BRACKET + LINE_TERM;

            }
        }

        return fromMe.hashCode() + " " + LF_BRACKET + "label=\"" +
                fromMe.hashCode() + "\"" + RG_BRACKET + LINE_TERM;

    }
}
