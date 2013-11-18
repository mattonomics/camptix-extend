<?php defined('ABSPATH') || die(); ?>
<!DOCTYPE html>
<html>
	<head>
		<?php
		echo
			"<script type=\"text/javascript\" src=\"", esc_url(($GLOBALS['wp_scripts']->registered['jquery-core']->src)), "\"></script>",
			"<script type=\"text/javascript\" src=\"", esc_url(($GLOBALS['wp_scripts']->registered['jquery-migrate']->src)), "\"></script>",
			"<script type=\"text/javascript\" src=\"", esc_url(plugins_url('js/main.js', __FILE__)), "\"></script>",
			"<script type=\"text/javascript\">
				var camptix_extend = {
					nonce: '", esc_attr(wp_create_nonce('camptix_extend_checkbox')), "',
					url: '", admin_url('admin-post.php?action=camptix_extend_checkin_user'), "'
				};
			</script>";
		
		?>
		<meta name="viewport" content="width=device-width">
		<style type="text/css">
			#container {
				margin: 0 auto;
				max-width: 600px;
			}
			#ticket_list {
				list-style: none;
				list-style-position: inside;
				padding: 0;
			}
			#ticket_list li:nth-child(even) {
				background: #f0f0f0;
			}
			#ticket_list li {
				border-style: solid;
				border-width: 0 1px 1px 1px;
			}
			#ticket_list li:first-child {
				border-top-width: 1px;
			}
			#ticket_list span {
				display: inline-block;
				padding: 5px;
			}
			span.checkbox, span.name {
				margin-right: 5px;
			}
			@media (max-width: 600px) {
				ul {
					padding: 0;
				}
				#ticket_list label {
					padding: 15px 10px;
					display: block;
				}
				#ticket_list span {
					padding: 0;
				}
			}
		</style>
	</head>
	<body>
		<div id="container">
			<ul id="ticket_list">
				<?php
				
				if (!empty($attendee_list))
					foreach ($attendee_list as $id => $attendee)
						echo "<li><label><span class=\"checkbox\"><input type=\"checkbox\" value=\"", absint($id), "\" name=\"attendee[]\" /></span><span class=\"name\">", esc_attr($attendee['name']), "</span><span class=\"email\">", esc_attr($attendee['email']), "</span></label></li>\n";
				else
					echo "<li>", __('All attendees have been checked in.', 'camptix_extend'), "</li>";
				?>
			</ul>
		</div>
	</body>
</html>