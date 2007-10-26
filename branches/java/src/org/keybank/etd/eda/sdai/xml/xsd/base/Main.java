/**
 * 
 */
package org.keybank.etd.eda.sdai.xml.xsd.base;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.util.Iterator;

import org.keybank.etd.eda.sdai.xml.xsd.object.ObjectSchemaTraverser;
import org.keybank.etd.eda.sdai.xml.xsd.walk.ObjectModelWriter;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.w3c.dom.Text;
import org.xml.sax.SAXException;

import com.sun.xml.xsom.XSSchemaSet;
import com.sun.xml.xsom.XSType;
import com.sun.xml.xsom.parser.AnnotationParserFactory;
import com.sun.xml.xsom.parser.XSOMParser;
import com.sun.xml.xsom.util.DomAnnotationParserFactory;

/**
 * @author pietrti
 *
 */
public class Main {
	
	private static String myFileName = "U:\\EDA\\Metadata\\Exeros\\ExerosAbstractMapping_superModel.xsd";
	
	/**
	 * @param args
	 * @throws SAXException 
	 */
	@SuppressWarnings("unchecked")
	public static void main(String[] args) throws SAXException {
		
		myFileName = args[0];
		
		File myFileObject = new File(myFileName);
		XSOMParser parser = new XSOMParser(); 
		
		parser.setErrorHandler(new ErrorReporter(System.out));
		parser.setAnnotationParser(new DomAnnotationParserFactory());
		
		try {
			parser.parse(myFileObject);	

		} catch (FileNotFoundException e){
			System.err.println("main: FileNotFoundException");
		    System.err.println(e.getMessage());
		    System.exit(1);		
		} catch (IOException e) {
			System.err.println("main: IOException");
		    System.err.println(e.getMessage());
		    System.exit(1);	
		}
		
		
		XSSchemaSet sset 			= parser.getResult();
	
		ObjectSchemaTraverser stt 	= new ObjectSchemaTraverser();
		stt.visit(sset);
		ObjectModelWriter writer = new ObjectModelWriter(stt.getModel());
		writer.walk();
		
		
		/*Iterator<XSType> itr = sset.iterateTypes(); 
		while(itr.hasNext()){
			Object annotation 		= itr.next().getAnnotation();
			StringBuffer buffer 	= new StringBuffer();
			
			createAnnotation((Element) annotation, buffer);
			
			String rawAnnotation 	= buffer.toString();
			rawAnnotation 			= rawAnnotation.trim();
			System.out.println(rawAnnotation);
		}*/

	}
	
	/*private static void createAnnotation(Node element, StringBuffer strBuffer) {
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

}
