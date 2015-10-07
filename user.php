<?
include_once("./include/common.php");
$err = "";
$message = "";
$is_err = false;
$s->assign('session_id', session_id());

if (isset($_REQUEST['action'])) {
	$action = clearTextData($_REQUEST['action']);
	if (in_array($action,array('login','logout','import'))) 
		$action($s);
}			

function login() {
	if (isset($_POST)) {
		$login = clearTextData($_POST['login']);
		$pass = clearTextData($_POST['pass']);
		$res = User::login($login,$pass);
		header("Location: flat.html?action=userSales");
		exit();		  
	}
	else {
		header("Location: index.html");
		exit();	
	}	
}

function import($s) {
	if (!isset($_SESSION['user_id'])) {
		header("Location: index.html");
		exit();
	}
	$amount=-1;
	$num_updated_all = 0;		
	if (isset($_FILES["userfile"])) {
		if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
			$filename = $_FILES['userfile']['tmp_name'];			
			$rows =  file($filename);
			unset($rows[0]);
			$amount=0;			
			foreach ($rows as $row) {				
				$row = iconv("windows-1251", "utf-8", $row);
				$el = explode('|',$row);

				$date = $el[33];//дата объявления
				//if ($date<date('d.m.Y'))
				
				$type_deal = $el[0];//1 - продажа, 2 - аренда
				$status = ($el[0]==1) ? REALTY_STATUS_IMPORT_SALE : REALTY_STATUS_IMPORT_RENT;
				$is_city = $el[1];//1-город 2-республика
				if ($is_city != 1) continue;
				$rooms = $el[2];//11-комната 22-дом 27-участок 28-уч. под застройку 41-гараж
				if ($rooms>11) continue; 
				$phone = $el[6];
				$an_name = $el[7];
				$phone2 = $el[8];
				$name2 = $el[9];
				$street = $el[10];
				//echo "\n$street _\n";
				if(!preg_match("/[0-9]+[а-яА-Я]*$/", $street)) {
					//нет номера дома 
    				//continue; 
				}
				//echo "\n$street\n";
				preg_match("/[0-9]+[а-яА-Я]*$/", $street,$nums_tenement);
				
				$num = mb_strtolower($nums_tenement[0],'UTF-8');//strtolower($nums_tenement[0]);				 
				$street_name = str_replace($num,'',$street);
				$street_name = trim(str_replace('  ',' ',$street_name));
				$street_clear2 = str_replace('ул','',$street_name);
				$street_clear2 = str_replace('.','',$street_clear2);								
				$street_arr = explode(' ',$street_clear2);
				//находим самую длинную строку в названии улицы
				$i = 0;
				$max_len = '';
				$last_len = 0;
				if(count($street_arr)) {
					foreach ($street_arr as $c) {
						if (strlen($c)>$last_len) {
							$max_len = $c;
							$last_len = strlen($c); 
						}
						$i++;	
					}					
				}
				if ($max_len=='') continue;
				//echo "\nmax_len=$max_len";
				$row = Street::findBy("name LIKE '%$max_len%'");
				if (!$row) {					
					$street_id = Street::createStatic(array('name'=>$street_name,'city_id'=>0, 'status'=>0));
				}
				else {
					$street_id = $row['id'];
					$street_name = $row['name'];
				}					
				$storey = $el[12];
				$stores = $el[13];
				$tenement_type = $el[15];
				if ($tenement_type=='КИРП') $type_id=0;
				elseif ($tenement_type=='ПАН') $type_id=1;
				elseif ($tenement_type=='БЛОК') $type_id=3;
				elseif ($tenement_type=='ДЕР') $type_id=4;				
				else $type_id=2;
								
				$total_area = str_replace(',','.',$el[16]);
				//if ($total_area=='') continue;
				$living_area  = str_replace(',','.',$el[17]);
				$kitchen_area  = str_replace(',','.',$el[18]);
				if (!$kitchen_area) $kitchen_area=9;
				$description  = $el[26];
				$price  = str_replace(',','.',$el[27]);
				//if (!($price>0)) continue; 
				$price_k  = $el[28];//1-тыс. 2-млн. 5-руб/м 6-у.е./м
				
				if ($price_k==1) $price *= 1000;
				elseif ($price_k==2) $price *= 1000000;
				elseif ($price_k==5) $price *= $total_area;
				
				$haggle = $el[29];//торг/чистая продажа
				$date_end = $el[31];//срок сдачи дома				

				$row = Tenement::findBy("street_id='$street_id' AND number='$num'");
				if ($row) {
					$tenement_id=$row['id'];
				}
				else {
					$num = clearTextData($num);
					$options = array(
					'city_id'=>0,
					'street_id'=>$street_id,
					'number'=>$num,
					'type_id'=>clearTextData($type_id),
					'storeys'=>clearTextData($stores),
					'hot_water'=>1,
					'type_energy'=>0,
					'type_heating'=>0,					
					'user_id'=>$_SESSION['user_id'],
					'status'=>REALTY_STATUS_NEW
					);
					
					$address = urlencode('Йошкар-Ола, '.$street_name.', д.'.$num);
					$geo_url = 'http://psearch-maps.yandex.ru/1.x/?text='.$address.'&key='.YANDEX_KEY.'&format=json';					
					$json = file_get_contents($geo_url);
					$d = json_decode($json);
					if (isset($d->{'response'}->{'GeoObjectCollection'}->{'featureMember'}[0]->{'GeoObject'}->{'Point'}->{'pos'})) {
						$coords = explode(' ',$d->{'response'}->{'GeoObjectCollection'}->{'featureMember'}[0]->{'GeoObject'}->{'Point'}->{'pos'});					
						$options['lon'] = $coords[0];
						$options['lat'] = $coords[1];				
					}						
					$tenement_id = Tenement::addStatic($options);
				}
				if (!$tenement_id) continue;
				$num_updated = Flat::updateByCondition("tenement_id='$tenement_id' AND rooms='$rooms' AND storey='$storey' AND price='$price' AND user_id='{$_SESSION['user_id']}'",array('updated_on'=>date('Y-m-d H:i:s')));
				$num_updated_all += $num_updated;
				if ($num_updated>0) continue;				
				$contacts = ($phone2!='') ? $phone2.' '.$name2 : $phone.' '.$an_name;
				$options = array(
					'tenement_id'=>$tenement_id,
					'price'=>clearTextData($price),
					'rooms'=>clearTextData($rooms),
					'storey'=>clearTextData($storey),
					'total_area'=>clearTextData($total_area),
					'kitchen_area'=>clearTextData($kitchen_area),
					'living_area'=>clearTextData($living_area),
					'description'=>clearTextData($description,1000),
					'contacts'=>clearTextData($contacts),
					'user_id'=>$_SESSION['user_id'],
					'status'=>$status							
				);
				Flat::addStatic($options);
				$amount++;
			}
			$dest = IMPORT_PATH.$_SESSION['user_id'].'_'.date('d').'_'.date('m').'_'.date('H').'_'.date('i').'_'.date('s').'.txt';
			move_uploaded_file($filename,$dest);
		}
	}
	$s->assign("amount",$amount);
	$s->assign("num_updated_all",$num_updated_all);	
	$s->display("import.tpl");
}


function logout() {
	session_destroy();
	setcookie('hx','');
	header("Location: index.html");
	exit();
}

?>