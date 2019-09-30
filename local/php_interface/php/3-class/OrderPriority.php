<?php

/**
 * @deprecated
 * Получение правильных значение статусов  даты и времени получения заказа
 *
 * Class OrderPriority
 */
class OrderPriority
{

    use InitMainTrait;

    protected $order;
    protected $dateInsert;

    public function __construct(Bitrix\Sale\Order $order, $dateInsert = false)
    {
        $this->includeModules();
        $this->$order = $order;
        $this->dateInsert = $dateInsert;
    }

    public function GetPriorityOrder()
    {
        $date_receipt_order = strtotime($this->arProp['DATE']);// Дата получения заказа!
        $date_insert = strtotime(date('d.m.Y')); // дата создания заказ
        $date_insert_h = date('H'); // часы создания заказа
        $date_insert_i = date('i'); // минуты создания заказа
        $insertMinut = $date_insert_h * 60 + $date_insert_i;
        $sTimeEnum = $this->arProp['TIME']; // окно получения заказа

        $priority = null; //24
        $dateOrder = $this->arProp['DATE']; // 13
        $timeOrder = $this->arProp['TIME']; // 11

        if ($this->typeID == 1) {

            //Доставка по минску
            if (in_array($this->deliveryId, $this->arDeliveryMinsk)) {

                //дата доставки совподает с датой создание заказа
                $arEnum = [];
                if ($date_insert == $date_receipt_order) {

                    if ($insertMinut <= 9 * 60 + 45) {
                        $arEnum[] = 'enum-1';
                    }

                    if ($insertMinut <= 9 * 60 + 30) {
                        $arEnum[] = 'enum-2';
                    }

                    if ($insertMinut >= 9 * 60 + 30 and $insertMinut <= 16 * 60) {
                        $arEnum[] = 'enum-3';
                    }

                    if ($insertMinut <= 16 * 60) {
                        $arEnum[] = 'enum-4';
                    }

                    if (!in_array($sTimeEnum, $arEnum)) {
                        // попытка обмана!
                        $priority = 'priority-3';
                        $timeOrder = 'enum-4';
                    } else {
                        switch ($sTimeEnum) {
                            case 'enum-1':
                                $priority = 'priority-1';
                                break;
                            case 'enum-2':
                                $priority = 'priority-1';
                                break;
                            case 'enum-3':
                                $priority = 'priority-3';
                                break;
                            case 'enum-4':
                                $priority = 'priority-3';
                                break;
                        }
                    }
                } else {
                    $priority = 'priority-1';
                }
                //Доставка по РБ
            } elseif (in_array($this->deliveryId, $this->arDeliveryRB)) {
                $priority = 'priority-1';
                if ($insertMinut <= 9 * 60 + 30) {
                    $dateOrder = getWorkingDay(date('d.m.Y'), false);
                } else {
                    $dateOrder = getWorkingDay(date('d.m.Y'), true);
                }
                //Самовывоз
            } elseif ($this->deliveryId == 3) {

                //дата доставки совподает с датой создание заказа
                if ($date_insert == $date_receipt_order) {

                    if ($insertMinut <= 10 * 60) {
                        $priority = 'priority-1';
                        $dateOrder = getWorkingDay(date('d.m.Y'), false);
                    }

                    if ($insertMinut >= 10 * 60 and $insertMinut <= 16 * 60) {
                        $priority = 'priority-2';
                        $dateOrder = getWorkingDay(date('d.m.Y'), true);
                    }

                    if ($insertMinut >= 16 * 60) {
                        $priority = 'priority-1';
                        $dateOrder = getWorkingDay(date('d.m.Y'), true);
                    }

                } else {
                    $priority = 'priority-1';
                    $dateOrder = getWorkingDay(date('d.m.Y'), true);
                }


            } else {
                $priority = 'priority-1';
                $dateOrder = getWorkingDay(date('d.m.Y'), true);
            }
            //юр лицо
        } elseif ($this->typeID == 2) {
            $priority = 'priority-4';
            $dateOrder = getWorkingDay(date('d.m.Y'), true);
        }
        return [
            'priority' => $priority,
            'dateOrder' => $dateOrder,
            'timeOrder' => $timeOrder,
        ];
    }

}
