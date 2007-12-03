/*
 * Metadata.java
 *
 * Created on November 20, 2007, 9:11 AM
 *
 * To change this template, choose Tools | Template Manager
 * and open the template in the editor.
 */

package org.csu.tepietro.metadata;

import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;
import java.util.Set;

/**
 *
 * @author PIETRTI
 */
public class Metadata {
    
    
    /**
     * Metadata annotation map
     */
    private HashMap<String,HashSet<String>> annotations;
    /**
     * Metadata reference map
     */
    private HashMap<String,HashSet<String>> references;
    /**
     * The URI of this metadata
     */
    private String uri;
    
    /**
     * Construct a new metadata object
     * @param uri The URI of the metadata
     */
    public Metadata(String uri) {
        this.uri = uri;
        this.init();
    }
    
    /**
     * Print this metadata
     */
    public String toString(){
        /**
         * The return value container
         */
        String returnString = "";
        /**
         * An iterator
         */
        Iterator<String> keyItr;
        /**
         * A set
         */
        Set<String> keys;
        
        /**
         * Get the annotation kets
         */
        keys = this.annotations.keySet();
        
        /**
         * Iterate the annotation keys
         */
        keyItr = keys.iterator();
        while(keyItr.hasNext()){
            /**
             * The current key
             */
            String currentKey = keyItr.next();
            /**
             * The curreent key's annotation values
             */
            Set<String> values = this.annotations.get(currentKey);
            /**
             * An inner-value iterator
             * Iterate the values
             */
            Iterator<String> valueItr = values.iterator();
            while(valueItr.hasNext()){
                /**
                 * The current value
                 */
                String currentValue = valueItr.next();
                /**
                 * Add the current key and the value to the return container
                 */
                returnString += currentKey + ":" + currentValue + ";";
            }
        }
        
        /**
         * The reference keys
         */
        keys = this.references.keySet();
        /**
         * Iterate the reference keys
         * Do the same above
         * Can likely refactor or clean this
         * duplication of code
         */
        keyItr = keys.iterator();
        while(keyItr.hasNext()){
            String currentKey = keyItr.next();
            Set<String> values = this.references.get(currentKey);
            Iterator<String> valueItr = values.iterator();
            while(valueItr.hasNext()){
                String currentValue = valueItr.next();
                returnString += currentKey + "->" + currentValue + ";";
            }
        }
        
        /**
         * Check if the return container is empty.
         * If it is, just show the URI.
         * Otherwise, show the URI with the return
         * container.
         */
        if(returnString.equals("")){
            return this.getUri();
        } else {
            return this.getUri() + "=" + returnString;
        }
        
    }
    
    /**
     * Get the URI of this  metadata object
     * @return String URI name
     */
    public String getUri(){
        return this.uri;
    }
    
    public String getSingleValueAnnotation(String rule){
        Object[] arrayAnnotations = this.getAnnoRuleValues(rule).toArray();
        //System.err.println("I got to this section of code");
        if(arrayAnnotations.length == 1){
            
            return (String)arrayAnnotations[0];
        }
        //System.err.println("Annotations Array:" + arrayAnnotations);
        return null;
    }
    
    public HashSet<String> getAnnoRuleValues(String rule){
        if(this.hasAnnoRule(rule)){
            return this.annotations.get(rule);
        } else {
            return null;
        }
    }
    
    public Set<String> getAnnotationKeys(){
        return this.annotations.keySet();
    }
    
    public Set<String> getReferenceKeys(){
        return this.references.keySet();
    }
    
    public Set<String> getReferenceValues(String key){
        return this.references.get(key);
    }
    
    /**
     * Annotate this metadata.
     * Annotations are textual, and are non-referencing. They
     * indicate no links or constraints, they are documentation
     * like.
     * @param rule The name of the annotation rule
     * @param value The value of the annotation
     */
    public void annotate(String rule, String value){
        //boolean returnValue = false;
        if(this.hasAnnoValue(rule, value)){
            // Pass
            //returnValue = true;
            return;
        } else {
            if(!this.hasAnnoRule(rule)){
                this.addAnnoNewRule(rule);
                //returnValue = true;
            }
            /**
             * Create an overwrite set of annotations
             */
            HashSet<String> overwrite = this.annotations.get(rule);
            /**
             * Add the value to this set
             */
            overwrite.add(value);
            /**
             * Put the new set in the annotation container
             */
            this.annotations.put(rule, overwrite);
            //return true;
        }
    }
    
    /**
     * Remove an annotation from this metadata.
     * Since the same annotation can have many values,
     * the value of the annotation must be given.
     * @param rule The rule of the annotation
     * @param value The value of the annotation
     */
    public boolean removeAnno(String rule, String value){
        if(this.hasAnnoValue(rule, value)){
            HashSet<String> values = this.annotations.get(rule);
            if(values.contains(value)){
                values.remove(value);
                this.annotations.put(rule, values);
                return true;
            } else {
                System.err.println(this.getUri() + " does not have rule " + rule + "with value " + value);
                return false;
            }
            
        } else {
            System.err.println(this.getUri() + " does not have rule " + rule + "with value " + value);
            return false;
        }
    }
    
    /**
     * Remove a reference from this metadata.
     * Since the same reference can have many values,
     * the URI to the referencing metadata must be given.
     * @param rule The rule of the reference
     * @param uriTo The URI of the referencing metadata
     */
    public boolean removeRef(String rule, String uriTo) {
        if(this.hasRefValue(rule, uriTo)){
            HashSet<String> values = this.references.get(rule);
            if(values.contains(uriTo)){
                values.remove(uriTo);
                this.annotations.put(rule, values);
                return true;
            } else {
                return false;
            }
           
        } else {
            return false;
        }
        
    }
    
    /**
     * Add a reference to this metadata
     * @param rule The rule of the reference
     * @param uri The URI to the metadata this reference references
     */
    public void reference(String rule, String uri){
        if(this.hasRefValue(rule, uri)){
            // Pass
            return;
        } else {
            if(!this.hasRefRule(rule)){
                this.addRefNewRule(rule);
            }
            
            HashSet<String> overwrite = this.references.get(rule);
            overwrite.add(uri);
            
            this.references.put(rule, overwrite);
        }
    }
    
    /**
     * Init this object
     */
    private void init(){
        this.annotations = new HashMap<String,HashSet<String>>();
        this.references = new HashMap<String,HashSet<String>>();
    }
    
    /**
     * Add a new annotation rule to the annotation map
     * @param rule The name of the annotation rule
     */
    private void addAnnoNewRule(String rule){ 
        this.annotations.put(rule, new HashSet<String>());
    }
    
    /**
     * See if this metadata already contains an annotation rule
     * @param rule The name of the annotation rule
     * @return True of this metadata contains the rule, false otherwise.
     */
    private boolean hasAnnoRule(String rule){
        return this.annotations.containsKey(rule);
    }
    
    /**
     * See if this metadata has a specific value for a rule.
     * A rule can be assigned many times with varying values.
     * @param rule The name of the annotation rule
     * @param value The value of the annotation
     * @return True if this metadata contains the annotation value, false otherwise.
     */
    private boolean hasAnnoValue(String rule,String value){
        
        if(this.hasAnnoRule(rule)){
            HashSet<String> local = this.annotations.get(rule);
            Iterator<String> localItr = local.iterator();
            while(localItr.hasNext()){
                if(localItr.next().equals(value)){
                    return true;
                }
            }
            return false;
        } else {
            return false;
        }
        
    }
    
    /**
     * Add a new reference rule
     * @param rule The name of the reference rule
     */
    private void addRefNewRule(String rule){
        this.references.put(rule, new HashSet<String>());
    }
    
    /**
     * See if this metadata contains a specific reference rule
     * @param rule The name of the reference rule
     * @return True if the metadata contains this reference rule, false otherwise.
     */
    private boolean hasRefRule(String rule){
        return this.references.containsKey(rule);
    }
    
    /**
     * See if this metadata has a specific reference value in a rule.
     * Since the rule can be given multiple values.
     * @param rule The name of the reference rule
     * @param value The name of the reference value
     * @return True if the reference value is contained in the rule set.
     */
    private boolean hasRefValue(String rule,String value){
        
        if(this.hasAnnoRule(rule)){
            HashSet<String> local = this.references.get(rule);
            try{
                Iterator<String> localItr = local.iterator();
                while(localItr.hasNext()){
                    if(localItr.next() == value){
                        return true;
                    }
                }
            } catch (java.lang.NullPointerException e){
                return false;
            }
            
            return false;
        } else {
            return false;
        }
        
    }
    
    
}
