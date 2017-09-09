<script type="text/javascript">
	function updateAdJsController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		
		$scope.encode_utf8=function (s) {
              return unescape(encodeURIComponent(s));
        }
 
		
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'put',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="updateAdJsController">
	<div class="row">
		<div class="eb-content">
			<form action="/ad/js/<?php echo $js->js_id?>" method="put" role="form" ng-submit="processFrom('/ad/js/<?php echo $js->js_id;?>')" onsubmit="return false;">
				<div class="form-group">
					<label for="js_id"></label>
					<input type="text" class="form-control" id="js_id" readonly ng-init="formData.js_id='<?php echo $js->js_id?>'"  required ng-model="formData.js_id" name="js_id" autofocus="autofocus" ng-autofocus="true" /> 
				</div>
				<?php echo Lang::get('campaigns.js_name');?>
				<div class="form-group">
					<label for="js_name"></label>
					<input type="text" class="form-control" id="js_name" ng-init="formData.js_name='<?php echo $js->js_name?>'"  required ng-model="formData.js_name" name="js_name" autofocus="autofocus" ng-autofocus="true" /> 
				</div>
							
				<?php echo Lang::get('campaigns.source');?>
				<div class="form-group">
					<label for="source"></label>
					<input type="text" class="form-control" id="source" ng-init="formData.source='<?php echo $js->source?>'"   ng-model="formData.source" name="source" autofocus="autofocus" ng-autofocus="true" /> 
				</div>
				
				<?php echo Lang::get('campaigns.campaign');?>
				<div class="form-group">
					<label for="campaign"></label>
					<input type="text" class="form-control" id="campaign" ng-init="formData.campaign='<?php echo $js->campaign?>'"   ng-model="formData.campaign" name="campaign" autofocus="autofocus" ng-autofocus="true" /> 
				</div>
				<?php echo Lang::get('campaigns.is_open')?>
				<div class="form-group">
					<label for="is_open"></label>
					 <select name="is_open" ng-model="formData.is_open" ng-init="formData.is_open=<?php echo $js->is_open?>" class="form-control">
						<option value="<?php echo $js->is_open?>"><?php echo $js->is_open ?"开启" :"关闭" ?></option>
						<?php 
						    $open = Config::get("is_open");
						    foreach ($open as $k => $v) {
						    	if(($js->is_open ?"开启" :"关闭") == $v ){
						    		continue;
						    	}
						?>
						    <option value="<?php echo $v?>" ><?php echo $v;?></option>
						<?php
							}
						?>
					</select>
				</div>
				
				<?php echo Lang::get('campaigns.js')?>
				<div class="form-group">
					<label for="js"></label>
					<textarea type="text" class="form-control" id="js" cols="100" rows="15" ng-init="formData.js=encode_utf8('<?php echo $js->content?>')"  ng-model="formData.js" name="js" autofocus="autofocus" /></textarea>
				</div>
				
			
				<?php echo Lang::get('campaigns.location')?>
				<div class="form-group">
					<label for="is_open"></label>
					 <select name="location" ng-model="formData.location" ng-init="formData.location='<?php echo $js->location?>'" class="form-control">
						<option value="<?php echo $js->location?>"><?php echo ($js->location == 1) ? "注册":(($js->location == 2) ? "创建"  : "充值")  ?></option>
						<?php 
						    $open = Config::get("location");
						    foreach ($open as $k => $v) {
						    	if($js->location == $k ){
						    		continue;
						    	}
						?>
						    <option value="<?php echo $k?>" ><?php echo $v;?></option>
						<?php
							}
						?>
					</select>
				</div>
				
				<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit')?>" />
			</form>
		</div>
	</div>
	
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	
</div>