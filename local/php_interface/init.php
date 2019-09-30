<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

require_once 'krumo/class.krumo.php';


use Bitrix\Sale;

\CModule::IncludeModule('highloadblock');
\CModule::IncludeModule('sale');


if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/constants.php"))
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/constants.php");

if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/events.php"))
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/events.php");

if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/classes.php"))
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/classes.php");
if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/functions.php"))
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/functions.php");
if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/fields/usertypeelement.php"))
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/fields/usertypeelement.php");


include_once __DIR__ . '/php.php';

$autoload = $_SERVER['DOCUMENT_ROOT'] . '/local/vendor/autoload.php';
if (file_exists($autoload))
    include_once $autoload;


AddEventHandler("main", "OnEndBufferContent", "removeType");
function removeType(&$content)
{

    $content = replace_output($content);
    if ($_SERVER['SERVER_NAME'] == 'buba.by' || true) {
        //$content = preg_replace('~<img[^>]+src="\K(?=/)~', 'https://bh.by', $content);
        $content=str_replace('/upload/iblock/', "https://bh.by/upload/iblock/", $content);
        $content=str_replace('/upload/sale/', "https://bh.by/upload/sale/", $content);
        //$content = preg_replace('style="background-image:url(K(?=/)~', 'https://bh.by', $content);
    }

}

function replace_output($d)
{
    return str_replace(' type="text/javascript"', "", $d);
}

if( !function_exists('ceiling') )
{
    function ceiling($number, $significance = 1)
    {
        return ( is_numeric($number) && is_numeric($significance) ) ? (ceil($number/$significance)*$significance) : false;
    }
}
