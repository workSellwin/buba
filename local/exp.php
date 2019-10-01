<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->IncludeComponent("bh:sale.export.1c","",Array(
        "SITE_LIST" => "s1",
        "IMPORT_NEW_ORDERS" => "N",
        "SITE_NEW_ORDERS" => "",
        "CHANGE_STATUS_FROM_1C" => "N",
        "EXPORT_PAYED_ORDERS" => "N",
        "EXPORT_ALLOW_DELIVERY_ORDERS" => "N",
        "EXPORT_FINAL_ORDERS" => "N",
        "REPLACE_CURRENCY" => "руб.",
        "GROUP_PERMISSIONS" => Array("1"),
        "USE_ZIP" => "Y",
        "INTERVAL" => "30",
        "FILE_SIZE_LIMIT" => "204800"
    )
);

//pr($content);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>
