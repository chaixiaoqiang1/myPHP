<script>
	function speakAuthority($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url,banORnot) {
			alertService.alerts = $scope.alerts;
			$scope.formData.is_ban_speak = banORnot;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data;
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};
	
	}
</script>
<div id='query' class="col-xs-12" ng-controller="speakAuthority">
	<div class="row">
		<div class="eb-content">
			<div class="clearfix">
				<br />
			</div>


			<div class="form-group" style="height: 30px; margin-top:10px;">
				<div class="col-md-6" style="padding: 0 ;width:330px">
					<input class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo Lang::get('serverapi.write_player_id');?>" name="player_id" ng-model="formData.player_id">
				</div>
			</div>

			<div class="col-md-4" style="padding: 0">
				<input type='button' class="btn btn-primary"
					value="<?php echo '禁言' ?>"
					ng-click="process('/game-server-api/poker/speakAuthority',1)" />
			</div>

			<div class="col-md-4" style="padding: 0">
				<input type='button' class="btn btn-primary"
					value="<?php echo '解除禁言' ?>"
					ng-click="process('/game-server-api/poker/speakAuthority',0)" />
			</div>

			<div class="col-xs-12">
				<table class="table table-striped">	
						<tr  ng-if = "items.is_ok == true">
							<td>操作成功</td>		
						</tr>		
				</table>
			</div>
			

		</div>
	</div>

</div>