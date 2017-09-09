<script>
	function recentAction($scope,$http,alertService){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url){
			alertService.alerts = $scope.alerts;
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
<div id='query' class="col-xs-12" ng-controller="recentAction">
	<div class="row">
		<div class="eb-content">
			<div class="clearfix">
				<br />
			</div>


			<div class="form-group" style="height: 30px; margin-top:10px;">
				<div class="col-md-6" style="padding: 0 ;width:330px">
					<input class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo Lang::get('serverapi.write_player_id');?>" name="player_id" ng-model="formData.player_id">
				</div>

				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-primary"
						value="<?php echo '查询' ?>"
					ng-click="process('/game-server-api/poker/recentAction')" />
				</div>
			</div>

			
			<br>
			<div class="col-xs-12">
				<table class="table table-striped">
					<thead>
						<tr class="info">
							<td>玩家动作</td>
							<td>翻译</td>
							<td>其他</td>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="t in items track by $index">
							<td>{{t.pro}}</td>
							<td>{{t.name}}</td>
							<td>{{t.other}}</td>
						</tr>
					</tbody>
				</table>
			</div>
			

		</div>
	</div>

</div>