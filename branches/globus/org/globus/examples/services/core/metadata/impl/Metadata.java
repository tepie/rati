/**
 * Metadata.java
 * 
 * Grid service implementation of a metadata storage concepts
 * for visualization. 
 * 
 * CIS 689 Grid Computing 
 * Cleveland State University
 * 
 */

package org.globus.examples.services.core.metadata.impl;

import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;
import java.util.Set;

/**
 * The metadata container
 * A metadata object is an abstraction of anything identified by a 
 * string. Metadata supports annotations about itself and references
 * to other metadata. 
 * 
 * @author pietrti
 *
 */
public class Metadata {
    
	private HashMap<String,String> annotations;
    private HashMap<String,String> references;
    private String uri;
	
    /**
     * Construct a new metadata object
     * @param uri the unique label for this metadata object
     */
    public Metadata(String uri) {
        this.uri = uri;
        this.init();
    }
 
    /**
     * Construct the string presentation of this object
     * @return string representation of object
     */
    synchronized public String toString(){
		String returnString = "";
		Iterator<String> keyItr;
		Set<String> keys;
        
        keys = this.annotations.keySet();
		String currentKey;
        keyItr = keys.iterator();
        while(keyItr.hasNext()){
            currentKey = keyItr.next();
			returnString += currentKey + ":" + this.annotations.get(currentKey) + ";";
        }
        
        keys = this.references.keySet();
		
        keyItr = keys.iterator();
        while(keyItr.hasNext()){
			currentKey = keyItr.next();
			returnString += currentKey + "->" + 
				this.references.get(currentKey) + ";";
        }
		
        if(returnString.equals("")){
            return this.getUri();
        } else {
            return this.getUri() + "=" + returnString;
        }
        
    }
	
    /**
     * Get the unique identifier of this object
     * @return a string of the uri
     */
    synchronized public String getUri(){
        return this.uri;
    }
    
    /**
     * Get the annotation value of a rule
     * @param rule the rule that has a value to it
     * @return a string value of the rule on this object, null if none
     */
    synchronized public String getAnnoRuleValues(String rule){
        if(this.hasAnnoRule(rule)){
            return this.annotations.get(rule);
        } else {
            return null;
        }
    }
    
    /**
     * Get the annotation keys used in this object
     * The keys represent the labels on the annotations
     * @return a string set of annotation keys
     */
    synchronized public Set<String> getAnnotationKeys(){
        return this.annotations.keySet();
    }
    
    /**
     * Get the reference keys used in this object
     * The keys represent the labels on the references
     * @return a string set of reference keys
     */
    synchronized public Set<String> getReferenceKeys(){
        return this.references.keySet();
    }
    
    /**
     * Get the value of a reference based on a key
     * @param key the key label for the reference
     * @return the string value of the reference
     */
    synchronized public String getReferenceValues(String key){
        return this.references.get(key);
    }
    
    /**
     * Add an annotation to this object
     * Existing annotation using this rule are not kept
     * @param rule the label of this annotation
     * @param value the value of this annotation
     */
    synchronized public void annotate(String rule, String value){
	   this.annotations.put(rule,value);
    }
    
    /**
     * Remove an annotation from this object
     * If the annotation value is not matched, then the
     * rule is not cleared
     * @param rule the annotation label
     * @param value the annotation value
     * @return true if the annotation was removed, false otherwise
     */
    synchronized public boolean removeAnno(String rule, String value){
        if(this.hasAnnoValue(rule, value)){
            this.annotations.remove(rule);
			return true; 
        } else {
            return false;
        }
    }
    
    /**
     * Remove a reference from this object
     * If the reference value is not matched, then the
     * rule is not cleared
     * @param rule the label of this reference
     * @param uriTo the label of the connecting metadata for this reference
     * @return true on success, false otherwise
     */
    synchronized public boolean removeRef(String rule, String uriTo) {
        if(this.hasRefValue(rule, uriTo)){
			this.references.remove(rule);
			return true;
		} else {
			return false;
		}
           
    }
	
    /**
     * Add a reference to this object
     * @param rule the label of this reference
     * @param uri the value of this reference
     */
    synchronized public void reference(String rule, String uri){
        if(this.hasRefValue(rule, uri)){
            return;
        } else {
            this.references.put(rule, uri);
        }
    }
    
    /**
     * Setup this object
     */
    synchronized private void init(){
        this.annotations = new HashMap<String,String>();
        this.references = new HashMap<String,String>();
    }
    
	
    /*synchronized private void addAnnoNewRule(String rule){ 
        this.annotations.put(rule,null);
    }*/
    
    /**
     * Check to see if this object has a value for the given annotation rule
     * @param rule the label of the annotation rule
     * @return true if this object contains the given annotation key, false otherwise
     */
    synchronized private boolean hasAnnoRule(String rule){
        return this.annotations.containsKey(rule);
    }
    
    /**
     * Check to see if this object has an annotation rule with a specific value
     * @param rule the label of the annotation rule
     * @param value the value of the annotation rule
     * @return true if this object has a annotation with a spefic value
     */
    synchronized private boolean hasAnnoValue(String rule,String value){
        if(this.hasAnnoRule(rule)){
            String local = this.annotations.get(rule);
            if(local.equals(value)){
				return true;
			} else {
				return false;
			}
        } else {
            return false;
        }
    }
    
    /*synchronized private void addRefNewRule(String rule){
        this.references.put(rule,null);
    }*/
    
    /*synchronized private boolean hasRefRule(String rule){
        return this.references.containsKey(rule);
    }*/
    
    /**
     * Check to see if this object has a reference label with a specific value
     */
    synchronized private boolean hasRefValue(String rule,String value){
        if(this.hasAnnoRule(rule)){
            String local = this.references.get(rule);
            if(local.equals(value)){
				return true;
			} else {
	            return false;
			}	
        } else {
            return false;
        }
        
    }
}
