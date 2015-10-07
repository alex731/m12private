if (document.getElementById){
document.write('<style type="text/css">\n')
document.write('.submenu{display: none;}\n')
document.write('</style>\n')
}

function SwitchMenu(obj){
	if(document.getElementById){
	var el = document.getElementById(obj);
	var ar = document.getElementById("masterdiv").getElementsByTagName("span");
		if(el.style.display != "block"){
			for (var i=0; i<ar.length; i++){
				if (ar[i].className=="submenu")
				ar[i].style.display = "none";
			}
			el.style.display = "block";
		}else{
			el.style.display = "none";
		}
	}
}

function borderize(what,color,bgColour){
	what.style.borderColor=color;
	what.style.backgroundColor=bgColour;
}

function borderize_on(e){
	if (document.all)
		source3=event.srcElement
	else if (document.getElementById)
		source3=e.target
	if (source3.className=="menulines"){
		borderize(source3,"#2D2D2D",'#F4F4F4')
	}
	else{
		while(source3.tagName!="TABLE"){
			source3=document.getElementById? source3.parentNode : source3.parentElement
			if (source3.className=="menulines")
			borderize(source3,"#2D2D2D",'#F4F4F4')
		}
	}
}

function borderize_off(e){
	if (document.all)
	source4=event.srcElement
	else if (document.getElementById)
	source4=e.target
	if (source4.className=="menulines"){
		borderize(source4,"#FFFFFF",'#F4F4F4')
	}
	else{
		while(source4.tagName!="TABLE"){
			source4=document.getElementById? source4.parentNode : source4.parentElement
			if (source4.className=="menulines")
			borderize(source4,"#FFFFFF",'#F4F4F4')
		}
	}
}