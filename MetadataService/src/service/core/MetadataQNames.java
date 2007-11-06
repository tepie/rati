package service.core;

import javax.xml.namespace.QName;

/**
 * Metadata QNames interface
 * @author pietrti
 *
 */
public interface MetadataQNames {
	public static final String NS = "http://localhost/MetadataService_instance";

	//public static final QName RP_VALUE = new QName(NS, "Value");

	//public static final QName RP_LASTOP = new QName(NS, "LastOp");

	public static final QName RESOURCE_PROPERTIES = new QName(NS,"MetadataResourceProperties");
	
}
