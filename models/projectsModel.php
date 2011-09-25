<?php

class ProjectsModel 
{
	private $table_name;
	
	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . "posts";
	}
	
	public function createProject ($args)
	{
		
		global $current_user;
		$user_info = get_currentuserinfo();
		
		$project = array(
			'post_title' => $args['title'],
			'post_content' => $args['description'],
			'post_status' => 'publish',
			'post_type' => "propel_project"
		);
		
		$id = wp_insert_post( $project );
		
		$args['start_date'] = isset($args['start_date']) ? $args['start_date'] : "0000-00-00";
		$args['priority'] = isset($args['priority']) ? $args['priority'] : 1;
		$args['complete'] = isset($args['complete']) ? $args['complete'] : 0;
		$args['user'] = isset($args['user']) ? $args['user'] : $current_user->ID;	
		
		$meta = array(
			'start' => $args['start_date'], 
			'end' => $args['end_date'],
			'priority' => $args['priority'],
			'complete' => $args['complete'],
		);
		
		add_post_meta($id, "_propel_project_metadata", $meta);
		add_post_meta($id, "_propel_project_user", $args['user']);
		add_post_meta($id, "_propel_project_owner", $args['owner']);
		
		return $id;
		
	}
	
	public function getProjects ()
	{
		global $wpdb;
		$sql = "SELECT * FROM `{$this->table_name}` WHERE `post_type` = 'propel_project'";
		return $wpdb->get_results($sql, OBJECT);
	}
	
	public function getProjectById ($id)
	{
		global $wpdb;
		$id = (int) $wpdb->escape($id);
		$sql = "SELECT * FROM `{$this->table_name}` WHERE `ID` = $id";
		return $wpdb->get_row($sql, OBJECT);
	}
	
	public function updateProject ($args)
	{
		$project = array();
		$project['ID'] = $args['id'];
		$project['post_title'] = $args['title'];
		$project['post_content'] = $args['description'];

		wp_update_post( $project );
		
		$meta = array(
			'start' => $args['start_date'], 
			'end' => $args['end_date'],
			'priority' => $args['priority'],
			'complete' => $args['complete']
		);
		
		update_post_meta($args['id'], "_propel_project_metadata", $meta);
			
	}	
	
	public function deleteProject ($id)
	{
		return wp_delete_post($id);		
	}

}


?>