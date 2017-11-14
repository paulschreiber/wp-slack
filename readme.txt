=== Publish to Slack ===
Contributors: paulschreiber
Tags: Slack,WordPress,publish,webhook
Requires at least: 4.8.3
Tested up to: 4.8.3
Requires PHP: 7.0
Stable tag: 0.5.0
License: GPL v2

A Slackbot that announces whenever a new WordPress post is published. Includes author, title and link. Customizable via filters to support additional metadata.

== Installation ==
1. Upload "wp-slack" to the "/wp-content/plugins/" directory.
1. Activate the plugin through the "Plugins" menu in WordPress.
1. Set up the wp_slack_endpoint filter

   add_filter( 'wp_slack_endpoint', function() {
   	return 'https://hooks.slack.com/services/xyzxyz/abcabcabc/defdef';
   });

== Selected filters ==
* Use "wp_slack_is_enabled" to disable posting in development or staging environments
* Use "wp_slack_post_types" to add support for custom post types.
* Use "wp_slack_label1"/"wp_slack_value1" (and "wp_slack_label2"/"wp_slack_value2") for custom metadata.

See source code for complete list of filters.


== Changelog ==
= 0.5 =
* Initial release
