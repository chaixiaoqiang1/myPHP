<script>
function HelperSingleFunctionController($scope, $http, alertService, $filter, $modal) {
    $scope.alerts = [];

    $scope.answer = function(id) {
        var modalInstance = $modal.open({
            templateUrl: 'answer.html',
            controller: HelperAnswerController,
            resolve: {
                id : function () {
                    return id;
                },
            },
            backdrop : false,
            keyboard : false
        });
        modalInstance.result.then(function() {
            alert('Answer success'); 
            location.reload(true);  
        });
    }
}

function HelperAnswerController($scope, $modalInstance, id, $http, alertService) {
    $scope.answerinit = {};
    $scope.answerinit.id = id;
    $scope.AnswerData = {};

    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    }
    $scope.ModifyForm= function(url) {
        $scope.AnswerData.type = 1;
        $scope.AnswerData.function_id = <?php echo $id; ?>;
        $http({
            'method' : 'post',
            'url' : url,
            'data' : $.param($scope.AnswerData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            $modalInstance.close();
        }).error(function(data) {
            alert('error: ' + data.error_description + '\n');
        });
    }
}

</script>
<div class="col-xs-12" ng-controller="HelperSingleFunctionController">
    <table class="table table-striped">
        <thead>
            <tr class="info">
                <td><b><?php echo Lang::get('platformapi.time') ?></b></td>
                <td><b><?php echo Lang::get('platformapi.user_uid') ?></b></td>
                <td><b><?php echo Lang::get('platformapi.server_track_name') ?></b></td>
                <td><b><?php echo Lang::get('platformapi.player_id') ?></b></td>
                <td><b><?php echo Lang::get('platformapi.question') ?></b></td>
                <td><b></b></td>
            </tr>
        </thead>
        <tbody> 
            <?php foreach ($view_data as $value) {
                $server = Server::where('game_id', Session::get('game_id'))->where('server_internal_id', $value->server_id)->first();
                $server_track_name = isset($server->server_track_name) ? $server->server_track_name : 'server_internal_id:'.$value->server_id;
                unset($server);
             ?>
                <tr>
                    <td><?php echo date('Y-m-d H:i:s',$value->question_time); ?></td>
                    <td><?php echo $value->uid; ?></td>
                    <td><?php echo $server_track_name; ?></td>
                    <td><?php echo $value->player_id; ?></td>
                    <td><?php echo $value->question_content; ?></td>
                    <td><button class="btn btn-primary" ng-click="answer(<?php echo $value->id;?>)"><?php echo Lang::get('platformapi.answer'); ?></button></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <div class="row margin-top-10">
        <div class="eb-content">
            <alert ng-repeat="alert in alerts" type="alert.type"
                close="alert.close()">{{alert.msg}}</alert>
        </div>
    </div>
</div>

<script type="text/ng-template" id="answer.html">
        <div class="modal-header">
        </div>
        <form action="/project/release" method="post" role="form" ng-submit="ModifyForm('/platform-api/mobilegame/helper/single_function')" onsubmit="return false;">
        <div class="modal-body">
            <div class="form-group">
                <label><?php echo Lang::get('platformapi.question_id') ?>:</label>    
                <input type="number" class="form-control" readonly required ng-model="AnswerData.id" ng-init="AnswerData.id=answerinit.id"/>
            </div>
            <div class="form-group">
                <label><?php echo Lang::get('platformapi.answer') ?>:</label>
                <textarea type="text" style="height:120px;" class="form-control" required ng-model="AnswerData.answer" /></textarea>
            </div>
        </div>
        <div class="modal-footer" style="text-align:center;">
            <button class="btn btn-primary"><?php echo Lang::get('platformapi.submit')?></button>
            <a class="btn btn-warning" ng-click="cancel()"><?php echo Lang::get('platformapi.cancel')?></a>
        </div>
        </form>
</script>