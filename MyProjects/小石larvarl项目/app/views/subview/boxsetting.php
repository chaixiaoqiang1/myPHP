<?php if (Auth::user()->is_admin) { ?>
<!-- Box (with bar chart) -->
<div class="box box-danger" id="loading-example">
	<div class="box-header">
		<!-- tools box -->
		<div class="pull-right box-tools">

		</div><!-- /. tools -->
		<i class="fa fa-cloud"></i>

		<h3 class="box-title"><?php echo Lang::get('system.setting') ?></h3>
	</div><!-- /.box-header -->
	<div class="box-body no-padding">
		<div class="row">
			<div class="col-sm-7">
				<!-- bar chart -->
				<div class="chart" id="bar-chart" style="height: 250px;">
				</div>
			</div>
			<div class="col-sm-5">
				<div class="pad">
				</div><!-- /.pad -->
			</div><!-- /.col -->
		</div><!-- /.row - inside box -->
	</div><!-- /.box-body -->
	<div class="box-footer">
		<div class="row">

		</div><!-- /.row -->
	</div><!-- /.box-footer -->
</div><!-- /.box -->        
<?php } ?>