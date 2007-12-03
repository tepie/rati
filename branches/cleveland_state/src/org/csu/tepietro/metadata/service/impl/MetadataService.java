/*
 * MetadataService.java
 *
 * Created on November 20, 2007, 9:05 AM
 *
 * To change this template, choose Tools | Template Manager
 * and open the template in the editor.
 */

package org.csu.tepietro.metadata.service.impl;

import edu.uci.ics.jung.graph.Graph;
import edu.uci.ics.jung.graph.decorators.StringLabeller;
import edu.uci.ics.jung.io.PajekNetReader;
import edu.uci.ics.jung.visualization.ISOMLayout;
import edu.uci.ics.jung.visualization.Layout;

import edu.uci.ics.jung.visualization.PluggableRenderer;
import edu.uci.ics.jung.visualization.VisualizationViewer;
import java.awt.Container;
import java.awt.Dimension;
import java.awt.Graphics2D;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.StringReader;
import java.util.Iterator;
import java.util.Set;
import java.util.Vector;
import javax.imageio.ImageIO;
import javax.jws.WebMethod;
import javax.jws.WebParam;
import javax.jws.WebService;

//import org.globus.wsrf.Resource;
//import org.globus.wsrf.ResourceProperties;

import org.csu.tepietro.metadata.Metadata;
//import org.globus.wsrf.ResourcePropertySet;
//import org.globus.wsrf.impl.SimpleResourcePropertySet;

/**
 *
 * @author PIETRTI
 */
@WebService()
public class MetadataService {
    
    private Vector<Metadata> metadata;
    private Vector<Metadata> versions;
    //private ResourcePropertySet propSet;

    
    public MetadataService(){
        this.reset();
    }
    
    @WebMethod
    public void reset(){
        this.metadata = new Vector<Metadata>(0);
        this.versions = new Vector<Metadata>(0);
        //this.propSet = new SimpleResourcePropertySet(MetadataQNames.RESOURCE_PROPERTIES);
    }
    
    @WebMethod
    public int add(@WebParam(name = "uri") String uri) {
        int index = this.findMetadataByUri(uri);
        if( index == -1){
            
            Metadata aboutToAdd = new Metadata(uri);
            
            this.metadata.add(aboutToAdd);
            index = this.metadata.indexOf(aboutToAdd);
        }
        
        this.version(this.metadata.get(index));
        return index;
    }
    
    @WebMethod
    public int delete(@WebParam(name = "uri") String uri) {
        int index = this.findMetadataByUri(uri);
        if( index != -1){
            Metadata removed = this.metadata.remove(index);
            return index;
            //Metadata aboutToAdd = new Metadata(uri);
            //this.metadata.add(aboutToAdd);
            //index = this.metadata.indexOf(aboutToAdd);
        } else {
            System.err.println("URI:\"" + uri + "\" does not exist.");
            return index;
        }
        
        //this.version(this.metadata.get(index));
        //return index;
    }
    
    @WebMethod
    public String toString(@WebParam(name = "uri") String uri) {
        int index = this.findMetadataByUri(uri);
        if( index == -1){
            return "Not found";
        } else {
            return this.metadata.get(index).toString();
        }
    }
    
    @WebMethod
    public int versions(@WebParam(name = "uri") String uri){
        int total = 0;
        
        for(int i = 0; i < this.versions.size(); i++){
            if(this.versions.get(i).getUri().equals(uri)){
                total ++;
            }
        }
        
        return total;
    }
    
    @WebMethod
    public int annotate(@WebParam(name = "uri")String uri,
            @WebParam(name = "rule")String rule,
    @WebParam(name = "value")String value){
        
        int index = this.add(uri);
        Metadata local = this.metadata.get(index);
        local.annotate(rule, value);
        this.metadata.set(index, local);
        return this.version(local);
    }
    
    @WebMethod
    public boolean deAnnotate(@WebParam(name = "uri")String uri,
            @WebParam(name = "rule")String rule,
    @WebParam(name = "value")String value){
        
        int index       = this.add(uri);
        Metadata local  = this.metadata.get(index);
        boolean result  = local.removeAnno(rule,value);
        
        if(result){
            this.metadata.set(index, local);
            this.version(local);
        }
        
        return result;
    }
    
    @WebMethod
    public int reference(@WebParam(name = "uri")String uri,
            @WebParam(name = "rule")String rule,
    @WebParam(name = "uriTo")String uriTo){
        
        int index       = this.add(uri);
        int refIndex    = this.add(uriTo);
        Metadata local  = this.metadata.get(index);
        
        local.reference(rule, uriTo);
        this.metadata.set(index, local);
        return this.version(local);
    }
    
    @WebMethod
    public boolean deReference(@WebParam(name = "uri")String uri,
            @WebParam(name = "rule")String rule,
    @WebParam(name = "uriTo")String uriTo){
        
        int index       = this.add(uri);
        Metadata local  = this.metadata.get(index);
        boolean result  = local.removeRef(rule, uriTo);
        
        if(result){
            this.metadata.set(index, local);
            this.version(local);
        }
        
        return result;
    }
    
    @WebMethod
    public String getNetwork(){
        Iterator<Metadata> itr = this.metadata.iterator();
        int indexCount = 1;
        String networkString = "*Vertices " + this.metadata.size() +"\n";
        while(itr.hasNext()){
            Metadata next = itr.next();
            networkString += "" + indexCount + " \"" + next.getUri() +"\"\n";
            this.annotate(next.getUri(),"network_int_id",""+indexCount+"");
            indexCount ++;
        }
        
        itr = this.metadata.iterator();
        networkString += "*arcslist\n";
        while(itr.hasNext()){
            Metadata next = itr.next();
            String stringIndex = next.getSingleValueAnnotation("network_int_id");
            Integer indexObject = new Integer(stringIndex);
            int index = indexObject.intValue();
            
            boolean newline = false;
            Set<String> referenceKeys = next.getReferenceKeys();
            Iterator<String> refItr = referenceKeys.iterator();
            if(refItr.hasNext()){
                networkString += stringIndex;
                newline = true;
            }
            while(refItr.hasNext()){
                String refRule = refItr.next();
                Set<String> values = next.getReferenceValues(refRule);
                Iterator<String> refValItr = values.iterator();
                while(refValItr.hasNext()){
                    String refValue = refValItr.next();
                    Metadata temp = this.metadata.get(this.findMetadataByUri(refValue));
                    networkString += " " + temp.getSingleValueAnnotation("network_int_id");
                }
            }
            
            if(newline) networkString += "\n";
            
        }
        return networkString;
    }
    
    @WebMethod
    public byte[] saveGraphAsJPEG() {
        //String filename = "c:\\temp\\file.png";
        Container c = new Container();
        Graph g = this.getGraph();
        Layout l = new ISOMLayout ( g );
        PluggableRenderer r = new PluggableRenderer();
        
        r.setVertexStringer(StringLabeller.getLabeller(g,PajekNetReader.LABEL) );
        //r.setVertexLabelCentering(true);
        VisualizationViewer vv = new VisualizationViewer( l, r, new Dimension(600,600));
        java.awt.image.BufferedImage bi = new java.awt.image.BufferedImage(600,600,java.awt.image.BufferedImage.TYPE_INT_RGB);
        Graphics2D gr = bi.createGraphics();
        // MOST-IMPORTANT
        vv.setSize(800,600);
        // MOST-IMPORTANT
        c.addNotify();
        c.add(vv);
        c.setVisible(true);
        c.paintComponents(gr);
        
        
        try {
            ByteArrayOutputStream stream = new ByteArrayOutputStream();
            ImageIO.write(bi,"png",stream);
            byte[] bytes = stream.toByteArray();
            return bytes;
            
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }
    }
    
    private Graph getGraph(){
        String network = this.getNetwork();
        StringReader stringReader = new StringReader(network);
        PajekNetReader netReader = new PajekNetReader(true);
        try {
            Graph g = netReader.load(stringReader);
            return g;
        } catch (IOException ex) {
            ex.printStackTrace();
            return null;
        }
    }
    
    private int findMetadataByUri(String uri){
        int index;
        if(this.metadata.size() > 0 && !this.metadata.isEmpty()){
            for(index = 0; index < this.metadata.size(); index++){
                try{
                    Metadata current = this.metadata.get(index);
                    if(current.getUri().equals(uri)){
                        return index;
                    }
                } catch (java.lang.ArrayIndexOutOfBoundsException e){
                    System.err.println("Array Index Out Of Bounds Exception: " + index);
                    System.err.println("Array Index Out Of Bounds Exception: " + this.metadata.capacity());
                    e.printStackTrace();
                    System.exit(-1);
                }
                
            }
        }
        return -1;
    }
    
    private int version(Metadata metadata){
        this.versions.add(metadata);
        return this.versions(metadata.getUri());
    }

    /*public ResourcePropertySet getResourcePropertySet() {
        return this.propSet;
    }*/
    
}
