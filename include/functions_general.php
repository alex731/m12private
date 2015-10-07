<?php
function clearTextData($text,$max_len=255) {
	$s = strip_tags(substr(trim($text),0,$max_len));
	//mysql_escape_string - magic_quotes run
	return $s;
} 

function numStrToClearStr($str) {
	$str = str_replace(',','.',$str);				
	$str = str_replace(' ','',$str);
	return $str;
}

function getRandomStr($length=5) {
	$r = md5(rand(0,999999).time());
	$s = substr($r,0,$length);
	return $s;
}

function getNextDate($date_str,$delta_days) {
	$d = explode('-',$date_str);
	$date_new = mktime(0, 0, 0, $d[1], $d[2]+$delta_days, $d[0]);	
	return date("Y-m-d",$date_new);
}

function formatDateExact($date_str) {
	$date = explode(' ',$date_str);
	$dates = explode('-',$date[0]);
	$times = explode(':',$date[1]);
	return $dates[2].'.'.$dates[1].' '.$times[0].':'.$times[1];	
}

function formatDate($date_str) {
	$date = explode(' ',$date_str);
	$dates = explode('-',$date[0]);
	$times = explode(':',$date[1]);
	return $dates[2].'.'.$dates[1].'.'.substr($dates[0],2,2);	
}

function truncate_utf8($string, $len, $wordsafe = FALSE, $dots = FALSE) {
	$slen = strlen($string);
	if ($slen <= $len) {
		return $string;
	}
	if ($wordsafe) {
		$end = $len;
		while (($string[--$len] != ' ') && ($len > 0)) {};
		if ($len == 0) {
		$len = $end;
		}
	}
	if ((ord($string[$len]) < 0x80) || (ord($string[$len]) >= 0xC0)) {
		return substr($string, 0, $len) . ($dots ? ' ...' : '');
	}
	while (--$len >= 0 && ord($string[$len]) >= 0x80 && ord($string[$len]) < 0xC0) {};
	return substr($string, 0, $len) . ($dots ? ' ...' : '');
}


function textReduce($text, $max_len=255) {
	/*
	$dots = (strlen($text)>$max_len) ? '...' : '';
	$desc = str_replace('.','. ',$text);
	$desc = str_replace(',',', ',$desc);
	$desc = str_replace('-',' - ',$desc);
	$desc = str_replace('. ,','.,',$desc);
	return substr($desc,0,$max_len).$dots;
	*/
	return truncate_utf8($text, $max_len,true,true);
}
?>