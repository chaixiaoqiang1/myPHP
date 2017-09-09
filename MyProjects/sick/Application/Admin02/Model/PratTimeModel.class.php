<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class PratTimeModel extends RelationModel{
	public $_link = array(
		'Shcool'=>array(
			'mapping_type'      	=> self::BELONGS_TO,
			'class_name'        	=> 'Shcool',
			'foreign_key'			=>'school_id',
			'as_fields'				=>'shcool_name',
		),
	);
}