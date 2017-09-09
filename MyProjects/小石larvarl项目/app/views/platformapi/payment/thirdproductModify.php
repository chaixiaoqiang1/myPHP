<script>
function modifyController($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.modify = function() {
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
            'url'    : '/platform-api/third_product/modify',
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

<div class="col-xs-12" ng-controller="modifyController">
    <div class="row" >
        <div class="eb-content">
            <form action="/platform-api/third_product/modify" ng-submit="modify()" onsubmit="return false;">
                
                <?php 
                $url = explode('/', Request::url());
                $url = $url[count($url)-1];
                
                if('modify' == $url){ ?>
                    <div class="form-group">
                        <label for="id">ID</label>
                        <input type="text" class="form-control" id="id" ng-init="formData.id='<?php echo $data['id'] ?>'" required ng-model="formData.id" name="id"/>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <label for="package_name">package_name</label>
                    <input type="text" class="form-control" id="package_name" ng-init=formData.package_name='<?php echo $data['package_name'];?>' required ng-model="formData.package_name" name="package_name"/>
                </div>

                <div class="form-group">
                    <label for="product_type">product_type</label>
                    <input type="text" class="form-control" id="product_type" ng-init=formData.product_type='<?php echo $data['product_type'];?>' required ng-model="formData.product_type" name="product_type"/>
                </div>

                <div class="form-group">
                    <label for="third_product_id">third_product_id</label>
                    <input type="text" class="form-control" id="third_product_id" ng-init=formData.third_product_id='<?php echo $data['third_product_id'];?>' required ng-model="formData.third_product_id" name="third_product_id"/>
                </div>

                <div class="form-group">
                    <label for="game_id">game_id</label>
                    <input type="text" class="form-control" id="game_id" ng-init=formData.game_id='<?php echo $data['game_id'];?>' required ng-model="formData.game_id" name="game_id"/>
                </div>

                <div class="form-group">
                    <label for="payment_id">payment_id</label>
                    <input type="text" class="form-control" id="payment_id" ng-init=formData.payment_id='<?php echo $data['payment_id'];?>' required ng-model="formData.payment_id" name="payment_id"/>
                </div>

                <div class="form-group">
                    <label for="currency_id">currency_id</label>
                    <input type="text" class="form-control" id="currency_id" ng-init=formData.currency_id='<?php echo $data['currency_id'];?>' required ng-model="formData.currency_id" name="currency_id"/>
                </div>

                <div class="form-group">
                    <label for="pay_amount">pay_amount</label>
                    <input type="text" class="form-control" id="pay_amount" ng-init=formData.pay_amount='<?php echo $data['pay_amount'];?>' required ng-model="formData.pay_amount" name="pay_amount"/>
                </div>
            <?php if(array_key_exists('charge_id', $data)) {?>
                <div class="form-group">
                    <label for="charge_id">charge_id</label>
                    <input type="text" class="form-control" id="charge_id" ng-init=formData.charge_id='<?php echo $data['charge_id'];?>' ng-model="formData.charge_id" name="charge_id"/>
                </div>
            <?php }?>
            <?php if(array_key_exists('charge_id', $data)) {?>
                <div class="form-group">
                    <label for="token_amount">token_amount</label>
                    <input type="text" class="form-control" id="token_amount" ng-init=formData.token_amount='<?php echo $data['token_amount'];?>' required ng-model="formData.token_amount" name="token_amount"/>
                </div>
            <?php }?>    
                <input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>"/>  
            </form>
        </div>
    </div>
    
    <div class="row margin-top-10">
        <div class="eb-content">
            <alert ng-repeat="alert in alerts" type="alert.type"
                close="alert.close()">{{alert.msg}}</alert>
        </div>
    </div>
</div>