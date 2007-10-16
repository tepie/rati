#! /usr/bin/env python

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
		