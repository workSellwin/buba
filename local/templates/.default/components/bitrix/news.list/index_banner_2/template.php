<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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

$arResult['ELEM'] = [];
foreach ($arResult["ITEMS"] as $val) {
    $arResult['ELEM'][$val['PROPERTIES']['POSITION']['VALUE']] = $val;
}
ksort($arResult['ELEM']);
unset($arResult["ITEMS"]);

?>
<div style="clear: both"></div>

<style>
    @media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) {
        body .home-banners .prod-item {
            display: block;
        }

        body .home-banners .prod-item img {
            display: block;
            max-width: 100%;
            width: 100%;
            height: 100%;
        }
    }
</style>

<section class="home-banners">
    <div class="row">
        <div class="col-md-20-4">
            <div class="prod-item" <?= $arResult['ELEM'][1]['PROPERTIES']['LINK']['VALUE'] ? 'onclick="location=\'' . $arResult['ELEM'][1]['PROPERTIES']['LINK']['VALUE'] . '\'" style="cursor: pointer;"' : '' ?>>
                <a class="btn-more btn-more-2" <?= $arResult['ELEM'][1]['PROPERTIES']['LINK']['VALUE'] ? 'href="' . $arResult['ELEM'][1]['PROPERTIES']['LINK']['VALUE'] . '"' : '' ?>>

                </a>
                <span>
                    <img src="<?= $arResult['ELEM'][1]['PREVIEW_PICTURE']['SRC'] ?>" alt="скидки">
                </span>
                <span class="plashka"></span>
            </div>

        </div>
        <div class="col-md-20-1" style="float: right;">
            <div class="col-xs-25-2 col-sm-25-1  col-md-6">
                <div class="prod-item" <?= $arResult['ELEM'][2]['PROPERTIES']['LINK']['VALUE'] ? 'onclick="location=\'' . $arResult['ELEM'][2]['PROPERTIES']['LINK']['VALUE'] . '\'" style="cursor: pointer;"' : '' ?>>
                    <a class="btn-more btn-more-2" <?= $arResult['ELEM'][2]['PROPERTIES']['LINK']['VALUE'] ? 'href="' . $arResult['ELEM'][2]['PROPERTIES']['LINK']['VALUE'] . '"' : '' ?>>

                    </a>
                    <span>
                        <img class="img-responsive" src="<?= $arResult['ELEM'][2]['PREVIEW_PICTURE']['SRC'] ?>">
                    </span>
                    <span class="plashka"></span>
                </div>
            </div>
            <div class="col-xs-25-2 col-sm-25-1  col-md-6">
                <div class="prod-item" <?= $arResult['ELEM'][3]['PROPERTIES']['LINK']['VALUE'] ? 'onclick="location=\'' . $arResult['ELEM'][3]['PROPERTIES']['LINK']['VALUE'] . '\'" style="cursor: pointer;"' : '' ?>>
                    <a class="btn-more btn-more-2" <?= $arResult['ELEM'][3]['PROPERTIES']['LINK']['VALUE'] ? 'href="' . $arResult['ELEM'][3]['PROPERTIES']['LINK']['VALUE'] . '"' : '' ?>>

                    </a>
                    <span>
                        <img class="img-responsive" src="<?= $arResult['ELEM'][3]['PREVIEW_PICTURE']['SRC'] ?>">
                    </span>
                    <span class="plashka"></span>
                </div>
            </div>
            <div class="col-xs-25-2 col-sm-25-1 col-md-6">
                <div class="prod-item" <?= $arResult['ELEM'][4]['PROPERTIES']['LINK']['VALUE'] ? 'onclick="location=\'' . $arResult['ELEM'][4]['PROPERTIES']['LINK']['VALUE'] . '\'" style="cursor: pointer;"' : '' ?>>
                    <a class="btn-more btn-more-2" <?= $arResult['ELEM'][4]['PROPERTIES']['LINK']['VALUE'] ? 'href="' . $arResult['ELEM'][4]['PROPERTIES']['LINK']['VALUE'] . '"' : '' ?>>

                    </a>
                    <span>
                        <img class="img-responsive" src="<?= $arResult['ELEM'][4]['PREVIEW_PICTURE']['SRC'] ?>">
                    </span>
                    <span class="plashka"></span>
                </div>
            </div>
            <div class="col-xs-25-2 col-sm-25-1  col-md-6">
                <div class="prod-item" <?= $arResult['ELEM'][5]['PROPERTIES']['LINK']['VALUE'] ? 'onclick="location=\'' . $arResult['ELEM'][5]['PROPERTIES']['LINK']['VALUE'] . '\'" style="cursor: pointer;"' : '' ?>>
                    <a class="btn-more btn-more-2" <?= $arResult['ELEM'][5]['PROPERTIES']['LINK']['VALUE'] ? 'href="' . $arResult['ELEM'][5]['PROPERTIES']['LINK']['VALUE'] . '"' : '' ?>>

                    </a>
                    <span>
                        <img class="img-responsive" src="<?= $arResult['ELEM'][5]['PREVIEW_PICTURE']['SRC'] ?>">
                    </span>
                    <span class="plashka"></span>
                </div>
            </div>
        </div>
        <div class="col-md-20-4">
            <div class="col-sm-3">
                <div class="col-xs-3">
                    <div class="prod-item" <?= $arResult['ELEM'][6]['PROPERTIES']['LINK']['VALUE'] ? 'onclick="location=\'' . $arResult['ELEM'][6]['PROPERTIES']['LINK']['VALUE'] . '\'" style="cursor: pointer;"' : '' ?>>
                        <a class="btn-more btn-more-2" <?= $arResult['ELEM'][6]['PROPERTIES']['LINK']['VALUE'] ? 'href="' . $arResult['ELEM'][6]['PROPERTIES']['LINK']['VALUE'] . '"' : '' ?>>

                        </a>
                        <span>
                            <img class="img-responsive" src="<?= $arResult['ELEM'][6]['PREVIEW_PICTURE']['SRC'] ?>">
                        </span>
                        <span class="plashka"></span>
                    </div>
                    <div class="prod-item" <?= $arResult['ELEM'][7]['PROPERTIES']['LINK']['VALUE'] ? 'onclick="location=\'' . $arResult['ELEM'][7]['PROPERTIES']['LINK']['VALUE'] . '\'" style="cursor: pointer;"' : '' ?>>
                        <a class="btn-more btn-more-2" <?= $arResult['ELEM'][7]['PROPERTIES']['LINK']['VALUE'] ? 'href="' . $arResult['ELEM'][7]['PROPERTIES']['LINK']['VALUE'] . '"' : '' ?>>

                        </a>
                        <span>
                            <img class="img-responsive" src="<?= $arResult['ELEM'][7]['PREVIEW_PICTURE']['SRC'] ?>">
                        </span>
                        <span class="plashka"></span>
                    </div>
                </div>
                <div class="col-xs-3">
                    <div class="prod-item" <?= $arResult['ELEM'][8]['PROPERTIES']['LINK']['VALUE'] ? 'onclick="location=\'' . $arResult['ELEM'][8]['PROPERTIES']['LINK']['VALUE'] . '\'" style="cursor: pointer;"' : '' ?>>
                        <a class="btn-more btn-more-2" <?= $arResult['ELEM'][8]['PROPERTIES']['LINK']['VALUE'] ? 'href="' . $arResult['ELEM'][8]['PROPERTIES']['LINK']['VALUE'] . '"' : '' ?>>

                        </a>
                        <span>
                            <img class="img-responsive" src="<?= $arResult['ELEM'][8]['PREVIEW_PICTURE']['SRC'] ?>">
                        </span>
                        <span class="plashka"></span>
                    </div>
                </div>
            </div>
            <div class="col-xs-6 col-sm-3">
                <div class="prod-item" <?= $arResult['ELEM'][9]['PROPERTIES']['LINK']['VALUE'] ? 'onclick="location=\'' . $arResult['ELEM'][9]['PROPERTIES']['LINK']['VALUE'] . '\'" style="cursor: pointer;"' : '' ?>>
                    <a class="btn-more btn-more-2" <?= $arResult['ELEM'][9]['PROPERTIES']['LINK']['VALUE'] ? 'href="' . $arResult['ELEM'][9]['PROPERTIES']['LINK']['VALUE'] . '"' : '' ?>>

                    </a>
                    <span>
                        <img class="img-responsive" src="<?= $arResult['ELEM'][9]['PREVIEW_PICTURE']['SRC'] ?>">
                    </span>
                    <span class="plashka"></span>
                </div>

                <div class="prod-item" <?= $arResult['ELEM'][10]['PROPERTIES']['LINK']['VALUE'] ? 'onclick="location=\'' . $arResult['ELEM'][10]['PROPERTIES']['LINK']['VALUE'] . '\'" style="cursor: pointer;"' : '' ?>>
                    <a class="btn-more btn-more-2" <?= $arResult['ELEM'][10]['PROPERTIES']['LINK']['VALUE'] ? 'href="' . $arResult['ELEM'][10]['PROPERTIES']['LINK']['VALUE'] . '"' : '' ?>>

                    </a>
                    <span>
                        <img class="img-responsive" src="<?= $arResult['ELEM'][10]['PREVIEW_PICTURE']['SRC'] ?>">
                    </span>
                    <span class="plashka"></span>
                </div>
            </div>
        </div>
    </div>
</section>

<div style="clear: both"></div>
