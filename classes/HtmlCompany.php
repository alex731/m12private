<?php
/**
 * User: Nik
 * Date: 25.01.13
 * Time: 23:25
 *
 * Класс - наследник Html.
 * Создан с единственной целью - разделить класс Html на части из-за его большого объема
 */
 
class HtmlCompany extends Html{

        /** Функция генерации html для списка компаний
     * @static
     * @param  $filter - список с данными фильтра
     * @param  &$companies - ссылка на список компаний
     * @param  $amount - количество найденных компаний не зависящее от страниц
     * @param  $per_page - количество на странице - передача значения по умолчанию
     * @param  $action - собственно action
     * @return string - возвращает html код
     */
    public static function getCompanyList($filter, &$companies, $amount, $per_page, $action) {

        //подготовим данные для paginator (разбивка на страницы)
        $paginator = '';
        if(isset($filter['per_page']))
            $per_page = $filter['per_page']['val'];
        if($amount > $per_page){
            $current_page = 1;
            $params_for_paginator = '';

            if(isset($filter['page']))
                $current_page = $filter['page']['val'];
            unset($filter['page']); // удалим параметр page из фильтра если он там есть - он больше не нужен
            foreach($filter as $filter_name => $filter_params)
                if(isset($filter_params['request_val']))
                    $params_for_paginator .= $filter_params['request_val'];

            $paginator = self::paginator("company.html?action=$action".$params_for_paginator,$amount,$per_page,$current_page);
        }

        // подготовим значение типа компании
        if(isset($filter['type_id'])){
            $type_id = $filter['type_id']['val'];
            unset($filter['type_id']); // удалим параметр type_id из фильтра - он далее не потребуется
        }

        //здесь сохраним значения остальных полей фильтра (если они есть - на будущее)
        $params_for_filter = '';
        foreach($filter as $filter_name => $filter_params)
            if(isset($filter_params['request_val']))
                $params_for_filter .= $filter_params['request_val'];

        //сформируем собственно ссылки на варианты фильтра
        $company_types = array( Company::COMPANY_TYPE_AGENCY    => 'Агентства недвижимости',
        /*
                                Company::COMPANY_TYPE_BUILDER   => 'Строительные компании',
                                Company::COMPANY_TYPE_BANK      => 'Кредитные организации (Банки)',
                                Company::COMPANY_TYPE_REPAIRER  => 'Организации по ремонту'*/
                                );

        $filter_html = '<a href=\'http://'.DOMAIN."/company.html?action=$action".$params_for_filter."'>".
            (!isset($type_id) ? "<b>Все</b>" : "Все").
            "</a>";
        foreach($company_types as $k=>$v){
            $filter_html .=
                '/ <a href=\'http://'.DOMAIN."/company.html?action=$action&type_id=$k".$params_for_filter."'>".
                (isset($type_id) && $type_id == $k ? "<b>$v</b>" : "$v").
                "</a>";
        }

        // подготовим список компаний
        // здесь строки для задания массива значений полей, которые бедет использованы в шаблоне
        $company_ids = '';
        $names = '';
        $lons = '';
        $lats = '';
        $contacts = '';
        $address = '';
        $logo = '';
        $site = '';

        $companies_html = "";

        foreach($companies as $company){
            $company_logo = Company::getLogoURL($company['id']);
            $company_site = Company::getSiteURL($company['domain']);
            
            $logo_html = !empty($company_logo) ? "<img class=company_card_logo src='$company_logo'><br />" : "";
            $companies_html .=
                    "<a href='$company_site' target=_blank class='company'><div class=company_card>".
                            self::getBlock(
                                $company['name'],
                                "<div class=company_card_inner>
                                    $logo_html                                    
                                </div>").
                    "</div></a>";

            $company_ids .= $company['id'].',';
            $names .= '"'.self::prepareStrForJS($company['name']).'",';
            $lons .= '\''.$company['lon'].'\',';
            $lats .= '\''.$company['lat'].'\',';
            $contacts .= '"'.self::prepareStrForJS($company['contacts']).'",';
            $address .= '"'.self::prepareStrForJS($company['address']).'",';
            $logo .= '"'.addslashes($company_logo).'",';
            $site .= '"'.addslashes($company_site).'",';
        }

        // соберем что получилось и вернем в вызывающую функцию
        $html = "Количество компаний: $amount<br><br>".
                $filter_html.'<br>'.
                "<div style='display:inline-block'>$companies_html</div>".
                "<div>$paginator</div>";
        return array('html_block' => $html,
                    'company_ids' => $company_ids,
                    'names' => $names,
                    'lons' => $lons,
                    'lats' => $lats,
                    'contacts' => $contacts,
                    'address' => $address,
                    'logo' => $logo,
                    'site' => $site);

    }

    public static function prepareStrForJS($s){
        $s = addslashes($s);
        $s = str_replace(Array("\r", '<br>', '<br />'), '', $s);
        $s = str_replace("\n", '<br />', $s);
        return $s;
    }

    /** Функция возвращает HTML блок с выбором агенства, через которое можно подать объявление,
     *  в случае, если с сайтом работает пользователь в качестве Гостя
     *
     * @static
     * @return string
     */
    public static function getAddByAgencyBlock(){

        // если пользователь - Гость
        if(User::isGuest()){

            // Определим значение уже выбранной компании в предыдущем показе формы
            $selected = Realty::getSelectedByAgency($_REQUEST);

            // Заполним поля создаваемого контрола
            $prop['name'] = 'by_agency';
            $prop['label'] = 'Подать объявление через агенство';
            $prop['tag'] = 'select';
            //$prop['not_field'] = '1';

            // подготовим значения списка

            // значение (по умолчанию)
            $vals = array(-1 => '-- не использовать агенство --');

            // значения из запроса
            $companies_data = Company::getListForAdvertiseByAgency();
            foreach($companies_data['companies'] as $company)
                $vals[intval($company['id'])] = $company['name'];

            // запишем значения списка в соответствующее поле контрола
            $prop['vals'] = $vals;

            // вернем сформированный html-контрол
            return self::getElementForm($prop, 0, array('val'=>$selected, 'msg'=>''));
        }
        return '';
    }

}
