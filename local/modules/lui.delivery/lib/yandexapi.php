<?php

namespace Lui\Delivery;

class YandexApi
{

    function GetDataYandex($q)
    {
        $yandex_apikey = yandex_apikey;
        $base_uri = 'https://geocode-maps.yandex.ru/1.x/';
        $arJson = json_decode(file_get_contents($base_uri . "?apikey={$yandex_apikey}&format=json&geocode={$q}&results=5"), true);
        $response = $arJson['response'];
        $GeoObjectCollection = $response['GeoObjectCollection'];
        $result = $GeoObjectCollection['metaDataProperty']['GeocoderResponseMetaData'];
        if ($result['found']) {
            $GeoObject = $GeoObjectCollection['featureMember'][0]['GeoObject'];
            $Address = $GeoObject['metaDataProperty']['GeocoderMetaData']['Address'];
            $Components = $GeoObject['metaDataProperty']['GeocoderMetaData']['Address']['Components'];
            unset($Address['Components']);
            $AddressLine = $GeoObject['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AddressLine'];
            $Point = $GeoObject['Point']['pos'];
            return [
                'guery' => $q,
                'AddressLine' => $AddressLine,
                'Point' => $Point,
                'Address' => $Address,
                'Components' => array_column(is_array($Components) ? $Components : [], 'name', 'kind'),
            ];
        }
    }

    function GetDataYandexAll($q)
    {
        $yandex_apikey = yandex_apikey;
        $base_uri = 'https://geocode-maps.yandex.ru/1.x/';
        $arJson = json_decode(file_get_contents($base_uri . "?apikey={$yandex_apikey}&format=json&geocode={$q}&results=5"), true);
        $response = $arJson['response'];
        $GeoObjectCollection = $response['GeoObjectCollection'];
        $result = $GeoObjectCollection['metaDataProperty']['GeocoderResponseMetaData'];
        $arResult = [];
        if ($result['found']) {
            foreach ($GeoObjectCollection['featureMember'] as $obj) {
                $GeoObject = $obj['GeoObject'];
                $Address = $GeoObject['metaDataProperty']['GeocoderMetaData']['Address'];
                $Components = $GeoObject['metaDataProperty']['GeocoderMetaData']['Address']['Components'];
                unset($Address['Components']);
                $AddressLine = $GeoObject['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AddressLine'];
                $Point = $GeoObject['Point']['pos'];
                $arResult[] = [
                    'guery' => $q,
                    'AddressLine' => $AddressLine,
                    'Point' => $Point,
                    'Address' => $Address,
                    'Components' => array_column(is_array($Components) ? $Components : [], 'name', 'kind'),
                ];
            }
            return $arResult;
        }
    }


    function GetQuery($propsData)
    {
        $q = '';
        if ($propsData['LOCATION']) {
            $q = $propsData['LOCATION'] . ' ';
        } else {
            if ($propsData['CITY']) {
                switch ($propsData['CITY']) {
                    case 'Сонечный':
                        $q .= 'посёлок ' . $propsData['CITY'] . ' ';
                        break;
                    default:
                        $q .= 'г.' . $propsData['CITY'] . ' ';
                        break;
                }
            } else {
                $q .= 'г. Минск ';
            }
        }

        if ($propsData['STREET']) {
            $q .= $propsData['STREET'];
        }

        if ($propsData['HOME']) {
            $q .= ', д.' . $propsData['HOME'];
        }

        if (stripos($q, 'Беларусь') === false) {
            $q = 'Беларусь, ' . $q;
        }

        return $q;
    }

    function isValidAddress($filed)
    {
        if (!empty($filed)) {
            $isValid = true;
            $country_code = $filed['Address']['country_code'];
            $locality = $filed['Components']['locality'];
            $street = $filed['Components']['street'];
            $house = $filed['Components']['house'];
            //проверка на беларусь
            if ($country_code != 'BY') {
                $isValid = false;
            }
            //г. Минск но нет улицы
            if ($locality == 'Минск' && $filed['Components']['locality'] == false) {
                $isValid = false;
            }
            //г. Минск, есть улицы но нет дома
            if ($locality == 'Минск' && $filed['Components']['locality'] != false && $house == false) {
                $isValid = false;
            }

            return $isValid;
        } else {
            return false;
        }
    }


}
