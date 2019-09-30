<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$this->setFrameMode(true);

use Bitrix\Main\Localization\Loc;
?>
<?if(!empty($arResult["ITEMS"])):?>
	<div class="container">
		<div class="news">
			<?foreach($arResult["ITEMS"] as $arItem):?>
				<?
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				?> 
				<div class="news__col" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
					<div class="news__item">
						<?if($arItem["PREVIEW_PICTURE"]):?>
							<a class="news__img" href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<?=$arItem["NAME"]?>">
								<img src="<?=$arItem["PREVIEW_PICTURE"]["CUT"]["src"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>">
							</a>
						<?endif?>
						<div class="news__info">
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="news__ttl" title="<?=$arItem["NAME"]?>"><?=$arItem["NAME"]?></a>
							<?if($arItem["PREVIEW_TEXT"]):?>
								<p><?=$arItem["PREVIEW_TEXT"]?></p>
							<?endif?>
							<a class="btn btn_border" href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<?=Loc::getMessage("T_MAINPAGE_NEWS_MORE")?>"><?=Loc::getMessage("T_MAINPAGE_NEWS_MORE")?></a>
						</div>
					</div>
				</div>
			<?endforeach?>
		</div>
	</div>
<?endif?>
