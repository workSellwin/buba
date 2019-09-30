<?php


class TestOrderPriority
{
    protected $arTest = [];
    protected $obOrderPriority;


    public function __construct(\Bitrix\Sale\Order $order, $time)
    {
        $this->order = $order;
        $this->time = $time;
        $this->obOrderPriority =new OrderPriority($order,$time);
    }


    function Test()
    {
        $this->Test1();
        return $this->arTest;
    }

    protected function Test1()
    {

    }

}
