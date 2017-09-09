
<script type="text/javascript">
    function AmountController($scope, $http, alertService){
        $scope.alerts = [];
        $scope.formData = {};
        $scope.processFrom = function(url) {
            alertService.alerts = $scope.alerts;
            $http({
                'method' : 'post',
                'url'    : url,
                'data'   : $.param($scope.formData),
                'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
            }).success(function(data) {
                for(var result in data){
                    // alert(data[result].res);
                    if (data[result].res) {
                        alertService.add('success',data[result].msg);
                    }else{
                        alertService.add('danger','error');
                    }
                } 
            }).error(function(data) {
                alertService.add('danger', data);
            });
        };
    }
    
</script>

<div class="col-xs-12" ng-controller="AmountController">
    <div class="row" >
        <div class="eb-content">
            <form action="/pay-amount" method="post" role="form" ng-submit="processFrom('/pay-amount/create')" onsubmit="return false;">
                <div class="form-group">
                    <textarea class="form-control" id="amount" placeholder="<?php echo Lang::get('amount.pay_amount_format')?>"  ng-model="formData.amount" name="amount" rows="8"></textarea>
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