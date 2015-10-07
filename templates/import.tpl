{include file="header.tpl"}
<div class="box2" >
  <div class="block-title">Импорт объявлений из партнерской программы "ИРР"</div>
  <div class="box-internal">
{if $amount>-1 || $num_updated_all>0}
<p style="color:red;">
{if $amount>0}
Импортировано {$amount} объявлений. Отредактируйте их в разделах "Кв. - ...(импорт)" и отправьте на проверку. Если у дома не указан адрес - <b>обязательно</b> укажите его. Вы всегда можете не показывать номер дома в объявлении если уберете галочку "Показывать адрес" при редактировании объявления о квартире. 
{else}
Объявления не импортированы. Заполните все необходимые поля. 
{/if}
{if $num_updated_all>0}
<p>У {$num_updated_all} объявлений обновлена дата на сегодняшнюю.</p>
{/if}
</p>
{/if}
<p>Выберите файл из партнерской программы ИРР. Импортированы будут по возможности все объявления - после импорта отредактируйте их в разделе "Кв. - ...(импорт)".</p>
<p>Если у дома не указан адрес - <b>обязательно</b> укажите его. Вы всегда можете не показвать номер дома в объявлении если уберете галочку "Показывать адрес" при редактировании объявления о квартире.
</p>
<p>Настоятельно рекомендуем Вам заполнить поля Площадь лоджии/балкона если есть балкон/лоджия (можно поставить любые цифры) - т.к. эти значения <b>влияют на результаты поиска</b>.
</p>
<p> 
После редактирования отправьте их на проверку модератору.
</p>
<form action="" method="post" name="import" id="import" enctype="multipart/form-data">
<div><input type="file" name="userfile"></div><br>
<div><input type="submit" value="Загрузить файл" class="btn btn-primary"></div>
</form>
</p>
		
</div>  
  </div>
{include file="footer.tpl"}