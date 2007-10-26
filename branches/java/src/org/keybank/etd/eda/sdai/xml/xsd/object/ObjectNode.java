/**
 * 
 */
package org.keybank.etd.eda.sdai.xml.xsd.object;

import java.util.HashMap;

import javax.swing.tree.DefaultMutableTreeNode;
import javax.swing.tree.TreeNode;

import org.xml.sax.Locator;

/**
 * @author pietrti
 *
 */
public class ObjectNode extends DefaultMutableTreeNode {
	
	private static final long serialVersionUID 		= 1L;
	
	/**
	 * The annotation map.
	 * This map contains textual metadata about this node
	 */
	private HashMap <String,String> annotationMap	= null;
	/**
	 * The extra reference map.
	 * This map contains extra reference type metadata about this node.
	 * Parent information should not be added because this is already tracked
	 * by the tree structure. The extra references are those that are not
	 * tree like, but graph like. 
	 */
	private HashMap <String,ObjectNode> extraReferenceMap = null;
	//private String category = null;
	
	/**
	 * The path separator.
	 * This is used to build a URI like path for this node. 
	 */
	public static final String generatedPathSep 	= "/";
	/**
	 * The primary annotation of this node.
	 * This annotation is nothing more then a primary label for the node,
	 * it can be anything depending on how you want to use the nodes.
	 */
	protected static final String primaryAnnotation = "Artifact Name";
	/**
	 * The locator annotation of this node.
	 * All nodes have a location relative to a tree, this annotation
	 * is the label for that annotation.
	 */
	protected static final String primaryLocation  	= "File Name";
	
	/**
	 * Construct a new org.keybank.etd.eda.sdai.xml.xsd.object node
	 * @see org.xml.sax.Locator
	 * @param primaryAnnotation the primary annotation used to identify this node
	 * @param locator
	 */
	public ObjectNode(String primaryAnnotation, Locator locator) {
		// Create the metadata maps for this node
		this.annotationMap 		= new HashMap<String,String>();
		this.extraReferenceMap 	= new HashMap<String,ObjectNode>();
		
		// Add the primary annotation to the map
		this.putAnno(ObjectNode.primaryAnnotation, this.basename(primaryAnnotation));
		
        if (locator == null) {
        	// I am not sure why this block is here?
        }
        else {
        	// Add the location annotation to the map
        	this.putAnno(ObjectNode.primaryLocation, fileNameFromLocator(locator));
        }
	}
	
	public String getPrimaryAnnotation(){
		return this.getAnno(ObjectNode.primaryAnnotation);
	}
	
	
	/**
	 * Put an annotation on this node
	 * @param key The label for the annotation
	 * @param value The value for the annotation
	 * @return The string result of putting the annotation on the map
	 * @see java.util.HashMap#put(Object, Object)
	 */
	public String putAnno(String key, String value){
		return this.annotationMap.put(key,value);
	}
	
	/**
	 * Put a reference on this node
	 * @param key The label for the reference
	 * @param value The value for the reference
	 * @return The string result of putting the annotation on the map
	 * @see java.util.HashMap#put(Object, Object)
	 */
	public ObjectNode putRef(String key, ObjectNode value){
		return this.extraReferenceMap.put(key,value);
	}
	
	/*public String getCategoryLabel(){
		return this.category;
	}
	
	public void setCategoryLabel(String label){
		this.category = label;
	}*/
	
	public void putAllAnnos(HashMap<String,String> all){
		this.annotationMap.putAll(all);
	}
	
	public void putAllRefs(HashMap<String,ObjectNode> all){
		this.extraReferenceMap.putAll(all);
	}
	
	public String getAnno(String key){
		return this.annotationMap.get(key);
	}
	
	public ObjectNode getRef(String key){
		return this.extraReferenceMap.get(key);
	}
	
	public HashMap<String,String> getAllAnnos(){
		return this.annotationMap;
	}
	
	public HashMap<String,ObjectNode> getAllRefs(){
		return this.extraReferenceMap;
	}
	
	/**
	 * Generate a textual URI like path for this node to its parent
	 * @return A URI like string path
	 */
	public String printPath(){
		String path = "";
		
		TreeNode[] paths = this.getPath();
		for(TreeNode loopNode : paths){
			ObjectNode obj = (ObjectNode)loopNode;
			path = path + ObjectNode.generatedPathSep + obj.getAnno(ObjectNode.primaryAnnotation);	
		}
		
		return path;
	}
	
	/**
	 * Will return the path of this node to its parent
	 */
	public String toString(){
		return this.printPath();
		
	}
	
	/* 
	 * Private functions begin here
	 */
	
	private String basename(String path){
		 int lastSlashPosition = path.lastIndexOf("/");
        if(lastSlashPosition != -1){
        	path = path.substring(lastSlashPosition + 1);
        }
        return path;
	}
	
	/**
	 * Generate the print tabs for this node.
	 * The print tabs indent output based on the getLevel()
	 * of this node.
	 * @see javax.swing.tree.DefaultMutableTreeNode#getLevel()
	 * @return a string with the number of tabs returned by getLevel
	 */
	@SuppressWarnings("unused")
	private String printTabs(){
		String tabs = "";
		for(int i = 0; i< this.getLevel(); i++){
			tabs = tabs + "\t";
		}
		return tabs;
	}
	
	private String fileNameFromLocator(Locator locator){
		 String filename = locator.getSystemId();
        filename = filename.replaceAll("\u002520", " ");
        // strip leading protocol
        if (filename.startsWith("file:/")) {
            filename = filename.substring(6);
        }
        return  this.basename(filename);
	}
	
	/*
	 * Private functions stop here
	 */

}
