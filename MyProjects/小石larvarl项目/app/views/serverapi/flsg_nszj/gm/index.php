<script type="text/javascript">
	function modalReplyCtroller1($scope, $modalInstance, gm, $http, alertService) {
		$scope.gm = gm;
		$scope.gmData = {};
			
		$scope.cancel = function () {
			$modalInstance.dismiss('cancel');
		};

		$scope.replyFrom = function(url) {
			gm.reply_message = $scope.gmData.reply_message;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.gmData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				//alertService.add('success', data.result);
				$modalInstance.close(gm);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}

	function loadGMController1($scope, $http, alertService, $modal)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.questions = [];
		$scope.reply_dones= [];
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.questions = data;
				$scope.reply_dones=data.reply_done;
				/*if (!data.GM_Logs) {
					alertService.add('danger', JSON.stringify(data));
				}*/
				//alertService.add('success', data.result);
				if (data.error=="没有要回复玩家的内容") {
					alertService.add('success', data.error);
				};
			}).error(function(data) {
				alertService.add('danger',  data.error);
			});
		};
		/*add function myDate*/
		$scope.myDate = function(timestamp) {
			return timestamp*1000;
		};
		$scope.done = function(gm) {
			alertService.alerts = $scope.alerts;
			var params = {
				'ser_id':gm.ser_id, 
				'server_gm_id' : gm.GMID,
				'player_id' : gm.PlayerID,
				'type' : gm.GMType,
				'reply_message' : ''
			};
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/gm/reply',
				'data'   : $.param(params),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				gm.IsDone = 1;
				// alertService.add('success', data.result);
			}).error(function(data) {
				alertService.add('danger', JSON.stringify(data));
			});
		};

		$scope.reply = function(gm) {
			var modalInstance = $modal.open({
					templateUrl: 'replyModalContent.html',
					controller: modalReplyCtroller1,
					resolve: {
						gm : function () {
							return gm;
						}
					},
					backdrop : false,
					keyboard : false
				});
			modalInstance.result.then(function(gm) {
				var i = 0;
				var len = $scope.questions.length;
				for (i; i < len; i++) {
					if ($scope.questions[i].GMID === gm.GMID) {
						$scope.questions[i].IsDone = 1;
					}
				}
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="loadGMController1">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/gm" method="post" role="form"
				ng-submit="processFrom('/game-server-api/gm')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0" multiple="multiple" ng-multiple="true" size=10>
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>						
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_internal_id ."--".$v->server_name;?></option>
						<?php } ?>		
					</select>
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
	<div class="col-xs-6 margin-top-10">
		<div class="panel panel-danger" ng-if="q.IsDone == 0"
			ng-repeat="q in questions" ng-init="q.server_id = formData.server_id">
			<div class="panel-heading">
				({{q.server_name}})---({{q.Name}})---({{q.PlayerID}})---({{q.SendTime}})
				<a href="/game-server-api/player?server_name={{q.server_name}}&player_id={{q.PlayerID}}" target="{{q.PlayerID}}I_blank"><?php echo Lang::get('slave.player_info'); ?></a>
				<a href="/slave-api/payment/order?server_name={{q.server_name}}&player_id={{q.PlayerID}}" target="{{q.PlayerID}}P_blank"><?php echo Lang::get('slave.channel_order_info'); ?></a>
				<a href="/game-server-api/gm/replied?server_name={{q.server_name}}&player_name={{q.Name}}" target="{{q.Name}}P_blank"><?php echo Lang::get('slave.replied_list'); ?></a>
				<div class="pull-right">{{q.type_name}}</div>
			</div>
			<div class="panel-body">{{q.Message}}</div>
			<div class="panel-footer">
				<button ng-click="reply(q)" class="btn btn-default"><?php echo Lang::get('basic.btn_reply')?></button>
				<button ng-click="done(q)" class="btn btn-default"><?php echo Lang::get('basic.btn_done')?></button>
			</div>
		</div>

		<div class="panel panel-success" ng-if="q.IsDone == 1"
			ng-repeat="q in questions">
			<div class="panel-heading">
				({{q.server_name}})---({{q.Name}})---({{q.PlayerID}})---({{q.SendTime}})
				<a href="/game-server-api/player?server_name={{q.server_name}}&player_id={{q.PlayerID}}" target="{{q.PlayerID}}I_blank"><?php echo Lang::get('slave.player_info'); ?></a>
				<a href="/slave-api/payment/order?server_name={{q.server_name}}&player_id={{q.PlayerID}}" target="{{q.PlayerID}}P_blank"><?php echo Lang::get('slave.channel_order_info'); ?></a>
				<a href="/game-server-api/gm/replied?server_name={{q.server_name}}&player_name={{q.Name}}" target="{{q.Name}}P_blank"><?php echo Lang::get('slave.replied_list'); ?></a>
				<div class="pull-right">{{q.type_name}}</div>
			</div>
			<div class="panel-body">{{q.Message}}</div>
			<div class="panel-footer">{{q.reply_message}}</div>

		</div>
	</div>
</div>

<script type="text/ng-template" id="replyModalContent.html">
        <div class="modal-header">
            <h3>{{gm.Message}}</h3>
        </div>
		<form action="/game-server-api/gm/reply" method="post" role="form" ng-submit="replyFrom('/game-server-api/gm/reply')" onsubmit="return false;">
        <div class="modal-body">
			<textarea ng-model="gmData.reply_message" rows="5" class="form-control" autofocus></textarea>
			<input type="hidden" ng-model="gmData.player_id" ng-init="gmData.player_id = gm.PlayerID"/>
			<input type="hidden" ng-model="gmData.server_gm_id" ng-init="gmData.server_gm_id = gm.GMID" />
			<input type="hidden" ng-model="gmData.type" ng-init="gmData.type = gm.GMType"/>
			<input type="hidden" ng-model="gmData.ser_id" ng-init="gmData.ser_id = gm.ser_id" />
        </div>
        <div class="modal-footer">
			<button class="btn btn-primary"><?php echo Lang::get('basic.btn_reply')?></button>
            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
        </div>
		</form>
		<div class="col-xs-6"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
    </script>