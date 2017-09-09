<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class WithdrawModel extends RelationModel{
	public $_link = array(
		'Shopadmin'=>array(
			'mapping_type'      	=> self::BELONGS_TO,
			'class_name'        	=> 'Shopadmin',
			'foreign_key'			=>'shopadmin_id',
			'as_fields'			=>'name',
		),

	);
}