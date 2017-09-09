<div class="col-xs-12" ng-controller="addWordFilterController">
	<div class="row" >
		<div class="eb-content">
			<form action="/game-server-api/word-filter" method="post" role="form" ng-submit="processFrom('/game-server-api/word-filter')" onsubmit="return false;">

				<div class="form-group">
					<textarea rows="5" class="form-control" placeholder="<?php echo Lang::get('serverapi.enter_word_filter')?>" ng-model="formData.words" name="words"?></textarea>
				</div>	
				<div class="form-group">
					<label><input type="checkbox" name="is_delete" value="true" ng-init="formData.is_delete=false" ng-model="formData.is_delete"><?php echo Lang::get('serverapi.is_delete') ?></label>	
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
	<div class="row margin-top-10">
		<div class="col-xs-12"> 
			<table class="table table-striped">
				<thead>
					<tr class="info">
						<td><?php echo Lang::get('serverapi.filter_word')?></td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="v in words">
						<td>{{v.word}}</td>
					</tr>
					
					<?php foreach($words as $v) { ?>
					<tr ng-if='t != 0'>
						<td><?php echo $v->word ?></td>
					</tr>
					<?php } ?>
				</body>
			</table>
		</div>
	</div>
</div>