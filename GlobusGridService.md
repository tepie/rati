Globus Grid Service Example Implementation

# Introduction #

I have attached a Globus Grid Service example that was developed for some graduate course work. This is provided for an example only to reflect how some of the concepts of this project can be implemented in a Grid environment.


# Details #
You can download the following GAR file and deploy it in your grid container:
  * [Globus GAR Download](http://rati.googlecode.com/files/org_globus_examples_services_core_metadata.gar)

Or you can browse the globus branch in the SVN repository:
  * [Globus SVN Branch](http://rati.googlecode.com/svn/branches/globus/)

# The WSDL #
Check our the service description:
  * [MetadataServiceService.wsdl](http://rati.googlecode.com/svn/branches/globus/schema/examples/MetadataService/MetadataServiceService.wsdl)
  * [MetadataServiceService.xsd](http://rati.googlecode.com/svn/branches/globus/schema/examples/MetadataService/MetadataServiceService.xsd)

# Example Client #
To see how a client would consume this service, check out the following code:
  * [MetadataMainClient.java](http://rati.googlecode.com/svn/branches/globus/org/globus/examples/clients/MetadataService/MetadataMainClient.java)

# Example Image #
Using [JUNG](http://jung.sourceforge.net/) the following is an example image produced by a client of this service.

![http://rati.googlecode.com/svn/branches/globus/metagrid_12590745.png](http://rati.googlecode.com/svn/branches/globus/metagrid_12590745.png)

# Links #
  * Project summary paper: [CIS 698 Grid Computing Project Final Metadata Visualization.pdf](http://cis.csuohio.edu/~tepietro/CIS698/final/CIS%20698%20Grid%20Computing%20Project%20Final%20Metadata%20Visualization.pdf)
  * Project presentation: [CIS 698 Project Overview.pdf](http://cis.csuohio.edu/~tepietro/CIS698/CIS%20698%20Project%20Overview.pdf)
