<?php
class Html {	 		
		//lon=47.8782 - долгота
		//lat=56.635 - широта
	public static function getMapAddTenement($lon=47.8782, $lat=56.635, $scale=13,$place_mark=false,$height=400, $weight=800,$title='Отметьте объект на карте:',$msg='Вы отметили расположение дома!') {
		if ($lon=='') $lon = LON_YOLA;
		if ($lat=='') $lat = LAT_YOLA;
		$mark = $place_mark ? 'map.addOverlay(createObject("Placemark", new YMaps.GeoPoint('.$lon.','.$lat.'), "constructor#pmrdmPlacemark", "Дом"));' : '';		
		$script = '				
		<span class="flat_view" id="put_house_txt">'.$title.'</span><hr>
		<div id="YMapsID-3050" style="width:'.$weight.'px;height:'.$height.'px"></div>
		<script src="http://api-maps.yandex.ru/1.1/?key='.YANDEX_KEY.'&modules=pmap&wizard=constructor" type="text/javascript"></script>
<script type="text/javascript">
    YMaps.jQuery(window).load(function () {
        map = new YMaps.Map(YMaps.jQuery("#YMapsID-3050")[0]);
        map.setCenter(new YMaps.GeoPoint('.$lon.','.$lat.'), '.$scale.', YMaps.MapType.PMAP);
        map.addControl(new YMaps.Zoom());
        map.addControl(new YMaps.ToolBar());
        YMaps.MapType.PMAP.getName = function () { return "Народная"; };
        map.addControl(new YMaps.TypeControl([
            YMaps.MapType.MAP,
            YMaps.MapType.SATELLITE,
            YMaps.MapType.HYBRID,
            YMaps.MapType.PMAP
        ], [0, 1, 2, 3]));               
       createObject = function(type, point, style, description) {
            var allowObjects = ["Placemark", "Polyline", "Polygon"],
                index = YMaps.jQuery.inArray( type, allowObjects),
                constructor = allowObjects[(index == -1) ? 0 : index];
                description = description || "";            
            var object = new YMaps[constructor](point, {style: style, hasBalloon : !!description});
            object.description = description;            
            return object;
        }
        '.$mark.'
		mapClick = YMaps.Events.observe(map, map.Events.Click, function (map, mEvent) {                
                map.openBalloon(mEvent.getGeoPoint(), "'.$msg.'");
                $("#lat").val(mEvent.getGeoPoint().getY());
                $("#lon").val(mEvent.getGeoPoint().getX());
        });        		
    });
    </script>
';		
		return $script;
	}

	public static function getMap($lon, $lat, $scale=12) {
		return '
		<script src="http://api-maps.yandex.ru/1.1/?key='.YANDEX_KEY.'&modules=pmap&wizard=constructor" type="text/javascript"></script>
<script type="text/javascript">
    YMaps.jQuery(window).load(function () {
        var map = new YMaps.Map(YMaps.jQuery("#YMapsID-3050")[0]);
        map.setCenter(new YMaps.GeoPoint('.$lon.','.$lat.'), '.$scale.', YMaps.MapType.PMAP);
        map.addControl(new YMaps.Zoom());
        map.addControl(new YMaps.ToolBar());
        YMaps.MapType.PMAP.getName = function () { return "Народная"; };
        map.addControl(new YMaps.TypeControl([
            YMaps.MapType.MAP,
            YMaps.MapType.SATELLITE,
            YMaps.MapType.HYBRID,
            YMaps.MapType.PMAP
        ], [0, 1, 2, 3]));
       map.addOverlay(createObject("Placemark", new YMaps.GeoPoint('.$lon.','.$lat.'), "constructor#pmrdmPlacemark", "Дом"));        
       function createObject (type, point, style, description) {
            var allowObjects = ["Placemark", "Polyline", "Polygon"],
                index = YMaps.jQuery.inArray( type, allowObjects),
                constructor = allowObjects[(index == -1) ? 0 : index];
                description = description || "";            
            var object = new YMaps[constructor](point, {style: style, hasBalloon : !!description});
            object.description = description;            
            return object;
        }		
    });	
</script>
<div id="YMapsID-3050" style="width:450px;height:350px"></div>		
		';
	}

	public static function getBlock_($title,$content) {
		$html = '<table class="tborder" cellpadding="5" cellspacing="1" border="0" width="100%" height="100%" align="top"> 
		<tr><td class="theader">'.$title.'</td></tr>
		<tr><td class="tcontent" height="100%">
		'.$content.'
		</td></tr></table>';
		return $html;		
	}
	
	public static function getBlock($title,$content) {
		$html = '
		<div class="box2" >
  <div class="block-title">'.$title.'</div>
  <div class="box-internal">
  '.$content.'
  </div>  
  </div>';
		return $html;		
	}
	
	public static function pageTenementAdd(Tenement $tenement, $errors=null) {
		$tenement_html_form = self::getFormSpan($tenement->getProperties(),0,'type_id',$errors[TENEMENT]); 								
		$block_content = self::getTenementFormAdd($tenement_html_form);				
		$block_html = self::getBlock('Добавление дома',$block_content);				
		return $block_html; 
	}
	
	public static function pageFlatAdd(Tenement $tenement, Flat $flat, $errors=null) {
		$err = isset($errors[TENEMENT]) ? $errors[TENEMENT] : null;
		$tenement_html_form = self::getFormSpan(Tenement::$_properties,0,'type_id',$err);
		//Если это юзер и тариф позволяет не указывать адрес
		if (isset($_SESSION['user_tariff_id']) && $_SESSION['user_tariff_id']==1) {		
			Flat::$_properties['show_address']['on_form']=1;
		}
		$flat_html_form = self::getForm2Col(Flat::$_properties,15,$errors[FLAT],'flat__',12);
		if (isset($errors['captcha']['is_error']))
			$captcha_error = '<span class="require">Неверный проверочный код</span>';
        else
            $captcha_error = '';
		$block_content = self::getFormFlatAdd($tenement_html_form,$flat_html_form, $captcha_error);
		$error_msg = ($errors && count($errors)>1 && isset($errors[FLAT]['val']['contacts']) || $errors && count($errors)>0 && !isset($errors[FLAT]['val']['contacts'])) ? "<p class='require'>Ошибка при заполнении формы. Проверьте поля, выделенные красным цветом.</p>" : '';				
		$block_html = self::getBlock('Добавление квартиры: шаг 1 из 2','<p class="base_text">Вводите название улицы и выбирайте нужную из выпадающего списка.</p>'.$error_msg.$block_content);				
		return $block_html; 
	}
	
	public static function pageCommercialAdd($errors=null) {
		$err = isset($errors[COMMERCIAL]) ? $errors[COMMERCIAL] : null;		
		$html_form = self::getForm(Commercial::$_properties,1,$errors,'');
		if (isset($errors['captcha']['is_error']))
			$captcha_error = '<span class="require">Неверный проверочный код</span>';
        else
            $captcha_error = '';
		$block_content = self::getFormCommercialAdd($html_form, $captcha_error);
		$block_html = self::getBlock('Добавление коммерческой недвижимости',''.$block_content);				
		return $block_html; 
	}
	
	public static function pageHouseAdd($errors=null) {
		$err = isset($errors[HOUSE]) ? $errors[HOUSE] : null;		
		$html_form = self::getForm(House::$_properties,1,$errors,'');
		if (isset($errors['captcha']['is_error']))
			$captcha_error = '<span class="require">Неверный проверочный код</span>';
		else
            $captcha_error = '';
		$block_content = self::getFormHouseAdd($html_form, $captcha_error);
		$block_html = self::getBlock('Добавление дома/коттеджа','<p class="base_text">Вводите название населенного пункта и улицы и выбирайте нужную из выпадающего списка.</p>'.$block_content);				
		return $block_html; 
	}

	public static function pageLandAdd($errors=null) {
		$err = isset($errors[LAND]) ? $errors[LAND] : null;		
		$html_form = self::getForm(Land::$_properties,1,$errors,'');
		if (isset($errors['captcha']['is_error']))
			$captcha_error = '<span class="require">Неверный проверочный код</span>';
		else
            $captcha_error = '';
		$block_content = self::getFormLandAdd($html_form, $captcha_error);
		$block_html = self::getBlock('Добавление земельного участка','<p class="base_text">Вводите название населенного пункта и выбирайте нужный из выпадающего списка.</p>'.$block_content);				
		return $block_html; 
	}

	public static function pageCompanyEdit($company,$errors=null) {
		//Если это юзер и тариф позволяет не указывать адрес
		if (isset($_SESSION['user_tariff_id']) && $_SESSION['user_tariff_id']==1) {		
			
		}		
		$defaults = array();
		$defaults['val'] = $company;
		if (isset($errors)) $defaults = $errors; 
		$html_form = self::getForm(Company::$_properties,0,$defaults);
		$photos_html = self::getCompanyPhotosEdit($company);
		$company_url = ($company['domain']!='') ? '<p>Ваш сайт: <a href="http://'.$company['domain'].'.'.DOMAIN.'" target="_blank">'.$company['domain'].'.'.DOMAIN.'</a></p>':'';				
		$block_content = self::getCompanyFormEdit($html_form.$company_url,$photos_html,$company);				
		$block_html = self::getBlock('Редактирование профайла компании',$block_content);
		return $block_html; 
	}
	
	
	public static function pageFlatEdit(Flat $flat,$errors) {
		//Если это юзер и тариф позволяет не указывать адрес
		if (isset($_SESSION['user_tariff_id']) || isset($_SESSION['admin'])) {		
			Flat::$_properties['show_address']['on_form']=1;
		}		
		$defaults = array();
		if (isset($errors)) $defaults = $errors;
		else $defaults['val'] = $flat->getVals();
		$flat_html_form = self::getForm(Flat::$_properties,0,$defaults);
		$photos_html = self::getFlatPhotosEdit($flat);				
		$block_content = self::getFlatFormEdit($flat_html_form,$photos_html);
		$district = (!is_null($flat->district)) ? " (мик-н {$flat->district})":'';	
		$address = $flat->city.$district.', '.$flat->street.', д.'.$flat->tnum;
		$block_html = self::getBlock('Редактирование квартиры: '.$address,$block_content);
		return $block_html; 
	}
	
	public static function pageFlatView(Flat $flat,$act="") {		
		$properties_val = $flat->getPropertiesVal();
		$vals_html = Html::getViewRealty($properties_val);
		if ($flat->user_id>0) {
			$val = $flat->company_name;
			$url_logo=Company::getLogoURL($flat->company_id);
			
			if($flat->tariff_id>1){
				$val = "<a href='http://".$flat->domain.".".$_SERVER['HTTP_HOST']."' target='_target'><img src='".$url_logo."' class='comp_logo'  title='$val'  alt='$val'></img></a>";
			}
		    elseif ($flat->domain!=''){
			   //$val = "<a href='http://".$flat->domain.".".$_SERVER['HTTP_HOST']."' target='_target'>$val</a>";
			   //$val = "<a href='http://".$flat->domain.".".$_SERVER['HTTP_HOST']."' target='_target'>$val</a>";
			}
						
			$vals_html.='<div class="flat_view">Автор: <b>'.$val.'</b></div>';
		}		
		$tenement_properties_val = $flat->getTenementInfo(array(
			'type_id','birthday','type_energy','type_heating','porches','height'));		
		$tenement_vals_html = Html::getViewRealty($tenement_properties_val);
		$url = ($flat->show_address) ? Html::getUrl('tenement','view',$flat->tenement_id) : '#';		 
		$tenement_photo_html = ($flat->tenement_photo!='') ? "		
		<a href='".$url."'><img src='/".PHOTOS_WEBPATH.TENEMENT."/".$flat->tenement_id."/".$flat->tenement_photo."_prev' class='border'></a><div>" : '';
		$tenement_photo_html .= ($flat->show_address) ? "<a href='".Html::getUrl('tenement','view',$flat->tenement_id)."'>Подробнее</a>" : 'Расположение на карте показано приблизительное';
		$district = (!is_null($flat->district)) ? " (мик-н {$flat->district})":'';	
		$address = $flat->city;	
		if ($flat->street && $flat->street!='') $address .= ', '.$flat->street;
		if ($flat->tnum!='' && $flat->show_address) $address .= ', д.'.$flat->tnum;
		//$address = $flat->city.$district.', '.$flat->street.', д.'.$flat->tnum;		
		$photos = $flat->getPhotos();
		$photo_flat_path = $flat->getPhotoWebPath(); 
		$photo_gallery_html = Html::getPhotosGallery($photos,$photo_flat_path);
		$date = formatDateExact($flat->created_on);
		$date_up = formatDateExact($flat->updated_on);
		$counter_html = "
		<div class='flat_view'>Обновлено: <b>$date_up</b></div>
		<div class='flat_view'>Размещено: <b>$date</b></div>
		<div class='flat_view'>Просмотров в списке: <b>$flat->quick_views</b></div>
		<div class='flat_view'>Просмотров подробно: <b>$flat->counter_views</b></div>
		<p><b>Пожалуйста сообщите риэлтору что Вы нашли объявление на сайте mari12.ru. Так Вы поможете развитию портала!</b></p>
		";
		$html_page = $vals_html.$photo_gallery_html.$counter_html;
		$is_admin = 0;
		$id = intval($_REQUEST['id']);
		$url_edit = false;
		$url_apply = false;
		$url_sold = false;
		$url_delete = false;
		
		if ($flat->status==REALTY_STATUS_NEW || $flat->status==REALTY_STATUS_IMPORT_SALE) {
			$status = REALTY_STATUS_APPLY;
		}
		else {
			$status = REALTY_STATUS_RENT_APPLY;
		}
		
		if (isset($_SESSION['last_flat_id']) && $id==$_SESSION['last_flat_id'] && !isset($_SESSION['admin']) && ($flat->status==REALTY_STATUS_NEW || $flat->status==REALTY_STATUS_RENT_NEW)) {					
			$url_edit = Html::getUrl('flat','edit',$flat->id);
			$url_apply = Html::getUrl('flat','apply',$flat->id,'&status='.$status);
		}		
		if (isset($_SESSION['user_id']) && $_SESSION['user_id']==$flat->user_id || (isset($_SESSION['last_flat_id']) && $id==$_SESSION['last_flat_id'])) {		
			$url_edit = Html::getUrl('flat','edit',$flat->id);
		}
		if (!$url_apply && isset($_SESSION['user_id']) && $_SESSION['user_id']==$flat->user_id 
				&& ($flat->tenement_status!=REALTY_STATUS_NEW || $flat->tnum!='')) {		
			$url_apply = Html::getUrl('flat','apply',$flat->id,'&status='.$status);
		}
		if (isset($_SESSION['user_id']) && $_SESSION['user_id']==$flat->user_id 
				&& $flat->tenement_status==REALTY_STATUS_ACTIVE
				&& $flat->status != REALTY_STATUS_IMPORT_SALE 
				&& $flat->status != REALTY_STATUS_IMPORT_RENT  
				) {		
			$url_apply = '';
			$activated_msg = " <b>Ваше объявление активировано, т.к. Вы проверенная компания.</b>";
		}
		else {
			$activated_msg = '';
		}
		if (isset($_SESSION['admin'])) {
			$is_admin = 1;
			$url_edit = Html::getUrl('flat','edit',$flat->id);
			$url_sold = Html::getUrl('flat','sold',$flat->id);
			$status = -1;
			if ($flat->status==REALTY_STATUS_APPLY) {
				$status = REALTY_STATUS_SALE;			
			}
			elseif ($flat->status==REALTY_STATUS_RENT_APPLY) {
				$status = REALTY_STATUS_RENT;
			}
			if ($status>-1) $url_approve = Html::getUrl('flat','approve',$flat->id,'&status='.$status);				
			$url_delete  = Html::getUrl('flat','delete',$flat->id);		
		}		
		
		if ($url_edit)
	 		$html_page .= '<input type="button" onclick="location=\''.$url_edit.'\'" value="Редактировать (добавить фотографии)" class="btn btn-primary">';
	
		if ($url_apply)
			$html_page .= '&nbsp;<input type="button" onclick="location=\''.$url_apply.'\'" value="Отправить объявление на проверку" class="btn btn-success">';
		 
		if ($is_admin && isset($url_approve))
			$html_page .= '&nbsp;<input type="button" onclick="location=\''.$url_approve.'\'" value="Активировать объявление" class="btn btn-success">';
		 
		if ($is_admin && $url_delete)  
			$html_page .= '&nbsp;<input type="button" onclick="location=\''.$url_delete.'\'" value="Удалить объявление" class="btn btn-danger">';
		 
		if ($is_admin && $url_sold)
			$html_page .= '&nbsp;<input type="button" onclick="location=\''.$url_sold.'\'" value="Продано" class="btn btn-info">';  
	 
		$res['block_flat_html'] = Html::getBlock($flat->rooms.'-комнатная квартира '.$act.' '.$address,$html_page.$activated_msg);
		$map = ''; 
		if ($flat->lon>0 && $flat->lat>0) {
			if (!$flat->show_address) {
				$flat->lon+=(rand(1,3)/2000);
				$flat->lat+=(rand(1,3)/2000);
			}
			$map = self::getMap($flat->lon,$flat->lat);
		}
		$tenement_control = '';
		if (isset($_SESSION['user_id']) && $flat->tenement_status==REALTY_STATUS_NEW) {
			$tenement_control = '<br><input type="button" onclick="location=\'/tenement.html?action=edit&id='.$flat->tenement_id.'\'" value="Редактировать (указать номер)" class="btn btn-primary">';
		}
		$res['block_tenement_html'] = Html::getBlock('Дом: '.$address,$tenement_vals_html.$tenement_photo_html.$map.$tenement_control);
		return $res;		 
	}
	
	public static function pageTenementEdit(Tenement $tenement) {		
		$defaults = array();
		$defaults['val'] = $tenement->getVals();			
		$tenement_html_form = self::getForm(Tenement::$_properties,0,$defaults);
		$photos_html = self::getRealtyPhotosEdit($tenement);				
		$block_content = self::getTenementFormEdit($tenement_html_form,$photos_html,$tenement);
		$block_html = self::getBlock('Редактирование дома',$block_content);
		return $block_html; 
	}
	
	public static function pageTenementView(Tenement $tenement) {		
		$properties_val = $tenement->getPropertiesVal();
		$vals_html = Html::getViewRealty($properties_val);		
		$district = (!is_null($tenement->district)) ? " (мик-н {$tenement->district})":'';
		$address = $tenement->city;	
		if ($tenement->street!='') $address .= ', '.$tenement->street;
		if ($tenement->number!='') $address .= ', д.'.$tenement->number;		
		$photos = $tenement->getPhotos();
		$photo_tenement_path = $tenement->getPhotoWebPath(); 
		$photo_gallery_html = Html::getPhotosGallery($photos,$photo_tenement_path,$address);
		//openstreetmap
		//<iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://www.openstreetmap.org/export/embed.html?bbox=47.89341,56.6364,47.90127,56.64135&amp;layer=mapnik&amp;marker=56.63882,47.89566" style="border: 1px solid black"></iframe><br /><small><a href="http://www.openstreetmap.org/?lat=56.638875&amp;lon=47.89734&amp;zoom=16&amp;layers=M&amp;mlat=56.63882&amp;mlon=47.89566" target="_blank">Посмотреть более крупную карту</a></small>		
		//$map = ($tenement->lat>0 && $tenement->lon>0) ? '<iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://www.openstreetmap.org/export/embed.html?bbox='.($tenement->lon-0.00225).','.($tenement->lat-0.00242).','.($tenement->lon+0.00561).','.($tenement->lat+0.00253).'&amp;layer=mapnik&amp;marker='.$tenement->lat.','.$tenement->lon.'" style="border: 1px solid black"></iframe><br /><small><a href="http://www.openstreetmap.org/?lat='.$tenement->lat.'&amp;lon='.$tenement->lon.'&amp;zoom=16&amp;layers=M&amp;mlat='.$tenement->lat.'&amp;mlon='.$tenement->lon.'" target="_blank">Посмотреть более крупную карту</a></small>' : '';
		//lon=47 - долгота
		//lat=56 - широта
		$map = ($tenement->lat>0 && $tenement->lon>0) ? '
<script src="http://api-maps.yandex.ru/1.1/?key='.YANDEX_KEY.'&modules=pmap&wizard=constructor" type="text/javascript"></script>
<script type="text/javascript">
    YMaps.jQuery(window).load(function () {
        var map = new YMaps.Map(YMaps.jQuery("#YMapsID-3050")[0]);
        map.setCenter(new YMaps.GeoPoint('.$tenement->lon.','.$tenement->lat.'), 12, YMaps.MapType.PMAP);
        map.addControl(new YMaps.Zoom());
        map.addControl(new YMaps.ToolBar());
        YMaps.MapType.PMAP.getName = function () { return "Народная"; };
        map.addControl(new YMaps.TypeControl([
            YMaps.MapType.MAP,
            YMaps.MapType.SATELLITE,
            YMaps.MapType.HYBRID,
            YMaps.MapType.PMAP
        ], [0, 1, 2, 3]));
       map.addOverlay(createObject("Placemark", new YMaps.GeoPoint('.$tenement->lon.','.$tenement->lat.'), "constructor#pmrdmPlacemark", "Дом"));        
       function createObject (type, point, style, description) {
            var allowObjects = ["Placemark", "Polyline", "Polygon"],
                index = YMaps.jQuery.inArray( type, allowObjects),
                constructor = allowObjects[(index == -1) ? 0 : index];
                description = description || "";            
            var object = new YMaps[constructor](point, {style: style, hasBalloon : !!description});
            object.description = description;            
            return object;
        }		
    });	
</script>
<div id="YMapsID-3050" style="width:450px;height:350px"></div>
		'
		 : '';
		if (isset($_SESSION['admin']) || ($tenement->status==REALTY_STATUS_NEW 
			&& (isset($_SESSION['user_id']) && $tenement->user_id==$_SESSION['user_id']))) {
			
			$url_edit = Html::getUrl('tenement','edit',$tenement->id);
			$url_edit = '<input type="button" onclick="location=\''.$url_edit.'\'" value="Редактировать" class="btn btn-primary">';	
		}
		else $url_edit = '';
		
		if ($tenement->status==REALTY_STATUS_NEW && isset($_SESSION['admin'])) {				
			$url_approve = Html::getUrl('tenement','approve',$tenement->id);
			$url_approve = '<input type="button" onclick="location=\''.$url_approve.'\'" value="Утвердить" class="btn btn-success">';
		}
		else $url_approve = '';
		
		$struct_html = '<table>
		<tr>
		<td style="text-align:left; vertical-align:top;">'.$vals_html.'</td>
		<td rowspan=2 style="vertical-align:top;">'.$photo_gallery_html.'
		<div id="div_history" class="base_text"></div>
		</td>
		</tr>
		<tr>
		<td style="vertical-align:top;">'.$map.'</td>
		</tr>
		<tr><td><br>
			'.$url_edit.$url_approve.'			
		</td></tr>
		</table>';
		$block_html = Html::getBlock('Информация о доме: '.$address,$struct_html);
		return $block_html; 
	}
	
	public static function getTenementFormAdd($tenement_html_form) {
		$html = '<form method="post" enctype="application/x-www-form-urlencoded" name="EditObject">
		<fieldset><legend>Описание дома</legend>   
		   <table><tr><td class="tcontent">
		   <div class="form_convert" id="tenement_desc">
		   '.$tenement_html_form.'
		   </div>
		   </td><td class="tcontent" style="padding-left:20px;">'.self::getMapAddTenement().'</td></tr></table> 
			<span id="spanButtonPlaceholder"></span>		   
		   <div id="divFileProgressContainerPhoto" style="height: 75px;"></div>
		   <div id="thumbnails1"></div>      
		</fieldset>
		<div align=center>
		   <br>
		   <input type="hidden" name="action" value="add">
		   <input type="submit" value="Сохранить">
		   </div>		   		  
		</form>		
		';
		return $html;		
	}	

	public static function getFormFlatAdd($tenement_html_form,$flat_html_form, $captcha_error = '') {
		$html = '<form method="post" enctype="application/x-www-form-urlencoded" name="EditObject">
		<fieldset><legend>Описание дома</legend>
		   <table><tr><td class="tcontent">
		   <div class="form_convert" id="tenement_desc">
		   '.$tenement_html_form.'
		   </div>
		   </td><td class="tcontent" style="padding-left:20px;">'.self::getMapAddTenement().'</td></tr></table> 
			<span id="spanButtonPlaceholderTenement"></span>		   
		   <div id="divFileProgressContainerTenement" style="height: 20px;"></div>
		   <div id="thumbnailsTenement"></div>
		   <div id="div_history"></div>     
		</fieldset>
		<fieldset><legend>Описание квартиры</legend>
			'.$flat_html_form.
            '<div style="clear:both;"><div class="form_convert">'.
                HtmlCompany::getAddByAgencyBlock().
            '</div></div>
            <div style="clear:both;">
			<div class="form_convert">
			<div class="field">
				<label for="captcha_code" '.
                (empty($captcha_error) ? '' : 'class="require"')
                .'">Введите код (регистр не важен):</label>
				<img id="captcha" src="/libs/securimage/securimage_show.php" style="vertical-align:middle;" />
				<input type="text" name="captcha_code" size="10" maxlength="6" tabindex="30"/>
				<a href="#" onclick="document.getElementById(\'captcha\').src = \'/libs/securimage/securimage_show.php?\' + Math.random(); return false;">[Другой код]</a>								
				</div>'.
				$captcha_error.
		   '</div>
		   <span id="spanButtonPlaceholderFlat"></span>		   
		   <div id="divFileProgressContainerFlat" style="height: 20px;"></div>
		   <div id="thumbnailsFlat"></div>
		   <div align="center" style="clear:both;">
		   <br>
		   <input type="hidden" name="action" value="add">
		   <input type="submit" value=" Предварительный просмотр " tabindex="40" class="btn btn-primary" style="margin-left: auto; margin-right: auto;">
		   </div>							
			</div>
		</fieldset>
		</form>		
		';
		return $html;		
	}

	public static function getCompanyFormEdit($html_form, $photos_html,$company) {
		if (is_file(PHOTOS_PATH.LOGO."/".$company['id'])) 
			$logo_html = "<img src='".PHOTOS_WEBPATH.LOGO."/".$company['id']."'>";		
		$logo_html = 'Логотип:
				<span id="spanButtonPlaceholderLogo"></span>		   
			   <div id="divFileProgressContainerLogo" style="height: 10px;"></div>
			   <div id="thumbnails'.LOGO.'">'.$logo_html.'</div>'; 
		$lon = ($company['lon']>0) ? $company['lon'] : LON_YOLA;
		$lat = ($company['lat']>0) ? $company['lat'] : LAT_YOLA;
		$place_mark = ($company['lon']>0) ? true : false; 
		$html = '<form method="post" enctype="application/x-www-form-urlencoded" name="EditObject">				
		<fieldset style="width:100%"><legend>Описание компании</legend>
		    <table>
		    <tr><td>	
			<div class="form_convert">			
			'.$html_form.'
			</div>
			</td>
			<td style="padding-left:30px; vertical-align:top;">
			'.$logo_html.'
			<hr>
			'.self::getMapAddTenement($lon,$lat,14,$place_mark,300,600,'Отметье Ваш офис на карте:', 'Вы отметили расположение офиса!').'			
			</td>
			</table>		
		</fieldset>
		<fieldset><legend>Фотографии компании</legend>
		<span id="spanButtonPlaceholderFlat"></span>		   
		   <div id="divFileProgressContainerFlat" style="height: 10px;"></div>
		   <div id="thumbnails'.COMPANY.'">'.$photos_html.'</div>
		</fieldset>						
		<div align=center>
		   <br>
		   <input type="hidden" name="action" value="edit">
		   <input type="submit" value="Сохранить" class="btn btn-success">
		   </div>		   		  
		</form>		
		';
		return $html;		
	}
	
	
	public static function getFlatFormEdit($flat_html_form, $photos_html) {
		$html = '<form method="post" enctype="application/x-www-form-urlencoded" name="EditObject">		
		<fieldset style="width:100%"><legend>Описание квартиры</legend>
			<div class="form_convert">			
			'.$flat_html_form.'
			</div>			
		</fieldset>
		<fieldset><legend>Фотографии квартиры</legend>
		<span id="spanButtonPlaceholderFlat"></span>		   
		   <div id="divFileProgressContainerFlat" style="height: 10px;"></div>
		   <div id="thumbnails2">'.$photos_html.'</div>
		</fieldset>
		<div align=center>
		   <br>
		   <input type="hidden" name="action" value="edit">
		   <input type="submit" value="Сохранить" class="btn btn-success">
		   </div>		   		  
		</form>		
		';
		return $html;		
	}
	
	public static function getTenementFormEdit($tenement_html_form, $photos_html,$tenement) {
		$html = '<form method="post" enctype="application/x-www-form-urlencoded" name="EditObject">		
		<fieldset><legend>Описание дома</legend>
		 <table><tr><td class="tcontent">
		   <div class="form_convert" id="tenement_desc">
			'.$tenement_html_form.'
			</div>
		   </td><td class="tcontent">'.self::getMapAddTenement($tenement->lon,$tenement->lat,15,true).'</td></tr></table>		
		</fieldset>
		<fieldset><legend>Фотографии дома</legend>	   
			<span id="spanButtonPlaceholder" style="border=1"></span><br>		   
		   <div id="divFileProgressContainerPhoto" style="height: 10px;"></div>
		   <div id="thumbnails1">'.$photos_html.'</div>
		</fieldset>
		<div align=center>
		   <br>
		   <input type="hidden" name="action" value="edit">
		   <input type="submit" value="Сохранить" class="btn btn-success">
		   </div>		   		  
		</form>
		<script>
		YMaps.jQuery(window).load(function () {
			if ($("#lon").val()>0 && $("#lat").val()>0) {
				map.addOverlay(createObject("Placemark", new YMaps.GeoPoint($("#lon").val(),$("#lat").val()), "constructor#pmrdmPlacemark", "Дом"));
			}
		});		
		</script>
		';
		return $html;		
	}
	
	public static function getFormCommercialAdd($html_form, $captcha_error = '') {
		$html = '<form method="post" enctype="application/x-www-form-urlencoded" name="EditObject">
        <fieldset><legend>Описание коммерческой недвижимости</legend>
		   <table><tr><td class="tcontent">
		   <div class="form_convert" id="desc">		   
		   <div class="field"><label for="type_deal">Тип операции:</label>
			Продаю <input type="radio" name="type_deal" value="1" checked onclick="$(\'#div_price label\').html(\'Цена (руб.)<font class=require>*</font>\')">&nbsp; &nbsp; &nbsp; 
			Сдаю <input type="radio" name="type_deal" value="2" onclick="$(\'#div_price label\').html(\'Цена (руб./мес)<font class=require>*</font>\')"></div>
		    '.$html_form.
		    HtmlCompany::getAddByAgencyBlock().
		    '<div class="field">
				<label for="captcha_code" '.
                (empty($captcha_error) ? '' : 'class="require"').
                '>Введите код (регистр не важен):</label>
				<input type="text" name="captcha_code" size="10" maxlength="6" tabindex="30"/>
				<a href="#" onclick="document.getElementById(\'captcha\').src = \'/libs/securimage/securimage_show.php?\' + Math.random(); return false;">[Другой код]</a>				
				<img id="captcha" src="/libs/securimage/securimage_show.php" />
				</div>'.
			$captcha_error.	
			'</div>
		   </div>
		   </td><td class="tcontent" style="padding-left:20px;">'.self::getMapAddTenement(LON_YOLA,LAT_YOLA,11,false,600).'</td></tr></table> 
			<span id="spanButtonPlaceholderTenement"></span>		   
		   <div id="divFileProgressContainerTenement" style="height: 20px;"></div>
		   <div id="thumbnailsTenement"></div>		        
		</fieldset>
		<div align=center>
		   <br>
		   <input type="hidden" name="action" value="add">
		   <input type="submit" value=" Предварительный просмотр " tabindex="40" class="btn btn-primary">
		   </div>		   		  
		</form>
		<div id="div_history" class="base_text"></div>
		';
		return $html;		
	}
	
	public static function getFormHouseAdd($html_form, $captcha_error = '') {
		$html = '<form method="post" enctype="application/x-www-form-urlencoded" name="EditObject">
		<fieldset><legend>Описание дома</legend>   
		   <table><tr><td class="tcontent">
		   <div class="form_convert" id="desc">
		   '.$html_form.
           HtmlCompany::getAddByAgencyBlock().
           '<div class="field">
				<label for="captcha_code" '.
                (empty($captcha_error) ? '' : 'class="require"').
                '>Введите код (регистр не важен):</label>
				<input type="text" name="captcha_code" size="10" maxlength="6" tabindex="30"/>
				<a href="#" onclick="document.getElementById(\'captcha\').src = \'/libs/securimage/securimage_show.php?\' + Math.random(); return false;">[Другой код]</a>				
				<img id="captcha" src="/libs/securimage/securimage_show.php" />
				</div>'.
            $captcha_error.
           '</div>
		   </div>
		   </td><td class="tcontent" style="padding-left:20px;">'.self::getMapAddTenement(LON_YOLA,LAT_YOLA,11,false,600).'</td></tr></table> 
			<span id="spanButtonPlaceholderTenement"></span>		   
		   <div id="divFileProgressContainerTenement" style="height: 20px;"></div>
		   <div id="thumbnailsTenement"></div>		        
		</fieldset>
		<div align=center>
		   <br>
		   <input type="hidden" name="action" value="add">
		   <input type="submit" value=" Предварительный просмотр " tabindex="40" class="btn btn-primary">
		   </div>		   		  
		</form>
		<div id="div_history" class="base_text"></div>
		';
		return $html;		
	}
	
	public static function getFormLandAdd($html_form, $captcha_error = '') {
		$html = '<form method="post" enctype="application/x-www-form-urlencoded" name="EditObject">
		<fieldset><legend>Описание земельного участка</legend>
		   <table><tr><td class="tcontent">
		   <div class="form_convert" id="desc">
		   '.$html_form.
           HtmlCompany::getAddByAgencyBlock().
           '<div class="field">
				<label for="captcha_code" '.
                (empty($captcha_error) ? '' : 'class="require"').
                '>Введите код (регистр не важен):</label>
				<input type="text" name="captcha_code" size="10" maxlength="6" tabindex="30"/>
				<a href="#" onclick="document.getElementById(\'captcha\').src = \'/libs/securimage/securimage_show.php?\' + Math.random(); return false;">[Другой код]</a>				
				<img id="captcha" src="/libs/securimage/securimage_show.php" />
				</div>'.
            $captcha_error.
           '</div>
		   </div>
		   </td><td class="tcontent" style="padding-left:20px;">'.self::getMapAddTenement(LON_YOLA,LAT_YOLA,11,false,600).'</td></tr></table> 
			<span id="spanButtonPlaceholderTenement"></span>		   
		   <div id="divFileProgressContainerTenement" style="height: 20px;"></div>
		   <div id="thumbnailsTenement"></div>		        
		</fieldset>
		<div align=center>
		   <br>
		   <input type="hidden" name="action" value="add">
		   <input type="submit" value=" Предварительный просмотр " tabindex="40" class="btn btn-primary">
		   </div>		   		  
		</form>
		<div id="div_history" class="base_text"></div>
		';
		return $html;		
	}
	
	public static function getForm($properties,$tabind=0,$errors=null,$prefix=null) {
		$html = '';
		foreach ($properties as $name => $prop) {			
			if (isset($prop['on_form']) && $prop['on_form']==0) continue;
			if (isset($prop['session']) && $prop['session']==0 && isset($_SESSION['user_id'])) continue;
			if (!is_null($prefix)) $prop['name']=$prefix.$prop['name'];
			$params = isset($errors) ? array('val'=>@$errors['val'][$name],'msg'=>@$errors['msg'][$name]) : array('val'=>'','msg'=>'');
			$html .= self::getElementForm($prop,$tabind,$params);
			$tabind++;
		}		
		return $html;	
	}
	
	public static function getForm2Col($properties,$tabind=0,$errors=null,$prefix=null,$amount_first) {
		
		$html = '<div style="float:left; padding-right:100px;">
		<div class="form_convert">			
			<div class="field"><label for="type_deal">Тип операции:</label>
			Продаю <input type="radio" name="type_deal" value="1" checked onclick="$(\'#div_flat__price label\').html(\'Цена (руб.)<font class=require>*</font>\')">&nbsp; &nbsp; &nbsp; 
			Сдаю <input type="radio" name="type_deal" value="2" onclick="$(\'#div_flat__price label\').html(\'Цена (руб./мес)<font class=require>*</font>\')"></div>
		';		
		$i = 1;		
		foreach ($properties as $name => $prop) {			
			if (isset($prop['on_form']) && $prop['on_form']==0) continue;			
			if ($i>$amount_first) break;						
			if (!is_null($prefix)) $prop['name']=$prefix.$prop['name'];
			$params = isset($errors) ? array('val'=>@$errors['val'][$name],'msg'=>@$errors['msg'][$name]) : array('val'=>'','msg'=>'');						
			$html .= self::getElementForm($prop,$tabind,$params);
			$tabind++;
			$i++;			
		}
		$html .= '</div></div><div><div class="form_convert">';
		$i = 1;
		foreach ($properties as $name => $prop) {			
			if (isset($prop['on_form']) && $prop['on_form']==0) continue;
			if (isset($prop['session']) && $prop['session']==0 && isset($_SESSION['user_id'])) continue;
			if ($i<=$amount_first) {
				$i++;
				continue;			
			}			
			if (!is_null($prefix)) $prop['name']=$prefix.$prop['name'];
			$params = isset($errors) ? array('val'=>@$errors['val'][$name],'msg'=>@$errors['msg'][$name]) : array('val'=>'','msg'=>'');						
			$html .= self::getElementForm($prop,$tabind,$params);
			$tabind++;
			$i++;			
		}
		$html .= '</div></div>';
		return $html;	
	}
	
	public static function getFormSpan($properties,$tabind=0,$name_after,$errors=null) {
		$html = '<span>Поля помеченные <font class="require">*</font> обязательны для заполнения</span><hr>';		
		foreach ($properties as $name => $prop) {
			if ($name==$name_after) {
				$html .= '<span id="span_'.$name.'" border=1>';
			}
			if (isset($errors['msg']) && !isset($errors['val'][$name])) $errors['val'][$name]='';			
			$html .= self::getElementForm($prop,$tabind,array('msg'=>@$errors['msg'][$name],'val'=>@$errors['val'][$name]));
			$tabind++;			
		}
		$html .= '</span>';
		return $html;	
	}
	
	public static function getElementForm($prop,$tabind=1,$error=null) {		
		$method = '_'.$prop['tag'];
		if ($prop['tag']!='hidden') {			
			$name = $prop['name'];			
			if ($error && $error['msg']!='') {
				//$err_html = '<div class="require">'.$error['msg'].'</div>';
				$err_html = '<br><span class="require">'.$error['msg'].'</span>';
				$class = 'class="require"';
			}
			else {
				$class = '';
				$err_html = ''; 
			}
			$required = (isset($prop['required'])) ? '<font class="require">*</font>:' : ':';
			$unit = (!isset($prop['unit'])) ? '' : ' ('.$prop['unit'].')';
			if (isset($error) && isset($error['val'])) $error['val'] = htmlspecialchars($error['val'], ENT_QUOTES, 'UTF-8');
            $field_class = (empty($prop['not_field'])) ? 'field' : 'not_field';
			$html = '<div class="'.$field_class.'" id="div_'.$name.'"><label for="'.$name.'" '.$class.'>'.$prop['label'].$unit.$required.'</label>'
			.self::$method($prop,$tabind,$error)
			.$err_html.'
		   </div>';
		}
		else {
			$html = self::$method($prop,1,$error); 
		}
		return $html;
	}
	
	protected static function _text($prop,$tabindex=1,$error=null) {
		$name = $prop['name'];				
		if (isset($prop['default']) && !isset($error['val'])) {
			$error['val'] = isset($prop['default']) ? $prop['default'] : ''; 
		}
		$html = '<input name="'.$name.'" id="'.$name.'" type="text" style="width:180;" maxlength="255" value="'.$error['val'].'" tabindex="'.$tabindex.'">';
		return $html;
	}

	protected static function _hidden($prop,$tabindex=1,$error=null) {
		$name = $prop['name'];
		$html = '<input name="'.$name.'" id="'.$name.'" type="hidden" value="'.$error['val'].'">';
		return $html;
	}
	
	
	protected static function _select($prop,$tabindex=1,$error=null) {
		$name = $prop['name'];		
		$options = '';
		foreach ($prop['vals'] as $k=>$val) {
			$selected = ($error['val'] != $k)?'':'selected';
			$options .= '<option value="'.$k.'" '.$selected.'>'.$val.'</option>';
		}
		$html = '<select name="'.$name.'" id="'.$name.'" style="width:180;" tabindex="'.$tabindex.'">
		   '.$options.'</select>';
		return $html;
	}	
	
	protected static function _textarea($prop,$tabindex=1,$error=null) {
		$name = $prop['name'];		
		$html = '<textarea name="'.$name.'" id="'.$name.'" style="width:270px; height:140px;" tabindex="'.$tabindex.'">'.$error['val'].'</textarea>';
		return $html;
	}
	
	protected static function _checkbox($prop,$tabindex=1,$error=null) {
		$name = $prop['name'];
		if ((!isset($error['val']) || $error['val']=='') && isset($prop['default'])) {			
			$error['val'] = $prop['default'];							
		}
		$checked = ($error['val']) ? 'checked' : '';
		$html = '<input type="checkbox" name="'.$name.'" id="'.$name.'" '.$checked.' value=1 tabindex="'.$tabindex.'">';
		return $html;
	}
	
	public static function getViewRealty($propertiesVal) {
		$html = '';
		foreach ($propertiesVal as $name => $val) {
			$html.='<div class="flat_view">'.$name.': <b>'.$val.'</b></div>';
		}
		return $html;
	}
	
	public static function getPhotosGallery($photos,$photo_path,$address) {
		$html = '<ul class="gallery clearfix">';
		$i = 2;
		foreach ($photos as $photo) {
			$title_n=(isset($address))? $address : $photo['description']; 
			$html.='<li><a href="'.$photo_path.$photo['name'].'" rel="prettyPhotoPhoto[gallery2]" title="'.$title_n/*$photo['description']*/.'"><img src="'.$photo_path.$photo['name'].'_prev" alt="'.addslashes($photo['title']).'" class="border"/></a></li>';
			if ($i==0) {
				//$html.='<br>';
				$i = 2;
			}
			else $i--;
		}
		$html.= '</ul>';
		return $html;
	}
		
	public static function getCompanyPhotosEdit($company) {
		$photos = Company::getPhotosStatic($company['id']);		
		if (!$photos) $photos = array(); 
		$photo_path = Company::getPhotoWebPathStatic($company['id']);
		$html = '';					
		foreach ($photos as $photo) {			
			$id = $photo['name'];
			$html.="<table class='base_text' style='float:left' id='table".$id."'>
			<tr><td rowspan=3><img src='".$photo_path.$photo['name']."_prev'></td></tr> 
			<tr><td><label for='photo_title_".$id."'>Название фото:</label><input type='text' id='photo_title_".$id."' name='photo_title_".$id."' value='{$photo['title']}'></td></tr>
			<tr><td><label for='photo_desc_".$id."'>Описание:</label><textarea id='photo_desc' name='photo_desc_".$id."' style='width:200px; height: 50px;'>{$photo['description']}</textarea></td></tr>
			<tr><td colspan=2><a onclick='delPhoto(\"".$id."\")' href='#'>Удалить</a></td></tr>
			</table>		
			<input type=hidden name=photo_company_exist[] value='".$id."'>";			
		}		
		return $html;
	}
	
	
	public static function getFlatPhotosEdit(Flat $flat) {
		$photos = $flat->getPhotos();
		if (!$photos) $photos = array(); 
		$photo_flat_path = $flat->getPhotoWebPath();		
		$html = '';		
		$photo_types = $flat->getPhotoTypes();	
		foreach ($photos as $photo) {
			$photo_type_html = '';
			foreach ($photo_types as $i => $photo_type) {
				$selected = ($i==$photo['tag']) ? 'selected' : '';
				$photo_type_html .= "<option value='$i' $selected>$photo_type</option>"; 
			}
			$id = $photo['name'];
			$html.="<table class='base_text' style='float:left' id='table".$id."'>
			<tr><td rowspan=3><img src='".$photo_flat_path.$photo['name']."_prev'></td>
			<td><label for='photo_type_".$id."'>На фото показано:</label><select name='photo_type_".$id."' id='photo_type_".$id."'>
			$photo_type_html			
			</select></td></tr> 
			<td><label for='photo_title_".$id."'>Название фото:</label><input type='text' id='photo_title_".$id."' name='photo_title_".$id."' value='{$photo['title']}'></td></tr>
			<tr><td><label for='photo_desc_".$id."'>Описание:</label><textarea id='photo_desc' name='photo_desc_".$id."' style='width:200px; height: 50px;'>{$photo['description']}</textarea></td></tr>
			<tr><td colspan=2><a onclick='delPhoto(\"".$id."\")' href='#'>Удалить</a></td></tr>
			</table>		
			<input type=hidden name=photo_flat_exist[] value='".$id."'>";			
		}		
		return $html;
	}
	
	public static function getRealtyPhotosEdit($realty) {
		$photos = $realty->getPhotos();
		if (!$photos) $photos = array(); 
		$photo_path = $realty->getPhotoWebPath();		
		$html = '';		
		$photo_types = $realty->getPhotoTypes();	
		foreach ($photos as $photo) {
			$photo_type_html = '';
			foreach ($photo_types as $i => $photo_type) {
				$selected = ($i==$photo['tag']) ? 'selected' : '';
				$photo_type_html .= "<option value='$i' $selected>$photo_type</option>"; 
			}
			$id = $photo['name'];
			$html.="<table class='base_text' style='float:left' id='table".$id."'>
			<tr><td rowspan=3><img src='".$photo_path.$photo['name']."_prev'></td>
			<td><label for='photo_type_".$id."'>На фото показано:</label><select name='photo_type_".$id."' id='photo_type_".$id."'>
			$photo_type_html			
			</select></td></tr> 
			<td><label for='photo_title_".$id."'>Название фото:</label><input type='text' id='photo_title_".$id."' name='photo_title_".$id."' value='{$photo['title']}'></td></tr>
			<tr><td><label for='photo_desc_".$id."'>Описание:</label><textarea id='photo_desc' name='photo_desc_".$id."' style='width:200px; height: 50px;'>{$photo['description']}</textarea></td></tr>
			<tr><td colspan=2><a onclick='delPhoto(\"".$id."\")' href='#'>Удалить</a></td></tr>
			</table>		
			<input type=hidden name=photo_{$realty->getKind()}_exist[] value='".$id."'>";			
		}		
		return $html;
	}	
	
	public static function getUrl($object,$action,$id=null,$params=null) {
		$q = ($params) ? $params : '';
		if ($id) $q = $q.'&id='.$id;
		return "/$object.html?action=$action".$q;
	}
	
	public static function paginator($url, $amount, $per_page=10, $current_page=1) {
		$paginator_html = '';
		for ($i = 1; ($i-1) < ceil($amount/$per_page); $i++) 
			if ($i == $current_page)
				$paginator_html .= " <b>".$i."</b> ";
			else
				$paginator_html .= "<a href='$url&page={$i}'>[".$i."]</a> ";
		return $paginator_html;
	}	
	
	public static function getFlatFilter($filter,$user_id=NULL,$status=REALTY_STATUS_SALE) {
		$s = "var rooms=price=price_sq=tenement=kitchen=total_area=balcon=no_corner=storey_no_first=storey_no_last=bath=photo=regions=newt=street=street_id=heating=is_owner=date='';";
		foreach ($filter as $k => $v) {
			if (isset($filter[$k])) {
				$s .=  $k.'="'.$filter[$k].'";';	
			}			 
		}		
		$show_filter = (count($filter)>0) ? 'block' : 'none';
		$show_filter_r = ($show_filter=='block') ? 'none' : 'block';
		if (!isset($filter['price_min'])) $filter['price_min']='';
		if (!isset($filter['price_max'])) $filter['price_max']='';
		if ($status==REALTY_STATUS_SALE && !$user_id) {
			$action = 'sales';
		}
		elseif ($status==REALTY_STATUS_SALE && $user_id) {
			$action = 'userSales';
		}
		elseif ($status==REALTY_STATUS_RENT && !$user_id) {
			$action = 'rent';
		}
		elseif ($status==REALTY_STATUS_RENT && $user_id) {
			$action = 'userRent';
		}
		elseif ($status==REALTY_STATUS_IMPORT_SALE && $user_id) {
			$action = 'userImportSales';
		}
		elseif ($status==REALTY_STATUS_IMPORT_RENT && $user_id) {
			$action = 'userImportRent';
		}
		if ($status==REALTY_STATUS_SALE) {
			$price_title="
		<th>Цена (руб.)</th>
		<th>Цена (руб./м<sup>2</sup>)</th>";
			$price_html = "
		<td class='filter'><select id='f_price' name='f_price' single size=6 onchange='document.filter_form.submit()'  class='span2-5'>
		 <option value=0>не важно</option>
		 <option value=1>до 1 000 000</option>
		 <option value=2>1 000 000 - 1 500 000</option>
		 <option value=3>1 500 000 - 2 000 000</option>
		 <option value=4>2 000 000 - 3 000 000</option>
		 <option value=5>Больше 3 000 000</option>
		</select><br>
		<b>Цена (тыс. руб.)</b><br>
		от <input type=text id='price_min' name='price_min' value='".$filter['price_min']."' class='span2'><br>
		до <input type=text id='price_max' name='price_max' value='".$filter['price_max']."' class='span2'>
		</td>
		<td class='filter'><select id='f_price_sq' name='f_price_sq' single size=6 onchange='document.filter_form.submit()' class='span2'>
		 <option value=0>не важно</option>
		 <option value=1>до 25 000</option>
		 <option value=2>25 000 - 30 000</option>
		 <option value=3>30 000 - 35 000</option>
		 <option value=4>35 000 - 40 000</option>
		 <option value=5>Больше 40 000</option>
		</select>
		</td>			
			";	
		}
		else {
			$price_title="<th>Цена (руб.)</th>";
			$price_html = "
		<td class='filter'><select id='f_price' name='f_price' single size=6 onchange='document.filter_form.submit()' class='span2'>
		 <option value=0>не важно</option>
		 <option value=1>до 3000</option>
		 <option value=2>3000 - 6000</option>
		 <option value=3>6000 - 10 000</option>
		 <option value=4>10 000 - 15 000</option>
		 <option value=5>15 000</option>
		</select><br>
		<b>Цена (тыс. руб.)</b><br>
		от <input type=text id='price_min' name='price_min' value='".$filter['price_min']."' style='width:70px;'><br>
		до <input type=text id='price_max' name='price_max' value='".$filter['price_max']."' style='width:70px;'>
		</td>";							
		}
		 
		$html = "
		<div id='show_filter_div' style='display:$show_filter_r; text-align:center; width:100%;'><input type=button id='show_filter' value='Расширенный поиск квартир' class='btn btn-primary'></div>
		<div id='filter' style='display:$show_filter;'>
		<form action='/flat.html' method='get' name='filter_form'>
		<fieldset style='background-color:#f9f9f9;'><legend>Расширенный поиск квартир</legend>	
		<table class='filter table'><thead><tr>
		<th>Комнат</th>
		$price_title
		<th>Тип дома</th>
		<th>Кухня (м<sup>2</sup>)</th>
		<th>Общая площадь (м<sup>2</sup>)</th>
		<th></th>
		</tr>
		<tr>
		<td class='filter'><select id='f_rooms' name='f_rooms' single size=7 onchange='document.filter_form.submit()' class='span2'>
		 <option value=0>Все</option>
		 <option value=1>1-к.</option>
		 <option value=2>2-к.</option>
		 <option value=3>3-к.</option>
		 <option value=4>4-к.</option>
		 <option value=5>5-к.</option>
		 <option value=6>Больше 5</option>
		</select><br>
		<b>Не старше даты:</b><br>
		<input class='span2' type='text' id='f_date' name='f_date' value='' />
		</td>
		$price_html		
		<td class='filter'><select id='f_tenement' name='f_tenement' single size=6 onchange='document.filter_form.submit()' class='span2'>
		 <option value=0>не важно</option>
		 <option value=1>кирпичный</option>
		 <option value=2>панельный</option>
		 <option value=3>монолитный</option>
		 <option value=4>блочный</option>
		 <option value=5>деревянный</option>
		</select>
		</td>
		<td class='filter'><select id='f_kitchen' name='f_kitchen' single size=5 onchange='document.filter_form.submit()' class='span2'>
		<option value=0>неважно</option> 
		<option value=1>0 (гостинка)</option>
		 <option value=2>до 8 (хрущевка)</option>
		 <option value=3>8-12</option>
		 <option value=4>больше 12</option>
		</select>
		</td>
		<td class='filter'><select id='f_total_area' name='f_total_area' single size=8 onchange='document.filter_form.submit()' class='span2'>
		<option value=0>неважно</option>
		 <option value=1>до 25 (гост., комн.)</option>
		 <option value=2>25-50</option>
		 <option value=3>50-60</option>
		 <option value=4>60-70</option>
		 <option value=5>70-80</option>
		 <option value=6>80-100</option>
		 <option value=7>больше 100</option>
		</select>
		</td>
		</tr>
		</thead>
		<tbody>
		<tr>
		<td align=left>		
		<b>Балкон</b> <input type='checkbox' value=1 id='f_balcon' name='f_balcon' onclick='document.filter_form.submit()'><br><br>
		<b>Этаж не первый</b> <input type='checkbox' value=1 id='f_storey_no_first' name='f_storey_no_first' onclick='document.filter_form.submit()'><br><br>
		<b>Этаж не последний</b> <input type='checkbox' value=1 id='f_storey_no_last' name='f_storey_no_last' onclick='document.filter_form.submit()'><br>		
		</td>
		<td align=left>
		<b>Не угловая</b> <input type='checkbox' value=1 id='f_no_corner' name='f_no_corner' onclick='document.filter_form.submit()'><br><br>
		<b>Санузел раздельный</b> <input type='checkbox' value=1 id='f_bath' name='f_bath' onclick='document.filter_form.submit()'><br><br>
		<b>С фото</b> <input type='checkbox' value=1 id='f_photo' name='f_photo' onclick='document.filter_form.submit()'>	
		</td>
		<td class='filter'>
		<b>Новостройка/вторичка</b><br>
		<select id='f_newt' name='f_newt' single size=3 onchange='document.filter_form.submit()' class='span2'>
		<option value=0>неважно</option> 
		<option value=1>вторичное</option>
		<option value=2>новостройки</option>
		</select>
		</td>
		<td class='filter'>
		<b>Улица Йошкар-Олы</b><br>
		<input name='f_street' id='f_street' type='text' style='width:180;' maxlength='255' value='' class='span2'>
		<input name='f_street_id' id='f_street_id' type='hidden' value='' onchange='document.filter_form.submit()'><br><br>
		<b>Только районы</b> <input type='checkbox' value=1 id='f_regions' name='f_regions' onclick='document.filter_form.submit()'>
		</td>
		<td class='filter'>
		<b>Отопление</b><br>
		<select id='f_heating' name='f_heating' single size=3 onchange='document.filter_form.submit()' class='span2'>
		<option value=0>неважно</option> 
		<option value=1>поквартирное</option>
		<option value=2>центральное</option>
		</select>
		</td>
		<td>
		<b>От собственника</b> <input type='checkbox' value=1 id='f_is_owner' name='f_is_owner' onclick='document.filter_form.submit()'>
		</td>
		</tr>
		<tr>
		<td colspan='10' class='filter' style='text-align:center;'>		
		<input type='submit' value=' Найти квартиры ' class='btn btn-primary'>
		<input type='button' id='btn_map' value='Показать квартиры на карте' class='btn btn-info'>		
		<input type=button id='close_filter' value='Закрыть' class='btn'>
		<input type='hidden' name='act' id='act' value='$action'>
		<input type='hidden' name='action' id='action' value='$action'>

		</td>
		</tr>
		</tbody>
		</table>		
		</fieldset>
		</form>
		</div>
		<script>
		$s				
		</script>
		";
		return $html;
	}
	
	public static function getCommercialFilter($filter,$user_id=NULL,$status=REALTY_STATUS_SALE) {
		$s = "var price=price_sq=house=total_area=photo=regions=city=city_id=radius=heating='';";
		foreach ($filter as $k => $v) {
			if (isset($filter[$k])) {
				$s .=  $k.'="'.$filter[$k].'";';	
			}			 
		}		
		$show_filter = (count($filter)>0) ? 'block' : 'none';
		$show_filter_r = ($show_filter=='block') ? 'none' : 'block';
		if (!isset($filter['price_min'])) $filter['price_min']='';
		if (!isset($filter['price_max'])) $filter['price_max']='';
		if ($status==REALTY_STATUS_SALE && !$user_id) {
			$action = 'sales';
		}
		elseif ($status==REALTY_STATUS_SALE && $user_id) {
			$action = 'userSales';
		}
		elseif ($status==REALTY_STATUS_RENT && !$user_id) {
			$action = 'rent';
		}
		elseif ($status==REALTY_STATUS_RENT && $user_id) {
			$action = 'userRent';
		}
		if ($status==REALTY_STATUS_SALE) {
			$price_title="
		<th>Цена (руб.)</th>
		<th>Цена (руб./м<sup>2</sup>)</th>";
			$price_html = "
		<td class='filter'><select id='f_price' name='f_price' single size=6 onchange='document.filter_form.submit()'  class='span2-5'>
		 <option value=0>не важно</option>
		 <option value=1>до 1 000 000</option>
		 <option value=2>1 000 000 - 1 500 000</option>
		 <option value=3>1 500 000 - 2 000 000</option>
		 <option value=4>2 000 000 - 3 000 000</option>
		 <option value=5>Больше 3 000 000</option>
		</select><br>
		<b>Цена (тыс. руб.)</b><br>
		от <input type=text id='price_min' name='price_min' value='".$filter['price_min']."' class='span2'><br>
		до <input type=text id='price_max' name='price_max' value='".$filter['price_max']."' class='span2'>
		</td>
		<td class='filter'><select id='f_price_sq' name='f_price_sq' single size=6 onchange='document.filter_form.submit()' class='span2'>
		 <option value=0>не важно</option>
		 <option value=1>до 25 000</option>
		 <option value=2>25 000 - 30 000</option>
		 <option value=3>30 000 - 35 000</option>
		 <option value=4>35 000 - 40 000</option>
		 <option value=5>Больше 40 000</option>
		</select>
		</td>			
			";	
		}
		else {
			$price_title="<th>Цена (руб.)</th>";
			$price_html = "
		<td class='filter'><select id='f_price' name='f_price' single size=6 onchange='document.filter_form.submit()' class='span2'>
		 <option value=0>не важно</option>
		 <option value=1>до 3000</option>
		 <option value=2>3000 - 6000</option>
		 <option value=3>6000 - 10 000</option>
		 <option value=4>10 000 - 15 000</option>
		 <option value=5>15 000</option>
		</select><br>
		<b>Цена (тыс. руб.)</b><br>
		от <input type=text id='price_min' name='price_min' value='".$filter['price_min']."' style='width:70px;'><br>
		до <input type=text id='price_max' name='price_max' value='".$filter['price_max']."' style='width:70px;'>
		</td>";							
		}
		 
		$html = "
		<!--<div id='show_filter_div' style='display:$show_filter_r; text-align:center; width:100%;'><input type=button id='show_filter' value='Расширенный поиск домов' class='btn btn-primary'></div>-->
		<div id='filter' style='display:$show_filter;'>
		<form action='/commercial.html' method='get' name='filter_form'>
		<fieldset style='background-color:#f9f9f9;'><legend>Расширенный поиск домов</legend>	
		<table class='filter table'><thead><tr>		
		$price_title
		<th>Удаленность от Йошкар-Олы (км)</th>
		<th>Тип дома</th>
		<th></th>						
		</tr>
		<tr>
		$price_html
		<td class='filter'>
		<input type='text' id='f_radius' name='f_radius' onchange='document.filter_form.submit()' class='span2'>		
		<br><br>
		<b>Населенный пункт</b><br>
		<input name='f_city' id='f_city' type='text' style='width:180;' maxlength='255' value='' class='span2'>
		<input name='f_city_id' id='f_city_id' type='hidden' value='' onchange='document.filter_form.submit()'>
		<br><br>
		<b>С фото</b> <input type='checkbox' value=1 id='f_photo' name='f_photo' onclick='document.filter_form.submit()'>
		<br><br>
		<b>Только районы</b> <input type='checkbox' value=1 id='f_regions' name='f_regions' onclick='document.filter_form.submit()'>
		</td>		
		<td class='filter'><select id='f_house' name='f_house' single size=5 onchange='document.filter_form.submit()' class='span2'>
		 <option value=0>не важно</option>
		 <option value=1>деревянный</option>
		 <option value=2>кирпичный</option>		 
		 <option value=3>монолитный</option>
		 <option value=4>блочный</option>		 
		</select>
		</td>
		</tr>
		</thead>
		<tbody>
		<tr>
		<td colspan='10' class='filter' style='text-align:center;'>		
		<input type='submit' value=' Найти дома ' class='btn btn-primary'>
		<!--<input type='button' id='btn_map' value='Показать дома на карте' class='btn btn-info'>-->		
		<input type=button id='close_filter' value='Закрыть' class='btn'>
		<input type='hidden' name='act' id='act' value='$action'>
		<input type='hidden' name='action' id='action' value='$action'>
		</td>
		</tr>
		</tbody>
		</table>		
		</fieldset>
		</form>
		</div>
		<script>
		$s				
		</script>
		";
		return $html;
	}
	
	
	public static function getHouseFilter($filter,$user_id=NULL,$status=REALTY_STATUS_SALE) {
		$s = "var price=price_sq=house=total_area=photo=regions=city=city_id=radius=heating='';";
		foreach ($filter as $k => $v) {
			if (isset($filter[$k])) {
				$s .=  $k.'="'.$filter[$k].'";';	
			}			 
		}		
		$show_filter = (count($filter)>0) ? 'block' : 'none';
		$show_filter_r = ($show_filter=='block') ? 'none' : 'block';
		if (!isset($filter['price_min'])) $filter['price_min']='';
		if (!isset($filter['price_max'])) $filter['price_max']='';
		if ($status==REALTY_STATUS_SALE && !$user_id) {
			$action = 'sales';
		}
		elseif ($status==REALTY_STATUS_SALE && $user_id) {
			$action = 'userSales';
		}
		elseif ($status==REALTY_STATUS_RENT && !$user_id) {
			$action = 'rent';
		}
		elseif ($status==REALTY_STATUS_RENT && $user_id) {
			$action = 'userRent';
		}
		if ($status==REALTY_STATUS_SALE) {
			$price_title="
		<th>Цена (руб.)</th>
		<th>Цена (руб./м<sup>2</sup>)</th>";
			$price_html = "
		<td class='filter'><select id='f_price' name='f_price' single size=6 onchange='document.filter_form.submit()'  class='span2-5'>
		 <option value=0>не важно</option>
		 <option value=1>до 1 000 000</option>
		 <option value=2>1 000 000 - 1 500 000</option>
		 <option value=3>1 500 000 - 2 000 000</option>
		 <option value=4>2 000 000 - 3 000 000</option>
		 <option value=5>Больше 3 000 000</option>
		</select><br>
		<b>Цена (тыс. руб.)</b><br>
		от <input type=text id='price_min' name='price_min' value='".$filter['price_min']."' class='span2'><br>
		до <input type=text id='price_max' name='price_max' value='".$filter['price_max']."' class='span2'>
		</td>
		<td class='filter'><select id='f_price_sq' name='f_price_sq' single size=6 onchange='document.filter_form.submit()' class='span2'>
		 <option value=0>не важно</option>
		 <option value=1>до 25 000</option>
		 <option value=2>25 000 - 30 000</option>
		 <option value=3>30 000 - 35 000</option>
		 <option value=4>35 000 - 40 000</option>
		 <option value=5>Больше 40 000</option>
		</select>
		</td>			
			";	
		}
		else {
			$price_title="<th>Цена (руб.)</th>";
			$price_html = "
		<td class='filter'><select id='f_price' name='f_price' single size=6 onchange='document.filter_form.submit()' class='span2'>
		 <option value=0>не важно</option>
		 <option value=1>до 3000</option>
		 <option value=2>3000 - 6000</option>
		 <option value=3>6000 - 10 000</option>
		 <option value=4>10 000 - 15 000</option>
		 <option value=5>15 000</option>
		</select><br>
		<b>Цена (тыс. руб.)</b><br>
		от <input type=text id='price_min' name='price_min' value='".$filter['price_min']."' style='width:70px;'><br>
		до <input type=text id='price_max' name='price_max' value='".$filter['price_max']."' style='width:70px;'>
		</td>";							
		}
		 
		$html = "
		<div id='show_filter_div' style='display:$show_filter_r; text-align:center; width:100%;'><input type=button id='show_filter' value='Расширенный поиск домов' class='btn btn-primary'></div>
		<div id='filter' style='display:$show_filter;'>
		<form action='/house.html' method='get' name='filter_form'>
		<fieldset style='background-color:#f9f9f9;'><legend>Расширенный поиск домов</legend>	
		<table class='filter table'><thead><tr>		
		$price_title
		<th>Удаленность от Йошкар-Олы (км)</th>
		<th>Тип дома</th>
		<th></th>						
		</tr>
		<tr>
		$price_html
		<td class='filter'>
		<input type='text' id='f_radius' name='f_radius' onchange='document.filter_form.submit()' class='span2'>		
		<br><br>
		<b>Населенный пункт</b><br>
		<input name='f_city' id='f_city' type='text' style='width:180;' maxlength='255' value='' class='span2'>
		<input name='f_city_id' id='f_city_id' type='hidden' value='' onchange='document.filter_form.submit()'>
		<br><br>
		<b>С фото</b> <input type='checkbox' value=1 id='f_photo' name='f_photo' onclick='document.filter_form.submit()'>
		<br><br>
		<b>Только районы</b> <input type='checkbox' value=1 id='f_regions' name='f_regions' onclick='document.filter_form.submit()'>
		</td>		
		<td class='filter'><select id='f_house' name='f_house' single size=5 onchange='document.filter_form.submit()' class='span2'>
		 <option value=0>не важно</option>
		 <option value=1>деревянный</option>
		 <option value=2>кирпичный</option>		 
		 <option value=3>монолитный</option>
		 <option value=4>блочный</option>		 
		</select>
		</td>
		</tr>
		</thead>
		<tbody>
		<tr>
		<td colspan='10' class='filter' style='text-align:center;'>		
		<input type='submit' value=' Найти дома ' class='btn btn-primary'>
		<!--<input type='button' id='btn_map' value='Показать дома на карте' class='btn btn-info'>-->		
		<input type=button id='close_filter' value='Закрыть' class='btn'>
		<input type='hidden' name='act' id='act' value='$action'>
		<input type='hidden' name='action' id='action' value='$action'>
		</td>
		</tr>
		</tbody>
		</table>		
		</fieldset>
		</form>
		</div>
		<script>
		$s				
		</script>
		";
		return $html;
	}
		
	public static function getLandFilter($filter,$user_id=NULL,$status=REALTY_STATUS_SALE) {
		$s = "var price=price_sq=total_area=photo=regions=city=city_id=radius=heating='';";
		foreach ($filter as $k => $v) {
			if (isset($filter[$k])) {
				$s .=  $k.'="'.$filter[$k].'";';	
			}			 
		}		
		$show_filter = (count($filter)>0) ? 'block' : 'none';
		$show_filter_r = ($show_filter=='block') ? 'none' : 'block';
		if (!isset($filter['price_min'])) $filter['price_min']='';
		if (!isset($filter['price_max'])) $filter['price_max']='';
		if ($status==REALTY_STATUS_SALE && !$user_id) {
			$action = 'sales';
		}
		elseif ($status==REALTY_STATUS_SALE && $user_id) {
			$action = 'userSales';
		}
		elseif ($status==REALTY_STATUS_RENT && !$user_id) {
			$action = 'rent';
		}
		elseif ($status==REALTY_STATUS_RENT && $user_id) {
			$action = 'userRent';
		}
		if ($status==REALTY_STATUS_SALE) {
			$price_title="
		<th>Цена (руб.)</th>
		<th>Цена (руб./сот.)</th>";
			$price_html = "
		<td class='filter'><select id='f_price' name='f_price' single size=6 onchange='document.filter_form.submit()'  class='span2-5'>
		 <option value=0>не важно</option>
		 <option value=1>до 100 000</option>
		 <option value=2>100 000 - 300 000</option>
		 <option value=3>300 000 - 600 000</option>
		 <option value=4>600 000 - 1 000 000</option>
		 <option value=5>Больше 1 000 000</option>
		</select><br>
		<b>Цена (тыс. руб.)</b><br>
		от <input type=text id='price_min' name='price_min' value='".$filter['price_min']."' class='span2'><br>
		до <input type=text id='price_max' name='price_max' value='".$filter['price_max']."' class='span2'>
		</td>
		<td class='filter'><select id='f_price_sq' name='f_price_sq' single size=6 onchange='document.filter_form.submit()' class='span2'>
		 <option value=0>не важно</option>
		 <option value=1>до 10 000</option>
		 <option value=2>10 000 - 30 000</option>
		 <option value=3>30 000 - 60 000</option>
		 <option value=4>60 000 - 100 000</option>
		 <option value=5>Больше 100 000</option>
		</select>
		</td>			
			";	
		}
		else {
			$price_title="<th>Цена (руб.)</th>";
			$price_html = "
		<td class='filter'><select id='f_price' name='f_price' single size=6 onchange='document.filter_form.submit()' class='span2'>
		 <option value=0>не важно</option>
		 <option value=1>до 3000</option>
		 <option value=2>3000 - 6000</option>
		 <option value=3>6000 - 10 000</option>
		 <option value=4>10 000 - 15 000</option>
		 <option value=5>15 000</option>
		</select><br>
		<b>Цена (тыс. руб.)</b><br>
		от <input type=text id='price_min' name='price_min' value='".$filter['price_min']."' style='width:70px;'><br>
		до <input type=text id='price_max' name='price_max' value='".$filter['price_max']."' style='width:70px;'>
		</td>";							
		}
		 
		$html = "
		<div id='show_filter_div' style='display:$show_filter_r; text-align:center; width:100%;'><input type=button id='show_filter' value='Расширенный поиск земельных участков' class='btn btn-primary'></div>
		<div id='filter' style='display:$show_filter;'>
		<form action='/land.html' method='get' name='filter_form'>
		<fieldset style='background-color:#f9f9f9;'><legend>Расширенный поиск земельных участков в Марий Эл</legend>	
		<table class='filter table'><thead><tr>		
		$price_title
		<th>Удаленность от Йошкар-Олы (км)</th>
		<th></th>						
		</tr>
		<tr>
		$price_html
		<td class='filter'>
		<input type='text' id='f_radius' name='f_radius' onchange='document.filter_form.submit()' class='span2'>		
		<br><br>
		<b>Населенный пункт</b><br>
		<input name='f_city' id='f_city' type='text' style='width:180;' maxlength='255' value='' class='span2'>
		<input name='f_city_id' id='f_city_id' type='hidden' value='' onchange='document.filter_form.submit()'>
		<br><br>
		<b>С фото</b> <input type='checkbox' value=1 id='f_photo' name='f_photo' onclick='document.filter_form.submit()'>
		<br><br>
		<b>Только районы</b> <input type='checkbox' value=1 id='f_regions' name='f_regions' onclick='document.filter_form.submit()'>
		</td>		
		</tr>
		</thead>
		<tbody>
		<tr>
		<td colspan='10' class='filter' style='text-align:center;'>		
		<input type='submit' value=' Найти земельные участки ' class='btn btn-primary'>
		<!--<input type='button' id='btn_map' value='Показать земельные участки на карте' class='btn btn-info'>-->		
		<input type=button id='close_filter' value='Закрыть' class='btn'>
		<input type='hidden' name='act' id='act' value='$action'>
		<input type='hidden' name='action' id='action' value='$action'>
		</td>
		</tr>
		</tbody>
		</table>		
		</fieldset>
		</form>
		</div>
		<script>
		$s				
		</script>
		";
		return $html;
	}
	
	
	public static function getFlatList($where, $order, $direction, $per_page=10, $current_page=1, $action='sales', $user_id=NULL) {		
		$start_pos = ($current_page-1)*$per_page;
		$limit = " LIMIT $start_pos,$per_page";
		//$amount = Flat::getAmountInList($where);
		$sort = ($order=='') ? "f.updated_on" : $order;
		$where_param = $where." GROUP BY f.id ORDER BY ".$sort." ".$direction.$limit;
		$params = '';
		if (isset($_GET['rooms'])) $params .= '&rooms='.intval($_GET['rooms']);
		
		if (isset($_REQUEST['f_rooms'])) {
			$params .= '&rooms='.intval($_REQUEST['f_rooms']).'&f_rooms='.intval($_REQUEST['f_rooms']);
		}		
		$price_min=0;
		$price_max=0;
		if (isset($_REQUEST['f_price'])) {
			$f_price = intval($_REQUEST['f_price']);
			$params .= '&f_price='.$f_price;
			switch ($f_price) {
				case 1:
					$price_max = 1000000;
					break;
				case 2:
					$price_min = 1000000;
					$price_max = 1500000;
					break;
				case 3:
					$price_min = 1500000;
					$price_max = 2000000;					
					break;
				case 4:
					$price_min = 2000000;
					$price_max = 3000000;					
					break;
				case 5:					
					$price_min = 3000000;
					break;
			}
		}
		else {
			if (isset($_REQUEST['price_min']) && $_REQUEST['price_min']>0) $price_min = intval($_REQUEST['price_min']);
			else $price_min = 0;
			if (isset($_REQUEST['price_max']) && $_REQUEST['price_max']>0) $price_max = intval($_REQUEST['price_max']);
			else $price_max = 0;
			
			if ($price_min>0) $params .= '&price_min='.$price_min;
			if ($price_max>0) $params .= '&price_max='.$price_max;
		}
		if (isset($_REQUEST['f_date'])) {
			$params .= '&f_date='.clearTextData($_REQUEST['f_date']);
		}
		if (isset($_REQUEST['f_price_sq'])) {
			$params .= '&f_price_sq='.intval($_REQUEST['f_price_sq']);
		}
		if (isset($_REQUEST['f_tenement'])) {
			$params .= '&f_tenement='.intval($_REQUEST['f_tenement']);
		}
		if (isset($_REQUEST['f_kitchen'])) {
			$params .= '&f_kitchen='.intval($_REQUEST['f_kitchen']);
		}
		if (isset($_REQUEST['f_total_area'])) {
			$params .= '&f_total_area='.intval($_REQUEST['f_total_area']);
		}
		if (isset($_REQUEST['f_total_area'])) {
			$params .= '&f_total_area='.intval($_REQUEST['f_total_area']);
		}
		if (isset($_REQUEST['f_balcon'])) {
			$params .= '&f_balcon='.intval($_REQUEST['f_balcon']);
		}
		if (isset($_REQUEST['f_no_corner'])) {
			$params .= '&f_no_corner='.intval($_REQUEST['f_no_corner']);
		}
		if (isset($_REQUEST['f_storey_no_first'])) {
			$params .= '&f_storey_no_first='.intval($_REQUEST['f_storey_no_first']);
		}
		if (isset($_REQUEST['f_storey_no_last'])) {
			$params .= '&f_storey_no_last='.intval($_REQUEST['f_storey_no_last']);
		}
		if (isset($_REQUEST['f_bath'])) {
			$params .= '&f_bath='.intval($_REQUEST['f_bath']);
		}
		if (isset($_REQUEST['f_photo'])) {
			$params .= '&f_photo='.intval($_REQUEST['f_photo']);
		}
		if (isset($_REQUEST['f_regions'])) {
			$params .= '&f_regions='.intval($_REQUEST['f_regions']);
		}
		if (isset($_REQUEST['f_newt'])) {
			$params .= '&f_newt='.intval($_REQUEST['f_newt']);
		}
		if (isset($_REQUEST['f_street']) && isset($_REQUEST['f_street_id'])) {
			$params .= '&f_street='.clearTextData($_REQUEST['f_street']);
			$params .= '&f_street_id='.intval($_REQUEST['f_street_id']);
		}
		if (isset($_REQUEST['f_heating'])) {
			$params .= '&f_heating='.intval($_REQUEST['f_heating']);
		}
		if (isset($_REQUEST['f_is_owner'])) {
			$params .= '&f_is_owner='.intval($_REQUEST['f_is_owner']);
		}
		
		if (isset($_SESSION['direction'])) {
			$direction = ($_SESSION['direction']=='ASC') ? 'ASC' : 'DESC';
			$arrow = ($direction=='ASC') ? '&darr;' : '&uarr;';
			$direction = 'direction='.$direction;						
		}
		else {
			$direction = '';
			$arrow = '';
		}		
		$arrow_date = '';
		$arrow_price = '';
		if ($sort=='updated_on') $arrow_date = $arrow;
		else if ($sort=='price') $arrow_price = $arrow;
		
		$amount = Flat::getAmountInList($where." GROUP BY f.id");
		
		$paginator = self::paginator("flat.html?action=$action".$params,$amount,$per_page,$current_page);		
		$html = "
		Найдено $amount объявлений:
		<br>						
		<table class='table table-striped table-bordered table-condensed'>
		<thead>
		 <tr>		 
		 <th>Комнат</th>
		 <th><a href='/flat.html?action=$action&sort=price".$params."'><b>Цена(руб.)</b></a>$arrow_price<br>руб./м<sup>2</sup></th>
		 <th>Этаж</th>		 
		 <th>Адрес</th>		 		 
		 <th>Фото</th>		 		 
		 <th>Общая/кухня м<sup>2</sup></th>
		 <th>Балкон</th>		 
		 <th>Описание</th>
		 <th><a href='/flat.html?action=$action&sort=updated_on".$params."'><b>Дата</b></a>$arrow_date</th>		 
		 <th></th>
		 </tr>
		</thead>
		<tbody>
		";
		$ids = array();
		$db_res = Flat::getFullListLink($where_param);
		$tenement_icons = array('kirpich','panel','monolit','block','derevo');
		while ($row = $db_res->fetchRow()) {
			$ids[] = $row['id'];
			$type_name = Tenement::$TYPE[$row['ttype']];
			$type = $tenement_icons[$row['ttype']];
			$type = "<img src='/images/icon_{$type}.png' title='{$type_name}'>";		
			$price = number_format($row['price'],0);
			$price_m = number_format($row['price_m'],0);
			$city = ($row['city_id']==0) ? '' : $row['city'].',';
			$addr = "$city {$row['street']}";
			if ($row['show_address']) $addr .= ", {$row['tnum']}";
			$is_corner = $row['is_corner'] ? '<br><b>Угловая</b>' : '';
			$bath = $row['type_bathroom'] ? '<br><b>С/у совмещен</b>' : '';
			$is_new = $row['is_new'] ? '<br><b>Новостройка</b>' : '';
			$is_owner = $row['is_owner'] ? '<br><b>Собственник</b>' : '';
			
			$desc = str_replace('.','. ',$row['description']);
			$desc = str_replace(',',', ',$desc);
			$desc = str_replace('-',' - ',$desc);
			$desc = str_replace('. ,','.,',$desc);
			//$desc_all = substr($desc,0,255).$dots.$is_corner.$bath;
			$desc_all = truncate_utf8($desc,300,true,true).$is_corner.$bath;
			
			
			//$hr = ($desc_all!='') ? '<hr>' : ''; 
			//$description = $desc_all.$hr.$row['contacts'];
			$description = $desc_all.$is_new.$is_owner;
			$flat_url = "/flat.html?action=view&id={$row['id']}";
			$photo_html = ($row['photo_tenement']!='') ? "<a href='$flat_url'  title='{$row['rooms']}-комнатная, {$price} руб.'><img src='/".PHOTOS_WEBPATH.TENEMENT."/".$row['tenement_id']."/".$row['photo_tenement']."_prev' class='border_const'></a>" : 'Нет фото';
			$photo_html2 = ($row['photo_flat']!='') ? "<a href='$flat_url'  title='{$row['rooms']}-комнатная, {$price} руб.'><img src='/".PHOTOS_WEBPATH.FLAT."/".$row['id']."/".$row['photo_flat']."_prev' class='border_const'></a>" : '';
			if ($photo_html2 != '') $photo_html = $photo_html2;
			
			$date = explode(' ',$row['updated_on']);
			$dates = explode('-',$date[0]);
			$date = $dates[2].'.'.$dates[1];//.'.'.$dates[0]
			
			$url_del = !isset($_SESSION['admin']) ? '' : '<br><br><a href="javascript:delFlat('.$row['id'].');">Удалить</a>';
			if ($user_id) {
				$url_del .= ' <a href="javascript:update('.$row['id'].');" title="Обновить дату на текущую"><img src="/images/icon_update.png" title="Обновить дату на сегодняшнюю"></a>';
				$url_del .= ' <a href="flat.html?action=edit&id='.$row['id'].'"><img src="/images/icon_edit.png" title="Редактировать"></a>';
				$url_del .= ' <a href="javascript:remove('.$row['id'].');"><img src="/images/icon_delete.png" title="Снять"></a>';
				$url_del .= ' <a href="javascript:sold('.$row['id'].');"><img src="/images/icon_sold.png" title="Продано"></a>'; 
			}
			if ($row['balcony']>0) $balcon = 'балкон '.$row['balcony'].'м<sup>2</sup>';
			elseif ($row['loggia']>0) $balcon = 'лоджия '.$row['loggia'].'м<sup>2</sup>';
			else $balcon = 'балкона нет'; 			
			$html .= "<tr>			
			<td style='text-align:right;'><b>{$row['rooms']}-к.</b></td>
			<td><b>$price</b><br><br>$price_m</td>
			<td>{$row['storey']}/{$row['storeys']}<br><a href='/tenement.html?action=view&id={$row['tenement_id']}' target='_blank'>$type</a></td>
			<td><a href='$flat_url'>$addr</a></td>
			<td>$photo_html</td>			
			<td>{$row['total_area']}/{$row['kitchen_area']}</td>
			<td>$balcon</td>			
			<td>$description</td>
			<td>$date</td>
			<td><a href='$flat_url'><img src='/images/icon_view.png' title='Смотреть'></a>$url_del</td>
			</tr>			
			";
		}
		$html .= "</tbody></table>
		<div>$paginator</div>		
		";
		return array('html'=>$html,'ids'=>$ids);
	}
	
	public static function getFlatList2($where, $order, $direction, $per_page=10, $current_page=1, $action='sales', $user_id=NULL) {		
		$start_pos = ($current_page-1)*$per_page;
		$limit = " LIMIT $start_pos,$per_page";
		//$amount = Flat::getAmountInList($where);
		$sort = ($order=='') ? "f.updated_on" : $order;
		$where_param = $where." GROUP BY f.id ORDER BY ".$sort." ".$direction.$limit;
		$params = '';
		if (isset($_GET['rooms'])) $params .= '&rooms='.intval($_GET['rooms']);
		
		if (isset($_REQUEST['f_rooms'])) {
			$params .= '&rooms='.intval($_REQUEST['f_rooms']).'&f_rooms='.intval($_REQUEST['f_rooms']);
		}		
		$price_min=0;
		$price_max=0;
		if (isset($_REQUEST['f_price'])) {
			$f_price = intval($_REQUEST['f_price']);
			$params .= '&f_price='.$f_price;
			switch ($f_price) {
				case 1:
					$price_max = 1000000;
					break;
				case 2:
					$price_min = 1000000;
					$price_max = 1500000;
					break;
				case 3:
					$price_min = 1500000;
					$price_max = 2000000;					
					break;
				case 4:
					$price_min = 2000000;
					$price_max = 3000000;					
					break;
				case 5:					
					$price_min = 3000000;
					break;
			}
		}
		else {
			if (isset($_REQUEST['price_min']) && $_REQUEST['price_min']>0) $price_min = intval($_REQUEST['price_min']);
			else $price_min = 0;
			if (isset($_REQUEST['price_max']) && $_REQUEST['price_max']>0) $price_max = intval($_REQUEST['price_max']);
			else $price_max = 0;
			
			if ($price_min>0) $params .= '&price_min='.$price_min;
			if ($price_max>0) $params .= '&price_max='.$price_max;
		}
		if (isset($_REQUEST['f_date'])) {
			$params .= '&f_date='.clearTextData($_REQUEST['f_date']);
		}
		if (isset($_REQUEST['f_price_sq'])) {
			$params .= '&f_price_sq='.intval($_REQUEST['f_price_sq']);
		}
		if (isset($_REQUEST['f_tenement'])) {
			$params .= '&f_tenement='.intval($_REQUEST['f_tenement']);
		}
		if (isset($_REQUEST['f_kitchen'])) {
			$params .= '&f_kitchen='.intval($_REQUEST['f_kitchen']);
		}
		if (isset($_REQUEST['f_total_area'])) {
			$params .= '&f_total_area='.intval($_REQUEST['f_total_area']);
		}
		if (isset($_REQUEST['f_total_area'])) {
			$params .= '&f_total_area='.intval($_REQUEST['f_total_area']);
		}
		if (isset($_REQUEST['f_balcon'])) {
			$params .= '&f_balcon='.intval($_REQUEST['f_balcon']);
		}
		if (isset($_REQUEST['f_no_corner'])) {
			$params .= '&f_no_corner='.intval($_REQUEST['f_no_corner']);
		}
		if (isset($_REQUEST['f_storey_no_first'])) {
			$params .= '&f_storey_no_first='.intval($_REQUEST['f_storey_no_first']);
		}
		if (isset($_REQUEST['f_storey_no_last'])) {
			$params .= '&f_storey_no_last='.intval($_REQUEST['f_storey_no_last']);
		}
		if (isset($_REQUEST['f_bath'])) {
			$params .= '&f_bath='.intval($_REQUEST['f_bath']);
		}
		if (isset($_REQUEST['f_photo'])) {
			$params .= '&f_photo='.intval($_REQUEST['f_photo']);
		}
		if (isset($_REQUEST['f_regions'])) {
			$params .= '&f_regions='.intval($_REQUEST['f_regions']);
		}
		if (isset($_REQUEST['f_newt'])) {
			$params .= '&f_newt='.intval($_REQUEST['f_newt']);
		}
		if (isset($_REQUEST['f_street']) && isset($_REQUEST['f_street_id'])) {
			$params .= '&f_street='.clearTextData($_REQUEST['f_street']);
			$params .= '&f_street_id='.intval($_REQUEST['f_street_id']);
		}
		if (isset($_REQUEST['f_heating'])) {
			$params .= '&f_heating='.intval($_REQUEST['f_heating']);
		}
		if (isset($_REQUEST['f_is_owner'])) {
			$params .= '&f_is_owner='.intval($_REQUEST['f_is_owner']);
		}
		
		if (isset($_SESSION['direction'])) {
			$direction = ($_SESSION['direction']=='ASC') ? 'ASC' : 'DESC';
			$arrow = ($direction=='ASC') ? '&darr;' : '&uarr;';
			$direction = 'direction='.$direction;						
		}
		else {
			$direction = '';
			$arrow = '';
		}		
		$arrow_date = '';
		$arrow_price = '';
		if ($sort=='updated_on') $arrow_date = $arrow;
		else if ($sort=='price') $arrow_price = $arrow;
		
		$amount = Flat::getAmountInList($where." GROUP BY f.id");
		
		$paginator = self::paginator("flat.html?action=$action".$params,$amount,$per_page,$current_page);		
		$html = "
		Найдено $amount объявлений:
		<br>						
		<table class='table table-striped table-bordered table-condensed'>
		<thead>
		 <tr>		 
		 <th>Комнат</th>
		 <th><a href='/flat.html?action=$action&sort=price".$params."'><b>Цена(руб.)</b></a>$arrow_price<br>руб./м<sup>2</sup></th>
		 <th>Этаж</th>		 
		 <th>Адрес</th>		 		 		 
		 <th>Общая/кухня м<sup>2</sup></th>
		 <th>Балкон</th>		 
		 <th>Описание</th>
		 <th>Тел.</th>
		 <th><a href='/flat.html?action=$action&sort=updated_on".$params."'><b>Дата</b></a>$arrow_date</th>
		 <th></th> 		 
		 </tr>
		</thead>
		<tbody>
		";
		$ids = array();
		$db_res = Flat::getFullListLink($where_param);
		$tenement_icons = array('kirpich','panel','monolit','block','derevo');
		while ($row = $db_res->fetchRow()) {
			$ids[] = $row['id'];
			$type_name = Tenement::$TYPE[$row['ttype']];
			$type = $tenement_icons[$row['ttype']];
			$type = "<img src='/images/icon_{$type}.png' title='{$type_name}'>";		
			$price = number_format($row['price'],0);
			$price_m = number_format($row['price_m'],0);
			$city = ($row['city_id']==0) ? '' : $row['city'].',';
			$addr = "$city {$row['street']}";
			if ($row['show_address']) $addr .= ", {$row['tnum']}";
			$is_corner = $row['is_corner'] ? '<br><b>Угловая</b>' : '';
			$bath = $row['type_bathroom'] ? '<br><b>С/у совмещен</b>' : '';
			$is_new = $row['is_new'] ? '<br><b>Новостройка</b>' : '';
			$is_owner = $row['is_owner'] ? '<br><b>Собственник</b>' : '';
			
			$desc = Realty::prepareDescription($row['description']);
			$desc_all = truncate_utf8($desc,300,true,true).$is_corner.$bath;					
			$description = $desc_all.$is_new.$is_owner;
			$tel = truncate_utf8($row['contacts'],100,true,false);
			$flat_url = "/flat.html?action=view&id={$row['id']}";
			$photo_html = ($row['photo_tenement']!='') ? "<a href='$flat_url'  title='{$row['rooms']}-комнатная, {$price} руб.'><img src='/".PHOTOS_WEBPATH.TENEMENT."/".$row['tenement_id']."/".$row['photo_tenement']."_prev' class='border_const'></a>" : 'Нет фото';
			$photo_html2 = ($row['photo_flat']!='') ? "<a href='$flat_url'  title='{$row['rooms']}-комнатная, {$price} руб.'><img src='/".PHOTOS_WEBPATH.FLAT."/".$row['id']."/".$row['photo_flat']."_prev' class='border_const'></a>" : '';
			if ($photo_html2 != '') $photo_html = $photo_html2;
			
			$date = explode(' ',$row['updated_on']);
			$dates = explode('-',$date[0]);
			$date = $dates[2].'.'.$dates[1];//.'.'.$dates[0]
			
			$url_del = !isset($_SESSION['admin']) ? '' : '<br><br><a href="javascript:delFlat('.$row['id'].');">Удалить</a>';
			if ($user_id) {
				$url_del .= ' <a href="javascript:update('.$row['id'].');" title="Обновить дату на текущую"><img src="/images/icon_update.png" title="Обновить дату на сегодняшнюю"></a>';
				$url_del .= ' <a href="flat.html?action=edit&id='.$row['id'].'"><img src="/images/icon_edit.png" title="Редактировать"></a>';
				$url_del .= ' <a href="javascript:remove('.$row['id'].');"><img src="/images/icon_delete.png" title="Снять"></a>';
				$url_del .= ' <a href="javascript:sold('.$row['id'].');"><img src="/images/icon_sold.png" title="Продано"></a>'; 
			}
			if ($row['balcony']>0) $balcon = 'балкон '.$row['balcony'].'м<sup>2</sup>';
			elseif ($row['loggia']>0) $balcon = 'лоджия '.$row['loggia'].'м<sup>2</sup>';
			else $balcon = 'балкона нет'; 			
			$html .= "<tr>			
			<td style='text-align:right;'><b>{$row['rooms']}-к.</b></td>
			<td><b>$price</b><br><br>$price_m</td>
			<td>{$row['storey']}/{$row['storeys']}<br><a href='/tenement.html?action=view&id={$row['tenement_id']}' target='_blank'>$type</a></td>
			<td><a href='$flat_url'>$addr</a></td>						
			<td>{$row['total_area']}/{$row['kitchen_area']}</td>
			<td>$balcon</td>			
			<td>$description</td>
			<td>$tel</td>
			<td>$date</td>			
			<td><a href='$flat_url'><img src='/images/icon_view.png' title='Смотреть'></a>$url_del</td>
			</tr>			
			";
		}
		$html .= "</tbody></table>
		<div>$paginator</div>		
		";
		return array('html'=>$html,'ids'=>$ids);
	}	
	
	public static function getCommercialList($where, $order, $direction, $per_page=10, $current_page=1, $action='sales', $user_id=NULL) {		
		$start_pos = ($current_page-1)*$per_page;
		$limit = " LIMIT $start_pos,$per_page";
		$sort = ($order=='') ? "f.updated_on" : $order;
		$where_param = $where." GROUP BY f.id ORDER BY ".$sort." ".$direction.$limit;
		$params = '';
		$price_min=0;
		$price_max=0;
		if (isset($_REQUEST['f_price'])) {
			$f_price = intval($_REQUEST['f_price']);
			$params .= '&f_price='.$f_price;
			switch ($f_price) {
				case 1:
					$price_max = 1000000;
					break;
				case 2:
					$price_min = 1000000;
					$price_max = 1500000;
					break;
				case 3:
					$price_min = 1500000;
					$price_max = 2000000;					
					break;
				case 4:
					$price_min = 2000000;
					$price_max = 3000000;					
					break;
				case 5:					
					$price_min = 3000000;
					break;
			}
		}
		else {
			if (isset($_REQUEST['price_min']) && $_REQUEST['price_min']>0) $price_min = intval($_REQUEST['price_min']);
			else $price_min = 0;
			if (isset($_REQUEST['price_max']) && $_REQUEST['price_max']>0) $price_max = intval($_REQUEST['price_max']);
			else $price_max = 0;
			
			if ($price_min>0) $params .= '&price_min='.$price_min;
			if ($price_max>0) $params .= '&price_max='.$price_max;
		}
		if (isset($_REQUEST['f_price_sq'])) {
			$params .= '&f_price_sq='.intval($_REQUEST['f_price_sq']);
		}
		if (isset($_REQUEST['f_radius'])) {
			$params .= '&f_radius='.intval($_REQUEST['f_radius']);
		}
		if (isset($_REQUEST['f_house'])) {
			$params .= '&f_house='.intval($_REQUEST['f_house']);
		}
		if (isset($_REQUEST['f_total_area'])) {
			$params .= '&f_total_area='.intval($_REQUEST['f_total_area']);
		}
		if (isset($_REQUEST['f_city']) && isset($_REQUEST['f_city_id'])) {
			$params .= '&f_city='.clearTextData($_REQUEST['f_city']);
			$params .= '&f_city_id='.intval($_REQUEST['f_city_id']);
		}
		if (isset($_REQUEST['f_photo'])) {
			$params .= '&f_photo='.intval($_REQUEST['f_photo']);
		}
		if (isset($_SESSION['direction'])) {
			$direction = ($_SESSION['direction']=='ASC') ? 'ASC' : 'DESC';
			$arrow = ($direction=='ASC') ? '&darr;' : '&uarr;';
			$direction = 'direction='.$direction;						
		}
		else {
			$direction = '';
			$arrow = '';
		}		
		$arrow_date = '';
		$arrow_price = '';
		if ($sort=='updated_on') $arrow_date = $arrow;
		else if ($sort=='price') $arrow_price = $arrow;
		
		$amount = Commercial::getAmountInList($where." GROUP BY f.id");
		
		$paginator = self::paginator("commercial.html?action=$action".$params,$amount,$per_page,$current_page);		
		$html = "
		Найдено $amount объявлений:
		<br>						
		<table class='table table-striped table-bordered table-condensed'>
		<thead>
		 <tr>
		 <th>Адрес</th>
		 <th><a href='/commercial.html?action=$action&sort=price".$params."'><b>Цена(руб.)</b></a>$arrow_price<br>руб./м<sup>2</sup></th>
		 <th>Тип</th>		 		 		 		 
		 <th>Фото</th>		 		 
		 <th>Общая м<sup>2</sup></th>		 		 		 
		 <th>Описание</th>
		 <th><a href='/commercial.html?action=$action&sort=updated_on".$params."'><b>Дата</b></a>$arrow_date</th>		 
		 <th></th>
		 </tr>
		</thead>
		<tbody>
		";
		$ids = array();
		$db_res = Commercial::getFullListLink($where_param);				
		while ($row = $db_res->fetchRow()) {
			$ids[] = $row['id'];
			$type_name = Commercial::$TYPE[$row['type_id']];					
			$price = number_format($row['price'],0);
			$price_m = number_format($row['price_m'],0);
			//$city = ($row['city_id']==0) ? '' : $row['city'].',';
			//$addr = "{$row['city']} {$row['street']}";
			$addr = $row['city'];
			if ($row['tenement_id']>0) {
				if ($row['street']!='') $addr .= ', '.$row['street'];
				if ($row['tnum']!='') $addr .= ', д.'.$row['tnum'];	
			}
			else {
				if ($row['street_name']!='') $addr .= ', '.$row['street_name'];
				if ($row['number']!='') $addr .= ', д.'.$row['number'];		
			}
						
			//$hr = ($desc_all!='') ? '<hr>' : ''; 
			//$description = $desc_all.$hr.$row['contacts'];
			$is_owner = $row['is_owner'] ? '<br><b>Собственник</b>' : '';
			$description = textReduce($row['description']).$is_owner;
			$flat_url = "/commercial.html?action=view&id={$row['id']}";
			$photo_html = ($row['photo']!='') ? "<a href='$flat_url'><img src='/".PHOTOS_WEBPATH.COMMERCIAL."/".$row['id']."/".$row['photo']."_prev'></a>" : '';						
			$photo_html2 = ($row['photo_tenement']!='') ? "<a href='$flat_url' ><img src='/".PHOTOS_WEBPATH.TENEMENT."/".$row['tenement_id']."/".$row['photo_tenement']."_prev' class='border_const'></a>" : 'Нет фото';			
			if ($photo_html == '') $photo_html = $photo_html2;
								
			$date = explode(' ',$row['updated_on']);
			$dates = explode('-',$date[0]);
			$date = $dates[2].'.'.$dates[1];//.'.'.$dates[0]
			$url_del = !isset($_SESSION['admin']) ? '' : '<br><br><a href="javascript:delObject('.$row['id'].');">Удалить</a>';
			if ($user_id) {
				$url_del .= ' <a href="javascript:update('.$row['id'].');" title="Обновить дату на текущую"><img src="/images/icon_update.png" title="Обновить дату на сегодняшнюю"></a>';
				$url_del .= ' <a href="commercial.html?action=edit&id='.$row['id'].'"><img src="/images/icon_edit.png" title="Редактировать"></a>';
				$url_del .= ' <a href="javascript:remove('.$row['id'].');"><img src="/images/icon_delete.png" title="Снять"></a>';
				$url_del .= ' <a href="javascript:sold('.$row['id'].');"><img src="/images/icon_sold.png" title="Продано"></a>'; 
			}
			//$dist = ($row['dist']>0) ? $row['dist'].' км' : '';
			$html .= "<tr>
			<td><a href='$flat_url'>$addr</a></td>						
			<td><b>$price</b><br><br>$price_m</td>
			<td>{$type_name}</td>			
			<td>$photo_html</td>			
			<td>{$row['total_area']}</td>							
			<td>$description</td>
			<td>$date</td>
			<td><a href='$flat_url'><img src='/images/icon_view.png' title='Смотреть'></a>$url_del</td>
			</tr>			
			";
		}
		$html .= "</tbody></table>
		<div>$paginator</div>		
		";
		return array('html'=>$html,'ids'=>$ids);
	}	
	
	public static function getHouseList($where, $order, $direction, $per_page=10, $current_page=1, $action='sales', $user_id=NULL) {		
		$start_pos = ($current_page-1)*$per_page;
		$limit = " LIMIT $start_pos,$per_page";
		$sort = ($order=='') ? "f.updated_on" : $order;
		$where_param = $where." GROUP BY f.id ORDER BY ".$sort." ".$direction.$limit;
		$params = '';
		$price_min=0;
		$price_max=0;
		if (isset($_REQUEST['f_price'])) {
			$f_price = intval($_REQUEST['f_price']);
			$params .= '&f_price='.$f_price;
			switch ($f_price) {
				case 1:
					$price_max = 1000000;
					break;
				case 2:
					$price_min = 1000000;
					$price_max = 1500000;
					break;
				case 3:
					$price_min = 1500000;
					$price_max = 2000000;					
					break;
				case 4:
					$price_min = 2000000;
					$price_max = 3000000;					
					break;
				case 5:					
					$price_min = 3000000;
					break;
			}
		}
		else {
			if (isset($_REQUEST['price_min']) && $_REQUEST['price_min']>0) $price_min = intval($_REQUEST['price_min']);
			else $price_min = 0;
			if (isset($_REQUEST['price_max']) && $_REQUEST['price_max']>0) $price_max = intval($_REQUEST['price_max']);
			else $price_max = 0;
			
			if ($price_min>0) $params .= '&price_min='.$price_min;
			if ($price_max>0) $params .= '&price_max='.$price_max;
		}
		if (isset($_REQUEST['f_price_sq'])) {
			$params .= '&f_price_sq='.intval($_REQUEST['f_price_sq']);
		}
		if (isset($_REQUEST['f_radius'])) {
			$params .= '&f_radius='.intval($_REQUEST['f_radius']);
		}
		if (isset($_REQUEST['f_house'])) {
			$params .= '&f_house='.intval($_REQUEST['f_house']);
		}
		if (isset($_REQUEST['f_total_area'])) {
			$params .= '&f_total_area='.intval($_REQUEST['f_total_area']);
		}
		if (isset($_REQUEST['f_city']) && isset($_REQUEST['f_city_id'])) {
			$params .= '&f_city='.clearTextData($_REQUEST['f_city']);
			$params .= '&f_city_id='.intval($_REQUEST['f_city_id']);
		}
		if (isset($_REQUEST['f_photo'])) {
			$params .= '&f_photo='.intval($_REQUEST['f_photo']);
		}
		if (isset($_SESSION['direction'])) {
			$direction = ($_SESSION['direction']=='ASC') ? 'ASC' : 'DESC';
			$arrow = ($direction=='ASC') ? '&darr;' : '&uarr;';
			$direction = 'direction='.$direction;						
		}
		else {
			$direction = '';
			$arrow = '';
		}		
		$arrow_date = '';
		$arrow_price = '';
		if ($sort=='updated_on') $arrow_date = $arrow;
		else if ($sort=='price') $arrow_price = $arrow;
		
		$amount = House::getAmountInList($where." GROUP BY f.id");
		
		$paginator = self::paginator("house.html?action=$action".$params,$amount,$per_page,$current_page);		
		$html = "
		Найдено $amount объявлений:
		<br>						
		<table class='table table-striped table-bordered table-condensed'>
		<thead>
		 <tr>
		 <th>Адрес</th>
		 <th><a href='/house.html?action=$action&sort=price".$params."'><b>Цена(руб.)</b></a>$arrow_price<br>руб./м<sup>2</sup></th>
		 <th>Этажей</th>		 		 		 		 
		 <th>Фото</th>		 		 
		 <th>Общая м<sup>2</sup></th>
		 <th>Удаленность</th>		 		 
		 <th>Описание</th>
		 <th><a href='/house.html?action=$action&sort=updated_on".$params."'><b>Дата</b></a>$arrow_date</th>		 
		 <th></th>
		 </tr>
		</thead>
		<tbody>
		";
		$ids = array();
		$db_res = House::getFullListLink($where_param);
		$tenement_icons = array('derevo','kirpich','monolit','block');		
		while ($row = $db_res->fetchRow()) {
			$ids[] = $row['id'];
			$type_name = House::$TYPE[$row['type_id']];
			$type = $tenement_icons[$row['type_id']];
			$type = "<img src='/images/icon_{$type}.png' title='{$type_name}'>";		
			$price = number_format($row['price'],0);
			$price_m = number_format($row['price_m'],0);
			//$city = ($row['city_id']==0) ? '' : $row['city'].',';
			//$addr = "{$row['city']} {$row['street']}";
			$addr = "{$row['city']}";
			//$hr = ($desc_all!='') ? '<hr>' : ''; 
			//$description = $desc_all.$hr.$row['contacts'];
			$is_owner = $row['is_owner'] ? '<br><b>Собственник</b>' : '';
			$description = textReduce($row['description']).$is_owner;
			$flat_url = "/house.html?action=view&id={$row['id']}";
			$photo_html = ($row['photo']!='') ? "<a href='$flat_url'><img src='/".PHOTOS_WEBPATH.HOUSE."/".$row['id']."/".$row['photo']."_prev'></a>" : 'Нет фото';			
			$date = explode(' ',$row['updated_on']);
			$dates = explode('-',$date[0]);
			$date = $dates[2].'.'.$dates[1];//.'.'.$dates[0]
			$url_del = !isset($_SESSION['admin']) ? '' : '<br><br><a href="javascript:delObject('.$row['id'].');">Удалить</a>';
			if ($user_id) {
				$url_del .= ' <a href="javascript:update('.$row['id'].');" title="Обновить дату на текущую"><img src="/images/icon_update.png" title="Обновить дату на сегодняшнюю"></a>';
				$url_del .= ' <a href="house.html?action=edit&id='.$row['id'].'"><img src="/images/icon_edit.png" title="Редактировать"></a>';
				$url_del .= ' <a href="javascript:remove('.$row['id'].');"><img src="/images/icon_delete.png" title="Снять"></a>';
				$url_del .= ' <a href="javascript:sold('.$row['id'].');"><img src="/images/icon_sold.png" title="Продано"></a>'; 
			}
			$dist = ($row['dist']>0) ? $row['dist'].' км' : '';
			$html .= "<tr>
			<td><a href='$flat_url'>$addr</a></td>						
			<td><b>$price</b><br><br>$price_m</td>
			<td>{$row['storeys']}<br>$type</td>			
			<td>$photo_html</td>			
			<td>{$row['total_area']}</td>
			<td>{$dist}</td>				
			<td>$description</td>
			<td>$date</td>
			<td><a href='$flat_url'><img src='/images/icon_view.png' title='Смотреть'></a>$url_del</td>
			</tr>			
			";
		}
		$html .= "</tbody></table>
		<div>$paginator</div>		
		";
		return array('html'=>$html,'ids'=>$ids);
	}
	
	public static function getLandList($where, $order, $direction, $per_page=10, $current_page=1, $action='sales', $user_id=NULL) {		
		$start_pos = ($current_page-1)*$per_page;
		$limit = " LIMIT $start_pos,$per_page";
		$sort = ($order=='') ? "f.updated_on" : $order;
		$where_param = $where." GROUP BY f.id ORDER BY ".$sort." ".$direction.$limit;
		$params = '';
		$price_min=0;
		$price_max=0;
		if (isset($_REQUEST['f_price'])) {
			$f_price = intval($_REQUEST['f_price']);
			$params .= '&f_price='.$f_price;
			switch ($f_price) {
				case 1:
					$price_max = 100000;
					break;
				case 2:
					$price_min = 100000;
					$price_max = 300000;
					break;
				case 3:
					$price_min = 300000;
					$price_max = 600000;					
					break;
				case 4:
					$price_min = 600000;
					$price_max = 1000000;					
					break;
				case 5:					
					$price_min = 1000000;
					break;
			}
		}
		else {
			if (isset($_REQUEST['price_min']) && $_REQUEST['price_min']>0) $price_min = intval($_REQUEST['price_min']);
			else $price_min = 0;
			if (isset($_REQUEST['price_max']) && $_REQUEST['price_max']>0) $price_max = intval($_REQUEST['price_max']);
			else $price_max = 0;
			
			if ($price_min>0) $params .= '&price_min='.$price_min;
			if ($price_max>0) $params .= '&price_max='.$price_max;
		}
		if (isset($_REQUEST['f_price_sq'])) {
			$params .= '&f_price_sq='.intval($_REQUEST['f_price_sq']);
		}
		if (isset($_REQUEST['f_area'])) {
			$params .= '&f_area='.intval($_REQUEST['f_area']);
		}
		if (isset($_REQUEST['f_photo'])) {
			$params .= '&f_photo='.intval($_REQUEST['f_photo']);
		}		
		if (isset($_SESSION['direction'])) {
			$direction = ($_SESSION['direction']=='ASC') ? 'ASC' : 'DESC';
			$arrow = ($direction=='ASC') ? '&darr;' : '&uarr;';
			$direction = 'direction='.$direction;						
		}
		else {
			$direction = '';
			$arrow = '';
		}		
		$arrow_date = '';
		$arrow_price = '';
		if ($sort=='updated_on') $arrow_date = $arrow;
		else if ($sort=='price') $arrow_price = $arrow;
		
		$amount = Land::getAmountInList($where." GROUP BY f.id");
		
		$paginator = self::paginator("land.html?action=$action".$params,$amount,$per_page,$current_page);		
		$html = "
		Найдено $amount объявлений:
		<br>						
		<table class='table table-striped table-bordered table-condensed'>
		<thead>
		 <tr>
		 <th>Адрес</th>
		 <th><a href='/land.html?action=$action&sort=price".$params."'><b>Цена(руб.)</b></a>$arrow_price<br>руб./сот.</th>		 		 		 		 
		 <th>Фото</th>		 		 
		 <th>Площадь (соток)</th>
		 <th>Удаленность</th>		 		 
		 <th>Описание</th>
		 <th><a href='/land.html?action=$action&sort=updated_on".$params."'><b>Дата</b></a>$arrow_date</th>		 
		 <th></th>
		 </tr>
		</thead>
		<tbody>
		";
		$ids = array();
		$db_res = Land::getFullListLink($where_param);
				
		while ($row = $db_res->fetchRow()) {
			$ids[] = $row['id'];				
			$price = number_format($row['price'],0);
			$price_m = number_format($row['price_h'],0);
			$addr = "{$row['city']}";
						
			$dots = (strlen($row['description'])>256) ? '...' : '';
			$desc = str_replace('.','. ',$row['description']);
			$desc = str_replace(',',', ',$desc);
			$desc = str_replace('-',' - ',$desc);
			$desc = str_replace('. ,','.,',$desc);
			$desc_all = substr($desc,0,255).$dots;
			
			//$hr = ($desc_all!='') ? '<hr>' : ''; 
			//$description = $desc_all.$hr.$row['contacts'];
			$is_owner = $row['is_owner'] ? '<br><b>Собственник</b>' : '';
			$description = $desc_all.$is_owner;
			$flat_url = "/land.html?action=view&id={$row['id']}";
			$photo_html = ($row['photo']!='') ? "<a href='$flat_url'><img src='/".PHOTOS_WEBPATH.LAND."/".$row['id']."/".$row['photo']."_prev'></a>" : 'Нет фото';			
			$date = explode(' ',$row['updated_on']);
			$dates = explode('-',$date[0]);
			$date = $dates[2].'.'.$dates[1];//.'.'.$dates[0]
			$url_del = !isset($_SESSION['admin']) ? '' : '<br><br><a href="javascript:delObject('.$row['id'].');">Удалить</a>';
			if ($user_id) {
				$url_del .= ' <a href="javascript:update('.$row['id'].');" title="Обновить дату на текущую"><img src="/images/icon_update.png" title="Обновить дату на сегодняшнюю"></a>';
				$url_del .= ' <a href="land.html?action=edit&id='.$row['id'].'"><img src="/images/icon_edit.png" title="Редактировать"></a>';
				$url_del .= ' <a href="javascript:remove('.$row['id'].');"><img src="/images/icon_delete.png" title="Снять"></a>';
				$url_del .= ' <a href="javascript:sold('.$row['id'].');"><img src="/images/icon_sold.png" title="Продано"></a>'; 
			}
			$dist = ($row['dist']>0) ? $row['dist'].' км' : '';
			$html .= "<tr>
			<td><a href='$flat_url'>$addr</a></td>						
			<td><b>$price</b><br><br>$price_m</td>						
			<td>$photo_html</td>			
			<td>{$row['area']}</td>
			<td>{$dist}</td>				
			<td>$description</td>
			<td>$date</td>
			<td><a href='$flat_url'><img src='/images/icon_view.png' title='Смотреть'></a>$url_del</td>
			</tr>			
			";
		}
		$html .= "</tbody></table>
		<div>$paginator</div>		
		";
		return array('html'=>$html,'ids'=>$ids);
	}
	
	
	public static function pageFlatMap() {
		return '<a href="javascript:showFlatByCor()">Все квартиры</a> &#8226;                           
    <a href="javascript:showBySize(1)">1-комн.</a> &#8226; 
    <a href="javascript:showBySize(2)">2-комн.</a> &#8226; 
    <a href="javascript:showBySize(3)">3-комн. и более</a> 
    &nbsp;
    <font style="background-color:#FFFFFF;">Цвет: | до 30 000 руб./м<sup>2</sup></font>
    <font style="background-color:#FEE800;"> | 30 000 - 40 000 руб./м<sup>2</sup></font>
    <font style="background-color:#FFAE00;"> | более 40 000 руб./м<sup>2</sup></font>
    <hr>
    <div id="YMapsID-3050" style="width:100%;height:600px;"></div>';
	}
	
	public static function pageCommercialView(Commercial $commercial,$address,$act) {		
		$properties_val = $commercial->getPropertiesVal();
		$vals_html = Html::getViewRealty($properties_val);						
		$photos = $commercial->getPhotos();
		$photo_path = $commercial->getPhotoWebPath();		 
		$photo_gallery_html = Html::getPhotosGallery($photos,$photo_path);
				
		if ($commercial->tenement_id>0) {
			$tenement = new Tenement();
			$tenement->getFull($commercial->tenement_id);
			$photos_tenement = $tenement->getPhotos();
			$photo_path_tenement = $tenement->getPhotoWebPath();		 
			$photo_gallery_html_tenement = Html::getPhotosGallery($photos_tenement,$photo_path_tenement);
			$photo_gallery_html .= $photo_gallery_html_tenement;
			
		}		
		//lon=47 - долгота
		//lat=56 - широта
		if ($commercial->lat>0 && $commercial->lon>0) 
			$map = self::getMap($commercial->lon,$commercial->lat,16);
		elseif ($commercial->tlat>0 && $commercial->tlon>0) 
			$map = self::getMap($commercial->tlon,$commercial->tlat,16);
		else 
			$map = '';
		$date = formatDateExact($commercial->created_on);
		$date_up = formatDateExact($commercial->updated_on);
		$counter_html = "<br><br>
		<div class='flat_view'>Обновлено: <b>$date_up</b></div>
		<div class='flat_view'>Размещено: <b>$date</b></div>
		<div class='flat_view'>Просмотров в списке: <b>$commercial->quick_views</b></div>
		<div class='flat_view'>Просмотров подробно: <b>$commercial->counter_views</b></div>
		<p><b>Пожалуйста сообщите риэлтору что Вы нашли объявление на сайте mari12.ru. Так Вы поможете развитию портала!</b></p>
		";
		
		$struct_html = '<table width="100%" class="content">
		<tr>
		<td style="text-align:left; vertical-align:top;" width="500px;">'.$vals_html.$counter_html.'</td>
		<td style="vertical-align:top;">'.$map.$photo_gallery_html.'
		<div id="div_history" class="base_text"></div>		
		</td>
		</tr>		
		</table>
		';
		
		$is_admin = 0;
		$id = intval($_REQUEST['id']);
		$url_edit = false;
		$url_apply = false;
		$url_sold = false;
		$url_delete = false;
		
		if (isset($_SESSION['last_commercial_id']) && $id==$_SESSION['last_commercial_id'] && !isset($_SESSION['admin']) && ($commercial->status==REALTY_STATUS_NEW || $commercial->status==REALTY_STATUS_RENT_NEW)) {
			if ($commercial->status==REALTY_STATUS_NEW) {
				$status = REALTY_STATUS_APPLY;
						
			}
			else {
				$status = REALTY_STATUS_RENT_APPLY;
				
			}		
			$url_edit = Html::getUrl('commercial','edit',$commercial->id);
			$url_apply = Html::getUrl('commercial','apply',$commercial->id,'&status='.$status);
		}
		
		if (isset($_SESSION['user_id']) && $_SESSION['user_id']==$commercial->user_id || isset($_SESSION['last_commercial_id']) && $id==$_SESSION['last_commercial_id']) {		
			$url_edit = Html::getUrl('commercial','edit',$commercial->id);
		}
		if (isset($_SESSION['admin'])) {
			$is_admin = 1;
			$url_edit = Html::getUrl('commercial','edit',$commercial->id);
			$url_sold = Html::getUrl('commercial','sold',$commercial->id);
			$status = -1;
			if ($commercial->status==REALTY_STATUS_APPLY) {
				$status = REALTY_STATUS_SALE;			
			}
			elseif ($commercial->status==REALTY_STATUS_RENT_APPLY) {
				$status = REALTY_STATUS_RENT;
			}
			if ($status>-1) $url_approve = Html::getUrl('commercial','approve',$commercial->id,'&status='.$status);				
			$url_delete  = Html::getUrl('commercial','delete',$commercial->id);		
		}		
		$html_page = '';
		if ($url_edit)
	 		$html_page .= '<input type="button" onclick="location=\''.$url_edit.'\'" value="Редактировать (добавить фотографии)" class="btn btn-primary">';
	
		if (isset($_SESSION['last_commercial_id']) && $id==$_SESSION['last_commercial_id'] && $url_apply)
			$html_page .= '&nbsp;<input type="button" onclick="location=\''.$url_apply.'\'" value="Отправить объявление на проверку" class="btn btn-success">';
		 
		if ($is_admin && $url_approve)
			$html_page .= '&nbsp;<input type="button" onclick="location=\''.$url_approve.'\'" value="Активировать объявление" class="btn btn-success">';
		 
		if ($is_admin && $url_delete)  
			$html_page .= '&nbsp;<input type="button" onclick="location=\''.$url_delete.'\'" value="Удалить объявление" class="btn btn-danger">';
		 
		if ($is_admin && $url_sold)
			$html_page .= '&nbsp;<input type="button" onclick="location=\''.$url_sold.'\'" value="Продано" class="btn btn-info">';  
			
		$block_html = Html::getBlock($act.' коммерческой недвижимости: '.$address,$struct_html.$html_page);
		return $block_html; 
	}
	
	
	public static function pageHouseView(House $house) {		
		$properties_val = $house->getPropertiesVal();
		$vals_html = Html::getViewRealty($properties_val);
		if ($house->user_id>0) {
			$val = $house->company_name;
			if ($house->tariff_id==TARIFF_PAID) {
				$val = "<a href='http://".$house->domain.".".$_SERVER['HTTP_HOST']."' target='_blank'>$val</a>";
			}
			$vals_html.='<div class="flat_view">Автор: <b>'.$val.'</b></div>';
		}				
		$address = $house->city;
		if ($house->street!='') $address .= ', '.$house->street;		
		$photos = $house->getPhotos();
		$photo_tenement_path = $house->getPhotoWebPath(); 
		$photo_gallery_html = Html::getPhotosGallery($photos,$photo_tenement_path);
		//lon=47 - долгота
		//lat=56 - широта
		$map = ($house->lat>0 && $house->lon>0) ? self::getMap($house->lon,$house->lat,10) : '';
		
		$date = formatDateExact($house->created_on);
		$date_up = formatDateExact($house->updated_on);
		$counter_html = "<br><br>
		<div class='flat_view'>Обновлено: <b>$date_up</b></div>
		<div class='flat_view'>Размещено: <b>$date</b></div>
		<div class='flat_view'>Просмотров в списке: <b>$house->quick_views</b></div>
		<div class='flat_view'>Просмотров подробно: <b>$house->counter_views</b></div>
		<p><b>Пожалуйста сообщите риэлтору что Вы нашли объявление на сайте mari12.ru. Так Вы поможете развитию портала!</b></p>
		";
		
		$struct_html = '<table width="100%" class="content">
		<tr>
		<td style="text-align:left; vertical-align:top;" width="500px;">'.$vals_html.$counter_html.'</td>
		<td style="vertical-align:top;">'.$map.$photo_gallery_html.'
		<div id="div_history" class="base_text"></div>		
		</td>
		</tr>		
		</table>
		';
		
		$is_admin = 0;
		$id = intval($_REQUEST['id']);
		$url_edit = false;
		$url_apply = false;
		$url_sold = false;
		$url_delete = false;
		if (isset($_SESSION['last_house_id']) && $id==$_SESSION['last_house_id'] && !isset($_SESSION['admin']) && ($house->status==REALTY_STATUS_NEW || $flat->status==REALTY_STATUS_RENT_NEW)) {
			if ($house->status==REALTY_STATUS_NEW) {
				$status = REALTY_STATUS_APPLY;
			}
			else {
				$status = REALTY_STATUS_RENT_APPLY;
			}		
			$url_edit = Html::getUrl('house','edit',$house->id);
			$url_apply = Html::getUrl('house','apply',$house->id,'&status='.$status);
		}
		
		if (isset($_SESSION['user_id']) && $_SESSION['user_id']==$house->user_id || isset($_SESSION['last_house_id']) && $id==$_SESSION['last_house_id']) {		
			$url_edit = Html::getUrl('house','edit',$house->id);
		}
		if (isset($_SESSION['admin'])) {
			$is_admin = 1;
			$url_edit = Html::getUrl('house','edit',$house->id);
			$url_sold = Html::getUrl('house','sold',$house->id);
			$status = -1;
			if ($house->status==REALTY_STATUS_APPLY) {
				$status = REALTY_STATUS_SALE;			
			}
			elseif ($house->status==REALTY_STATUS_RENT_APPLY) {
				$status = REALTY_STATUS_RENT;
			}
			if ($status>-1) $url_approve = Html::getUrl('house','approve',$house->id,'&status='.$status);				
			$url_delete  = Html::getUrl('house','delete',$house->id);		
		}		
		$html_page = '';
		if ($url_edit)
	 		$html_page .= '<input type="button" onclick="location=\''.$url_edit.'\'" value="Редактировать (добавить фотографии)" class="btn btn-primary">';
	
		if (isset($_SESSION['last_house_id']) && $id==$_SESSION['last_house_id'] && $url_apply)
			$html_page .= '&nbsp;<input type="button" onclick="location=\''.$url_apply.'\'" value="Отправить объявление на проверку" class="btn btn-success">';
		 
		if ($is_admin && $url_approve)
			$html_page .= '&nbsp;<input type="button" onclick="location=\''.$url_approve.'\'" value="Активировать объявление" class="btn btn-success">';
		 
		if ($is_admin && $url_delete)  
			$html_page .= '&nbsp;<input type="button" onclick="location=\''.$url_delete.'\'" value="Удалить объявление" class="btn btn-danger">';
		 
		if ($is_admin && $url_sold)
			$html_page .= '&nbsp;<input type="button" onclick="location=\''.$url_sold.'\'" value="Продано" class="btn btn-info">';  
			
		$block_html = Html::getBlock('Продажа частного дома: '.$address,$struct_html.$html_page);
		return $block_html; 
	}

	public static function pageLandView(Land $land) {		
		$properties_val = $land->getPropertiesVal();
		$vals_html = Html::getViewRealty($properties_val);
		if ($land->user_id>0) {
			$val = $land->company_name;
			if ($land->tariff_id==TARIFF_PAID) {
				$val = "<a href='http://".$land->domain.".".$_SERVER['HTTP_HOST']."' target='_blank'>$val</a>";
			}
			$vals_html.='<div class="flat_view">Автор: <b>'.$val.'</b></div>';
		}				
		
		$address = $land->city;				
		$photos = $land->getPhotos();
		$photo_tenement_path = $land->getPhotoWebPath(); 
		$photo_gallery_html = Html::getPhotosGallery($photos,$photo_tenement_path);
		//lon=47 - долгота
		//lat=56 - широта
		$map = ($land->lat>0 && $land->lon>0) ? self::getMap($land->lon,$land->lat,10) : '';
		
		$date = formatDateExact($land->created_on);
		$date_up = formatDateExact($land->updated_on);
		$counter_html = "<br><br>
		<div class='flat_view'>Обновлено: <b>$date_up</b></div>
		<div class='flat_view'>Размещено: <b>$date</b></div>
		<div class='flat_view'>Просмотров в списке: <b>$land->quick_views</b></div>
		<div class='flat_view'>Просмотров подробно: <b>$land->counter_views</b></div>
		<p><b>Пожалуйста сообщите риэлтору что Вы нашли объявление на сайте mari12.ru. Так Вы поможете развитию портала!</b></p>
		";
		
		$struct_html = '<table width="100%" class="content">
		<tr>
		<td style="text-align:left; vertical-align:top;" width="500px;">'.$vals_html.$counter_html.'</td>
		<td style="vertical-align:top;">'.$map.$photo_gallery_html.'
		</td>
		</tr>		
		</table>
		';
		
		$is_admin = 0;
		$id = intval($_REQUEST['id']);
		$url_edit = false;
		$url_apply = false;
		$url_sold = false;
		$url_delete = false;
		if (isset($_SESSION['last_land_id']) && $id==$_SESSION['last_land_id'] && !isset($_SESSION['admin']) && ($land->status==REALTY_STATUS_NEW || $flat->status==REALTY_STATUS_RENT_NEW)) {
			if ($land->status==REALTY_STATUS_NEW) {
				$status = REALTY_STATUS_APPLY;
			}
			else {
				$status = REALTY_STATUS_RENT_APPLY;
			}		
			$url_edit = Html::getUrl('land','edit',$land->id);
			$url_apply = Html::getUrl('land','apply',$land->id,'&status='.$status);
		}
		
		if (isset($_SESSION['user_id']) && $_SESSION['user_id']==$land->user_id || isset($_SESSION['last_land_id']) && $id==$_SESSION['last_land_id']) {		
			$url_edit = Html::getUrl('land','edit',$land->id);
		}
		if (isset($_SESSION['admin'])) {
			$is_admin = 1;
			$url_edit = Html::getUrl('land','edit',$land->id);
			$url_sold = Html::getUrl('land','sold',$land->id);
			$status = -1;
			if ($land->status==REALTY_STATUS_APPLY) {
				$status = REALTY_STATUS_SALE;			
			}
			elseif ($land->status==REALTY_STATUS_RENT_APPLY) {
				$status = REALTY_STATUS_RENT;
			}
			if ($status>-1) $url_approve = Html::getUrl('land','approve',$land->id,'&status='.$status);				
			$url_delete  = Html::getUrl('land','delete',$land->id);		
		}		
		$html_page = '';
		if ($url_edit)
	 		$html_page .= '<input type="button" onclick="location=\''.$url_edit.'\'" value="Редактировать (добавить фотографии)" class="btn btn-primary">';
	
		if (isset($_SESSION['last_land_id']) && $id==$_SESSION['last_land_id'] && $url_apply)
			$html_page .= '&nbsp;<input type="button" onclick="location=\''.$url_apply.'\'" value="Отправить объявление на проверку" class="btn btn-success">';
		 
		if ($is_admin && $url_approve)
			$html_page .= '&nbsp;<input type="button" onclick="location=\''.$url_approve.'\'" value="Активировать объявление" class="btn btn-success">';
		 
		if ($is_admin && $url_delete)  
			$html_page .= '&nbsp;<input type="button" onclick="location=\''.$url_delete.'\'" value="Удалить объявление" class="btn btn-danger">';
		 
		if ($is_admin && $url_sold)
			$html_page .= '&nbsp;<input type="button" onclick="location=\''.$url_sold.'\'" value="Продано" class="btn btn-info">';  
				
		$block_html = Html::getBlock('Продажа земельного участка: '.$address,$struct_html.$html_page);
		return $block_html; 
	}

	public static function pageCommercialEdit(Commercial $commercial) {		
		$defaults = array();
		$defaults['val'] = $commercial->getVals();
		$props = Commercial::$_properties;
		/*
		unset($props['city']);
		unset($props['street']);
		unset($props['number']);
		*/		
		$html_form = self::getForm($props,0,$defaults);
		$photos_html = self::getRealtyPhotosEdit($commercial);				
		$block_content = self::getCommercialFormEdit($html_form,$photos_html,$commercial);
		$block_html = self::getBlock('Редактирование объявления по коммерческой недвижимости',$block_content);
		return $block_html; 
	}
	
	
	public static function pageHouseEdit(House $house) {		
		$defaults = array();
		$defaults['val'] = $house->getVals();			
		$html_form = self::getForm(House::$_properties,0,$defaults);
		$photos_html = self::getRealtyPhotosEdit($house);				
		$block_content = self::getHouseFormEdit($html_form,$photos_html,$house);
		$block_html = self::getBlock('Редактирование объявления по дому',$block_content);
		return $block_html; 
	}
	
	public static function pageLandEdit(Land $land) {		
		$defaults = array();
		$defaults['val'] = $land->getVals();			
		$html_form = self::getForm(Land::$_properties,0,$defaults);
		$photos_html = self::getRealtyPhotosEdit($land);				
		$block_content = self::getLandFormEdit($html_form,$photos_html,$land);
		$block_html = self::getBlock('Редактирование объявления по земельному участку',$block_content);
		return $block_html; 
	}
	
	public static function getCommercialFormEdit($html_form, $photos_html,$commercial) {
		if ($commercial->tlon>0) {
			$commercial->lon = $commercial->tlon;
			$commercial->lat = $commercial->tlat;
		}
		$html = '<form method="post" enctype="application/x-www-form-urlencoded" name="EditObject">		
		<fieldset><legend>Описание коммерческой недвижимости</legend>
		 <table><tr><td class="tcontent">
		   <div class="form_convert" id="tenement_desc">
			'.$html_form.'
			</div>
		   </td><td class="tcontent">'.self::getMapAddTenement($commercial->lon,$commercial->lat,15,true).'</td></tr></table>		
		</fieldset>
		<fieldset><legend>Фотографии объекта</legend>	   
			<span id="spanButtonPlaceholder" style="border=1"></span><br>		   
		   <div id="divFileProgressContainerPhoto" style="height: 10px;"></div>
		   <div id="thumbnails'.COMMERCIAL.'">'.$photos_html.'</div>
		</fieldset>
		<div align=center>
		   <br>
		   <input type="hidden" name="action" value="edit">
		   <input type="submit" value="Сохранить" class="btn btn-success">
		   </div>		   		  
		</form>
		<script>
		YMaps.jQuery(window).load(function () {
			if ($("#lon").val()>0 && $("#lat").val()>0) {
				map.addOverlay(createObject("Placemark", new YMaps.GeoPoint($("#lon").val(),$("#lat").val()), "constructor#pmrdmPlacemark", "Объект"));
			}
		});		
		</script>		
		';
		return $html;		
	}
	
	
	public static function getHouseFormEdit($html_form, $photos_html,$house) {
		$html = '<form method="post" enctype="application/x-www-form-urlencoded" name="EditObject">		
		<fieldset><legend>Описание дома</legend>
		 <table><tr><td class="tcontent">
		   <div class="form_convert" id="tenement_desc">
			'.$html_form.'
			</div>
		   </td><td class="tcontent">'.self::getMapAddTenement($house->lon,$house->lat,15,true).'</td></tr></table>		
		</fieldset>
		<fieldset><legend>Фотографии дома</legend>	   
			<span id="spanButtonPlaceholder" style="border=1"></span><br>		   
		   <div id="divFileProgressContainerPhoto" style="height: 10px;"></div>
		   <div id="thumbnails'.HOUSE.'">'.$photos_html.'</div>
		</fieldset>
		<div align=center>
		   <br>
		   <input type="hidden" name="action" value="edit">
		   <input type="submit" value="Сохранить" class="btn btn-success">
		   </div>		   		  
		</form>
		<script>
		YMaps.jQuery(window).load(function () {
			if ($("#lon").val()>0 && $("#lat").val()>0) {
				map.addOverlay(createObject("Placemark", new YMaps.GeoPoint($("#lon").val(),$("#lat").val()), "constructor#pmrdmPlacemark", "Дом"));
			}
		});		
		</script>		
		';
		return $html;		
	}
	
	public static function getLandFormEdit($html_form, $photos_html,$land) {
		$html = '<form method="post" enctype="application/x-www-form-urlencoded" name="EditObject">		
		<fieldset><legend>Описание дома</legend>
		 <table><tr><td class="tcontent">
		   <div class="form_convert" id="tenement_desc">
			'.$html_form.'
			</div>
		   </td><td class="tcontent">'.self::getMapAddTenement($land->lon,$land->lat,15,true).'</td></tr></table>		
		</fieldset>
		<fieldset><legend>Фотографии земельного участка</legend>	   
			<span id="spanButtonPlaceholder" style="border=1"></span><br>		   
		   <div id="divFileProgressContainerPhoto" style="height: 10px;"></div>
		   <div id="thumbnails'.LAND.'">'.$photos_html.'</div>
		</fieldset>
		<div align=center>
		   <br>
		   <input type="hidden" name="action" value="edit">
		   <input type="submit" value="Сохранить" class="btn btn-success">
		   </div>		   		  
		</form>
		<script>
		YMaps.jQuery(window).load(function () {
			if ($("#lon").val()>0 && $("#lat").val()>0) {
				map.addOverlay(createObject("Placemark", new YMaps.GeoPoint($("#lon").val(),$("#lat").val()), "constructor#pmrdmPlacemark", "Земельный участок"));
			}
		});		
		</script>		
		';
		return $html;		
	}


    public static function getUserBoxMessage($id, $current_page=1, $per_page=10)
    {
        $start_pos = ($current_page-1)*$per_page;
        $limit = " LIMIT $start_pos,$per_page";
        $count_mes = Message::getCountMessage($id);
        $count_data=$count_mes->fetchRow();
        $order = " ORDER BY id DESC";
        $where="i.to_id='$id' ";
        $db_res = Message::getUserMessageList($where.$order.$limit);
        $paginator = self::paginator("message.html?action=box",$count_data['count'],$per_page,$current_page);
        $count=($count_data>0)? "У Вас не прочитанно ".$count_data['count']." сообщений": "У Вас нет не прочитанных сообщений";
        $html="
        <div>$paginator</div>
        <p align='center'>".$count."</p>";

        $html .= "
        <p>
		<table class='base_text'>
		<thead>
		<tr>
		 <th>Дата</th>
		 <th>Тема</th>
		</tr>
		</thead>
		<tbody>
		";

        while ($row = $db_res->fetchRow()) {
            $status=($row['status']==0)? '<b>'.$row['subject'].'</b>' : $row['subject'];
            $html .= "<tr>
			<td>{$row['create_date']}</td>
			<td><a href='/message.html?action=view&id=$row[id]'>{$status}</td>
			</tr>";
        }

        $html .= "</tbody></table>";

        return $html;
    }







	//==================== Admin ===============================================
    public static function getAdminBoxMessage($where)
    {
        $order = " ORDER BY id DESC";
        $db_res = Message::getAdminMessageList($where.$order);
        $html = "
        <div><a href='/adminarea/message.php?action=add'>Создать сообщение</a></div>
		<table class='base_text'>
		<thead>
		<tr>
		 <th>Дата</th>
		 <th>Тема</th>
		 <th>Статус</th>
		 <th></th>
		 <th></th>
		 <th></th>
		 <th></th>
		</tr>
		</thead>
		<tbody>
		";

        while ($row = $db_res->fetchRow()) {
            $status=($row['status']==1)? 'Отправлено' : 'Неотправлено';
            $html .= "<tr>
			<td>{$row['create_date']}</td>
			<td>{$row['subject']}</td>
			<td>{$status}</td>
			<td><a href='/adminarea/message.php?action=add&id={$row['id']}' target='_blank'>Редактировать</a></td>
			<td><a href='/adminarea/message.php?action=view&id={$row['id']}' target='_blank'>Просмотр</a></td>
			<td><a href='/adminarea/message.php?action=send&id={$row['id']}'>Отправить</a></td>
			<td><a href='javascript:delMessage({$row['id']});'>Удалить</a></td>
			</tr>";
        }

        $html .= "</tbody></table>
		<script>
		delMessage = function(id) {
			if (confirm('Вы уверены удалить это сообщение?')) {
				location.href = '/adminarea/message.php?action=delete&id='+id;
			}
			return 0;
		}
		</script>";
        return $html;
    }


    public static function getAdminFlatList($where, $per_page=10, $current_page=1,$action) {
		$start_pos = ($current_page-1)*$per_page;
		$limit = " LIMIT $start_pos,$per_page";
		$amount = Flat::getAmountInList($where);
		$order = " ORDER BY id DESC";
		$db_res = Flat::getAdminListLink($where.$order.$limit);
		//$flat->getFullBy($where.$order.$limit);
		$paginator = self::paginator("flat.php?action=$action",$amount,$per_page,$current_page);
		$html = "
		<div><a href='flat.html?action=activateAll'>Активировать все</a></div>
		<div>$paginator</div>
		<table class='base_text'>
		<thead>
		<tr>
		 <th>Комнат</th>
		 <th>Цена</th>
		 <th>Тип дома</th>
		 <th>Адрес</th>
		 <th>Юзер</th>
		 <th></th>
		 <th></th>
		 <th></th>
		</tr>
		</thead>
		<tbody>
		";
		$status = ($action=='newSales') ? REALTY_STATUS_SALE : REALTY_STATUS_RENT; 
		while ($row = $db_res->fetchRow()) {
			$type = Tenement::$TYPE[$row['ttype']];
			$price = number_format($row['price'],0);
			$city = ($row['city_id']==0) ? '' : $row['city'].',';
			$addr = $row['tenement_status']==REALTY_STATUS_NEW ? "(NEW!) $city {$row['street']}, {$row['tnum']}" : "$city {$row['street']}, {$row['tnum']}";
			$user = '';
			if ($row['user_name']!='') {
				$user = $row['user_name'];
			}
			if ($row['company_name']!='') {
				$user .= ' - '.$row['company_name'];
			}  
			$html .= "<tr>
			<td>{$row['rooms']}</td>
			<td>{$price}</td>
			<td>$type</td>			
			<td><a href='/tenement.html?action=edit&id={$row['tenement_id']}' target='_blank'>$addr</a></td>
			<td>$user</td>
			<td><a href='/flat.html?action=edit&id={$row['id']}' target='_blank'>Редактировать</a></td>
			<td><a href='/flat.html?action=view&id={$row['id']}' target='_blank'>Смотреть</a></td>
			<td><a href='javascript:delFlat({$row['id']});'>Удалить</a></td>
			<td><a href='/flat.html?action=approve&id={$row['id']}&status=$status' target='_blank'>Активировать</a></td>
			</tr>";
		}
		$html .= "</tbody></table>
		<script>		
		delFlat = function(id) {
			if (confirm('Вы уверены удалить эту квартиру?')) {
				location.href = '/flat.html?action=delete&id='+id;
			}
			return 0;			
		}				
		</script>";
		return $html;
	}	
	
	public static function getAdminTenementList($action, $per_page=10, $current_page=1) {				
		if ($action=='listActive') {
			$where = "t.status='".REALTY_STATUS_APPLY."'";
		}
		else if($action=='listNew') {
			$where = "t.status='".REALTY_STATUS_NEW."'";
		}		
		$start_pos = ($current_page-1)*$per_page;
		$limit = " LIMIT $start_pos,$per_page";
		$amount = Tenement::getAmountInList($where);
		$order = " ORDER BY id DESC";
		$db_res = Tenement::getFullListLink($where.$order.$limit);
		$paginator = self::paginator("tenement.php?action=$action",$amount,$per_page,$current_page);
		$html = "
		<div>$paginator</div>
		<div><a href='tenement.html?action=activateAll'>Активировать все</a></div>		
		<table class='base_text'>
		<thead>
		<tr>
		 <th>Адрес</th>		 
		 <th>Тип дома</th>
		 <th>Этажность</th>
		 <th>Карта</th>		 
		 <th></th>
		 <th></th>
		 <th></th>
		 <th></th>
		</tr>
		</thead>
		<tbody>
		";		
		while ($row = $db_res->fetchRow()) {
			$type = Tenement::$TYPE[$row['type_id']];
			$city = ($row['city_id']==0) ? '' : $row['city'].',';			
			$addr = "$city {$row['street']}, {$row['number']}";
			$map = ($row['lat']&&$row['lon']) ? 'Да' : '<b>Нет</b>'; 
			$html .= "<tr>
			<td>$addr</td>			
			<td>$type</td>
			<td>{$row['storeys']}</td>
			<td>$map</td>
			<td><a href='/tenement.html?action=edit&id={$row['id']}' target='_blank'>Редактировать</a></td>			
			<td><a href='/tenement.html?action=view&id={$row['id']}' target='_blank'>Смотреть</a></td>
			<td><a href='javascript:delTenement({$row['id']})'>Удалить</a></td>
			<td><a href='/tenement.html?action=approve&id={$row['id']}' target='_blank'>Активировать</a></td>
			</tr>";
		}
		$html .= "</tbody></table>
		<script>		
		delTenement = function(id) {
			if (confirm('Вы уверены удалить этот дом?')) {
				location.href = '/tenement.html?action=delete&id='+id;
			}
			return 0;
		}				
		</script>";
		return $html;
	}
	
	public static function getAdminCommercialList($action, $per_page=10, $current_page=1) {				
		if ($action=='newSales') {
			$where = "f.status='".REALTY_STATUS_APPLY."'";
		}
		else if($action=='newRent') {
			$where = "f.status='".REALTY_STATUS_RENT_APPLY."'";
		}		
		$start_pos = ($current_page-1)*$per_page;
		$limit = " LIMIT $start_pos,$per_page";
		$amount = Commercial::getAmountInList($where);
		$order = " ORDER BY id DESC";
		$db_res = Commercial::getFullListLink($where.' GROUP BY f.id '.$order.$limit);
		$paginator = self::paginator("commercial.php?action=$action",$amount,$per_page,$current_page);
		$html = "
		<div>$paginator</div>
		<div><a href='commercial.html?action=activateAll'>Активировать все</a></div>	
		<table class='base_text'>
		<thead>
		<tr>
		 <th>Адрес</th>
		 <th>Цена</th>		 
		 <th>Тип</th>
		 <th>Карта</th>
		 <th>Юзер</th>		 
		 <th></th>
		 <th></th>
		 <th></th>
		 <th></th>
		</tr>
		</thead>
		<tbody>
		";
		$status = ($action=='newSales') ? REALTY_STATUS_SALE : REALTY_STATUS_RENT;
		while ($row = $db_res->fetchRow()) {
			$type = Commercial::$TYPE[$row['type_id']];
			$city = ($row['city_id']==0) ? '' : $row['city'].',';			
			$addr = $city;
			$price = number_format($row['price'],0);
			$map = ($row['lat']&&$row['lon']) ? 'Да' : '<b>Нет</b>';
			$user = '';
			if ($row['user_name']!='') {
				$user = $row['user_name'];
			}
			if ($row['company_name']!='') {
				$user .= ' - '.$row['company_name'];
			} 
			$html .= "<tr>
			<td>$addr</td>
			<td>$price</td>
			<td>$type</td>			
			<td>$map</td>
			<td>$user</td>
			<td><a href='/commercial.html?action=edit&id={$row['id']}' target='_blank'>Редактировать</a></td>			
			<td><a href='/commercial.html?action=view&id={$row['id']}' target='_blank'>Смотреть</a></td>
			<td><a href='javascript:delObject({$row['id']})'>Удалить</a></td>
			<td><a href='/commercial.html?action=approve&id={$row['id']}&status=$status' target='_blank'>Активировать</a></td>
			</tr>";
		}
		$html .= "</tbody></table>
		<script>		
			delObject = function(id) {
			if (confirm('Вы уверены удалить этот объект?')) {
				location.href = '/commercial.html?action=delete&id='+id;
			}
			return 0;
		}				
		</script>";
		return $html;
	}
	
	
	public static function getAdminHouseList($action, $per_page=10, $current_page=1) {				
		if ($action=='listActive') {
			$where = "f.status='".REALTY_STATUS_ACTIVE."'";
		}
		else if($action=='listNew') {
			$where = "f.status='".REALTY_STATUS_APPLY."'";
		}		
		$start_pos = ($current_page-1)*$per_page;
		$limit = " LIMIT $start_pos,$per_page";
		$amount = House::getAmountInList($where);
		$order = " ORDER BY id DESC";
		$db_res = House::getFullListLink($where.$order.$limit);
		$paginator = self::paginator("house.php?action=$action",$amount,$per_page,$current_page);
		$html = "
		<div>$paginator</div>
		<div><a href='house.html?action=activateAll'>Активировать все</a></div>	
		<table class='base_text'>
		<thead>
		<tr>
		 <th>Адрес</th>
		 <th>Цена</th>		 
		 <th>Тип дома</th>
		 <th>Этажность</th>
		 <th>Карта</th>
		 <th>Юзер</th>		 
		 <th></th>
		 <th></th>
		 <th></th>
		 <th></th>
		</tr>
		</thead>
		<tbody>
		";		
		while ($row = $db_res->fetchRow()) {
			$type = House::$TYPE[$row['type_id']];
			$city = ($row['city_id']==0) ? '' : $row['city'].',';			
			$addr = $city;
			$price = number_format($row['price'],0);
			$map = ($row['lat']&&$row['lon']) ? 'Да' : '<b>Нет</b>';
			$user = '';
			if ($row['user_name']!='') {
				$user = $row['user_name'];
			}
			if ($row['company_name']!='') {
				$user .= ' - '.$row['company_name'];
			} 
			$html .= "<tr>
			<td>$addr</td>
			<td>$price</td>
			<td>$type</td>
			<td>{$row['storeys']}</td>
			<td>$map</td>
			<td>$user</td>
			<td><a href='/house.html?action=edit&id={$row['id']}' target='_blank'>Редактировать</a></td>			
			<td><a href='/house.html?action=view&id={$row['id']}' target='_blank'>Смотреть</a></td>
			<td><a href='javascript:delObject({$row['id']})'>Удалить</a></td>
			<td><a href='/house.html?action=approve&id={$row['id']}' target='_blank'>Активировать</a></td>
			</tr>";
		}
		$html .= "</tbody></table>
		<script>		
			delObject = function(id) {
			if (confirm('Вы уверены удалить этот дом?')) {
				location.href = '/house.html?action=delete&id='+id;
			}
			return 0;
		}				
		</script>";
		return $html;
	}
	
	public static function getAdminLandList($action, $per_page=10, $current_page=1) {				
		if ($action=='listActive') {
			$where = "f.status='".REALTY_STATUS_ACTIVE."' GROUP BY f.id";
		}
		else if($action=='listNew') {
			$where = "f.status='".REALTY_STATUS_APPLY."' GROUP BY f.id";
		}		
		$start_pos = ($current_page-1)*$per_page;
		$limit = " LIMIT $start_pos,$per_page";
		$amount = Land::getAmountInList($where);
		$order = " ORDER BY id DESC";
		$db_res = Land::getFullListLink($where.$order.$limit);
		$paginator = self::paginator("land.php?action=$action",$amount,$per_page,$current_page);
		$html = "
		<div><a href='land.html?action=activateAll'>Активировать все</a></div>
		<div>$paginator</div>		
		<table class='base_text'>
		<thead>
		<tr>
		 <th>Адрес</th>
		 <th>Цена</th>		 
		 <th>Описание</th>
		 <th>Карта</th>		 
		 <th></th>
		 <th></th>
		 <th></th>
		 <th></th>
		</tr>
		</thead>
		<tbody>
		";		
		while ($row = $db_res->fetchRow()) {			
			$city = ($row['city_id']==0) ? '' : $row['city'];			
			$addr = $city;
			$price = number_format($row['price'],0);
			$map = ($row['lat']&&$row['lon']) ? 'Да' : '<b>Нет</b>'; 
			$html .= "<tr>
			<td>$addr</td>
			<td>$price</td>			
			<td>{$row['description']}</td>
			<td>$map</td>
			<td><a href='/land.html?action=edit&id={$row['id']}' target='_blank'>Редактировать</a></td>			
			<td><a href='/land.html?action=view&id={$row['id']}' target='_blank'>Смотреть</a></td>
			<td><a href='javascript:delObject({$row['id']})'>Удалить</a></td>
			<td><a href='/land.html?action=approve&id={$row['id']}' target='_blank'>Активировать</a></td>
			</tr>";
		}
		$html .= "</tbody></table>
		<script>		
			delObject = function(id) {
			if (confirm('Вы уверены удалить это объявление?')) {
				location.href = '/land.html?action=delete&id='+id;
			}
			return 0;
		}				
		</script>";
		return $html;
	}



}
?>