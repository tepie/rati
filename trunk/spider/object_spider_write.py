#! /usr/bin/env python

import httplib
import urllib
import sys
import re
from HTMLParser import HTMLParser

import spider
from xml.sax.saxutils import escape
		
class MyObjectXmlWriter:
	
	
	def __init__(self,pairs,content):
		
		self.__pairs = pairs
		self.__content = content
		self.__prefix = "/web/"
		self.__xml_type = u"<?xml version='1.0' encoding='UTF-8' standalone='yes'?>"
		self.__xml_head = u"<web><datastore>"
		self.__xml_tail = u"</datastore></web>"
		
		self.__xml_object_open 	= u"<object category='%s' oid='%s'>"
		self.__xml_object_close = u"</object>"
		self.__xml_reference 	= u"<reference name='%s' oidref='%s'/>"
		self.__xml_annotation_open	= u"<annotation name='%s'>"
		self.__xml_annotation_close = u"</annotation>"
		
	def __write_head(self):
		print self.__xml_type
		print self.__xml_head

	def __write_tail(self):
		print self.__xml_tail
	
	def __write_object_open(self,category,oid):
		fake_out = "%s%s" % (self.__prefix,oid)
		print self.__xml_object_open % (escape(category),escape(fake_out))
	
	def __write_object_close(self):
		print self.__xml_object_close
		
	def __write_reference(self,name,ref):
		fake_out = "%s%s" % (self.__prefix,ref)
		print self.__xml_reference % (escape(name),escape(fake_out))
		
	def __write_annotation(self,rule,value):
		print self.__xml_annotation_open % escape(rule)
		print escape(value)
		print self.__xml_annotation_close
		
	def __loop_pairs(self):
		count = 0
		for k, v in self.__pairs.iteritems():
			self.__write_object_open(u"page",k)
			print u"<!-- Counter:",count,"-->"
			try:
				for member in v:
					self.__write_reference(u"href",member)
					
			except TypeError,e: print u"<!-- TypeError:",v,e,"-->"
			self.__write_annotation("content",self.__content[k])
			self.__write_object_close()
			count = count + 1
			
	def print_object_xml(self):
		self.__write_head()
		self.__loop_pairs()
		self.__write_tail()
		

class miniHTMLParser( HTMLParser ):
	viewedQueue = []
	instQueue = []
	limit = 5
	
	def set_limit(self,li):
		self.limit = li
		
	def get_viewed(self): 
		return self.viewedQueue
	
	def get_next_link( self ):
		if self.instQueue == []:
			return ''
		else:
			return self.instQueue.pop(0)

	def gethtmlfile( self, site, page ):
		try:
			f = urllib.urlopen(page)
			resppage = f.read()
		except:
			print "connection error"
			resppage = ""

		return resppage

	def handle_starttag( self, tag, attrs ):
		if tag == 'a':
			#print attrs
			if attrs[0][0] == "href":
				newstr = str(attrs[0][1])
				#if re.search('http', newstr) == None:
				if re.search('mailto', newstr) == None:
						#print "\t",newstr
					if len(self.viewedQueue) < self.limit: 
						self.viewedQueue.append( newstr )
					else:
						#print u"<!-- Ignoring Limit %s: %s -->" % (str(self.limit),newstr)
						pass
				else:
					#print u"<!-- Ignoring Mailto %s -->" % newstr
					pass
			else:
				#print u"<!-- Ignoring %s %s -->" % (attrs[0][0],newstr)
				pass
						
			'''if re.search('http', newstr) == None:
				if re.search('mailto', newstr) == None:
					if re.search('htm', newstr) != None:
						if (newstr in self.viewedQueue) == False:
							print newstr
							#self.instQueue.append( newstr )
							#self.viewedQueue.append( newstr )
					else:
						print "  ignoring", newstr
				else:
					print "  ignoring", newstr
			else:
				print "  ignoring", newstr
			'''

		
if __name__ == '__main__':
	
	url 		= "https://www.key.com/"
	resources 	= 100
	depth 		= 20
	threads		= 5
	
	page = "%s%s" % (url,"index.html")
	
	'''if len(sys.argv) == 5:
		url = sys.argv[1]
		resource = sys.argv[2]
		depth = sys.argv[3]
		threads = sys.argv[4]
	else:
		print "usage:",sys.argv[0],"<base url> <resource> <depth> <threads>"
	'''
	weburls = spider.weburls(page,resources,depth,threads)
	
	pairs = {}
	content = {}
	for thisurl in weburls:
		#print thisurl
		if not pairs.has_key(thisurl): pairs[thisurl] = []
		htmlparse = miniHTMLParser()
		htmlparse.set_limit(depth)
		retfile = htmlparse.gethtmlfile( url, thisurl )
		if re.search("(\.gif$)|(\.ico$)|(\.jpg$)",thisurl) == None:
			content[thisurl] = retfile
		else:
			content[thisurl] = ""
		try:
			htmlparse.feed(retfile)
			links = htmlparse.get_viewed()
			for l in links: 
				if re.search("^http",l) == None:
					if l == "." or l == "/" or l == "#":
						pairs[thisurl].append(url)
					else:
						temp ="%s%s" % (url,l)
						#print temp,thisurl,l
						pairs[thisurl].append(temp)
				else:					
					pairs[thisurl].append(l)
			htmlparse.close()
		except:pass
		
	myxmlwriter = MyObjectXmlWriter(pairs,content)
	myxmlwriter.print_object_xml()
	print u"<!-- Found URLS:",len(weburls),"-->"
