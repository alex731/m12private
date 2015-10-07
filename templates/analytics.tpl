{include file="header.tpl"}
<div class="box2" >
  <div class="block-title">Аналитика - динамика изменения цен на недвижимость в Йошкар-Оле и Марий Эл</div>
  <div class="box-internal">
  
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
    {literal}
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Дата');        
        data.addColumn('number', '1-к. кв.');
        data.addColumn('number', '2-к. кв.');        
        data.addColumn('number', '3-к. кв.');
        data.addRows([
		{/literal}
          {$ms}          
        {literal}
        ]);

        var options = {
          width: 1000, height: 400,allowHtml: true,
          title: 'Средняя недельная цена за кв.м. в руб. по квартирам в Йошкар-Оле (вторичка и новостройки)'
        };
        
        var formatter = new google.visualization.NumberFormat({
            'suffix':" руб./кв.м",
            'fractionDigits':0
          });
      	formatter.format(data, 1); // Apply formatter to second column
      	formatter.format(data, 2);
      	formatter.format(data, 3);
        
        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);

        var data2 = new google.visualization.DataTable();
        data2.addColumn('string', 'Дата');        
        data2.addColumn('number', '1-к. кв.');
        data2.addColumn('number', '2-к. кв.');        
        data2.addColumn('number', '3-к. кв.');
        data2.addRows([
		{/literal}
          {$ms2}          
        {literal}
        ]);

        var options = {
          width: 1000, height: 400,
          title: 'Средняя недельная цена квартиры в Йошкар-Оле (вторичка и новостройки)'
        };
        var formatter = new google.visualization.NumberFormat({
            'suffix':" руб.",
            'fractionDigits':0
          });
      	formatter.format(data2, 1); // Apply formatter to second column
      	formatter.format(data2, 2);
      	formatter.format(data2, 3);

        var chart2 = new google.visualization.LineChart(document.getElementById('chart_div2'));
        chart2.draw(data2, options);        
      }
      {/literal}
    </script>
<div id="chart_div" align="center"></div>
<div id="chart_div2" align="center"></div>
</div></div>
{include file="footer.tpl"}