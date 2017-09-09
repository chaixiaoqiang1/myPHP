<script> 
function getPaymentOrderListController($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.file='';
    $scope.start_time = null;
    $scope.end_time = null;
    $scope.formData = {};
	$scope.items = [];
	$scope.pagination = {};
	//pagination
	$scope.pagination.totalItems = 0;
	$scope.pagination.currentPage = 1;
	$scope.pagination.perPage= 1;

	$scope.$watch('pagination.currentPage', function(newPage, oldPage) {
		if ($scope.end_time > 0) {
			$scope.processFrom(newPage);
		}
	});
    $scope.processFrom = function(newPage) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        $http({
            'method': 'post',
            'url': '/slave-api/payment/order/list?page=' + newPage,
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
        	$scope.pagination.currentPage = data.current_page;
			$scope.pagination.perPage= data.per_page;
			$scope.pagination.totalItems = data.count;
			$scope.items = data.items;
			location.hash = '#top';
		}).error(function(data) {
			alertService.add('danger', data.error);
		});
    };
    $scope.download = function(url) {
		alertService.alerts = $scope.alerts;
		$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
	     $http({
        'method': 'post',
        'url': url,
        'data': $.param($scope.formData),
        'headers': {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    	}).success(function(data) {
    		alertService.add('success', 'OK');
    		console.log(data.now);
        	window.location.replace("/slave-api/payment/order/download?now=" + data.now);
		}).error(function(data) {
			alertService.add('danger', data.error);
		});
	};

  	$scope.$watch('formData.pay_type_id', function(newValue, oldValue){
  		$scope.formData.hasChange = newValue==oldValue;
  		$scope.formData.newValue = newValue;
  		$scope.formData.child_pay_type = "999";
  	});
} 
</script>
<div class="col-xs-12" ng-controller="getPaymentOrderListController" style="overflow:auto">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/slave-api/payment/order/list" method="get" role="form"
				ng-submit="processFrom(1)" onsubmit="return false;">

				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding: 0">
						<select class="form-control" name="server_id"
							id="select_game_server" ng-model="formData.server_id"
							ng-init="formData.server_id=0">
							<option value="0"><?php echo Lang::get('slave.show_all_servers') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
					</div>
					<div class="col-md-3" style="padding: 1">
						<select class="form-control" name="pay_type_id"
							id="select_pay_type_id" ng-model="formData.pay_type_id"
							ng-init="formData.pay_type_id=0">
							<option value="0"><?php echo Lang::get('slave.all_pay_types') ?></option>
							<?php foreach ($pay_types as $k => $v) { ?>
							<option value="<?php echo $v->pay_type_id?>"><?php echo $v->pay_type_name;?></option>
							<?php } ?>		
						</select>
					</div>
					<div class="col-md-3" style="padding: 1">
						<select class="form-control" 
							ng-model="formData.child_pay_type" ng-init="formData.child_pay_type=999">
							<option value="999"><?php echo Lang::get('slave.child_pay_type') ?></option>
						<?php foreach ($child_pay as $key=>$child) { ?>
							<?php foreach ($child as $k => $v) { ?>
								<option ng-if="formData.newValue==<?php echo $key ?>" value="<?php echo $v->method_id ?>">
									<?php echo ($v->is_use ?'&#9787':'&#10008').$v->method_name; ?>
								</option>	
							<?php } ?>
						<?php } ?>
						</select>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding: 0">
						<select class="form-control" name="get_payment"
							ng-model="formData.get_payment" ng-init="formData.get_payment=2">
							<option value="2"><?php echo Lang::get('slave.order_all_order_statics') ?></option>
							<option value="0"><?php echo Lang::get('slave.order_statics_un_pay') ?></option>
							<option value="1"><?php echo Lang::get('slave.order_statics_complete') ?></option>
						</select>
					</div>
					<div class="col-md-6" style="padding: 2">
						<select class="form-control" name="is_recharge_in_game"
							id="select_is_recharge_in_game"
							ng-model="formData.is_recharge_in_game"
							ng-init="formData.is_recharge_in_game=2">
							<option value="2"><?php echo Lang::get('slave.yes_or_no_recharge_in_game') ?></option>
							<option value="0"><?php echo Lang::get('slave.no_recharge_in_game') ?></option>
							<option value="1"><?php echo Lang::get('slave.yes_recharge_in_game') ?></option>
						</select>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding: 0">
						<input type="text" class="form-control"
							ng-model="formData.lower_bound" name="lower_bound"
							placeholder="<?php echo Lang::get('slave.pay_money_lower_bound') ?>" />
					</div>
					<div class="col-md-6" style="padding: 2">
						<input type="text" class="form-control"
							ng-model="formData.upper_bound" name="upper_bound"
							placeholder="<?php echo Lang::get('slave.pay_money_upper_bound') ?>" />
					</div>
				</div>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding: 0">
						<input type="text" class="form-control"
							ng-model="formData.lower_gold" name="lower_gold"
							placeholder="<?php echo Lang::get('slave.pay_money_lower_gold') ?>" />
					</div>
					<div class="col-md-6" style="padding: 2">
						<input type="text" class="form-control"
							ng-model="formData.upper_gold" name="upper_gold"
							placeholder="<?php echo Lang::get('slave.pay_money_upper_gold') ?>" />
					</div>
				</div>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding: 0">
						<input type="text" class="form-control"
							ng-model="formData.sdk_id" name="sdk_id"
							placeholder="<?php echo Lang::get('slave.order_by_channel') ?>" />
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-4" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-4" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
                    <div class="col-md-4" style="padding: 0 0 0 0">
                        <div class="input-group">
                            <select class="form-control" name="statistics_time"
                                    id="select_order_statistics_time"
                                    ng-model="formData.statistics_time"
                                    ng-init="formData.statistics_time=0">
                                <option value="0"><?php echo Lang::get('slave.order_create_time') ?></option>
                                <option value="1"><?php echo Lang::get('slave.order_pay_time') ?></option>
                            </select>
                        </div>
                    </div>
				</div>
				<div class="clearfix"></div>
				<div class="form-group">
					Per Page: <label> <input name="per_page" value="30" type="radio"
						ng-checked="true" ng-model="formData.per_page"
						ng-init="formData.per_page=30" /> 30
					</label> <label> <input name="per_page" value="100" type="radio"
						ng-model="formData.per_page" /> 100
					</label> <label> <input name="per_page" value="500" type="radio"
						ng-model="formData.per_page" /> 500
					</label> <label> <input name="per_page" value="1000" type="radio"
						ng-model="formData.per_page" /> 1000
					</label> <label> <input name="per_page" value="2000" type="radio"
						ng-model="formData.per_page" /> 2000
					</label>
				</div>
				<div class="col-md-6" style="padding: 0">
					<input type="submit" class="btn btn-default" style=""
						value="<?php echo Lang::get('basic.btn_submit') ?>" />
                    <?php echo Lang::get("slave.order_list_tips")?>
                </div>
				<div class="col-md-4" style="padding: 30">
					<input type='button' class="btn btn-warning"
						value="<?php echo Lang::get('serverapi.download_csv') ?>"
						ng-click="download('/slave-api/payment/order/download')" />
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
	<!-- 	<div class="row margin-top-10"> -->
	<div class="col-xs-12" style="padding: 0;">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get("slave.order_id");?></b></td>
					<td><b><?php echo Lang::get("slave.order_by_channel");?></b></td>
					<td><b><?php echo Lang::get("slave.order");?></b></td>
					<td><b><?php echo Lang::get("slave.order_external");?></b></td>
					<td><b><?php echo Lang::get("slave.combine_order");?></b></td>
					<td><b><?php echo Lang::get("slave.order_type");?></b></td>
					<td><b><?php echo Lang::get("slave.order_child_type");?></b></td>
					<td><b><?php echo Lang::get("slave.order_recharge_money");?></b></td>
					<td><b><?php echo Lang::get("slave.order_recharge_unit");?></b></td>
					<td><b><?php echo Lang::get("slave.order_recharge_exchange");?></b></td>
					<td><b><?php echo Lang::get("slave.order_recharge_dollar");?></b></td>
                    <td><b><?php echo Lang::get('slave.goods_value');?></b></td>
                    <td><b><?php echo Lang::get('slave.giftbag_name');?></b></td>
					<td><b><?php echo Lang::get("slave.order_recharge_yuanbao");?></b></td>
					<td><b>Product_ID</b></td>
					<td><b><?php echo Lang::get("slave.is_or_not_offer_yuanbao");?></b></td>
					<td><b><?php echo Lang::get("slave.order_date");?></b></td>
					<td><b><?php echo Lang::get("slave.pay_time");?></b></td>
					<td><b><?php echo Lang::get("slave.order_stat");?></b></td>
					<td><b><?php echo Lang::get("slave.player_nickname");?></b></td>
					<td><b><?php echo Lang::get("slave.player_id");?></b></td>
					<td><b><?php echo Lang::get("slave.player_uid");?></b></td>
					<td><b><?php echo Lang::get("slave.web_name");?></b></td>
					<td><b><?php echo Lang::get("slave.server");?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.order_id}}</td>
					<td>{{t.sdk_id}}</td>
					<td>{{t.order_sn}}</td>
					<td>{{t.tradeseq}}</td>
					<td>{{t.combined_order}}</td>
					<td>{{t.pay_type_name}}</td>
					<td>{{t.method_name}} {{t.money_flow_name}}</td>
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
					<td>{{t.product_name}}</td>
					<td style="background-color:pink" 
						ng-if="t.offer_yuanbao == 0 && t.get_payment == 1">{{t.offer_yuanbao_txt}}</td>
					<td ng-if="t.offer_yuanbao == 0 && t.get_payment == 0">{{t.offer_yuanbao_txt}}</td>
					<td ng-if="t.offer_yuanbao == 1">{{t.offer_yuanbao_txt}}</td>

					<td>{{t.create_time}}</td>
					<td>{{t.pay_time}}</td>
					<td>{{t.get_payment_txt}}</td>
					<td>{{t.player_name}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.pay_user_id}}</td>
					<td>{{t.nickname}}</td>
					<td>{{t.server_name}}</td>
				</tr>
				</tbody>
		</table>
		<div ng-show="!!pagination.totalItems">
			<pagination total-items="pagination.totalItems"
				page="pagination.currentPage" class="pagination-sm"
				boundary-links="true" rotate="false"
				items-per-page="pagination.perPage" max-size="10"></pagination>
		</div>
	</div>
	<!-- 	</div> -->
</div>