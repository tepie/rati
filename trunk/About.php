<?php
	
	include_once("Include\\SettingsWebApp.php");
	include_once("Include\\HtmlCommon.php");
	
	echo commonHtmlPageHead("About");
	echo commonHtmlPlainHeader();
?>
<br />

<div class="about_section_heading"><a name="what">What is rati?</a></div>
<div class="about_section_content">
<br />
<img src="<?php echo $web_app_default_logo_image; ?>" alt="rati the rat" align="right" />Rati stands for <b>"relational analysis through images"</b>. 
What does that mean? Say you wanted to do a family tree project 
to understand your family's heritage, culture, and where you
came from. The result of this gives insight into your background and helps
form your identity through your relationships with past family 
members.
<br /><br />
Once this analysis is complete, you can follow the relationships
back to a certain point to understand the how relationships were formed
and what resulted from those relationships, such as children.
<br /><br />
Assuming all this collection of information about your past family members
was done with paper an pencil, reading the information and following relationships
can be hard when many levels and members exist in the tree.
<br /><br />
What would be a better representation of that information verses simple text?
<br /><br />
How about with a picture? Since a picture speaks a thousand words, you can learn a great
deal by representing the relationships between the members of your family visually
rather then as text in a short amount of time.
<br /><br />
This is what rati does, it demonstrates the connection between <b>"things"</b> via
<b>"relationships"</b> and allows you to navigate the relationship structure.
<br /><br />
</div> 

<div class="about_section_heading"><a name="business_value">What is the business value?</a></div>
<div class="about_section_content">
<br />
While examining this tool, the age old question <b>"what is the business value"</b>
may come up. This is a very valid question, in that it avoids the technical savvy-ness
of a tool and gets back to real people.<br /><br />
When it comes to understanding your information, lengthy documentation and explanations
are good for research and in depth analysis. Full detail is a major requirement
in many areas such as legal and regulatory. Other times, just the basics are needed, a very
high level understanding for the start of basic conversation on how the world works and 
connects. 
<br /><br />
The visual perspective of information and its relationships in rati is a strive to share, at
a common level, visuals of connected information and the basic definitional meaning of these
connected objects. Rati can do much more, storing any number of relationships and annotation
values, but the basics will likely do because of the use of images. A relational map speaks
a thousand words (just like any other picture). How you explain something in words using 
language to another person may not entirely make sense since you are likely familiar with the 
concepts and the way they are used. An image helps portray that concept visually along with words
making it easier to share ideas and reality with people. 
<br /><br />
</div>
<div class="about_section_heading"><a name="why_rati">Rati... the Rat?</a></div>
<div class="about_section_content">
<br />
You might ask yourself, <b>"why would you
name a tool after a rodent"</b>, an animal that causes more problems then good
and would be better off dead then alive. While you might feel that this is true,
rats serve an important part in our environment, and killing them all would not
be good.
<br /><br />
Second, rats have survived for many years in very harsh conditions, they are strong
and smart. Rats will also eat almost anything, from fresh food to garbage. It is
the hope that this tool reflects some of the positive qualities of the rat:
<br /><br />
<ol>
	<li>Its ability to stand the test of time</li>
	<li>To consume any type of relational information available</li>
</ol>
<br /><br />
</div>
<div class="about_section_heading"><a name="links">Documentation Links</a></div>
<div class="about_section_content">
<br />
The following are documentational
links to help you learn more about this tool. If your answers are not
questioned by this documentation, feel free to contact the author with 
your answers.<br /><br />
<ul>
	<!-- <li><a href="<?php echo $web_app_source_code_url; ?>">Project Source Code</a></li> -->
	<li><a href="<?php echo $web_app_page_how_name;?>">How Rati Works</a></li>
	<!-- <li><a href="<?php echo $web_app_page_import_name;?>">Importing Data Into Rati</a></li> -->
	<li><a href="<?php echo $web_app_page_export_name;?>">Exporting Data From Rati</a></li>
	<li><a href="<?php echo $web_app_page_doxygen; ?>">Doxygen Documentation</a></li>
	<li><a href="./Doc/Object%20Relational%20Data%20Storage%20for%20Metadata.pdf">Overview of Object Database (PDF)</a></li>
	<li><a href="./Doc/Data%20Visualization%20Navigation.png">Flow Diagram (PNG)</a></li>
</ul>
<br /><br />
</div>

<?php echo commonHtmlPageFooter(); ?>
