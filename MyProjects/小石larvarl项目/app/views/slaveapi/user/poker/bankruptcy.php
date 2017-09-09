<script src="/js/echarts.min.js"></script>
<script>
	function PokerSignupInfoController($scope, $http, alertService, $filter)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.end_time = null;
		$scope.start_time = null;

		$scope.processFrom = function() {
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.create_start_time = $filter('date')($scope.create_start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.create_end_time = $filter('date')($scope.create_end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/poker/bankruptcy',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
                $scope.count = data.count;
                $scope.level = data.level;
                var bankruptcy_level = echarts.init(document.getElementById('bankruptcy_level'));
				var trend_len = 10050;
				var x_title = [];
				var user_num = [];
				for(var i =10001;i<=trend_len;i++){
					x_title[i-10001] = data.level[i].lev;
					user_num[i-10001] = data.level[i].num;
				}
				var seriesdata = [	                
						{
		                    "name":'<?php echo Lang::get('slave.bankruptcy_user_num');?>',
		                    "type":"line",
		                    smooth:true,
				            symbol: 'none',
				            sampling: 'average',
				            itemStyle: {
				                normal: {
				                    color: 'rgb(255, 70, 131)'
				                }
				            },
				            areaStyle: {
				                normal: {
				                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
				                        offset: 0,
				                        color: 'rgb(255, 158, 68)'
				                    }, {
				                        offset: 1,
				                        color: 'rgb(255, 70, 131)'
				                    }])
				                }
				            },
		                    "data":user_num
		                },
		            ];
				option_level = {
		            tooltip: {
		                show: true
		            },
		            legend: {
		                data: ['<?php echo Lang::get('slave.bankruptcy_user_num');?>', ]
		            },
		            xAxis : [
		                {	
		                	axisLabel: {
								rotate: 45,
							},
		                	boundaryGap : false, 
		                    type : 'category',
		                    data : x_title
		                }
		            ],
		            yAxis : [
		                {
		                    type : 'value',
		                    boundaryGap: [0, '100%']
		                }
		            ],
		            series : seriesdata
				};
				bankruptcy_level.setOption(option_level);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="PokerSignupInfoController">
	<div class="row">
		<div class="eb-content">
			<form action="" method="" role="form"
				ng-submit="processFrom()"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="by_create_time"
						id="by_create_time" ng-model="formData.by_create_time"
						ng-init="formData.by_create_time=0">
							<option value="0"><?php echo Lang::get('slave.not_by_create_time') ?></option>	
							<option value="1"><?php echo Lang::get('slave.by_create_time') ?></option>
					</select>
				</div>
				<div class="form-group" ng-show="formData.by_create_time">
					<b><?php echo Lang::get('slave.by_create_time'); ?></b><br>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="create_start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="create_end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="form-group" style="height:35px;">
					<b><?php echo Lang::get('slave.bankruptcy'); ?></b><br>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>

				<input type="submit" class="btn btn-default" style="margin-top:10px"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="row margin-top-10 eb-content">
		<div>
			<div class="panel panel-success">
				<div class="panel-heading">
					<?php echo Lang::get('slave.bankruptcy_info') ?>
				</div>
				<div class="panel-body">
					<dl class="dl-horizontal">
						<dt><?php echo Lang::get('slave.bankruptcy_user_num')?>:</dt>
						<dd>{{count.bankruptcy_user_num}}</dd>
						<dt><?php echo Lang::get('slave.bankruptcy_times')?>:</dt>
						<dd>{{count.bankruptcy_times}}</dd>
						<dt><?php echo Lang::get('slave.bustreward')?>:</dt>
						<dd>{{count.bustreward}}</dd>
						<dt><?php echo Lang::get('slave.playedplayer')?>:</dt>
						<dd>{{count.playedplayer}}</dd>
						<dt><?php echo Lang::get('slave.bankruptcy_dollar')?>:</dt>
						<dd>{{count.bankruptcy_dollar}}</dd>
					</dl>
				</div>
			</div>

			<div class="panel panel-success">
				<div class="panel-heading">
					<?php echo Lang::get('slave.bankruptcy_player_level') ?>
				</div>
				<div class="panel-body">
					<div id="bankruptcy_level" style="height:500px;width:100%"></div>
				</div>
			</div>
		</div>
	</div>
</div>