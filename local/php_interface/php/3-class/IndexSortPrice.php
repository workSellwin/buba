<?php

class IndexSortPrice
{
    protected $IBLOCK_ID = 2;
    protected $arPriceGroup = [];
    protected $arID = [];
    protected $nPageSize = 100;

    public function __construct()
    {
        \Bitrix\Main\Loader::includeModule("sale");
        \Bitrix\Main\Loader::includeModule("catalog");
        \Bitrix\Main\Loader::includeModule("iblock");
        $this->arPriceGroup = [
            '2' => 'PRICE_SORT_2',
            '10' => 'PRICE_SORT_10',
            '14' => 'PRICE_SORT_14',
        ];
    }

    public function reIndex($page = false)
    {
        $this->GetAllID($page);
        foreach ($this->arID as $id) {
            $this->UpdateSortIndexPrice($id, $this->GetItemPrice($id));
        }
    }


    public function UpdatePageReIndex($page)
    {
        $count = $this->GetCountPage();
        $allPage = ceil($count / $this->nPageSize);
        if ($page > $allPage) {
            return false;
        }
        $this->reIndex($page);
        $this->ShowNextPage($allPage, ++$page);
    }

    public function ShowNextPage($all, $page)
    {
        $page2 = $page - 1;
        echo <<<HTML
                <form method="get">
                <h3>Страница {$page2} из {$all}</h3>
                    <input type="hidden" name="PAGE" value="{$page}">
                    <input type="submit" id="luiwadjogs-submit-price">
                </form>
                <script>
                    $(document).ready(function() {
                      $('#luiwadjogs-submit-price').trigger('click');
                    });
                </script>
HTML;
    }

    /**
     *
     */
    public function GetCountPage()
    {
        return count($this->GetArAllId());
    }

    /**
     * @param $id
     * @return array
     */
    public function GetItemPrice($id)
    {
        $arResult = [];
        foreach ($this->GetPricesGroup() as $idGroup => $property) {
            $arPrice = $this->GetOptimalPrice($id, [$idGroup]);
            $arResult[$property] = $arPrice[0];
            $arResult[$property . '_DISCOUNT'] = $arPrice[1];
        }
        return $arResult;
    }

    /**
     * @param bool $page
     */
    public function GetAllID($page = false)
    {
        $this->arID = $this->GetArAllId($page);
    }


    public function GetArAllId($page = false)
    {
        $arResult = [];
        $nav = false;
        if ($page !== false) {
            $nav = [
                'iNumPage' => $page,
                'nPageSize' => $this->nPageSize,
            ];
        }
        $res = CIBlockElement::getList(
            ['ID' => 'ASC'],
            ['ACTIVE' => 'Y', 'IBLOCK_ID' => $this->IBLOCK_ID],
            false,
            $nav,
            array('ID', 'NAME')
        );
        while ($row = $res->getNext()) {
            $arResult[] = $row['ID'];
        }
        return $arResult;
    }


    public function GetPricesGroup()
    {
        return $this->arPriceGroup;
    }


    public function GetOptimalPrice($intProductID, $arUserGroups)
    {
        $arPrice = \CCatalogProduct::GetOptimalPrice($intProductID, 1, $arUserGroups, 'N', 's1');
        $discount=$arPrice['RESULT_PRICE']['BASE_PRICE'] - $arPrice['RESULT_PRICE']['DISCOUNT_PRICE'];
        return [$arPrice['RESULT_PRICE']['DISCOUNT_PRICE'], $discount];
    }


    public function UpdateSortIndexPrice($id, $update)
    {
        \CIBlockElement::SetPropertyValuesEx($id, $this->IBLOCK_ID, $update);
    }

}