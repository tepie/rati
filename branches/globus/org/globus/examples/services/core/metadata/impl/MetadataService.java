/**
 * MetadataService.java
 * 
 * Grid service implementation of a metadata storage concepts
 * for visualizationg. 
 * 
 * CIS 689 Grid Computing 
 * Cleveland State University
 * 
 */

package org.globus.examples.services.core.metadata.impl;

import edu.uci.ics.jung.graph.Graph;
import edu.uci.ics.jung.graph.decorators.StringLabeller;
import edu.uci.ics.jung.io.PajekNetReader;
import edu.uci.ics.jung.visualization.FRLayout;
import edu.uci.ics.jung.visualization.ISOMLayout;
import edu.uci.ics.jung.visualization.Layout;

import edu.uci.ics.jung.visualization.PluggableRenderer;
import edu.uci.ics.jung.visualization.VisualizationViewer;
import java.awt.Container;
import java.awt.Dimension;
import java.awt.Graphics2D;
import java.io.ByteArrayOutputStream;
import java.rmi.RemoteException;
import java.io.IOException;
import java.io.StringReader;
import java.util.Iterator;
import java.util.Set;
import java.util.Vector;
import javax.imageio.ImageIO;

import org.globus.wsrf.Resource;
import org.globus.wsrf.ResourceProperties;
import org.globus.wsrf.ResourceProperty;
import org.globus.wsrf.impl.ReflectionResourceProperty;
import org.globus.wsrf.ResourcePropertySet;
import org.globus.wsrf.impl.SimpleResourcePropertySet;

/**
 * Grid metadata service container
 * @author Terrence Anthony Pietrondi
 */
public class MetadataService implements Resource, ResourceProperties{
    private Vector<Metadata> metadata;
    private Vector<Metadata> versions;
    private ResourcePropertySet propSet;

    /**
     * Construct a new metadata service
     * @throws RemoteException
     * @throws java.lang.Exception
     */
    public MetadataService() throws RemoteException, java.lang.Exception{
		this.reset();
    }

    /**
     * Reset the service
     * Clear all contents in terms of metadata
     * @return ResetResponse object, which is void
     * @throws java.lang.Exception
     */
    public org.globus.examples.stubs.MetadataService.ResetResponse reset() throws java.lang.Exception{
		this.propSet = new SimpleResourcePropertySet(MetadataQNames.RESOURCE_PROPERTIES);
        this.metadata = new Vector<Metadata>(0);
        this.versions = new Vector<Metadata>(0);
		
		return new org.globus.examples.stubs.MetadataService.ResetResponse();
        
    }
    /**
     * Add metadata
     * @param uri the label for the metadata
     * @return the version number of this metadata
     */
	synchronized public int add(String uri) {
        int index = this.findMetadataByUri(uri);
        if( index == -1){
            Metadata aboutToAdd = new Metadata(uri);
            this.metadata.add(aboutToAdd);
            index = this.metadata.indexOf(aboutToAdd);
        }
        
        this.version(this.metadata.get(index));
        return index;
    }
    
	/**
	 * Delete metadata
	 * @param uri the label for the metadata
	 * @return the index of the removed metadata 
	 */
	synchronized public int delete(String uri) {
        int index = this.findMetadataByUri(uri);
        if( index != -1){
            Metadata removed = this.metadata.remove(index);
            return index;
        } else {
            System.err.println("URI:\"" + uri + "\" does not exist.");
            return index;
        }
    }
    
	/**
	 * Get metadata as a string
	 * @param uri the label for the metadata
	 * @return string representation of the metadata
	 */
	synchronized public String toString(String uri) {
        int index = this.findMetadataByUri(uri);
        if( index == -1){
            return "Not found";
        } else {
            return this.metadata.get(index).toString();
        }
    }
	
	/**
	 * See how many versions there are for a metadata
	 * @param uri the label for the metadata
	 * @return the number of versions
	 */
	synchronized public int versions(String uri){
        int total = 0;
        
        for(int i = 0; i < this.versions.size(); i++){
            if(this.versions.get(i).getUri().equals(uri)){
                total ++;
            }
        }
        
        return total;
    }
    
	/**
	 * Annotate metadata
	 * @param a metadata service annotation object
	 * @return the version of the annotated metadata
	 */
	synchronized public int annotate(org.globus.examples.stubs.MetadataService.Annotate a){
		int index = this.add(a.getUri());
        Metadata local = this.metadata.get(index);
		local.annotate(a.getRule(),a.getValue());
        this.metadata.set(index, local);
        return this.version(local);
    }
   
	/**
	 * Remove an annotation 
	 * @param a a metadata de-annotation object
	 * @return the version of the annotated metadata
	 */
	synchronized public boolean deAnnotate(org.globus.examples.stubs.MetadataService.DeAnnotate a){
        
		int index = this.add(a.getUri());
		
        Metadata local  = this.metadata.get(index);
        boolean result  = local.removeAnno(a.getRule(),a.getValue());
        
        if(result){
            this.metadata.set(index, local);
            this.version(local);
        }
        
        return result;
    }
    
	/**
	 * Add a reference
	 * @param a a metadata service reference object
	 * @return the version of the source of the reference
	 */
    synchronized public int reference(org.globus.examples.stubs.MetadataService.Reference a){
        
		int index = this.add(a.getUri());

        int refIndex    = this.add(a.getUriTo());
        Metadata local  = this.metadata.get(index);
        
        local.reference(a.getRule(),a.getUriTo());
        this.metadata.set(index, local);
        return this.version(local);
    }
    
    /**
     * Remove a reference
     * @param a a metadata service de-reference object
     * @return true if reference removed
     */
	synchronized public boolean deReference(org.globus.examples.stubs.MetadataService.DeReference a){
		int index 		= this.add(a.getUri());
        Metadata local  = this.metadata.get(index);
        boolean result  = local.removeRef(a.getRule(),a.getUriTo());
        
        if(result){
            this.metadata.set(index, local);
            this.version(local);
        } else {
			System.err.println("DeReference failed! Index: " + index);
		}
        
        return result;
    }

	/**
	 * Get the network string used in the image generation
	 * @return the string network representation
	 */
    synchronized public String getNetwork(){
		
        int vertexIndexCount = 0;
        String networkString = "*Vertices " + this.metadata.size() +"\n";
        Iterator<Metadata> itrMetada = this.metadata.iterator();
		
		for(int counting = 0; counting < this.metadata.size(); counting++){
			Metadata next = this.metadata.get(counting);
			int tempcount = counting+1;
			networkString += "" + tempcount + " \"" + next.getUri() +"\"\n";
			
			next.annotate("network_int_id",""+tempcount);
			this.metadata.set(counting,next);
		}
        
        networkString += "*arcslist\n";
		int indexCount = 0;
		while(indexCount < this.metadata.size()){
          
			Metadata next = this.metadata.get(indexCount);
			//String stringIndex = next.getAnnoRuleValues("network_int_id");

            //Integer indexObject = new Integer(stringIndex);
            //int index = indexObject.intValue();
			//int index = indexCount + 1;
            
            boolean newline = false;
            Set<String> referenceKeys = next.getReferenceKeys();
            Iterator<String> refItr = referenceKeys.iterator();
            if(refItr.hasNext()){
				int temp = indexCount + 1;
                networkString += "" + temp  + "";
                newline = true;
            }
			
            while(refItr.hasNext()){
                String refRule = refItr.next();
                String value = next.getReferenceValues(refRule);
				Metadata temp = this.metadata.get(this.findMetadataByUri(value));
				networkString += " " + temp.getAnnoRuleValues("network_int_id");
            }
            
            if(newline) networkString += "\n";
			indexCount++;
            
        }
        return networkString;
    }
    
    /**
     * Get the contents of the server as an image
     * @return an image byte stream array
     */
    synchronized public byte[] saveGraphAsJPEG() {
        Container c = new Container();
        Graph g = this.getGraph();
        Layout l = new ISOMLayout ( g );
        PluggableRenderer r = new PluggableRenderer();
        
        r.setVertexStringer(StringLabeller.getLabeller(g,PajekNetReader.LABEL) );
        
        VisualizationViewer vv = new VisualizationViewer( l, r, new Dimension(600,600));
        java.awt.image.BufferedImage bi = new java.awt.image.BufferedImage(600,600,java.awt.image.BufferedImage.TYPE_INT_RGB);
        Graphics2D gr = bi.createGraphics();
        
        vv.setSize(800,600);
        
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
    
    /**
     * Get a graph object needed for image creation
     * @return graph object
     */
    synchronized private Graph getGraph(){
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
    
    /**
     * Get the index of metadata by its uri
     * @param uri the uri of interest
     * @return the index if found, -1 if not found
     */
    synchronized private int findMetadataByUri(String uri){
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
    
    /**
     * Version a metadata object
     * @param metadata the metadata object to version
     * @return the current version number
     */
    synchronized private int version(Metadata metadata){
        this.versions.add(metadata);
        return this.versions(metadata.getUri());
    }
    
    /**
     * Needed for implementated interfaces
     * @return
     */
    synchronized public ResourcePropertySet getResourcePropertySet() {
        return this.propSet;
    }
    
}
