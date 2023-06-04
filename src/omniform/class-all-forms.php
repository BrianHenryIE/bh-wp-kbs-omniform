<?php
/**
 * Add a checkbox to the WP_List_Table to enable KB Support piping.
 *
 * @package brianhenryie/bh-wp-kbs-omniform
 */

namespace BrianHenryIE\WP_KBS_OmniForm\OmniForm;

use WP_Screen;

/**
 * Add a column to the WP_Posts_List_Table and handle the AJAX for updating the checkbox value.
 */
class All_Forms {

	/**
	 * Append a "KB Support" column to the list table to indicate is the form set to pipe to KB Support.
	 *
	 * We'll (TODO) hide this by default, but it allows us to have the data in the table which we'll need for
	 * the enable/disable checkbox.
	 *
	 * @hooked manage_omniform_posts_columns
	 * @see \WP_Posts_List_Table::get_columns()
	 * @param array<string, string> $columns The existing list of columns.
	 * @return array<string, string>
	 */
	public function add_ticket_to_columns( array $columns ): array {
		$columns['open-ticket'] = __( 'KB Support', 'bh-wp-kbs-omniform' );

		return $columns;
	}

	/**
	 * Print a tick icon in the KB Support column if the form opens tickets.
	 *
	 * @hooked manage_omniform_posts_custom_column
	 * @see \WP_Posts_List_Table::column_default()
	 *
	 * @param string $column The column id, hopefully as added in the array above.
	 * @param int    $post_id WP_Posts ID of the ticket.
	 */
	public function print_ticket_column( string $column, int $post_id ): void {

		if ( 'open-ticket' !== $column ) {
			return;
		}

		$enabled = get_post_meta( $post_id, 'bh_wp_kbs_opens_tickets', true ) === 'yes';

		$submission_count = get_post_meta( $post_id, 'bh_wp_kbs_submission_count', true );

		// TODO: link to filtered submissions (there's no existing filter in the KB Ticket view for this).
		echo sprintf(
			'<span data-opens-ticket="%s" class="opens-ticket" title="%s">%s %s</span>',
			esc_attr( $enabled ? 'true' : 'false' ),
			esc_attr( $enabled ? 'connected' : '' ),
			$enabled ? '<span class="dashicons dashicons-plugins-checked"></span>&nbsp;' : '',
			esc_html( $enabled ? $submission_count : '' )
		);
	}

	/**
	 * Hide the new column by default.
	 *
	 * @hooked default_hidden_columns
	 * @see get_hidden_columns()
	 *
	 * @param string[]  $hidden Array of IDs of columns hidden by default.
	 * @param WP_Screen $screen WP_Screen object of the current screen.
	 *
	 * @return string[]
	 */
	public function add_column_to_hidden_list( array $hidden, WP_Screen $screen ): array {

		if ( 'omniform' === $screen->post_type ) {
			$hidden[] = 'open-ticket';
		}

		return $hidden;
	}

	/**
	 * Add a checkbox to use the form for opening tickets.
	 *
	 * @hooked bulk_edit_custom_box
	 * @hooked quick_edit_custom_box
	 * @see \WP_Posts_List_Table::inline_edit()
	 *
	 * @param string $column_name Name of the column to edit.
	 * @param string $post_type The post type slug, or current screen name if this is a taxonomy list table.
	 */
	public function quick_edit( string $column_name, string $post_type ): void {

		if ( ( 'omniform' !== $post_type ) || ( 'open-ticket' !== $column_name ) ) {
			return;
		}

		echo sprintf(
			'<input id="omniform-kb-ticket" type="checkbox" name="omniform-kb-ticket" value="true" /><label for="omniform-kb-ticket">%s</label>',
			esc_html( __( 'Open KB ticket with form submission', 'bh-wp-kbs-omniform' ) )
		);
	}

	/**
	 * Bulk list table edits save via page refresh.
	 */

	/**
	 * Individual list table edits save via AJAX.
	 *
	 * @hooked wp_ajax_inline_save
	 * @see wp_ajax_inline_save()
	 */
	public function ajax_save(): void {

		check_ajax_referer( 'inlineeditnonce', '_inline_edit' );

		if ( ! isset( $_REQUEST['screen'] )
			|| 'edit-omniform' !== $_REQUEST['screen']
			|| ! isset( $_REQUEST['post_ID'] ) ) {
			return;
		}

		$post_id = intval( $_REQUEST['post_ID'] );

		$open_ticket = isset( $_REQUEST['omniform-kb-ticket'] );

		if ( $open_ticket ) {
			add_post_meta( $post_id, 'bh_wp_kbs_opens_tickets', 'yes', true );
		} else {
			delete_post_meta( $post_id, 'bh_wp_kbs_opens_tickets' );
		}
	}
}
