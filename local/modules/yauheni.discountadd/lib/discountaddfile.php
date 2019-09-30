<?php

namespace Yauheni\DiscountAdd;

class discountaddfile
{
    public $SITE_ID = 's1';
    public $PRIORITY = 17788;
    public $USER_GROUPS = [];
    public $USER_ID = '';
    public $pref = '';
    public $poles_name = array(
        'DATE_FROM',                    //дата начала акции у клиента
        'DATE_TO',                      //дата окончания акции у клиента
        'ARTNUMBER',                    //Артикул
        'DISCOUNT',                     //Маркетинговая Скидка
        'CONDITION',                    //Условие покупки от шт
        'SMOTKI',                       //Условие покупки в комплекте с указанной позицией - 1+1
    );

    public function __construct()
    {
        \CModule::IncludeModule("sale");
        \CModule::IncludeModule("catalog");
        \CModule::IncludeModule("iblock");
    }

    public function process($file_path, $SiteID, $UserID, $USER_GROUPS, $pref)
    {

        $dataArray['FIELDS'] = \CsvLib::CsvToArray2($file_path, $this->poles_name);

        $this->SITE_ID = $SiteID;
        $this->USER_ID = $UserID;
        $this->USER_GROUPS[] = $USER_GROUPS;
        $this->pref = $pref;

        $data = $this->convertArrayFields($dataArray);

        if(!empty($data)){
            foreach ($data as $key => $val) {
                $this->addDiscount($val);
            }
        }


        /*$arDiscountUpdate = $this->getListDiscount($data);
        if (!empty($arDiscountUpdate)) {
            foreach ($data as $key => $val) {
                if ($arDiscountUpdate[$val['NAME']]) {
                    $dis_id = $arDiscountUpdate[$val['NAME']];
                    $this->updateDiscount($dis_id, $val);
                } else {
                    $this->addDiscount($val);
                }
            }
        } else {
            foreach ($data as $key => $val) {
                $this->addDiscount($val);
            }
        }*/
    }

    protected function convertArrayFields($data)
    {

        $arDataCondition1 = array();//без условий
        $arDataCondition2 = array();//Условие покупки от шт
        $arDataCondition3 = array();//Условие покупки от шт
        $arDis = [];
        unset($data['FIELDS'][0]);
        foreach ($data as $key => $val) {
            if ($key == 'FIELDS') {
                foreach ($val as $k => $v) {

                    //без условий--------------------------------------------
                    if (!$v['CONDITION'] && !$v['SMOTKI']) {
                        if ($v['DISCOUNT']) {
                            $dis = str_replace('%', '', $v['DISCOUNT']);
                            if ($arDataCondition1[$dis]) {
                                $arDataCondition1[$dis][] = $v;
                            } else {
                                $arDataCondition1[$dis][] = $v;
                            }
                        }
                    }
                    //Условие покупки от шт-----------------------------------
                    if($v['CONDITION'] && !$v['SMOTKI']) {
                        if ($v['DISCOUNT']) {
                            $dis = str_replace('%', '', $v['DISCOUNT']);
                            if ($arDataCondition2[$dis][$v['CONDITION']]) {
                                $arDataCondition2[$dis][$v['CONDITION']][] = $v;
                            } else {
                                $arDataCondition2[$dis][$v['CONDITION']][] = $v;
                            }

                        }
                    }
                    //смотки 1+1 -------------------------------------------------
                    if($v['SMOTKI'] && !$v['CONDITION']){
                        if ($v['DISCOUNT']) {
                            $dis = str_replace('%', '', $v['DISCOUNT']);
                            if ($arDataCondition3[$dis]) {
                                $arDataCondition3[$dis][] = $v;
                            } else {
                                $arDataCondition3[$dis][] = $v;
                            }
                        }
                    }
                }
            }
        }
        //без условий -----------------------------------------------------------------------------------------------------
        foreach ($arDataCondition1 as $key => $val) {
            $CHILDREN = [];
            $DiscountValue = $key;
            $ACTIVE_FROM = '';
            $ACTIVE_TO = '';
            foreach ($val as $k => $v) {
                $ACTIVE_FROM = $v['DATE_FROM']. ' 00:00:00';
                $ACTIVE_TO = $v['DATE_TO']. ' 00:00:00';
                $CHILDREN['CHILDREN'][] = [
                    'CLASS_ID' => 'CondIBProp:2:9',
                    'DATA' => array(
                        'logic' => 'Equal',
                        'value' => trim($v['ARTNUMBER']),
                    ),
                ];
            }

            $arDisCondition1[] = $this->getDiscountArrayFieldsCondition1($ACTIVE_FROM, $ACTIVE_TO, $DiscountValue, $CHILDREN);
        }

        //Условие покупки от шт ----------------------------------------------------------------------------------------
        foreach ($arDataCondition2 as $key => $val) {
            foreach ($val as $k => $v){
                $CHILDREN = [];
                $DiscountValue = $key;
                $ACTIVE_FROM = '';
                $ACTIVE_TO = '';
                foreach ($v as $kk => $vv){
                    $ACTIVE_FROM = $vv['DATE_FROM']. ' 00:00:00';
                    $ACTIVE_TO = $vv['DATE_TO']. ' 00:00:00';
                    $CHILDREN['CHILDREN'][]=[

                        'CLASS_ID' => 'ActSaleBsktGrp',
                        'DATA' => array(
                            'Type' => 'Discount',
                            'Value' => $DiscountValue,
                            'Unit' => 'Perc',
                            'Max' => 0,
                            'All' => 'AND',
                            'True' => 'True',
                        ),
                        'CHILDREN' => [
                            [
                                'CLASS_ID' => 'CondIBProp:2:9',
                                'DATA' => array(
                                    'logic' => 'Equal',
                                    'value' => trim($v['ARTNUMBER']),
                                )
                            ],
                            [
                                'CLASS_ID' => 'CondBsktFldQuantity',
                                'DATA' => array(
                                    'logic' => 'EqGr',
                                    'value' => $vv['CONDITION'],
                                )
                            ],
                        ],
                    ];
                }

                $arDisCondition2[] = $this->getDiscountArrayFieldsCondition2($ACTIVE_FROM, $ACTIVE_TO, $DiscountValue, $CHILDREN, $COUNT=$k);
            }
        }

        //смотки 1+1 -----------------------------------------------------------------------------------------------------------------------------
        $aListElem = $this->getListElement($arDataCondition3);
        foreach ($arDataCondition3 as $key => $val) {
            $DiscountValue = $key;
            foreach ($val as $k => $v) {
                if($v['ARTNUMBER'] && $v['SMOTKI']){
                    $ACTIVE_FROM = $v['DATE_FROM']. ' 00:00:00';
                    $ACTIVE_TO = $v['DATE_TO'] . ' 00:00:00';

                    $elem1=$aListElem[$v['ARTNUMBER']];
                    $elem2=$aListElem[$v['SMOTKI']];
                    $arDisCondition3[] = $this->getDiscountArrayFieldsCondition3($ACTIVE_FROM, $ACTIVE_TO, $DiscountValue, $elem1, $elem2);
                }
            }
        }

       //------------------------------------------------------------------------------------------------------------------------------------------

        $result = array_merge(is_array($arDisCondition1)? $arDisCondition1 : [], is_array($arDisCondition2)? $arDisCondition2 : [], is_array($arDisCondition3)? $arDisCondition3 : []);
        return $result;
    }

    protected function getListElement($arData){
        $arCodes =[];
        foreach ($arData as $key => $val) {
            foreach ($val as $kod){
                $arCodes[]=trim($kod['ARTNUMBER']);
                $arCodes[]=trim($kod['SMOTKI']);
            }
        }
        $aListElem=[];
        if(!empty($arCodes)){
            $arSelect = Array("ID", "NAME", "IBLOCK_ID", 'PROPERTY_ARTNUMBER');
            $arFilter = Array("IBLOCK_ID"=>2, "ACTIVE"=>"Y", "PROPERTY_ARTNUMBER"=>$arCodes);
            $res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            while($ob = $res->GetNext())
            {
                $aListElem[$ob['PROPERTY_ARTNUMBER_VALUE']] = $ob['ID'];
            }
        }
        return $aListElem;
    }

    protected function addDiscount($data)
    {
        if (!empty($data)) {
            \CSaleDiscount::Add($data);
        }
    }

    protected function updateDiscount($id, $data)
    {
        if (!empty($data)) {
            \CSaleDiscount::Update($id, $data);
        }
    }

    protected function getListDiscount($data)
    {
        if (!empty($data)) {
            $name = [];
            $arDiscountUpdate = [];
            foreach ($data as $key => $val) {
                $name[] = $val['NAME'];
            }
            $rsDiscounts = \CSaleDiscount::GetList([], ['NAME' => $name, 'LID' => $this->SITE_ID], false, false, []);
            while ($arDiscount = $rsDiscounts->Fetch()) {
                $arDiscountUpdate[$arDiscount['NAME']] = $arDiscount['ID'];
            }
            return $arDiscountUpdate;
        }
    }

    //без условий
    protected function getDiscountArrayFieldsCondition1($ACTIVE_FROM, $ACTIVE_TO, $DiscountValue, $CHILDREN)
    {
        $CONDITIONS_CHILDREN = array();

        if ($this->USER_ID) {
            $CONDITIONS_CHILDREN = array(
                'CHILDREN' => array(
                    0 => array(
                        'CLASS_ID' => 'CondMainUserId',
                        'DATA' => array(
                            'logic' => 'Equal',
                            'value' => array(
                                0 => $this->USER_ID
                            ),
                        ),
                    ),
                ),
            );
        }

        return array(
            'LID' => $this->SITE_ID,
            'NAME' => ($this->USER_ID ? 'Маркетинг USER_ID = ' . $this->USER_ID . ' ' . $DiscountValue . '% '.$this->pref : 'Маркетинг ' . $DiscountValue . '% '.$this->pref),
            'ACTIVE_FROM' => $ACTIVE_FROM,
            'ACTIVE_TO' => $ACTIVE_TO,
            'ACTIVE' => 'Y',
            'SORT' => $DiscountValue,
            'PRIORITY' => $this->PRIORITY,
            'LAST_DISCOUNT' => 'N',
            'LAST_LEVEL_DISCOUNT' => 'N',
            'XML_ID' => '',
            'CONDITIONS' => array(
                'CLASS_ID' => 'CondGroup',
                'DATA' => array(
                    'All' => 'AND',
                    'True' => 'True',
                ),
                'CHILDREN' => $CONDITIONS_CHILDREN['CHILDREN'],
            ),
            'ACTIONS' => array(
                'CLASS_ID' => 'CondGroup',
                'DATA' => array('All' => 'AND',),
                'CHILDREN' => array(
                    0 => array(
                        'CLASS_ID' => 'ActSaleBsktGrp',
                        'DATA' => array(
                            'Type' => 'Discount',
                            'Value' => $DiscountValue,
                            'Unit' => 'Perc',
                            'Max' => 0,
                            'All' => 'OR',
                            'True' => 'True',
                        ),
                        'CHILDREN' => $CHILDREN['CHILDREN'],
                    ),
                ),
            ),
            'USER_GROUPS' => $this->USER_GROUPS,
        );
    }

    //Условие покупки от шт
    protected function getDiscountArrayFieldsCondition2($ACTIVE_FROM, $ACTIVE_TO, $DiscountValue, $CHILDREN, $COUNT)
    {
        $CONDITIONS_CHILDREN = array();

        if ($this->USER_ID) {
            $CONDITIONS_CHILDREN = array(
                'CHILDREN' => array(
                    0 => array(
                        'CLASS_ID' => 'CondMainUserId',
                        'DATA' => array(
                            'logic' => 'Equal',
                            'value' => array(
                                0 => $this->USER_ID
                            ),
                        ),
                    ),
                ),
            );
        }

        return array(
            'LID' => $this->SITE_ID,
            'NAME' => ($this->USER_ID ? 'Маркетинг USER_ID = ' . $this->USER_ID . ' ' . $DiscountValue . '% от '.$COUNT.' шт. '.$this->pref : 'Маркетинг ' . $DiscountValue . '% от '.$COUNT. ' шт. '.$this->pref),
            'ACTIVE_FROM' => $ACTIVE_FROM,
            'ACTIVE_TO' => $ACTIVE_TO,
            'ACTIVE' => 'Y',
            'SORT' => $DiscountValue,
            'PRIORITY' => $this->PRIORITY,
            'LAST_DISCOUNT' => 'N',
            'LAST_LEVEL_DISCOUNT' => 'N',
            'XML_ID' => '',
            'CONDITIONS' => array(
                'CLASS_ID' => 'CondGroup',
                'DATA' => array(
                    'All' => 'AND',
                    'True' => 'True',
                ),
                'CHILDREN' => $CONDITIONS_CHILDREN['CHILDREN'],
            ),
            'ACTIONS' => array(
                'CLASS_ID' => 'CondGroup',
                'DATA' => array('All' => 'AND',),
                'CHILDREN' => $CHILDREN['CHILDREN'],
            ),
            'USER_GROUPS' => $this->USER_GROUPS,
        );
    }

    //смотки
    protected function getDiscountArrayFieldsCondition3($ACTIVE_FROM, $ACTIVE_TO, $DiscountValue, $elem1, $elem2)
    {
//<span class = "catalog-element-popup-info"> Получите <span class = "catalog-element-popup-element"> скидку #DISCOUNT_VALUE # </ span>, при покупке <a href = ' # LINK # 'target =' _ blank '> # NAME # </a> </ span> | del | <span class = "catalog-element-popup-info"> Покупка товара, вы получите <span class = "catalog- element-popup-element "> скидку #DISCOUNT_VALUE # </ span> на <a href='#LINK#' target='_blank'> # NAME # </a> </ span>

        $arSelect = Array("ID", "NAME", "IBLOCK_ID", "CODE", "DETAIL_PAGE_URL");
        $arFilter = Array("IBLOCK_ID"=>2, "ID"=>array($elem1, $elem2));
        $dataElem=array();
        $res = \CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
        while($ob = $res->GetNext())
        {

            $dataElem[$ob['ID']]['NAME'] = $ob['NAME'];
            $dataElem[$ob['ID']]['URL'] = $ob['DETAIL_PAGE_URL'];

        }

        return array(
            'LID' => $this->SITE_ID,
            'NAME' => 'Промо смотка: Товар  с ID '.$elem1.' + '.$elem2.$this->pref,
            'CURRENCY'=>'BYN',
            'ACTIVE_FROM' => $ACTIVE_FROM,
            'ACTIVE_TO' => $ACTIVE_TO,
            'ACTIVE' => 'Y',
            'SORT' => $DiscountValue,
            'PRIORITY' => 1,
            'LAST_DISCOUNT' => 'N',
            'LAST_LEVEL_DISCOUNT' => 'N',
            'XML_ID' => '',
            'USER_GROUPS' => $this->USER_GROUPS,
            //'PREDICTION_TEXT' => "Получите скидку #DISCOUNT_VALUE#, при покупке #NAME# |del|Покупая товар, вы получите скидку #DISCOUNT_VALUE# на #NAME#",
            //'PREDICTION_TEXT' => htmlspecialchars ('<span class=\"catalog-element-popup-info\">Получите <span class=\"catalog-element-popup-element\">скидку #DISCOUNT_VALUE#</span>, при покупке <a data-elem=\'1\' href=\"#LINK#\" target=\"_blank\">#NAME#</a></span> | del | <span class=\"catalog-element-popup-info\">Покупая товар, вы получите <span class=\"catalog-element-popup-element\">скидку #DISCOUNT_VALUE#</span> на <a data-elem=\'2\' href=\"#LINK#\" target=\"_blank\">#NAME#</a></span>'),
            //'PREDICTION_TEXT' => "<span class='catalog-element-popup-info'> Получите <span class='catalog-element-popup-element'> скидку ".$DiscountValue." % </ span>, при покупке <a href='".$dataElem[$elem1]['URL']."' target='_ blank'>".$dataElem[$elem1]['NAME']."</ a> </ span> | del | <span class = 'catalog-element-popup-info'> Покупка товара, вы получите <span class = 'catalog- element-popup-element '> скидку ".$DiscountValue." % </ span> на <a href='".$dataElem[$elem2]['URL']."' target='_blank'>".$dataElem[$elem2]['NAME']."</ a> </ span>",
            'PREDICTION_TEXT' => '<span class="catalog-element-popup-info">Получите <span class="catalog-element-popup-element">скидку #DISCOUNT_VALUE#</span>, при покупке <a data-elem="1" href=\'#LINK#\' target=\'_blank\'>#NAME#</a></span> |del|<span class="catalog-element-popup-info">Покупая товар, вы получите <span class="catalog-element-popup-element">скидку #DISCOUNT_VALUE#</span> на <a data-elem="2" href=\'#LINK#\' target=\'_blank\'>#NAME#</a></span> ',
            'PREDICTIONS' => [
                'CLASS_ID' => 'CondGroup',
                'DATA' => [
                    'All' => 'AND',
                    'True' => 'True',
                ],
                'CHILDREN' => [
                    0 => [
                        'CLASS_ID' => 'CondGroup',
                        'DATA' => [
                            'All' => 'OR',
                            'True' => 'True',
                        ],
                        'CHILDREN' => [
                            0 => [],
                            1 => [
                                'CLASS_ID' => 'CondGroup',
                                'DATA' => [
                                    'All' => 'AND',
                                    'True' => 'True',
                                ],
                                'CHILDREN' => [
                                    0 => [
                                        'CLASS_ID' => 'CondBsktProductGroup',
                                        'DATA' => [
                                            'Found' => 'Found',
                                            'ALL' => 'OR',
                                        ],
                                        'CHILDREN' => [
                                            0 => [
                                                'CLASS_ID' => 'CondIBElement',
                                                'DATA' => [
                                                    'logic' => 'Equal',
                                                    'value' => [
                                                        0 => $elem1,
                                                        1 => $elem2,
                                                    ],
                                                ],
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                    ],
                ],
            ],
            'CONDITIONS' => array(
                'CLASS_ID' => 'CondGroup',
                'DATA' => array(
                    'All' => 'AND',
                    'True' => 'True',
                ),
                'CHILDREN' => [
                    0=>[
                        'CLASS_ID' => 'CondGroup',
                        'DATA' => array(
                            'All' => 'OR',
                            'True' => 'True',
                        ),
                        'CHILDREN' => [
                            0 => [],
                            1 => [
                                'CLASS_ID' => 'CondGroup',
                                'DATA' => [
                                    'All' => 'AND',
                                    'True' => 'True',
                                ],
                                'CHILDREN' => [
                                    0 => [
                                        'CLASS_ID' => 'CondBsktProductGroup',
                                        'DATA' => [
                                            'Found' => 'Found',
                                            'ALL' => 'OR',
                                        ],
                                        'CHILDREN' => [
                                            0 => [
                                                'CLASS_ID' => 'CondIBElement',
                                                'DATA' => [
                                                    'logic' => 'Equal',
                                                    'value' => [
                                                        1 => $elem1,
                                                    ],
                                                ],
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                    ]
                ],
            ),
            'ACTIONS' => array(
                'CLASS_ID' => 'CondGroup',
                'DATA' => array(
                    'All' => 'AND',
                ),
                'CHILDREN' => [
                    0=>[
                        'CLASS_ID' => 'ActSaleBsktGrp',
                        'DATA' => array(
                            'All' => 'OR',
                            'True' => 'True',
                            'Max' => 0,
                            'Unit' => 'Perc',
                            'Value' => $DiscountValue,
                            'Type' => 'Discount',
                        ),
                        'CHILDREN' => [
                            0 => [],
                            1 => [
                                'CLASS_ID' => 'ActSaleSubGrp',
                                'DATA' => [
                                    'All' => 'AND',
                                    'True' => 'True',
                                ],
                                'CHILDREN' => [
                                    0 => [
                                        'CLASS_ID' => 'CondIBElement',
                                        'DATA' => [
                                            'logic' => 'Equal',
                                            'value' => [
                                                1 => $elem2,
                                            ],
                                        ],
                                    ]
                                ]
                            ]
                        ],
                    ]
                ],
            ),
            'PRESET_ID' => 'Sale\Handlers\DiscountPreset\ConnectedProduct',

        );
    }


}