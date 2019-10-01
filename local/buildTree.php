<?php


namespace Asdrubael\Utils;


class BuildTree
{

    const PARENT_ID = 'PARENT_ID';
    const ID = "ID";
    const CHILDREN = 'CHILDREN';

    private $tree = [];
    private $outputArr = [];

    function __construct($outputArr)
    {
        $tempArr = [];

        foreach( $outputArr as $key => $item )
        {
            if( is_array($item[self::PARENT_ID]) && count($item[self::PARENT_ID]) )
            {
                foreach( $item[self::PARENT_ID] as $parentId)
                {
                    $temp = $item;
                    $temp[self::PARENT_ID] = $parentId;
                    $tempArr[] = $temp;
                }
                unset($outputArr[$key]);
            }
        }
        echo '<pre>' . print_r($tempArr, true) . '</pre>';
        $this->outputArr = array_merge($outputArr, $tempArr);
    }

    public function createTree()
    {
        $length = count($this->outputArr);

        foreach( $this->outputArr as $key => $item ){
            if( $item[self::PARENT_ID] === false )
            {
                $this->tree[$item[self::ID]] = $item;
                $this->tree[$item[self::ID]][self::CHILDREN] = [];

                unset($this->outputArr[$key]);
            }
            elseif( is_array($item[self::PARENT_ID]) && count($item[self::PARENT_ID]) )
            {

            }
            elseif( intval($item[self::PARENT_ID]) > 0 )
            {
                if( $this->putChild($item, $this->tree) )
                    unset($this->outputArr[$key]);
            }
        }
        //echo '<pre>' . print_r($this->outputArr, true) . '</pre>';
        if( $length > count($this->outputArr) )
        {
            $this->createTree();
        }
    }

    public function putChild($outputItem, &$treeArr)
    {
        $needId = $outputItem[self::PARENT_ID];

        foreach( $treeArr as &$item ){

            if( $item['ID'] == $needId && !$this->checkExist($outputItem[self::ID], $item[self::CHILDREN]) )
            {

                $item[self::CHILDREN][] = array_merge($outputItem, [self::CHILDREN => []]);
                return true;
            }
            elseif( is_array( $item[self::CHILDREN] ) && count( $item[self::CHILDREN] ) )
            {

                $this->putChild( $outputItem, $item[self::CHILDREN]);
            }
        }
        return false;
    }

    private function checkExist( $need, $arr )
    {
        foreach( $arr as $item )
        {
            if( $item[self::ID] == $need)
            {
                return true;
            }
        }
        return false;
    }

    public function getTree()
    {
        return $this->tree;
    }
}