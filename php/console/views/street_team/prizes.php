<div class="yui-g">
<h1>Prizes</h1>
<p><?php echo link_for('Create a new Prize', 'street_team', 'new_prize'); ?></p><br /><br />

<?php if (count($prizes) > 0) : ?>
	<table>
		<tr>
			<th>Thumbnail</th>
			<th>Title</th>
			<th>Short Name</th>
			<th>Sponsor</th>
			<th>Initial Stock</th>
			<th>Current Stock</th>
			<th>Point Cost</th>
			<!-- <th>Category</th> -->
			<th>Status</th>
			<th>Start Date</th>
			<th>End Date</th>
			<th>Grand</th>
			<th>Weekly</th>
			<th>Featured</th>
			<th>$ Value</th>
			
			<th>Actions</th>
			
		</tr>
		<?php foreach ($prizes as $prize): ?>
		<tr>
			<td><img src="<?php echo URL_THUMBNAILS .'/'.$prize['thumbnail']; ?>" width="100" /></td>
			<td><?php echo $prize['title']; ?></td>
			<td><?php echo $prize['shortName']; ?></td>
			<td><a href="<?php echo $prize['sponsorUrl']; ?>"><?php echo $prize['sponsor']; ?></a></td>
			<td><?php echo $prize['initialStock']; ?></td>
			<td><?php echo $prize['currentStock']; ?></td>
			<td><?php echo $prize['pointCost']; ?></td>
			<!--<td><?php echo $prize['category']; ?></td>-->
			<td><?php echo $prize['status']; ?></td>
			<td><?php echo $prize['dateStart']; ?></td>
			<td><?php echo $prize['dateEnd']; ?></td>
			
			<td> <? echo $prize['isGrand']; ?></td>
			<td> <? echo $prize['isWeekly']; ?></td>
			<td> <? echo $prize['isFeatured']; ?></td>
			<td> <? echo $prize['dollarValue']; ?></td>
			
			<td>
			<?php
			$link_list = array(
				array('title' => 'View', 'ctrl' => 'street_team', 'action' => 'view_prize', 'id' => $prize['id']),
				array('title' => 'Edit', 'ctrl' => 'street_team', 'action' => 'modify_prize', 'id' => $prize['id']),
    			array('title' => 'remove', 'ctrl' => 'street_team', 'action' => 'destroy_prize', 'id' => $prize['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false")
			);
			if (($links = build_link_list($link_list))) {
				echo $links;
			}
			?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php else: ?>
<h2>Sorry no prizes currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><?php echo link_for('Create a new Prize', 'street_team', 'new_prize'); ?></p><br /><br />
</div>
<iframe src="http://rcm.amazon.com/e/cm?t=<?php echo AMAZON_ASSOCIATE_ID;?>&o=1&p=12&l=ur1&category=gift_certificates&banner=127JF9E4530CSFRCY4R2&f=ifr" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>
