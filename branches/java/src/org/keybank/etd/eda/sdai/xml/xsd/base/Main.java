/**
 * 
 */
package org.keybank.etd.eda.sdai.xml.xsd.base;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;

import org.keybank.etd.eda.sdai.xml.xsd.object.ObjectSchemaTraverser;
import org.keybank.etd.eda.sdai.xml.xsd.walk.ObjectModelWriter;
import org.xml.sax.SAXException;

import com.sun.xml.xsom.XSSchemaSet;
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

	}
}
