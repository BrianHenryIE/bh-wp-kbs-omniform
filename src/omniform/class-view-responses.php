<?php
/**
 * Add a link to the associated ticket on each form entry.
 *
 * @package brianhenryie/bh-wp-kbs-omniform
 */

namespace BrianHenryIE\WP_KBS_OmniForm\OmniForm;

/**
 * Add a column to the WP_Posts_List_Table linking to tickets created by the responses.
 */
class View_Responses {

	/**
	 * Append the Ticket column to the list table's columns.
	 *
	 * @hooked manage_omniform_response_posts_columns
	 * @see \WP_Posts_List_Table::get_columns()
	 * @param array<string, string> $columns The existing list of columns.
	 * @return array<string, string>
	 */
	public function add_ticket_to_columns( array $columns ): array {
		$columns['ticket'] = __( 'Ticket', 'bh-wp-kbs-omniform' );

		return $columns;
	}

	/**
	 * Print the ticket priority in the ticket priority column.
	 *
	 * @hooked manage_omniform_response_posts_custom_column
	 * @see \WP_Posts_List_Table::column_default()
	 *
	 * @param string $column The column id, hopefully as added in the array above.
	 * @param int    $post_id WP_Posts ID of the ticket.
	 */
	public function print_ticket_column( string $column, int $post_id ): void {

		if ( 'ticket' !== $column ) {
			return;
		}

		$ticket_id = get_post_meta( $post_id, 'kb_ticket_id', true );

		if ( empty( $ticket_id ) ) {
			return;
		}

		echo sprintf(
			'<a href="%s">%d</a>',
			esc_url( admin_url( sprintf( 'post.php?post=%d&action=edit', $ticket_id ) ) ),
			intval( $ticket_id )
		);
	}

}
