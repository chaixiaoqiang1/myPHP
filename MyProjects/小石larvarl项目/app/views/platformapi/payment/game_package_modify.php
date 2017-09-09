<script>
function modify_game_productController($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.modify = function() {
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
            'url'    : '/platform-api/mobilegame/game_package/modify',
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

<div class="col-xs-12" ng-controller="modify_game_productController">
    <div class="row" >
        <div class="eb-content">
            <form action="platform-api/mobilegame/game_package/modify" ng-submit="modify()" onsubmit="return false;">
                
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
                    <label for="package_name">包名</label>
                    <input type="text" class="form-control" id="package_name" ng-init="formData.package_name='<?php echo $data['package_name'] ?>'" required ng-model="formData.package_name" name="package_name"/>
                </div>

                <div class="form-group">
                    <label for="fb">fb</label>
                    <input type="text" class="form-control" id="fb" ng-init=formData.fb='<?php echo $data['fb'];?>' required ng-model="formData.fb" name="fb"/>
                </div>

                <div class="form-group">
                    <label for="google_play">google_play</label>
                    <input type="text" class="form-control" id="google_play" ng-init=formData.google_play='<?php echo $data['google_play'];?>' required ng-model="formData.google_play" name="google_play"/>
                </div>

                <div class="form-group">
                    <label for="apps_flyer">apps_flyer</label>
                    <input type="text" class="form-control" id="apps_flyer" ng-init=formData.apps_flyer='<?php echo $data['apps_flyer'];?>' required ng-model="formData.apps_flyer" name="apps_flyer"/>
                </div>

                <div class="form-group">
                    <label for="chart_boost">chart_boost</label>
                    <input type="text" class="form-control" id="chart_boost" ng-init=formData.chart_boost='<?php echo $data['chart_boost'];?>' required ng-model="formData.chart_boost" name="chart_boost"/>
                </div>

                <div class="form-group">
                    <label for="adwords">adwords</label>
                    <input type="text" class="form-control" id="adwords" ng-init=formData.adwords='<?php echo $data['adwords'];?>' required ng-model="formData.adwords" name="adwords"/>
                </div>

                <div class="form-group">
                    <label for="gocpa">gocpa</label>
                    <input type="text" class="form-control" id="gocpa" ng-init=formData.gocpa='<?php echo $data['gocpa'];?>' required ng-model="formData.gocpa" name="gocpa"/>
                </div>
                <div class="form-group">
                    <label for="os_type">os_type</label>
                    <input type="text" class="form-control" id="os_type" ng-init=formData.os_type='<?php echo $data['os_type'];?>' required ng-model="formData.os_type" name="os_type"/>
                </div>
                <div class="form-group">
                    <label for="extra1">extra1</label>
                    <input type="text" class="form-control" id="extra1" ng-init=formData.extra1='<?php echo $data['extra1'];?>' required ng-model="formData.extra1" name="extra1"/>
                </div>
                <div class="form-group">
                    <label for="extra2">extra2</label>
                    <input type="text" class="form-control" id="extra2" ng-init=formData.extra2='<?php echo $data['extra2'];?>' required ng-model="formData.extra2" name="extra2"/>
                </div>
                <div class="form-group">
                    <label for="sdk_ad_info">sdk_ad_info</label>
                    <input type="text" class="form-control" id="sdk_ad_info" ng-init=formData.sdk_ad_info='<?php echo $data['sdk_ad_info'];?>' required ng-model="formData.sdk_ad_info" name="sdk_ad_info"/>
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