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
        


        String query = null;
        String outType = null;
        //try {

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

        Logger.getLogger(GraphDraw.class).debug(query);
        Logger.getLogger(GraphDraw.class).debug(outType);

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
            //image/png
            response.setContentType("image/png;charset=UTF-8");
            ServletOutputStream binary = response.getOutputStream();
            File tempFile = FileNameMaker.makeAName();
            FileWriter tempWriter = new FileWriter(tempFile);
            tempWriter.write(graphvizText);
            tempWriter.close();
            String command = "circo -Tpng " + tempFile.getAbsolutePath();
            Logger.getLogger(GraphDraw.class).debug(command);
            Runtime runtime = Runtime.getRuntime();
            Process process = runtime.exec(command);
            //int exitValue = process.waitFor();
            //if(exitValue == 0){
            DataInputStream processInput = new DataInputStream(process.getInputStream());
            //byte buf[] = new byte[1024];

            try {
                byte next = processInput.readByte();
                while (true) {
                    //out.print(next);
                    binary.write(next);
                    next = processInput.readByte();
                }
            } catch (EOFException ex) {

            }

            //out.write(buf.toString(), 0, buf.length);
            //out.write(buf, 0, buf.length);

            //

            processInput.close();

        }
    /*} finally {
    out.close();
    binary.close();
    }*/
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
