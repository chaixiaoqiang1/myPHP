<script >
	function getSoldStaticsController($scope, $http, alertService, $filter)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');		
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				if (data.error == "没有数据") {
					alertService.add('danger', data.error);
				}else{
					$scope.items = data;
				}
				//alertService.add('success', data.result);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});

		};
	}
</script>
<div class="col-xs-12" ng-controller="getSoldStaticsController">
	<div class="row">
		<div class="eb-content">
			<form method="post" ng-submit="processFrom('/game-server-api/shop/soldStatics')" onsubmit="return false;">
				<div class="well">
					<label><?php echo Lang::get('serverapi.select_game_server') ?></label>
					<select class="form-control" name="server_id" id="select_game_server" 
					ng-model="formData.server_id" ng-init="formData.server_id=-1" multiple="multiple" ng-multiple="true" size=10>
						<option value=-2 style="font-weight:bold"><?php echo Lang::get('serverapi.select_all_server');?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value=<?php echo $v->server_id?>><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div style="width:300px; float:left">
					<lable><b><?php echo Lang::get('timeName.start_time') ?></b></lable>
					<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
				</div>
				<div style="width:300px; float:left">
					<lable><b><?php echo Lang::get('timeName.end_time') ?></b></lable>
					<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
				</div>
				<div class="clearfix">
					<input type="submit" class="btn btn-success" style="width:100px; font-weight:bold;"
						value=" <?php echo Lang::get('basic.btn_search') ?> " />
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
			<table class="table table-striped table-hover" cellpadding="0" cellspacing="0" border="0" >
				<thead>
					<tr class="info">
						<td style="width:20%;text-align:left"><?php echo Lang::get('timeName.server_name');?></td>
						<td style="width:20%;text-align:left"><?php echo Lang::get('timeName.commodity');?></td>
						<td style="width:20%;text-align:left"><?php echo Lang::get('timeName.commodity_number');?></td>
						<td style="width:20%;text-align:left"><?php echo Lang::get('timeName.persons_amount');?></td>
						<td style="width:20%;text-align:left"><?php echo Lang::get('timeName.total_price');?></td>
					</tr>
				</thead>
				<tbody>
		             <tr ng-repeat="t in items">
						<td style="width:20%;text-align:left">{{t[0]}}</td>
						<td style="width:20%;text-align:left">{{t[1]}}</td>
						<td style="width:20%;text-align:left">{{t[2]}}</td>
						<td style="width:20%;text-align:left">{{t[3]}}</td>
						<td style="width:20%;text-align:left">{{t[4]}}</td>
					</tr>	
				</tbody>

			</table>
		</div>
	</div>
</div>