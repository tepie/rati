package service.impl;

import java.util.Iterator;
import java.util.Vector;

/**
 * A metadata service
 * @author pietrti
 *
 */
public class MetadataService {

	/**
	 * Metdata object container
	 */
	private Vector<Metadata> metadata;
	/**
	 * Versions of metadata
	 */
	private Vector<Metadata> versions;
	
	/**
	 * Construct a new metadata service
	 */
	public MetadataService() {
		this.metadata = new Vector<Metadata>(0);
		this.versions = new Vector<Metadata>(0);
	}
	
	/**
	 * Print this service
	 */
	public String toString(){
		String returnString = "";
		Iterator<Metadata> metadataItr = this.iterator();
		while(metadataItr.hasNext()){
			returnString += metadataItr.next().toString() + "\n";
		}
		return returnString;
	}
	
	/**
	 * Get this service's iterator
	 * @return the iterator of metadata
	 */
	public Iterator<Metadata> iterator(){
		return this.metadata.iterator();
	}
	
	/**
	 * Get the number of versions for a URI in this service
	 * @param uri The URI of the metadata to check
	 * @return integer totaling the number of  versions
	 */
	public int versions(String uri){
		int total = 0;
		
		for(int i = 0; i < this.versions.size(); i++){
			if(this.versions.get(i).getUri().equals(uri)){
				total ++;
			}
		}
		
		return total;
	}
	
	/**
	 * Add a metadata object to this service
	 * @param uri The URI of the metadata
	 * @return integer index of the added metadata
	 */
	public int add(String uri){
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
	 * Annotate a metadata object
	 * @param uri The URI of the metadata
	 * @param rule The name of the annotation rule
	 * @param value The value of the annotation rule
	 */
	public void annotate(String uri, String rule, String value){
		int index = this.add(uri);
		Metadata local = this.metadata.get(index);
		local.annotate(rule, value);
		this.metadata.set(index, local);
		this.version(local);
	}
	
	/**
	 * Remove an annotation on a metadata object
	 * @param uri The URI of the metadata
	 * @param rule The name of the annotation rule
	 * @param value The value of the annotation rule
	 */
	public void deAnnotate(String uri, String rule, String value){
		int index = this.add(uri);
		Metadata local = this.metadata.get(index);
		local.removeAnno(rule,value);
		this.metadata.set(index, local);
		this.version(local);
	}
	
	/**
	 * Reference a metadata object
	 * @param uri The URI of the metadata
	 * @param rule The name of the reference rule
	 * @param uriTo The URI value that this reference points too
	 */
	public void reference(String uri, String rule, String uriTo){
		int index = this.add(uri);
		@SuppressWarnings("unused")
		int refIndex = this.add(uriTo);
		Metadata local = this.metadata.get(index);
		local.reference(rule, uriTo);
		this.metadata.set(index, local);
		this.version(local);
	}
	
	/**
	 * Remove an reference on a metadata object
	 * @param uri The URI of the metadata
	 * @param rule The name of the reference rule
	 * @param uriTo The URI value that this reference points too
	 */
	public void deReference(String uri, String rule, String uriTo){
		int index = this.add(uri);
		Metadata local = this.metadata.get(index);
		local.removeRef(rule, uriTo);
		this.metadata.set(index, local);
		this.version(local);
	}
	
	/**
	 * Get the vector index of a URI by name
	 * @param uri The URI to look for
	 * @return Integer index of the URI in the metadata
	 */
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
	
	/**
	 * Version a metadata object
	 * @param metadata
	 */
	private void version(Metadata metadata){
		this.versions.add(metadata);
	}

}
