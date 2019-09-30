<?php

class PriorityOrder
{

    use InitMainTrait;
    use isOrder;

    protected $arOrder;
    protected $debug;
    protected $ORDER_ID;
    protected $arParams;
    protected $person_type_id;
    public $arDeliveryMinsk = [2, 7, 8, 10, 11, 14, 15, 16, 17, 20, 21];
    public $arDeliveryRB = [4, 9, 19];
    protected $arOrderProps = [];
    public $date; //Дата получения заказа
    public $arTime = []; //Время получения заказа
    protected $priority;

    public function __construct($ID, $debug = false)
    {
        if (is_numeric($ID)) {
            $this->includeModules();
            $this->ORDER_ID = $ID;
            $this->arOrder = CSaleOrder::GetByID($ID);
            $this->debug = $debug;
        } else {
            throw new \Exception($ID . ' no number');
        }
        $this->person_type_id = $this->arOrder['PERSON_TYPE_ID'];
    }

    public function setPriorityOrder()
    {

        if (!$this->isOrder() or $this->debug) {
            $this->arOrderProps = $this->getOrderProps($this->ORDER_ID); //Свойство заказа
            $this->getDateReceiptOrder();//Дата получения заказа
            $this->getTimeReceiptOrder();//Время получения заказа

            //физ лицо
            if ($this->person_type_id == 1) {
                $date_insert = strtotime(date('d.m.Y', strtotime($this->arOrder['DATE_INSERT'])));
                $date_receipt_order = strtotime($this->date);
                $date_insert_h = date('H', strtotime($this->arOrder['DATE_INSERT']));
                $date_insert_i = date('i', strtotime($this->arOrder['DATE_INSERT']));
                $insertMinut = $date_insert_h * 60 + $date_insert_i;

                //Доставка по минску
                if (in_array($this->arOrder["DELIVERY_ID"], $this->arDeliveryMinsk)) {

                    //дата доставки совподает с датой создание заказа
                    $arEnum = [];
                    if ($date_insert == $date_receipt_order) {

                        if ($insertMinut <= 10 * 60 + 0) {
                            $arEnum[] = 'enum-1';
                        }

                        if ($insertMinut <= 16 * 60 + 0) {
                            $arEnum[] = 'enum-2';
                        }

                        if (!in_array($this->arTime['VALUE'], $arEnum)) {
                            // попытка обмана!
                            if ($insertMinut >= 16 * 60 + 0) {
                                AddOrderProperty(13, getWorkingDay(date('d.m.Y'), true), $this->ORDER_ID);
                                if ($this->arTime['VALUE'] == 'enum-1') {
                                    $this->priority = 1;
                                } else {
                                    $this->priority = 3;
                                }
                            }elseif ($insertMinut>=10*60+0){
                                $this->priority = 3;
                                $this->arTime['VALUE'] = 'enum-2';
                                AddOrderProperty(11, 'enum-2', $this->ORDER_ID);
                            }

                        } else {
                            switch ($this->arTime['VALUE']) {
                                case 'enum-1':
                                    $this->priority = 1;
                                    break;
                                case 'enum-2':
                                    $this->priority = 3;
                                    break;
                            }
                        }
                    } else {
                        $this->priority = 1;
                    }
                    //Доставка по РБ
                } elseif (in_array($this->arOrder["DELIVERY_ID"], $this->arDeliveryRB)) {

                    $this->priority = 1;
                    if ($insertMinut <= 10 * 60 + 0) {
                        AddOrderProperty(13, getWorkingDay(date('d.m.Y'), false), $this->ORDER_ID);
                    } else {
                        AddOrderProperty(13, getWorkingDay(date('d.m.Y'), true), $this->ORDER_ID);
                    }

                    //Самовывоз
                } elseif ($this->arOrder["DELIVERY_ID"] == 3) {

                    if ($date_insert == $date_receipt_order) {
                        if ($insertMinut <= 15 * 60 + 0) {
                            AddOrderProperty(13, getWorkingDay(date('d.m.Y'), false), $this->ORDER_ID);
                        }else{
                            AddOrderProperty(13, getWorkingDay(date('d.m.Y'), true), $this->ORDER_ID);
                        }
                    }else{
                        AddOrderProperty(13, getWorkingDay(date('d.m.Y'), false), $this->ORDER_ID);
                    }
                    $this->priority = 2;

                } else {
                    $this->priority = 1;
                }

            }//юр лицо
            elseif ($this->arOrder["PERSON_TYPE_ID"] == 2) {
                $this->priority = 4;
            }


            if (!$this->debug) {
                AddOrderProperty(24, "priority-" . $this->priority, $this->ORDER_ID);
            } else {
                PR([
                    'priority' => $this->priority,
                    'date_insert' => $date_insert,
                    'date_receipt_order' => $date_receipt_order,
                    'DATE_INSERT' => $this->arOrder['DATE_INSERT'],
                    'DELIVERY_ID' => $this->arOrder["DELIVERY_ID"],
                    'arTime' => $this->arTime,
                    'arEnum' => $arEnum,
                    'date_insert_h' => $date_insert_h,
                    'date_insert_i' => $date_insert_i,
                    'date' => $this->date,
                    'insertMinut' => $insertMinut,
                    'arOrderProps' => $this->arOrderProps[11],
                ]);
            }
        }
    }

    /**
     * @param $ID
     * @return array
     * //Свойство заказа
     */
    public function getOrderProps($ID)
    {
        $db_props = \CSaleOrderPropsValue::GetOrderProps($ID);
        $arProps = [];
        while ($arRes = $db_props->Fetch()) {
            $arProps[$arRes["ORDER_PROPS_ID"]] = $arRes;
        }
        return $arProps;
    }

    /**
     * Дата получения заказа
     */
    protected function getDateReceiptOrder()
    {
        if ($this->arOrderProps[13]) {
            $this->date = $this->arOrderProps[13]["VALUE"];
        }
    }

    /**
     * Время получения заказа
     */
    protected function getTimeReceiptOrder()
    {
        if ($this->arOrderProps[11]) {
            if ($arOrderProps = \CSaleOrderPropsVariant::GetByValue(11, $this->arOrderProps[11]["VALUE"])) {
                $this->arTime = $arOrderProps;
            }
        }
    }

}
