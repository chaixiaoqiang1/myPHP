<script>
    function ServerController($scope, $http, alertService, $filter) {
        $scope.alerts = [];
        $scope.formData = {};
        $scope.process = function (url) {
            $scope.alerts = [];
            alertService.alerts = $scope.alerts;
            $http({
                'method': 'post',
                'url': '/game-server-api/mnsg/update_new',
                'data': $.param($scope.formData),
                'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function (data) {
                alertService.add('success', data.msg);
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
        };

    }
</script>
<div class="col-xs-12" ng-controller="ServerController">
    <div class="row">
        <div class="eb-content">
            <div  class="form-group">
                <input type="text" name="title" class="form-control"
                        id="title" ng-model="formData.title" required placeholder="<?php echo Lang::get('basic.title'); ?>" style="width:300px" />
            </div>
            <div  class="form-group">
                <input type="text" name="version" required class="form-control"
                        id="version" ng-model="formData.version" placeholder="<?php echo Lang::get('basic.version'); ?>" style="width:300px" />
                <b>Note:<?php echo Lang::get('serverapi.only_show_the_largest_one'); ?></b>
            </div>
            <div class="form-group">
                <input type="text" name="title1" class="form-control"
                            id="title1" ng-model="formData.title1" required placeholder="<?php echo Lang::get('basic.title'); ?>" style="width:300px" />
                <textarea style="width:85%;height:200px" ng-model = "formData.content1" placeholder="<?php echo Lang::get('basic.input_devide_line'); ?>" required>
                </textarea>    
            </div>
            <div  class="form-group">
                <input type="text" name="title2" class="form-control"
                            id="title2" ng-model="formData.title2" required placeholder="<?php echo Lang::get('basic.title'); ?>" style="width:300px" />
                <textarea style="width:85%;height:200px" ng-model = "formData.content2" placeholder="<?php echo Lang::get('basic.input_devide_line'); ?>" required>
                </textarea>    
            </div>
            <div  class="form-group">
                <input type="text" name="title3" class="form-control"
                            id="title3" ng-model="formData.title3" required placeholder="<?php echo Lang::get('basic.title'); ?>" style="width:300px" />
                <textarea style="width:85%;height:200px" ng-model = "formData.content3" placeholder="<?php echo Lang::get('basic.input_devide_line'); ?>" required>
                </textarea>    
            </div>
            <div  class="form-group">
                <input type="text" name="title4" class="form-control"
                            id="title4" ng-model="formData.title4" required placeholder="<?php echo Lang::get('basic.title'); ?>" style="width:300px" />
                <textarea style="width:85%;height:200px" ng-model = "formData.content4" placeholder="<?php echo Lang::get('basic.input_devide_line'); ?>" required>
                </textarea>    
            </div>
            <div class="clearfix">
                <br/>
            </div>
            <div class="form-group" style="height: 40px;">
                <div class="col-md-2" style="padding: 0">
                    <input type='button' class="btn btn-primary" value="<?php echo Lang::get('basic.btn_submit'); ?>" ng-click="process()"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row margin-top-10">
        <div class="eb-content">
            <alert ng-repeat="alert in alerts" type="alert.type"
                   close="alert.close()">{{alert.msg}}
            </alert>
        </div>
    </div>
</div>