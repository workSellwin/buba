<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$this->setFrameMode(true);?>

<div class="f__nav-bottom-ttl">Мы в социальных сетях:</div>
<?if(!empty($arResult["IN"])):?>
	<a href="<?=$arResult["IN"]?>" target="_blank"><img src="/local/templates/.default/images/ico-inst.jpg" alt=""></a>
<?endif;?>
<?if(!empty($arResult["VK"])):?>
	<a href="<?=$arResult["VK"]?>" target="_blank"><img src="/local/templates/.default/images/ico-vk.jpg" alt=""></a>
<?endif;?>
<?if(!empty($arResult["FB"])):?>
	<a href="<?=$arResult["FB"]?>" target="_blank"><img src="/local/templates/.default/images/ico-fb.jpg" alt=""></a>
<?endif;?>
<?if(!empty($arResult["YT"])):?>
	<a href="<?=$arResult["YT"]?>" target="_blank"><img src="/local/templates/.default/images/ico-yt.jpg" alt=""></a>
<?endif;?>
<?if(!empty($arResult["OK"])):?>
	<a href="<?=$arResult["OK"]?>" target="_blank"><img src="/local/templates/.default/images/ico-ok.jpg" alt=""></a>
<?endif;?>
<?if(!empty($arResult["GP"])):?>
	<a href="<?=$arResult["GP"]?>" target="_blank"><img src="/local/templates/.default/images/ico-g+.jpg" alt=""></a>
<?endif;?>