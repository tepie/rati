/*
 * MetadataQNames.java
 *
 * Created on December 2, 2007, 9:09 PM
 *
 * To change this template, choose Tools | Template Manager
 * and open the template in the editor.
 */

package org.globus.examples.services.core.metadata.impl;

import javax.xml.namespace.QName;

/**
 *
 * @author PIETRTI
 */
public interface MetadataQNames {
    public static final String NS = "http://www.globus.org/" +
            "namespaces/examples/core/" +
            "MetadataService";
			
	//public static final QName RP_METADATA = new QName(NS, "Metadata");
	//public static final QName RP_VERSIONS = new QName(NS, "Versions");
    
    public static final QName RESOURCE_PROPERTIES = new QName(NS,
			"MetadataResourceProperties");
    
}
