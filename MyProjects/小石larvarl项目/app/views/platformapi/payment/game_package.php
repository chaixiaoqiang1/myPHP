<div class="col-xs-12" ng-controller="game_productcontroller">
	<div class="col-xs-10">
		<table class="table table-striped">
			<tbody>
			<?php foreach ($data as $value) {?>
					<tr class="info">
						<td style="color:darkblue"><b>ID</b></td>
						<td style="color:darkblue"><b>包名</b></td>
						<td style="color:darkblue"><b>os_type</b></td>
						<td style="color:darkblue"><b>fb</b></td>
						<td style="color:darkblue"><b>google_play</b></td>
						<td style="color:darkblue"><b>apps_flyer</b></td>
					</tr>
					<tr>
						<td><a href="/platform-api/mobilegame/game_package/modify?id=<?php echo $value['id']; ?>"><button class="btn btn-danger"><?php echo $value['id']; ?></button></a></td>
						<td><?php echo $value['package_name']; ?></td>
						<td><?php echo $value['os_type']; ?></td>
						<td><?php 
							if(is_array($value['fb'])){
								foreach ($value['fb'] as $k => $v) {
									echo $k.':'.$v."<br/>";
								} 
							}else{
								echo $value['fb'];
							}
						?></td>
						<td><?php 
							if(is_array($value['google_play'])){
								foreach ($value['google_play'] as $k => $v) {
									echo $k.':'.substr($v, 0, 8).'...'.substr($v, strlen($v)-9, strlen($v)-1)."<br/>";
								} 
							}else{
								echo $value['google_play'];
							}
						?></td>
						<td><?php 
							if(is_array($value['apps_flyer'])){
								foreach ($value['apps_flyer'] as $k => $v) {
									echo $k.':'.$v."<br/>";
								} 
							}else{
								echo $value['apps_flyer'];
							}
						?></td>
					</tr>
					<tr class="info">
						<td></td>
						<td style="color:darkblue"><b>chart_boost</b></td>
						<td style="color:darkblue"><b>adwords</b></td>
						<td style="color:darkblue"><b>gocpa</b></td>
						<td style="color:darkblue"><b>extra1</b></td>
						<td style="color:darkblue"><b>extra2</b></td>
					</tr>
					<tr>
						<td></td>
						<td><?php 
							if(is_array($value['chart_boost'])){
								foreach ($value['chart_boost'] as $k => $v) {
									echo $k.':'.$v."<br/>";
								} 
							}else{
								echo $value['chart_boost'];
							}
						?></td>
						<td><?php 
							if(is_array($value['adwords'])){
								foreach ($value['adwords'] as $k => $v) {
									echo $k.':'.$v."<br/>";
								} 
							}else{
								echo $value['adwords'];
							}
						 ?></td>
						<td><?php 							
							if(is_array($value['gocpa'])){
								foreach ($value['gocpa'] as $k => $v) {
									echo $k.':'.$v."<br/>";
								} 
							}else{
								echo $value['gocpa'];
							}
						?></td>
						<td><?php echo $value['extra1']; ?></td>
						<td><?php echo $value['extra2']; ?></td>
					</tr>
					<tr class="info">
						<td></td>
						<td style="color:darkblue"><b>sdk_ad_info</b></td>
					</tr>
					<tr>
						<td></td>
						<td><?php 
							if(is_array($value['sdk_ad_info'])){
								foreach ($value['sdk_ad_info'] as $k => $v) {
									echo $k.':'.$v."<br/>";
								} 
							}else{
								echo $value['sdk_ad_info'];
							}
						?></td>
					</tr>
				<tr></tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</div>