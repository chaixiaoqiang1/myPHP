<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class OrderModel extends RelationModel{
	public $_link = array(
		'Order_info'=>array(
			'mapping_type'      	=> self::HAS_MANY,
			'class_name'        	=> 'Order_info',
			'foreign_key'			=>'order_id',
		),
		/*'Address'=>array(
			'mapping_type'      	=> self::BELONGS_TO,
			'class_name'        	=> 'Address',
			'foreign_key'			=>'address_id',
			'as_fields'			=>'consignee,address,phone',
		),*/
	);
}