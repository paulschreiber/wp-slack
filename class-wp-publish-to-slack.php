<?php
/**
 * WordPress Publish to Slack
 *
 * @package WPPublishToSlack
 */

/**
 * WordPress Publish to Slack
 */
class WP_Publish_To_Slack {

	/**
	 * Set up WordPress hooks
	 */
	public static function hooks() {
		add_action( 'transition_post_status', [ __CLASS__, 'transition_post_status' ], 10, 3 );
	}

	/**
	 * HTTP POST the data to the Slack endpoint
	 *
	 * @param array $payload data to send to Slack.
	 * @param array $endpoint Slack Incoming Webhook.
	 */
	public static function post_to_slack( $payload, $endpoint ) {
		if ( ! $payload ) {
			return;
		}

		$fields = [
			'body' => [
				'payload' => wp_json_encode( $payload, JSON_UNESCAPED_SLASHES ),
			],
		];
		$result = wp_remote_post( $endpoint, $fields );
	}

	/**
	 * When a post is published, send a message to Slack.
	 *
	 * @param string  $new_status current status of post.
	 * @param string  $old_status previous status of post.
	 * @param WP_Post $post_object WP_Post object.
	 */
	public static function transition_post_status( $new_status, $old_status, $post_object ) {
		/**
		 * Enable/disable posting of messages to Slack. Set to false for non-production environments.
		 *
		 * @param boolean $is_enabled whether to announce posts to Slack
		 */
		if ( ! apply_filters( 'wp_slack_is_enabled', true ) ) {
			return;
		}

		/**
		 * Provide a webhook endpoint for your Slack Workspace
		 *
		 * @param string $endpoint URL of Slack webhook
		 */
		$endpoint = apply_filters( 'wp_slack_endpoint', false );
		if ( ! $endpoint ) {
			return;
		}

		/**
		 * Filters the list post types whose publication is sent to slack
		 *
		 * @param array $post_types list of post types
		 */
		$allowed_post_types = apply_filters( 'wp_slack_post_types', [ 'post' ] );
		if ( ! $allowed_post_types ) {
			return;
		}

		if ( ! in_array( $post_object->post_type, $allowed_post_types, true ) ) {
			return;
		}

		if ( 'publish' !== $new_status || 'publish' === $old_status ) {
			return;
		}

		/**
		 * Filter first item's text label
		 *
		 * @param string $label text label
		 */
		$label1 = apply_filters( 'wp_slack_label1', false );

		/**
		 * Filter first item's value
		 *
		 * @param string $value value
		 */
		$value1 = apply_filters( 'wp_slack_value1', false );

		/**
		 * Filter second item's text label
		 *
		 * @param string $label text label
		 */
		$label2 = apply_filters( 'wp_slack_label2', false );

		/**
		 * Filter second item's value
		 *
		 * @param string $value value
		 */
		$value2 = apply_filters( 'wp_slack_value2', false );


		$fields = [];
		if ( $label1 && $value1 ) {
			$fields[] = [
				'title' => $label1,
				'value' => $value1,
				'short' => true,
			];
		}

		if ( $label2 && $value2 ) {
			$fields[] = [
				'title' => $label2,
				'value' => $value2,
				'short' => true,
			];
		}

		$title = html_entity_decode( get_the_title( $post_object->ID ) );
		$title_link = get_the_permalink( $post_object->ID );
		$attachments = [
			'ts' => time(),
			'title' => wp_strip_all_tags( $title ),
			'title_link' => $title_link,
		];

		$thumb_url = get_the_post_thumbnail_url( $post_object->ID, [ 75, 75 ] );
		if ( $thumb_url ) {
			$attachments['thumb_url'] = $thumb_url;
		}

		if ( $fields ) {
			$attachments['fields'] = $fields;
		}

		$author_icon = get_avatar_url( $post_object->post_author, [
			'size' => 16,
		] );
		/**
		 * Filter the author icon
		 *
		 * @param string $url author icon
		 */
		$author_icon = apply_filters( 'wp_slack_author_icon_url', $author_icon );
		if ( $author_icon ) {
			$attachments['author_icon'] = $author_icon;
		}

		$author_page_url = get_author_posts_url( $post_object->post_author );
		/**
		 * Filter the URL to the author page
		 *
		 * @param string $url author page
		 */
		$author_icon = apply_filters( 'wp_slack_author_page_url', $author_page_url );
		if ( $author_page_url ) {
			$attachments['author_link'] = $author_page_url;
		}

		$first_name = get_the_author_meta( 'first_name', $post_object->post_author );
		$last_name = get_the_author_meta( 'last_name', $post_object->post_author );
		$author_name = trim( $first_name . ' ' . $last_name );
		/**
		 * Filter the author's display name
		 *
		 * @param string $name author name
		 */
		$author_name = apply_filters( 'wp_slack_author_name', $author_name );
		if ( $author_name ) {
			$attachments['author_name'] = $author_name;
		}

		$payload = [];
		if ( $attachments ) {
			$payload['attachments'] = [ $attachments ];
		}

		/**
		 * Filter the icon used by the slackbot
		 *
		 * @param string $url icon url
		 */
		$icon_url = apply_filters( 'wp_slack_icon_url', false );
		if ( $icon_url ) {
			$payload['icon_url'] = $icon_url;
		}

		/**
		 * Filter the payload to post to Slac
		 *
		 * @param array $payload payload to post to Slac
		 */
		$payload = apply_filters( 'wp_slack_payload', $payload );

		self::post_to_slack( $payload, $endpoint );
	}
}

add_action( 'init', [ 'WP_Publish_To_Slack', 'hooks' ] );
