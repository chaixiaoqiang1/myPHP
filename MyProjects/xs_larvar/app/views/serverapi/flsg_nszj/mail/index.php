<script>
	function sendMailController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.start_time=null;
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				var result = data.result;
				var len = result.length;
				for (var i=0; i < len; i++) {
					if (result[i].status == 'ok') {
						alertService.add('success', result[i].msg);
					} else if (result[i]['status'] == 'error') {
	            		alertService.add('danger', result[i].msg);
					}
				}
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};
	}
</script>
<div class="col-xs-12" ng-controller="sendMailController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/mail" method="post" role="form"
				ng-submit="processFrom('/game-server-api/mail')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id" required
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0" multiple="multiple"
						ng-multiple="true" size=20>
						<optgroup
							label="<?php echo Lang::get('serverapi.select_game_server') ?>">
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
						</optgroup>
					</select>
				</div>
				<div class="form-group col-md-8">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('serverapi.enter_player_id')?>"required
						ng-model="formData.player_id" name="player_id"?>
						<p style="color:#aaa"><?php echo Lang::get('serverapi.send_mail_tip') ?></p>
				</div>
				<?php if(59 == $game_id){ ?>
				 <div class="form-group col-md-4">
					<select class="form-control" name="area_id"
						id="area_id" ng-model="formData.area_id"
						ng-init="formData.area_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_area') ?></option>
						<option value="59"><?php echo Lang::get('serverapi.tw_area')?></option>
						<option value="65"><?php echo Lang::get('serverapi.hk_area')?></option>
					</select>
				</div>
				<?php } ?>
				<?php if(63 == $game_id){ ?>
				 <div class="form-group col-md-4">
					<select class="form-control" name="area_id"
						id="area_id" ng-model="formData.area_id"
						ng-init="formData.area_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_area') ?></option>
						<option value="63"><?php echo Lang::get('serverapi.uk_area')?></option>
						<option value="64"><?php echo Lang::get('serverapi.sg_area')?></option>
					</select>
				</div>
				<?php } ?>
				<div class="clearfix"></div>
				<div class="form-group">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('serverapi.enter_mail_title')?>" required
						ng-model="formData.mail_title" name="mail_title"?>
				</div>
				<div class="form-group">
					<textarea type="text" class="form-control" id="mail_body"
						placeholder="<?php echo Lang::get('serverapi.enter_mail_body') ?>"
						required ng-model="formData.mail_body" name="mail_body" rows="8"></textarea>
				</div>

				<input type="submit" class="btn btn-default"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
		<!-- /.col -->
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

</div>