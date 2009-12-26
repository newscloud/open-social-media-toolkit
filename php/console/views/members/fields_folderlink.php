Title<br />
<input type="text" name="folderlink[title]" value="<?php echo htmlentities($folderlink['title']); ?>" />
<div class="spacer"></div>

Link Address Url<br />
<input type="text" name="folderlink[url]" value="<?php echo htmlentities($folderlink['url']); ?>" />
<div class="spacer"></div>

Notes<br />
<textarea rows="4" cols="80" name="folderlink[notes]"><?php echo htmlentities($folderlink['notes']); ?></textarea>
<div class="spacer"></div>

Link Type<br />
<select name="folderlink[linkType]">
<option value="link"<? if ($folderlink['linkType'] == 'link') echo ' selected'; ?>>Default</option>
<option value="product"<? if ($folderlink['linkType'] == 'product') echo ' selected'; ?>>Amazon Product</option>
</select>
<div class="spacer"></div>

Image Address URL<br />
<input type="text" name="folderlink[imageUrl]" value="<?php echo htmlentities($folderlink['imageUrl']); ?>" />
<div class="spacer"></div>

Place in Folder<br />
<select name="folderlink[folderid]">
<option value="0"<? if ($folderlink['folderid'] == 0) echo ' selected'; ?>>Default - more coming soon</option>
</select>

<div class="spacer"></div>
