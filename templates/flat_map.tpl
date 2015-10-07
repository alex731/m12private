{include file="header.tpl"}
{literal}
<script src="http://api-maps.yandex.ru/1.1/?key={/literal}{$YANDEX_KEY}{literal}&modules=pmap&wizard=constructor" type="text/javascript"></script>
<script type="text/javascript">	
    var map, geoResult,rez;
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

        flat_number1 = new Array ({/literal}{$ids}{literal}0);
        size1 = new Array ({/literal}{$rooms}{literal}0);        
        price1 = new Array ({/literal}{$prices}{literal}'0');
        price_m = new Array ({/literal}{$prices_m}{literal}'0');
        area = new Array ({/literal}{$areas}{literal}'0');        
        adres1 = new Array ({/literal}{$addresses}{literal}'0');
        storeys = new Array ({/literal}{$storeys}{literal}'0');
        dates = new Array ({/literal}{$dates}{literal}'0');
        types = new Array ({/literal}{$types}{literal}'0');
        x1 = new Array ({/literal}{$lons}{literal}'0');
        y1 = new Array ({/literal}{$lats}{literal}'0');
        photo11 = new Array ({/literal}{$photos}{literal}'0');
        icon1 = new Array ({/literal}{$icons}{literal}'0');
        
        var s = new YMaps.Style();
        var pathicon = 'http://mari12.ru/images/';

        for (var i = 1; i < 4; i++) {
          var s = new YMaps.Style();
          s.iconStyle = new YMaps.IconStyle();
          s.iconStyle.offset = new YMaps.Point(-7,-28);
          s.iconStyle.href = pathicon+i+'ka.png';
          s.iconStyle.size = new YMaps.Point(28,29);
          YMaps.Styles.add(i+'ka', s);
        }

        for (var i = 1; i < 4; i++) {
        var s = new YMaps.Style();
         s.iconStyle = new YMaps.IconStyle();
          s.iconStyle.offset = new YMaps.Point(-7,-28);
          s.iconStyle.href = pathicon+i+'kb.png';
          s.iconStyle.size = new YMaps.Point(28,29);
          YMaps.Styles.add(i+'kb', s);
         }

        for (var i = 1; i < 4; i++) {
        var s = new YMaps.Style();
         s.iconStyle = new YMaps.IconStyle();
          s.iconStyle.offset = new YMaps.Point(-7,-28);
          s.iconStyle.href = pathicon+i+'kc.png';
          s.iconStyle.size = new YMaps.Point(28,29);
          YMaps.Styles.add(i+'kc', s);
         }

        if (get('x1') != '') {
           showpoint(get('x1'),get('y1'),get('size'))
        } 
        else {
        	showFlatByCor();
        }
    });	

    function get(par) {
    	rez = '';
    	str = new String(window.location);

    	if (str.indexOf(par) > -1) {
    		for (i = str.indexOf(par) + par.length + 1; i < str.length; i++) {
    			if (str.charAt(i) == '&') {
    				break;
    			}
    			rez = rez + str.charAt(i);
    		}
    	}
    	return rez;
    }


    function normal1(s)
    {
    	pos = s.indexOf('кв.');
    	rez=s;
    	if (pos>0) 
        rez='';
    	{
    		for (var i = 0; i < pos; i++) {
    			rez=rez+s.charAt(i);
    		}
    	}
    	return rez;
    }

    function genContent(i)
    {
    	var img='';
    	if (photo11[i]!='') img = '<img src="http://mari12.ru/photos/1/'+photo11[i]+'"><br>'; 
        var c = '<div width="100%">'
        +img
        +size1[i]+'-комн., <strong>'+price1[i]+'</strong> р., '+area[i]+'м<sup>2</sup><br>'
        +'<strong>'+price_m[i]+'</strong> р./м<sup>2</sup><br>'        
    	+adres1[i]+'<br>'
    	+types[i]+', '+storeys[i]+', '+dates[i]+'<br>'
    	+'<a href="http://mari12.ru/flat.html?action=view&id='+flat_number1[i]+'" target=_blank>Смотреть</a></div>';
    	return c;
    }

    function showpoint(x,y,size) {
    	x1 = x * 1;
    	y1 = y * 1;
    	map.setCenter(new YMaps.GeoPoint(x1, y1));
    	var placemark1 = new YMaps.Placemark(new YMaps.GeoPoint(x1, y1));
    	map.addOverlay(placemark1);
    }

    function showById(id)
    {
    	for (var i = 0; i < flat_number1.length-1; i++)
    	{
    		if (flat_number1[i]==id) {
    			placemark = new YMaps.Placemark(new YMaps.GeoPoint(x1[i],y1[i]), {
    					style:icon1[i],
    					hasHint: true,
    					maxWidth: 600,
    					balloonOptions: {
    						mapAutoPan: true,
    						maxWidth: 600
    					}
    				});
	    		placemark.setBalloonContent(genContent(i));
	    		map.addOverlay(placemark);
	    		map.openBalloon(new YMaps.GeoPoint(x1[i],y1[i]), genContent(i),{maxWidth: 600});
    		}
    	}
    }

    function showFlatByCor()
    {
    	map.removeAllOverlays();
    	for (var i = 0; i < flat_number1.length-1; i++) {
    		placemark = new YMaps.Placemark(new YMaps.GeoPoint(x1[i],y1[i]), {
    						style:icon1[i],
    						hasHint: true,
    						maxWidth: 600,
    						balloonOptions: {
    							mapAutoPan: true,
    							maxWidth: 600
    						}
    					});

    		placemark.setBalloonContent(genContent(i));
    		map.addOverlay(placemark);
    	}
    }

    function showBySize(n)
    {
    	map.removeAllOverlays();
    	for (var i = 0; i < flat_number1.length-1; i++) {
    		if (size1[i]==n || (n==3 && size1[i]>n)) {
    			placemark = new YMaps.Placemark(new YMaps.GeoPoint(x1[i],y1[i]), {
    					style:icon1[i],
    					hasHint: true,
    					maxWidth: 600,
    					balloonOptions: {
    						mapAutoPan: true,
    						maxWidth: 600
    					}
    				});
				placemark.setBalloonContent(genContent(i));
	    		map.addOverlay(placemark);
	    	}
    	}
    }

    function showAddress (value,id1,time1,i) {
    	var geocoder = new YMaps.Geocoder(normal1(value), {results: 1, boundedBy: map.getBounds()});
    	YMaps.Events.observe(geocoder, geocoder.Events.Load, function () {
    		if (this.length()) {
    			geoResult = this.get(0);
    			placemark = new YMaps.Placemark(geoResult.getGeoPoint(), {
    						style:icon1[i],
    						hasHint: true,
    						maxWidth: 600,
    						balloonOptions: {
    							mapAutoPan: true,
    							maxWidth: 600    							
    						}
    					});
	    		placemark.setBalloonContent(genContent(i));
	    		map.addOverlay(placemark);
    		}
    	});
    }
</script>
{/literal}
{$block_html}
{include file="footer.tpl"}