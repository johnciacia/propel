<?php


class ProjectsModel 
{
	
	public function createProject ($args)
	{
		global $wpdb;
		$table	 = $wpdb->prefix . "projects";
		$data = array('title' => $args['title'],
					'description' => $args['description']);

		$wpdb->insert( $table, (array) $data );
		return $wpdb->insert_id;
	}
	
	public function getProjects ()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "projects";
		$sql = "SELECT * FROM `$table_name`";
		return $wpdb->get_results($sql, OBJECT);
	}
	
	public function getProjectById ($id)
	{
		global $wpdb;
		$id = (int) $wpdb->escape($id);
		$table_name = $wpdb->prefix . "projects";
		$sql = "SELECT * FROM `$table_name` WHERE `id` = $id";
		return $wpdb->get_row($sql, OBJECT);
	}
	
	public function updateProject ($args)
	{
		global $wpdb;
		return $wpdb->update("{$wpdb->prefix}projects", 
			array('title' => $args['title'], 'description' => $args['description']), 
			array('id' => $args['id']));
			
	}	
	
	public function deleteProject ($id)
	{
		global $wpdb;
		$id = (integer) $wpdb->escape($id);
		$sql = "DELETE FROM `{$wpdb->prefix}projects` WHERE `id` = $id";  
		return $wpdb->query($sql);		
	}

}


?>