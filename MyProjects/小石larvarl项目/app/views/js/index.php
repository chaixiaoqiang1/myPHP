<script type="text/javascript">
	function AdLinkController($scope, $http, alertService) {
	    
	    $scope.alerts = [];
		$scope.formData = {};
		$scope.getGame = function() {
			$http({
				'method' : 'get',
				'url'	 : '/ad/action1?type=game',
				'params'   : $scope.formData,
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.game = data;
			}).error(function(data) {
				alertService.add('danger', data.error, 2000);
			});
		};
	   
		$scope.getJs= function() {
			$http({
				'method' : 'get',
				'url'	 : '/ad/action3?type=js',
				'params'   : $scope.formData,
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.js = data;
			}).error(function(data) {
				alertService.add('danger', data.error, 2000);
			});
		};
		
		
		
	}
</script>
<div class="col-xs-12" ng-controller="AdLinkController">
	<div class="row">
		<div class="eb-content">
			    <div class="form-group">
		            <a href="/ad/js/create" target = "_blank"><input type="button" class="btn btn-primary" value="<?php echo Lang::get('basic.btn_add') ?>"/></a>
	            </div>
				<table class="table table-striped" >
					<tr>
						<td><?php echo Lang::get('campaigns.js_id')?></td>
						<td><?php echo Lang::get('campaigns.js_name')?></td>
						<td><?php echo Lang::get('campaigns.js_type')?></td>
						<td><?php echo Lang::get('campaigns.source')?></td>
						<td><?php echo Lang::get('campaigns.campaign')?></td>
						<td><?php echo Lang::get('campaigns.is_open')?></td>
						<!--<td><?php echo Lang::get('campaigns.show_oper')?></td>-->
					</tr>
					<?php foreach($js as $key => $v){?>
					<tr>
						<td><?php echo $v->js_id?></td>
						<td><a href="/ad/js/<?php echo $v->js_id?>/edit" target="_blank"><?php echo $v->js_name?></a></td>
						<td><?php echo $v->type?></td>
						<td><?php echo $v->source?></td>
						<td><?php echo $v->campaign?></td>
						<td><?php echo $v->is_open?></td>
					</tr>
					<?php } ?>
				</table>
				<br /><br /><br /><br />
		</div>
	</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>

