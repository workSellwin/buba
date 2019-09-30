<?php

function getCurrentBudget($userId, $currency = 'BYN')
{
    if (Cmodule::IncludeModule('sale')) {

        if( empty($currency) ){
            $currency = CCurrency::GetBaseCurrency();
        }
        $data = CSaleUserAccount::GetByUserID($userId, $currency);

        if (!empty($data) && intval( $data['CURRENT_BUDGET'] ) > 0) {
            return round( $data['CURRENT_BUDGET'], 2 );
        }
        return 0;
    }
}