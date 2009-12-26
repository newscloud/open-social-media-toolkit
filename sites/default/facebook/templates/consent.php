<?php

$static='<h1>Research Consent Form</h1>

<p>You are invited to be in a research study of about youth engagement with social media. You were selected as a possible participant because you have signed up for the '.SITE_TITLE.' application inside Facebook. I ask that you read this form and ask any questions you may have about agreeing to be in the study.</p>

<h2>Background Information</h2>

<p>This study aims to understand what engages youth in social media. This study also aims to understand how social network sites may play a role in community-formation around important issues and stimulate real world impact. Our findings may be used to inform the design of innovative learning materials and media-rich environments.</p> 

<h2>Procedures:</h2>

<p>If you agree to be in this study, we would ask you to do the following things:</p><div class="bullet_list"><ul>

<li>Allow the researcher to track and analyze all online activities within the online learning environment</li> 
<li>Be asked to take survey(s)</li>
<li>Be asked to participate in a 45 min focus group.</li></ul><!-- end bullet list --></div>

<h2>Risks and Benefits of being in the Study</h2>

<p>The study has a minor, foreseeable risk: you may worry about others recognizing you. All personally identifying information will be deleted and changed to appropriate pseudonyms (e.g., your name, your school district, names of students mentioned, etc).</p>

<p>There is one potential benefit; those students who are invited to use a new social media publication may feel a greater sense of belonging, and overall increased sense of connectedness to peers around University of Minnesota news and issues, if the environment proves effective. </p>

<h2>Confidentiality:</h2>

<p>The records of this study will be kept private. In any sort of report we might publish, we will not include any information that will make it possible to identify a subject. Research records will be stored securely and only the researcher will have access to the records. All participants will be assigned a pseudonym and all data will be changed to reflect the pseudonym name.</p>

<h2>Contacts and Questions:</h2>

<!-- <p>If you decide to sign up, <a href="?p=contact" onclick="switchPage(\'contact\');">contact us</a> afterwards and we\'ll give you an additional 50 bonus points.</p>--> <br />';

$category='consent';
$static = $dynTemp->useDBTemplate('ResearchConsentForm',$static,'',false, $category);
?>