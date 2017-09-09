<script>
	function OperationController($scope, $http, alertService, $filter){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.is_show = 0;
		$scope.process = function(){
			$scope.items = {};
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url' 	 : '/slave-api/operation/log',
				'data' 	 : $.param($scope.formData),
				'headers':{'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				$scope.is_show = 1;
				$scope.items = data;
			}).error(function(data){
				alertService.add('danger',data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="OperationController">
	<div class="row">
		<div class="eb-content">
			<div class="form-group">
                <select class="form-control" name="server_id"
                        id="select_game_server" ng-model="formData.server_id"
                        ng-init="formData.server_id=0" multiple="multiple"
                        ng-multiple="true" size=10>
                    <optgroup
                        label="<?php echo Lang::get('serverapi.select_game_server') ?>(按住Ctrl可多选)">
                        <?php foreach ($servers as $k => $v) { ?>
                            <option value="<?php echo $v->server_id ?>"><?php echo $v->server_name; ?></option>
                        <?php } ?>
                    </optgroup>
                </select>
            </div>
			<div class="col-md-8" style="padding-left:0">
				<input type="text" name="operation_id" ng-model="formData.operation_id" class="form-control"
				placeholder="<?php echo Lang::get('slave.enter')?>operationId"/>
			</div>
			<div class="col-md-4">
				<input type="button" class="btn btn-primary" value="<?php echo Lang::get('basic.btn_show')?>"
				ng-click="process()"/>
			</div>
		</div>
	</div>

	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type" 
				close="alert.close()">
				{{alert.msg}}
			</alert>
		</div>
	</div>

	<div class="col-xs-12">
		<table class="table table-striped" ng-if="is_show==1">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.server');?></b></td>
					<td><b><?php echo Lang::get('slave.people_num');?></b></td>
					<td><b><?php echo Lang::get('slave.avg_lev');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.server_name}}</td>
					<td>{{t.num}}</td>
					<td>{{t.lev}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>