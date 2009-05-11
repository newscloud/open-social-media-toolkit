<div class="yui-g">
<h1>CRON Jobs</h1>
<p><a href="index.php?p=console&group=admin&action=cronJobs">Create a new cron job</a> | <a href="index.php?p=console&group=admin&action=initcronjobs">Initialize cron jobs</a></p><br /><br />

<?php if (count($cronJobs) > 0) : ?>
	<table>
		<tr>
			<th>Task</th>
			<th>Comments</th>
			<th>Status</th>
			<th>Frequency</th>
			<th>Last Start</th>
			<th>Next Run</th>
		</tr>
		<?php foreach ($cronJobs as $job): ?>
		<tr>
			<td><a href="index.php?p=console&group=admin&action=cronJobs&id=<?php echo $job['id']; ?>"><?php echo $job['task']; ?></a></td>
			<td><?php echo $job['comments']; ?></td>
			<td><?php echo $job['status']; ?></td>
			<td><?php echo $job['freqMinutes']; ?></td>
			<td><?php echo $job['lastStart']; //date('M n, Y \a\t g:i:s a',$job['lastStart']); ?></td>
			<td><?php echo $job['nextRun']; //date('M n, Y \a\t g:i:s a',$job['nextRun']) ?></td>
			<td>
				<a href="index.php?p=console&group=admin&action=run_cronjob&task=<? echo $job['task']; ?>">Run</a> --
				<a href="index.php?p=console&group=admin&action=view_cronJobs&id=<? echo $job['id']; ?>">View</a> -- 
				<a href="index.php?p=console&group=admin&action=modify_cronJobs&id=<? echo $job['id']; ?>">Edit</a> -- 
    			<a href="index.php?p=console&group=admin&action=destroy_cronJobs&id=<?php echo $job['id'] ?>" onclick="if(!confirm('Are you sure you want to remove this item?')) return false">remove</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php else: ?>
<h2>Sorry no jobs currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><a href="index.php?p=console&group=admin&action=new_job">Create a new Job</a></p>
</div>
