#! /usr/bin/env python

import httplib
import urllib
import sys
import re
from HTMLParser import HTMLParser

# these are local
import spider,objectwriter

#from xml.sax.saxutils import escape

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
	resources 	= 10
	depth 		= 3
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
	
	try:
		weburls = spider.weburls(page,resources,depth,threads)
	except IOError,e:
		print "Could not get the URLs..."
		print e
		sys.exit(-1)
	
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
		
	myxmlwriter = objectwriter.MyObjectXmlWriter(pairs,content)
	myxmlwriter.print_object_xml()
	print u"<!-- Found URLS:",len(weburls),"-->"
