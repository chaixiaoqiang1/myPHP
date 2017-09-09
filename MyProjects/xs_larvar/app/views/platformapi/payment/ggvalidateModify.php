<script>
function modifyController($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.modify = function() {
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
            'url'    : '/platform-api/ggvalidate/modify',
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
            <form action="/platform-api/ggvalidate/modify" ng-submit="modify()" onsubmit="return false;">
                
                <?php 
                $url = explode('/', Request::url());
                $url = $url[count($url)-1];
                
                if('modify' == $url){ ?>
                    <div class="form-group">
                        <label for="id">ID</label>
                        <input type="text" class="form-control" id="id" ng-init="formData.id='<?php echo $data['id'] ?>'" required disabled="true" ng-model="formData.id" name="id"/>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <label for="package_name">package_name</label>
                    <input type="text" class="form-control" id="package_name" ng-init=formData.package_name='<?php echo $data['package_name'];?>' required ng-model="formData.package_name" name="package_name"/>
                </div>

                <div class="form-group">
                    <label for="refresh_token">refresh_token</label>
                    <input type="text" class="form-control" id="refresh_token" ng-init=formData.refresh_token='<?php echo $data['refresh_token'];?>' required ng-model="formData.refresh_token" name="refresh_token"/>
                </div>

                <div class="form-group">
                    <label for="client_id">client_id</label>
                    <input type="text" class="form-control" id="client_id" ng-init=formData.client_id='<?php echo $data['client_id'];?>' required ng-model="formData.client_id" name="client_id"/>
                </div>

                <div class="form-group">
                    <label for="client_secret">client_secret</label>
                    <input type="text" class="form-control" id="client_secret" ng-init=formData.client_secret='<?php echo $data['client_secret'];?>' required ng-model="formData.client_secret" name="client_secret"/>
                </div>
                
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