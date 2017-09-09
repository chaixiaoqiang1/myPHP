<script src="http://echarts.baidu.com/build/dist/echarts-all.js"></script>
<script> 
function getUserPokerController($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.start_time = null;
    $scope.end_time = null;
    $scope.formData = {};
    $scope.total = {};
    $scope.processFrom = function() {
		$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        alertService.alerts = $scope.alerts;
        $http({
            'method': 'post',
            'url': '/slave-api/poker/user-log',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
            $scope.total = data.log;
            var myChart = echarts.init(document.getElementById('echart')); 
            var linenames = ['<?php echo Lang::get("slave.poker_num");?>','<?php echo Lang::get("slave.poker_pay");?>','<?php echo Lang::get("slave.poker_week_log");?>','<?php echo Lang::get("slave.poker_newusers");?>','<?php echo Lang::get("slave.poker_old");?>','<?php echo Lang::get("slave.poker_rate");?>','<?php echo Lang::get("slave.poker_online_max");?>','<?php echo Lang::get("slave.poker_online_avg");?>','<?php echo Lang::get("slave.poker_play_max");?>','<?php echo Lang::get("slave.poker_play_avg");?>'];
            var option = {
            	tooltip: {
		                show: true
		            },
		        legend: {
		                data: linenames
		            },
		        xAxis : [
	        		{
			            type : 'category',
			            boundaryGap : false,
			            data : data.date
			        }
		    	],
			    yAxis : [
			        {
			            type : 'value',
			        }
			    ],
			    series : [
			    	{
			    		name:'<?php echo Lang::get("slave.poker_num");?>',
						type:'line',
						data:data.f_num
					},
					{
			    		name:'<?php echo Lang::get("slave.poker_pay");?>',
						type:'line',
						data:data.f_pay_num
					},
					{
			    		name:'<?php echo Lang::get("slave.poker_week_log");?>',
						type:'line',
						data:data.f_week_log
					},
					{
			    		name:'<?php echo Lang::get("slave.poker_newusers");?>',
						type:'line',
						data:data.f_new
					},
					{
			    		name:'<?php echo Lang::get("slave.poker_old");?>',
						type:'line',
						data:data.f_old
					},
					{
			    		name:'<?php echo Lang::get("slave.poker_rate");?>',
						type:'line',
						data:data.f_rate
					},
					{
			    		name:'<?php echo Lang::get("slave.poker_online_max");?>',
						type:'line',
						data:data.f_max1
					},
					{
			    		name:'<?php echo Lang::get("slave.poker_online_avg");?>',
						type:'line',
						data:data.f_avg1
					},
					{
			    		name:'<?php echo Lang::get("slave.poker_play_max");?>',
						type:'line',
						data:data.f_max2
					},
					{
			    		name:'<?php echo Lang::get("slave.poker_play_avg");?>',
						type:'line',
						data:data.f_avg2
					},
			    	]
            };
     		
			myChart.setOption(option); 


            
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
} 
</script>
<div class="col-xs-12" ng-controller="getUserPokerController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/poker/user-log" method="post" role="form"
				ng-submit="processFrom('/slave-api/poker/user-log')"
				onsubmit="return false;">
				
				<div class="form-group" style="height:35px;">
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

				<div class="clearfix">
				</div>
				<input type="submit" class="btn btn-default" style=""
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

	<div id="echart" style="height:500px;width:100%;"></div>
	<div class="col-xs-12">
		<table class="table table-striped">
			<thead>
				<tr class="info" id="server">
					<td><?php echo Lang::get("slave.poker_date");?></td>
					<td><?php echo Lang::get("slave.poker_num");?></td>
					<td><?php echo Lang::get("slave.poker_pay");?></td>
					<td><?php echo Lang::get("slave.poker_week_log");?></td>
					<td><?php echo Lang::get("slave.poker_newusers");?></td>
					<td><?php echo Lang::get("slave.poker_old");?></td>
					<td><?php echo Lang::get("slave.poker_rate");?></td>
					<td><?php echo Lang::get("slave.poker_online_max");?></td>
					<td><?php echo Lang::get("slave.poker_online_avg");?></td>
					<td><?php echo Lang::get("slave.poker_play_max");?></td>
					<td><?php echo Lang::get("slave.poker_play_avg");?></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in total">
					<td>{{t.date}}</td>
					<td>{{t.num}}</td>
					<td>{{t.pay_num}}</td>
					<td>{{t.week_log}}</td>
					<td>{{t.new}}</td>
					<td>{{t.old}}</td>
					<td>{{t.rate}}</td>
					<td>{{t.max1}}</td>
					<td>{{t.avg1}}</td>
					<td>{{t.max2}}</td>
					<td>{{t.avg2}}</td>
				</tr>

			</tbody>
		</table>
	</div> 
</div>