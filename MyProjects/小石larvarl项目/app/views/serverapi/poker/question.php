<script type="text/javascript">
	function modalReplyCtroller($scope, $modalInstance, gm, $http, alertService) {
		$scope.gm = gm;
		$scope.gmData = {};
			
		$scope.cancel = function () {
			$modalInstance.dismiss('cancel');
		};

		$scope.replyFrom = function(url) {
			gm.reply_message = $scope.gmData.reply_message;
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/poker/reply?action=2',
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

	function pokerGMController($scope, $http, alertService, $modal)
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
				$scope.questions = data.result;

				//$scope.reply_dones=data.reply_done;
				/*if (!data) {
					alertService.add('danger', '没有问题');
				}*/
				 alertService.add('success', "共计:  "+data.num +"条");
			}).error(function(data) {
				alertService.add('danger', JSON.stringify(data));
			});
		};
		/*add function myDate*/
		$scope.myDate = function(timestamp) {
			return timestamp*1000;
		};
		$scope.done = function(gm) {
			alertService.alerts = $scope.alerts;
			var params = {
				'server_gm_id' : gm.id,
				'player_id' : gm.player_id,
				'type' : gm.type,
				'reply_message' : ''
			};
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/poker/reply?action=1',
				'data'   : $.param(params),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				gm.is_done = 1;
				// alertService.add('success', data.result);
			}).error(function(data) {
				alertService.add('danger', JSON.stringify(data));
			});
		};

		$scope.reply = function(gm) {
			var modalInstance = $modal.open({
					templateUrl: 'replyModalContent.html',
					controller: modalReplyCtroller,
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
					if ($scope.questions[i].id === gm.id) {
						$scope.questions[i].is_done= 1;
					}
				}
			});
		};
	}

</script>

<div class="col-xs-12" ng-controller="pokerGMController">
	
		<form action="/game-server-api/poker/gm" method="post" role="form" ng-submit="processFrom('/game-server-api/poker/gm')" onsubmit="return false;">
				
				<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.get_GM') ?>" />
		</form>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="col-xs-6 margin-top-10">
		<div class="panel panel-danger" ng-if="q.is_done == 0"
			ng-repeat="q in questions" >
			<div class="panel-heading">
				{{q.player_name}}({{q.player_id}}) {{q.created_at}}
				<div class="pull-right">{{q.type}}</div>

			</div>
			<div class="panel-body">{{q.msg}}</div>
			<div class="panel-footer">
				<button ng-click="reply(q)" class="btn btn-default"><?php echo Lang::get('basic.btn_reply')?></button>
				<button ng-click="done(q)" class="btn btn-default"><?php echo Lang::get('basic.btn_done')?></button>
			</div>
		</div>

		<div class="panel panel-success" ng-if="q.IsDone == 1"
			ng-repeat="q in questions">
			<div class="panel-heading">
				{{q.player_name}}({{q.player_id}}) {{q.created_at}}
				<div class="pull-right">{{q.type}}</div>
			</div>
			<div class="panel-body">{{q.msg}}</div>
			<div class="panel-footer">{{q.reply_message}}</div>

		</div>
	</div>
</div>

<script type="text/ng-template" id="replyModalContent.html">
        <div class="modal-header">
            <h3>{{gm.msg}}</h3>
        </div>
		<form action="/game-server-api/poker/reply" method="post" role="form" ng-submit="replyFrom('/game-server-api/poker/reply')" onsubmit="return false;">
        <div class="modal-body">
			<textarea ng-model="gmData.reply_message" rows="5" class="form-control" autofocus name ="reply_message" ></textarea>
			<input type="hidden" ng-model="gmData.player_id" ng-init="gmData.player_id = gm.player_id"/>
			<input type="hidden" ng-model="gmData.server_gm_id" ng-init="gmData.server_gm_id = gm.id" name = "qid"/>
			<input type="hidden" ng-model="gmData.type" ng-init="gmData.type = gm.type"/>
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