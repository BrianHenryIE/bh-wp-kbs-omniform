<?php
/**
 * Create the ticket when a new OmniForm response is created.
 *
 * @package brianhenryie/bh-wp-kbs-omniform
 */

namespace BrianHenryIE\WP_KBS_OmniForm\WP_Includes;

use WP_Post;

/**
 * Add an action on `wp_insert_post()` to parse the data and create a new KB Ticket.
 */
class Post {

	/**
	 * Add OmniForm responses as KB Tickets.
	 *
	 * @see kbs_add_ticket()
	 *
	 * @hooked wp_after_insert_post
	 * @see wp_after_insert_post()
	 *
	 * @param int          $post_id     Post ID.
	 * @param WP_Post      $omniform_response_post        Post object.
	 * @param bool         $update      Whether this is an existing post being updated.
	 * @param null|WP_Post $post_before Null for new posts, the WP_Post object prior
	 *                                  to the update for updated posts.
	 */
	public function create_ticket_from_form( int $post_id, WP_Post $omniform_response_post, bool $update, ?WP_Post $post_before ): void {

		if ( 'omniform_response' !== $omniform_response_post->post_type ) {
			return;
		}

		if ( ! function_exists( 'kbs_add_ticket' ) ) {
			return;
		}

		$omniform_form_id = get_post_meta( $omniform_response_post->ID, '_omniform_id', true );

		if ( 'yes' !== get_post_meta( $omniform_form_id, 'bh_wp_kbs_opens_tickets', true ) ) {
			return;
		}

		$kb_support_params = array(
			'post_title'        => '',
			'user_email'        => '',
			'post_content'      => '',
			'post_category'     => '',
			'status'            => '',
			'department'        => '',
			'agent_id'          => '',
			'participants'      => array(),
			'user_info'         => array(
				'first_name' => '',
				'email'      => '',
			),
			'attachments'       => '',
			'privacy_accepted'  => '',
			'terms_agreed'      => '',
			'form_data'         => array(),
			'source'            => "omniform_{$omniform_form_id}",
			'submission_origin' => get_post_meta( $omniform_response_post->ID, '_wp_http_referer', true ),
			'post_date'         => $omniform_response_post->post_date,
		);

		$omniform_response_data    = json_decode( $omniform_response_post->post_content, true );
		$omniform_response_content = $omniform_response_data['response'];

		$ticket_data = wp_parse_args( $omniform_response_content, $kb_support_params );

		// Convert or insert string categories as kbs ticket_category terms.
		if ( ! empty( $ticket_data['post_category'] ) ) {
			$categories                     = array_map( 'trim', explode( ',', $ticket_data['post_category'] ) );
			$kb_support_ticket_category_ids = array();
			foreach ( $categories as $category ) {

				$term = term_exists( $category, 'ticket_category' )
					?? wp_insert_term( $category, 'ticket_category' );

				if ( is_wp_error( $term ) ) {
					continue;
				}

				$kb_support_ticket_category_ids[] = $term['term_id'];
			}
			$ticket_data['post_category'] = $kb_support_ticket_category_ids;
		}

		if ( ! empty( $ticket_data['privacy_accepted'] ) ) {
			$ticket_data['privacy_accepted'] = $omniform_response_post->post_date;
		}

		if ( ! empty( $ticket_data['terms_accepted'] ) ) {
			$ticket_data['terms_accepted'] = $omniform_response_post->post_date;
		}

		if ( empty( $ticket_data['user_info']['email'] ) ) {
			$ticket_data['user_info']['email'] = $ticket_data['user_email'];
		}

		$ticket_data['form_data'] = $omniform_response_data;

		$kb_support_ticket_post_id = kbs_add_ticket( $ticket_data );

		if ( false === $kb_support_ticket_post_id ) {
			// Error.
			return;
		}

		add_post_meta( $omniform_response_post->ID, 'kb_ticket_id', $kb_support_ticket_post_id );

		// Record the submission count for the OmniForm All Forms (omniform) list table.
		$submission_count = get_post_meta( $omniform_form_id, 'bh_wp_kbs_submission_count', true );
		$submission_count = intval( $submission_count ) + 1;
		add_post_meta( $omniform_form_id, 'bh_wp_kbs_submission_count', $submission_count, true );
	}
}
