<script>
	function getRecordOrdersController($scope, $filter, $modal, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.items = [];
		$scope.show = 0;
		$scope.pagecontrol = 0;
		$scope.pagination = {};

		$scope.pagination.totalItems = 0;
		$scope.pagination.currentPage = 1;
		$scope.pagination.perPage= 50;

		$scope.$watch('pagination.currentPage', function(newPage, oldPage) {
			if ($scope.pagecontrol > 0 && newPage != oldPage) {
				$scope.processFrom('search', newPage);
			}
		});

		$scope.processFrom = function(type, newPage) {
			$scope.formData.is_gm = <?php echo $is_gm; ?>;
			$scope.formData.type = type;
			$scope.formData.order_type = 'fail';
			$scope.formData.page = newPage;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/payment/order/record<?php echo ($is_gm ? '/gm' : '') ?>',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.pagecontrol = 1;
				$scope.show = 1;
				$scope.pagination.currentPage = data.current_page;
				$scope.pagination.totalItems = data.count;
				$scope.orders = data.orders;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};

		$scope.update = function(order){
			var modalInstance = $modal.open({
				templateUrl: 'update.html',
				controller: modalUpdateRecordController,
				resolve: {
					order : function () {
						return order;
					}
				},
				backdrop : false,
				keyboard : false
			});
			modalInstance.result.then(function() {
				$scope.processFrom('search', 1);	
			});
		}

		$scope.ResetOrder = function(order){
			var modalInstance = $modal.open({
				templateUrl: 'reset.html',
				controller: modalResetRecordController,
				resolve: {
					order : function () {
						return order;
					}
				},
				backdrop : false,
				keyboard : false
			});
			modalInstance.result.then(function() {
				$scope.processFrom('search', 1);	
			});			
		}

		$scope.showimg = function(order){
		var modalInstance = $modal.open({
			templateUrl: 'check.html',
			controller: checkController,
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

		$scope.FinishOrder = function(id){
			$scope.formData.is_gm = <?php echo $is_gm; ?>;
			$scope.formData.type = 'finish';
			$scope.formData.order_type = 'fail';
			$scope.formData.id = id;
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/payment/order/record<?php echo ($is_gm ? '/gm' : '') ?>',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alert(data.msg);
				$scope.processFrom('search', 1);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});			
		}

		$(document).ready(function(){
			$scope.processFrom('search', 1);
		});
	}

	function modalUpdateRecordController($scope, $modalInstance, order, $http, alertService) {
		$scope.record_order_init = order;
		$scope.UpdateData = {};
		$scope.UpdateData.id = $scope.record_order_init.id;
		$scope.UpdateData.type = 'update';
		$scope.cancel = function() {
			$modalInstance.dismiss('cancel');
		}
		$scope.UpdateForm= function() {
			$http({
				'method' : 'post',
				'url' : '/slave-api/payment/order/record<?php echo ($is_gm ? '/gm' : '') ?>',
				'data' : $.param($scope.UpdateData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alert(data.msg);
				$modalInstance.close();
			}).error(function(data) {
				alert('error: ' + data.error);
			});
		}
	}

	function modalResetRecordController($scope, $modalInstance, order, $http, alertService) {
		$scope.record_order_init = order;
		$scope.ResetData = {};
		$scope.ResetData.id = $scope.record_order_init.id;
		$scope.ResetData.type = 'reset';
		$scope.cancel = function() {
			$modalInstance.dismiss('cancel');
		}
		$scope.ResetForm= function() {
			$http({
				'method' : 'post',
				'url' : '/slave-api/payment/order/record<?php echo ($is_gm ? '/gm' : '') ?>',
				'data' : $.param($scope.ResetData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alert(data.msg);
				$modalInstance.close();
			}).error(function(data) {
				alert('error: ' + data.error);
			});
		}
	}

	function checkController($scope, $modalInstance, order, $http, alertService) {
		$scope.order = order;
		$scope.cancel = function() {
			$modalInstance.dismiss('cancel');
		}
	}
</script>
<div class="col-xs-12" ng-controller="getRecordOrdersController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/slave-api/economy/parts" method="get" role="form"
				ng-submit="processFrom('search', 1)" onsubmit="return false;">
				<div class="form-group col-md-4" style="padding-left:0;">
					<select class="form-control" name="by_time"
						id="if_time" ng-model="formData.by_time" ng-init = "formData.by_time = <?php echo $is_gm; ?>" />
						<option value="0"><?php echo Lang::get('slave.not_by_time'); ?></option>
						<option value="1"><?php echo Lang::get('slave.by_time'); ?></option>
					</select>
				</div>
				<div class="form-group col-md-4" style="padding-left:0;">
					<select class="form-control" name="already_deal"
						id="already_deal" ng-model="formData.already_deal" ng-init = "formData.already_deal = <?php echo $is_gm; ?>" />
						<option value="0"><?php echo Lang::get('slave.not_deal'); ?></option>
						<option value="1"><?php echo Lang::get('slave.dealt_not_done'); ?></option>
						<option value="2"><?php echo Lang::get('slave.order_done'); ?></option>
						<option value="3"><?php echo Lang::get('slave.not_limit'); ?></option>
					</select>
				</div>
				<div class="form-group col-md-4" style="padding-left:0;">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.enter_order_number')?>"
						ng-model="formData.order_sn" name="order_sn"?>
				</div>
				<div class="form-group col-md-4" style="padding-left:0;">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.enter_tradeseq_number')?>"
						ng-model="formData.tradeseq" name="tradeseq"?>
				</div>
				<div class="form-group col-md-4" style="padding-left:0;">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.enter_player_uid')?>"
						ng-model="formData.player_uid" >
				</div>
				<div class="form-group col-md-4" style="padding-left:0;">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.enter_order_id')?>"
						ng-model="formData.order_id">
				</div>
				<div class="form-group col-md-4" style="padding-left:0;">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.enter_player_id')?>"
						ng-model="formData.player_id">
				</div>
				<div class="form-group col-md-4" style="padding-left:0;">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.enter_player_name')?>"
						ng-model="formData.player_name">
				</div>
				<div class="form-group" ng-show="formData.by_time == 1">
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
				<div class="clearfix"></div>
				<br>
				<input type="submit" class="btn btn-danger"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />

			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="col-xs-12" ng-if="show == 1">
		<?php if(!$is_gm){ ?>
		<p style="color:red;font-size:15px"><b>特别提醒：为了方便财务补储，点击order_id将打开对应游戏下的订单查询功能（如果并不是您当前所在的游戏，<br>则会切换到order_id对应的游戏，如果切换有问题可能是没有该游戏权限，请申请权限。）</b></p>
		<?php } ?>
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.order_id'); ?></b></td>
					<td><b><?php echo Lang::get('slave.order_sn'); ?></b></td>
					<td><b><?php echo Lang::get('slave.tradeseq'); ?></b></td>
					<td><b><?php echo Lang::get('slave.order_type'); ?></b></td>
					<td><b><?php echo Lang::get('slave.order_child_type'); ?></b></td>
					<td><b><?php echo Lang::get('slave.order_created_time'); ?></b></td>
					<td><b><?php echo Lang::get('slave.pay_amount'); ?></b></td>
					<td><b><?php echo Lang::get('slave.currency_code'); ?></b></td>
					<td><b><?php echo Lang::get('slave.player_id'); ?></b></td>
					<td><b><?php echo Lang::get('slave.player_name'); ?></b></td>
					<td><b><?php echo Lang::get('slave.server_name'); ?></b></td>
					<td><b><?php echo Lang::get('slave.pay_user_id'); ?></b></td>
					<td><b><?php echo Lang::get('slave.reason'); ?></b></td>
					<td><b><?php echo Lang::get('slave.record_created_time'); ?></b></td>
					<td><b><?php echo Lang::get('slave.created_operator'); ?></b></td>
					<td><b><?php echo Lang::get('slave.game_name'); ?></b></td>
					<td><b><?php echo Lang::get('slave.last_operator'); ?></b></td>
					<td><b><?php echo Lang::get('slave.deal_time'); ?></b></td>
					<td><b><?php echo Lang::get('slave.result'); ?></b></td>
					<td><b><?php echo Lang::get('slave.is_done'); ?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="o in orders">
					<?php if(!$is_gm){ ?>
					<td><a href="/slave-api/payment/order?order_id={{o.order_id}}&game_id={{o.game_id}}" target="order_player"><button class="btn btn-primary">{{o.order_id}}</button></a></td>
					<?php }else{ ?>
					<td>{{o.order_id}}</td>
					<?php } ?>
					<td>{{o.order_sn}}</td>
					<td>{{o.tradeseq}}</td>
					<td>{{o.pay_type_name}}</td>
					<td>{{o.method_name}}</td>
					<td>{{o.order_created_time}}</td>
					<td>{{o.pay_amount}}</td>
					<td>{{o.currency_code}}</td>
					<td>{{o.player_id}}</td>
					<td>{{o.player_name}}</td>
					<td>{{o.server_name}}</td>
					<td>{{o.pay_user_id}}</td>
					<td style="color:red">{{o.reason}}<button ng-if="o.if_img == 1" ng-click="showimg(o)" class="btn btn-primary"><?php echo Lang::get('slave.check_img'); ?></button></td>
					<td>{{o.created_time}}</td>
					<td>{{o.created_operator}}</td>
					<td>{{o.game_name}}</td>
					<td>{{o.last_operator}}</td>
					<td>{{o.deal_time}}</td>
					<?php if(!$is_gm){ ?>
						<td ng-if="o.deal_time == '-'"><button class="btn btn-primary" ng-click="update(o)"><?php echo Lang::get('slave.deal_result'); ?></button></td>
						<td ng-if="o.deal_time != '-'" style="color:red">{{o.result}}</td>
					<?php }else{ ?>
						<td style="color:red">{{o.result}}</td>
					<?php }?>
					<?php if($is_gm){ ?>
						<td ng-if="o.is_done==1"><?php echo Lang::get('slave.order_done'); ?></td>
						<td ng-if="o.is_done==0 && o.deal_time == '-'"><?php echo Lang::get('slave.not_deal'); ?></td>
						<td ng-if="o.is_done==0 && o.deal_time != '-'"><button class="btn btn-primary" ng-click="FinishOrder(o.id)"><?php echo Lang::get('slave.done_order'); ?></button>
						<button class="btn btn-primary" ng-click="ResetOrder(o)"><?php echo Lang::get('slave.reset'); ?></button></td>
					<?php }else{ ?>
						<td ng-if="o.is_done==1"><?php echo Lang::get('slave.order_done'); ?></td>
						<td ng-if="o.is_done==0"><?php echo Lang::get('slave.order_not_done'); ?></td>
					<?php } ?>
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
</div>

<script type="text/ng-template" id="update.html">
        <div class="modal-header">
        </div>
		<form action="" method="post" role="form" ng-submit="UpdateForm()" onsubmit="return false;">
			<div class="modal-body">
				<div class="form-group">
					<label><?php echo Lang::get('slave.order_id')?>:</label>
					<input type="text" class="form-control" readonly ng-model="UpdateData.order_id" ng-init="UpdateData.order_id = record_order_init.order_id"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('slave.order_sn')?>:</label>
					<input type="text" class="form-control" readonly ng-model="UpdateData.order_sn" ng-init="UpdateData.order_sn= record_order_init.order_sn"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('slave.pay_user_id')?>:</label>
					<input type="text" class="form-control" readonly ng-model="UpdateData.pay_user_id" ng-init="UpdateData.pay_user_id= record_order_init.pay_user_id"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('slave.result')?>:</label>
					<input type="text" class="form-control" required ng-model="UpdateData.result" autofocus="autofocus"?>
				</div>
			</div>
	        <div class="modal-footer" style="text-align:center;">
				<button class="btn btn-primary"><?php echo Lang::get('basic.btn_submit')?></button>
	            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
	        </div>
		</form>
</script>

<script type="text/ng-template" id="reset.html">
        <div class="modal-header">
        </div>
		<form action="" method="post" role="form" ng-submit="ResetForm()" onsubmit="return false;">
			<div class="modal-body">
				<div class="form-group">
					<label><?php echo Lang::get('slave.order_id')?>:</label>
					<input type="text" class="form-control" readonly ng-model="ResetData.order_id" ng-init="ResetData.order_id = record_order_init.order_id"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('slave.order_sn')?>:</label>
					<input type="text" class="form-control" readonly ng-model="ResetData.order_sn" ng-init="ResetData.order_sn= record_order_init.order_sn"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('slave.tradeseq')?>:</label>
					<input type="text" class="form-control" ng-model="ResetData.tradeseq" ng-init="ResetData.tradeseq= record_order_init.tradeseq"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('slave.reason')?>:</label>
					<input type="text" class="form-control" required ng-model="ResetData.reason" autofocus="autofocus"?>
				</div>
			</div>
	        <div class="modal-footer" style="text-align:center;">
				<button class="btn btn-primary"><?php echo Lang::get('basic.btn_submit')?></button>
	            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
	        </div>
		</form>
</script>

<script type="text/ng-template" id="check.html">
        <div class="modal-header">
        </div>
		<div class="modal-body">
			<div class="form-group">
			<img ng-src="/img/order_img/{{order.game_id}}_{{order.order_id}}_fail.jpg" style="max-width:100%;">
			</div>
		</div>
        <div class="modal-footer" style="text-align:center;">
			<p style="color:red;text-align:center;"><b><?php echo Lang::get('slave.cilck_to_download_pic') ?></b></p>
            <a class="btn btn-warning" ng-click="cancel()">确认</a>
        </div>
</script>