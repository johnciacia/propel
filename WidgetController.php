<?php

class Widgets {
	
	/**
	 * Handle AJAX requests
	 * @since 1.2
	 */
	public function ajax ($action)
	{
		require_once("modules/tasks/TasksModel.php");
		$TasksModel = new TasksModel();
		switch($action) {
			case 'quick-task':
				$data = $TasksModel->createTask($_POST);
				$widgets = get_option('dashboard_widget_options');
				@extract(@$widgets['wptrac'], EXTR_SKIP);
						
				$image = WP_PLUGIN_URL . "/propel/images/";
				if(date("Y-m-d") == $data['end']) {
					$image .= "today.png";
				} else if(date("Y-m-d") > $data['end']) {
					$image .= "overdue.png";
				} else {
					$image .= "later.png";
				}
				
				if(empty($pm_complete)) $pm_complete = 0;
				$completed = ($pm_complete == "on") ? 0 : 1;
				
				$task = $TasksModel->getTaskByTitle($_POST['title']);
				return json_encode(array ('id' => $task->id, 'description' => $_POST['description'], 
										'title' => $_POST['title'], 'image' =>  $image, 
										'percent' => $_POST['complete'], 'show_completed' => $completed));
				
			case 'task-glance':
				if($_POST['action2'] == "delete")
					$TasksModel->deleteTask($_POST['id']);
				else if($_POST['action2'] == "complete")
					$TasksModel->completeTask($_POST['id']);
				return $_POST['id'];
		}
	
	}
	
	/**
	 * @since 1.1
	 */
	public function task_list ()
	{
		global $wpdb, $userdata;
		get_currentuserinfo();
		$widgets = get_option('dashboard_widget_options');
		@extract(@$widgets['wptrac'], EXTR_SKIP);
		if(empty($pm_complete)) $pm_complete = 1;
		$completed = ($pm_complete == "on") ? 1 : 0;
		
		/**
		 * @TODO: Create a WidgetModule or move this into TracModel
		 */
		if(!$pm_complete) {
			$tasks = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix ."tasks` WHERE `complete` < 100 ORDER BY `end`", OBJECT);
		} else {
			$tasks = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix ."tasks` ORDER BY `end`", OBJECT);
		}
 		require_once('widgets/task-list.php');
	}

	/**
	 * @since 1.1
	 */
	public function config_task_list ()
	{
		/**
		 * @todo set default values
		 */
		if (!$widget_options = get_option('dashboard_widget_options'))
			$widget_options = array();
			
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$wptrac['pm_complete'] = $_POST['pm_complete'];
			$widget_options['wptrac'] = $wptrac;
			update_option('dashboard_widget_options', $widget_options);
		}

		if(empty($pm_complete)) $pm_complete = 0;
		
		@extract($widget_options['wptrac']);
		require_once('widgets/config-task-list.php');
	}
	
	/**
	 * @since 1.1
	 */
	public function add_task ()
	{
		require_once("modules/tasks/TasksModel.php");
		$TasksModel = new TasksModel();
		$widgets = get_option('dashboard_widget_options');
		@extract(@$widgets['wptrac'], EXTR_SKIP);		
		list($day, $month, $year) = array(date("d"), date("F"), date("Y"));
		$months = $TasksModel->getMonths();
		$projects = $TasksModel->getProjects();
		if(count($projects) == 0) {
			echo '<p>You must create at least one project to use this widget.</p>';
			return;
		}
		$users = $TasksModel->getUsers();
		
		if(empty($advance)) $advance = 0;
		
		require_once('widgets/add-task.php');
	}

	/**
	 * @since 1.1
	 */
	public function config_add_task ()
	{
		if (!$widget_options = get_option('dashboard_widget_options'))
			$widget_options = array();
			
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$wptrac['advance'] = $_POST['advance'];
			$widget_options['wptrac'] = $wptrac;
			update_option('dashboard_widget_options', $widget_options);
		}
		
		if(empty($advance)) $advance = 0;
		/**
		 * @todo remove supresson operator
		 */
		@extract($widget_options['wptrac']);
		
		require_once('widgets/config-add-task.php');		
	}
	
	/**
	 * @since 1.1
	 */
	public function add_project ()
	{
		echo "Add a projects...";
	} 
	
}

?>