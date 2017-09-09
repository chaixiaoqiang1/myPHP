<div class="col-xs-12">
	<div class="row" >
		<div class="eb-content">
			<form action="/slave-api/compare/server/data" method="post" role="form">
				<div class="form-group">
					<select class="form-control" name="server_id[]" id="select_game_server" required multiple="multiple" size=20>
						<optgroup label="<?php echo Lang::get('serverapi.select_game_server') ?>">
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
						</optgroup>
					</select>
				</div>	
				<div class="form-group col-md-4" style="padding-left:0;">
					<input type="number" class="form-control"
						placeholder="<?php echo Lang::get('slave.days_start')?>" name="days_start"?>
				</div>
				<div class="form-group col-md-4" style="padding-left:0;">
					<input type="number" class="form-control"
						placeholder="<?php echo Lang::get('slave.days_end')?>" name="days_end"?>
				</div>
				<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>"/>	
			</form>
		</div>
	</div>
</div>