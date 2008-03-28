/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.rati.execute;

import java.io.DataInputStream;
import java.io.EOFException;
import java.io.IOException;
import java.io.PrintStream;
import java.io.PrintWriter;
import java.util.List;
import javax.servlet.ServletOutputStream;
import org.rati.graph.ObjectGraphvizWriter;
import org.rati.graph.RatiGraph;
import org.apache.cayenne.auto.rati.Object;
import org.rati.global.RatiLogger;
import org.rati.graph.GraphSetup;
import org.rati.graph.PerformGraphImageDraw;

/**
 *
 * @author terry
 */
public class DrawGraph {

    private String query = null;
    private String output = null;

    public static void main(String[] argv) {
        if (argv.length != 2) {
            System.exit(-1);
        }

        String query = argv[0];
        String output = argv[1];

        DrawGraph drawer = new DrawGraph(query, output);
        if (drawer.getOutput().equalsIgnoreCase(GraphSetup.WEB_PARM_GRAPHVIZ)) {
            drawer.draw(System.out);
        } else if (drawer.getOutput().equalsIgnoreCase(GraphSetup.WEB_PARM_IMAGE)) {

        }

    }

    public DrawGraph(String query, String output) {
        this.query = query;
        this.output = output;
    }

    public void draw(PrintStream out) {
        String graphvizText = this.getGraphvizText();
        out.print(graphvizText);

    }

    public void draw(PrintWriter out, boolean close) {
        String graphvizText = this.getGraphvizText();
        try {
            out.print(graphvizText);
        } finally {
            if (close) {
                out.close();
            }
        }
    }

    public void draw(ServletOutputStream binary, boolean close) {
        String graphvizText = this.getGraphvizText();
        try {
            PerformGraphImageDraw drawer = new PerformGraphImageDraw();
            DataInputStream processInput = drawer.perform(graphvizText);
            try {
                byte next = processInput.readByte();
                while (true) {
                    binary.write(next);
                    next = processInput.readByte();
                }
            } catch (EOFException ex) {
                RatiLogger.getLogger(DrawGraph.class).error("EOFException: " + ex.getMessage());
            } catch (IOException ex) {
                RatiLogger.getLogger(DrawGraph.class).error("IOException: " + ex.getMessage());
            }

        } finally {
            if (close) {
                try {
                    binary.close();
                } catch (IOException ex) {
                    RatiLogger.getLogger(DrawGraph.class).error("IOException: " + ex.getMessage());
                }
            }
        }
    }

    public String getOutput() {
        return this.output;
    }

    public String getQuery() {
        return this.query;
    }

    protected String getGraphvizText() {
        RatiGraph graph = new RatiGraph();
        ObjectGraphvizWriter writer = null;
        Object queryObject = graph.objectExists(this.getQuery());
        String graphvizText = null;
        if (queryObject != null) {
            writer = new ObjectGraphvizWriter(queryObject);
            graphvizText = writer.createOneObjectGraphviz();
        } else {
            List results = graph.objectsStartingWith(this.getQuery());
            writer = new ObjectGraphvizWriter(results);
            graphvizText = writer.createObjectGraphviz();
        }
        return graphvizText;
    }
}
