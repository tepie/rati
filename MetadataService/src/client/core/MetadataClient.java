package client.core;

import service.core.MetadataService;

/**
 * A Metadata service client
 * @author pietrti
 *
 */
public class MetadataClient {

	/**
	 * The client's service for metadata
	 */
	private static MetadataService service;
	
	/**
	 * The main execution method of this client
	 * @param args Command line arguments
	 */
	public static void main(String[] args) {
		init();
	}
	
	/**
	 * Initalize this client
	 */
	private static void init(){
		service = new MetadataService();
		
		System.out.println("=====================================");
		
		service.add("Terry");
		System.out.println(service);
		System.out.println("=====================================");
		service.add("Dan Mazur");
		System.out.println(service);
		System.out.println("=====================================");
		
		//System.out.println("Versions of \"Terry\": " + service.versions("Terry"));
		//System.out.println("Versions of \"Dan Mazur\": " + service.versions("Dan Mazur"));
		
		service.annotate("Terry P", "Title", "Lead Appl Sys Programmer");
		service.annotate("Terry P", "Is", "Terry");
		service.reference("Terry P", "Is", "Terry");
		service.reference("Terry", "Is", "Terry P");
		service.reference("Terry", "self", "Terry");
		service.annotate("Dan Mazur", "Title", "Enterprise Architect");
		System.out.println(service);
		System.out.println("=====================================");
		//System.out.println("Versions of \"Terry\": " + service.versions("Terry"));
		//System.out.println("Versions of \"Dan Mazur\": " + service.versions("Dan Mazur"));
		
		service.annotate("Terry", "Office Phone", "216-689-9139");
		service.annotate("Dan Mazur", "Office Phone", "216-689-9376");
		System.out.println(service);
		System.out.println("=====================================");
		//System.out.println("Versions of \"Terry\": " + service.versions("Terry"));
		//System.out.println("Versions of \"Dan Mazur\": " + service.versions("Dan Mazur"));
		
		service.reference("Terry", "Manager", "Dan Mazur");
		System.out.println(service);
		System.out.println("=====================================");
		service.reference("Terry", "Manager", "Dan Mazur");
		System.out.println(service);
		System.out.println("=====================================");
		
		service.reference("Dan Mazur", "Team Member", "Terry");
		System.out.println(service);
		System.out.println("=====================================");
		service.deAnnotate("Terry", "Office Phone", "216-689-9139");
		System.out.println(service);
		System.out.println("=====================================");
		service.deAnnotate("Dan Mazur", "Office Phone", "216-689-9376");
		System.out.println(service);
		System.out.println("=====================================");
		service.deReference("Terry", "Manager", "Dan Mazur");
		System.out.println(service);
		System.out.println("=====================================");
		service.deReference("Dan Mazur", "Team Member", "Terry");
		System.out.println(service);
		System.out.println("=====================================");
		System.out.println("Versions of \"Terry\": " + service.versions("Terry"));
		System.out.println("Versions of \"Dan Mazur\": " + service.versions("Dan Mazur"));
		System.out.println("=====================================");
	}

}
