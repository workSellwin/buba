<?
/** @global CMain $APPLICATION */

use Bitrix\Main,
    Bitrix\Main\Application,
    Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\SiteTable,
    Bitrix\Main\UserTable,
    Bitrix\Main\Config\Option,
    Bitrix\Sale;
use Lui\Delivery\Code1cGetSellwin;
use Lui\Delivery\OrdersData;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/sale/prolog.php');

CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");
CModule::IncludeModule("lui.delivery");

$arResult = [];

if ($_REQUEST['export'] or $_REQUEST['view']) {

    $arResult = [];
    $arOrders = [];
    $obOrdersData = new OrdersData();
    /*
    $arHead = [
        '1CID' => '1С номер',//1
        'ID' => 'ID',//2
        'DATE' => 'Дата получения заказа',//3
        'TIME_FROM' => 'Время получения заказа с',//4
        'TIME_TO' => 'Время получения заказа по',//5
        'UNLOADED_ORDER' => 'Товарный чек выгружен',//6
        'PHONE' => 'Телефон',//7
        'FIO' => 'Фамилия и имя получателя',//8
        'DELIVERY' => 'Служба доставки',//9
        'CITY' => 'Населенный пункт',//10
        'PRIORITY' => 'Приоритет',//11
        'PRICE' => 'Сумма',//12
        'USER_DESCRIPTION' => 'Комментарии покупателя',//13
        'PAID' => 'Оплачен',//14
        'YANDEX_ADRESS' => 'Яндекс Адрес',//15
        'APARTMENT' => 'Квартира',//16
        'YANDEX_LON' => 'lon',//17
        'YANDEX_LAT' => 'lat',//18
        'YANDEX_Q' => 'Яндекс Запрос',//19
    ];
*/
    //if ($_REQUEST['BTS']) {
    $arHead = [
        '1CID' => '1с',//1
        'ID' => 'ID заказа',//2
        'DATE' => 'Дата получения заказа',//3
        'TIME_FROM' => 'Время получения заказа',//4
        'TIME_TO' => 'время закрытия',//5
        'PHONE' => 'Телефон',//6
        'FIO' => 'Фамилия и имя получателя',//7
        'YANDEX_ADRESS' => ' Город',//8
        'STREET' => 'Улица',//9
        'HOME' => 'Дом',//10
        'APARTMENT' => 'Квартира',//11
        'PRICE' => 'Сумма',//12
        'USER_DESCRIPTION' => 'Комментарии покупателя',//13
        'PAID' => 'Оплачен',//14
        'TIME_IDLE' => 'Время простоя', // 15
        'PRIORITY' => 'Приоритет',//16
    ];
    //  }


    $arResult['HEADER'] = $arHead;
    //$parameters = $obOrdersData->GetFilterParams($_REQUEST['TIME']);

    $arOrdersID = $obOrdersData->GetOrders($_REQUEST['TIME']);

    if ($_REQUEST['export'] and $_REQUEST['ID']) {
        $arOrders = $obOrdersData->GetData($_REQUEST['ID'], $arHead);
    } elseif ($arOrdersID) {
        $arOrders = $obOrdersData->GetData(array_column($arOrdersID, 'ID'), $arHead);
    }
    if ($arOrders) {
        $arOrders = $obOrdersData->validation($arOrders);
        $arOrders = $obOrdersData->deliveryRB($arOrders);
    }


    if ($_REQUEST['IS_UNLOADED_ORDER']) {
        $arOrders = array_filter($arOrders, function ($v) {
            return !(bool)$v['ORDER']['PROPS']['UNLOADED_ORDER_L'];
        });
    }


    if (is_numeric($_REQUEST['TIME'])) {
        switch ($_REQUEST['TIME']) {
            case '1':
                $arOrders = array_filter($arOrders, function ($v) {
                    $bull = false;
                    $delivery = $v['ORDER']['DELIVERY_ID'];
                    $timeFrom = $v['ROW']['TIME_FROM'];
                    $timeTo = $v['ROW']['TIME_TO'];
                    if (OrdersData::isRB($delivery)) {
                        $bull = true;
                    } elseif (OrdersData::isMinsk($delivery)) {
                        if ($timeFrom == '12:00' and ($timeTo == '16:00' or $timeTo == '18:00')) {
                            $bull = true;
                        }
                    }
                    return $bull;
                });
                break;
            case '2':
                $arOrders = array_filter($arOrders, function ($v) {
                    $bull = false;
                    $delivery = $v['ORDER']['DELIVERY_ID'];
                    $timeFrom = $v['ROW']['TIME_FROM'];
                    $timeTo = $v['ROW']['TIME_TO'];
                    if (OrdersData::isMinsk($delivery)) {
                        if (($timeFrom == '18:00' or $timeFrom == '19:00') and ($timeTo == '21:00' or $timeTo == '22:00')) {
                            $bull = true;
                        }
                    }
                    return $bull;
                });
                break;
        }
    }

    $arResult['ORDERS'] = $arOrders;
}

$APPLICATION->SetTitle('Выгрузка маршрутного листа');
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php"); ?>
    <form action="" method="post">
        <div>
            <label for="">Дата доставки</label>
            <input type="date" required name="FROM" value="<?= $_REQUEST['FROM'] ? $_REQUEST['FROM'] : '' ?>">
            <?
            $arTime = [
                'enum-1' => '12.00-18.00',
                'enum-2' => '18.00-22.00',
                '1' => 'Минск, РБ (12-18)',
                '2' => 'Минск (18-22)',
            ]; ?>
            <label for="">Время доставки <select name="TIME">
                    <option value="">Все</option>
                    <?
                    foreach ($arTime as $k => $v) { ?>
                        <option <?= $_REQUEST['TIME'] == $k ? 'selected' : '' ?> value="<?= $k ?>"><?= $v ?></option>
                        <?
                    } ?>
                </select>
            </label>
            <hr>
        </div>
        <div>
            <?/*  <label for="is_yandex_input">Запрос в яндекс
                <input type="checkbox" id="is_yandex_input"
                       value="Y" <?= $_REQUEST['IS_YANDEX'] ? 'checked' : '' ?>
                       name="IS_YANDEX"></label><br>*/ ?>
            <label for="is_uploader_input">не выгруженные ранее
                <input type="checkbox" id="is_uploader_input"
                       value="Y" <?= $_REQUEST['IS_UNLOADED_ORDER'] ? 'checked' : '' ?>
                       name="IS_UNLOADED_ORDER"> </label><br>
            <?/*   <label for="is_bts_input">Белтранспутник
                <input type="checkbox" id="is_bts_input"
                       value="Y" <?= $_REQUEST['BTS'] ? 'checked' : '' ?>
                       name="BTS"> </label><br>*/ ?>
            <hr>
        </div>
        <? /* <label for="">до </label>
        <input type="date" required name="TO" value="<?= $_REQUEST['TO'] ? $_REQUEST['TO'] : '' ?>">*/ ?>
        <input name="view" type="submit" value="Показать">
        <input name="export" type="submit" value="Выгрузить">

        <?

        if ($_REQUEST['view'] and $arResult['ORDERS']) {

            CJSCore::Init(array("jquery"));

            echo "<br>";
            echo <<<SCRIPT
<script>
$(document).ready(function(){
    $('body').on('click','#checked_all',function(){
        var c=$(this);
        if(c.prop('checked')){
            $('.all_checked').each(function(i,elem) {
              $(this).prop('checked',true);
            });
        }else{
             $('.all_checked').each(function(i,elem) {
              $(this).prop('checked',false);
            });
        }
    });
});
</script>
SCRIPT;

            echo "<table class='adm-list-table'>";
            $arHead = $arResult['HEADER'];
            echo "<thead>";
            echo "<tr class='adm-list-table-header'>";
            echo "<th class='adm-list-table-cell'><div class=\"adm-list-table-cell-inner\"><input type='checkbox'    id='checked_all' value='Y'></div></th>";
            echo "<th class='adm-list-table-cell'><div class=\"adm-list-table-cell-inner\">№</div></th>";
            foreach ($arHead as $td) {
                echo "<th class='adm-list-table-cell'><div class=\"adm-list-table-cell-inner\">{$td}</div></th>";
            }
            echo "</tr>";
            echo "<thead>";
            echo "<tbody>";
            $k = 1;
            foreach ($arResult['ORDERS'] as $i => $tr) {
                $checked = $tr['ORDER']['PROPS']['UNLOADED_ORDER_L'] ? '' : 'checked';
                if ($tr['CONFIG']['ERROR'] == 'Y') {
                    $checked = '';
                }
                $STATUS_ID = $tr['ORDER']['STATUS_ID'];
                echo "<tr class='adm-list-table-row '>";
                echo "<td class='adm-list-table-cell' {$tr['CONFIG']['STYLE']}><input type='checkbox'  class='all_checked' {$checked}  name='ID[]' value='{$tr['ROW']['ID']}'></td>";
                echo "<td class='adm-list-table-cell' {$tr['CONFIG']['STYLE']}>{$k}</td>";
                $k++;
                foreach ($tr['ROW'] as $code => $td) {
                    switch ($code) {
                        case 'ID' :
                            echo "<td class='adm-list-table-cell' {$tr['CONFIG']['STYLE']}><a target='_blank' href='/bitrix/admin/sale_order_view.php?ID={$td}&filter=Y&set_filter=Y&lang=ru'><b>№ {$td}</b></a></td>";
                            break;
                        case 'TIME' :
                        case 'PRICE':
                        case 'PHONE':
                        case 'YANDEX_ADRESS':
                            echo "<td class='adm-list-table-cell' {$tr['CONFIG']['STYLE']}><b>{$td}</b></td>";
                            break;
                        case 'PAID':
                            if ($td == 'Да') {
                                echo "<td class='adm-list-table-cell' {$tr['CONFIG']['STYLE']}><b>{$td}</b></td>";
                            } else {
                                echo "<td class='adm-list-table-cell' {$tr['CONFIG']['STYLE']}>{$td}</td>";
                            }
                            break;
                        case 'YANDEX_LON':
                        case 'YANDEX_LAT':
                            if (!$_REQUEST['BTS']) {
                                echo "<td class='adm-list-table-cell' {$tr['CONFIG']['STYLE']}>{$td}</td>";
                            }
                            break;
                        default:
                            echo "<td class='adm-list-table-cell' {$tr['CONFIG']['STYLE']}>{$td}</td>";
                            break;
                    }
                }
                echo "</tr>";
            }
            echo "<tbody>";
            echo "</table>";
        }

        if ($_REQUEST['export'] and $arResult['ORDERS']) {
            $obd = new \Lui\Delivery\NavBy($arResult);
            $obd->setSoapConfig('sellwin', 'oafege', 'http://gps.beltranssat.by/vrp-rs/ws/vrp?wsdl');
            $obd->sends();
            $ob = new \Lui\Delivery\ExportExcel();
            $ob->SetExcel($arResult);
        }
        ?>
    </form>
<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
