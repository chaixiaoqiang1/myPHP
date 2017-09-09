<script src="/js/ajaxfileupload.js"></script>
<script> 
function modalOfferYuanbaoController($scope, $modalInstance, order, $http, alertService) {
	$scope.region_id = '<?php echo Platform::find(Session::get('platform_id'))->region_id?>';
	$scope.order = order;
	$scope.orderData = {};
	$scope.cancel = function() {
		$modalInstance.dismiss('cancel');
	}
	$scope.offerYuanbaoForm= function(url) {
		$http({
			'method' : 'post',
			'url' : url,
			'data' : $.param($scope.orderData),
			'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).success(function(data) {
			$modalInstance.close();
		}).error(function(data) {
			alert('error: ' + data.error + '\n<?php echo Lang::get('slave.help_message_allowed')?>');
		});
	}
}
function modalRefundController($scope, $modalInstance, order, $http, alertService) {
	$scope.order = order;
	$scope.orderData = {};
	$scope.cancel = function() {
		$modalInstance.dismiss('cancel');
	}
	$scope.refundOrder = function(url) {
		$http({
			'method' : 'post',
			'url' : url,
			'data' : $.param($scope.orderData),
			'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).success(function(data) {
			alert(data);
			$modalInstance.close();
		}).error(function(data) {
			alert('error: ' + data.error + '\n<?php echo Lang::get('slave.help_message2')?>');
		});
	}
}

function modalRecordController($scope, $modalInstance, order, $http, $modal, alertService) {
	$scope.record_order_init = order;
	$scope.record_order = {};
	$scope.record_order.order_created_time = order.create_time;
	$scope.cancel = function() {
		$modalInstance.dismiss('cancel');
	}
	$scope.recordOrder = function(url) {
		$scope.record_order.is_record = 1;
		$scope.record_order.order_type = 'fail';
		$http({
			'method' : 'post',
			'url' : url,
			'data' : $.param($scope.record_order),
			'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).success(function(data) {
			alert(data.msg);
			$modalInstance.close();
		}).error(function(data) {
			alert(data.error);
		});
		$scope.record_order.is_record = 0;
	}

	$scope.AwardOrder = function(url) {
		$scope.record_order.is_record = 1;
		$scope.record_order.order_type = 'award';
		$http({
			'method' : 'post',
			'url' : url,
			'data' : $.param($scope.record_order),
			'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).success(function(data) {
			alert(data.msg);
			$modalInstance.close();
		}).error(function(data) {
			alert(data.error);
		});
		$scope.record_order.is_record = 0;
	}

	$scope.uploadpic = function(order){
		var modalInstance = $modal.open({
			templateUrl: 'uploadpic.html',
			controller: uploadController,
			resolve: {
				filename : function () {
					return order.order_id+"_fail";
				},
				folder : function(){
					return 'img/order_img';
				},
				filetype : function(){
					return 'jpg';
				}
			},
			backdrop : false,
			keyboard : false
		});
		modalInstance.result.then(function() {
		});		
	}

}

function modalTradeseqController($scope, $modalInstance, order, $http, alertService) {
	$scope.order_init = order;
	$scope.order = {};
	$scope.cancel = function() {
		$modalInstance.dismiss('cancel');
	}
	$scope.TradeseqOrder = function(url) {
		$http({
			'method' : 'post',
			'url' : url,
			'data' : $.param($scope.order),
			'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).success(function(data) {
			alert("补填成功");
			$modalInstance.close();
		}).error(function(data) {
			alert(data.error_description);
		});
	}
}

function modalResultController($scope, $modalInstance, result, $http, alertService) {
	$scope.showresult = {};
	$scope.result_display = {};
	$scope.showresult.result = result;
	$scope.cancel = function() {
		$modalInstance.dismiss('cancel');
	}
}

function getPaymentOrderPlayerController($scope, $http, alertService, $modal, $filter) {
	$scope.find_google_order = 0;
    $scope.alerts = [];
    $scope.formData = {};
    $scope.total = {};
    $scope.google_order = {};
	$scope.start_time = null;
	$scope.end_time = null;
	$scope.flag = 0;
	$scope.is_change = 0;
    $scope.processFrom = function() {
    	$scope.formData.limit_order = 0;
    	$scope.google_order = {};
        alertService.alerts = $scope.alerts;
		$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        $http({
            'method': 'post',
            'url': '/slave-api/payment/order',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
        	$scope.google_order = {};
        	if($scope.is_change == 0)
        	{
	        	if(data.length>5)
	        		$scope.flag = 1;
	        	else
	        		$scope.flag = 2;
            }
            $scope.total = data;
            if(0 == data.length && 'GPA.' == $scope.formData.tradeseq.substr(0,4)){
            	$scope.find_google_order = 1;
            }else{
            	$scope.find_google_order = 0;
            }
        }).error(function(data) {
            alertService.add('danger', "没有数据");
        });
    };
    $scope.process_limit_order = function() {
    	$scope.formData.limit_order = 1;
    	$scope.google_order = {};
        alertService.alerts = $scope.alerts;
		$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        $http({
            'method': 'post',
            'url': '/slave-api/payment/order',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
            $scope.total = data;
        }).error(function(data) {
            alertService.add('danger', "没有数据");
        });
    };
    $scope.processGoogle_order = function(google_time) {
    	if('GPA.' != $scope.formData.tradeseq.substr(0,4)){
    		$scope.find_google_order = 0;
    	}else{
    		alertService.alerts = $scope.alerts;
	    	$scope.google_order = {};
	        $scope.google_order.tradeseq = $scope.formData.tradeseq;
			$scope.google_order.google_time = $filter('date')(google_time, 'yyyy-MM-dd HH:mm:ss');
	        $http({
	            'method': 'post',
	            'url': '/slave-api/payment/order',
	            'data': $.param($scope.google_order),
	            'headers': {
	                'Content-Type': 'application/x-www-form-urlencoded'
	            }
	        }).success(function(data) {
	            alertService.add('success', data.msg);
	        }).error(function(data) {
	            alertService.add('danger', data.error);
	        });
        }
    };
    $scope.confirmYuanbao = function(order_sn,url) {
    	
    	$scope.confirm_order = {};
        $scope.confirm_order.order_sn = order_sn;
        $http({
            'method': 'post',
            'url': url,
            'data': $.param($scope.confirm_order),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
            alertService.add('success', data.res);
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
        
    };
	$scope.offerYuanbao = function(order) {
		var modalInstance = $modal.open({
			templateUrl: 'offer_yuanbao.html',
			controller: modalOfferYuanbaoController,
			resolve: {
				order : function () {
					return order;
				}
			},
			backdrop : false,
			keyboard : false
		});
		modalInstance.result.then(function() {
			$scope.processFrom();	
		});
	}
	$scope.refund = function(order) {
		var modalInstance = $modal.open({
			templateUrl: 'refund_order.html',
			controller: modalRefundController,
			resolve: {
				order : function () {
					return order;
				}
			},
			backdrop : false,
			keyboard : false
		});
		modalInstance.result.then(function() {
			console.log(order);
		});
	}
	$scope.stateChange = function(order){
		if($scope.flag == 1)
			$scope.flag = 2;
		else if($scope.flag==2)
			$scope.flag = 1;
		$scope.is_change = 1;
	}

	$scope.RecordOrder = function(order){
		var modalInstance = $modal.open({
			templateUrl: 'record_order.html',
			controller: modalRecordController,
			resolve: {
				order : function () {
					return order;
				}
			},
			backdrop : false,
			keyboard : false
		});
		modalInstance.result.then(function() {
			console.log(order);
		});		
	}

	$scope.Awardorder = function(order){
		var modalInstance = $modal.open({
			templateUrl: 'award_order.html',
			controller: modalRecordController,
			resolve: {
				order : function () {
					return order;
				}
			},
			backdrop : false,
			keyboard : false
		});
		modalInstance.result.then(function() {
			console.log(order);
		});		
	}

	$scope.TradeseqOrder = function(order_sn){
		var modalInstance = $modal.open({
			templateUrl: 'tradeseq_order.html',
			controller: modalTradeseqController,
			resolve: {
				order : function () {
					var order=[order_sn]
					return order;
				}
			},
			backdrop : false,
			keyboard : false
		});
		modalInstance.result.then(function() {
			console.log(order);
		});		
	}

	$scope.ShowResult = function(result){
		var modalInstance = $modal.open({
			templateUrl: 'show_result.html',
			controller: modalResultController,
			resolve: {
				result : function () {
					return result;
				}
			},
			backdrop : false,
			keyboard : false
		});
		modalInstance.result.then(function() {
			console.log(order);
		});		
	}
} 
</script>
<style type="text/css">
        td
        {
            white-space: nowrap;
        }
    </style>
<div class="col-xs-12" ng-controller="getPaymentOrderPlayerController" style="overflow:auto">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/payment/order" method="get" role="form"
				ng-submit="processFrom()" onsubmit="return false;">
				<div class="form-group" style="height:30px;">
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="clearfix">
				</div>
				<div class="form-group col-md-3" style="padding-left:0;">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.enter_order_number')?>"
						ng-model="formData.order_sn" name="order_sn"?>
				</div>
				<div class="form-group col-md-3" style="padding-left:0;">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.enter_tradeseq_number')?>"
						ng-model="formData.tradeseq" name="tradeseq"?>
				</div>
				<div class="form-group col-md-3" style="padding-left:0;">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.enter_player_nickname')?>"
						ng-model="formData.player_nickname" name="player_nickname"?>
				</div>
				<div class="form-group col-md-3" style="padding-left:0;">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.enter_player_uid')?>"
						ng-model="formData.player_uid" 
						<?php if($uid){ ?>ng-init="formData.player_uid=<?php echo $uid; ?>" <?php } ?> name="player_uid"?>
				</div>
				<div class="form-group col-md-3" style="padding-left:0;">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.enter_order_id')?>"
						ng-model="formData.order_id" <?php  if($order_id_init){ ?>ng-init="formData.order_id=<?php echo $order_id_init; ?>"<?php } ?> name="order_id"?>
				</div>
				<div class="form-group col-md-3" style="padding-left:0;">
					<input type="number" class="form-control"
						placeholder="<?php echo Lang::get('slave.enter_player_id')?>"
						ng-model="formData.player_id" name="player_id"?>
				</div>
				<div class="form-group col-md-3" style="padding-left:0;">
					<select class="form-control" name="get_payment"
						id="get_payment" ng-model="formData.get_payment"
						ng-init="formData.get_payment=-1">
						<option value="-1"><?php echo Lang::get('slave.all_pay_status') ?></option>
						<option value="0"><?php echo Lang::get('slave.un_paid_status') ?></option>
						<option value="1"><?php echo Lang::get('slave.paid_status') ?></option>
					</select>
				</div>
				<div class="form-group col-md-3" style="padding-left:0;">
					<select class="form-control" name="offer_yuanbao"
						id="offer_yuanbao" ng-model="formData.offer_yuanbao"
						ng-init="formData.offer_yuanbao=-1">
						<option value="-1"><?php echo Lang::get('slave.all_offer_status') ?></option>
						<option value="0"><?php echo Lang::get('slave.un_offered_status') ?></option>
						<option value="1"><?php echo Lang::get('slave.offered_status') ?></option>
					</select>
				</div>
				<div class="clearfix">
				</div>
				<div class="form-group">
					<input type="text" class="form-control" placeholder="<?php echo Lang::get('slave.enter_bank_account') ?>" ng-model="formData.bank_account" name="bank_account">
				</div>
				<div>
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('slave.show_all_servers') ?></option>
					<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
					<?php } ?>		
				</select>
				</div>
				<div class="form-group" style="padding-top:5px;">
					<?php echo "( ".Lang::get("slave.search_condition")." )";?>
				</div>
				<div class="form-group">
					<label>
						<input type="checkbox" value="1" ng-model="formData.is_offer" ng-init="formData.is_offer = 0" name="is_offer" ng-true-value="1" ng-false-value="0" /> 
						<?php echo Lang::get('slave.is_offer_yuanbao') ?>
					</label>
					<label>
						<input type="checkbox" value="1" ng-model="formData.is_refund" ng-init="formData.is_refund = 0" name="is_refund" ng-true-value="1" ng-false-value="0" /> 
						<?php echo Lang::get('slave.is_refund') ?>
					</label>
					<br/>
					<b style="color:red"><?php echo Lang::get('slave.order_refill_note'); ?></b>
				</div>
				<div class="form-group">
					<div class="col-md-6" style="padding: 0 0 0 0">
						<input type="submit" class="btn btn-default"
						value="<?php echo Lang::get('basic.btn_submit') ?>" />	
						<input ng-click="stateChange(t)" type="button"class="btn btn-default" value="切换显示"/>
						<input ng-click="process_limit_order('/slave-api/payment/order')" type="button" class="btn btn-default" value="查询玩家最高金额订单元宝"/>
					</div>
					<div class="col-md-6" style="padding: 0 0 0 0"  ng-if="find_google_order == 1">
						<b><?php echo Lang::get('slave.google_order'); ?></b>
						<div class="input-group">
							<quick-datepicker ng-model="google_time" init-value="12:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
							&nbsp;&nbsp;<input type="button" class="btn btn-default" value="<?php echo Lang::get('basic.google_submit') ?>" ng-click="processGoogle_order(google_time)" />
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

	<div class="row margin-top-10">
		<div class="col-xs-12">
			<table ng-if = "flag == 1 && formData.limit_order == 0" class="table table-striped">
				<thead>
					<tr class="info">
						<td><b><?php echo Lang::get("slave.order_id");?></b></td>
						<td><b><?php echo Lang::get("slave.order");?></b></td>
						<td><b><?php echo Lang::get("slave.order_external");?></b></td>
						<td><b><?php echo Lang::get("slave.combine_order");?></b></td>
						<td><b><?php echo Lang::get("slave.order_type");?></b></td>
						<td><b><?php echo Lang::get("slave.order_child_type");?></b></td>
						<td><b><?php echo Lang::get("slave.goods_type");?></b></td>
						<td><b><?php echo Lang::get("slave.order_recharge_money");?></b></td>
						<td><b><?php echo Lang::get("slave.order_recharge_unit");?></b></td>
						<td><b><?php echo Lang::get("slave.order_recharge_exchange");?></b></td>
						<td><b><?php echo Lang::get("slave.order_recharge_dollar");?></b></td>
                        <td><b><?php echo Lang::get('slave.goods_value');?></b></td>
                        <td><b><?php echo Lang::get('slave.giftbag_name');?></b></td>
						<td><b><?php echo Lang::get("slave.order_recharge_yuanbao");?></b></td>
						<td><b><?php echo Lang::get("slave.is_or_not_offer_yuanbao");?></b></td>
						<td></td>
						<td><b><?php echo Lang::get("slave.order_date");?></b></td>
						<td><b><?php echo Lang::get("slave.pay_time");?></b></td>
						<td><b><?php echo Lang::get("slave.order_stat");?></b></td>
						<td><b><?php echo Lang::get("slave.mycard_code");?></b></td>
						<td><b><?php echo Lang::get("slave.player_nickname");?></b></td>
						<td><b><?php echo Lang::get("slave.player_id");?></b></td>
						<td><b><?php echo Lang::get("slave.user_nickname");?></b></td>
						<td><b><?php echo Lang::get("slave.player_uid");?></b></td>
						<td><b><?php echo Lang::get("slave.web_id");?></b></td>
						<td><b><?php echo Lang::get("slave.server");?></b></td>
						<td><b><?php echo Lang::get('slave.bank_name');?></b></td>
						<td><b><?php echo Lang::get('slave.bank_user_name');?></b></td>
						<td><b><?php echo Lang::get('slave.bank_account');?></b></td>
						<td><b><?php echo Lang::get('slave.bank_pay_time');?></b></td>
						<td ng-if="formData.is_refund == 1"><b><?php echo Lang::get("slave.execute_action");?></b></td>

					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="t in total">
						<td>{{t.order_id}}</td>
						<td>{{t.order_sn}}</td>
						<td>{{t.tradeseq}} <button ng-if="t.tradeseq == ''" class="btn btn-danger" ng-click="TradeseqOrder(t.order_sn)"><?php echo Lang::get('slave.tradeseqorder');?></button></td>
						<td>{{t.combined_order}}</td>
						<td>{{t.pay_type_name}}</td>
						<td>{{t.method_name}} {{t.money_flow_name}}</td>
						<td>{{t.goods_type}}</td>
						<?php if(in_array(Session::get('game_id'), array(69,72))){ ?>
						<td ng-if="'Google Play'==t.pay_type_name && 'USD'==t.currency_code">{{t.pay_amount*0.9675 | number:2}}</td>
						<td ng-if="'Google Play'!=t.pay_type_name || 'USD'!=t.currency_code">{{t.pay_amount | number:2}}</td>
						<td>{{t.currency_code}}</td>
						<td>{{t.exchange}}</td>
						<td ng-if="'Google Play'==t.pay_type_name && 'USD'==t.currency_code">{{t.dollar_amount*0.9675 | number:2}}</td>
						<td ng-if="'Google Play'!=t.pay_type_name || 'USD'!=t.currency_code">{{t.dollar_amount | number:2}}</td>
						<td ng-if="'Google Play'==t.pay_type_name && 'USD'==t.currency_code && ''!=t.giftbag_name">{{t.goods_value*0.9675 | number:2}}</td>
						<td ng-if="'Google Play'!=t.pay_type_name || 'USD'!=t.currency_code || ''==t.giftbag_name">{{t.goods_value | number:2}}</td>
						<?php }else{ ?>
						<td>{{t.pay_amount | number:2}}</td>
						<td>{{t.currency_code}}</td>
						<td>{{t.exchange}}</td>
						<td>{{t.dollar_amount | number:2}}</td>
	                    <td>{{t.goods_value | number:2}}</td>
	                    <?php } ?>
                        <td>{{t.giftbag_name}}</td>
						<td>{{t.yuanbao_amount | number:2}}</td>
						<td ng-if="t.offer_yuanbao == 0 && formData.is_offer == 1">
							<button class="btn btn-danger" ng-click="offerYuanbao(t)"><?php echo Lang::get('slave.btn_offer_yuanbao')?></button>
						</td>
						<td ng-if="t.offer_yuanbao == 0 && formData.is_offer == 0 && t.already_record == 0">
							{{t.offer_yuanbao_txt}}
							<button class="btn btn-danger" ng-click="RecordOrder(t)"><?php echo Lang::get('slave.btn_record_order')?></button>
						</td>
						<td ng-if="t.offer_yuanbao == 0 && formData.is_offer == 0 && t.already_record == 1">
							{{t.offer_yuanbao_txt}}
							<div class="btn btn-info" ng-click="RecordOrder(t)"><?php echo Lang::get('slave.btn_already_record')?></div>
						</td>
						<td ng-if="t.offer_yuanbao == 0 && formData.is_offer == 0 && t.already_record == 9">
							{{t.offer_yuanbao_txt}}
							<div class="btn btn-info" ng-click="ShowResult(t.record_result)"><?php echo Lang::get('slave.dealt')?></div>
						</td>
						<td ng-if="t.offer_yuanbao == 1 && t.already_award == 1">
							{{t.offer_yuanbao_txt}}<div class="btn btn-info" ng-click="Awardorder(t)"><?php echo Lang::get('slave.btn_already_award')?></div>
						</td>
						<td ng-if="t.offer_yuanbao == 1 && t.already_award == 9">
							{{t.offer_yuanbao_txt}}<div class="btn btn-info" ng-click="ShowResult(t.record_result)"><?php echo Lang::get('slave.dealt')?></div>
						</td>
						<td ng-if="t.offer_yuanbao == 1 && t.already_award == 0">
							{{t.offer_yuanbao_txt}}<button class="btn btn-danger" ng-click="Awardorder(t)"><?php echo Lang::get('slave.btn_award_order')?></button>
						</td>
						<td ng-if="t.offer_yuanbao == 0 && t.get_payment == 1"><button class="btn btn-danger" ng-click="confirmYuanbao(t.order_sn,'/platform-api/payment/confirmyuanbao')"><?php echo Lang::get('slave.confirm')?></button></td>
						<td ng-if="t.offer_yuanbao != 0 || t.get_payment != 1"></td>
						<td>{{t.create_time}}</td>
						<td>{{t.pay_time}}</td>
						<td ng-if="t.order_status != 5">{{t.get_payment_txt}}</td>
						<td ng-if="t.order_status == 5"><?php echo Lang::get('slave.refunded');?></td>
						
						<td>{{t.mycard_activity_code}}</td>
						<td>{{t.player_name}}</td>
						<td>{{t.player_id}}</td>
						<td>{{t.nickname}}</td>
						<td>{{t.pay_user_id}}</td>
						<td>{{t.login_email}}</td>
						<td>{{t.server_name}}</td>
						<td>{{t.bank_name}}</td>
						<td>{{t.bank_user_name}}</td>
						<td>{{t.bank_account}}</td>	
						<td>{{t.bank_pay_time}}</td>
						<td ng-if="t.get_payment == 1 && t.order_status != 5 && formData.is_refund == 1">
						<button ng-click="refund(t)"><?php echo Lang::get('slave.act_refund');?></button>
						</td>
					</tr>
				</tbody>
			
			</table>
		</div>
	</div>


	<div class="row margin-top-10">
		<div class="col-xs-12">
			<table ng-if = "flag == 2 && formData.limit_order == 0">
				<tbody>
					<tr>
						<td>
							<table style="width:139px; table-layout:fixed;" class="table table-striped">
								<tbody>
									<tr><td><b><?php echo Lang::get("slave.order_id");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.order");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.order_external");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.combine_order");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.order_type");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.order_child_type");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.goods_type");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.order_recharge_money");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.order_recharge_unit");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.order_recharge_exchange");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.order_recharge_dollar");?></b></td></tr>
						            <tr><td><b><?php echo Lang::get('slave.goods_value');?></b></td></tr>
						            <tr><td><b><?php echo Lang::get('slave.giftbag_name');?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.order_recharge_yuanbao");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.is_or_not_offer_yuanbao");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.order_date");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.pay_time");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.order_stat");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.mycard_code");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.player_nickname");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.player_id");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.user_nickname");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.player_uid");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.web_id");?></b></td></tr>
									<tr><td><b><?php echo Lang::get("slave.server");?></b></td></tr>
									<tr><td><b><?php echo Lang::get('slave.bank_name');?></b></td></tr>
									<tr><td><b><?php echo Lang::get('slave.bank_user_name');?></b></td></tr>
									<tr><td><b><?php echo Lang::get('slave.bank_account');?></b></td></tr>
									<tr><td><b><?php echo Lang::get('slave.bank_pay_time');?></b></td></tr>
									<tr ng-if="formData.is_refund == 1"><td><b><?php echo Lang::get("slave.execute_action");?></b></td></tr>
								</tbody>
							</table>
						</td>
						<td ng-repeat = "t in total">
							<table class="table table-striped">
								<tbody>
									<tr><td>{{t.order_id}}</td></tr>
									<tr><td>{{t.order_sn}}</td></tr>
									<tr><td ng-if="t.tradeseq == null||t.tradeseq == ''"><button ng-if="t.tradeseq == ''" class="btn btn-danger" ng-click="TradeseqOrder(t.order_sn)"><?php echo Lang::get('slave.tradeseqorder');?></button></td>
										<td ng-if="t.tradeseq != null && t.tradeseq !=''">{{t.tradeseq}}</td></tr>
									<tr><td ng-if="t.combined_order == null||t.combined_order == ''">&nbsp;</td>
										<td ng-if="t.combined_order != null&&t.combined_order != ''">{{t.combined_order}}</td></tr>
									<tr><td>{{t.pay_type_name}}</td></tr>
									<tr><td>{{t.method_name}} {{t.money_flow_name}}</td></tr>
									<tr><td>{{t.goods_type}}</td></tr>
									<tr><td>{{t.pay_amount | number:2}}</td></tr>
									<tr><td>{{t.currency_code}}</td></tr>
									<tr><td>{{t.exchange}}</td></tr>
									<tr><td>{{t.dollar_amount | number:2}}</td></tr>
			                        <tr><td>{{t.goods_value | number:2}}</td></tr>
			                        <tr><td ng-if="t.giftbag_name == null||t.giftbag_name == ''">&nbsp;</td>
										<td ng-if="t.giftbag_name != null && t.giftbag_name !=''">{{t.giftbag_name}}</td></tr>
									<tr><td>{{t.yuanbao_amount | number:2}}</td></tr>
									<tr ng-if="t.offer_yuanbao == 0 && formData.is_offer == 1"><td>
										<button class="btn btn-danger" ng-click="offerYuanbao(t)"><?php echo Lang::get('slave.btn_offer_yuanbao')?></button>
									</td></tr>
									<tr ng-if="t.offer_yuanbao == 0 && formData.is_offer == 0 && t.already_record == 0"><td>
										{{t.offer_yuanbao_txt}}
										<button class="btn btn-danger" ng-click="RecordOrder(t)"><?php echo Lang::get('slave.btn_record_order')?></button><button class="btn btn-danger" ng-if="t.get_payment == 1" ng-click="confirmYuanbao(t.order_sn,'/platform-api/payment/confirmyuanbao')"><?php echo Lang::get('slave.confirm')?></button>
									</td></tr>
									<tr ng-if="t.offer_yuanbao == 0 && formData.is_offer == 0 && t.already_record == 1"><td>
										{{t.offer_yuanbao_txt}}
										<div class="btn btn-info" ng-click="RecordOrder(t)"><?php echo Lang::get('slave.btn_already_record')?></div><button class="btn btn-danger" ng-if="t.get_payment == 1" ng-click="confirmYuanbao(t.order_sn,'/platform-api/payment/confirmyuanbao')"><?php echo Lang::get('slave.confirm')?></button>
									</td></tr>
									<tr ng-if="t.offer_yuanbao == 0 && formData.is_offer == 0 && t.already_record == 9"><td>
										{{t.offer_yuanbao_txt}}
										<div class="btn btn-info" ng-click="ShowResult(t.record_result)"><?php echo Lang::get('slave.dealt')?></div><button class="btn btn-danger" ng-if="t.get_payment == 1" ng-click="confirmYuanbao(t.order_sn,'/platform-api/payment/confirmyuanbao')"><?php echo Lang::get('slave.confirm')?></button>
									</td></tr>
									<tr ng-if="t.offer_yuanbao == 1 && t.already_award == 1"><td>
										{{t.offer_yuanbao_txt}}<div class="btn btn-info" ng-click="Awardorder(t)"><?php echo Lang::get('slave.btn_already_award')?></div>
									</td></tr>
									<tr ng-if="t.offer_yuanbao == 1 && t.already_award == 9"><td>
										{{t.offer_yuanbao_txt}}<div class="btn btn-info" ng-click="ShowResult(t.record_result)"><?php echo Lang::get('slave.dealt')?></div>
									</td></tr>
									<tr ng-if="t.offer_yuanbao == 1 && t.already_award == 0"><td>
										{{t.offer_yuanbao_txt}}<button class="btn btn-danger" ng-click="Awardorder(t)"><?php echo Lang::get('slave.btn_award_order')?></button>
									</td></tr>
									<tr><td>{{t.create_time}}</td></tr>
									<tr><td>{{t.pay_time}}</td></tr>
									<tr ng-if="t.order_status != 5"><td>{{t.get_payment_txt}}</td></tr>
									<tr ng-if="t.order_status == 5"><td><?php echo Lang::get('slave.refunded');?></td></tr>
									<tr><td ng-if="t.mycard_activity_code == null||t.mycard_activity_code == ''">&nbsp;</td>
										<td ng-if="t.mycard_activity_code != null && t.mycard_activity_code !=''">{{t.mycard_activity_code}}</td></tr>
									<tr><td ng-if="t.player_name == null||t.player_name == ''">&nbsp;</td>
										<td ng-if="t.player_name != null && t.player_name !=''">{{t.player_name}}</td></tr>
									<tr><td ng-if="t.player_id == null||t.player_id == ''">&nbsp;</td>
										<td ng-if="t.player_id != null && t.player_id !=''">{{t.player_id}}</td></tr>
									<tr><td ng-if="t.nickname == null||t.nickname == ''">&nbsp;</td>
										<td ng-if="t.nickname != null && t.nickname !=''">{{t.nickname}}</td></tr>
									<tr><td ng-if="t.pay_user_id == null||t.pay_user_id == ''">&nbsp;</td>
										<td ng-if="t.pay_user_id != null && t.pay_user_id !=''">{{t.pay_user_id}}</td></tr>
									<tr><td>{{t.login_email}}</td></tr>
									<tr><td>{{t.server_name}}</td></tr>
									<tr><td ng-if="t.bank_name == null||t.bank_name == ''">&nbsp;</td>
										<td ng-if="t.bank_name != null && t.bank_name !=''">{{t.bank_name}}</td></tr>
									<tr><td ng-if="t.bank_user_name == null||t.bank_user_name == ''">&nbsp;</td>
										<td ng-if="t.bank_user_name != null && t.bank_user_name !=''">{{t.bank_user_name}}</td></tr>
									<tr><td ng-if="t.bank_account == null||t.bank_account == ''">&nbsp;</td>
										<td ng-if="t.bank_account != null && t.bank_account !=''">{{t.bank_account}}</td></tr>
									<tr><td ng-if="t.bank_pay_time == null||t.bank_pay_time == ''">&nbsp;</td>
										<td ng-if="t.bank_pay_time != null && t.bank_pay_time !=''">{{t.bank_pay_time}}</td></tr>
									
									<tr ng-if="t.get_payment == 1 && t.order_status != 5 && formData.is_refund == 1"><td>
									<button ng-click="refund(t)"><?php echo Lang::get('slave.act_refund');?></button>
									</td></tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<div class="row margin-top-10">
		<div class="col-xs-12">
			<table ng-if = "formData.limit_order == 1" class="table table-striped">
				<thead>
					<tr class="info">
						<td><b><?php echo Lang::get("slave.player_id");?></b></td>
						<td><b><?php echo Lang::get("slave.order_recharge_yuanbao");?></b></td>
						<td><b><?php echo Lang::get("slave.server");?></b></td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="t in total">
						<td>{{t.player_id}}</td>
						<td>{{t.yuanbao_amount | number:2}}</td>
						<td>{{t.server_name}}</td>
						</td>
					</tr>
				</tbody>
			
			</table>
		</div>
	</div>


</div>


<script type="text/ng-template" id="offer_yuanbao.html">
        <div class="modal-header">
        </div>
		<form action="/platform-api/order/offer" method="post" role="form" ng-submit="offerYuanbaoForm('/platform-api/order/offer')" onsubmit="return false;">
        <div class="modal-body">
        	<div class="form-group">
				<label><?php echo Lang::get('slave.order_sn')?>:</label>	
				<input type="text" class="form-control" readonly ng-model="orderData.order_sn" ng-init="orderData.order_sn = order.order_sn"?>
			</div>
			<div class="form-group">
				<label><?php echo Lang::get('slave.tradeseq')?>:</label>	
				<input type="text" class="form-control" ng-model="orderData.tradeseq" ng-init="orderData.tradeseq = order.tradeseq"?>
			</div>
			<div class="form-group">
				<label><?php echo Lang::get('slave.pay_amount')?></label>
				<input type="text" class="form-control" ng-model="orderData.pay_amount" ng-init="orderData.pay_amount = order.pay_amount"?>
			</div>
			<div class="form-group">
				<label><?php echo Lang::get('slave.basic_yuanbao')?>:</label>
				<input type="text" class="form-control" ng-model="orderData.basic_yuanbao_amount" ng-init="orderData.basic_yuanbao_amount= order.basic_yuanbao_amount"?>
			</div>
			<div class="form-group">
				<label><?php echo Lang::get('slave.extra_yuanbao')?>:</label>
				<input type="text" class="form-control" ng-model="orderData.extra_yuanbao_amount" ng-init="orderData.extra_yuanbao_amount = order.extra_yuanbao_amount"?>
			</div>
			<div class="form-group">
				<label><?php echo Lang::get('slave.huodong_yuanbao')?>:</label>
				<input type="text" class="form-control" ng-model="orderData.huodong_yuanbao_amount" ng-init="orderData.huodong_yuanbao_amount = order.huodong_yuanbao_amount"?>
			</div>
			<div class="form-group">
				<label><?php echo Lang::get('slave.all_yuanbao')?>:</label>
				<input type="text" class="form-control" ng-model="orderData.yuanbao_amount" ng-init="orderData.yuanbao_amount = order.yuanbao_amount"?>
			</div>
			<div class="form-group">
				<label>礼包ID:</label>
				<input type="text" class="form-control" ng-model="orderData.giftbag_id" ng-init="orderData.giftbag_id = order.giftbag_id"?>
			</div>
			<div class="form-group" ng-if="order.pay_type_id == 1 && region_id == 1">
				<label><?php echo Lang::get('slave.mycard_id')?>:</label>
				<input type="text" class="form-control" ng-model="orderData.mycard_id"?>
			</div>
        </div>
		<input type="hidden" ng-model="orderData.order_id" ng-init="orderData.order_id = order.order_id" name="order_id"/>
		<input type="hidden" ng-model="orderData.player_id" ng-init="orderData.player_id = order.player_id" name="player_id"/>
		<input type="hidden" ng-model="orderData.server_name" ng-init="orderData.server_name = order.server_name" name="server_name"/>
		<input type="hidden" ng-model="orderData.player_name" ng-init="orderData.player_name = order.player_name" name="player_name"/>
        <div class="modal-footer" style="text-align:center;">
			<button class="btn btn-primary"><?php echo Lang::get('slave.btn_offer_yuanbao')?></button>
            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
        </div>
		</form>
</script>


<script type="text/ng-template" id="refund_order.html">
        <div class="modal-header">
        </div>
		<form action="/slave-api/payment/order/refund/act" method="post" role="form" ng-submit="refundOrder('/slave-api/payment/order/refund/act')" onsubmit="return false;">
        <div class="modal-body">
			<div class="form-group">
				<label><?php echo Lang::get('slave.refund_amount')?>:</label>	
				<input type="text" class="form-control" ng-model="orderData.refund_amount" autofocus="autofocus" />
			</div>
        </div>
        <input type="hidden" ng-model="orderData.order_id" ng-init="orderData.order_id = order.order_id" />
		<input type="hidden" ng-model="orderData.order_sn" ng-init="orderData.order_sn = order.order_sn" />
		<input type="hidden" ng-model="orderData.tradeseq" ng-init="orderData.tradeseq = order.tradeseq" />
		<input type="hidden" ng-model="orderData.pay_user_id" ng-init="orderData.pay_user_id = order.pay_user_id" />
		<input type="hidden" ng-model="orderData.currency_code" ng-init="orderData.currency_code = order.currency_code" />
		<input type="hidden" ng-model="orderData.pay_type_name" ng-init="orderData.pay_type_name = order.pay_type_name" />
		<input type="hidden" ng-model="orderData.pay_amount" ng-init="orderData.pay_amount = order.pay_amount" />
		<input type="hidden" ng-model="orderData.basic_yuanbao_amount" ng-init="orderData.basic_yuanbao_amount = order.basic_yuanbao_amount" />
		<input type="hidden" ng-model="orderData.extra_yuanbao_amount" ng-init="orderData.extra_yuanbao_amount = order.extra_yuanbao_amount" />
		<input type="hidden" ng-model="orderData.huodong_yuanbao_amount" ng-init="orderData.huodong_yuanbao_amount = order.huodong_yuanbao_amount" />
		<input type="hidden" ng-model="orderData.yuanbao_amount" ng-init="orderData.yuanbao_amount = order.yuanbao_amount" />
		<input type="hidden" ng-model="orderData.server_internal_id" ng-init="orderData.server_internal_id = order.server_internal_id" />
        <div class="modal-footer" style="text-align:center;">
			<button class="btn btn-primary"><?php echo Lang::get('basic.btn_submit')?></button>
            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
        </div>
		</form>
</script>

<script type="text/ng-template" id="record_order.html">
        <div class="modal-header">
        </div>
		<form action="" method="post" role="form" ng-submit="recordOrder('/slave-api/payment/order')" onsubmit="return false;">
			<div class="modal-body">
				<div class="form-group">
					<label><?php echo Lang::get('slave.order_id')?>:</label>
					<input type="text" class="form-control" readonly ng-model="record_order.order_id" ng-init="record_order.order_id = record_order_init.order_id"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('slave.order_sn')?>:</label>
					<input type="text" class="form-control" readonly ng-model="record_order.order_sn" ng-init="record_order.order_sn= record_order_init.order_sn"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('slave.tradeseq')?>:</label>
					<input type="text" class="form-control" ng-model="record_order.tradeseq" ng-init="record_order.tradeseq= record_order_init.tradeseq"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('slave.pay_amount')?>:</label>
					<input type="text" class="form-control" readonly ng-model="record_order.pay_amount" ng-init="record_order.pay_amount= record_order_init.pay_amount"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('slave.currency_code')?>:</label>
					<input type="text" class="form-control" readonly ng-model="record_order.currency_code" ng-init="record_order.currency_code= record_order_init.currency_code"?>
				</div>
				<div class="form-group">
					<input type="hidden" class="form-control" readonly ng-model="record_order.pay_type_name" ng-init="record_order.pay_type_name= record_order_init.pay_type_name"?>
					<input type="hidden" class="form-control" readonly ng-model="record_order.method_name" ng-init="record_order.method_name= record_order_init.method_name"?>
					<input type="hidden" class="form-control" readonly ng-model="record_order.pay_user_id" ng-init="record_order.pay_user_id= record_order_init.pay_user_id"?>
					<input type="hidden" class="form-control" readonly ng-model="record_order.player_id" ng-init="record_order.player_id= record_order_init.player_id"?>
					<input type="hidden" class="form-control" readonly ng-model="record_order.player_name" ng-init="record_order.player_name= record_order_init.player_name"?>
					<input type="hidden" class="form-control" readonly ng-model="record_order.server_name" ng-init="record_order.server_name= record_order_init.server_name"?>
				</div>
				<div class="form-group">	
					<label><?php echo Lang::get('slave.reason')?>:</label>
					<input type="text" class="form-control" required ng-model="record_order.reason" autofocus="autofocus"?>
				</div>
				<input type="button" class="btn btn-primary" ng-click="uploadpic(record_order_init)" value="<?php echo Lang::get('basic.add_pic')?>" />
			</div>
	        <div class="modal-footer" style="text-align:center;">
				<button class="btn btn-primary"><?php echo Lang::get('basic.btn_submit')?></button>
	            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
	        </div>
		</form>
</script>

<script type="text/ng-template" id="uploadpic.html">
        <div class="modal-header">
        </div>
        <form onsubmit="return false;" ng-submit="upload('/slave-api/payment/order')">
			<div class="modal-body">
				<div class="form-group">	
					<p><?php echo Lang::get('slave.choose_pic_file')?><font color="red"><?php echo Lang::get('slave.pic_file_size')?></font>:<br/></p>
						<input type="file" class="form-control" id="file_upload" name="file_upload" />
						<button class="btn btn-primary"/><?php echo Lang::get('slave.submit'); ?></button>
				</div>
			</div>
			<div class="modal-footer" style="text-align:center;">
		            <a class="btn btn-warning" ng-click="cancel()"><?php echo Lang::get('basic.btn_close')?></a>
		        </div>
	    </form>
</script>

<script type="text/ng-template" id="award_order.html">
        <div class="modal-header">
        </div>
		<form action="" method="post" role="form" ng-submit="AwardOrder('/slave-api/payment/order')" onsubmit="return false;">
			<div class="modal-body">
				<div class="form-group">
					<label><?php echo Lang::get('slave.order_id')?>:</label>
					<input type="text" class="form-control" readonly ng-model="record_order.order_id" ng-init="record_order.order_id = record_order_init.order_id"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('slave.order_sn')?>:</label>
					<input type="text" class="form-control" readonly ng-model="record_order.order_sn" ng-init="record_order.order_sn= record_order_init.order_sn"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('slave.tradeseq')?>:</label>
					<input type="text" class="form-control" readonly ng-model="record_order.tradeseq" ng-init="record_order.tradeseq= record_order_init.tradeseq"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('slave.pay_amount')?>:</label>
					<input type="text" class="form-control" readonly ng-model="record_order.pay_amount" ng-init="record_order.pay_amount= record_order_init.pay_amount"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('slave.currency_code')?>:</label>
					<input type="text" class="form-control" readonly ng-model="record_order.currency_code" ng-init="record_order.currency_code= record_order_init.currency_code"?>
				</div>
				<div class="form-group">
					<input type="hidden" class="form-control" readonly ng-model="record_order.pay_type_name" ng-init="record_order.pay_type_name= record_order_init.pay_type_name"?>
					<input type="hidden" class="form-control" readonly ng-model="record_order.method_name" ng-init="record_order.method_name= record_order_init.method_name"?>
					<input type="hidden" class="form-control" readonly ng-model="record_order.pay_user_id" ng-init="record_order.pay_user_id= record_order_init.pay_user_id"?>
					<input type="hidden" class="form-control" readonly ng-model="record_order.player_id" ng-init="record_order.player_id= record_order_init.player_id"?>
					<input type="hidden" class="form-control" readonly ng-model="record_order.player_name" ng-init="record_order.player_name= record_order_init.player_name"?>
					<input type="hidden" class="form-control" readonly ng-model="record_order.server_name" ng-init="record_order.server_name= record_order_init.server_name"?>
				</div>
				<div class="form-group">	
					<label><?php echo Lang::get('slave.award_content')?>:</label>
					<input type="text" class="form-control" required ng-model="record_order.reason" autofocus="autofocus"?>
				</div>
			</div>
	        <div class="modal-footer" style="text-align:center;">
				<button class="btn btn-primary"><?php echo Lang::get('basic.btn_submit')?></button>
	            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
	        </div>
		</form>
</script>

<script type="text/ng-template" id="tradeseq_order.html">
        <div class="modal-header">
        </div>
		<form action="" method="post" role="form" ng-submit="TradeseqOrder('/platform-api/payment/tradeseq/add')" onsubmit="return false;">
			<div class="modal-body">
				<div class="form-group">
					<label><?php echo Lang::get("slave.order_external");?>:</label>
					<input type="text" class="form-control" ng-model="order.tradeseq" ng-init="order.tradeseq"?>
				</div>
			</div>
			<input type="hidden" class="form-control" ng-model="order.order_sn" ng-init="order.order_sn= order_init[0]"?>
	        <div class="modal-footer" style="text-align:center;">
				<button class="btn btn-primary"><?php echo Lang::get('basic.btn_submit')?></button>
	            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
	        </div>
		</form>
</script>

<script type="text/ng-template" id="show_result.html">
        <div class="modal-header">
        </div>
		<div class="modal-body">
			<div class="form-group">
				<label><?php echo Lang::get("slave.result");?>:</label>
				<input type="text" class="form-control" readonly ng-model="result_display.result" ng-init="result_display.result=showresult.result" ?>
			</div>
			<div class="form-group">
				<a href="/slave-api/payment/order/record/gm" target="record_order"><?php echo Lang::get('slave.record_check'); ?></a><br>
				<a href="/slave-api/payment/order/award/gm" target="award_order"><?php echo Lang::get('slave.award_check'); ?></a>
			</div>
		</div>
        <div class="modal-footer" style="text-align:center;">
            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
        </div>
</script>