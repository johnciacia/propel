<?php

add_shortcode( 'pl-projects', 'propel_projects_shortcode' );
function propel_projects_shortcode ($atts) {

	extract( shortcode_atts( array( 'id' => NULL ), $atts ) );
	
	if( $id == NULL ) { 
		$args = array(
			'numberposts' => -1,
			'post_type' => 'propel_project',
			'post_status' => 'publish'
		);
		$projects = get_posts( $args );
	} else {
		$projects = array();
		$projects[] = get_post( $id );
	}

	ob_start();
	?>
	<div class="propel-projects">
		<ul>
		<?php 
		foreach( $projects as $project ) {
			echo '<li><a href="#project-' . $project->ID . '" title="' . esc_attr($project->post_content) . '"><span>' . $project->post_title . '</span></a></li>';
		}
		?>
		</ul>

		<?php
		foreach( $projects as $project ) :
			$argv = array(
				'numberposts' => -1,
				'post_type' => 'propel_task',
				'post_status' => 'publish',
				'post_parent' => $project->ID
			);
			$tasks = get_posts( $argv );
		?>


		<div id="project-<?php echo $project->ID ?>">
			<table width="100%">
			<?php 
			foreach( $tasks as $task ) : 
				$complete = get_post_meta( $task->ID, '_propel_complete', true );
				$priority = get_post_meta( $task->ID, '_propel_priority', true );
				$end = get_post_meta( $task->ID, '_propel_end_date', true );
				$due = '';
				if( ! empty( $end ) ) {
					$due = "Due: " . date( 'Y-m-d', $end );
				}
			?>
				<tbody>
				<tr>
					<td width="20%"><p><?php echo $task->post_title; ?></p></td>
					<td width="80%">
						<div class="propel-progress-bar" title="<?php echo $complete; ?>"></div>
					</td>
					<td width="10%"><p><?php echo $complete; ?>%</p></td>
				</tr>
				
				<tr>
					<td>&nbsp;</td>
					<td colspan="2"><p><?php echo $task->post_content; ?></p></td>
				</tr>
				
				<tr>
					<td>&nbsp</td>
					<td colspan="2"><p><small>Priority: <?php echo $priority; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $due; ?></p></small></td>
				</tr>
				</tbody>
			<?php endforeach; ?>
			</table>
		</div>
		<?php endforeach; ?>
	</div>

	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('.propel-projects').tabs().wrap('<div class="propel">');
			jQuery('.propel-progress-bar').each(function() {
				value = { value: parseInt(this.title) }
				jQuery(this).progressbar( value )
			})
		})
	</script>
	<?php 
	return ob_get_clean();
}

add_action( 'propel_deprecated_options', 'propel_deprecated_options' );
function propel_deprecated_options( $options ) {
	echo '<p><input name="propel_options[theme]" id="theme" type="text" value="'.$options['theme'].'" /> (default: <code>' . WP_PLUGIN_URL . '/propel/themes/smoothness/jquery-ui-1.8.6.custom.css</code>)</p>';
	echo '<br />';
}
?>