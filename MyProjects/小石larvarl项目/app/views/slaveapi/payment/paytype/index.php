<script> 

function getPayTypeStatController($scope, $http, alertService, $modal, $filter) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.orders = {};
	$scope.start_time = null;
	$scope.end_time = null;
    $scope.processFrom = function() {
        alertService.alerts = $scope.alerts;
		$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
		var form_url = '<?php echo Request::url(); ?>';
        $http({
            'method': 'post',
            'url': form_url,
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
        	$scope.orders = data.body;
        	//
   //      	var canvas = document.getElementById('chart-area');
   //      	canvas.innerHTML = '';
   //          var ctx = canvas.getContext('2d');
   //          ctx.clearRect(0,0,canvas.width,canvas.height);
   //          var body = data.body;
   //          var pieData  = new Array(body.length);
   //          var colorArray = ["#F7464A", "#46BFBD", "#FDB45C", "#949FB1", "#4D5360"];
   //          var highlightArray = ["#FF5A5E", "#5AD3D1", "#FFC870", "#A8B3C5", "#616774"];
   //          for(var i = 0; i < body.length; i++){
   //              var randomNum = Math.floor(Math.random()*16777215);
   //              var anotherRandomNum = randomNum + 1315860;
   //              var a = '#'+randomNum.toString(16);
   //              var b = '#'+anotherRandomNum.toString(16);
   //          	pieData[i] = {
   //          			value: body[i].amount_rate,
   //  					color: a,
   //  					highlight: b,
   //  					label: body[i].pay_type_name + " : " + body[i].pay_method_name + " : " + body[i].money_flow_name,
   //              }
   //          }
			// window.myPie = new Chart(ctx).Pie(pieData);
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
} 
</script>
<div class="col-xs-12" ng-controller="getPayTypeStatController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/payment/pay-type" method="get" role="form"
				ng-submit="processFrom()" onsubmit="return false;">
				<div class="form-group col-md-4" style="padding: 0;">
					<select class="form-control" name="pay_type_id"
						ng-model="formData.pay_type_id" ng-init="formData.pay_type_id=0">
						<option value="0"><?php echo Lang::get('slave.all_pay_types');?></a>
					<?php foreach(PayType::currentPlatform()->get() as $k => $v) { ?>
						<option value="<?php echo $v->pay_type_id?>"><?php echo $v->pay_type_name ?></option>
					<?php } ?>
					</select>
				</div>
				<div class="clearfix"></div>
				<div class="form-group" style="height: 30px;">
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
				<input type="submit" class="btn btn-default"
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
	<div class="row margin-top-10">
		<div class="col-xs-12">
			<table class="table table-striped">
				<thead>
					<tr class="info">
						<td><b><?php echo Lang::get("slave.start_time");?></b></td>
						<td><b><?php echo Lang::get("slave.end_time");?></b></td>
						<td><b><?php echo Lang::get("slave.pay_type");?></b></td>
						<td><b><?php echo Lang::get("slave.pay_method");?></b></td>
						<td><b><?php echo Lang::get("slave.money_flow_name");?></b></td>
						<?php if (Request::is('slave-api/payment/pay-type')) { ?>
						<td><b><?php echo Lang::get("slave.pay_amount");?></b></td>
						<td><b><?php echo Lang::get("slave.pay_amount_dollar");?></b></td>
						<td><b><?php echo Lang::get("slave.pay_type_method_rate");?></b></td>
						<?php } ?>
						<td><b><?php echo Lang::get("slave.get_payment_count");?></b></td>
						<td><b><?php echo Lang::get("slave.all_order_count");?></b></td>
						<td><b><?php echo Lang::get("slave.get_payment_rate");?></b></td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="o in orders">
						<td>{{o.pay_time_first}}</td>
						<td>{{o.pay_time_last}}</td>
						<td>{{o.pay_type_name}}</td>
						<td>{{o.pay_method_name}}</td>
						<td>{{o.money_flow_name}}</td>
						<?php if (Request::is('slave-api/payment/pay-type')) { ?>
							<?php if(in_array(Session::get('game_id'), array(69,72))){ ?>
								<td ng-if="'Google Play'==o.pay_type_name">{{o.total_amount*0.9675| number:2}}</td>
								<td ng-if="'Google Play'!=o.pay_type_name">{{o.total_amount| number:2}}</td>
								<td ng-if="'Google Play'==o.pay_type_name">{{o.total_dollar_amount*0.9675| number:2}}</td>
								<td ng-if="'Google Play'!=o.pay_type_name">{{o.total_dollar_amount| number:2}}</td>
								<td ng-if="'Google Play'==o.pay_type_name">{{o.amount_rate*0.9675}}%({{o.amount_rate}}%)</td>
								<td ng-if="'Google Play'!=o.pay_type_name">{{o.amount_rate}}%</td>
							<?php }else{ ?>
								<td>{{o.total_amount| number:2}}</td>
								<td>{{o.total_dollar_amount| number:2}}</td>
								<td>{{o.amount_rate}}%</td>
							<?php } ?>
						<?php } ?>
						<td>{{o.get_payment_count}}</td>
						<td>{{o.count}}</td>
						<td>{{o.get_payment_rate}}%</td>
					</tr>
					</body>
			
			</table>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="col-xs-12">
			<div id="canvas-holder">
				<canvas id="chart-area" width="500" height="500" />
			</div>
		</div>
	</div>
</div>