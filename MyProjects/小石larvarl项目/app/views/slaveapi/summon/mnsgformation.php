<script>
	function formationController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.is_show = 0;
		$scope.process = function(url) {
			$scope.items = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.is_show = 1;
				$scope.items = data;
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};
	}
</script>
<div class="col-xs-12" ng-controller="formationController">
	<div class="row">
		<div class="col-xs-10">
			<div class="form-group">
			    <div class="col-md-6">
			        <div class="input-group">
			            <quick-datepicker ng-model="start_time" init-value="00:10:00"></quick-datepicker>
			            <i class="glyphicon glyphicon-calendar"></i>
			        </div>
			    </div>
			    <div class="col-md-6">
			        <div class="input-group">
			            <quick-datepicker ng-model="end_time" init-value="23:50:59"></quick-datepicker>
			            <i class="glyphicon glyphicon-calendar"></i>
			        </div>
			    </div>
			</div></br>
			<div class="form-group">
				<div class="col-md-10">
					<select class="form-control" name="search_type" ng-model="formData.search_type"
						ng-init="formData.search_type=1" >
							<option value="1">查询阵容的登场率</option>
							<option value="2">查询阵容的胜率</option>
							<option value="3">查询英雄的登场率</option>
							<option value="4">查询英雄的胜率</option>
							<option value="5">查询英雄的登场率和胜率</option>
					</select>
				</div>
			</div>
			<div class="clearfix"></br></div>
			<div class="form-group" ng-if="formData.search_type == 1 || formData.search_type == 2">
				<div class="col-md-2">
					<select class="form-control" name="formation1" ng-model="formData.formation1"
						ng-init="formData.formation1=0">
							<option value="0">阵容第1个英雄</option>
							<?php foreach ($heros as $k => $v) { ?>
								<option value="<?php echo $k?>"><?php echo $k.':'.$v;?></option>
							<?php } ?>		
					</select>
				</div>
				<div class="col-md-2">
					<select class="form-control" name="formation2" ng-model="formData.formation2"
						ng-init="formData.formation2=0">
							<option value="0">阵容第2个英雄</option>
							<?php foreach ($heros as $k => $v) { ?>
								<option value="<?php echo $k?>"><?php echo $k.':'.$v;?></option>
							<?php } ?>		
					</select>
				</div>
				<div class="col-md-2">
					<select class="form-control" name="formation3" ng-model="formData.formation3"
						ng-init="formData.formation3=0">
							<option value="0">阵容第3个英雄</option>
							<?php foreach ($heros as $k => $v) { ?>
								<option value="<?php echo $k?>"><?php echo $k.':'.$v;?></option>
							<?php } ?>		
					</select>
				</div>
				<div class="col-md-2">
					<select class="form-control" name="formation4" ng-model="formData.formation4"
						ng-init="formData.formation4=0">
							<option value="0">阵容第4个英雄</option>
							<?php foreach ($heros as $k => $v) { ?>
								<option value="<?php echo $k?>"><?php echo $k.':'.$v;?></option>
							<?php } ?>		
					</select>
				</div>
				<div class="col-md-2">
					<select class="form-control" name="formation5" ng-model="formData.formation5"
						ng-init="formData.formation5=0">
							<option value="0">阵容第5个英雄</option>
							<?php foreach ($heros as $k => $v) { ?>
								<option value="<?php echo $k?>"><?php echo $k.':'.$v;?></option>
							<?php } ?>		
					</select>
				</div>
			</div>
			<div class="form-group" ng-if="formData.search_type == 3 || formData.search_type == 4 || formData.search_type == 5">
				<div class="col-md-10">
					<select class="form-control" name="hero_id" ng-model="formData.hero_id"
						ng-init="formData.hero_id=0" multiple="multiple"
						ng-multiple="true" size=10>
						<optgroup
							label="选择英雄(按住Ctrl可进行多选)">
							<?php foreach ($heros as $k => $v) { ?>
								<option value="<?php echo $k?>"><?php echo $k.':'.$v;?></option>
							<?php } ?>		
							</optgroup>
					</select>
				</div>
			</div>
			<div class="col-md-10">
				<select class="form-control" name="formation_type" ng-model="formData.formation_type"
					ng-init="formData.formation_type=0" >
						<option value="0">选择阵容类型(可选)</option>
						<option value="1">竞技场进攻</option>
						<option value="2">竞技场防守</option>
				</select>
			</div></br></br>
			<div class="form-group">
				<div class="col-md-5" style="padding: 10;">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0" multiple="multiple"
						ng-multiple="true" size=10>
						<optgroup
							label="<?php echo Lang::get('serverapi.select_game_server') ?>(按住Ctrl可进行多选)">
							<?php foreach ($servers as $k => $v) { ?>
								<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
							<?php } ?>		
							</optgroup>
					</select>
				</div>
				<div class="col-md-5">
					<select class="form-control" name="vip" ng-model="formData.vip"
						ng-init="formData.vip=0" multiple="multiple"
						ng-multiple="true" size=10>
						<optgroup
							label="选择vip等级(可选)">
							<?php for ($i=1 ; $i<=15 ; $i++) { ?>
								<option value="<?php echo $i ?>"><?php echo 'vip'.$i;?></option>
							<?php } ?>		
							</optgroup>
					</select>
				</div>
			</div></br></br>
			<div class="form-group">
				<div class="col-md-5">
					<select class="form-control" name="player_lev" ng-model="formData.player_lev"
						ng-init="formData.player_lev=0" multiple="multiple"
						ng-multiple="true" size=10>
						<optgroup
							label="选择玩家等级(可选)">
							<?php for ($i=1 ; $i<=100 ; $i++) { ?>
								<option value="<?php echo $i ?>"><?php echo $i;?></option>
							<?php } ?>		
							</optgroup>
					</select>
				</div>
				<div class="col-md-5" ng-if="formData.search_type == 3 || formData.search_type == 4">
					<select class="form-control" name="hero_type" ng-model="formData.hero_type"
						ng-init="formData.hero_type=0" multiple="multiple"
						ng-multiple="true" size=10>
						<optgroup
							label="选择英雄类型(可选)">
							<option value="12">前排力量</option>
							<option value="13">中排力量</option>
							<option value="14">后排力量</option>
							<option value="22">前排智力</option>
							<option value="23">中排智力</option>
							<option value="24">后排智力</option>
							<option value="32">前排敏捷</option>
							<option value="33">中排敏捷</option>
							<option value="34">后排敏捷</option>
							</optgroup>
					</select>
				</div>
				<div class="col-md-2" style="padding-top: 30px;">
					<input type='button' class="btn btn-info"
						value="<?php echo Lang::get('basic.btn_show') ?>"
						ng-click="process('/slave-api/mnsg/formation')" />
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
		<div class="col-xs-12">
			<table class="table table-striped" ng-if="is_show == 1 && (formData.search_type == 1 || formData.search_type == 3 )">
				<thead>
					<tr class="info">
						<td><b>服务器</b></td>
						<td><b>总登场数</b></td>
						<td><b>登场数</b></td>
						<td><b>登场率</b></td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="t in items">
						<td>{{t.server_name}}</td>
						<td>{{t.total}}</td>
						<td>{{t.count}}</td>
						<td>{{(t.count/t.total)*100 | number:2}}%<td>
					</tr>
				</tbody>
			</table>
			<table class="table table-striped" ng-if="is_show == 1 && (formData.search_type == 2 || formData.search_type == 4 )">
				<thead>
					<tr class="info">
						<td><b>服务器</b></td>
						<td><b>场数</b></td>
						<td><b>胜利场数</b></td>
						<td><b>胜率</b></td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="t in items">
						<td>{{t.server_name}}</td>
						<td>{{t.total}}</td>
						<td>{{t.count}}</td>
						<td>{{(t.count/t.total)*100 | number:2}}%<td>
					</tr>
				</tbody>
			</table>
			<table class="table table-striped" ng-if="is_show == 1 && (formData.search_type == 5)">
				<thead>
					<tr class="info">
						<td><b>英雄</b></td>
						<td><b>参加场数</b></td>
						<td><b>胜利场数</b></td>
						<td><b>总登场数</b></td>
						<td><b>登场数</b></td>
						<td><b>胜率</b></td>
						<td><b>登场率</b></td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="t in items">
						<td>{{t.hero_name}}</td>
						<td>{{t.appear_total}}</td>
						<td>{{t.hero_win}}</td>
						<td>{{t.all_total_appear}}</td>
						<td>{{t.hero_appear}}</td>
						<td>{{(t.hero_win/t.hero_appear)*100 | number:2}}%</td>
						<td>{{(t.hero_appear/t.all_total_appear)*100 | number:2}}%</td>
					</tr>
				</tbody>
			</table>
		</div>
</div>