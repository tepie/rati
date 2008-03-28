package org.apache.cayenne.auto.rati.auto;

/** Class _Relationship was generated by Cayenne.
  * It is probably a good idea to avoid changing this class manually, 
  * since it may be overwritten next time code is regenerated. 
  * If you need to make any customizations, please use subclass. 
  */
public class _Relationship extends org.apache.cayenne.CayenneDataObject {

    public static final String VALUE_PROPERTY = "value";
    public static final String ATTRIBUTE_RELATIONSHIP_PROPERTY = "attributeRelationship";
    public static final String OBJECT_REFERENCE_PROPERTY = "objectReference";
    public static final String OBJECT_RELATIONSHIP_PROPERTY = "objectRelationship";

    public static final String ID_PK_COLUMN = "ID";

    public void setValue(String value) {
        writeProperty("value", value);
    }
    public String getValue() {
        return (String)readProperty("value");
    }
    
    
    public void setAttributeRelationship(org.apache.cayenne.auto.rati.Attribute attributeRelationship) {
        setToOneTarget("attributeRelationship", attributeRelationship, true);
    }

    public org.apache.cayenne.auto.rati.Attribute getAttributeRelationship() {
        return (org.apache.cayenne.auto.rati.Attribute)readProperty("attributeRelationship");
    } 
    
    
    public void setObjectReference(org.apache.cayenne.auto.rati.Object objectReference) {
        setToOneTarget("objectReference", objectReference, true);
    }

    public org.apache.cayenne.auto.rati.Object getObjectReference() {
        return (org.apache.cayenne.auto.rati.Object)readProperty("objectReference");
    } 
    
    
    public void setObjectRelationship(org.apache.cayenne.auto.rati.Object objectRelationship) {
        setToOneTarget("objectRelationship", objectRelationship, true);
    }

    public org.apache.cayenne.auto.rati.Object getObjectRelationship() {
        return (org.apache.cayenne.auto.rati.Object)readProperty("objectRelationship");
    } 
    
    
}
