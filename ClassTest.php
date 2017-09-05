<?php

/***
 * Class People  测试类的一些使用属性标识
 */
class People{
    //类的公共属性
    public $name = 'public name';
    //类的私有属性
    private $age = 'private age';
    //类的受保护的属性
    protected $run = 'protected run';
    //静态属性
    public static $s1 = "public static s1";
    private static $s2 = "public static s2";
    protected static $s3 = "public static s3";
    //常量
    const C1 = "public const C1";
//    private const C2 = "public const C2";
//    protected const C3 = "public const C3";
}


$xq = new People();
echo $xq->name;
//echo $xq->age; // 无法访问私有的属性
//echo $xq->run; // 无法访问受保护的属性
echo $xq::$s1;
echo People::$s1;  //公共的静态属性有两种访问方式
//echo $xq::$s2; //无法访问私有的静态属性
//echo $xq::$s3; // 无法访问受保护的静态属性
//echo $xq->C1; 会报错
echo $xq::C1;
echo  People::C1;  //常量只能以静态的形式去调用 可以以类名或对象形式调用

//以上事例则说明伯优先级高不是静态属性  常量前面加其它标识会报错
