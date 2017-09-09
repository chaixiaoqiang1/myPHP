<div class="col-xs-12">
	<table class="table table-striped">
		<thead>
			<tr class="info">
				<td><?php echo Lang::get('organ.organ_id') ?></td>
				<td><?php echo Lang::get('organ.organ_name') ?></td>
				<td><?php echo Lang::get('organ.allows_ips') ?></td>
				<td><?php echo Lang::get('organ.last_updated') ?></td>
			</tr>
		</thead>
		<tbody>
	<?php foreach (Organization::paginate(20) as $k => $v) { ?>
		<tr>
				<td><?php echo $v->organization_id ?></td>
				<td><a href="organizations/<?php echo $v->organization_id?>/edit"><?php echo $v->organization_name?></a></td>
				<td><a href="organizations/<?php echo $v->organization_id?>/edit"><?php echo $v->allowed_ips?></a></td>
				<td><?php echo $v->updated_at?></td>
			</tr>
	<?php } ?>	
		</tbody>
	</table>
</div>
<?php echo Organization::paginate(20)->links();?>