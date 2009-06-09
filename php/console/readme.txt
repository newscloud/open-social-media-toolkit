Creating a new console area
---------------------------

1) global.php
- make it aware of the database table to use for that controller
if (preg_match('/member_email/i', $action)) {
	$db = new dbConsoleModel('ContactEmails');

2) header.php
- add the menu option
<?php if (($url = url_for('stories', 'featured'))): ?>
		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Featured Stories</a></li>
<?php endif; ?>

3) views directory

a) create the list view e.g. forumtopics.php, probably should be renamed list_forumtopics.php
b) create the fields e.g. fields_forumtopic.php
c) create the modify form e.g. modify_forumtopic.php
d) create the create form e.g. new_forumtopic.php
e) create the view page e.g. view_forumtopic.php

4) Controllers directory, menu option _ controller.php e.g. members_controller.php

Add the public functions for each option

public function forumtopics() - perhaps change to list_
public function view_forumtopics()
public function new_forumtopic()
public function create_forumtopic() 
public function modify_forumtopic() 
public function update_forumtopic() 
public function destroy_forumtopic() 

5) Modify roles.php as necessary