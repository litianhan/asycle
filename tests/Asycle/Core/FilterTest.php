<?php
/**
 * Date: 2017/12/8
 * Time: 12:01
 */
class FilterTest extends \Asycle\Core\TestCase{
    public function testIsInteger(){
        $filter = new \Asycle\Core\Filter(['id'=>123]);
        $filter->requiredKey('id')->isInteger();
        $this->assertEquals(false,$filter->hasError());

        $filter->reset(['id'=>122.5])->requiredKey('id')->isInteger();
        $this->assertEquals(true,$filter->hasError());

        $filter->reset(['id'=>null])->requiredKey('id')->isInteger();
        $this->assertEquals(true,$filter->hasError());

        $filter->reset(['id'=>''])->requiredKey('id')->isInteger();
        $this->assertEquals(true,$filter->hasError());

        $filter->reset(['id'=>'-155'])->requiredKey('id')->isInteger();
        $this->assertEquals(false,$filter->hasError());

        $filter->reset(['id'=>'54dd'])->requiredKey('id')->isInteger();
        $this->assertEquals(true,$filter->hasError());

        $filter->reset(['id'=>'12'])->requiredKey('id')->isInteger(12);
        $this->assertEquals(false,$filter->hasError());

        $filter->reset(['id'=>'12'])->requiredKey('id')->isInteger(12,12);
        $this->assertEquals(false,$filter->hasError());

        $filter->reset(['id'=>'11'])->requiredKey('id')->isInteger(12,85);
        $this->assertEquals(true,$filter->hasError());

        $filter->reset(['id'=>'85'])->requiredKey('id')->isInteger(12,85);
        $this->assertEquals(false,$filter->hasError());

        $filter->reset(['id'=>'86'])->requiredKey('id')->isInteger(12,85);
        $this->assertEquals(true,$filter->hasError());

        $filter->reset(['id'=>PHP_INT_MAX])->requiredKey('id')->isInteger(12);
        $this->assertEquals(false,$filter->hasError());

        $filter->reset(['id'=>'111111111111111111111111111111111111111'])->requiredKey('id')->isInteger(12);
        $this->assertEquals(false,$filter->hasError());

    }
    public function testIsFloat(){
        $filter = new \Asycle\Core\Filter(['id'=>123.6]);
        $filter->requiredKey('id')->isFloat();
        $this->assertEquals(false,$filter->hasError());

        $filter->reset(['id'=>'122.222'])->requiredKey('id')->isFloat();
        $this->assertEquals(false,$filter->hasError());

        $filter->reset(['id'=>'-122.222'])->requiredKey('id')->isFloat();
        $this->assertEquals(false,$filter->hasError());

        $filter->reset(['id'=>'-122.2d22'])->requiredKey('id')->isFloat();
        $this->assertEquals(true,$filter->hasError());

        $filter->reset(['id'=>null])->requiredKey('id')->isFloat();
        $this->assertEquals(true,$filter->hasError());
        $filter->reset(['id'=>''])->requiredKey('id')->isFloat();
        $this->assertEquals(true,$filter->hasError());
        $filter->reset(['id'=>'545555455454545415454545454545454545'])->requiredKey('id')->isFloat();
        $this->assertEquals(false,$filter->hasError());

    }
    public function testIsEmail(){
        $filter = new \Asycle\Core\Filter(['email'=>'123@163.com']);
        $filter->requiredKey('email')->isEmail();
        $this->assertEquals(false,$filter->hasError());

        $filter->includeKey('qq','123@qq.com')->isEmail();
        $this->assertEquals(false,$filter->hasError());

        $filter->reset(['email'=>'545555455454545415454545454545454545'])->requiredKey('email')->isEmail();
        $this->assertEquals(true,$filter->hasError());

        $filter->reset(['email'=>''])->requiredKey('email')->isEmail();
        $this->assertEquals(true,$filter->hasError());

        $filter->reset(['email'=>null])->requiredKey('email')->isEmail();
        $this->assertEquals(true,$filter->hasError());

    }

    public function testIsUrl(){
        $filter = new \Asycle\Core\Filter(['url'=>'http://123.com']);
        $filter->requiredKey('url')->isURL();
        $this->assertEquals(false,$filter->hasError());

        $filter->includeKey('qq','123@qq.com')->isURL();
        $this->assertEquals(true,$filter->hasError());

        $filter->reset(['url'=>'545555455454545415454545454545454545'])->requiredKey('url')->isURL();
        $this->assertEquals(true,$filter->hasError());

        $filter->reset(['url'=>''])->requiredKey('url')->isURL();
        $this->assertEquals(true,$filter->hasError());

        $filter->reset(['url'=>null])->requiredKey('url')->isURL();
        $this->assertEquals(true,$filter->hasError());
    }

    public function testIsMobilePhone(){
        $filter = new \Asycle\Core\Filter(['phone'=>'13355555555']);
        $filter->requiredKey('phone')->isMobilePhone();
        $this->assertEquals(false,$filter->hasError());

        $filter->includeKey('qq','123323232')->isMobilePhone();
        $this->assertEquals(true,$filter->hasError());

        $filter->reset(['phone'=>'545555455454545415454545454545454545'])->requiredKey('url')->isMobilePhone();
        $this->assertEquals(true,$filter->hasError());

        $filter->reset(['phone'=>''])->requiredKey('phone')->isMobilePhone();
        $this->assertEquals(true,$filter->hasError());

        $filter->reset(['phone'=>null])->requiredKey('phone')->isMobilePhone();
        $this->assertEquals(true,$filter->hasError());

        $filter->reset(['phone'=>''])->requiredKey('phone')->isMobilePhone();
        $this->assertEquals(true,$filter->hasError());

        $filter->reset(['phone'=>13555556666])->requiredKey('phone')->isMobilePhone(['135']);
        $this->assertEquals(false,$filter->hasError());

        $filter->reset(['phone'=>13555556666])->requiredKey('phone')->isMobilePhone(['1355555666']);
        $this->assertEquals(false,$filter->hasError());

        $filter->reset(['phone'=>13555556666])->requiredKey('phone')->isMobilePhone(['133']);
        $this->assertEquals(true,$filter->hasError());

        $filter->reset(['phone'=>13555556666])->requiredKey('phone')->isMobilePhone(['133','1355555']);
        $this->assertEquals(false,$filter->hasError());
    }

}