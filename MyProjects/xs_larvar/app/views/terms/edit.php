<script type="text/javascript">
function updateTermController($scope, $http, alertService) {
	$scope.alerts = [];
	$scope.formData = {};
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

<div class="col-xs-12" ng-controller="updateTermController">
	<div class="row" >
		<div class="eb-content">
			<form action="/ad/term/<?php echo $term->term_id; ?>" method="put" role="form" ng-submit="processFrom('/ad/term/<?php echo $term->term_id; ?>')" onsubmit="return false;">
				
				<?php echo Lang::get('campaigns.term_id');?>
				<div class="form-group">
					<label for="term_id"></label>
					<input type="text" class="form-control" id="term_id" readonly ng-init="formData.term_id='<?php echo $term->term_id?>'" required ng-model="formData.term_id" name="term_id" /> 
				</div>
								
                <?php echo Lang::get('campaigns.term_value')?>
				<div class="form-group">
					<label for="term_value"></label>
					<input type="text" class="form-control" id="term_value"  ng-init="formData.term_value='<?php echo $term->term_value?>'" required ng-model="formData.term_value" name="term_value" /> 
				</div>
				<div class="form-group">
					<label for="lp_id"></label>
				    <select name="lp_id" id="lp_id" ng-init="formData.lp_id='<?php echo $lp?>' " class="form-control" required ng-model="formData.lp_id">
						<option value="<?php echo  $lp?>"><?php echo $lp?></option>
						<?php 
						    foreach (AdLp::all() as $k => $v) {
						    	if( $lp == $v->lp_id ){
						    		continue;
						    	}
						?>
						    <option value="<?php echo $v->lp_id?>" ><?php echo $v->lp_id?></option>
						<?php
							}
						?>
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