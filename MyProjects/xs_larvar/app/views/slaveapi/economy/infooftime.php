<script src="/js/echarts.min.js"></script>
<script>
	function getAbnormalEconomyController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.items = [];
		$scope.firstpay = [];
		$scope.arppu = [];
		$scope.devide_parts = [];
		$scope.time_group = [];
		$scope.show = 0;
		$scope.processFrom = function() {
			$scope.alerts = [];
			$scope.show = 1;
			$scope.firstpay = [];
			$scope.arppu = [];
			$scope.devide_parts = [];
			$scope.time_group = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/payment/infooftime',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.firstpay = data.firstpayinfo;
				$scope.arppu = data.arppu;
				$scope.devide_parts = data.devide_parts;
				$scope.time_group = data.time_group;
				$scope.pay_newer = data.pay_newer;
				$scope.pay_trend = data.trend;
				var myChart1 = echarts.init(document.getElementById('echart1'));
				option1 = {
					title : {
				        text: '人数比例',
				        x:'center'
				    },
				    tooltip: {
		                show: true,
		                formatter: "{a} <br/>{b} : {c} ({d}%)"
		            	},
				    legend: {
				        orient : 'vertical',
				        x : 'left',
				        data:[$scope.firstpay['lessthan3']['name'],$scope.firstpay['between3and15']['name'],$scope.firstpay['between15and30']['name'],$scope.firstpay['largerthan30']['name']]
				    },
				    calculable : true,
				    series : [
				        {
				            type:'pie',
				            radius : '55%',
				            center: ['50%', '60%'],
				            data:[
				                {value:$scope.firstpay['lessthan3']['value'], name:$scope.firstpay['lessthan3']['name']},
				                {value:$scope.firstpay['between3and15']['value'], name:$scope.firstpay['between3and15']['name']},
				                {value:$scope.firstpay['between15and30']['value'], name:$scope.firstpay['between15and30']['name']},
				                {value:$scope.firstpay['largerthan30']['value'], name:$scope.firstpay['largerthan30']['name']}
				            ]
				        }
				    ]
				};

				var myChart2 = echarts.init(document.getElementById('echart2'));
				option2 = {
					title : {
				        text: '人数比例',
				        x:'center'
				    },
				    tooltip: {
		                show: true,
		                formatter: "{a} <br/>{b} : {c} ({d}%)"
		            	},
				    legend: {
				        orient : 'vertical',
				        x : 'left',
				        data:[$scope.devide_parts['10%']['name'],$scope.devide_parts['40%']['name'],$scope.devide_parts['50%']['name']]
				    },
				    calculable : true,
				    series : [
				        {
				            type:'pie',
				            radius : '55%',
				            center: ['50%', '60%'],
				            data:[
				                {value:$scope.devide_parts['10%']['num'], name:$scope.devide_parts['10%']['name']},
				                {value:$scope.devide_parts['40%']['num'], name:$scope.devide_parts['40%']['name']},
				                {value:$scope.devide_parts['50%']['num'], name:$scope.devide_parts['50%']['name']}
				            ]
				        }
				    ]
				};
				var myChart3 = echarts.init(document.getElementById('echart3'));
				option3 = {
					title : {
				        text: '充值总额比例',
				        x:'center'
				    },
				    tooltip: {
		                show: true,
		                formatter: "{a} <br/>{b} : {c} ({d}%)"
		            	},
				    legend: {
				        orient : 'vertical',
				        x : 'left',
				        data:[$scope.devide_parts['10%']['name'],$scope.devide_parts['40%']['name'],$scope.devide_parts['50%']['name']]
				    },
				    calculable : true,
				    series : [
				        {
				            type:'pie',
				            radius : '55%',
				            center: ['50%', '60%'],
				            data:[
				                {value:$scope.devide_parts['10%']['value'], name:$scope.devide_parts['10%']['name']},
				                {value:$scope.devide_parts['40%']['value'], name:$scope.devide_parts['40%']['name']},
				                {value:$scope.devide_parts['50%']['value'], name:$scope.devide_parts['50%']['name']}
				            ]
				        }
				    ]
				};
				var arr = data.time_group;
				var value = [];
				var linenames=[];
				if(arr['1']) {
					var item ={
						name:$scope.time_group['1']['name'],
			            value:$scope.time_group['1']['num']
					}
					var name =	$scope.time_group['1']['name'];
					value.push(item);
					linenames.push(name);
				}
				if(arr['2']) {
					var item ={
						name:$scope.time_group['2']['name'],
			            value:$scope.time_group['2']['num']
					}
					var name =	$scope.time_group['2']['name'];
					value.push(item);
					linenames.push(name);
				}
				if(arr['3']) {
					var item ={
						name:$scope.time_group['3']['name'],
			            value:$scope.time_group['3']['num']
					}
					var name =	$scope.time_group['3']['name'];
					value.push(item);
					linenames.push(name);
				}
				if(arr['4']) {
					var item ={
						name:$scope.time_group['4']['name'],
			            value:$scope.time_group['4']['num']
					}
					var name =	$scope.time_group['4']['name'];
					value.push(item);
					linenames.push(name);
				}
				if(arr['5']) {
					var item ={
						name:$scope.time_group['5']['name'],
			            value:$scope.time_group['5']['num']
					}
					var name =	$scope.time_group['5']['name'];
					value.push(item);
					linenames.push(name);
				}
				if(arr['6']) {
					var item ={
						name:$scope.time_group['6']['name'],
			            value:$scope.time_group['6']['num']
					}
					var name =	$scope.time_group['6']['name'];
					value.push(item);
					linenames.push(name);
				}
				if(arr['7']) {
					var item ={
						name:$scope.time_group['7']['name'],
			            value:$scope.time_group['7']['num']
					}
					var name =	$scope.time_group['7']['name'];
					value.push(item);
					linenames.push(name);
				}
				if(arr['8']) {
					var item ={
						name:$scope.time_group['8']['name'],
			            value:$scope.time_group['8']['num']
					}
					var name =	$scope.time_group['8']['name'];
					value.push(item);
					linenames.push(name);
				}
				if(arr['9']) {
					var item ={
						name:$scope.time_group['9']['name'],
			            value:$scope.time_group['9']['num']
					}
					var name =	$scope.time_group['9']['name'];
					value.push(item);
					linenames.push(name);
				}
				if(arr['10']) {
					var item ={
						name:$scope.time_group['10']['name'],
			            value:$scope.time_group['10']['num']
					}
					var name =	$scope.time_group['10']['name'];
					value.push(item);
					linenames.push(name);
				}
			  	


				
				var myChart4 = echarts.init(document.getElementById('echart4'));
				option4 = {
					title : {
				        text: '人数比例',
				        x:'center'
				    },
				    tooltip: {
		                show: true,
		                formatter: "{a} <br/>{b} : {c} ({d}%)"
		            	},
				    legend: {
				        orient : 'vertical',
				        x : 'left',
				        data:linenames
				    },
				    calculable : true,
				    series : [
				        {
				            type:'pie',
				            radius : '55%',
				            center: ['50%', '60%'],
				            data:value
				        }
				    ]
				};
				var myChart5 = echarts.init(document.getElementById('echart5'));
				option5 = {
					title : {
				        text: '玩家数量比例',
				        x:'center'
				    },
				    tooltip: {
		                show: true,
		                formatter: "{a} <br/>{b} : {c} ({d}%)"
		            	},
				    legend: {
				        orient : 'vertical',
				        x : 'left',
				        data:['新玩家','老玩家']
				    },
				    calculable : true,
				    series : [
				        {
				            type:'pie',
				            radius : '55%',
				            center: ['50%', '60%'],
				            data:[
				                {value:$scope.pay_newer['new_player_num'], name:'新玩家'},
				                {value:$scope.pay_newer['old_player_num'], name:'老玩家'}
				            ]
				        }
				    ]
				};
				var myChart6 = echarts.init(document.getElementById('echart6'));
				option6 = {
					title : {
				        text: '充值总额比例',
				        x:'center'
				    },
				    tooltip: {
		                show: true,
		                formatter: "{a} <br/>{b} : {c} ({d}%)"
		            	},
				    legend: {
				        orient : 'vertical',
				        x : 'left',
				        data:['新玩家','老玩家']
				    },
				    calculable : true,
				    series : [
				        {
				            type:'pie',
				            radius : '55%',
				            center: ['50%', '60%'],
				            data:[
				                {value:$scope.pay_newer['new_dollar'], name:'新玩家'},
				                {value:$scope.pay_newer['old_dollar'], name:'老玩家'}
				            ]
				        }
				    ]
				};

				var echart_trend = echarts.init(document.getElementById('echart_trend'));
				var echart_trend_add = echarts.init(document.getElementById('echart_trend_add'));
				var trend_len = 288;
				var x_title = [];
				var user_num = [];
				var pay_num = [];
				var all_times = [];
				var pay_times = [];
				var all_dollar = [];
				var pay_dollar = [];
				var all_times_add = [];
				var pay_times_add = [];
				var all_dollar_add = [];
				var pay_dollar_add = [];
				for(var i =0;i<trend_len;i++){
					x_title[i] = data.pay_trend[i].time;
					user_num[i] = data.pay_trend[i].all_user;
					pay_num[i] = data.pay_trend[i].pay_user;
					all_times[i] = data.pay_trend[i].all_times;
					pay_times[i] = data.pay_trend[i].pay_times;
					all_dollar[i] = data.pay_trend[i].all_dollar;
					pay_dollar[i] = data.pay_trend[i].pay_dollar;
					if(i == 1){
						all_times_add[i] = data.pay_trend[i].all_times;
						pay_times_add[i] = data.pay_trend[i].pay_times;
						all_dollar_add[i] = data.pay_trend[i].all_dollar;
						pay_dollar_add[i] = data.pay_trend[i].pay_dollar;
					}else{
						all_times_add[i] = all_times_add[i-1] - (-data.pay_trend[i].all_times);
						pay_times_add[i] = pay_times_add[i-1] - (-data.pay_trend[i].pay_times);
						all_dollar_add[i] = all_dollar_add[i-1] - (-data.pay_trend[i].all_dollar);
						pay_dollar_add[i] = pay_dollar_add[i-1] - (-data.pay_trend[i].pay_dollar);
					}
				}
				var seriesdata = [	                
						{
		                    "name":'<?php echo Lang::get('slave.all_user_num');?>',
		                    "type":"line",
		                    "data":user_num
		                },
		                {
		                    "name":'<?php echo Lang::get('slave.pay_user_num');?>',
		                    "type":"line",
		                    "data":pay_num
		                },
		                {
		                    "name":'<?php echo Lang::get('slave.all_times');?>',
		                    "type":"line",
		                    "data":all_times
		                },
		                {
		                    "name":'<?php echo Lang::get('slave.pay_times');?>',
		                    "type":"line",
		                    "data":pay_times
		                },
		                {
		                	"name":"<?php echo Lang::get('slave.all_dollar');?>",
		                    "type":"line",
		                    "data":all_dollar
		                },
		                {
		                	"name":'<?php echo Lang::get('slave.pay_dollar');?>',
		                    "type":"line",
		                    "data":pay_dollar
		                }
		            ];

		        var seriesdata_add = [	                
		                {
		                    "name":'<?php echo Lang::get('slave.all_times');?>',
		                    "type":"line",
		                    "data":all_times_add
		                },
		                {
		                    "name":'<?php echo Lang::get('slave.pay_times');?>',
		                    "type":"line",
		                    "data":pay_times_add
		                },
		                {
		                	"name":"<?php echo Lang::get('slave.all_dollar');?>",
		                    "type":"line",
		                    "data":all_dollar_add
		                },
		                {
		                	"name":'<?php echo Lang::get('slave.pay_dollar');?>',
		                    "type":"line",
		                    "data":pay_dollar_add
		                }
		           	];
				option_trend = {
		            tooltip: {
		                show: true
		            },
		            legend: {
		                data: ['<?php echo Lang::get('slave.all_user_num');?>', 
		                		'<?php echo Lang::get('slave.all_times');?>',
		                		'<?php echo Lang::get('slave.all_dollar');?>',
		                		'<?php echo Lang::get('slave.pay_user_num');?>',
		                		'<?php echo Lang::get('slave.pay_times');?>',
		                		'<?php echo Lang::get('slave.pay_dollar');?>']
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
		                    type : 'value'
		                }
		            ],
		            series : seriesdata
				};

				option_trend_add = {
		            tooltip: {
		                show: true
		            },
		            legend: {
		                data: ['<?php echo Lang::get('slave.all_times');?>',
		                		'<?php echo Lang::get('slave.all_dollar');?>',
		                		'<?php echo Lang::get('slave.pay_times');?>',
		                		'<?php echo Lang::get('slave.pay_dollar');?>']
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
		                    type : 'value'
		                }
		            ],
		            series : seriesdata_add
				};
				
				myChart1.setOption(option1); 
				myChart2.setOption(option2); 
				myChart3.setOption(option3); 
				myChart4.setOption(option4); 
				myChart5.setOption(option5); 
				myChart6.setOption(option6);
				echart_trend.setOption(option_trend);
				echart_trend_add.setOption(option_trend_add);

			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<style> 
.div-height{border:1px solid #F00; width:400px; max-height:500px; min-height:200px;} 
</style> 
<div class="col-xs-12" ng-controller="getAbnormalEconomyController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/slave-api/economy/parts" method="get" role="form"
				ng-submit="processFrom()" onsubmit="return false;">
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
				<input type="submit" class="btn btn-default"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
		<div class="col-xs-8"><br><b><?php echo Lang::get('slave.recharge_tips'); ?></b></div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

	<!-- <div id="echart" style="height:500px;width:100%;"></div> -->
	<div class="col-xs-12">
		<table class="table table-striped" ng-if="show == 1">
			<tr>
				<td width="70%">
					<table class="table table-striped">
						<thead>
							<tr class="info">
								<td><b><?php echo Lang::get('slave.first_recharge_interval'); ?></b></td>
								<td><b><?php echo Lang::get('slave.number'); ?></b></td>
								<td><b><?php echo Lang::get('slave.recharge_again_num'); ?></b></td>
								<td><b><?php echo Lang::get('slave.recharge_again_rate'); ?></b></td>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="f in firstpay">
								<td>{{f.name}}</td>
								<td>{{f.value}}</td>
								<td>{{f.num}}</td>
								<td>{{f.num/f.value*100 | number:2}}%</td> 
						</tbody>
					</table>
				</td>
				<td>
					<div id="echart1" class="div-height"></div>
				</td>
			</tr>						
		</table>
		<table class="table table-striped" ng-if="show == 1">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.data_type'); ?></b></td>
					<td><b><?php echo Lang::get('slave.dollar'); ?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="a in arppu">
					<td>{{a.name}}</td>
					<td>{{a.value}}</td>
				</tr>
			</tbody>
		</table>
		<table class="table table-striped" ng-if="show == 1">
			<tr>
				<td>
					<table class="table table-striped">
						<thead>
							<tr class="info">
								<td><b><?php echo Lang::get('slave.recharge_sum_interval'); ?></b></td>
								<td><b><?php echo Lang::get('slave.number'); ?></b></td>
								<td><b><?php echo Lang::get('slave.recharge_sum_interval_allplayers'); ?></b></td>
								<td><b><?php echo Lang::get('slave.rate'); ?></b></td>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="d in devide_parts">
								<td>{{d.name}}</td>
								<td>{{d.num}}</td>
								<td>{{d.value}}</td>
								<td>{{d.rate}}</td>
							</tr>
						</tbody>
					</table>
				</td>
				<td width="10%">
					<div id="echart2" class="div-height"></div>
				</td>
				<td width="10%">
					<div id="echart3" class="div-height"></div>
				</td>
			</tr>
		</table>
		<table class="table table-striped" ng-if="show == 1">
			<tr>
				<td width = "70%">
					<table class="table table-striped">
						<thead>
							<tr class="info">
								<td><b><?php echo Lang::get('slave.recharge_count'); ?></b></td>
								<td><b><?php echo Lang::get('slave.number'); ?></b></td>
								<td><b><?php echo Lang::get('slave.rate'); ?></b></td> 
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="t in time_group">
								<td>{{t.name}}</td>
								<td>{{t.num}}</td>
								<td>{{t.rate}}</td> 
							</tr>
						</tbody>
					</table>
				</td>
				<td>
					<div id="echart4" class="div-height"></div>
				</td>
			</tr>
		</table>
		<table class="table table-striped" ng-if="show == 1">
			<tr>
				<td width="70%">
					<table class="table table-striped">
						<thead>
							<tr class="info">
								<td><b><?php echo Lang::get('slave.type'); ?></b></td>
								<td><b><?php echo Lang::get('slave.player_num'); ?></b></td>
								<td><b><?php echo Lang::get('slave.rate'); ?></b></td>
								<td><b><?php echo Lang::get('slave.order_recharge_amount'); ?></b></td>
								<td><b><?php echo Lang::get('slave.rate'); ?></b></td> 
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Total</td>
								<td>{{pay_newer.all_player_num}}</td>
								<td>-</td>
								<td>{{pay_newer.all_dollar}}</td>
								<td>-</td>
							</tr>
							<tr>
								<td><?php echo Lang::get('slave.newer'); ?></td>
								<td>{{pay_newer.new_player_num}}</td>
								<td>{{pay_newer.new_player_num / pay_newer.all_player_num*100 | number:2}}%</td>
								<td>{{pay_newer.new_dollar}}</td>
								<td>{{pay_newer.new_dollar / pay_newer.all_dollar*100 | number:2}}%</td>
							</tr>
							<tr>
								<td><?php echo Lang::get('slave.older'); ?></td>
								<td>{{pay_newer.old_player_num}}</td>
								<td>{{pay_newer.old_player_num / pay_newer.all_player_num*100 | number:2}}%</td>
								<td>{{pay_newer.old_dollar}}</td>
								<td>{{pay_newer.old_dollar / pay_newer.all_dollar*100 | number:2}}%</td>
							</tr>
						</tbody>
					</table>
				</td>
				<td>
					<div id="echart5" class="div-height"></div>
				</td>
				<td>
					<div id="echart6" class="div-height"></div>
				</td>
			</tr>
		</table>
	</div>
	<div class="col-xs-10" ng-show="show">
		<h4>充值各时段信息</h4>
		<div id="echart_trend" style="height:500px;width:100%"></div>
	</div>
	<div class="col-xs-10" ng-show="show">
		<h4>充值各时段累计信息</h4>
		<div id="echart_trend_add" style="height:500px;width:100%"></div>
	</div>
</div>