<script type="text/javascript">
	function AdJsController($scope, $http, alertService){
		$scope.alerts = [];
		$scope.formData = {};
	
		
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
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

<div class="col-xs-12" ng-controller="AdJsController">
	<div class="row" >
		<div class="eb-content">
			<form action="/ad/js" method="post" role="form" ng-submit="processFrom('/ad/js')" onsubmit="return false;">
				<?php echo Lang::get("campaigns.source_or_campaign");?>
				<div class="form-group">
					<input type="radio" name="valid" value="1" ng-model="formData.valid" /> <?php echo Lang::get('campaigns.source1')?>&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" name="valid" value="2" ng-model="formData.valid" /> <?php echo Lang::get('campaigns.campaign1')?>&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" name="valid" value="3" ng-model="formData.valid" /> <?php echo Lang::get('campaigns.all')?>
				</div>
				
				<div class="form-group">
					<?php echo Lang::get('campaigns.source');?>
					<label for="source"></label>
					<textarea type="text" class="form-control" id="source" placeholder="<?php echo Lang::get('campaigns.enter_source') ?>"  ng-model="formData.source" name="source" autofocus="autofocus"/> </textarea>
				</div>
				
				<?php echo Lang::get('campaigns.campaign')?>
				<div class="form-group">
					<label for="campaign"></label>
					<textarea type="text" class="form-control " id="campaign" placeholder="<?php echo Lang::get('campaigns.enter_campaign') ?>" ng-cols="30"  ng-model="formData.campaign" name="campaign" autofocus="autofocus"/> </textarea>
				</div>
				
				<?php echo Lang::get('campaigns.location')?>
				<div class="form-group">
					<select name="location" ng-model="formData.location" ng-init="formData.location=0" class="form-control" required>
						<option value="0"><?php echo Lang::get('campaigns.enter_location')?></option>
						<?php 
						    foreach( Config::get('location') as $key => $v){?>
							<option value="<?php echo $key?>"><?php echo $v?></option>
						<?php }?>
					</select>
				</div>
				<?php echo Lang::get('campaigns.js_name')?>
				<div class="form-group">
					<label for="js_name"></label>
					<input type="text" class="form-control" id="js_name" placeholder="<?php echo Lang::get('campaigns.js_name') ?>" required ng-model="formData.js_name" name="js_name" /> 
				</div>	
				<?php echo Lang::get('campaigns.js');?>
				<div class="form-group">
					<label for="js"></label>
					<textarea type="text" class="form-control" rows="15" cols="100" id="js" placeholder="<?php echo Lang::get('campaigns.enter_js') ?>" required ng-model="formData.js" name="js" autofocus="autofocus"/></textarea> 
				</div>			
				<?php echo Lang::get('campaigns.is_open')?>
				<div class="form-group">
					<select name="is_open" ng-model="formData.is_open" ng-init="formData.is_open='' " class="form-control" required>
						<option value=""><?php echo Lang::get('campaigns.is_open_on')?></option>
						<option value="1"><?php echo Lang::get('campaigns.open_on');?></option>
						<option value="0"><?php echo Lang::get('campaigns.open_close')?></option>
					</select>
				</div>

				<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>"/>	
			</form>	 
		</div><!-- /.col -->
	</div>

	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>