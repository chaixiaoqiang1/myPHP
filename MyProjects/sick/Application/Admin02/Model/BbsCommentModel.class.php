<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class BbsCommentModel extends RelationModel{
	public $_link = array(
		'User'=>array(
			'mapping_type'      	=> self::BELONGS_TO,
			'class_name'        	=>'User',
			'foreign_key'			=>'user_id',
			'as_fields' 			=>'username,icon',
		),
	);
}