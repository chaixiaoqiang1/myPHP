<script src="/js/echarts.min.js"></script>
<script>
	function pokerUserOnlineController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time=null;
		$scope.end_time=null;
		$scope.formData = {};
		$scope.show = 0;
		$scope.day2show = 0;

		$scope.process = function(url) {
			$scope.show = 0;
			$scope.day2show = 0;
			$scope.items1 = [];
			$scope.items2 = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.show = 1;
				var i =0;
				var title = [];
				var value1 = [];
				var value2 = [];
				var value3 = [];
				var value4 = [];
				var linenames = [];
				var seriesdata = [];
				var datalen = data.length;
				var len = 0;
				if(datalen == 0){
					linenames = [];
				}else{
					if(datalen == 1){
						linenames = ['在线','在玩'];
						$scope.items1 = data.day1;
					}else{
						$scope.day2show = 1;
						linenames = ['Day1在线','Day1在玩','Day2在线','Day2在玩'];
						$scope.items1 = data.day1;
						$scope.items2 = data.day2;
					}
				}
				if(datalen != 0){
					len = data.day1.length;
				}
				if(datalen != 0){
					for(;i<len;i++){
						title[i] = data.day1[i].time;
						value1[i] = data.day1[i].num;
						value2[i] = data.day1[i].playing;
						if(datalen == 2){
							value3[i] = data.day2[i].num;
							value4[i] = data.day2[i].playing;
							seriesdata = [		                
								{
				                    "name":"Day1在线",
				                    "type":"line",
				                    "data":value1
				                },
				                {
				                    "name":"Day1在玩",
				                    "type":"line",
				                    "data":value2
				                },
				                {
				                    "name":"Day2在线",
				                    "type":"line",
				                    "data":value3
				                },
				                {
				                    "name":"Day2在玩",
				                    "type":"line",
				                    "data":value4
				                }
				            ];
						}else{
							seriesdata = [		                
								{
				                    "name":"在线",
				                    "type":"line",
				                    "data":value1
				                },
				                {
				                    "name":"在玩",
				                    "type":"line",
				                    "data":value2
				                }
				            ];
						}
					}
				}
		        var myChart = echarts.init(document.getElementById('echart')); 
		        
		        var option = {
		            tooltip: {
		                show: true
		            },
		            legend: {
		                data: linenames
		            },
		            xAxis : [
		                {	
		                	axisLabel: {
								rotate: 60,
							},
		                	boundaryGap : false, 
		                    type : 'category',
		                    data : title
		                }
		            ],
		            yAxis : [
		                {
		                    type : 'value'
		                }
		            ],
		            series : seriesdata
		        };
		        myChart.setOption(option); 
				
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};
		
	}
</script>
<div class="col-xs-12" ng-controller="pokerUserOnlineController">
	<div class="row">
		<div class="eb-content">
			<div class="form-group">
				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix">
				<b>&nbsp;&nbsp;两天时间不同则对比两天数据，相同则取单日数据，查询今日数据请将两个时间都选在今天</b>
				<br/>
			</div>
			
			<div class="col-md-4" style="padding: 0">
				<input type='button' class="btn btn-default"
					value="<?php echo Lang::get('basic.btn_submit') ?>"
					ng-click="process('/game-server-api/poker/user-num')" />
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
		<div id="echart" style="height:500px;width:100%"></div>
		<table class="table table-striped" ng-if="show == 1">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.poker_time')?></b></td>
					<td><b><?php echo Lang::get('slave.poker_num_online');?></b></td>
					<td><b><?php echo Lang::get('slave.poker_num_playing');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items1">
					<td>{{t.time}}</td>
					<td>{{t.num}}</td>
					<td>{{t.playing}}</td>
				</tr>
			</tbody>
			<!--<tfoot>
				<div class="pagebar" style="margin-bottom:0px;">
			        <button class="btn btn-info" type="button" ng-disabled="currentPage == 0" ng-click="currentPage = currentPage - 1">上一页</button>
			        <button class="btn btn-info" type="button" ng-disabled="currentPage == pageNum() - 1" ng-click="currentPage = currentPage + 1">下一页</button>
			    </div>
			</tfoot>-->
		</table>	
		<table class="table table-striped" ng-if="day2show == 1">
			<thead>
				<tr class="info">
					<td><b>第二日<?php echo Lang::get('slave.poker_time')?></b></td>
					<td><b>第二日<?php echo Lang::get('slave.poker_num_online');?></b></td>
					<td><b>第二日<?php echo Lang::get('slave.poker_num_playing');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items2">
					<td>{{t.time}}</td>
					<td>{{t.num}}</td>
					<td>{{t.playing}}</td>	
				</tr>
			</tbody>
			<!--<tfoot>
				<div class="pagebar" style="margin-bottom:0px;">
			        <button class="btn btn-info" type="button" ng-disabled="currentPage == 0" ng-click="currentPage = currentPage - 1">上一页</button>
			        <button class="btn btn-info" type="button" ng-disabled="currentPage == pageNum() - 1" ng-click="currentPage = currentPage + 1">下一页</button>
			    </div>
			</tfoot>-->
		</table>	
	</div>
</div>


