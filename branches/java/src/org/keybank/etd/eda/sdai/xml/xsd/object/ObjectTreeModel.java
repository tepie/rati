package org.keybank.etd.eda.sdai.xml.xsd.object;

import javax.swing.tree.DefaultTreeModel;

@SuppressWarnings("serial")
public class ObjectTreeModel extends DefaultTreeModel {
    /**
     * A simple constructor. Is made private to allow creating the root node
     * first.
     *
     * @param root The root node.
     */
    private ObjectTreeModel(ObjectNode root) {
        super(root);
    }

    /**
     * A factory method for creating a new empty tree.
     *
     * @return New empty tree model.
     */
    public static ObjectTreeModel getInstance() {
        ObjectRoot root = new ObjectRoot();
        return new ObjectTreeModel(root);
    }
    
    /*@SuppressWarnings("unchecked")
	public ObjectNode findNodeByPrimaryAnnotation(String primaryAnnotation){
    	
    	ObjectTreeModel local = ObjectTreeModel.getInstance();
    	ObjectNode roots = (ObjectNode) local.getRoot();
    	Enumeration<ObjectNode> e = (Enumeration<ObjectNode>)roots.depthFirstEnumeration();
    	
    	while (e.hasMoreElements()) {
			ObjectNode elem = (ObjectNode) e.nextElement();
			System.err.println(elem.getPrimaryAnnotation() + " vs. " +primaryAnnotation);
			if(elem.getPrimaryAnnotation() == primaryAnnotation){
				System.err.println(primaryAnnotation + " has matched!");
				return elem;
			}
			
		} 
    	
    	System.err.println(primaryAnnotation + " never matched!");
    	return null;
    }*/
    

    public void addObjectNode(ObjectNode node) {
        ((ObjectNode) this.root).add(node);
    }
    
    private ObjectTreeModel(ObjectRoot root) {
        super(root);
    }
    
    

}
