package org.keybank.etd.eda.sdai.xml.xsd.object;

import java.lang.reflect.Field;
import java.util.HashMap;

/**
 * Interface to describe some of the schema components.
 * Since the XSOM API does not provide labels and details, 
 * we can put some references here for a good old time.
 * 
 * It might be smart to one day move this to a properties
 * file, but I don't have time for that right now.
 * 
 * @author Terrence Pietrondi
 * @link http://www.w3.org/TR/xmlschema-2/
 * @link http://www.w3.org/TR/xmlschema-1/
 */
public class SchemaComponents extends Object{
	
	/**
	 * The top most schema component label
	 */
	public static final String COMPONENT				= "Schema Component";
	public static final String CATEGORY					= "Category";
	public static final String TARGET_NAMESPACE			= "Target Namespace";
	
	/**
	 * The attribute declaration label
	 */
	public static final String ATTRIBUTE_DECL			= "Attribute Declaration";
	public static final String ELEMENT_DECL				= "Element Declaration";
	public static final String COMPLEXT_TYPE_DEF 		= "Complex Type Definition";
	public static final String ATTRIBUTE_USES 			= "Attribute Uses";
	public static final String ATTRIBUTE_GROUP_DEF 		= "Attribute Group Definition";
	public static final String MODEL_GROUP_DEF 			= "Model Group Definition";
	public static final String MODEL_GROUP 				= "Attribute Group Definition";
	public static final String PARTICLE 				= "Particle";
	public static final String WILDCARD 				= "Wildcard";
	public static final String IDENTITY_CONSTR_DEF 		= "Identity-constraint Definition";
	public static final String NOTATION_DECL 			= "Notation Declaration";
	public static final String ANNOTATION	 			= "Annotation";
	public static final String SIMPLE_TYPE_DEF	 		= "Simple Type Definition";
	public static final String SIMPLE_LIST_TYPE	 		= "List Simple Type Definition";
	public static final String SIMPLE_UNION_TYPE	 	= "Union Simple Type Definition";
	public static final String SCHEMA	 				= "Schema";
	
	public static final String BASE_COMPONENT		 	= SCHEMA;
	
	/**
	 * The facet type label
	 */
	public static final String FACET	 				= "Facet Type";
	
	/**
	 * Null constructor.
	 * This doesn't do a damn thing.
	 */
	public SchemaComponents(){
		
	}
	
	/**
	 * Get the base component
	 * @return String label for the base component. The base component
	 * is the top most component in an schema document, or the schema.
	 */
	public String getBaseComponent(){
		return SchemaComponents.BASE_COMPONENT;
	}
	
	/**
	 * Get a field map of the components of this object
	 * @return HashMap of the field names as keys, and the values of the fields in this object
	 */
	public HashMap<String,String> getComponetFieldMap(){
		// create the return collection
		HashMap<String,String> map 	= new HashMap<String,String>();
		// get the class object for this
		Class<?> classObj 			= this.getClass();
		// extract the fields from the class object
		Field[] fields 				= classObj.getDeclaredFields();
		
		// for each field, fill the return
		// container with field name as the key,
		// and the value of the field as the value
		for(Field f : fields){
			try {
				String value = (String) f.get(classObj);
				map.put(f.getName(),value);
			} catch (IllegalArgumentException e) {
				e.printStackTrace();
			} catch (IllegalAccessException e) {
				e.printStackTrace();
			}
		}
		
		return map;
	}
	
	
}
