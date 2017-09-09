<script>
	function gameMessage($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.auto = 0;
		$scope.$watch($scope.auto,function() {
			if($scope.auto == 0){
				$scope.auto_process();
				$scope.auto = 1;
			}
		});
		$scope.process = function() {
			$scope.items = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/file/game-message',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data.table;
				alertService.add('success', data.result);
			}).error(function(data) {
				$scope.items = data.table;
				alertService.add('danger', data.error);
			});
		};
		$scope.auto_process = function() {
			$scope.items = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/file/game-message',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data.table;
			}).error(function(data) {
				$scope.items = data.table;
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="gameMessage">
	<div class="row">
		<div class="eb-content">
			<form method="post" ng-submit="process()" onsubmit="return false;">
				<div class="form-group">
					<div class="col-md-10" style="padding-left:0">
						<select class="form-control" name="type" ng-model="formData.type" ng-init="formData.type=1">
							<option value="1"><?php echo Lang::get('serverapi.single_mid')?></option>
							<option value="2"><?php echo Lang::get('serverapi.batch_operation')?></option>
						</select>
					</div>
					<div class="col-md-2">
						<input type="button" class="btn btn-warning" value="<?php echo Lang::get('basic.btn_submit')?>"
						ng-click="process()"/>
					</div>
				</div>
				<div><br/><br/></div>
				<div class="form-group" ng-if="formData.type==1">
					<div class="col-md-3" style="padding-left:0">
						<input type="text" name="mid" ng-model="formData.mid" class="form-control"
						placeholder="<?php echo Lang::get('slave.enter').Lang::get('serverapi.mid')?>"/>
					</div>
					<div class="col-md-3">
						<input type="text" name="desc" ng-model="formData.desc" class="form-control"
						placeholder="<?php echo Lang::get('slave.enter').Lang::get('serverapi.desc')?>"/>
					</div>
					<?php if(in_array($game_code, array('nszj', 'flsg', 'dld', 'poker'))){?>
						<div class="col-md-3">
							<input type="text" name="name" ng-model="formData.name" class="form-control"
							placeholder="<?php echo Lang::get('slave.enter').Lang::get('serverapi.english_name')?>"/>
						</div>
						<div class="col-md-3">
							<input type="text" name="is_filter" ng-model="formData.is_filter" class="form-control"
							placeholder="<?php echo Lang::get('serverapi.is_filter')?>"/>
						</div>
					<?php }?>
				</div>
				<div class="form-group" ng-if="formData.type==2">
					<textarea name="text_data" ng-model="formData.text_data" required class="form-control"
					rows="15" placeholder="<?php if(in_array($game_code, array('nszj', 'flsg', 'dld', 'poker'))){
							echo Lang::get('serverapi.message_tip1');
						}else{
							echo Lang::get('serverapi.message_tip4');
						} ?>">
					</textarea>
				</div>
			</form>
			<div>
				<p><font size="4" color="red"><?php echo Lang::get('serverapi.message_tip2') ?></font></p>
				<p><font size="4" color="red"><?php echo Lang::get('serverapi.message_tip3') ?></font></p>
			</div>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="col-xs-12">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('serverapi.mid')?></b></td>
					<td><b><?php echo Lang::get('serverapi.desc');?></b></td>
					<?php if(in_array($game_code, array('nszj','flsg','dld','poker'))){ ?>
						<td><b><?php echo Lang::get('serverapi.english_name');?></b></td>
						<td><b><?php echo Lang::get('serverapi.is_filter');?></b></td>
					<?php }?>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.id}}</td>
					<td>{{t.desc}}</td>
					<?php if(in_array($game_code, array('nszj','flsg','dld','poker'))){ ?>
						<td>{{t.name}}</td>
						<td>{{t.is_filter}}</td>
					<?php }?>
				</tr>
			</tbody>
		</table>
	</div>
</div>