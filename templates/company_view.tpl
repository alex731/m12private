{include file="header.tpl"}
<script type="text/javascript" src="/common/jquery.prettyPhoto.js?fgd=dfgdf"></script>
<script type="text/javascript" src="/common/jquery.boxy.js"></script>
<script>
{literal}
$(document).ready(function() {
	$(".gallery:first a[rel^='prettyPhoto']").prettyPhoto({animationSpeed:'slow',theme:'light_square',slideshow:10000, autoplay_slideshow: false});
	$(function() {
	    /* set global variable for boxy window */
	    var contactBoxy = null;
	    /* what to do when click on contact us link */
	    $('#send_msg_btn').click(function(){
	        var boxy_content;
	        boxy_content += "<div style=\"width:300px; height:300px\"><form id=\"feedback\">";
	        boxy_content += "<p>Ваше сообщение:<br /><textarea name=\"msg\" id=\"msg\" cols=\"37\" rows=\"10\"></textarea></p><input type=\"submit\" name=\"submit\" value=\"Отправить\" class=\"btn btn-primary\" />";
	        boxy_content += "</form></div>";
	        contactBoxy = new Boxy(boxy_content, {
	            title: "Подать объявление <br>(заказ обратного звонка)",
	            draggable: false,
	            modal: true,
	            behaviours: function(c) {
	                c.find('#feedback').submit(function() {
	                    Boxy.get(this).setContent("<div style=\"width: 300px; height: 300px\">Отправка...</div>");
	                    // submit form by ajax using post and send 3 values: subject, your_email, comment
	                    $.post("http://{/literal}{$HOST}{literal}/company.php?action=request", { msg: c.find("#msg").val()},
	                    function(data){
	                        /*set boxy content to data from ajax call back*/
	                        contactBoxy.setContent("<div style=\"width: 300px; height: 300px\">"+data+"</div>");
	                    });
	                    return false;
	                });
	            } 
	        });
	        return false;
	    });
	    $('#logo').attr('src',$('#logo').attr('src')+'?rnd='+Math.random());
	});
});
{/literal}
</script>
<div class="box2">
  <div class="block-title">{$company.name}</div>
  <div class="box-internal">
  <table width="100%">
  <tr><td colspan=2>
  		{if $logo!=''}<img src="{$logo}" id="logo" align="right" valign="top" class="logo">{/if}
		<p>{$company.description}</p>
		<p><b>Адрес:</b> {$company.address}</p>
		<p><b>Контактные данные:</b> {$company.contacts}</p>
		<p><b>E-mail:</b> <a href="mailto:{$company.email}">{$company.email}</a></p>
		<p><input id="send_msg_btn" class="btn btn-info" type="button"
                  value={if $is_agency}"Подать объявление (заказать обратный звонок)"{else}"Заказать обратный звонок"{/if}></p>
		<p>
		<ul class="user_menu">
		{if $flats!=''}<li><a href="http://{$HOST}/#flats" target="_parent">Продажа квартир</a></li>{/if}
		{if $flats_rent!=''}<li><a href="http://{$HOST}/#flats_rent" target="_parent">Аренда квартир</a></li>{/if}		
		{if $houses!=''}<li><a href="http://{$HOST}/#houses" target="_parent">Продажа домов, коттеджей</a></li>{/if}		
		{if $lands!=''}<li><a href="http://{$HOST}/#lands" target="_parent">Продажа земельных участков, садовых участков</a></li>{/if}
		{if $commercial_rent!=''}<li><a href="http://{$HOST}/#commercial_rent" target="_parent">Аренда коммерческой недвижимости</a></li>{/if}
		{if $commercial!=''}<li><a href="http://{$HOST}/#commercial" target="_parent">Продажа коммерческой недвижимости</a></li>{/if}		
		</ul>
		</p>
		</td>
	</tr>		
		<tr>
		<td class="base_text">		 
		{$gallery}
		</td>
		<td align="right" width="400px;">
		{if $company.lat>0 && $company.lon>0}
		<div style="text-align:left; width:100%; font-weight:bold;">Офис на карте:</div>
<!-- Этот блок кода нужно вставить в ту часть страницы, где вы хотите разместить карту  (начало) -->
<script src="http://api-maps.yandex.ru/1.1/?key={$YANDEX_KEY}&modules=pmap&wizard=constructor" type="text/javascript"></script>
<script type="text/javascript">
	{literal}
    YMaps.jQuery(window).load(function () {
        var map = new YMaps.Map(YMaps.jQuery("#YMapsID-4214")[0]);
        map.setCenter(new YMaps.GeoPoint({/literal}{$company.lon},{$company.lat}{literal}), 16, YMaps.MapType.PMAP);
        map.addControl(new YMaps.Zoom());
        //map.addControl(new YMaps.ToolBar());
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

       map.addOverlay(createObject("Placemark", new YMaps.GeoPoint({/literal}{$company.lon},{$company.lat}{literal}), "constructor#pmrdmPlacemark", "Офис компании"));
        
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
    {/literal}
</script>
<div id="YMapsID-4214" style="width:400px;height:300px; text-align:right;"></div>
{/if}
		</td>
		</tr></table>
		</p>
		{if $flats!=''}
		<h3 style="text-align:center;"><a name="flats"></a>Продажа квартир в Йошкар-Оле и Марий Эл</h3>
		{$flats}
		{/if}
		
		{if $flats_rent!=''}
		<h3 style="text-align:center;"><a name="flats_rent"></a>Аренда квартир в Йошкар-Оле и Марий Эл</h3>
		{$flats_rent}
		{/if}
		
		{if $houses!=''}
		<h3 style="text-align:center;"><a name="houses"></a>Продажа домов и коттеджей в Йошкар-Оле и Марий Эл</h3>
		{$houses}
		{/if}
		
		{if $lands!=''}
		<h3 style="text-align:center;"><a name="lands"></a>Продажа земельных участков в Йошкар-Оле и Марий Эл</h3>
		{$lands}
		{/if}

		{if $commercial_rent!=''}
		<h3 style="text-align:center;"><a name="commercial_rent"></a>Аренда коммерческой недвижимости в Йошкар-Оле и Марий Эл</h3>
		{$lands}
		{/if}
		
		{if $commercial!=''}
		<h3 style="text-align:center;"><a name="commercial"></a>Продажа коммерческой недвижимости в Йошкар-Оле и Марий Эл</h3>
		{$lands}
		{/if}		
  </div>

{include file="footer.tpl"}