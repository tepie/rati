/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package org.rati.web;

import java.io.IOException;
import java.io.PrintWriter;
import java.util.List;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.apache.commons.collections.list.TreeList;
import org.rati.global.Context;
import org.rati.graph.GraphSetup;
import org.rati.graph.ObjectXmlWriter;
import org.rati.graph.RatiGraph;


/**
 *
 * @author terry
 */
public class GraphList extends HttpServlet {
    
    public static String WEB_PARM_PATH = GraphSetup.WEB_PARM_QUERY;
   
    /** 
    * Processes requests for both HTTP <code>GET</code> and <code>POST</code> methods.
    * @param request servlet request
    * @param response servlet response
    */
    protected void processRequest(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {
        response.setContentType("text/xml;charset=UTF-8");
        String startingAt = null;
        PrintWriter out = response.getWriter();
        try {
            if(request.getParameterMap().keySet().contains(WEB_PARM_PATH)){
                startingAt = request.getParameter(WEB_PARM_PATH);
            } else {
                startingAt = GraphSetup.RATI_URI_ROOT;
            }
            
            RatiGraph graph = new RatiGraph();
            List results = graph.objectsStartingWith(startingAt);
            
            if(results == null || results.size() <= 0){
                results = new TreeList();
            }
            
            out.print(ObjectXmlWriter.createObjectXmlFromList(results));
            
        } finally { 
            out.close();
            Context.commit();
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
