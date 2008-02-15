/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.rati.web;

import java.io.BufferedInputStream;
import java.io.DataInputStream;
import java.io.EOFException;
import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.util.List;
import javax.servlet.ServletException;
import javax.servlet.ServletOutputStream;
import org.apache.cayenne.auto.rati.Object;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.apache.log4j.Level;
import org.apache.log4j.Logger;
import org.rati.global.FileNameMaker;
import org.rati.global.RatiLogger;
import org.rati.graph.GraphSetup;
import org.rati.graph.ObjectGraphvizWriter;
import org.rati.graph.PerformGraphImageDraw;
import org.rati.graph.RatiGraph;

/**
 *
 * @author terry
 */
public class GraphDraw extends HttpServlet {

    /** 
     * Processes requests for both HTTP <code>GET</code> and <code>POST</code> methods.
     * @param request servlet request
     * @param response servlet response
     */
    protected void processRequest(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        
        String query = null;
        String outType = null;

        if (request.getParameterMap().keySet().contains(GraphSetup.WEB_PARM_QUERY)) {
            query = request.getParameter(GraphSetup.WEB_PARM_QUERY).toLowerCase();
        } else {
            query = GraphSetup.RATI_URI_ROOT;
        }

        if (request.getParameterMap().keySet().contains(GraphSetup.WEB_PARM_TYPE)) {
            outType = request.getParameter(GraphSetup.WEB_PARM_TYPE).toLowerCase();
        } else {
            outType = GraphSetup.WEB_PARM_GRAPHVIZ;
        }

        RatiLogger.getLogger(GraphDraw.class).debug("Query: " + query);
        RatiLogger.getLogger(GraphDraw.class).debug("Type: " + outType);

        RatiGraph graph = new RatiGraph();
        ObjectGraphvizWriter writer = null;
        Object queryObject = graph.objectExists(query);
        String graphvizText = null;
        if (queryObject != null) {
            writer = new ObjectGraphvizWriter(queryObject);
            graphvizText = writer.createOneObjectGraphviz();
        } else {
            List results = graph.objectsStartingWith(query);
            writer = new ObjectGraphvizWriter(results);
            graphvizText = writer.createObjectGraphviz();
        }

        if (outType.equalsIgnoreCase(GraphSetup.WEB_PARM_GRAPHVIZ)) {
            PrintWriter out = null;
            try {
                response.setContentType("text/plain;charset=UTF-8");
                out = response.getWriter();
                out.println(graphvizText);
            } finally {
                out.close();
            }
        } else if (outType.equalsIgnoreCase(GraphSetup.WEB_PARM_IMAGE)) {
            
            response.setContentType(PerformGraphImageDraw.CONTENT_TYPE_PNG);
            ServletOutputStream binary = response.getOutputStream();
           
            PerformGraphImageDraw drawer = new PerformGraphImageDraw();
            DataInputStream processInput = drawer.perform(graphvizText);
            try {
                byte next = processInput.readByte();
                while (true) {
                    binary.write(next);
                    next = processInput.readByte();
                }
            } catch (EOFException ex) {
                RatiLogger.getLogger(GraphDraw.class).error("EOFException: " + ex.getMessage());
            }

            processInput.close();

        } else {
            //TODO: handle this case
        }
    }

    // <editor-fold defaultstate="collapsed" desc="HttpServlet methods. Click on the + sign on the left to edit the code.">
    /** 
     * Handles the HTTP <code>GET</code> method.
     * @param request servlet request
     * @param response servlet response
     */
    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        processRequest(request, response);
    }

    /** 
     * Handles the HTTP <code>POST</code> method.
     * @param request servlet request
     * @param response servlet response
     */
    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        processRequest(request, response);
    }

    /** 
     * Returns a short description of the servlet.
     */
    @Override
    public String getServletInfo() {
        return "Short description";
    }
    // </editor-fold>
}
