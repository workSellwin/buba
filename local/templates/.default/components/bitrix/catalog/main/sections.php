<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
$APPLICATION->IncludeComponent(
    "bitrix:catalog.section.list",
    "main_catalog_list",
    array(
        "IBLOCK_TYPE" => "catalog",
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_GROUPS" => "N",
        "COUNT_ELEMENTS" => "N",
        "TOP_DEPTH" => "2",
        "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
        "VIEW_MODE" => "LIST",
        "SHOW_PARENT_NAME" => "N",
        "HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"])?$arParams["SECTIONS_HIDE_SECTION_NAME"]:"N"),
        "ADD_SECTIONS_CHAIN" => "N",
        "COMPONENT_TEMPLATE" => "main_catalog_list",
        "SECTION_ID" => $_REQUEST["SECTION_ID"],
        "SECTION_CODE" => "",
        "SECTION_FIELDS" => array(
            0 => "",
            1 => "",
        ),
        "SECTION_USER_FIELDS" => array(
            0 => "",
            1 => "UF_SITE",
            2 => "UF_HIDE_CATALOG",
            3 => "UF_SORT_BH",
        ),
        "COMPOSITE_FRAME_MODE" => "A",
        "COMPOSITE_FRAME_TYPE" => "AUTO"
    ),
    $component,
    array("HIDE_ICONS" => "Y")
);