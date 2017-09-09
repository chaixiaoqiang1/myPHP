<script type="text/javascript">
function updateNoticeController($scope, $http, alertService, $filter)
{
	$scope.alerts = [];
	$scope.formData = {};
	$scope.processFrom = function(url) {
		$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
		alertService.alerts = $scope.alerts;
		$http({
			'method' : 'post',
			'url'	 : url,
			'data'   : $.param($scope.formData),
			'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).success(function(data) {
			var result = data;
			var len = result.length;
			for (var i=0; i < len; i++) {
				if (result[i].status == 'OK') {
					alertService.add('success', result[i].msg);
				} else if (result[i]['status'] == 'error') {
            		alertService.add('danger', result[i].msg);
				}
			}
			//alertService.add('success', data.result);
		}).error(function(data) {
			alertService.add('danger', data.error);
		});
	};
	
}
</script>
<div class="col-xs-12" ng-controller="updateNoticeController">

	
	<div class="row" >
		<div class="eb-content">
			<form action="/game-server-api/level/stop-announce" method="post" role="form" ng-submit="processFrom()" onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id" id="select_game_server" ng-model="formData.server_id" ng-init="formData.server_id=0" multiple="multiple" ng-multiple="true" size=10>
						<optgroup label="<?php echo Lang::get('serverapi.select_game_server') ?>">
						<?php foreach ($server as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
						</optgroup>
					</select>
				</div>		
					
			    <div class="form-group col-md-12" style="margin-left:-15px">
					<input type="text" class="form-control" id="notice_head"  placeholder="<?php echo Lang::get('serverapi.activity_notice_head')?>" required ng-model="formData.notice_head" name="notice_head" /> 
				</div>

				<div class="form-group col-md-12" style="margin-left:-15px">
					<input type="text" class="form-control" id="notice_link"  placeholder="<?php echo Lang::get('serverapi.activity_notice_link')?>" required ng-model="formData.notice_link" name="notice_link" /> 
				</div>

				<div class="form-group col-md-12" style="margin-left:-15px">
					<textarea class="form-control" id="notice"  placeholder="<?php echo Lang::get('serverapi.activity_notice')?>" required ng-model="formData.notice" name="notice" ></textarea>  
				</div>

				<div class="form-group" style="height: 40px;">
					<div class="col-md-4" style="padding: 0">
						<input type='button' class="btn btn-primary"
							value="<?php echo Lang::get('serverapi.update_notice') ?>"
							ng-click="processFrom('/game-server-api/announce/update')" />
					</div>
					
			    </div>
			</form>	 
		</div><!-- /.col -->
	</div>

   <!--开启活动-->
 
   







	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

</div>