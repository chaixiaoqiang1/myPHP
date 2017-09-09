<?php
/**
 *@time 2014-3-7 15:30:31
 *@desc pay_types表对应的名称
 *
 */
return array(
    'id' => 'ID[payment库pay_type表中的ID]',
    'type_id' => 'ID[type_id]',
    'platform_type_id' => '储值数据库的pay_type表的ID',
    'pay_type_name' => '名称[pay_type_name]',
    'pay_type_id' => '付款类型ID[pay_type_id]',
    'company' => '公司名称[company]',
    'platform_id' => '平台ID',
    'created_at' => '创建时间',
    'updated_at' => '修改时间',
    'create_success1' => '创建成功，并已经同步到前台payment数据库',
    'create_success2' => '创建成功，但未能同步到前台payment数据库',
    'platform_pay_type' => '储值数据库正在使用的支付方法列表',
    'eastblue_pay_type' => '注意：运营后台记录的支付方法列表。可以通过点击下面的链接修改当前网站正在使用的支付方法',
);