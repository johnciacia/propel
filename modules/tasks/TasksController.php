<?php 

class TasksController
{

	public function __construct ()
	{
		require_once("TasksModel.php");
		$this->model = new TasksModel();
		
		$actions = array("create", "edit", "_create", "_update", "delete", "complete", "approve", "unapprove");
		
		
		if(empty($_GET['action'])) {
			$this->index();
		} else {	
			if(in_array($_GET['action'], $actions)) {
				$this->$_GET['action']();
			} else {
				$this->index();
			}
		}	
	}
	
	private function index ()
	{

        $excerpt_mode = esc_url(add_query_arg('mode', 'excerpt', remove_query_arg('action', $_SERVER['REQUEST_URI'])));
        $list_mode = esc_url(add_query_arg('mode', 'list', remove_query_arg('action', $_SERVER['REQUEST_URI'])));
        
        $projects = $this->model->getProjects();
        
     	empty($_GET['mode']) ? $mode = "list" : $mode = $_GET['mode'];
     	   
        if(empty($id))
        	$id = 0;
        if(empty($pid))
        	$pid = 0;
        
		if(!empty($_REQUEST['filter'])) {
        	$filter = explode("-", $_REQUEST['filter']);
        	if(count($filter) == 1) {
        		$id = $filter[0];
        	} else {
        		list($id, $pid) = $filter;
        	}
		}
        	
        $tasks = $this->model->getTasks($id, $pid);
		$pending = $this->model->getPendingCount();
        require_once ("views/tasks.php");	
	}

	private function create ()
	{
		list($day, $month, $year) = array(date("d"), date("F"), date("Y"));
		$months = $this->model->getMonths();
		$projects = $this->model->getProjects();
		$users = $this->model->getUsers();
		require_once ("views/tasks-create.php");	
	}
	
	private function edit ()
	{
		$months = $this->model->getMonths();
		$task = $this->model->getTask($_REQUEST['id']);
		$projects = $this->model->getProjects();
		$users = $this->model->getUsers();
		list ($start_year, $start_month, $start_day) = explode("-", $task->start);
		list ($end_year, $end_month, $end_day) = explode("-", $task->end);
		require_once ("views/tasks-edit.php");	
	}
	
	private function _create ()
	{
		$this->model->createTask($_POST);
		$this->index();
	}
	
	private function _update ()
	{
		$this->model->updateTask($_POST);
		$this->index();
	}
	
	private function delete ()
	{
		if (isset($_REQUEST['check'])) {
			foreach ((array) $_REQUEST['check'] as $id) {
				$this->model->deleteTask($id);
			}
			echo '<div class="updated fade below-h2" id="message"><p>Tasks deleted.</p></div>';
		} else {
			$this->model->deleteTask($_REQUEST['id']);
			echo '<div class="updated fade below-h2" id="message"><p>Task deleted.</p></div>';
		}
		$this->index();
	}
	
	private function complete ()
	{
		if (isset($_REQUEST['check'])) {
			foreach ((array) $_REQUEST['check'] as $id) {
				$this->model->completeTask($id);
			}
			echo '<div class="updated fade below-h2" id="message"><p>Projects completed.</p></div>';
		} else {
			$this->model->completeTask($_REQUEST['task_id']);
			echo '<div class="updated fade below-h2" id="message"><p>Project completed.</p></div>';
		}
		$this->index();	
	}
	
	private function approve ()
	{
		if (isset($_REQUEST['check'])) {
			foreach ((array) $_REQUEST['check'] as $id) {
				$this->model->approveTask($id);
			}
			echo '<div class="updated fade below-h2" id="message"><p>Tasks approved.</p></div>';
		} else {
			$this->model->approveTask($_GET['id']);
			echo '<div class="updated fade below-h2" id="message"><p>Task approved.</p></div>';
		}	
		$this->index();
	}
	
	private function unapprove ()
	{
		if (isset($_REQUEST['check'])) {
			foreach ((array) $_REQUEST['check'] as $id) {
				$this->model->unapproveTask($id);
			}	
			echo '<div class="updated fade below-h2" id="message"><p>Tasks unapproved.</p></div>';
		} else {
			$this->model->unapproveTask($_GET['id']);
			echo '<div class="updated fade below-h2" id="message"><p>Task unapproved.</p></div>';
		}	
		$this->index();
	}

}

?>