<?php
	/* FACEBOOK Module Templates for Newswire Page aka Choose Stories*/
	$category = 'forum';
	$this->addTemplateDynamic($dynTemp, 'forumTopicsList','<h5>Forum Topics</h5>
            <p class="bold">Join the discussion. Just click a topic below to sound off!</p><br />
			<div class="pointsTable">
                  <table cellspacing="0">
                    <thead><tr><th>Topic</th><th>Last Updated</th><th>Recent Posts</th><th>Recent Views</th></tr></thead><tbody>{items}
                    </tbody>
                  </table>
			</div><!--end "pointsTable"-->','',$category); // <th>New</th>
	$this->addTemplateDynamic($dynTemp, 'forumTopicsListItem', '<tr><td class="pointValue"><a href="?p=wall&topic={id}&title={title}" >{title}</a></td><td >{lastChanged} ago</td><td>{numPostsToday}</td><td>{numViewsToday}</td></tr>','',$category);

?>