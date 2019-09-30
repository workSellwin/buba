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
//Обновление скидки
$arResult = [];
// ID	Дата получения заказа	Время получения заказа	Телефон	Фамилия и имя получателя	Населенный пункт	Город	Улица	Дом	Квартира	Сумма	Комментарии покупателя	Оплачен
if ($_REQUEST['export'] or $_REQUEST['view']) {
    define('yandex_apikey', '91227d27-4cba-45e6-9179-7f0dc075d31d');

    $obOrdersData = new OrdersData();

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

    $arOrders = [];

    $arOrders[] = $arHead;

    $parameters = $obOrdersData->GetFilterParams($_REQUEST['TIME']);


    $arOrdersID = $obOrdersData->GetOrders($_REQUEST['TIME']);
    if ($arOrdersID) {
        $arOrders = $obOrdersData->GetData(array_column($arOrdersID, 'ID'), $arHead);
    }

    PR($arOrders);

    $dbRes = \Bitrix\Sale\Order::getList($parameters);
    $i = 1;
    while ($ar = $dbRes->fetch()) {
        $order = Sale\Order::load($ar['ID']);
        $propertyCollection = $order->getPropertyCollection();
        $propsData = [];
        foreach ($propertyCollection as $propertyItem) {
            $propsData[$propertyItem->getField("CODE")] = trim($propertyItem->getViewHtml());
        }
        $STATUS_ID = $order->getField('STATUS_ID');
        $arOrders[$i]['STATUS'] = $STATUS_ID;
        $arOrders[$i]['YELLOW'] = in_array($STATUS_ID, ['PP', 'DD']) ? 'style="background: yellow;"' : '';

        // YANDEX
        $q = '';
        $arYandex = [];
        $ob = new \Lui\Delivery\YandexApi();
        $q = $ob->GetQuery($propsData);
        if ($_REQUEST['IS_YANDEX']) {
            $arYandex = $ob->GetDataYandex($q);
        }
        $YANDEX_ADRESS = '';
        $APARTMENT = '';
        $YANDEX_LON = '';
        $YANDEX_LAT = '';
        if ($arYandex['Point']) {
            $YANDEX_ADRESS = $arYandex['Address']['formatted'];
            //PR($arYandex);
            if ($propsData['ROOM']) {
                //$YANDEX_ADRESS .= ', к.' . $propsData['ROOM'];
                $APARTMENT .= $propsData['ROOM'] ? 'к. ' . $propsData['ROOM'] : '';
            }
            $YANDEX_ADRESS = str_replace('Беларусь,', '', $YANDEX_ADRESS);

            $arOrders[$i]['ERROR'] = 'N';
            if ($ob->isValidAddress($arYandex)) {
                $arOrders[$i]['YELLOW'] = 'style="background: #ff7272;"';
                $arOrders[$i]['ERROR'] = 'Y';
            }


            $arPoint = explode(' ', $arYandex['Point']);
            $YANDEX_LAT = $arPoint[0];
            $YANDEX_LON = $arPoint[1];
        }

        //YANDEX END
        $delivery_name = '';
        $DELIVERY_ID = $order->GetField('DELIVERY_ID');
        if ($DELIVERY_ID) {
            $delivery = \Bitrix\Sale\Delivery\Services\Manager::getById($DELIVERY_ID);
            /*if (isset($delivery['PARENT_ID']) && $delivery['PARENT_ID'] > 0) {
                $delivery_parent = \Bitrix\Sale\Delivery\Services\Manager::getById($delivery['PARENT_ID']);
                $delivery_name = $delivery_parent['NAME'] . ' - ';
            }*/
            $delivery_name = $delivery_name . $delivery['NAME'];
        }

        // условие не выгружать с пустой даты выгрузки чека
        if ($_REQUEST['IS_UNLOADED_ORDER'] == 'Y' and !$propsData['UNLOADED_ORDER']) {
            continue;
        }


        $dataFromTo = explode('-', $propsData['TIME']);

        $orderID = $order->getId();
        $arOrders[$i]['TR'] = [
            '1CID' => $ar1CID[$orderID] ? $ar1CID[$orderID] : Code1cGetSellwin::GetID($orderID), // ID
            'ID' => $order->getId(), // ID
            'DATE' => $propsData['DATE'], //  Дата получения заказа
            'TIME_FROM' => $dataFromTo[0], // Время получения заказа
            'TIME_TO' => $dataFromTo[1], // Время получения заказа
            'UNLOADED_ORDER' => $propsData['UNLOADED_ORDER'], //Товарный чек выгружен
            'PHONE' => ' ' . $propsData['PHONE'], // Телефон
            'FIO' => $propsData['FIO'], // Фамилия и имя получателя
            'DELIVERY' => $delivery_name, // Фамилия и имя получателя
            'CITY' => $propsData['CITY'], // Населенный пункт
            'PRIORITY' => $propsData['PRIORITY'], // Приоритет
            //  'LOCATION' => $propsData['LOCATION'], // Город
            //   'STREET' => $propsData['STREET'], // Улица
            //   'HOME' => $propsData['HOME'], // Дом
            //  'ROOM' => $propsData['ROOM'], // Квартира
            'PRICE' => $order->getPrice(), // Сумма
            'USER_DESCRIPTION' => $order->getField('USER_DESCRIPTION'), // Комментарии покупателя
            'PAID' => $order->isPaid() ? 'Да' : '-', // Оплачен
            'YANDEX_ADRESS' => $YANDEX_ADRESS,
            'APARTMENT' => $APARTMENT,
            'YANDEX_LON' => $YANDEX_LON,
            'YANDEX_LAT' => $YANDEX_LAT,
            'YANDEX_Q' => $q,
        ];

        $i++;
    }
    $arResult['ORDERS'] = $arOrders;
    //PR($arResult);
    /*
    foreach ($rows as $row) {
        $order = Sale\Order::load($row['ID']);
        $type = $order->GetField('PERSON_TYPE_ID');
        $propertyCollection = $order->getPropertyCollection();
        PR($type);
    }
    */
}

$APPLICATION->SetTitle('Выгрузка маршрутного листа');
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php"); ?>
    <form action="" method="post">
        <label for="">Дата доставки</label>
        <input type="date" required name="FROM" value="<?= $_REQUEST['FROM'] ? $_REQUEST['FROM'] : '' ?>">
        <?
        $arTime = [
            'enum-1' => '12.00-16.00',
            'enum-2' => '12.00-18.00',
            'enum-3' => '18.00-21.00',
            'enum-4' => '19.00-22.00',
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
        <label style="border-right: 1px solid; margin-right: 8px;" for="">Запрос в яндекс <input type="checkbox"
                                                                                                 value="Y" <?= $_REQUEST['IS_YANDEX'] ? 'checked' : '' ?>
                                                                                                 name="IS_YANDEX">
        </label>
        <label for="">Выгруженные по чеку<input type="checkbox"
                                                value="Y" <?= $_REQUEST['IS_UNLOADED_ORDER'] ? 'checked' : '' ?>
                                                name="IS_UNLOADED_ORDER"> </label>
        <? /* <label for="">до </label>
        <input type="date" required name="TO" value="<?= $_REQUEST['TO'] ? $_REQUEST['TO'] : '' ?>">*/ ?>
        <input name="view" type="submit" value="Показать">
        <input name="export" type="submit" value="Выгрузить">
    </form>
<?
if ($_REQUEST['view'] and $arResult['ORDERS']) {
    echo "<br>";

    echo "<table class='adm-list-table'>";
    $arHead = array_shift($arResult['ORDERS']);
    echo "<thead>";
    echo "<tr class='adm-list-table-header'>";
    foreach ($arHead as $td) {
        echo "<th class='adm-list-table-cell'><div class=\"adm-list-table-cell-inner\">{$td}</div></th>";
    }
    echo "</tr>";
    echo "<thead>";
    echo "<tbody>";
    foreach ($arResult['ORDERS'] as $tr) {
        echo "<tr class='adm-list-table-row '>";
        foreach ($tr['TR'] as $code => $td) {
            switch ($code) {
                case 'ID' :
                    echo "<td class='adm-list-table-cell' {$tr['YELLOW']}><a target='_blank' href='/bitrix/admin/sale_order_view.php?ID={$td}&filter=Y&set_filter=Y&lang=ru'><b>№ {$td}</b></a></td>";
                    break;
                case 'TIME' :
                case 'PRICE':
                case 'PHONE':
                case 'YANDEX_ADRESS':
                    echo "<td class='adm-list-table-cell' {$tr['YELLOW']}><b>{$td}</b></td>";
                    break;
                case 'PAID':
                    if ($td == 'Да') {
                        echo "<td class='adm-list-table-cell' {$tr['YELLOW']}><b>{$td}</b></td>";
                    } else {
                        echo "<td class='adm-list-table-cell' {$tr['YELLOW']}>{$td}</td>";
                    }
                    break;
                default:
                    echo "<td class='adm-list-table-cell' {$tr['YELLOW']}>{$td}</td>";
                    break;
            }
        }
        echo "</tr>";
    }
    echo "<tbody>";
    echo "</table>";
}

if ($_REQUEST['export'] and $arResult['ORDERS']) {
    $ob = new \Lui\Delivery\ExportExcel();
    $ob->SetExcel($arResult);
}
?>
<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
