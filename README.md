# orc-options
### Orchard Recovery Center Options Plugin

* Contributors: [martinwedepohl](https://en.gravatar.com/martinwedepohl) 
* Tags: options, orchard recovery center, wedepohl engineering
* Requires at least: 4.7 or higher
* Tested up to: 6.1.1
* Stable tag: 3.0.0
* License: [GPLv3 or later](https://www.gnu.org/licenses/gpl-3.0.html)

### Description
Orchard Recovery Center Options Plugin is used to provide a set of options, custom post types, templates and shortcodes used for the [Orchard Recovery Center Website](https://orchardrecovery.com).

### Privacy Notices
This plugin doesn't store any user data

### Installation For Development
- Download/clone the plugin to a development directory on the website at the root level (same level as the /wp-content directory)
- From the command line in the newly created directory issue the following commands
```
npm install
composer install
gulp watch
```
- This will copy any changes made in the development directory to the /wp-content/plugins/orc-options directory
- The plugin can be activated through the plugins page
- If any new classes are added to the development area issue the following command
```
composer dump
```

### Frequently Asked Questions

### Screenshots

### Changelog

###### 3.0.0 2023-03-10
* Removed WPBakery Page Builder

###### 2.0.4 2022-08-08
* Merge changes from github

###### 2.0.3 2022-08-08
* FIX: Formatting per WordPress phpcs coding standards
* Feature: Add Google Tag manager
* Feature: Add Facebook Domain Verification
* FIX: Removed unused gulp functions

###### 2.0.1 2022-03-14
* Use pop-up for the staff excerpt rather than pushing images down and causing a layout shift

###### 2.0.0 2022-03-11
* Use webpack rather than gulp to build plugin
* Fixed WordPress warning in php code
* Changed contact handler, staff and videos to vanilla javascript instead of jquery
* Video CSS moved from page to external file

###### 1.4.6 2022-03-01
* Enhancement: Use Google Analytics new tag method

###### 1.4.5 2020-12-30
* FIX: Avoid accessing null setting for base name

###### 1.4.4 2020-01-20
* FIX: Build files are not copied to the plugin directory they need to be retrieved from GitHub

###### 1.4.3 2020-01-20
* FIX: Correct typos when visiting options page from plugin page
* FIX: Correct typos when saving options

###### 1.4.2 2020-01-20
* Feature: Move Staff Members under main ORC OPtions menu
* FIX: Correctly decode stored textareas
* FIX: Correct call to close comments and pings
* FIX: Correct call to disable Yoast SEO ld+json scripts since we are using our own

###### 1.4.1 2020-01-17
* Enhancement: Start to use classes in files

###### 1.4.0 2020-01-14
* Enhancement: Major change to the structure of the development side of the plugin
* Enhancement: Use npm, composer, gulp to build the release version of the plugin
* Enhancement: Better directory structure

######  1.3.1 2019-12-27
* Feature: Ability to remove the WPBakery License Key when site is cloned from the main site to a development site

######  1.3.0 2019-10-22
* Feature: Allow excerpt for staff pages hooked into the area selection buttons

######  1.2.2 2019-06-21
* Enhancement: Improvements to the saving of the meta box

######  1.2.1 2019-06-21
* FIX: Require a position for staff member
* Feature: Show additional columns in administration menu

######  1.2.0 2019-06-19
* Enhancement: Move staff members over to custom post type rather than a page

######  1.1.21 2019-06-06
* Press Media has a clickable title to take you to the specific article

######  1.1.20 2019-05-02
* Local Business Schema added

######  1.1.19 2019-04-26
* Added additional css for images in the format section

######  1.1.18 2019-04-11
* Added ability to put Organization Schema in the header

######  1.1.17 2019-03-18
* Changed carousel for staff to grab position from the custom fields rather than the excerpt which was being used for the position

######  1.1.16 2019-02-22
* Added modifications for some of the recurring events so rather than going to an individual post the link will go to a single page for all of the reocurring events

######  1.1.15 2019-02-14
* Remove extra lines not necessary in the head of the site

######  1.1.15 2019-02-13
* Fixing validation errors

######  1.1.13 2018-12-26
* Put code back Event tracking code back into the function that is called when an email is sent from the site.
* Added ability to create a different event for each of the contact pages

######  1.1.12 2018-11-23
* Removed Google Analytics Event tracking from Contact Form 7 Submission since it is now handled by a Thank You page

######  1.1.11 2018-11-23
* Added Bing, LinkedIn and Twitter tracking

######  1.1.10 2018-10-24
* Display Calendar in a month format

###### 1.1.9 2018-05-21
* Added ability to email privacy officer

###### 1.1.8 2018-05-21
* FIX: Removed reference to is_christmas in carousel since not used
* FIX: Use prepare with database calls to avoid SQL injection bugs
* Added ability to email website administrator
* Added ability to delete the emails and email addresses stored from Contact Form 7 after a period of time
* Added ability to set the number of days before the Contact Form 7 data is deleted

###### 1.1.7 2018-05-16
* Fixed bug with uninitialized variable in contact form
* Allow contact forms to include short codes
* Delete Contact Form 7/Flamingo email and email addresses
* Removed backup file

###### 1.1.6 2018-05-10
* Added hooks for Contact Form 7 to work with Google Analytics

###### 1.1.5 2018-04-30
* Add in Intake email for the plugin
* Improved link colors from theme colors

###### 1.1.4 2018-04-26
* Removed custom post types, custom fields for Videos which now use pages

###### 1.1.3 2018-04-22
* Added Google Analytics and Facebook Pixel code to plugin

###### 1.1.2 2018-04-20
* Use WPBakery suggestion for getting the css class rather than regex

###### 1.1.1 2018-04-19
* Improvements to the carousel elements (added CSS section)
