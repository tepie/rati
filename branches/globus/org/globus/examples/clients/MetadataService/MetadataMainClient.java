/**
 * MetadataMainClient.java
 * 
 * Grid service implementation of a metadata storage concepts
 * for visualization. 
 * 
 * CIS 689 Grid Computing 
 * Cleveland State University
 * 
 */

package org.globus.examples.clients.MetadataService;

import org.apache.axis.message.addressing.Address;
import org.apache.axis.message.addressing.EndpointReferenceType;

import org.globus.examples.stubs.MetadataService.MetadataServiceServicePortType;
import org.globus.examples.stubs.MetadataService.service.MetadataServiceAddressingLocator;

import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;

/**
 * An example metadata client
 * 
 * @author Terrence A. Pietrondi
 *
 */
public class MetadataMainClient {
	
	/**
	 * Clean up the metadata service
	 * Delete all known added members in the service
	 * to clean it up.
	 * @param metadataPort The service port type
	 */
    private static void cleanupService(MetadataServiceServicePortType metadataPort){
        
        try{
            metadataPort.delete("Terry");
			metadataPort.delete("Dan");
			metadataPort.delete("Bill");
			metadataPort.delete("Tom C.");
			metadataPort.delete("Tom M.");
			metadataPort.delete("Steve");
			metadataPort.delete("Alan");
        } catch (Exception ex) {
            ex.printStackTrace();
			System.exit(-1);
        }
        
    }
    
    /**
     * Load the metadata service
     * Load service by adding new members, adding annotations
     * and adding references
     * @param metadataPort The service port type
     */
    private static void loadService(MetadataServiceServicePortType metadataPort){
	
        try{
		
            metadataPort.reset(new org.globus.examples.stubs.MetadataService.Reset());
            
            metadataPort.add("Terry");
			metadataPort.add("Dan");
			metadataPort.add("Bill");
			metadataPort.add("Tom C.");
			metadataPort.add("Tom M.");
			metadataPort.add("Steve");
			metadataPort.add("Alan");
			
			metadataPort.annotate(new org.globus.examples.stubs.MetadataService.Annotate("title","Terry","Big shot"));
			metadataPort.annotate(new org.globus.examples.stubs.MetadataService.Annotate("title","Dan","Bigger shot"));
			metadataPort.annotate(new org.globus.examples.stubs.MetadataService.Annotate("title","Bill","Big shot"));
			metadataPort.annotate(new org.globus.examples.stubs.MetadataService.Annotate("title","Alan","Bigger shot"));
			metadataPort.annotate(new org.globus.examples.stubs.MetadataService.Annotate("title","Steve","Biggest shot"));
			
           metadataPort.reference(new org.globus.examples.stubs.MetadataService.Reference("Reports To","Terry","Dan"));
		   metadataPort.reference(new org.globus.examples.stubs.MetadataService.Reference("Reports To","Bill","Dan"));
		   metadataPort.reference(new org.globus.examples.stubs.MetadataService.Reference("Reports To","Dan","Tom M."));
		   metadataPort.reference(new org.globus.examples.stubs.MetadataService.Reference("Reports To","Tom M.","Alan"));
		   metadataPort.reference(new org.globus.examples.stubs.MetadataService.Reference("Reports To","Alan","Steve"));
		   metadataPort.reference(new org.globus.examples.stubs.MetadataService.Reference("Are Peers","Terry","Bill"));
		   metadataPort.reference(new org.globus.examples.stubs.MetadataService.Reference("Are Friends","Terry","Alan"));
		   metadataPort.reference(new org.globus.examples.stubs.MetadataService.Reference("Has never met","Steve","Dan"));
		   
        } catch (Exception ex) {
            ex.printStackTrace();
			System.exit(-1);
        }
    }
    
    /**
     * Write a given byte array to a file
     * This is used when the client gets the content
     * from the servie as a byte array to render as an image. 
     * In order to store the image, we write it as a file from
     * the service. 
     * @param byteArray the byte array to be written to a file
     * @param filename the desired file name to write the byte array too
     */
    private static void writeFile(byte[] byteArray, String filename){
        FileOutputStream outputStream;
        try {
            outputStream = new FileOutputStream(filename);
            outputStream.write(byteArray);
            System.out.println("Wrote to file: " + filename);
        } catch (FileNotFoundException ex) {
            ex.printStackTrace();
        }catch (IOException ex) {
            ex.printStackTrace();
        }
        
    }
    
    /**
     * Main entry point of client
     * @param args expects a single command line arguement pointing to the URL of the service
     */
    public static void main(String[] args) {
		MetadataServiceAddressingLocator locator = new MetadataServiceAddressingLocator();
		
		try{
			String serviceURI = args[0];
			
			EndpointReferenceType endpoint = new EndpointReferenceType();
			endpoint.setAddress(new Address(serviceURI));
			
			MetadataServiceServicePortType metadata 
					= locator.getMetadataServiceServicePortTypePort(endpoint);
					
			metadata = locator.getMetadataServiceServicePortTypePort(endpoint);
			
			loadService(metadata);
			
			String network = metadata.getNetwork(new org.globus.examples.stubs.MetadataService.GetNetwork());
			System.out.println("Get network: " + network);
			
			byte[] stream = metadata.saveGraphAsJPEG(new org.globus.examples.stubs.MetadataService.SaveGraphAsJPEG());
			
			String filename = "c:\\temp\\metagrid_" + stream.hashCode() + ".png";
			writeFile(stream,filename);
			
			cleanupService(metadata);
			
		} catch (Exception e) {
			e.printStackTrace();
		}
    }
    
}
