=== GamiPress - BadgeOS Importer ===
Contributors: gamipress, tsunoa, rubengc, eneribs
Tags: badgeos, gamipress, gamification, points, badges, achievements, awards, rewards, credits, engagement, migration, importer, import
Requires at least: 4.0
Tested up to: 5.3
Stable tag: 1.0.8
License: GNU AGPLv3
License URI:  http://www.gnu.org/licenses/agpl-3.0.html

Tool to migrate all stored data from BadgeOS to GamiPress

== Description ==

GamiPress - BadgeOS Importer let's you migrate all data from BadgeOS to [GamiPress](https://wordpress.org/plugins/gamipress/ "GamiPress")!

= Features =

* Ability to move BadgeOS achievements to GamiPress achievements.
* Ability to move BadgeOS points to any GamiPress points type.
* Ability to reassign all BadgeOS logs to GamiPress logs.
* Ability to reassign all BadgeOS user earned achievements and points to GamiPress user earnings.

= BadgeOS plugins support =

* Support for BadgeOS community add-on activity to be imported to [GamiPress - BuddyPress integration](https://wordpress.org/plugins/gamipress-buddypress-integration/) and [GamiPress - bbPress integration](https://wordpress.org/plugins/gamipress-bbpress-integration/).
* Support for BadgeOS LearnDash integration activity to be imported to [GamiPress - LearnDash integration](https://wordpress.org/plugins/gamipress-learndash-integration/).

== Installation ==

= From WordPress backend =

1. Navigate to Plugins -> Add new.
2. Click the button "Upload Plugin" next to "Add plugins" title.
3. Upload the downloaded zip file and activate it.

= Direct upload =

1. Upload the downloaded zip file into your `wp-content/plugins/` folder.
2. Unzip the uploaded zip file.
3. Navigate to Plugins menu on your WordPress admin area.
4. Activate this plugin.

== Frequently Asked Questions ==

= How to import your data? =

Before start importing, you just need to create a new points type to migrate BadgeOS points.

BadgeOS achievement types will be migrated as GamiPress achievement types with the same name.

1. Navigate to WP Admin area -> GamiPress -> Tools -> Import/Export tab.
2. At box "BadgeOS Importer" you will find settings to set to which data migrate.
3. Click the button "Start Importing Data" and wait until process gets finished.
4. That's all!

== Screenshots ==

== Changelog ==

= 1.0.8 =

* **Bug Fixes**
* Fixed user earnings import process.
* **Improvements**
* Added some cleanup checks before run the tool.

= 1.0.7 =

* Added support to GamiPress 1.5.1 relationships since P2P has been deprecated.

= 1.0.6 =

* Added support to GamiPress 1.5.1 logs database changes.

= 1.0.5 =

* Added extra checks on LearnDash integration events to meet if an event should be a "complete specific" event instead of "complete any" event.

= 1.0.4 =

* Added support to GamiPress 1.4.3 and 1.4.7 database upgrades.

= 1.0.3 =

* Improvements on fields descriptions.

= 1.0.2 =

* Added the ability to keep the imported points balance instead of sum them to the current points balance.
* Improvements migrating large amounts of records.

= 1.0.1 =

* Added the ability to select import user earnings and logs.
* Improvements on import process descriptions.

= 1.0.0 =

* Initial release.
