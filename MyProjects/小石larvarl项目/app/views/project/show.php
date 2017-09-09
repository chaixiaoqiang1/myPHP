
<div class="col-xs-12">
	<table class="table table-striped">
		<thead>
			<tr class="info">
				<td><b><?php echo Lang::get('project.project_id') ?></b></td>
				<td><b><?php echo Lang::get('project.project_name') ?></b></td>
				<td><b><?php echo Lang::get('project.current_version') ?></b></td>
				<td><b><?php echo Lang::get('project.last_release_user') ?></b></td>
				<td><b><?php echo Lang::get('project.last_release_record') ?></b></td>
				<td><b><?php echo Lang::get('project.last_release_time') ?></b></td>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($project_logs as $k => $v) { ?>
		<tr>
				<td><a href="/project"><?php echo $v->project_id?></a></td>
				<td><a href="/project"><?php echo Project::where('project_id', '=', $v->project_id)->pluck('project_name');?></a></td>
				<td><?php echo $v->current_version ?></td>
				<td><?php echo $v->last_release_user ?></td>
				<td><?php echo $v->last_release_record ?></td>
				<td><?php echo $v->last_release_time ?></td>
			</tr>
	<?php } ?>	
		</tbody>
	</table>
</div>
