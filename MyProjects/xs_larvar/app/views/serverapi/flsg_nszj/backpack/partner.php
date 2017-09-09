<script>
	function PartnerLogController($scope, $http, alertService){
		$scope.formData = {};
		$scope.alerts = [];
		$scope.process = function(url){
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'    : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				var result = data;
				var len = result.length;
				for (var i = 0; i < len; i++) {
					if (result[i].status == "OK") {
						alertService.add('success', result[i].msg);
					}else if(result[i].status == "error"){
						alertService.add('danger', result[i].msg);
					}
				}
			}).error(function(){
				alertService.add('danger', data.error)  ;
			});
		};
	} 
</script>
<div class="col-xs-12" ng-controller="PartnerLogController">
	<div class="row">
		<div class="eb-content">
			<form method="post" ng-submit="process()" onsubmit="return false;">
				<div class="form-group ">
					<select class="form-control" name="server_id" ng-model="formData.server_id" ng-init="formData.server_id=0" multiple="multiple"
					ng-multiple="true" size=10>
						<option value="0"><?php echo Lang::get('serverapi.select_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name ?></option>
						<?php } ?>		
					</select>
				</div>

				<div class="form-group ">
					<select class="form-control" name="partner_id" ng-model="formData.partner_id" ng-init="formData.partner_id=0" multiple="multiple"
					ng-multiple="true" size=10>
						<option value="0"><?php echo Lang::get('serverapi.select_partner');?> </option>
						<?php foreach ($partner as $key => $value) {?>
							<option value="<?php echo $value->id?>"><?php echo $value->name?></option>
						<?php }?>	
					</select>
				</div>
				
				<br>
				<div class="form-group" style="height: 30px;">
					<br/>
					<span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.partner_introduce1')?></span>
				</div>
			
				<div style="height:60px"></div>
				<div class="col-md-6" style="padding: 0">
					<div class="form-group" style="height: 40px;">
						<div class="col-md-4" style="padding: 0">
							<input type='button' class="btn btn-primary"
								value="<?php echo Lang::get('serverapi.promotion_set') ?>"
								ng-click="process('/game-server-api/nszj/partner?action=open')" />
						</div>
						<div class="col-md-4" style="padding: 0">
							<input type='button' class="btn btn-primary"
								value="<?php echo Lang::get('serverapi.promotion_lookup') ?>"
								ng-click="process('/game-server-api/nszj/partner?action=look')" />
						</div>
						<div class="col-md-4" style="padding: 0">
							<input type='button' class="btn btn-danger"
								value="<?php echo Lang::get('serverapi.promotion_close') ?>"
								ng-click="process('/game-server-api/nszj/partner?action=close')" />
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class = "row marfin-top-10">
		<div class = "col-xs-6">
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>			
		</div>
	</div>
</div>