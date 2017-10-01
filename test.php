<?php



$items = array(
    1 => array('id' => 1, 'pid' => 0, 'name' => '安徽省'),
    2 => array('id' => 2, 'pid' => 0, 'name' => '浙江省'),
    3 => array('id' => 3, 'pid' => 1, 'name' => '合肥市'),
    4 => array('id' => 4, 'pid' => 3, 'name' => '长丰县'),
    5 => array('id' => 5, 'pid' => 1, 'name' => '安庆市'),
);

// 无限级分类  常用的地方   导航   分类   地区
$tree = array();
foreach ($items as $k=>$v){
    if($v['pid']){
        $items[$v['pid']]['son'][] = &$items[$v['id']];
        continue;
    }
    $tree[] = &$items[$v['id']];
}


print_r($tree);

die;


//print_r(generateTree($items));die;




    // 字符串截取
    $str = "helloworld";
    echo substr($str,0);
    echo substr($str,0,3);

    echo uniqid('hello_',true);die;
    // 第一个参数为空，没有第二个参数为13位 59b3f505e0f3f
    // 第一个参数为空，第二个参数为true时为23位 59b3f54fe85592.32382523
    // 第一个参数为空，第二个参数不存在为 hello_59b3f59eedd51
    // 两个参数都存在时 hello_59b3f5ec83fb66.79694792

    if($_POST){
        $connect = mysql_connect("localhost",'root','root');
        mysql_query("use test");
        mysql_query("set names utf8");
        die;
    }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<form action="" method="post">
    姓名：<input type="text" name="username" id="username">
    电话：<input type="text" name="phone" id="phone">
    密码：<input type="password" name="password" id="password">
    确认密码：<input type="password" name="dpassword" id="dpassword">
    <input type="submit" value="提交" id="submit">
</form>

<script>
    window.onload = function () {
        var submit = document.querySelector("#submit");
        submit.onclick = function () {
            var username = document.querySelector("#username");
            if(username.value == ''){
                return false;
            }
            return true;
        };
    };
</script>

</body>
</html>


<!--注册用户后获得一个个人的推荐码推荐5个用户可提升1000元额度。 封顶额度1.5万元然后用户可以发起申请贷款，注册后没有邀请别人的话就是可贷额度显示1000元-->
<!-- 用户表中应该有一个审核状态（未审核，审核中，审核未通过，审核通过） -->
<?php
//生成一个唯一的推荐码

?>


