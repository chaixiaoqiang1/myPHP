<script>
	function playCount($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time=null;
		$scope.end_time=null;
		$scope.formData = {};
		$scope.process = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
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
<div id='query' class="col-xs-12" ng-controller="playCount">
	<div class="row">
		<div class="eb-content">
			<div class="form-group">
				<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<quick-datepicker ng-model="start_time" init-value="00:10:00"></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>
				<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>

				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-primary"
					value="<?php echo '查询' ?>"
					ng-click="process('/game-server-api/poker/playcount')" />
				</div>

			</div>		
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="row margin-top-10 ">
		<div class="eb-content"> 
			<table class="table table-striped"  border="1">
			<thead>
				<tr class="info">
					<td><?php echo "日期";?></td>
					<td colspan="4"><?php echo "1";?></td>
					<td colspan="4"><?php echo "2";?></td>
					<td colspan="4"><?php echo "5";?></td>
					<td colspan="4"><?php echo "10";?></td>
					<td colspan="4"><?php echo "20";?></td>
					<td colspan="4"><?php echo "50";?></td>
					<td colspan="4"><?php echo "100";?></td>
					<td colspan="4"><?php echo "200";?></td>
					<td colspan="4"><?php echo "500";?></td>
					<td colspan="4"><?php echo "1000";?></td>
					<td colspan="4"><?php echo "2000";?></td>
					<td colspan="4"><?php echo "2500";?></td>
					<td colspan="4"><?php echo "5000";?></td>
					<td colspan="4"><?php echo "10000";?></td>
					<td colspan="4"><?php echo "20000";?></td>
					<td colspan="4"><?php echo "25000";?></td>
					<td colspan="4"><?php echo "50000";?></td>
					<td colspan="4"><?php echo "100000";?></td>
					<td colspan="4"><?php echo "200000";?></td>
					<td colspan="4"><?php echo "500000";?></td>
				</tr>
				<tr>
					<td><?php echo " ";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
					<td ><?php echo "台费";?></td>
					<td>POT</td>
					<td ><?php echo "牌局数";?></td>
					<td ><?php echo "玩家数";?></td>
				</tr>
			</thead>
			
			<tbody>
				<tr ng-repeat="i in items">
				<td><nobr>{{i.date}}</nobr></td>
				<td><nobr>{{i.blind1.table_fee}}</nobr></td>
				<td><nobr>{{i.blind1.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind1.game_num}}</nobr></td>
				<td><nobr>{{i.blind1.player_num}}</nobr></td>
				<td><nobr>{{i.blind2.table_fee}}</nobr></td>
				<td><nobr>{{i.blind2.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind2.game_num}}</nobr></td>
				<td><nobr>{{i.blind2.player_num}}</nobr></td>
				<td><nobr>{{i.blind5.table_fee}}</nobr></td>
				<td><nobr>{{i.blind5.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind5.game_num}}</nobr></td>
				<td><nobr>{{i.blind5.player_num}}</nobr></td>
				<td><nobr>{{i.blind10.table_fee}}</nobr></td>
				<td><nobr>{{i.blind10.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind10.game_num}}</nobr></td>
				<td><nobr>{{i.blind10.player_num}}</nobr></td>
				<td><nobr>{{i.blind20.table_fee}}</nobr></td>
				<td><nobr>{{i.blind20.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind20.game_num}}</nobr></td>
				<td><nobr>{{i.blind20.player_num}}</nobr></td>
				<td><nobr>{{i.blind50.table_fee}}</nobr></td>
				<td><nobr>{{i.blind50.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind50.game_num}}</nobr></td>
				<td><nobr>{{i.blind50.player_num}}</nobr></td>
				<td><nobr>{{i.blind100.table_fee}}</nobr></td>
				<td><nobr>{{i.blind100.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind100.game_num}}</nobr></td>
				<td><nobr>{{i.blind100.player_num}}</nobr></td>
				<td><nobr>{{i.blind200.table_fee}}</nobr></td>
				<td><nobr>{{i.blind200.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind200.game_num}}</nobr></td>
				<td><nobr>{{i.blind200.player_num}}</nobr></td>
				<td><nobr>{{i.blind500.table_fee}}</nobr></td>
				<td><nobr>{{i.blind500.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind500.game_num}}</nobr></td>
				<td><nobr>{{i.blind500.player_num}}</nobr></td>
				<td><nobr>{{i.blind1000.table_fee}}</nobr></td>
				<td><nobr>{{i.blind1000.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind1000.game_num}}</nobr></td>
				<td><nobr>{{i.blind1000.player_num}}</nobr></td>
				<td><nobr>{{i.blind2000.table_fee}}</nobr></td>
				<td><nobr>{{i.blind2000.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind2000.game_num}}</nobr></td>
				<td><nobr>{{i.blind2000.player_num}}</nobr></td>
				<td><nobr>{{i.blind2500.table_fee}}</nobr></td>
				<td><nobr>{{i.blind2500.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind2500.game_num}}</nobr></td>
				<td><nobr>{{i.blind2500.player_num}}</nobr></td>
				<td><nobr>{{i.blind5000.table_fee}}</nobr></td>
				<td><nobr>{{i.blind5000.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind5000.game_num}}</nobr></td>
				<td><nobr>{{i.blind5000.player_num}}</nobr></td>
				<td><nobr>{{i.blind10000.table_fee}}</nobr></td>
				<td><nobr>{{i.blind10000.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind10000.game_num}}</nobr></td>
				<td><nobr>{{i.blind10000.player_num}}</nobr></td>
				<td><nobr>{{i.blind20000.table_fee}}</nobr></td>
				<td><nobr>{{i.blind20000.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind20000.game_num}}</nobr></td>
				<td><nobr>{{i.blind20000.player_num}}</nobr></td>
				<td><nobr>{{i.blind25000.table_fee}}</nobr></td>
				<td><nobr>{{i.blind25000.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind25000.game_num}}</nobr></td>
				<td><nobr>{{i.blind25000.player_num}}</nobr></td>
				<td><nobr>{{i.blind50000.table_fee}}</nobr></td>
				<td><nobr>{{i.blind50000.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind50000.game_num}}</nobr></td>
				<td><nobr>{{i.blind50000.player_num}}</nobr></td>
				<td><nobr>{{i.blind100000.table_fee}}</nobr></td>
				<td><nobr>{{i.blind100000.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind100000.game_num}}</nobr></td>
				<td><nobr>{{i.blind100000.player_num}}</nobr></td>
				<td><nobr>{{i.blind200000.table_fee}}</nobr></td>
				<td><nobr>{{i.blind200000.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind200000.game_num}}</nobr></td>
				<td><nobr>{{i.blind200000.player_num}}</nobr></td>
				<td><nobr>{{i.blind500000.table_fee}}</nobr></td>
				<td><nobr>{{i.blind500000.pot_fee}}</nobr></td>
				<td><nobr>{{i.blind500000.game_num}}</nobr></td>
				<td><nobr>{{i.blind500000.player_num}}</nobr></td>
				</tr>
			</tbody>
			</table>	
		</div>
	</div>

</div>