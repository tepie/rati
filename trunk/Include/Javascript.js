
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