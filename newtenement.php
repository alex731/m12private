<?
include_once("./include/common.php");
$id = intval($_REQUEST['id']);
switch ($id) {
	//ИнвестСтройСервис
	case 1: 
		$title = 'ул. Лебедева 51в, секция Б, ввод в эксплуатацию 2 квартал 2012г.';
		$page = '		
		<img src="/photos/4/1/lebedeva2.jpg" align="right" valign="top">
		<p>Заказчик: ООО "ИнвестСтройСервис", подрядчик: ООО "ПМК-9". <b>Тел. (8362) 64-88-13, ул. Мира 70.</b></p>
		<p>Многоквартирный жилой комплекс по ул. Лебедева (севернее жилого дома 51) в г. Йошкар-Оле; Секция А – 9-ти этажный 64-квартирный жилой дом по ул. Лебедева; <br>Секция Б (эта новостройка) – 7-ми этажный 56-квартирный жилой дом по ул. Лебедева.</p>
		<p><b>Цена: 35 т.р./м<sup>2</sup> за первый и последний этаж. 36 т.р./м<sup>2</sup> для средних этажей.</b></p>
		<p><b>Ипотека в банках ВТБ24, Сбербанк.</b></p>
		<p>Однокомнатные квартиры: 42,51 м<sup>2</sup>.</p>
		<p>Двухкомнатные квартиры: 64,68 м<sup>2</sup> и 65,93 м<sup>2</sup>.</p>
		<p>Высота жилых этажей 2.6 м, <b>поквартирное отопление.</b><br>
Наружные стены кирпичные: керамический облицовочный кирпич, силикатный кирпич  трехслойной конструкции, с гибкими связями из стеклопластика, с утеплением из пенополистирольных плит. Внутренние стены и перегородки кирпичные. Окна и балконные двери пластиковые. Крыша чердачная с кровлей из рулонных материалов и внутренним водостоком, плоская с террасой с использованием гидроизоляции и теплоизоляции с внутренним организованным водостоком и наружным неорганизованным водостоком.</p>
		<p>
		На участке предусмотрены площадки для отдыха взрослого населения, детских игр, для занятия физкультурой, хозяйственных нужд и площадки для стоянки машин. Площадки оборудуются малыми архитектурными формами. Территория жилого дома полностью благоустраивается посадкой деревьев и кустарников, газонов; покрытие дорог, проездов, тротуаров предусмотрено из асфальтобетона. Отводится площадка и изготавливаются конструкции для сбора бытовых отходов.		
		</p>
		<p>
		<table><tr><td>
<!-- Этот блок кода нужно вставить в ту часть страницы, где вы хотите разместить карту  (начало) -->
<script src="http://api-maps.yandex.ru/1.1/?key='.YANDEX_KEY.'&modules=pmap&wizard=constructor" type="text/javascript"></script>
<script type="text/javascript">
    YMaps.jQuery(window).load(function () {
        var map = new YMaps.Map(YMaps.jQuery("#YMapsID-4214")[0]);
        map.setCenter(new YMaps.GeoPoint(47.941178,56.62329), 16, YMaps.MapType.PMAP);
        map.addControl(new YMaps.Zoom());
        map.addControl(new YMaps.ToolBar());
        YMaps.MapType.PMAP.getName = function () { return "Народная"; };
        map.addControl(new YMaps.TypeControl([
            YMaps.MapType.MAP,
            YMaps.MapType.SATELLITE,
            YMaps.MapType.HYBRID,
            YMaps.MapType.PMAP
        ], [0, 1, 2, 3]));

        YMaps.Styles.add("constructor#pmrdmPlacemark", {
            iconStyle : {
                href : "http://api-maps.yandex.ru/i/0.3/placemarks/pmrdm.png",
                size : new YMaps.Point(28,29),
                offset: new YMaps.Point(-8,-27)
            }
        });

       map.addOverlay(createObject("Placemark", new YMaps.GeoPoint(47.940963,56.623171), "constructor#pmrdmPlacemark", "Строящийся дом"));
        
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

<div id="YMapsID-4214" style="width:450px;height:350px"></div>
		</td><td class="base_text">
		<b>Планировка квартир:</b> 
		<ul class="gallery clearfix"><li><a href="/photos/4/1/planirovka.gif" rel="prettyPhotoPhoto[gallery2]" title="Планировка квартир"><img src="/photos/4/1/planirovka_prev.gif" alt=""/></a></li></ul>
		<a href="http://iss12.ru/index.php?option=com_content&view=article&id=2:l-51&catid=2:2010-12-26-22-58-31&Itemid=8" target="_blank">Проектная декларация</a></p>
		</td></tr></table>
		</p>		
		<p><b>Тел. 64-88-13, ул. Мира 70.</b></p>
		';
		break;
		
	case 2: 
		$title = 'б. Ураева 11 (рядом), ввод в эксплуатацию с 1 квартала 2012г.';
		$page = '		
		<img src="/photos/4/2/uraeva2.jpg" align="right" valign="top">
		<p>Заказчик: ООО "Теплогазстрой", подрядчик: ООО СФ "СМУ-2". <b>Тел. (8362) 64-88-13, ул. Мира 70.</b></p>
		<p>72-квартирный 9-этажный  жилой дом по адресу: г. Йошкар-Ола, примерно в 70 м по направлению на северо-восток от ориентира: г. Йошкар-Ола, бульвар Ураева, д. 11</p>
		<p><b>2-я очередь - сдача 1кв. 2012 г. Цена: 35 т.р./м<sup>2</sup> за первый и последний этаж. 36 т.р./м<sup>2</sup> для средних этажей.</b><br>
		Двухкомнатные квартиры: 64.79, 65.06, 66.11 м<sup>2</sup> 
		</p>
		<p><b>3-я и 4-я очереди - сдача 4кв. 2012 г. Цена: 32 т.р./м<sup>2</sup> за первый и последний этаж. 33 т.р./м<sup>2</sup> для средних этажей. </b><br>
		 Однокомнатные квартиры: 38.4, 41.8 м<sup>2</sup><br>
		 Двухкомнатные квартиры: 55.8, 66 м<sup>2</sup>
		</p>
		<p><b>Ипотека в банках ВТБ24, Сбербанк.</b></p>						
		<p>Высота этажа:  2,7м.<br>
Наружные стены: теплоэффективная слоистая кладка толщиной 660 мм: внутр. слой – кирп. кладка толщиной 380 мм, утеплитель - пенополистирол 160 мм, нар. слой – кирп. кладка 120 мм; Внутренние стены из силикатного кирпича, перегородки из силикатного кирпича, кладка стен с вент. каналами из керамического кирпича. Перекрытия – сборные ж/б многопустотные плиты, толщиной 220 мм, индивидуальные монолитные плиты. Окна и балконные двери пластиковые. Крыша плоская чердачная с кровлей из рулонных материалов и внутренним организованным водостоком, с выпуском на отмостку, с перепуском в зимний период в систему канализации. Отопление централизованное.</p>
		<p>
Проектом благоустройства предусматривается организация дворового пространства: устройство детских игровых площадок, площадки для тихого отдыха, спортивных площадок, площадок для хозяйственных целей и площадок для гостевой стоянки автомобилей на 9, 3, 13 и 12 машиномест. Хозяйственные площадки представлены площадками для чистки ковров, сушки белья и площадкой для установки контейнеров. На всех площадках устанавливаются соответствующие малые архитектурные формы.
		</p>
		<p>
		<table><tr><td>
<!-- Этот блок кода нужно вставить в ту часть страницы, где вы хотите разместить карту  (начало) -->
<script src="http://api-maps.yandex.ru/1.1/?key='.YANDEX_KEY.'&modules=pmap&wizard=constructor" type="text/javascript"></script>
<script type="text/javascript">
    YMaps.jQuery(window).load(function () {
        var map = new YMaps.Map(YMaps.jQuery("#YMapsID-2827")[0]);
        map.setCenter(new YMaps.GeoPoint(47.926286,56.639681), 16, YMaps.MapType.PMAP);
        map.addControl(new YMaps.Zoom());
        map.addControl(new YMaps.ToolBar());
        YMaps.MapType.PMAP.getName = function () { return "Народная"; };
        map.addControl(new YMaps.TypeControl([
            YMaps.MapType.MAP,
            YMaps.MapType.SATELLITE,
            YMaps.MapType.HYBRID,
            YMaps.MapType.PMAP
        ], [0, 1, 2, 3]));

        YMaps.Styles.add("constructor#pmrdmPlacemark", {
            iconStyle : {
                href : "http://api-maps.yandex.ru/i/0.3/placemarks/pmrdm.png",
                size : new YMaps.Point(28,29),
                offset: new YMaps.Point(-8,-27)
            }
        });

       map.addOverlay(createObject("Placemark", new YMaps.GeoPoint(47.940963,56.623171), "constructor#pmrdmPlacemark", "Строящийся дом"));
       map.addOverlay(createObject("Placemark", new YMaps.GeoPoint(47.926286,56.63948), "constructor#pmrdmPlacemark", "Строящийся дом"));
        
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

<div id="YMapsID-2827" style="width:450px;height:350px"></div>
		</td><td class="base_text">
		<b>Планировка квартир:</b> 
		<ul class="gallery clearfix"><li><a href="/photos/4/2/planirovka_uraeva.jpg" rel="prettyPhotoPhoto[gallery2]" title="Планировка квартир"><img src="/photos/4/2/planirovka_uraeva_prev.jpg" alt=""/></a></li></ul>		
		<a href="http://iss12.ru/index.php?option=com_content&view=article&id=3:-11&catid=5:2010-12-26-22-59-29&Itemid=5" target="_blank">Проектная декларация</a></p>
		</td></tr></table>
		</p>		
		<p><b>Тел. (8362) 64-88-13, ул. Мира 70.</b></p>
		';
		break;
}

$s->assign("title",$title);
$s->assign("page",$page);

$s->display("newtenement.tpl");
?>