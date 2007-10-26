package org.keybank.etd.eda.sdai.xml.xsd.walk;

import java.io.PrintStream;
import java.util.Enumeration;
import java.util.HashMap;

import javax.swing.tree.DefaultMutableTreeNode;

import org.keybank.etd.eda.sdai.xml.xsd.object.ObjectNode;
import org.keybank.etd.eda.sdai.xml.xsd.object.ObjectTreeModel;


public class ObjectModelWriter implements Walker {

	private PrintStream out;
	private ObjectTreeModel model;
	private Object root;
	
	protected static final String primaryObjectId 	= "oid";
	protected static final String referenceRule 	= "oidref";
	protected static final String categoryRule 		= "Category";
	protected static final String categoryDirectory = "Categories";
	
	private static final String xmlDef 		= "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
	private static final String docStart 	= "<objectxml><datastore>";
	private static final String docStop 	= "</datastore></objectxml>";
	
	public ObjectModelWriter(ObjectTreeModel model){
		this.model 	= model;
		this.out 	= new PrintStream(System.out);		
	}
	
	public ObjectModelWriter(ObjectTreeModel model,PrintStream out){
		this.model 	= model;
		this.out 	= out;
	}
	
	@SuppressWarnings("unchecked")
	public void walk() {
		this.root 							= (Object) model.getRoot();
		Enumeration<ObjectNode> children 	= (Enumeration<ObjectNode>) ((DefaultMutableTreeNode) this.root).children();
		Enumeration<ObjectNode> 			e = children;
		
		this.out.println(ObjectModelWriter.xmlDef);
		this.out.println(ObjectModelWriter.docStart);
		//this.out.println(this.generateCategories());
		this.out.println(this.formatNodeText((ObjectNode)this.root));
		this.walk(e);
		
		this.out.println(ObjectModelWriter.docStop);
	}
	
	@SuppressWarnings("unchecked")
	private void walk(Enumeration<ObjectNode> e){
		while(e.hasMoreElements()){
			ObjectNode node = e.nextElement();
			
			this.out.println(this.formatNodeText(node));
        	this.walk(node.children());
        }
	}
	
	/*private String generateCategories(){
		SchemaComponents components 	= new SchemaComponents();
		HashMap<String,String> fields 	= components.getComponetFieldMap();
		String categories 				= "<!-- Categories Start -->\n";
		ObjectNode localRoot			= (ObjectNode) this.root;
		
		String baseCategoryPath		= this.formCategoryPath(
			localRoot.toString(),ObjectModelWriter.categoryRule
		);
		
		categories = this.openNode(this.formCategoryPath(root.toString(),
				ObjectModelWriter.categoryRule),ObjectModelWriter.categoryRule);
		
		for(String f : fields.keySet()){
			String innerCategory 	= "";
			String categoryPath		= this.formCategoryPath(localRoot.toString(),
				fields.get(f)
			);
			
			innerCategory 			= this.openNode(categoryPath,ObjectModelWriter.categoryRule);
			
			innerCategory = innerCategory + 
				this.declareAnnotation("Name", fields.get(f)
			);
			
			innerCategory = innerCategory + 
				this.declareReference(ObjectModelWriter.categoryRule,
						baseCategoryPath
			);
			
			innerCategory = innerCategory + this.closeNode() + "\n";
			
			categories = categories + innerCategory;
		}
		categories = categories + "<!-- Categories Stop -->";
		return categories;
		
	}*/
	
	/*private String formCategoryPath(String root,String category){
		return root + 
			ObjectNode.generatedPathSep + 
			ObjectModelWriter.categoryDirectory + 
			ObjectNode.generatedPathSep + 
			category;
	}*/
	
	@SuppressWarnings("unchecked")
	private ObjectNode findNodeByPrimaryAnnotation(String primaryAnnotation){
		ObjectNode local = (ObjectNode)this.root;
		Enumeration<ObjectNode> e = (Enumeration<ObjectNode>)local.breadthFirstEnumeration();
		
		while(e.hasMoreElements()){
			ObjectNode elem = (ObjectNode) e.nextElement();
			//System.err.println(elem.getPrimaryAnnotation() + " vs. " +primaryAnnotation);
			if(elem.getPrimaryAnnotation() == primaryAnnotation){
				//System.err.println(primaryAnnotation + " has matched!");
				return elem;
			}
		}
		
		//System.err.println(primaryAnnotation + " never matched!");
		return null;
	}
	
	private String formatNodeText(ObjectNode node){
		String complete = "";
		complete = this.openNode(node.toString(),"Content");
		
		if(!node.isRoot()){
			complete = complete + this.declareReference("Parent",node.getParent().toString());
		} else {
			complete = complete + this.declareReference("Parent",node.toString());
		}
		
		/*if(node.getCategoryLabel() != null){
			ObjectNode localRoot = (ObjectNode) this.root;
			String generatedPath = this.formCategoryPath(localRoot.toString(), node.getCategoryLabel());
			complete = complete + this.declareReference(ObjectModelWriter.categoryRule,generatedPath);
		} else {
			complete = complete + "\t<!-- No category assignment on this node -->\n";
		}*/
		
		complete = complete + this.declareAnnotation("Path", node.toString());
		
		HashMap<String,String> annoMap = node.getAllAnnos();
		
		for(String key: annoMap.keySet()){
			complete = complete + this.declareAnnotation(key,annoMap.get(key));
			ObjectNode possibleLink = this.findNodeByPrimaryAnnotation(annoMap.get(key));
			if(possibleLink != null && !(possibleLink.toString().equals(node.toString()))){
				complete = complete + this.declareReference(key,possibleLink.toString());
			}
		}
		
		//HashMap<String,ObjectNode> refMap = node.getAllRefs();
		
		/*for(String key: refMap.keySet()){
			ObjectNode value = (ObjectNode)refMap.get(key);
			if(value != null){
				complete = complete + this.declareReference(key,value.toString());
			}
		}*/
		
		complete = complete + this.closeNode();
		return complete;
	}
	
	private String declareReference(String ruleTo, String targetOid){
		return  "\t<reference name='"+ ruleTo +"' "+ ObjectModelWriter.referenceRule + "='" +targetOid +"'/>\n";
	}
	
	private String declareAnnotation(String annoName,String annoContent){
		return "\t<annotation name='"+annoName+"'>"+annoContent+"</annotation>\n";
	}
	
	private String openNode(String oid,String category){
		return "<object category='" + category + "' " + ObjectModelWriter.primaryObjectId + "='" + oid + "'>\n";
	}
	
	private String closeNode(){
		return "</object>";
	}

}
