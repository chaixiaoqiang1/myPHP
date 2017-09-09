<script type="text/javascript">
function mnsgAnnouceController($scope, $http, alertService)
{
	$scope.alerts = [];
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
<div class="col-xs-12" ng-controller="mnsgAnnouceController">
	<div class="row" >
		<div class="eb-content">
			<form action="/game-server-api/mnsg/announce" method="post" role="form" ng-submit="processFrom('/game-server-api/mnsg/announce')" onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id" id="select_game_server" required ng-model="formData.server_id" multiple="multiple" ng-multiple="true" size=10>
						<optgroup label="<?php echo Lang::get('serverapi.select_game_server') ?>">
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name.'----'.$v->game_id.'--'.$v->server_id.'--'.$v->server_internal_id;?></option>
						<?php } ?>		
						</optgroup>
					</select>
				</div>			
				<div class="form-group">
					<textarea type="text" class="form-control" id="form_content" placeholder="<?php echo Lang::get('serverapi.enter_mnsg_announce_content') ?>" required ng-model="formData.content" name="content" rows="5"></textarea> 
				</div>				
			
				<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>"/>	
			</form>	 
		</div><!-- /.col -->
	</div>
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

</div>