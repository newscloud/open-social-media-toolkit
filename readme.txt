READ ME - NewsCloud Social Media Toolkit
----------------------------------------
COPYRIGHT (c) 2009 NewsCloud.com

This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

Current Release Notes - DECEMBER 2009
----------------------------------------
Theis release provides the code needed for running Facebook applications similar to Hot Dish and MnDaily. 

The set up has been simplified - see /setup.txt. The documentation in in the /docs folder may also prove helpful.

NOTE: This is a complex and sophisticated infrastructure for powering social media news communities. Managing this system requires advanced system administration and Web development expertise. If you are interested in learning about NewsCloud's managed service and consulting services, please visit: http://blog.newscloud.com/services.html 

We encourage you to participate in our developer forum and knowledge base. Please submit all feedback and questions to the developer community there:
http://support.newscloud.com

You can get more information by following our blog:
http://opensource.newscloud.com

Please also follow us on twitter @newscloud: http://twitter.com/newscloud

Release Notes
---------------------------------------------
v0.31 - Facebook PHP Open Source Releases Jan 6, 2009
 * Updated documentation and sample settings to make configuration easier for more environments
 * Patch release to v0.30

v0.30 - Facebook PHP Updated Dec 2009
 * Updated to provide easier set up: /sites/default subdirectory added with sample installation files, see /setup.txt
 * New features include ideas, answers, twitter rooms and photo streams
 * Photoshop design kit provides in /docs/designKit to offer assistance with graphics

v0.25 - Facebook Release Jun 9, 2009

v0.20 - Facebook preview beta - May 11, 2009

v0.11 - Updated alpha - November 2008
	- Requires that you run init database and init cron jobs from your config page
	* RSS feeds
	* Mobile pages
	* Twitter posting
	* ReCaptcha for registration
	* Google SiteMaps
	* Ad Rotator 
	* Blog page: matrix style display with SimplePie integration
	
v0.10 - Original alpha release - October 2008

Explanation of directory layout
----------------------------------------
/docs
	- additional developer documentation 
	
/sites
	- the default subdirectory is the best place to start, sample install documents are here
	- directory of site specific code for each topic installation
	- this is the directory tree where you have to set constants can change and customize style sheets, templates

/core
	- code for the Social Media Toolkit core engine
	- generally, you won't need to modify this

/php
	- code for the alpha test version of the Social Media Toolkit php engine
	- generally, you won't need to modify this	

/facebook
	- code for the Social Media Toolkit facebook client
	- /lib has the latest PHP client for Facebook development - update here http://wiki.developers.facebook.com/index.php/PHP