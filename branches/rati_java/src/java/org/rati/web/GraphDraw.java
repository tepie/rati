/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.rati.web;

import java.io.IOException;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.rati.execute.DrawGraph;
import org.rati.global.RatiLogger;
import org.rati.graph.GraphSetup;
import org.rati.graph.PerformGraphImageDraw;

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
        
        DrawGraph drawer = new DrawGraph(query,outType);
        
         if (outType.equalsIgnoreCase(GraphSetup.WEB_PARM_GRAPHVIZ)) {
             response.setContentType(PerformGraphImageDraw.CONTENT_TYPE_TEXT);
             drawer.draw(response.getWriter(), true);
        } else if (outType.equalsIgnoreCase(GraphSetup.WEB_PARM_IMAGE)) {
            response.setContentType(PerformGraphImageDraw.CONTENT_TYPE_PNG);
            drawer.draw(response.getOutputStream(), true);
        } else {
            
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
