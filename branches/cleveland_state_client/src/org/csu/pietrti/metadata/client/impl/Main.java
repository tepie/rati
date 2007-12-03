/*
 * Main.java
 *
 * Created on November 20, 2007, 11:44 AM
 *
 * To change this template, choose Tools | Template Manager
 * and open the template in the editor.
 */

package org.csu.pietrti.metadata.client.impl;

import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;

/**
 *
 * @author PIETRTI
 */
public class Main {
    
    private static MetadataServiceService metadataService ;
    private static MetadataService metadataPort;
    
    /** Creates a new instance of Main */
    public Main() {
        init();
    }
    
    private static void init(){
        metadataService = new MetadataServiceService();
        metadataPort    = metadataService.getMetadataServicePort();
    }
    
    private static void cleanupService(){
        
        try{
            int result;
            result = metadataPort.delete("Terry");
            result = metadataPort.delete("Dan");
            result = metadataPort.delete("Bill");
            result = metadataPort.delete("Tom C.");
            result = metadataPort.delete("Tom M.");
            result = metadataPort.delete("Steve");
            result = metadataPort.delete("Alan");
            
        } catch (Exception ex) {
            ex.printStackTrace();
        }
        
    }
    
    private static void loadService(){
        int result;
        int version;
        try{
            metadataPort.reset();
            
            result = metadataPort.add("Terry");
            result = metadataPort.add("Dan");
            result = metadataPort.add("Bill");
            result = metadataPort.add("Tom C.");
            result = metadataPort.add("Tom M.");
            result = metadataPort.add("Steve");
            result = metadataPort.add("Alan");
            
            version = metadataPort.annotate("Terry","business title","Software Developer");
            version = metadataPort.annotate("Terry","education level","Masters");
            version = metadataPort.annotate("Dan","business title","Data Architect");
            version = metadataPort.annotate("Bill","business title","Database Developer");
            version = metadataPort.annotate("Tom C.","business title","Software Developer");
            version = metadataPort.annotate("Tom M.","business title","Team Manager");
            version = metadataPort.annotate("Steve","business title","CIO");
            version = metadataPort.annotate("Alan","business title","Group Manager");
            
            version = metadataPort.reference("Terry","reports too", "Dan");
            version = metadataPort.reference("Dan","reports too", "Tom M.");
            version = metadataPort.reference("Terry","is a peer too", "Bill");
            version = metadataPort.reference("Bill","is a peer too", "Terry");
            version = metadataPort.reference("Tom C.","is a peer too", "Terry");
            version = metadataPort.reference("Steve","is a manager of", "Alan");
            version = metadataPort.reference("Alan","is a manager of", "Tom M.");
            
            version = metadataPort.reference("Alan","has met", "Terry");
            version = metadataPort.reference("Alan","has not met", "Bill");
         
        } catch (Exception ex) {
            ex.printStackTrace();
        }
        
    }
    
    private static void writeFile(byte[] byteArray, String filename){
        FileOutputStream outputStream;
        try {
            outputStream = new FileOutputStream(filename);
            outputStream.write(byteArray);
            
        } catch (FileNotFoundException ex) {
            ex.printStackTrace();
        }catch (IOException ex) {
            ex.printStackTrace();
        }
        
    }
    
    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        try { // Call Web Service Operation
            init();
            loadService();
            
            String network = metadataPort.getNetwork();
            System.out.println("Network String =  " + network);
            
            byte[] stream   = metadataPort.saveGraphAsJPEG();
            String filename = "c:\\temp\\metadata_" + stream.hashCode() + ".png";
            
            writeFile(stream,filename);
            
            System.out.println("Graph written too file = " + filename);
            
            cleanupService();
            
        } catch (Exception ex) {
            ex.printStackTrace();
        }
    }
    
}
