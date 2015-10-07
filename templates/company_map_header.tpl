{literal}
<script src="http://api-maps.yandex.ru/1.1/?key={/literal}{$YANDEX_KEY}{literal}&modules=pmap&wizard=constructor" type="text/javascript"></script>
<script type="text/javascript">
    var map, geoResult,rez;
    var objManager;
    YMaps.jQuery(window).load(function () {
        map = new YMaps.Map(YMaps.jQuery("#YMapsID-3050")[0]);
        map.setCenter(new YMaps.GeoPoint({/literal}{$lon}{literal},{/literal}{$lat}{literal}), {/literal}{$scale}{literal}, YMaps.MapType.PMAP);
        map.addControl(new YMaps.Zoom());
        map.addControl(new YMaps.ToolBar());
        YMaps.MapType.PMAP.getName = function () { return "Народная"; };
        map.addControl(new YMaps.TypeControl([
            YMaps.MapType.MAP,
            YMaps.MapType.SATELLITE,
            YMaps.MapType.HYBRID,
            YMaps.MapType.PMAP
        ], [0, 1, 2, 3]));
        map.addControl(new YMaps.MiniMap(), new YMaps.ControlPosition(YMaps.ControlPosition.BOTTOM_RIGHT));

        company_ids = new Array ({/literal}{$ids}{literal}0);
        names = new Array ({/literal}{$names}{literal}'0');
        x1 = new Array ({/literal}{$lons}{literal}'0');
        y1 = new Array ({/literal}{$lats}{literal}'0');
        contacts = new Array({/literal}{$contacts}{literal}'0');
        address = new Array({/literal}{$address}{literal}'0');
        logo = new Array({/literal}{$logo}{literal}'0');
        site = new Array({/literal}{$site}{literal}'0');

        objManager = new YMaps.ObjectManager();
        map.addOverlay(objManager);

        showCompanies();
    });

    function genContent(i)
    {
        var img='';
    	if (logo[i] != '') img = '<img style="max-height:150px" src="'+logo[i]+'"><br><br>';
        var c = '<div width="100%">'
                + '<strong>'+names[i]+'</strong><br>'
                + img
                + '<br>Контакты:<br>' + contacts[i]+'<br>'
                + '<br>Адрес:<br>' + address[i]+'<br>'
                +'<br><a href="' + site[i] + '" target=_blank>Смотреть</a>'
                + '</div>';
    	return c;
    }

    function showCompanies()
    {
        objManager.removeAll();
    	for (var i = 0; i < company_ids.length-1; i++) {

    		placemark = new YMaps.Placemark(new YMaps.GeoPoint(x1[i],y1[i]), {
    						hasHint: true,
    						maxWidth: 600,
    						balloonOptions: {
    							mapAutoPan: true,
    							maxWidth: 600
    						}
    					});
//
    		placemark.setBalloonContent(genContent(i));
            objManager.add(placemark);
    	}
    }

</script>
{/literal}