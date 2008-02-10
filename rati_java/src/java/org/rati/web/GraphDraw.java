/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.rati.web;

import java.io.IOException;
import java.io.PrintWriter;
import java.util.List;
import javax.servlet.ServletException;
import org.apache.cayenne.auto.rati.Object;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.rati.graph.GraphSetup;
import org.rati.graph.ObjectGraphvizWriter;
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
        response.setContentType("text/plain;charset=UTF-8");
        PrintWriter out = response.getWriter();
        String query = null;
        String outType = null;
        try {

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

            RatiGraph graph = new RatiGraph();
            ObjectGraphvizWriter writer = null;
            Object queryObject = graph.objectExists(query);
            if (queryObject != null) {
                writer = new ObjectGraphvizWriter(queryObject);
                out.print(writer.createOneObjectGraphviz());
            } else {
               List results = graph.objectsStartingWith(query);
               writer = new ObjectGraphvizWriter(results);
               out.print(writer.createObjectGraphviz());
            }
        } finally {
            out.close();
        }
    }

    // <editor-fold defaultstate="collapsed" desc="HttpServlet methods. Click on the + sign on the left to edit the code.">
    /** 
     * Handles the HTTP <code>GET</code> method.
     * @param request servlet request
     * @param response servlet response
     */
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        processRequest(request, response);
    }

    /** 
     * Handles the HTTP <code>POST</code> method.
     * @param request servlet request
     * @param response servlet response
     */
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        processRequest(request, response);
    }

    /** 
     * Returns a short description of the servlet.
     */
    public String getServletInfo() {
        return "Short description";
    }
    // </editor-fold>
}
