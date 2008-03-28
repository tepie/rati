/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.rati.graph;

import org.apache.cayenne.auto.rati.Attribute;
import org.rati.global.Context;

/**
 *
 * @author terry
 */
public class GraphSetup {

    public static final String RATI_ATTRIBUTE_URI = "RATI_URI";
    public static final String RATI_ATTRIBUTE_SHAPE = "RATI_SHAPE";
    public static final String RATI_ATTRIBUTE_COLOR = "RATI_COLOR";
    public static final String RATI_ATTRIBUTE_LABEL = "RATI_LABEL";
    public static final String RATI_URI_PREFIX = "RATI";
    public static final String RATI_URI_SEP = "/";
    public static final String RATI_URI_ROOT = RATI_URI_SEP;
    public static final String RATI_URI_WILDCARD = "%";
    public static final String WEB_PARM_QUERY = "q";
    public static final String WEB_PARM_TYPE = "type";
    public static final String WEB_PARM_GRAPHVIZ = "graphviz";
    public static final String WEB_PARM_IMAGE = "image";

    public static Attribute uri() {
        RatiGraph graph = new RatiGraph();
        Attribute returnme = graph.attributeMake(RATI_ATTRIBUTE_URI);
        Context.getContext().commitChanges();
        return returnme;
    }

    public static Attribute shape() {
        RatiGraph graph = new RatiGraph();
        Attribute returnme = graph.attributeMake(RATI_ATTRIBUTE_SHAPE);
        Context.getContext().commitChanges();
        return returnme;
    }

    public static Attribute color() {
        RatiGraph graph = new RatiGraph();
        Attribute returnme = graph.attributeMake(RATI_ATTRIBUTE_COLOR);
        Context.getContext().commitChanges();
        return returnme;
    }

    public static Attribute label() {
        RatiGraph graph = new RatiGraph();
        Attribute returnme = graph.attributeMake(RATI_ATTRIBUTE_LABEL);
        Context.getContext().commitChanges();
        return returnme;
    }
}
