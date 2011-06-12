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
		//global $wpdb;
		$project = array(
			'post_title' => $args['title'],
			'post_content' => $args['description'],
			'post_status' => 'publish',
			'post_type' => "propel_project"
		);

		return wp_insert_post( $project );
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
			
	}	
	
	public function deleteProject ($id)
	{
		return wp_delete_post($id);		
	}

}


?>