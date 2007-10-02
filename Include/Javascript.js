
/* is IE variable holder */
var is_ie;

/** Popup a window */
function popup(url){
	newwindow=window.open(url,'_blank','resizable=1,scrollbars=1,height=500,width=600');
	if (window.focus) {newwindow.focus()}
	return false;
}

/** Detect this browser */
function detectBrowser(){
	var browser		=	navigator.appName
	var b_version	=	navigator.appVersion
	
	if (browser == "Microsoft Internet Explorer"){
		is_ie = true;
	} else{
		is_ie = false;
	}
	
	return;
}

/** Write some heading
*/
function writeHeadingSpacing(){
	/** This is not an IE browser */
	if(!is_ie){ 
		//document.write("<br />"); 
	} 
	
	return;
}

/** Load a page 
* page the page name to load
* parm the parameter value in the url to set
*/
function loadPage(page,parm){
	var val 		= document.getElementById(parm);
	var location 	= page + '?' + parm + '=' + val.value;
	/** I don't know why this works, but it does */
	document.write("<br />");
	window.open(location,'_self');
}

/** Ajax
*/

var xmlHttp;

function getXmlHttpObject(){
	var xmlHttp = null;
	try {
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	} catch (e) {
		// Internet Explorer
		try	{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e)	{
			try	{
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
			}catch (e)	{
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	
	return xmlHttp;
}

function showHtmlExport(url){
	alert(url);
	document.getElementById("image_html_export").innerHTML="Loading...";
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		alert ("Your browser does not support AJAX!");
		return;
	} 
	
	xmlHttp.onreadystatechange=stateChanged;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}

function stateChanged() { 
	if (xmlHttp.readyState==4){ 
		document.getElementById("image_html_export").innerHTML=xmlHttp.responseText;
	}
}

