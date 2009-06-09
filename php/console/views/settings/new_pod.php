<h1>Create a new pod</h1>

<form method="post" action="<? echo url_for('settings', 'create_pod'); ?>">
Header<br />
<input type="text" name="podform[header]" value="<?php echo htmlentities($pod['header']); ?>" />
<div class="spacer"></div>

Body<br />
<input type="text" name="podform[body]" value="<?php echo htmlentities($pod['body']); ?>" />
<div class="spacer"></div>

Footer<br />
<input type="text" name="podform[footer]" value="<?php echo htmlentities($pod['footer']); ?>" />
<div class="spacer"></div>

Name<br />
<input type="text" name="podform[name]" value="<?php echo htmlentities($pod['name']); ?>" />
<div class="spacer"></div>

Pod Filename<br />
<input type="text" name="podform[podfile]" value="<?php echo htmlentities($pod['podfile']); ?>" />
<div class="spacer"></div>

Model<br />
<select name="podform[model]">
<option value="none">None</option>
<?php foreach ($models as $model): ?>
<option value="<? echo $model['name']; ?>"><?echo $model['name']; ?></option>
<?php endforeach; ?>
</select>
<div class="spacer"></div>

Type<br />
<select name="podform[type]">
<option value="default">Default</option>
<option value="dataTable">Data Table</option>
<option value="ajax">Ajax</option>
<option value="chart-pie">Chart (Pie)</option>
<option value="chart-line">Chart (Line)</option>
<option value="chart-bar">Chart (Bar)</option>
</select>
<div class="spacer"></div>
<br /><br />

<input type="submit" value="Create Pod">
</form>
