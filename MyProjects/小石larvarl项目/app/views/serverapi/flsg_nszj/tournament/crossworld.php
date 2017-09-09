<script>
	function crossWorldLordsController($scope, $http, alertService, $filter){
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
		$scope.lookup = function(url){
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url' : url,
				'data': $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				var result = data;
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
		};
		$scope.look = function(url){
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time= $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
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
				alertService.add('error', data.error);
			});
		}

		$scope.process_reset = function(url){
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time= $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
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
				alertService.add('error', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="crossWorldLordsController" id="<?php  echo $game_code;?>">
	<div class="row">
		<div class="col-xs-9">
			<div class="form-group" style="height:40px">
				<div class="input-group">
					<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
					<i class="glyphicon glyphicon-calendar"></i>
				</div>
			</div>
            <div class="form-group" style="height:40px">
                <select class="form-control" name="match_type" ng-model="formData.match_type" ng-init="formData.match_type=0">
                    <option value="0"><?php echo Lang::get('serverapi.select_match_type')?></option>
                    <?php if($game_code=='flsg'){?>
                        <option value = 6><?php echo Lang::get('serverapi.tianxiadiyi')?></option>
                        <option value = 8><?php echo Lang::get('serverapi.sanguojingrui')?></option>
                        <option value = 9><?php echo Lang::get('serverapi.sanguopojun')?></option>
                        <option value = 10><?php echo Lang::get('serverapi.sanguoshenwei')?></option>
                        <option value = 11><?php echo Lang::get('serverapi.sanguotianke')?></option>
                        <option value = 12><?php echo Lang::get('serverapi.sanguotianyuan')?></option>
                        <option value = 19><?php echo Lang::get('serverapi.kings_of_kings')?></option>
                        <?php }elseif($game_code=='nszj'){ ?>
                        <option value = 6><?php echo Lang::get('serverapi.firghtofkings')?></option>
                        <option value="9">极地战神组</option>
						<option value="10">破空战神组</option>
						<option value="11">炼狱战神组</option>
						<option value="12">梵天战神组</option>
						<option value="13">寰宇战神组</option>
						<option value="14">全民PK赛终极战</option>
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
					<option value="0"><?php echo Lang::get('serverapi.select_game_server')?></option>
					<?php foreach ($servers as $key => $value) { ?>
						<option value="<?php echo $value->server_id?>"><?php echo $value->server_name?></option>
					<?php }?>
				</select>
			</div>
			<?php if($game_code=='nszj'){?>
			<div ng-if="formData.match_type==6">
				<div class="form-group">
						<textarea name="gift_data" ng-model="formData.gift_data"
							placeholder="<?php echo Lang::get('serverapi.server_tip') ?>"
							rows="15" required class="form-control"></textarea>
				</div>
			</div>
			<?php }else{ ?>
				<div class="form-group">
						<textarea name="gift_data" ng-model="formData.gift_data"
							placeholder="<?php echo Lang::get('serverapi.server_tip') ?>"
							rows="15" required class="form-control"></textarea>
				</div>
			<?php }?>
			<div class="form-group" style="height:40px">
				<input type="button" class="btn btn-primary" value="<?php echo Lang::get('serverapi.tournament_world_open')?>"
				ng-click="process('/game-server-api/cross/world-open')">
				<input type="button" class="btn btn-primary" value="<?php echo Lang::get('serverapi.tournament_world_connect')?>"
				ng-click="process('/game-server-api/cross/world-update')">
				<input type="button" class="btn btn-primary" value="<?php echo Lang::get('serverapi.tournament_world_signup')?>"
				ng-click="process('/game-server-api/cross/world-signup')">
				<input type='button' class="btn btn-primary" value="<?php echo Lang::get('serverapi.tournament_lookup') ?>"
				ng-click="lookup('/game-server-api/cross/world-lookup')"/>
				
				<input type='button' class="btn btn-primary" value="<?php echo Lang::get('serverapi.tournament_look') ?>"
				ng-click="look('/game-server-api/cross/world-look')" />
				<?php if($game_code=='nszj'){?>
					<div ng-if="formData.match_type!=6"  class="btn" style="box-shadow:inset 0px 0px 0px 0px rgba(0, 0, 0, 0);padding-left: 0px;">
						<input type='button' class="btn btn-warning" value="<?php echo Lang::get('serverapi.reset_all_server') ?>"
					ng-click="process_reset('/game-server-api/cross/world_reset')"/>
					</div>
				<?php }else{ ?>
					
				<?php }?>
			</div>
			
			<div class="alert alert-danger">
				<b><?php echo Lang::get('serverapi.tournament_attention')?></b>
			</div>
			<div class="form-group" style="height:30px">
				<input type="text" class="form-control" ng-model="formData.id" name="id" placeholder="<?php echo Lang::get('serverapi.world_enter_id')?>">
			</div>
			<div class="form-group" style="height:30px">
				<input type="text" class="form-control" ng-model="formData.password" name="password" placeholder="<?php echo Lang::get('serverapi.world_enter_password')?>">
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