<div class="yui-g">
<h1>CRON Jobs</h1>
<p><?php echo link_for('Create a new cron job', 'admin', 'new_cronjob'); ?> | <?php echo link_for('Initialize cron jobs', 'admin', 'initcronjobs'); ?></p><br /><br />

<?php if (count($cronJobs) > 0) : ?>
	<table>
		<tr>
			<th>Task</th>
			<th>Comments</th>
			<th>Status</th>
			<th>Frequency</th>
			<th>Last Start</th>
			<th>Next Run</th>
			<th>Actions</th>
		</tr>
		<?php foreach ($cronJobs as $job): ?>
		<tr>
			<td><?php echo $job['task']; ?></td>
			<td><?php echo $job['comments']; ?></td>
			<td><?php echo $job['status']; ?></td>
			<td><?php echo $job['freqMinutes']; ?></td>
			<td><?php echo $job['lastStart']; //date('M n, Y \a\t g:i:s a',$job['lastStart']); ?></td>
			<td><?php echo $job['nextRun']; //date('M n, Y \a\t g:i:s a',$job['nextRun']) ?></td>
			<td>
			<?php
			$link_list = array(
				array('title' => 'Run', 'ctrl' => 'admin', 'action' => 'run_cronjob', 'id' => $job['id'], 'extra_params' => array('task' => $job['task'])),
				array('title' => 'View', 'ctrl' => 'admin', 'action' => 'view_cronjobs', 'id' => $job['id']),
				array('title' => 'Edit', 'ctrl' => 'admin', 'action' => 'modify_cronjobs', 'id' => $job['id']),
    			array('title' => 'remove', 'ctrl' => 'admin', 'action' => 'destroy_cronjobs', 'id' => $job['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false")
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
<h2>Sorry no jobs currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><?php echo link_for('Create a new cron job', 'admin', 'new_cronjob'); ?></p>
</div>
