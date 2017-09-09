<?php
namespace Admin\Controller;
use Think\Controller;
	/*
	 * 数据统计
	 */
class DataController extends CommonController
{
	public function __construct()
	{
		parent::__construct();
		$this->model = M('ty_doctor');
	}
	/*
	 * 医生挂号统计
	 */
	public function registration(){

		$where[id] = array('neq',1);
		$count=M('ty_service')->where($where)->count();
		$Page= new \Think\Page($count,5);
		setPage($Page);
		$show = $Page->show();
		$data = M('ty_service')->field('username,office,servetime')->limit($Page->firstRow.','.$Page->listRows)->select();
//		var_dump($data,$show);die;
		$this->assign('page',$show);
		$this->assign('list',$data);
		$this->display();
	}

	/*
	 * 医生诊治统计
	 */
	public function doctorconsult(){

		$where[id] = array('neq',1);
		$count=$this->model->where($where)->count();
		$Page= new \Think\Page($count,5);
		setPage($Page);
		$show= $Page->show();
		$list = $this->model->where($where)->field('id,username,office,tuwenpirce,orderprice,servernum')->order('office')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($list as $k=>&$v){
			if(!$v['username'] && !$v['office']){
				unset($list[$k]);
			}
			$v['office'] = M('ty_category')->where(array('id'=>$v['office']))->getField('catname');
			$v['total_price'] = $v['tuwenpirce']*$v['imagetext_people'] + $v['orderprice']*$v['order_people'];
		}

		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
	}

	/*
	 * 患者消费记录
	 */
	public function patientcost(){

		$where[id] = array('neq',1);
		$count=M('ty_sick')->where($where)->count();
		$count = $count*3;
		$Page= new \Think\Page($count,5);
		setPage($Page);
		$show= $Page->show();
		$info = M('ty_service')->field('s_id,type,count(type) as zong')->group('s_id,type')->limit($Page->firstRow.','.$Page->listRows)->select();
//		var_dump($info);die;
		$sick = M('ty_sick');
		foreach($info as $k=>&$v){
			$v['s_id'] = $sick->where(array('id'=> $v['s_id']))->getField('username');
			if($v['type'] == 1){
				$v['type'] = "图文服务";
			}else if($v['type'] == 2){
				$v['type'] = "预约服务";
			}else if($v['type'] == 3){
				$v['type'] = "免费服务";
			}else{
				$v['type'] = "免费服务";
			}
		}
		$this->assign('list',$info);
		$this->assign('page',$show);
		$this->display();
	}

	/*
	 *  评论修改
	 */
	/*public function edit(){

	}*/



}



