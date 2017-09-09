<script type="text/javascript">
function updateAmountController($scope, $http, alertService) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.processFrom = function(url) {
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'put',
            'url'    : url,
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

<div class="col-xs-12" ng-controller="updateAmountController">
    <div class="row" >
        <div class="eb-content">
            <form action="/amount/<?php echo $amount->amount_id; ?>" method="put" role="form" ng-submit="processFrom('/amount/<?php echo $amount->amount_id; ?>')" onsubmit="return false;">

                <div class="form-group">
                    <label for="currency_id"></label>
                    <select class="form-control" name="currency_id" ng-model="formData.currency_id" ng-init="formData.currency_id=<?php echo $amount->currency_id ?>">
                        <option value="0"><?php echo '选择'.Lang::get('amount.pay_currency') ?></option>
                        <?php foreach (Currency::all() as $k => $v) { ?>
                        <option value="<?php echo $v->currency_id?>"><?php echo $v->currency_name;?></option>
                        <?php } ?>      
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="pay_amount"></label>
                    <input type="text" class="form-control" id="pay_amount" ng-init="formData.pay_amount='<?php echo $amount->pay_amount?>'" required ng-model="formData.pay_amount" name="pay_amount"/>
                </div>

                <div class="form-group">
                    <label for="yuanbao_amount"></label>
                    <input type="text" class="form-control" id="yuanbao_amount" ng-init="formData.yuanbao_amount='<?php echo $amount->yuanbao_amount?>'" required ng-model="formData.yuanbao_amount" name="yuanbao_amount"/>
                </div>
                
                <div class="form-group">
                    <label for="yuanbao_extra"></label>
                    <input type="text" class="form-control" id="yuanbao_extra" ng-init="formData.yuanbao_extra='<?php echo $amount->yuanbao_extra?>'" required ng-model="formData.yuanbao_extra" name="yuanbao_extra"/>
                </div>
                
                <div class="form-group">
                    <label for="yuanbao_hudong"></label>
                    <input type="text" class="form-control" id="yuanbao_hudong" ng-init="formData.yuanbao_hudong='<?php echo $amount->yuanbao_hudong?>'" required ng-model="formData.yuanbao_hudong" name="yuanbao_hudong"/>
                </div>    
                <div class="form-group">
                    <label for="pay_type_id">pay_type_id</label>
                </div>      

                <div class="form-group">
                    <label for="method_id">method_id</label>
                </div>  

                <div class="form-group">
                    <label for="platform_id"></label>
                    <select class="form-control" name="platform_id" ng-model="formData.platform_id" ng-init="formData.platform_id=<?php echo $amount->platform_id ?>">
                        <option value="0"><?php echo '选择'.Lang::get('amount.pay_platform') ?></option>
                        <?php foreach (Platform::all() as $k => $v) { ?>
                        <option value="<?php echo $v->platform_id?>"><?php echo $v->platform_name;?></option>
                        <?php } ?>      
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