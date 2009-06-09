Currently indexes need to be manually added to your tables.
Here are some of the indexes:

RawSessions index
	SQL: CREATE UNIQUE INDEX session_id ON RawSessions (userid, siteid, t);
RawExtLinks index
	SQL: CREATE UNIQUE INDEX extlink_id ON RawExtLinks (userid, siteid, itemid, t);
User collective index
	SQL: CREATE UNIQUE INDEX user_site_id ON UserCollectives (userid, siteid);
Session Lengths index
	SQL: CREATE UNIQUE INDEX user_site_date ON SessionLengths (userid, siteid, start_session);
LogDumps index
	SQL: CREATE UNIQUE INDEX log_dump_id ON LogDumps (userid1, itemid, siteid, t);
SurveyMonkey index
	SQL: CREATE UNIQUE INDEX site_user_id ON SurveyMonkeys (siteid, userid);
