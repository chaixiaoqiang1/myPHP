<script>
	function crossServerPkController($scope, $http, alertService, $filter){
		$scope.alerts = [];
		$scope.start_time = null;
		$scope.formData = {};
		$scope.process = function(url){
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url' : url,
				'data' : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				var result = data.result;
				var len = result.length;
				for (var i=0; i<len; i++){
					if (result[i].status == 'ok') {
						alertService.add('success', result[i].msg);
					}else if(result[i]['status'] == 'error'){
						alertService.add('danger', result[i].msg);
					}
				}
			}).error(function(data){
				alertService.add('danger', data.error);
			});
		}
		$scope.look = function(url){
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url' : url,
				'data' : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				var result = data.result;
				var len = result.length;
				for (var i = 0; i < len; i++) {
					if (result[i].status == 'ok') {
						alertService.add('success', result[i].msg);
					}else if (result[i].status == 'error') {
						alertService.add('danger', result[i].msg);
					}
				};
			}).error(function(data){
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="crossServerPkController" id="<?php  echo $game_code;?>">
	<div class="row">
		<div class="col-xs-9">
			<div class="form-group" style="height:40px">
				<div class="form-group col-md-4">
					<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
					<i class="glyphicon glyphicon-calendar"></i>
				</div>
				<div class="form-group col-md-6">
					<p><font color=red size=4>女神全服战开启后可在<a href="/game-server-api/all/server/fight" target="_blank">全服战设置</a>中对活动进行设置</font></p>
				</div>  
			</div>
            <div class="form-group" style="height:40px">
                <select class="form-control" name="match_type" ng-model="formData.match_type" ng-init="formData.match_type=0">
                    <option value="0">选择比赛类型(等级)</option>
                    <?php if($game_code=='nszj'){?>
                        <option value = 8>全服戰</option>
                    <?php }elseif($game_code=='flsg'){ ?>
                        <option value = 13>110-119</option>
                        <option value = 14>120-129</option>
                        <option value = 15>130-139</option>
                        <option value = 16>140-159</option>
                        <option value = 17>160-179</option>
                        <option value = 18>180-199</option>
                    <?php } ?>
                </select>
            </div>
			<div class="form-group">
				<select class="form-control" name="server_id" ng-model="formData.server_id" ng-init="formData.server_id=0" multiple="multiple" ng-multiple="true" size="10">
					<option value="0"><?php echo Lang::get('serverapi.select_game_server')?></option>
					<?php foreach ($servers as $key => $value) { ?>
						<option value="<?php echo $value->server_id?>"><?php echo $value->server_name?></option>
					<?php }?>
				</select>
			</div>
			<div class="form-group">
				<select class="form-control" name="server_id2" ng-model="formData.server_id2" ng-init="formData.server_id2=0">
					<option value="0">请选择主服务器</option>
					<?php foreach ($servers as $key => $value) { ?>
						<option value="<?php echo $value->server_id?>"><?php echo $value->server_name?></option>
					<?php }?>
				</select>
			</div>
			<div class="form-group" style="height:40px">
				<input type="button" class="btn btn-primary" value="<?php echo Lang::get('serverapi.tournament_world_open')?>"
				ng-click="process('/game-server-api/cross/world-open')">

				<input type="button" class="btn btn-primary" value="<?php echo Lang::get('serverapi.tournament_world_connect')?>"
				ng-click="process('/game-server-api/cross/world-update')">
				
				<input type='button' class="btn btn-primary" value="<?php echo Lang::get('serverapi.tournament_look') ?>"
				ng-click="look('/game-server-api/cross/world-look')" style="margin-left:5px"/>
				<?php if('nszj' == $game_code){?>
					<input type='button' class="btn btn-primary" value="比赛报名"
					ng-click="look('/game-server-api/cross/allserver-signup')" style="margin-left:5px"/>

					<input type='button' class="btn btn-primary" value="报名查看"
					ng-click="look('/game-server-api/cross/allserver-lookup')" style="margin-left:5px"/>
				<?php }?>
				<input type='button' class="btn btn-warning" value="重置所有比赛服连接"
				ng-click="look('/game-server-api/cross/all-update')" style="margin-left:5px"/>
			</div>
			
			<div class="alert alert-danger">
				<b><?php echo Lang::get('serverapi.tournament_attention')?></b>
			</div>
			<div class="form-group" style="height:30px">
				<input type="text" class="form-control" ng-model="formData.id" name="id" placeholder="<?php echo Lang::get('serverapi.enter_id')?>">
			</div>
			<div class="form-group" style="height:30px">
				<input type="text" class="form-control" ng-model="formData.password" name="password" placeholder="<?php echo Lang::get('serverapi.enter_password')?>">
			</div>
			<div class="col-md-4" style="padding:0">
				<input type="button" class="btn btn-danger" value="<?php echo Lang::get('serverapi.close') ?>"
				ng-click="process('/game-server-api/cross/world-close')" />
			</div>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>