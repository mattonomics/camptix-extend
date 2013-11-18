<?php

/*
	Plugin Name: CampTix Extend
	Description: Extended functionality for CampTix.
*/

class camptix_extend {
	
	public function __construct() {
		add_action('init', array($this, 'init'));
	}
	
	public function init() {
		if (! class_exists('CampTix_Plugin'))
			return;

		add_action('template_redirect', array($this, 'create_page'));
		add_action('camptix_menu_tools_checkin', array($this, 'option_output'));
		add_action('admin_menu', array($this, 'admin_menu'));
		add_action('admin_post_camptix_extend_checkin_redirect', array($this, 'redirect'));
		add_action('admin_post_camptix_extend_checkin_user', array($this, 'checkin_user'));
		
		add_filter('camptix_menu_tools_tabs', array($this, 'add_tabs'));
	}
	
	public function admin_menu() {
		add_submenu_page('edit.php?post_type=tix_ticket', __('CampTix Extend', 'camptix_extend'), __('CampTix Extend', 'camptix_extend'), 'edit_posts', 'admin.php?camptix_extend_launch_checkin&_wpnonce=' . wp_create_nonce(__CLASS__));
	}
	
	public function redirect() {
		check_admin_referer(__CLASS__);
		if (empty($_POST['camptix_extend']))
			wp_die(__('No data was received.', 'camptix_extend'));
		
		$build = array();	
		foreach ($_POST['camptix_extend'] as $post)
			$build[] = trim(absint($post));
		
		wp_redirect(add_query_arg(array('camptix_extend' => 'checkin', 'camptix_extend_tix' => implode(',', $build), 'nonce' => wp_create_nonce(__CLASS__)), site_url()));
		exit;
	}
	
	public function create_page() {
		if (!empty($_GET['camptix_extend']) && $_GET['camptix_extend'] == 'checkin' && !empty($_GET['camptix_extend_tix']) && wp_verify_nonce($_GET['nonce'], __CLASS__)) {
			$attendee_list = $this->get_attendees(explode(',', $_GET['camptix_extend_tix']));
			include 'page.php';
			exit;
		}
	}
	
	public function get_attendees($ids = array()) {
		$return = array();
		foreach ($ids as $id) {
			$retrieve = get_posts(array(
				'post_type' => 'tix_attendee',
				'posts_per_page' => -1,
				'post_status' => array('publish'),
				'orderby' => 'ID',
				'order' => 'DESC',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'tix_ticket_id',
						'compare' => '=',
						'value' => absint($id)
					),
					array(
						'key' => 'camptix_extend_checkedin',
						'compare' => 'NOT EXISTS'
					)
				)
			));
			foreach ($retrieve as $attendee)
				$return[$attendee->ID] = array(
					'email' => get_post_meta($attendee->ID, 'tix_email', true),
					'name'	=> $attendee->post_title
				);
		}
		return $return;
	}
	
	public function add_tabs($tabs) {
		$tabs['checkin'] = __('Checkin', 'camptix_extend');
		return $tabs;
	}
	
	public function option_output() {
		$tickets = get_posts( array(
			'post_type' => 'tix_ticket',
			'post_status' => 'any',
			'posts_per_page' => -1,
		) );
		
		$ticket_select = '';
		foreach ($tickets as $ticket)
			$ticket_select .= "<label><input type=\"checkbox\" name=\"camptix_extend[]\" value=\"" . absint($ticket->ID) . "\" /> " . esc_attr($ticket->post_title) . "</label><br />\n";
		
		?>
		<form method="post" action="<?php echo esc_url(admin_url('admin-post.php?action=camptix_extend_checkin_redirect')); ?>">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php _e( 'Check in guests for', 'camptix_extend' ); ?></th>
						<td>
							<?php echo $ticket_select; ?>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<?php wp_nonce_field(__CLASS__); ?>
				<input type="submit" class="button-primary" value="<?php esc_attr_e('Check In Guests', 'camptix_extend'); ?>" />
			</p>
		</form>
		<?php
	}
	
	public function checkin_user() {
		if (! wp_verify_nonce($_POST['nonce'], 'camptix_extend_checkbox') || !current_user_can('edit_posts') || empty($_POST['data']))
			die('{ error: true }');
		$id = absint($_POST['data']);
		update_post_meta($id, 'camptix_extend_checkedin', true);
		wp_cache_flush();
		die('{ error: false }');
	}
}

new camptix_extend;