function fileQueueError(file, errorCode, message) {
	try {
		var imageName = "error.gif";
		var errorName = "";
		if (errorCode === SWFUpload.errorCode_QUEUE_LIMIT_EXCEEDED) {
			errorName = "Вы пытаетесь загрузить слишком много файлов.";
		}

		if (errorName !== "") {
			alert("Error:"+errorName);
			return;
		}

		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			imageName = "zerobyte.gif";
			break;
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			imageName = "toobig.gif";
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
		default:
			alert(message);
			break;
		}

		addImage("images/" + imageName);

	} catch (ex) {
		this.debug(ex);
	}

}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		if (numFilesQueued > 0) {
			this.startUpload();
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadProgress(file, bytesLoaded) {

	try {
		var percent = Math.ceil((bytesLoaded / file.size) * 100);

		var progress = new FileProgress(file,  this.customSettings.upload_target);
		progress.setProgress(percent);
		if (percent === 100) {
			progress.setStatus("Создание превью...");
			progress.toggleCancel(false, this);
		} else {
			progress.setStatus("Загрузка...");
			progress.toggleCancel(true, this);
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadSuccess(file, serverData) {
	try {
		var progress = new FileProgress(file,  this.customSettings.upload_target);		
		var parts = serverData.split(':');		
		if (parts.length==2) {			
			addImage(parts[0],parts[1]);
			progress.setStatus("Превью созданы.");
			progress.toggleCancel(false);
		}
		else {
			addImage("images/error.gif");
			progress.setStatus("Error.");
			progress.toggleCancel(false);
			alert("Error:"+serverData);
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadComplete(file) {
	try {
		/*  I want the next upload to continue automatically so I'll call startUpload here */
		if (this.getStats().files_queued > 0) {
			this.startUpload();
		} else {
			var progress = new FileProgress(file,  this.customSettings.upload_target);
			progress.setComplete();
			progress.setStatus("Все файлы загружены.");
			progress.toggleCancel(false);
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadError(file, errorCode, message) {
	var imageName =  "error.gif";
	var progress;
	try {
		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			try {
				progress = new FileProgress(file,  this.customSettings.upload_target);
				progress.setCancelled();
				progress.setStatus("Отменено");
				progress.toggleCancel(false);
			}
			catch (ex1) {
				this.debug(ex1);
			}
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			try {
				progress = new FileProgress(file,  this.customSettings.upload_target);
				progress.setCancelled();
				progress.setStatus("Остановлено");
				progress.toggleCancel(true);
			}
			catch (ex2) {
				this.debug(ex2);
			}
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			imageName = "uploadlimit.gif";
			break;
		default:
			alert(message);
			break;
		}
		addImage("images/" + imageName);
	} catch (ex3) {
		this.debug(ex3);
	}

}


function addImage(kind,file_name) {
	//alert("addImage("+kind+","+file_name+")");
	if (file_name !='images/toobig.gif') {
		//'Tenement'
		if (kind == 1) {			
			var html = "<table class='base_text' style='float:left'><tr><td rowspan=3><img src='"+$_site_url+"tmp_photos/"+file_name+"_prev'>" +		
			"</td>" +
			"<td><label for='photo_type_"+file_name+"'>На фото показан:</label><select name='photo_type_"+file_name+"' id='photo_type_"+file_name+"'>" +
			"<option value='0'>Дом</option>" +
			"<option value='1'>Двор</option>" +
			"<option value='2'>Подъезд</option>" +
			"<option value='3'>Другое</option>" +
			"</select></td></tr>" + 
			"<td><label for='photo_title_"+file_name+"'>Название фото:</label><input type='text' id='photo_title_"+file_name+"' name='photo_title_"+file_name+"'></td></tr>" +
			"<tr><td><label for='photo_desc_"+file_name+"'>Описание:</label><textarea id='photo_desc' name='photo_desc_"+file_name+"' style='width:200px; height: 50px;' ></textarea></td></tr>" +		
			"</table>" +		
			"<input type=hidden name=photo_tenement[] value='"+file_name+"'>"; 
		}
		//'Flat'
		else if (kind == 2) {
			var html = "<table class='base_text' style='float:left'><tr><td rowspan=3><img src='"+$_site_url+"tmp_photos/"+file_name+"_prev'>" +		
			"</td>" +
			"<td><label for='photo_type_"+file_name+"'>На фото показано:</label><select name='photo_type_"+file_name+"' id='photo_type_"+file_name+"'>" +
			"<option value='0'>Кухня</option>" +
			"<option value='1'>Ванная</option>" +
			"<option value='2'>Туалет</option>" +
			"<option value='3'>Зал</option>" +
			"<option value='4'>Лоджия/балкон</option>" +
			"<option value='5'>Прихожая</option>" +
			"<option value='6'>Комната</option>" +
			"<option value='7'>Вид из окна</option>" +
			"</select></td></tr>" + 
			"<td><label for='photo_title_"+file_name+"'>Название фото:</label><input type='text' id='photo_title_"+file_name+"' name='photo_title_"+file_name+"'></td></tr>" +
			"<tr><td><label for='photo_desc_"+file_name+"'>Описание:</label><textarea id='photo_desc' name='photo_desc_"+file_name+"' style='width:200px; height: 50px;' ></textarea></td></tr>" +		
			"</table>" +		
			"<input type=hidden name=photo_flat[] value='"+file_name+"'>"; 
		}
		//'House'
		else if (kind == 3) {			
			var html = "<table class='base_text' style='float:left'><tr><td rowspan=3><img src='"+$_site_url+"tmp_photos/"+file_name+"_prev'>" +		
			"</td>" +
			"<td><label for='photo_type_"+file_name+"'>На фото показан:</label><select name='photo_type_"+file_name+"' id='photo_type_"+file_name+"'>" +
			"<option value='0'>Дом</option>" +
			"<option value='1'>Двор</option>" +
			"<option value='2'>Внутри</option>" +
			"<option value='3'>Другое</option>" +
			"</select></td></tr>" + 
			"<td><label for='photo_title_"+file_name+"'>Название фото:</label><input type='text' id='photo_title_"+file_name+"' name='photo_title_"+file_name+"'></td></tr>" +
			"<tr><td><label for='photo_desc_"+file_name+"'>Описание:</label><textarea id='photo_desc' name='photo_desc_"+file_name+"' style='width:200px; height: 50px;' ></textarea></td></tr>" +		
			"</table>" +		
			"<input type=hidden name=photo_house[] value='"+file_name+"'>"; 
		}
		//'Land'
		else if (kind == 5) {			
			var html = "<table class='base_text' style='float:left'><tr><td rowspan=3><img src='"+$_site_url+"tmp_photos/"+file_name+"_prev'>" +		
			"</td>" +
			"<td><label for='photo_type_"+file_name+"'>На фото показан:</label><select name='photo_type_"+file_name+"' id='photo_type_"+file_name+"'>" +
			"<option value='0'>Земельный участок</option>" +
			"<option value='1'>Дорога</option>" +
			"<option value='2'>Населенный пункт</option>" +
			"<option value='3'>Другое</option>" +
			"</select></td></tr>" + 
			"<td><label for='photo_title_"+file_name+"'>Название фото:</label><input type='text' id='photo_title_"+file_name+"' name='photo_title_"+file_name+"'></td></tr>" +
			"<tr><td><label for='photo_desc_"+file_name+"'>Описание:</label><textarea id='photo_desc' name='photo_desc_"+file_name+"' style='width:200px; height: 50px;' ></textarea></td></tr>" +		
			"</table>" +		
			"<input type=hidden name=photo_land[] value='"+file_name+"'>"; 
		}
		//'COMPANY'
		else if (kind == 7) {			
			var html = "<table class='base_text' style='float:left'><tr><td rowspan=3><img src='"+$_site_url+"tmp_photos/"+file_name+"_prev'>" +		
			"</td></tr>" +			
			"<td><label for='photo_title_"+file_name+"'>Название фото:</label><input type='text' id='photo_title_"+file_name+"' name='photo_title_"+file_name+"'></td></tr>" +
			"<tr><td><label for='photo_desc_"+file_name+"'>Описание:</label><textarea id='photo_desc' name='photo_desc_"+file_name+"' style='width:200px; height: 50px;' ></textarea></td></tr>" +		
			"</table>" +		
			"<input type=hidden name=photo_company[] value='"+file_name+"'>"; 
		}
		//'Logo'
		else if (kind == 8) {			
			var html = "<img src='"+$_site_url+"tmp_photos/"+file_name+"'><input type=hidden name=logo[] value='"+file_name+"'>";
			$("#thumbnails"+kind).empty();
		}
		//'Commercial'
		else if (kind == 9) {			
			var html = "<table class='base_text' style='float:left'><tr><td rowspan=3><img src='"+$_site_url+"tmp_photos/"+file_name+"_prev'>" +		
			"</td>" +
			"<td><label for='photo_type_"+file_name+"'>На фото показан:</label><select name='photo_type_"+file_name+"' id='photo_type_"+file_name+"'>" +
			"<option value='0'>Внутри</option>" +
			"<option value='1'>Снаружи</option>" +
			"<option value='2'>Другое</option>" +			
			"</select></td></tr>" + 
			"<td><label for='photo_title_"+file_name+"'>Название фото:</label><input type='text' id='photo_title_"+file_name+"' name='photo_title_"+file_name+"'></td></tr>" +
			"<tr><td><label for='photo_desc_"+file_name+"'>Описание:</label><textarea id='photo_desc' name='photo_desc_"+file_name+"' style='width:200px; height: 50px;' ></textarea></td></tr>" +		
			"</table>" +		
			"<input type=hidden name=photo_commercial[] value='"+file_name+"'>"; 
		}

		$(html).appendTo("#thumbnails"+kind);
	}
	else {
		$("<div style='float:left'><img src='"+$_site_url+"images/toobig.gif'></div>").appendTo("#thumbnails"+kind);
	}
	/*
	var newImg = document.createElement("img");
	newImg.style.margin = "5px";
	
	//document.getElementById("thumbnails").appendChild(newImg);
	$("#thumbnails")[0].appendChild(newImg);
	if (newImg.filters) {
		try {
			newImg.filters.item("DXImageTransform.Microsoft.Alpha").opacity = 0;
		} catch (e) {
			// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
			newImg.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + 0 + ')';
		}
	} else {
		newImg.style.opacity = 0;
	}

	newImg.onload = function () {
		fadeIn(newImg, 0);
	};
	newImg.src = src;
	*/
}

function fadeIn(element, opacity) {
	var reduceOpacityBy = 5;
	var rate = 30;	// 15 fps


	if (opacity < 100) {
		opacity += reduceOpacityBy;
		if (opacity > 100) {
			opacity = 100;
		}

		if (element.filters) {
			try {
				element.filters.item("DXImageTransform.Microsoft.Alpha").opacity = opacity;
			} catch (e) {
				// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
				element.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + opacity + ')';
			}
		} else {
			element.style.opacity = opacity / 100;
		}
	}

	if (opacity < 100) {
		setTimeout(function () {
			fadeIn(element, opacity);
		}, rate);
	}
}



/* ******************************************
 *	FileProgress Object
 *	Control object for displaying file info
 * ****************************************** */

function FileProgress(file, targetID) {
	this.fileProgressID = "divFileProgress"+targetID;

	this.fileProgressWrapper = document.getElementById(this.fileProgressID);
	if (!this.fileProgressWrapper) {
		this.fileProgressWrapper = document.createElement("div");
		this.fileProgressWrapper.className = "progressWrapper";
		this.fileProgressWrapper.id = this.fileProgressID;

		this.fileProgressElement = document.createElement("div");
		this.fileProgressElement.className = "progressContainer";

		var progressCancel = document.createElement("a");
		progressCancel.className = "progressCancel";
		progressCancel.href = "#";
		progressCancel.style.visibility = "hidden";
		progressCancel.appendChild(document.createTextNode(" "));

		var progressText = document.createElement("div");
		progressText.className = "progressName";
		progressText.appendChild(document.createTextNode(file.name));

		var progressBar = document.createElement("div");
		progressBar.className = "progressBarInProgress";

		var progressStatus = document.createElement("div");
		progressStatus.className = "progressBarStatus";
		progressStatus.innerHTML = "&nbsp;";

		this.fileProgressElement.appendChild(progressCancel);
		this.fileProgressElement.appendChild(progressText);
		this.fileProgressElement.appendChild(progressStatus);
		this.fileProgressElement.appendChild(progressBar);

		this.fileProgressWrapper.appendChild(this.fileProgressElement);

		document.getElementById(targetID).appendChild(this.fileProgressWrapper);
		fadeIn(this.fileProgressWrapper, 0);

	} else {
		this.fileProgressElement = this.fileProgressWrapper.firstChild;
		this.fileProgressElement.childNodes[1].firstChild.nodeValue = file.name;
	}

	this.height = this.fileProgressWrapper.offsetHeight;

}
FileProgress.prototype.setProgress = function (percentage) {
	this.fileProgressElement.className = "progressContainer green";
	this.fileProgressElement.childNodes[3].className = "progressBarInProgress";
	this.fileProgressElement.childNodes[3].style.width = percentage + "%";
};
FileProgress.prototype.setComplete = function () {
	this.fileProgressElement.className = "progressContainer blue";
	this.fileProgressElement.childNodes[3].className = "progressBarComplete";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setError = function () {
	this.fileProgressElement.className = "progressContainer red";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setCancelled = function () {
	this.fileProgressElement.className = "progressContainer";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setStatus = function (status) {
	this.fileProgressElement.childNodes[2].innerHTML = status;
};

FileProgress.prototype.toggleCancel = function (show, swfuploadInstance) {
	this.fileProgressElement.childNodes[0].style.visibility = show ? "visible" : "hidden";
	if (swfuploadInstance) {
		var fileID = this.fileProgressID;
		this.fileProgressElement.childNodes[0].onclick = function () {
			swfuploadInstance.cancelUpload(fileID);
			return false;
		};
	}
};
