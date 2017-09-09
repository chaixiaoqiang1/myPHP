<script>
function welfareAnnouceController($scope, $http, alertService)
{
	$scope.alerts = [];
	$scope.formData = {};
	$scope.list = [{content:'content0',url:'url0',font_leading:'font_leading0',font_size:'font_size0',font_color:'font_color0'}];
	$scope.version_list = [{version_content:'version_content0',version_url:'version_url0',version_font_leading:'version_font_leading0',version_font_size:'version_font_size0',version_font_color:'version_font_color0'}];
	$scope.formData.content = [];
	$scope.formData.url = [];
	$scope.formData.font_leading = [];
	$scope.formData.font_size = [];
	$scope.formData.font_color = [];
	$scope.formData.version_content = [];
	$scope.formData.version_url = [];
	$scope.formData.version_font_leading = [];
	$scope.formData.version_font_size = [];
	$scope.formData.version_font_color = [];
	$scope.process = function(url) {
		var len = $scope.list.length;
		for(var j=0; j < len; j++){
			$scope.formData.content[j] = document.getElementById('content'+j).value;
			$scope.formData.url[j] = document.getElementById('url'+j).value;
			$scope.formData.font_leading[j] = document.getElementById('font_leading'+j).value;
			$scope.formData.font_size[j] = document.getElementById('font_size'+j).value;
			$scope.formData.font_color[j] = document.getElementById('font_color'+j).value;
		}
		var len = $scope.version_list.length;
		for(var j=0; j < len; j++){
			$scope.formData.version_content[j] = document.getElementById('version_content'+j).value;
			$scope.formData.version_url[j] = document.getElementById('version_url'+j).value;
			$scope.formData.version_font_leading[j] = document.getElementById('version_font_leading'+j).value;
			$scope.formData.version_font_size[j] = document.getElementById('version_font_size'+j).value;
			$scope.formData.version_font_color[j] = document.getElementById('version_font_color'+j).value;
		}
		$scope.formData.is_look = 0;
		alertService.alerts = $scope.alerts;
		$http({
			'method' : 'post',
			'url'	 : url,
			'data'   : $.param($scope.formData),
			'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).success(function(data) {
			var result = data.result;
			var len = result.length;
			for (var i=0; i < len; i++) {
				if (result[i].status == 'ok') {
					alertService.add('success', result[i].msg);
				} else if (result[i]['status'] == 'error') {
            		alertService.add('danger', result[i].msg);
				}
			}
		}).error(function(data) {
			alertService.add('danger', data.error);
		});
	};
	$scope.lookup = function(url) {
		$scope.formData.is_look = 1;
		alertService.alerts = $scope.alerts;
		$http({
			'method' : 'post',
			'url'	 : url,
			'data'   : $.param($scope.formData),
			'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).success(function(data) {
			var result = data.result;
			var len = result.length;
			for (var i=0; i < len; i++) {
				if (result[i].status == 'ok') {
					alertService.add('success', result[i].msg);
				} else if (result[i]['status'] == 'error') {
            		alertService.add('danger', result[i].msg);
				}
			}
		}).error(function(data) {
			alertService.add('danger', data.error);
		});
	};
	$scope.add=function(){
		var len = $scope.list.length;
		var obj={content:'content'+len,url:'url'+len,font_leading:'font_leading'+len,font_size:'font_size'+len,font_color:'font_color'+len};
    	$scope.list.push(obj);
	};
	$scope.version_add=function(){
		var len = $scope.version_list.length;
		var obj={version_content:'version_content'+len,version_url:'version_url'+len,version_font_leading:'version_font_leading'+len,version_font_size:'version_font_size'+len,version_font_color:'version_font_color'+len};
    	$scope.version_list.push(obj);
	};
}
</script>
<div class="col-xs-12" ng-controller="welfareAnnouceController">
	<div class="row" >
		<div class="eb-content">
			<div class="form-group">
                <select class="form-control" name="server_id"
                        id="select_game_server" ng-model="formData.server_id"
        				multiple="multiple" ng-multiple="true" size=10>
                    <optgroup
                        label="<?php echo Lang::get('serverapi.select_game_server') ?>(按住Ctrl可多选)">
                        <?php foreach ($servers as $k => $v) { ?>
                            <option value="<?php echo $v->server_id ?>"><?php echo $v->server_name; ?></option>
                        <?php } ?>
                    </optgroup>
                </select>
            </div>	
            <div class="form-group">
            	<select class="form-control" name="announce_type" ng-model="formData.announce_type" ng-init="formData.announce_type=0">
            	    <option value="0">设置/修改活动公告和版本公告</option>
            		<option value="1">只设置/修改活动公告(保留原版本公告)</option>
            		<option value="2">只设置/修改版本公告(保留原活动公告)</option>
            	</select>
            </div>	
			<div style="padding-top: 20px;">
				<p><font color="red" size="4"><?php echo Lang::get('serverapi.welfare_annouce_tip3') ?></font></p>
				<p><font size="3"><?php echo Lang::get('serverapi.welfare_annouce_tip1') ?></font></p></div>
            <div ng-repeat="d in list">
            	<div class="form-group col-md-4" style="padding-left: 0;">
            		<select class="form-control" name={{d.font_leading}} id={{d.font_leading}} >
            		    <option value="12"><?php echo Lang::get('serverapi.font_leading') ?></option>
            			<option value="8">8</option>
            			<option value="10">10</option>
            			<option value="14">10</option>
            			<option value="16">10</option>	
            		</select>
            	</div>
            	<div class="form-group col-md-4">
            		<select class="form-control" name={{d.font_size}} id={{d.font_size}}>
            			<option value="0"><?php echo Lang::get('serverapi.font_size') ?></option>
            			<option value="12">12px</option>
            			<option value="14">14px</option>
            			<option value="16">16px</option>
            			<option value="18">18px</option>
            			<option value="20">20px</option>	
            		</select>
            	</div>
            	<div class="form-group col-md-4">
            		<select class="form-control" name="font_color" name={{d.font_color}} id={{d.font_color}}>
            			<option value="0"><?php echo Lang::get('serverapi.font_color') ?></option>
            			<option value="1">red</option>
            			<option value="2">blue</option>	
            		</select>
            	</div>
    			<div class="form-group">
    				<textarea type="text" class="form-control" placeholder="<?php echo Lang::get('serverapi.enter_announce_welfare') ?>" 
    				 name={{d.content}} id={{d.content}} rows="3"></textarea> 
    			</div>
    			<div class="form-group col-md-9" style="padding: 0">
	                <input type='text' class="form-control" name={{d.url}} id={{d.url}}
	                       placeholder="<?php echo Lang::get('serverapi.enter_url') ?>"/>
	            </div>	
            </div>
            <div class="col-md-3" style="padding: 0">
                <input type='button' class="btn btn-primary"
                       value="<?php echo Lang::get('basic.add_url') ?>"
                       ng-click="add()"/>
            </div>
            <div style="padding-top: 50px;">
            	<p><font size="3"><?php echo Lang::get('serverapi.welfare_annouce_tip2') ?></font></p>
            </div>
            <div ng-repeat="v in version_list">
            	<div class="form-group col-md-4" style="padding-left: 0">
            		<select class="form-control" name={{v.version_font_leading}} id={{v.version_font_leading}}>
            			<option value="12"><?php echo Lang::get('serverapi.font_leading') ?></option>
            			<option value="8">8</option>
            			<option value="10">10</option>
            			<option value="14">10</option>
            			<option value="16">10</option>
            		</select>
            	</div>
            	<div class="form-group col-md-4">
            		<select class="form-control" name={{v.version_font_size}} id={{v.version_font_size}}>
            			<option value="0"><?php echo Lang::get('serverapi.font_size') ?></option>
            			<option value="12">12px</option>
            			<option value="14">14px</option>
            			<option value="16">16px</option>
            			<option value="18">18px</option>
            			<option value="20">20px</option>	
            		</select>
            	</div>
            	<div class="form-group col-md-4">
            		<select class="form-control" name={{v.version_font_color}} id={{v.version_font_color}}>
            			<option value="0"><?php echo Lang::get('serverapi.font_color') ?></option>
            			<option value="1">red</option>
            			<option value="2">blue</option>	
            		</select>
            	</div>
				<div class="form-group">
					<textarea type="text" class="form-control" placeholder="<?php echo Lang::get('serverapi.version_explain') ?>" 
						name={{v.version_content}} id={{v.version_content}} rows="3"></textarea> 
				</div>
    			<div class="form-group col-md-9" style="padding: 0">
	                <input type='text' class="form-control" name={{v.version_url}} id={{v.version_url}}
	                       placeholder="<?php echo Lang::get('serverapi.enter_url') ?>"/>
	            </div>
			</div>
			<div class="col-md-3" style="padding: 0">
                <input type='button' class="btn btn-primary"
                       value="<?php echo Lang::get('basic.add_url') ?>"
                       ng-click="version_add()"/>
            </div>
			<div class="form-group" style="height: 40px;">
                <div class="col-md-4" style="padding: 0">
                    <input type='button' class="btn btn-warning"
                           value="<?php echo Lang::get('basic.btn_set').'/'.Lang::get('basic.update') ?>"
                           ng-click="process('/game-server-api/announce/welfare')"/>
                </div>
                <div class="col-md-4" style="padding: 0">
                    <input type='button' class="btn btn-primary"
                           value="<?php echo Lang::get('basic.btn_show') ?>"
                           ng-click="lookup('/game-server-api/announce/welfare')"/>
                </div>
			</div>	 
		</div><!-- /.col -->
	</div>
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

</div>