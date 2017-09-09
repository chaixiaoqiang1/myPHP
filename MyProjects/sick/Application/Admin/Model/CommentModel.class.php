<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class CommentModel extends RelationModel{
	public $_link = array(
		'Order'=>array(
			'mapping_type'      	=> self::BELONGS_TO,
			'class_name'        	=>'Order',
			'foreign_key'			=>'order_id',
			'as_fields' 			=>'order_num',
		),
		'User'=>array(
			'mapping_type'      	=> self::BELONGS_TO,
			'class_name'        	=>'User',
			'foreign_key'			=>'user_id',
			'as_fields' 			=>'user_name,icon',
		),
	);
}