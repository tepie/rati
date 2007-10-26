/*
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS HEADER.
 * 
 * Copyright 1997-2007 Sun Microsystems, Inc. All rights reserved.
 * 
 * The contents of this file are subject to the terms of either the GNU
 * General Public License Version 2 only ("GPL") or the Common Development
 * and Distribution License("CDDL") (collectively, the "License").  You
 * may not use this file except in compliance with the License. You can obtain
 * a copy of the License at https://glassfish.dev.java.net/public/CDDL+GPL.html
 * or glassfish/bootstrap/legal/LICENSE.txt.  See the License for the specific
 * language governing permissions and limitations under the License.
 * 
 * When distributing the software, include this License Header Notice in each
 * file and include the License file at glassfish/bootstrap/legal/LICENSE.txt.
 * Sun designates this particular file as subject to the "Classpath" exception
 * as provided by Sun in the GPL Version 2 section of the License file that
 * accompanied this code.  If applicable, add the following below the License
 * Header, with the fields enclosed by brackets [] replaced by your own
 * identifying information: "Portions Copyrighted [year]
 * [name of copyright owner]"
 * 
 * Contributor(s):
 * 
 * If you wish your version of this file to be governed by only the CDDL or
 * only the GPL Version 2, indicate your decision by adding "[Contributor]
 * elects to include this software in this distribution under the [CDDL or GPL
 * Version 2] license."  If you don't indicate a single choice of license, a
 * recipient has the option to distribute your version of this file under
 * either the CDDL, the GPL Version 2 or to extend the choice of license to
 * its licensees as provided above.  However, if you add GPL Version 2 code
 * and therefore, elected the GPL Version 2 license, then the option applies
 * only if the new code is made subject to such option by the copyright
 * holder.
 */

package org.keybank.etd.eda.sdai.xml.xsd.object;

import java.text.MessageFormat;
import java.util.HashMap;
import java.util.Iterator;

import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.w3c.dom.Text;

import com.sun.xml.xsom.XSAnnotation;
import com.sun.xml.xsom.XSAttGroupDecl;
import com.sun.xml.xsom.XSAttributeDecl;
import com.sun.xml.xsom.XSAttributeUse;
import com.sun.xml.xsom.XSComplexType;
import com.sun.xml.xsom.XSContentType;
import com.sun.xml.xsom.XSElementDecl;
import com.sun.xml.xsom.XSFacet;
import com.sun.xml.xsom.XSIdentityConstraint;
import com.sun.xml.xsom.XSListSimpleType;
import com.sun.xml.xsom.XSModelGroup;
import com.sun.xml.xsom.XSModelGroupDecl;
import com.sun.xml.xsom.XSNotation;
import com.sun.xml.xsom.XSParticle;
import com.sun.xml.xsom.XSRestrictionSimpleType;
import com.sun.xml.xsom.XSSchema;
import com.sun.xml.xsom.XSSchemaSet;
import com.sun.xml.xsom.XSSimpleType;
import com.sun.xml.xsom.XSType;
import com.sun.xml.xsom.XSUnionSimpleType;
import com.sun.xml.xsom.XSWildcard;
import com.sun.xml.xsom.XSXPath;
import com.sun.xml.xsom.impl.Const;
import com.sun.xml.xsom.visitor.XSSimpleTypeVisitor;
import com.sun.xml.xsom.visitor.XSTermVisitor;
import com.sun.xml.xsom.visitor.XSVisitor;

/**
 * Generates approximated tree model for XML from a schema component. This is
 * not intended to be a fully-fledged round-trippable tree model.
 * 
 * <h2>Usage of this class</h2>
 * 
 * <ol>
 * <li>Create a new instance.</li>
 * <li>Call {@link #visit(com.sun.xml.xsom.XSSchemaSet)} function on your
 * schema set.>/li>
 * <li>Retrieve the model using {@link #getModel()}. </li>
 * </ol>
 * 
 * Every node in the resulting tree is a {@link ObjectSchemaTraverser.ObjectNode},
 * and the model itself is {@link ObjectSchemaTraverser.SchemaTreeModel}. You can
 * use {@link ObjectSchemaTraverser.SchemaTreeCellRenderer} as a cell renderer for
 * your tree.
 * 
 * @author Kirill Grouchnikov (kirillcool@yahoo.com)
 */
public class ObjectSchemaTraverser implements XSVisitor, XSSimpleTypeVisitor {
	
	/**
	 * The associated tree model.
	 */
	private ObjectTreeModel model;

	/**
	 * The current node in the tree.
	 */
	private ObjectNode currNode;

	/**
	 * Simple constructor.
	 */
	public ObjectSchemaTraverser() {
		this.model 		= ObjectTreeModel.getInstance();
		this.currNode 	= (ObjectNode) this.model.getRoot();
	}

	/**
	 * Retrieves the tree model of <code>this</code> traverser.
	 * 
	 * @return Tree model of <code>this</code> traverser.
	 */
	public ObjectTreeModel getModel() {
		return model;
	}

	/**
	 * Visits the root schema set.
	 * 
	 * @param s
	 *            Root schema set.
	 */
	public void visit(XSSchemaSet s) {
		for (XSSchema schema : s.getSchemas()) {
			schema(schema);
		}
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSVisitor#schema(com.sun.xml.xsom.XSSchema)
	 */
	public void schema(XSSchema s) {
		// QUICK HACK: don't print the built-in components
		if (s.getTargetNamespace().equals(Const.schemaNamespace)) {
			return;
		}

		ObjectNode newNode = new ObjectNode(s.getLocator().getSystemId(), 
			s.getLocator()
		);
		
		this.currNode = newNode;

		this.model.addObjectNode(newNode);

		for (XSComplexType complexType : s.getComplexTypes().values()) {
			complexType(complexType);
		}
		
		for (XSModelGroupDecl modelGroupDecl : s.getModelGroupDecls().values()) {
			modelGroupDecl(modelGroupDecl);
		}

		for (XSSimpleType simpleType : s.getSimpleTypes().values()) {
			simpleType(simpleType);
		}
		
		for (XSAttGroupDecl groupDecl : s.getAttGroupDecls().values()) {
			attGroupDecl(groupDecl);
		}

		for (XSAttributeDecl attrDecl : s.getAttributeDecls().values()) {
			attributeDecl(attrDecl);
		}
		

		for (XSElementDecl elementDecl : s.getElementDecls().values()) {
			elementDecl(elementDecl);
		}
		
		//for(XSAnnotation annotationType : s.get)
	}
	
	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSVisitor#attGroupDecl(com.sun.xml.xsom.XSAttGroupDecl)
	 */
	public void attGroupDecl(XSAttGroupDecl decl) {

		ObjectNode newNode = new ObjectNode(decl.getName(), decl.getLocator());

		this.currNode.add(newNode);
		this.currNode = newNode;
		this.annotation(decl.getAnnotation(true));
		
		Iterator<? extends XSAttGroupDecl> itrXSAttGroupDecl;

		itrXSAttGroupDecl = decl.iterateAttGroups();
		while (itrXSAttGroupDecl.hasNext()) {
			dumpRef((XSAttGroupDecl) itrXSAttGroupDecl.next());
		}

		Iterator<? extends XSAttributeUse> itrXSAttributeUse;

		itrXSAttributeUse = decl.iterateDeclaredAttributeUses();
		while (itrXSAttributeUse.hasNext()) {
			attributeUse((XSAttributeUse) itrXSAttributeUse.next());
		}

		this.currNode = (ObjectNode) this.currNode.getParent();
	}

	/**
	 * Creates node of attribute group declaration reference.
	 * 
	 * @param decl
	 *            Attribute group declaration reference.
	 */
	public void dumpRef(XSAttGroupDecl decl) {
		ObjectNode newNode = new ObjectNode(decl.getName(), decl.getLocator());

		newNode.putAnno(SchemaComponents.COMPONENT, SchemaComponents.ATTRIBUTE_GROUP_DEF);
		//newNode.putRef(SchemaComponents.COMPONENT, SchemaComponents.ATTRIBUTE_GROUP_DEF);
		newNode.putAnno(SchemaComponents.TARGET_NAMESPACE, decl.getTargetNamespace());
		this.currNode.add(newNode);
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSVisitor#attributeUse(com.sun.xml.xsom.XSAttributeUse)
	 */
	public void attributeUse(XSAttributeUse use) {
		XSAttributeDecl decl = use.getDecl();

		// String additionalAtts = "";
		HashMap<String, String> additionalAttsMap = new HashMap<String, String>();

		if (use.isRequired()) {
			additionalAttsMap.put("Required", "True");
		} else {
			additionalAttsMap.put("Required", "False");
		}
		
		if (use.getFixedValue() != null	&& use.getDecl().getFixedValue() == null) {
			additionalAttsMap.put("Fixed Value", use.getFixedValue().toString());
		}
		
		if (use.getDefaultValue() != null && use.getDecl().getDefaultValue() == null) {
			additionalAttsMap.put("Default Value", use.getDefaultValue().toString());
		}

		if (decl.isLocal()) {
			// this is anonymous attribute use
			dump(decl, additionalAttsMap);

		} else {
			// reference to a global one
			ObjectNode newNode = new ObjectNode(decl.getName(), decl
					.getLocator());

			newNode.putAnno(SchemaComponents.COMPONENT, SchemaComponents.ATTRIBUTE_USES);
			//newNode.putRef(SchemaComponents.COMPONENT, SchemaComponents.ATTRIBUTE_USES);
			newNode.putAnno(SchemaComponents.TARGET_NAMESPACE, decl.getTargetNamespace());
			newNode.putAllAnnos(additionalAttsMap);
			this.currNode.add(newNode);
			this.currNode = newNode;
			this.annotation(decl.getAnnotation(true));
			this.currNode = (ObjectNode) this.currNode.getParent();
			
		}
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSVisitor#attributeDecl(com.sun.xml.xsom.XSAttributeDecl)
	 */
	public void attributeDecl(XSAttributeDecl decl) {
		dump(decl, new HashMap<String, String>());
	}

	/**
	 * Creates node for attribute declaration with additional attributes.
	 * 
	 * @param decl
	 *            Attribute declaration.
	 * @param additionalAtts
	 *            Additional attributes.
	 */
	private void dump(XSAttributeDecl decl,	HashMap<String, String> additionalAtts) {
		XSSimpleType type = decl.getType();

		ObjectNode newNode = new ObjectNode(decl.getName(), decl.getLocator());
		newNode.putAnno(SchemaComponents.COMPONENT, SchemaComponents.ATTRIBUTE_DECL);
		//newNode.putRef(SchemaComponents.COMPONENT, SchemaComponents.ATTRIBUTE_DECL);
		newNode.putAnno(SchemaComponents.TARGET_NAMESPACE, type.getTargetNamespace());
		
		//TODO: 
		newNode.putAnno("Type Name", type.getName());
		
		/*ObjectNode elem = this.findNodeByPrimaryAnnotation(type.getName());
		if(elem != null){
			newNode.putRef("Type Name", elem);
		}*/
		
		newNode.putAllAnnos(additionalAtts);

		if (decl.getFixedValue() != null) {
			newNode.putAnno("Fixed Value", decl.getFixedValue().toString());
		}

		if (decl.getDefaultValue() != null) {
			newNode.putAnno("Default Value", decl.getDefaultValue().toString());
		}

		this.currNode.add(newNode);
		this.currNode = newNode;
		this.annotation(type.getAnnotation(true));

		if (type.isLocal()) {
			simpleType(type);
		}
		this.currNode = (ObjectNode) this.currNode.getParent();
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSContentTypeVisitor#simpleType(com.sun.xml.xsom.XSSimpleType)
	 */
	public void simpleType(XSSimpleType type) {

		if (!type.isLocal()) {
			ObjectNode newNode = new ObjectNode(type.getName(), type
					.getLocator());

			newNode.putAnno(SchemaComponents.COMPONENT,SchemaComponents.SIMPLE_TYPE_DEF);
			//newNode.putRef(SchemaComponents.COMPONENT,SchemaComponents.SIMPLE_TYPE_DEF);
			this.currNode.add(newNode);
			this.currNode = newNode;
			this.annotation(type.getAnnotation(true));
		}	

		try {
			type.visit((XSSimpleTypeVisitor) this);
		} catch (NullPointerException e) {
			System.err.println("Caught NullPointer:"
					+ "type.visit((XSSimpleTypeVisitor) this);");
		}

		if (!type.isLocal()) {
			this.currNode = (ObjectNode) this.currNode.getParent();
		}
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSSimpleTypeVisitor#listSimpleType(com.sun.xml.xsom.XSListSimpleType)
	 */
	public void listSimpleType(XSListSimpleType type) {
		XSSimpleType itemType = type.getItemType();

		if (itemType.isLocal()) {
			/*
			 * ObjectNode newNode = new ObjectNode("ListSimpleType",
			 * type.getLocator()); this.currNode.add(newNode); this.currNode =
			 * newNode;
			 */
			simpleType(itemType);

			// this.currNode = (ObjectNode) this.currNode.getParent();
		} else {
			// global type
			ObjectNode newNode = new ObjectNode(itemType.getName(), itemType
					.getLocator());
			newNode.putAnno(SchemaComponents.COMPONENT,SchemaComponents.SIMPLE_LIST_TYPE);
			//newNode.putRef(SchemaComponents.COMPONENT,SchemaComponents.SIMPLE_LIST_TYPE);
			newNode.putAnno(SchemaComponents.TARGET_NAMESPACE, itemType.getTargetNamespace());
			this.currNode.add(newNode);
			this.currNode = (ObjectNode) this.currNode.getParent();
		}
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSSimpleTypeVisitor#unionSimpleType(com.sun.xml.xsom.XSUnionSimpleType)
	 */
	public void unionSimpleType(XSUnionSimpleType type) {
		final int len = type.getMemberSize();
		StringBuffer ref = new StringBuffer();
		
		//TODO: convert to annotations/references
		//HashMap<String,String> refMap = new HashMap<String,String>();
		
		for (int i = 0; i < len; i++) {
			XSSimpleType member = type.getMember(i);
			if (member.isGlobal()) {
				ref.append(MessageFormat.format(" '{'{0}'}'{1}", new Object[] {
						member.getTargetNamespace(), member.getName() }));
				//refMap.p
			}
		}

		/*
		 * String name = (ref.length() == 0) ? "Union" : ("Union memberTypes=\"" +
		 * ref + "\"");
		 */

		if ((ref.length() != 0)) {
			ObjectNode newNode = new ObjectNode("Union memberTypes=\"" + ref
					+ "\"", type.getLocator());
			newNode.putAnno(SchemaComponents.COMPONENT, SchemaComponents.SIMPLE_UNION_TYPE);
			//newNode.putRef(SchemaComponents.COMPONENT, SchemaComponents.SIMPLE_UNION_TYPE);
			this.currNode.add(newNode);
			this.currNode = newNode;
			this.annotation(type.getAnnotation(true));
		}

		for (int i = 0; i < len; i++) {
			XSSimpleType member = type.getMember(i);
			if (member.isLocal()) {
				simpleType(member);
			}
		}

		if ((ref.length() != 0)) {
			this.currNode = (ObjectNode) this.currNode.getParent();
		}
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSSimpleTypeVisitor#restrictionSimpleType(com.sun.xml.xsom.XSRestrictionSimpleType)
	 */
	public void restrictionSimpleType(XSRestrictionSimpleType type) {

		if (type.getBaseType() == null) {
			// don't print anySimpleType
			if (!type.getName().equals("anySimpleType")) {
				throw new InternalError();
			}
			if (!Const.schemaNamespace.equals(type.getTargetNamespace())) {
				throw new InternalError();
			}
			return;
		}

		XSSimpleType baseType = type.getSimpleBaseType();

		/*
		 * String str = MessageFormat.format("Restriction {0}", new
		 * Object[]{baseType.isLocal() ? "" : " org.keybank.etd.eda.sdai.xml.xsd.base=\"{" +
		 * baseType.getTargetNamespace() + "}" + baseType.getName() + "\""});
		 */
		if (!baseType.isLocal()) {
			/*ObjectNode newNode = new ObjectNode(type.getName(), baseType
					.getLocator());
			newNode.put("Component", "RestrictionSimpleType");
			newNode.put("TargetNamespace", baseType.getTargetNamespace());
			this.currNode.add(newNode);
			this.currNode = newNode;
			*/
		} else {
			/*ObjectNode newNode = new ObjectNode("RestrictionSimpleType",
					baseType.getLocator());
			newNode.put("Component", "RestrictionSimpleType");
			// newNode.put("TargetNamespace", baseType.getTargetNamespace());
			this.currNode.add(newNode);
			this.currNode = newNode;
			*/
		}

		if (baseType.isLocal()) {
			simpleType(baseType);
		}

		Iterator<XSFacet> itr = type.iterateDeclaredFacets();
		while (itr.hasNext()) {
			facet((XSFacet) itr.next());
		}

		/*if (!baseType.isLocal()) {
			this.currNode = (ObjectNode) this.currNode.getParent();
		}*/
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSVisitor#facet(com.sun.xml.xsom.XSFacet)
	 */
	public void facet(XSFacet facet) {
		/*
		 * ObjectNode newNode = new ObjectNode(MessageFormat.format( "{0}
		 * value=\"{1}\"", new Object[]{facet.getName(), facet.getValue(), }),
		 * facet.getLocator());
		 */
		ObjectNode newNode = new ObjectNode(facet.getValue().toString(), facet
				.getLocator());
		newNode.putAnno(SchemaComponents.COMPONENT, SchemaComponents.FACET);
		//newNode.putRef(SchemaComponents.COMPONENT, SchemaComponents.SIMPLE_UNION_TYPE);
		newNode.putAnno("Facet Name", facet.getName());
		this.currNode.add(newNode);
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSVisitor#notation(com.sun.xml.xsom.XSNotation)
	 */
	public void notation(XSNotation notation) {
		/*
		 * ObjectNode newNode = new ObjectNode(MessageFormat.format( "Notation
		 * name='\"0}\" public =\"{1}\" system=\"{2}\"", new
		 * Object[]{notation.getName(), notation.getPublicId(),
		 * notation.getSystemId()}), notation.getLocator());
		 */
		ObjectNode newNode = new ObjectNode(notation.getName(), notation
				.getLocator());
		newNode.putAnno(SchemaComponents.COMPONENT, SchemaComponents.NOTATION_DECL);
		//newNode.putRef(SchemaComponents.COMPONENT, SchemaComponents.NOTATION_DECL);
		newNode.putAnno("Public Id", notation.getPublicId());
		newNode.putAnno("System Id", notation.getSystemId());
		this.currNode.add(newNode);
	}
	
	/*private void createAnnotation(Node element, StringBuffer strBuffer){
		if (element instanceof Text) {
		     Text text = (Text) element;
		     strBuffer.append(text.getData());
		}
		
		NodeList children = element.getChildNodes();
		for (int i = 0; i < children.getLength(); i++) {
			Node child = children.item(i);
			createAnnotation(child, strBuffer);
		}
	}*/

	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSVisitor#complexType(com.sun.xml.xsom.XSComplexType)
	 */
	public void complexType(XSComplexType type) {
		
		/*if (!(annotationObject instanceof org.w3c.dom.Element)){
			System.err.println("WARNING: Annotation should be org.w3c.dom.Element");
			//throw new IllegalStateException("Annotation should be org.w3c.dom.Element");
		}*/
		
		if (!type.isLocal()) {
			/*Object annotationObject 	= type.getAnnotation();
			StringBuffer buffer 		= new StringBuffer();
			String rawAnnotation		= null;
			
			try{
				this.createAnnotation((Node) annotationObject, buffer);
				rawAnnotation 	= buffer.toString();
				rawAnnotation 	= rawAnnotation.trim();
			} catch (ClassCastException e){
				System.err.println("complexType:ClassCastException: " + type.getName());
				//e.printStackTrace();
			}
			*/
			
			
			ObjectNode newNode = new ObjectNode(type.getName(), type
					.getLocator());
			
			newNode.putAnno(SchemaComponents.COMPONENT, SchemaComponents.COMPLEXT_TYPE_DEF);
			
			/*if(rawAnnotation != null){
				newNode.putAnno(SchemaComponents.ANNOTATION, rawAnnotation);	
			}*/
			
			this.currNode.add(newNode);
			this.currNode = newNode;
			this.annotation(type.getAnnotation(true));
		} else {
			/*ObjectNode newNode = new ObjectNode("ComplexType", type
					.getLocator());
			newNode.put("Component", "ComplexType");
			this.currNode.add(newNode);
			this.currNode = newNode;
			*/
		}

		// TODO: wildcard

		if (type.getContentType().asSimpleType() != null) {
			// simple content
			/*ObjectNode newNode2 = new ObjectNode(type.getName(), type
					.getContentType().getLocator());
			newNode2.put("Component", "ComplexType");
			this.currNode.add(newNode2);
			this.currNode = newNode2;
			*/
			XSType baseType = type.getBaseType();

			if (type.getDerivationMethod() == XSType.RESTRICTION) {
				// restriction
				/*
				 * String str = MessageFormat.format( "Restriction org.keybank.etd.eda.sdai.xml.xsd.base=\"<{0}>{1}\"",
				 * new Object[]{ baseType.getTargetNamespace(),
				 * baseType.getName()});
				 */
				/*ObjectNode newNode3 = new ObjectNode(baseType.getName(),
						baseType.getLocator());
				newNode3.put("Component", "ComplexTypeRestriction");
				newNode3.put("TargetNamespace", baseType.getTargetNamespace());
				//newNode3.put("BaseType", baseType.getName());
				this.currNode.add(newNode3);
				this.currNode = newNode3;
				*/
				dumpComplexTypeAttribute(type);

				//this.currNode = (ObjectNode) this.currNode.getParent();
			} else {
				// extension
				/*
				 * String str = MessageFormat.format( "Extension org.keybank.etd.eda.sdai.xml.xsd.base=\"<{0}>{1}\"",
				 * new Object[]{ baseType.getTargetNamespace(),
				 * baseType.getName()});
				 */

				/*ObjectNode newNode3 = new ObjectNode(baseType.getName(),
						baseType.getLocator());
				newNode3.put("Component", "ComplexTypeExtension");
				newNode3.put("TargetNamespace", baseType.getTargetNamespace());
				newNode3.put("BaseType", baseType.getName());
				this.currNode.add(newNode3);
				this.currNode = newNode3;
				*/
				// check if have redefine tag
				if ((type.getTargetNamespace().compareTo(
						baseType.getTargetNamespace()) == 0)
						&& (type.getName().compareTo(baseType.getName()) == 0)) {
					/*ObjectNode newNodeRedefine = new ObjectNode("Redefine",
							type.getLocator());
					newNode3.put("Component", "ComplexType");
					this.currNode.add(newNodeRedefine);
					this.currNode = newNodeRedefine;
					*/
					baseType.visit(this);
					//this.currNode = (ObjectNode) newNodeRedefine.getParent();
				}
				
				dumpComplexTypeAttribute(type);

				//this.currNode = (ObjectNode) this.currNode.getParent();
			}

			//this.currNode = (ObjectNode) this.currNode.getParent();
		} else {
			// complex content
			/*ObjectNode newNode2 = new ObjectNode(type.getName(), type
					.getContentType().getLocator());
			newNode2.put("Component", "ComplexTypeContent");
			this.currNode.add(newNode2);
			this.currNode = newNode2;
			*/
			XSComplexType baseType = type.getBaseType().asComplexType();

			if (type.getDerivationMethod() == XSType.RESTRICTION) {
				// restriction
				/*
				 * String str = MessageFormat.format( "Restriction org.keybank.etd.eda.sdai.xml.xsd.base=\"<{0}>{1}\"",
				 * new Object[]{ baseType.getTargetNamespace(),
				 * baseType.getName()});
				 */
				/*ObjectNode newNode3 = new ObjectNode(baseType.getName(),
						baseType.getLocator());
				newNode3.put("Component", "ComplexTypeRestriction");
				newNode3.put("TargetNamespace", baseType.getTargetNamespace());
				newNode3.put("BaseType", baseType.getName());
				this.currNode.add(newNode3);
				this.currNode = newNode3;
				*/
				try {
					type.getContentType().visit(this);
					dumpComplexTypeAttribute(type);
				} catch (NullPointerException e) {
					System.err.println("Caught NullPointer:"
							+ "type.getContentType().visit(this);");
				}

				//this.currNode = (ObjectNode) this.currNode.getParent();
			} else {
				// extension
				/*
				 * String str = MessageFormat.format( "Extension
				 * org.keybank.etd.eda.sdai.xml.xsd.base=\"'{'{0}'}'{1}\"", new Object[]{
				 * baseType.getTargetNamespace(), baseType.getName()});
				 */
				/*ObjectNode newNode3 = new ObjectNode(baseType.getName(),
						baseType.getLocator());
				newNode3.put("Component", "ComplexTypeExtension");
				newNode3.put("TargetNamespace", baseType.getTargetNamespace());
				newNode3.put("BaseType", baseType.getName());
				this.currNode.add(newNode3);
				this.currNode = newNode3;
				*/
				// check if have redefine tag
				if ((type.getTargetNamespace().compareTo(
						baseType.getTargetNamespace()) == 0)
						&& (type.getName().compareTo(baseType.getName()) == 0)) {
					/*ObjectNode newNodeRedefine = new ObjectNode("Redefine",
							type.getLocator());
					this.currNode.add(newNodeRedefine);
					this.currNode = newNodeRedefine;
					
					*/
					baseType.visit(this);
					//this.currNode = (ObjectNode) newNodeRedefine.getParent();
				}

				try {
					type.getExplicitContent().visit(this);
					dumpComplexTypeAttribute(type);

				} catch (NullPointerException e) {
					System.err.println("Caught NullPointer:"
							+ "dumpComplexTypeAttribute(type);");
				}

				//this.currNode = (ObjectNode) this.currNode.getParent();
			}

			//this.currNode = (ObjectNode) this.currNode.getParent();
		}

		if(!type.isLocal()){
			this.currNode = (ObjectNode) this.currNode.getParent();
		}
	}

	/**
	 * Creates node for complex type.
	 * 
	 * @param type
	 *            Complex type.
	 */
	private void dumpComplexTypeAttribute(XSComplexType type) {
		Iterator<? extends XSAttGroupDecl> itr1;

		itr1 = type.iterateAttGroups();
		while (itr1.hasNext()) {
			dumpRef((XSAttGroupDecl) itr1.next());
		}

		Iterator<? extends XSAttributeUse> itr2;
		itr2 = type.iterateDeclaredAttributeUses();
		while (itr2.hasNext()) {
			attributeUse((XSAttributeUse) itr2.next());
		}
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSTermVisitor#elementDecl(com.sun.xml.xsom.XSElementDecl)
	 */
	public void elementDecl(XSElementDecl decl) {
		elementDecl(decl, new HashMap<String, String>());
	}

	private void elementDecl(XSElementDecl decl, HashMap<String, String> extraAtts) {
		
		XSType type = decl.getType();

		// TODO: various other attributes

		/*
		 * String str = MessageFormat.format("Element name=\"{0}\"{1}{2}", new
		 * Object[]{ decl.getName(), type.isLocal() ? "" : " type=\"{" +
		 * type.getTargetNamespace() + "}" + type.getName() + "\"", extraAtts});
		 */
		if (!type.isLocal()) {
			
			/*Object annotationObject 	= decl.getAnnotation();
			StringBuffer buffer 		= new StringBuffer();
			String rawAnnotation		= null;
			*/
			/*try{
				this.createAnnotation((Node) annotationObject, buffer);
				rawAnnotation 	= buffer.toString();
				rawAnnotation 	= rawAnnotation.trim();
			} catch (ClassCastException e){
				System.err.println("elementDecl:ClassCastException: " + decl.getName());
				//e.printStackTrace();
			}*/
			
			
			
			ObjectNode newNode = new ObjectNode(decl.getName(), decl
					.getLocator());
			newNode.putAnno(SchemaComponents.COMPONENT, SchemaComponents.ELEMENT_DECL);
			//newNode.putRef(SchemaComponents.COMPONENT, SchemaComponents.ELEMENT_DECL);
			newNode.putAnno(SchemaComponents.TARGET_NAMESPACE, type.getTargetNamespace());
			newNode.putAnno("Type Name", type.getName());
			
			/*if(rawAnnotation != null){
				newNode.putAnno(SchemaComponents.ANNOTATION, rawAnnotation);	
			}*/
			
			
			/*ObjectNode elem = this.findNodeByPrimaryAnnotation(type.getName());
			if(elem != null){
				newNode.putRef("Type Name", elem);
			}*/
			
			newNode.putAllAnnos(extraAtts);
			this.currNode.add(newNode);
			this.currNode = newNode;
			this.annotation(type.getAnnotation(true));
		} else {
			/*ObjectNode newNode = new ObjectNode(decl.getName(), decl
					.getLocator());
			newNode.put("Component", "ElementDecl");
			// newNode.put("TargetNamespace", type.getTargetNamespace());
			// newNode.put("TypeName", type.getName());
			newNode.putAll(extraAtts);
			this.currNode.add(newNode);
			this.currNode = newNode;
			*/
		}

		if (type.isLocal()) {
			if (type.isLocal()) {
				try {
					type.visit(this);
				} catch (NullPointerException e) {
					System.err.println("Caught NullPointer:" + "type.visit(this);");
				}
	
			}
		}

		if(!type.isLocal()){
			this.currNode = (ObjectNode) this.currNode.getParent();
		}
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSTermVisitor#modelGroupDecl(com.sun.xml.xsom.XSModelGroupDecl)
	 */
	public void modelGroupDecl(XSModelGroupDecl decl) {
		/*
		 * ObjectNode newNode = new ObjectNode(MessageFormat.format( "Group
		 * name=\"{0}\"", new Object[]{decl.getName()}), decl.getLocator());
		 */
		ObjectNode newNode = new ObjectNode(decl.getName(), decl.getLocator());
		newNode.putAnno(SchemaComponents.COMPONENT, SchemaComponents.MODEL_GROUP_DEF);
		//newNode.putRef(SchemaComponents.COMPONENT, SchemaComponents.MODEL_GROUP_DEF);
		this.currNode.add(newNode);
		this.currNode = newNode;
		this.annotation(decl.getAnnotation(true));
		modelGroup(decl.getModelGroup());

		this.currNode = (ObjectNode) this.currNode.getParent();
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSTermVisitor#modelGroup(com.sun.xml.xsom.XSModelGroup)
	 */
	public void modelGroup(XSModelGroup group) {
		modelGroup(group, new HashMap<String, String>());
	}

	/**
	 * Creates node for model group with additional attributes.
	 * 
	 * @param group
	 *            Model group.
	 * @param extraAtts
	 *            Additional attributes.
	 */
	private void modelGroup(XSModelGroup group,
			HashMap<String, String> extraAtts) {
		/*
		 * ObjectNode newNode = new ObjectNode(MessageFormat.format( "{0}{1}",
		 * new Object[]{group.getCompositor(), extraAtts}), group.getLocator());
		 */
		/*ObjectNode newNode = new ObjectNode(group.getCompositor().toString(),
				group.getLocator());
		newNode.put("Component", "ModelGroup");
		newNode.putAll(extraAtts);
		this.currNode.add(newNode);
		this.currNode = newNode;
		*/
		final int len = group.getSize();
		for (int i = 0; i < len; i++) {
			particle(group.getChild(i));
		}

		//this.currNode = (ObjectNode) this.currNode.getParent();
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSContentTypeVisitor#particle(com.sun.xml.xsom.XSParticle)
	 */
	public void particle(XSParticle part) {
		int i;

		// StringBuffer buf = new StringBuffer();
		HashMap<String, String> bufAnnos = new HashMap<String, String>();
		i = part.getMaxOccurs();
		if (i == XSParticle.UNBOUNDED) {
			// buf.append(" maxOccurs=\"unbounded\"");
			bufAnnos.put("Max Occurs", "Unbounded");
		} else {
			if (i != 1) {
				// buf.append(" maxOccurs=\"" + i + "\"");
				bufAnnos.put("Max Occurs", "" + i + "");
			}
		}

		i = part.getMinOccurs();
		if (i != 1) {
			// buf.append(" minOccurs=\"" + i + "\"");
			bufAnnos.put("Min Occurs", "" + i + "");
		}

		// final String extraAtts = buf.toString();
		final HashMap<String, String> extraAttsMap = bufAnnos;

		part.getTerm().visit(new XSTermVisitor() {
			public void elementDecl(XSElementDecl decl) {
				if (decl.isLocal()) {
					ObjectSchemaTraverser.this.elementDecl(decl, extraAttsMap);
				} else {
					// reference
					/*
					 * ObjectNode newNode = new ObjectNode(MessageFormat
					 * .format("Element ref=\"'{'{0}'}'{1}\"{2}", new
					 * Object[]{decl.getTargetNamespace(), decl.getName(),
					 * extraAtts}), decl.getLocator());
					 */
					/*ObjectNode newNode = new ObjectNode(decl.getName(), decl
							.getLocator());
					newNode.put("TargetNamespace", decl.getTargetNamespace());
					newNode.put("Component", "ElementDecl");
					newNode.putAll(extraAttsMap);
					currNode.add(newNode);
					*/
				}
			}

			public void modelGroupDecl(XSModelGroupDecl decl) {
				// reference
				/*
				 * ObjectNode newNode = new ObjectNode(MessageFormat
				 * .format("Group ref=\"'{'{0}'}'{1}\"{2}", new Object[]{
				 * decl.getTargetNamespace(), decl.getName(), extraAtts}),
				 * decl.getLocator());
				 */
				ObjectNode newNode = new ObjectNode(decl.getName(), decl
						.getLocator());
				newNode.putAnno(SchemaComponents.COMPONENT, SchemaComponents.MODEL_GROUP_DEF);
				//newNode.putRef(SchemaComponents.COMPONENT, SchemaComponents.MODEL_GROUP_DEF);
				newNode.putAnno(SchemaComponents.TARGET_NAMESPACE, decl.getTargetNamespace());
				
				currNode.add(newNode);
			}

			public void modelGroup(XSModelGroup group) {
				ObjectSchemaTraverser.this.modelGroup(group, extraAttsMap);
			}

			public void wildcard(XSWildcard wc) {
				ObjectSchemaTraverser.this.wildcard(wc, extraAttsMap);
			}
		});
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSTermVisitor#wildcard(com.sun.xml.xsom.XSWildcard)
	 */
	public void wildcard(XSWildcard wc) {
		wildcard(wc, new HashMap<String, String>());
	}

	private void wildcard(XSWildcard wc, HashMap<String, String> annoMap) {
		// TODO
		/*
		 * ObjectNode newNode = new ObjectNode(MessageFormat.format( "Any ", new
		 * Object[]{extraAtts}), wc.getLocator()); currNode.add(newNode);
		 */
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSVisitor#annotation(com.sun.xml.xsom.XSAnnotation)
	 */
	public void annotation(XSAnnotation ann) {
		// TODO: it would be nice even if we just put <xs:documentation>
		Object annotation 		= ann.getAnnotation();
		StringBuffer buffer 	= new StringBuffer();
		
		this.createAnnotation((Element) annotation, buffer);
		
		String rawAnnotation 	= buffer.toString();
		rawAnnotation 			= rawAnnotation.trim();
		this.currNode.putAnno(SchemaComponents.ANNOTATION, rawAnnotation);
	}
	
	private void createAnnotation(Node element, StringBuffer strBuffer) {
	    if (element instanceof Text) {
	        Text text = (Text) element;
	        strBuffer.append(text.getData());
	    }
	    NodeList children = element.getChildNodes();
	    for (int i = 0; i < children.getLength(); i++) {
	        Node child = children.item(i);
	        createAnnotation(child, strBuffer);
	    }
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSContentTypeVisitor#empty(com.sun.xml.xsom.XSContentType)
	 */
	public void empty(XSContentType t) {
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSVisitor#identityConstraint(com.sun.xml.xsom.XSIdentityConstraint)
	 */
	public void identityConstraint(XSIdentityConstraint ic) {
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see com.sun.xml.xsom.visitor.XSVisitor#xpath(com.sun.xml.xsom.XSXPath)
	 */
	public void xpath(XSXPath xp) {
	}
}
