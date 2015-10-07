<?php

function smarty_prepare() { 
	$smarty = new Smarty;
	$smarty->compile_check = true;
	$smarty->debugging = false;
	return $smarty;
}
function adodb_prepare(){ 
	global $config;
            $ADODB_FETCH_MODE  = ADODB_FETCH_NUM;
	$ADODB_COUNTRECS = false;
	$c = &ADONewConnection($config['dbtype']);
	$c->debug = false;
	if (!$c->PConnect($config['dbserver'],$config['dbuser'],$config['dbpassword'],$config['dbname'])) { 
		echo "Sorry, too much users on site now. Please, enter later.";	
	}
	return $c;
}

function smarty_prepare_directory() { 
	global $config;
	$smarty = new Smarty;
	$smarty->compile_check = true;
	$smarty->force_compile = true;
	$smarty->template_dir = $config["template_dir"];
	$smarty->compile_dir = $config["compile_dir"];
	$smarty->debugging = false;	
	return $smarty;
}
function adodb_prepare_directory(){ 
	global $config;
	//$ADODB_FETCH_MODE  = ADODB_FETCH_NUM;
	//$ADODB_COUNTRECS = false;
	$c = &ADONewConnection($config['dbtype']);
	$c->debug = false;
	if (!$c->PConnect($config['dbserver'],$config['dbuser'],$config['dbpassword'],$config['dbname'])) { 
		echo "Sorry, too much users on site now. Please, enter later.";	
	}
	return $c;
}


//Возвращает из f:/windows/file.txt -> file
function get_file_name($full_name){ 
	$file_name = substr(strrchr($full_name,'/'),1); 
	return strtok($file_name,".");
}


function public_news() {
	global $c, $s,$config;
	$news = array();
	$res = $c->Execute("SELECT * FROM news n, news_".$_SESSION["language"]." nl WHERE n.id_news=nl.id_news AND n.status='a' order by date DESC");
	$i = 0;
	while(!$res->EOF and $i < $config["news_count"]){
		$row = $res->GetRowAssoc(false);
		$news[$i]["id_news"] = $row["id_news"];
		$news[$i]["date"] = date("j F Y",strtotime($row["date"]." + 1 sec"));
		$news[$i]["title"] = html_entity_decode($row["title"]);
		$news[$i]["content"] = cut_text(stripslashes($row["content"]),$config["news_length"]);
		$news[$i]["content"] = str_replace('{$config.company}',$config["company"],$news[$i]["content"]);
		$i++;
		$res->MoveNext();
	}
	$s->assign("news", $news);
}

function public_tip($count=1){
	global $c, $s,$config, $txt;

	$res = $c->Execute("SELECT * FROM tips_".$_SESSION["language"]);	
	$i = 0;
	while(!$res->EOF){
		$row = $res->GetRowAssoc(false);
		$tip[$i] = $row["id_tip"];
		$i++;
		$res->MoveNext();
	}
	if ($count==1) {
		srand(mktime());
		$rand_i = rand(0,$i-1);
		$res = $c->Execute("SELECT * FROM tips_".$_SESSION["language"]." WHERE id_tip='$tip[$rand_i]'");
		$row = $res->GetRowAssoc(false);
		$tip["title"] = html_entity_decode($row["title"]);
		$tip["content"] = html_entity_decode($row["content"]); 
		$s->assign("tip", $tip);
	}
	else {
		srand(mktime());
		$rand_i = rand(0,$i-1);
		$rand2_i = rand(0,$i-1);
		$a = 0;
		while ($rand2_i == $rand_i && $a < 30) {
			srand(mktime()+rand(1,10));
			$rand2_i = rand(0,$i-1);
			$a++;
		}
		$res = $c->Execute("SELECT * FROM tips_".$_SESSION["language"]." WHERE id_tip='$tip[$rand_i]' OR id_tip='$tip[$rand2_i]'");
		$row = $res->GetRowAssoc(false);
		$tip["title1"] = html_entity_decode($row["title"]);
		$tip["content1"] = html_entity_decode($row["content"]); 
		$res->MoveNext();
		$row = $res->GetRowAssoc(false);
		$tip["title2"] = html_entity_decode($row["title"]);
		$tip["content2"] = html_entity_decode($row["content"]); 		
		$s->assign("tip", $tip);	
	}
}	

function check_client() {
	$id_client = 0;
	if ( !( (isset($_SESSION["id_client"])) and ($_SESSION["id_client"] > 0))) 	{
		header("Location: login.html");
		exit;
	}
}

//f:\index.php -> index.php
function last_string($string,$delimiter) {
	return substr(strrchr($string,$delimiter), 1);
}
	

function is_valid_email( $p_email ) {
	global $config;
	# Use a regular expression to check to see if the email is in valid format
	#  x-xx.xxx@yyy.zzz.abc etc.
	if (eregi("^[_.0-9a-z-]+@([0-9a-z][-0-9a-z.]+).([a-z]{2,6}$)", $p_email, $check)) {
		# passed format check. see if we should check the mx records
		if ( $config["system"] == "0") {	
			if (getmxrr($check[1].".".$check[2], $temp)) {
				return true;
			} else {
				$host = substr( strstr( $check[0], '@' ), 1 ).".";

				# for no mx record... try dns check
				if (checkdnsrr ( $host, "ANY" ))
					return true;
			}
		} else {
			# Email format was valid but did't check for valid mx records
			return true;
		}
	}
	# Everything failed.  Bad email.
	return false;
}
function check_name($name) {
	if (preg_match("/^[\d\w\-]+$/",$name)) return true; else return false;
}

function check_domain($domain) {
	if (preg_match("(^(\w)[-\w]+\w\.(\w+)$)",$domain) && strlen($domain) < 255) return true; else return false;
}

function check_domain_name($domain) {
	if (preg_match("/^[\d\w\-]+$/",$domain) && strlen($domain) < 64) return true; else return false;
}
function check_domain_zone($zone) {
	if (preg_match("(\w+)",$zone) && strlen($zone) < 64) return true; else return false;
}
function check_float($float) {
	if (ereg("([[:digit:]]+)|([[:digit:]]+)\.([[:digit:]]+)",$float)&& !preg_match("([[:alpha:]]+)",$float)) return true; else return false;
}

function check_int($int) {
	if (ereg("([[:digit:]]+)",$int)) return true; else return false;
}
function cut_text($text, $count_words) {
	$now_words = 0;
	$chars = 0;
	$amount_chars = strlen($text)-1;
	$end = '...';
	while ($now_words < $count_words){
		if ($chars >= $amount_chars){
			$end = '';
			$chars++;
			break;
		}
		while ($text[$chars] == ' '){
			$chars++;
			if ($chars >= $amount_chars){
				$end = '';
				$chars++;
				break(2);
			}
		}
		if ($chars >= $amount_chars){
				$end = '';
				$chars++;
				break(2);
			}
		while ($text[$chars] != ' '){
			if ($chars >= $amount_chars){
				$end = '';
				$chars++;
				break(2);
			}
			$chars++;
		}
		$now_words++;
	}
	return substr($text,0,$chars).$end;
}

function get_js_date($date)
{
	return ($date == null ? date('Ymd') : date('Ymd', strtotime($date)));
}

function get_date_field($form_name, $field_name, $val_date)
{
	$val_date = get_js_date($val_date);
	$r = '<input type="hidden" name="'.$field_name.'" value="'.$val_date."\">\n";
	$r.= '<table width="200" border="0" cellspacing="0" cellpadding="0">'."\n".'<tr>';
	$r.= '<td class="row" width="30">';
	$r.= '<select name="_day_'.$field_name.
		'" class="form" onchange="updateDate(document.'.$form_name.'._day_'.$field_name.
		', document.'.$form_name.'._month_'.$field_name.', document.'.$form_name.
		'._year_'.$field_name.', document.'.$form_name.'.'.$field_name.')">'.
				'<script language="javascript">document.write(buildDaySelector(document.'.
		$form_name.'.'.$field_name.'));</script></select>';
	$r.= '</td>';
	$r.= '<td class="row" width="40">';
	$r.= '<select name="_month_'.$field_name.
		'" class="form" onchange="updateDate(document.'.$form_name.'._day_'.$field_name.
		', document.'.$form_name.'._month_'.$field_name.', document.'.$form_name.
		'._year_'.$field_name.', document.'.$form_name.'.'.$field_name.')">'.
				'<script language="javascript">document.write(buildMonthSelector(document.'.
		$form_name.'.'.$field_name.'));</script></select>';
	$r.= '</td>';
	$r.= '<td class="row" width="40">';
	$r.= '<select name="_year_'.$field_name.
		'" class="form" onchange="updateDate(document.'.$form_name.'._day_'.$field_name.
		', document.'.$form_name.'._month_'.$field_name.', document.'.$form_name.
		'._year_'.$field_name.', document.'.$form_name.'.'.$field_name.')">'.
				'<script language="javascript">document.write(buildYearSelector(document.'.
		$form_name.'.'.$field_name.'));</script></select>';
	$r.= '</td>';
	$r.= '<td class="row" width="20">';
	$r.= '<a href="javascript:doNothing()" onclick="setDateField(document.'.$form_name.
		'._day_'.$field_name.', document.'.$form_name.'._month_'.$field_name.
		',document.'.$form_name.'._year_'.$field_name.', document.'.$form_name.
		'.'.$field_name.");self.newWin = window.open('../common/calendar.html','cal','dependent=yes,width=180,height=180,titlebar=yes,top=243,left=232')".
		'"><img src="../common/calendar.gif" width="16" height="16" border="0"></a>';
	$r.= '</td></tr></table>';
	return $r;
}

function create_date($date){
	return (date("Y-m-d H:i:s", strtotime(
		substr($date, 0, 4).'-'.
		substr($date, 4, 2).'-'.
		substr($date, 6, 2)
		))
	);
}

function format_date($format, $date){
	return date($format,strtotime($date));
}


function send_mail($to, $from_name="", $from, $subject, $content, $coding = "iso-8859-1"){
	$headers = "From: ".$from_name."<".$from.">\r\n";
	$headers .= "X-Mailer: PHP/".phpversion()."\r\n";
	$headers .= "X-Priority: 0\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=".$coding."\r\n";	
	//$fp = fopen(microtime().'.html','w+');
	//fwrite($fp,$to."<br>".$subject.'<br>'.$from."<br>".$content);
	//fclose($fp);
	return mail($to, $subject, $content, $headers);
}

function login() {
	global $c;
	//restrict area
	$id_client = $_SESSION["id_client"];
	//domains
	$domain = array();
	$res = $c->Execute("SELECT id_domain FROM domains WHERE id_client='".$id_client."'");
	$i = 0;
	while ($row = $res->FetchRow()) {
		$domain[$i] = $row["id_domain"];
		$i++;
	}
	$_SESSION["profile_domains"] = $domain;	
	
	//links
    $link = array();
	$res = $c->Execute("SELECT id_link FROM links WHERE id_client='".$id_client."'");
	$i = 0;
	while ($row = $res->FetchRow()) {
		$link[$i] = $row["id_link"];
		$i++;
	}
	$_SESSION["profile_links"] = $link;			
	
	//articles
	$article = array();
	$res = $c->Execute("SELECT id_article FROM articles WHERE id_client='".$id_client."'");
	$i = 0;
	while ($row = $res->FetchRow()) {
		$article[$i] = $row["id_article"];
		$i++;
	}
	$_SESSION["profile_articles"] = $article;				

}

function totalStr($haystack, $needle,$i = 0)
{
	while(strpos($haystack,$needle) !== false) {
		echo $haystack = substr($haystack, (strpos($haystack,$needle) + 1));
		$i++;
	}
	return $i;
}


?>
