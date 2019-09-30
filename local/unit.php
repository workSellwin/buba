<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 29.09.2019
 * Time: 18:30
 */

namespace My\Test;

/**
 * Class Unit
 * @package My\Test
 *
 */
class Unit
{
    private $var1;
    private $var2;
    private $var3;
    private $var4;

    /**
     * Unit constructor.
     * @param $var1
     * @param $var2
     * @param $var3
     * @param $var4
     */
    public function __construct($var1, $var2, $var3, $var4)
    {
        $this->var1 = $var1;
        $this->var2 = $var2;
        $this->var3 = $var3;
        $this->var4 = $var4;
    }

    public function __toString()
    {
        return "class Unit";
    }

    /**
     * @return mixed
     */
    public function getVar1()
    {
        return $this->var1;
    }

    /**
     * @param mixed $var1
     */
    public function setVar1($var1)
    {
        $this->var1 = $var1;
    }

    /**
     * @return mixed
     */
    public function getVar2()
    {
        return $this->var2;
    }

    /**
     * @param mixed $var2
     */
    public function setVar2($var2)
    {
        $this->var2 = $var2;
    }

    /**
     * @return mixed
     */
    public function getVar3()
    {
        return $this->var3;
    }

    /**
     * @param mixed $var3
     */
    public function setVar3($var3)
    {
        $this->var3 = $var3;
    }

    /**
     * @return mixed
     */
    public function getVar4()
    {
        return $this->var4;
    }

    /**
     * @param mixed $var4
     */
    public function setVar4($var4)
    {
        $this->var4 = $var4;
    }
}