// Imageready javascript (just for a change!)

function newImage(arg) {
	if (document.images) {
		rslt = new Image();
		rslt.src = arg;
		return rslt;
	}
}

function changeImages() {
	if (document.images && (preloadFlag == true)) {
		for (var i=0; i<changeImages.arguments.length; i+=2) {
			document[changeImages.arguments[i]].src = changeImages.arguments[i+1];
		}
	}
}

var preloadFlag = false;
function preloadImages() {
	if (document.images) {
		but_company_over = newImage("images/but_company-over.gif");
		but_news_over = newImage("images/but_news-over.gif");
		but_webhosting_over = newImage("images/but_webhosting-over.gif");
		but_services_over = newImage("images/but_services-over.gif");
		but_customerSup_over = newImage("images/but_customerSup-over.gif");
		preloadFlag = true;
	}
}


// Used for news navigation
function toggleFolder(strID){
	var objRef = eval("FC"+strID);
	if (objRef.innerHTML !== ""){
		if (objRef.style.display == "inline"){
			objRef.style.display = "none";
		} else {
			objRef.style.display = "inline";
		}	
	}
}	

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
	
function hilite(){
	var el = window.event.srcElement;
	
	if (el.className == "Folder"){
		el.className = "FolderO";
	} else if (el.className == "FolderO"){
		el.className = "Folder";
	}
}

// Used to prevent multiple submit button clicks
clickcounter = 0;
function NoRepeat() {
	if(clickcounter == 1) { return false; }
	clickcounter = 1;
	return true;
}

// calendar popup function

function openCalendar(sDate,sCallback,sCallbackField,nTop,nLeft)
{
	var oWindow;
	oWindow = window.open("/includes/popCalendar.asp?date=" + sDate + "&callback=" + sCallback + "" + "&callbackfield=" + sCallbackField + "","calendar","top=" + nTop + ",left=" + nLeft + ",height=165,width=160,status=no,resizable=no,toolbar=no,menubar=no,scrollbars=no,location=no");
	oWindow.focus();
	return true;
}

function Date_Change(sNewValue,sField)
{
	document.editForm[sField].value = sNewValue;
}

// Used in CMS
function popup(url)
{
	window.open(url, 'editor', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resize=no,width=655,height=508,left=20,top=20')
}

// Generic searchfield validation
// use onsubmit="return validator(this)" in form tag
function validator(theForm) { // only submit form if search text present
	if (theForm.SearchText.value == '')
	{
		theForm.SearchText.focus();
		return (false);
	}
	return (true);
}

// used on the microsites to popup large pics of the maps
function largePic(picSrc,picCaption) {
  var thePage = "/largepic.html?largePicSrc=" + escape(picSrc) + "&Caption=" + escape(picCaption);
  // var thePage = "http://" + location.hostname + "/largepic.html?largePicSrc=" + escape(picSrc) + "&Caption=" + escape(picCaption);
  window.open(thePage,"","width=200,height=200,resizable=yes,screenX=150,screenY=150,top=150,left=150");
}