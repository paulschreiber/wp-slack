<?php
/**
 * WordPress Publish to Slack
 *
 * @package WPPublishToSlack
 */

/*
Plugin Name: WordPress Publish to Slack
Plugin URI: https://github.com/paulschreiber/wp_publish_to_slack/
Description: A Slackbot that announces whenever a new WordPress post is published. Includes author, title and link. Customizable via filters to support additional metadata.
Version: 0.5.0
Author: Paul Schreiber
Author URI: https://paulschreiber.com/
License: GPL v2
*/
require_once( dirname( __FILE__ ) . '/class-wp-publish-to-slack.php' );
