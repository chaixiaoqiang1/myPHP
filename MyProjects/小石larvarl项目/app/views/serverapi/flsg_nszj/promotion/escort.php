<script>
	function playerEscotController($scope, $http, alertService){
		$scope.formData = {};
		$scope.alerts = [];
		$scope.process = function(){
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'    : '/game-server-api/player/escort',
				'data'   : $.param($scope.formData), 
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function (data) {
                var result = data.result;
                var len = result.length;
                for (var i = 0; i < len; i++) {
                    if (result[i].status == 'ok') {
                        alertService.add('success', result[i].msg);
                    } else if (result[i]['status'] == 'error') {
                        alertService.add('danger', result[i].msg);
                    }
                }
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
		};
	} 
</script>
<div class="col-xs-12" ng-controller = "playerEscotController">
	<div class = "row">
		<div class = "col-xs-6">
			<form action = "/game-server-api/player/escort" method = "post" role = "form" ng-submit = "process()" onsubmit = "return false;">
				<div class = "form-group">
					<div class="form-group" style="width:50%;">
					<select class="form-control" name="activity_type"
						id="select_activity_type" ng-model="formData.activity_type"
						ng-init="formData.activity_type=0">
						<option value="0"><?php echo Lang::get('slave.escort_ship') ?></option>
						<?php if('flsg' == $game_code){ ?>
							<option value="1"><?php echo Lang::get('slave.escort_che') ?></option>	
						<?php } elseif ('nszj' == $game_code) { ?>
							<option value="1">圣域护送</option>
						<?php }?>
					</select>
				</div>
					<label>
						<input type = "radio" ng-model = "formData.action_type" name = "action_type" ng-init = "formData.action_type=1" value="1"/>
						<?php echo Lang::get('serverapi.escort_for_name')?>
					</label>

					<label>
						<input type = "radio" ng-model = "formData.action_type" name = "action_type"  value="2"/>
						<?php echo Lang::get('serverapi.escort_for_id')?>
					</label>
					<span style = "color:red; font-size:16px;">(建议使用player_id操作)</span>
				</div>
				<div class="form-group">
					<textarea name="content" ng-model="formData.content"
						placeholder="<?php echo Lang::get('serverapi.all_server_tips') ?>"
						rows="15" required class="form-control"></textarea>
				</div>

				<div class="form-group" style="height: 30px;">
				<br/>
				<span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.escort_introduce')?></span>
			</div>
			<br>
				<input type="submit" class="btn btn-danger" value="<?php echo Lang::get('basic.btn_submit') ?>"/>
			</form>
		</div>
	</div>
	<div class = "row marfin-top-10">
		<div class = "col-xs-6">
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>			
		</div>
	</div>
</div>