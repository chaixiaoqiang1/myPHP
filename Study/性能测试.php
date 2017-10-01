<?php
/**
 * 数据库操作篇
 */
$dsn = 'mysql:dbname=my_tp5;host=127.0.0.1';
$user = 'root';
$password = 'root';
try {
    $dbh = new PDO($dsn, $user, $password);
    $query="insert into test(title) values";
    $start = microtime(true);
    for($i=0;$i<50000;$i++){
        $query.="('插入测试{$i}'),";//需要执行的sql语句
    }
    $query = substr($query,0,-1);
    $res=$dbh->exec($query);//执行添加语句并返回受影响行数
    $end = microtime(true);
    echo "数据添加成功，受影响行数为： ".$res;
    echo '<hr>';
    $rs = $end-$start;
    print_r($rs);die;
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}



