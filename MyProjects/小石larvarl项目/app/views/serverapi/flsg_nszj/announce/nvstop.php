<script type="text/javascript">
function stopAnnounceController($scope, $http, alertService)
{
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
            var result = data.result;
            var len = result.length;
            for (var i=0; i < len; i++) {
                if (result[i].status == 'ok') {
                    alertService.add('success', result[i].result);
                } else if (result[i]['status'] == 'error') {
                    alertService.add('danger', result[i].result);
                }
            }
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
    $scope.lookup = function(url) {
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
            'url'    : url,
            'data'   : $.param($scope.formData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            var result = data;
            var len = result.length;
            for (var i=0; i < len; i++) {
                if (result[i].status == 'ok') {
                    alertService.add('success', result[i].result);
                } else if (result[i]['status'] == 'error') {
                    alertService.add('danger', result[i].result);
                }
            }
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
}
</script>

<div class="col-xs-12" ng-controller="stopAnnounceController">
    <div class="row">
        <div class="eb-content">
            <form action="/game-server-api/level/stop-announce-nvshen" method="post" role="form" ng-submit="processForm()" onsubmit=" return false;">
                <div class="form-group">
                    <select class="form-controller" name="server_id" id="select_game_server" ng-model="formData.server_id" ng-init="formData.server_id=0" multiple="multiple" ng-multiple="true" size=10 style="width:100%">
                        <optgroup label="<?php echo Lang::get('serverapi.select_game_server')?>">
                        <?php foreach($servers as $k => $v):?>
                            <option value="<?php echo $v->server_id?>"><?php echo $v->server_name?></option>
                        <?php endforeach?>
                        </optgroup>
                    </select>
                </div>

                <div class="form-group col-md-12" style="margin-left:-15px">
                    <input type="text" class="form-control" id="level"  placeholder="<?php echo Lang::get('serverapi.select_level')?>" required ng-model="formData.level" name="level" /> 
                </div>
                <div class="form-group" style="height: 40px;">
                    <div class="col-md-4" style="padding: 0">
                        <input type='button' class="btn btn-primary"
                            value="<?php echo Lang::get('serverapi.open_stop') ?>"
                            ng-click="processFrom('/game-server-api/level/stop-announce-nvshen?action=true')" />
                    </div>
                    <div class="col-md-4" style="padding: 0">
                        <input type='button' class="btn btn-danger"
                            value="<?php echo Lang::get('serverapi.close_stop') ?>"
                            ng-click="processFrom('/game-server-api/level/stop-announce-nvshen?action=false')" />
                    </div>
                    <div class="col-md-4" style="padding: 0">
                        <input type='button' class="btn btn-primary"
                            value="<?php echo Lang::get('serverapi.lookup_stop') ?>"
                            ng-click="lookup('/game-server-api/level/stop-announce-nvshen/look')" />
                    </div>
                </div>  
            </form>
            <p><?php echo Lang::get('serverapi.nvstop_advice'); ?><p>
        </div><!-- /.col -->
    </div>
    <div class="row margin-top-10">
        <div class="eb-content"> 
            <alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
        </div>
    </div>

</div>
