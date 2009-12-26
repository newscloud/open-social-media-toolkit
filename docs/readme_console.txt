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

6) Adding Dynamic Data to Views 

Fetch the data from the database in the controller action, you want to have all of your business logic in there. Build up the array of key => values for the option strings and then set that variable using $this->set('options', $options) so that it is available in the view.

Then you want to do
<select id="foo">
  <? foreach ($options as $id => $value): ?>
    <option value="<? echo $id; ?>"><? echo $value; ?></option>
  <? endforeach; ?>
</select>

You only want to do looping and output operations in the views. If you don't want to clutter your controller files or your view files or you have a repeated display action, you can add a function to the helper/application_helper.php file which is a place to store functions that take data and output view code. 

So for instance to turn that into a helper, you would add this function to the application_helper file:
function build_options_list($options) {
  $output = '';
  foreach ($options as $id => $value)
    $output .= "<option value=\"$id\">$value</option>";
  return $output;
}

and your view file would look like:

<select id="foo">
  <? echo build_options_list($options); ?>
</select>

Ideally you want to keep any code that talks to the database and builds data sets in the controller, and then repeated or larger functions to output dynamic html should be in the helpers, and strictly outputting html and data in the views.