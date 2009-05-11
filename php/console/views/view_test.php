<div class="yui-g">
<div class="test">
<h2>Test Details</h2>
<p id="test[name]">Name: <?php echo htmlentities($test['name']); ?></p>
<div class="spacer"></div>

<p id="test[foo]">Foo: <?php echo htmlentities($test['foo']); ?></p>
<div class="spacer"></div>

<p id="test[bar]">Bar: <?php echo htmlentities($test['bar']); ?></p>
<div class="spacer"></div>

<p id="test[asdf]">Asdf: <?php echo htmlentities($test['asdf']); ?></p>
<div class="spacer"></div>

<br /><br />
<p>Actions:</p>
<ul>
	<li><a href="index.php?p=console&group=main&action=new_test">Create a new test</a></li>
	<li><a href="index.php?p=console&group=main&action=modify_test">Edit this test</a></li>
	<li><a href="index.php?p=console&group=main&action=destroy_test">Delete this test</a></li>
</ul>
</div>
