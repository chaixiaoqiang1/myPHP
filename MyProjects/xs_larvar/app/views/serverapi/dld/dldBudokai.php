<script>
    function resetXJYWCController($scope, $http, alertService, $filter) {
        $scope.alerts = [];
        $scope.formData = {};
        $scope.processFrom = function (url) {
            alertService.alerts = $scope.alerts;
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
<div class="col-xs-12" ng-controller="resetXJYWCController">
    <div class="row">
        <div class="eb-content">
            <form action="" method="post" role="form"
                  onsubmit="return false;">
                <div class="form-group">
                    <select class="form-control" name="server_id"
                            id="select_game_server" ng-model="formData.servers_id"
                            ng-init="formData.servers_id=0" multiple="multiple" ng-multiple="true" size=10>
                        <option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
                        <?php foreach ($servers as $k => $v) { ?>
                            <option
                                value="<?php echo $v->server_id ?>"><?php echo $v->server_internal_id . "--" . $v->server_name; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <input type="submit" class="btn btn-primary"
                       value="<?php echo Lang::get('basic.btn_open') ?>"
                       ng-click="processFrom('/game-server-api/dld/galaxyBudokai')"/>
                <input type="submit" class="btn btn-danger"
                       value="<?php echo Lang::get('basic.btn_close') ?>"
                       ng-click="processFrom('/game-server-api/dld/galaxyBudokaiClose')"/>
            </form>
        </div>
        <!-- /.col -->
    </div>
    <div class="row margin-top-10">
        <div class="eb-content">
            <alert ng-repeat="alert in alerts" type="alert.type"
                   close="alert.close()">{{alert.msg}}
            </alert>
        </div>
    </div>

</div>