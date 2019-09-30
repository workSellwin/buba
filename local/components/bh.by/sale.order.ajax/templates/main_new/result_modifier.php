<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arParams
 * @var array $arResult
 * @var SaleOrderAjax $component
 */
if($arParams['AJAX_MAIN'] == 'Y'){

    PR($arResult['JS_DATA']['ORDER_PROP']['properties']); die();
}
$component = $this->__component;
$component::scaleImages($arResult['JS_DATA'], $arParams['SERVICES_IMAGES_SCALING']);

$ob = new DiscountNeighbour();
if (!$ob->isView()) {
    echo <<<STYLE
<style>
[data-property-id-row="38"]{
   display: none;
}
</style>
STYLE;

}


// Yauheni_4---------------------
//ReplaseTimeInterval40($arResult['JS_DATA']['ORDER_PROP']['properties']);
//-----------------------------
