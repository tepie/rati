package org.keybank.etd.eda.sdai.xml.xsd.object;

/**
 * Extension of object node to reflect the root of the tree
 * @author pietrti
 *
 */
public class ObjectRoot extends ObjectNode {

	private static final long serialVersionUID = 1L;

	/**
	 * Construct root
	 */
	public ObjectRoot() {
        super(SchemaComponents.SCHEMA, null);
    }

}
