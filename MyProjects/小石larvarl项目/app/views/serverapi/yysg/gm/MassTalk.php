<script>
    function godRemoveController($scope, $http, alertService, $filter) {
        $scope.alerts = [];
        $scope.formData = {};
        $scope.process = function (url) {
            alertService.alerts = $scope.alerts;
            $scope.formData.is_alertIntegral = 0;
            $http({
                'method': 'post',
                'url': url,
                'data': $.param($scope.formData),
                'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function (data) {
                var result = data.result;
                var len = result.length;
                for (var i = 0; i < len; i++) {
                    if (result[i].status == 'ok') {
                        alertService.add('success', result[i].msg);
                    } else if (result[i]['status'] == 'error') {
                        alertService.add('danger', result[i].msg);
                    }
                }
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
        };
    }
</script>
<div class="col-xs-12" ng-controller="godRemoveController">
    <div class="row" >
        <div class="form-group">
            <label>
                <input type="radio" name="name_or_id" value="1"  ng-model="formData.name_or_id" ng-init="formData.name_or_id=1"  ng-checked="true"/>
                <?php echo Lang::get('serverapi.gift_used_player_id')?>
            </label>
            <label>
                <input type="radio" ng-model="formData.name_or_id" name="name_or_id" value="2"/>
                <?php echo Lang::get('serverapi.gift_used_player_name')?>
            </label>
        </div>
        <div class="eb-content">
                <div class="form-group">
                    <textarea name="gift_data" ng-model="formData.gift_data"
                        placeholder="<?php echo Lang::get('serverapi.massTalk_enter_tip') ?>"
                        rows="15" required class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <textarea type="text" class="form-control" id="msg" name="msg"
                              placeholder="<?php echo Lang::get('serverapi.enter_message_body') ?>"
                              ng-model="formData.msg"  rows="8"></textarea>
                </div>   
        </div>
        <div class="form-group col-md-6">
            <p><font color=red size=4>昵称有特殊字符的玩家，尽量使用id发</font></p>
        </div>
        <div class="clearfix">
                <br/>
        </div>
        <div class="form-group" style="height: 40px;">
            <div class="col-md-2">
                <input type='button' class="btn btn-danger"
                       value="<?php echo Lang::get('basic.btn_send') ?>"
                       ng-click="process('/game-server-api/yysg/mass/gmTalkSend')"/>
            </div>
        </div>
        
    </div>
    <div class="row margin-top-10">
        <div class="eb-content"> 
            <alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
        </div>
    </div>
</div>