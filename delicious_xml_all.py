
import xml.sax.handler
from xml.sax.saxutils import quoteattr
from xml.sax.saxutils import escape
import urllib
import xml.sax
import pprint
import codecs
from sets import Set

class BaseCategories:
	def __init__(self):
		self.__categories = []
		self.__categories.append("post")
		self.__categories.append("tag")
		
	def getCategories(sefl):
		return self.__categories

class VisualObject:
	def __init__(self,uri):
		self.__uri 		= uri
		self.__annos 	= []
		self.__refs 	= []
	
	def __str__(self):
		xmlString = "%s" % self.__openXmlObject()
		
		for a in self.__annos:
			rule 	= a[0]
			value 	= a[1]
			xmlString = "%s%s" % (xmlString,self.__xmlAnno(rule,value))
			
		for r in self.__refs:
			rule 	= r[0]
			value 	= r[1]
			xmlString = "%s%s" % (xmlString,self.__xmlRef(rule,value))
		
		xmlString = "%s%s" % (xmlString,self.__closeXmlObject())
		
		return "%s" % (xmlString)
	
	def openDocument(self):
		return "<?xml version='1.0' encoding='UTF-8' standalone='yes'?><del.icio.us><datastore>"
		
	def closeDocument(self):
		return "</datastore></del.icio.us>"
	
	def __openXmlObject(self):
		return "<object category='' oid=%s>" % quoteattr(self.__uri)
	
	def __closeXmlObject(self):
		return "</object>"
		
	def __xmlAnno(self,rule,value):
		return "<annotation name=%s>%s</annotation>" % (quoteattr(rule),escape(value))
	
	def __xmlRef(self,rule,value):
		return "<reference name=%s oidref=%s/>" % (quoteattr(rule),quoteattr(value))
	
	def annotate(self,rule,value):
		self.__annos.append((rule,value))

	def reference(self,rule,value):
		self.__refs.append((rule,value))
	
	def getUri(self):
		return self.__uri

class DeliciousHandler(xml.sax.handler.ContentHandler):
	def __init__(self):
		self.__visual 	= []
		self.__tags 	= []
		self.__categories = []
		self.__user 	= None
		self.__pathSep 	= "/"
		
		self.__tagDir 	= "tags"
		self.__categoryDir = "categories"
		
		self.__prefix 	= "%sdel.icio.us" % (self.__pathSep)
		
	def __createCategoryUri(self,category):
		return "%s%s%s%s%s" % (self.__prefix,self.__pathSep,self.__categoryDir,self.__pathSep,category)
	
	def __setPathPrefix(self):
		self.__prefix = "%s%s%s" % (self.__prefix,self.__pathSep,self.__user)
	
	def __createUri(self,href):
		
		if href[-1] == "/":
			href = href[:-1]
			
		return "%s%s%s" % (self.__prefix,self.__pathSep,href)
	
	def __createTagUri(self,tag):
		return "%s%s%s%s%s" % (self.__prefix,self.__pathSep,self.__tagDir,self.__pathSep,tag)
	
	def getVisualObjects(self):
		tagSet 		= self.__getTagsSet()
		categoryUri = self.__createCategoryUri("tag")
		
		for tagUri in tagSet:			
			tagVisObj = VisualObject(tagUri)
			#tagVisObj.reference("category",categoryUri)
			self.__visual.append(tagVisObj)
		
		return self.__visual
	
	def __getTagsSet(self):
		return Set(self.__tags)

	def startElement(self, name, attributes):
		if name == "posts":
			self.__user = attributes["user"]
			self.__setPathPrefix();
		
		elif name == "post":
			thisUri = self.__createUri(attributes["href"])
			visObj 	= VisualObject(thisUri)
			
			visObj.annotate("description",attributes["description"])
			visObj.annotate("hash",attributes["hash"])
			visObj.annotate("time",attributes["time"])
			visObj.annotate("href",attributes["href"])
			
			tagList = attributes["tag"].split(" ")
			
			for t in tagList:
				tagUri = self.__createTagUri(t)
				visObj.reference("tag",tagUri)
				self.__tags.append(tagUri)

			#categoryUri = self.__createCategoryUri("post")
			#visObj.reference("category",categoryUri)
			self.__visual.append(visObj)
		else:
			pass

	def characters(self, data): pass

	def endElement(self, name): pass

parser = xml.sax.make_parser()
handler = DeliciousHandler()

parser.setContentHandler(handler)
parser.parse("delicious_xml_all.xml")

urlHome 	= "http://csc06pocdvpa01s.keybank.com/rati/Import.php?xml_string="
visual_list = handler.getVisualObjects()
tempVisObj 	= VisualObject(None)

print "%s objects will be set..." % (len(visual_list))

for v in visual_list:
	str = "%s%s%s" % (v.openDocument(),v,v.closeDocument())
	
	if type(str) != unicode: 
		show = unicode(str,errors='replace')
	else: 
		show = str.encode("utf-8")
	
	#print show
	
	urlData 	= urllib.quote(show)
	urlToOpen 	= "%s%s" % (urlHome,urlData)
	try:
		u 			= urllib.urlopen(urlToOpen)
		data 		= u.read()
		print "sent: %s" % v.getUri()
		print "response: %s" % data 
	except IOError,e:
		print e
		
	
