{include file="header.tpl"}

{include file="company_map_header.tpl"}
{literal}
<style>
div.company_card {
    background-color: #FFFFFF;
	
	stext-align:center;
	display:inline;
    float:left;
}

div.company_card_inner {
	height: 170px;
    width: 100%;
    text-align:center;
}

img.company_card_logo {
    max-height: 90%;
    max-width: 90%;
	border: 1px solid #ccc;
	padding:3px;
	background:#fff;
}
a.company {
	padding:0 !important;
	color: #fff !important;
}
a.company:hover {
	padding:0 !important;
	color: #999 !important;
}
</style>
{/literal}


<div class="box2" >
    <div class="block-title">{$title}</div>
    <div class="box-internal">

        <div style='display:inline-block;'>
        {$block_html}
        </div>
        <p>
        Расположение компаний по недвижимости в Йошкар-Оле
        <div id="YMapsID-3050" style="width:100%;height:600px;"></div>'
        </p>


    </div>
</div>



{include file="footer.tpl"}