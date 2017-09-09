
<div class="col-xs-12" ng-controller="AdLinkController">
	<div class="row">
		<div class="eb-content">
			<form action="/ad/link" method="post" role="form" ng-submit="processFrom('/ad/link')" onsubmit="return false;">
		        <div class="form-group">
					<select name="game" ng-model="formData.game" ng-init="formData.game=0" id="game" class="form-control" ng-change="getSource()">
						<option value="0"><?php echo Lang::get('campaigns.enter_game_name')?></option>
						<?php foreach(Game::all() as $key  => $v){?>
							<option value="<?php echo $v->game_id?>"><?php echo $v->game_name?></option>
							<?php
						}
							?>	
					</select>
		        </div>
				<div class="form-group">
					<select name="source" ng-model="formData.source"  id="source" class="form-control" ng-change="getCampaign()">
						<option value=""><?php echo Lang::get('campaigns.enter_game_name')?></option>
						<option ng-repeat="source in source" value="{{source.source_id}}">{{source.source_name}}</option>
					</select>
				
				</div>
				<div class="form-group" >
					<select name="campaign" id="campaign" ng-model="formData.campaign"   class="form-control" ng-change="getTerm()">
						<option value=""><?php echo Lang::get('campaigns.select_campaign')?></option>
						<option ng-repeat="campaign in campaign" value="{{campaign.campaign_id}}">{{campaign.campaign_name}}</option>
					</select>
				</div>
				<div class="form-group" >
					<select name="term" id="term" ng-model="formData.term" id="term" class="form-control" >
						<option value=""><?php echo Lang::get('terms.select_term')?></option>
						<option ng-repeat="term in term" value="{{term.term_id}}">{{term.term_value}}</option>
					</select>
				</div>
               
               
               
				<div class="form-group">
                    <input  type="text" name="lp" id="lp" ng-model="formData.lp" class="form-control"/>
				</div>
				<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>"/>	
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>