<?php

class ProjectsController extends PropelController
{

	public function load ()
	{
		require_once("ProjectsModel.php");
		$this->model = new ProjectsModel();
	
		$actions = array("create", "edit", "_create", "_update", "delete", "complete");
		
        /**
         * @todo only "/a-z0-9_/"i are acceptable characters
         */
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
	
	private function index() 
	{
		$projects = $this->model->getProjects();
		$mode = (empty($_GET['mode'])) ? "list" : $_GET['mode'];
		
		/**
		 * @todo create a loadView function
		 */
		require_once ("views/projects.php");
	}
	
	private function create()
	{
		/**
		 * $months should be reterived from the model
		 */
		$months = $this->model->getMonths();
		$day = date("d");
		$month = date("F");
		$year = date("Y");
		require_once ("views/projects-create.php");	
	}
	
	private function edit()
	{
		$project = $this->model->getProject($_GET['id']);
		require_once ("views/projects-edit.php");	
	}
	
	private function _create()
	{
		$this->model->createProject($_POST);
		$this->index();
	}
	
	private function _update()
	{
		$this->model->updateProject($_POST);
		$this->index();
	}
	
	private function delete()
	{
		if (isset($_GET['check'])) {
			foreach ((array) $_GET['check'] as $id) {
				$this->model->deleteProject($id);
			}
			/**
			 * This HTML should be in a view. Not in the controller
			 */
			echo '<div class="updated fade below-h2" id="message"><p>Projects deleted.</p></div>';
		} else {
			$this->model->deleteProject($_GET['id']);
			echo '<div class="updated fade below-h2" id="message"><p>Project deleted.</p></div>';
		}
		$this->index();
	}
	
	private function complete()
	{
		if (isset($_GET['check'])) {
			foreach ((array) $_GET['check'] as $id) {
				$this->model->completeProject($id);
			}
			
			/**
			 * @TODO: This HTML should be in a view. Not in the controller
			 */
			echo '<div class="updated fade below-h2" id="message"><p>Projects completed.</p></div>';
		} else {
			$this->model->completeProject($_GET['id']);
			echo '<div class="updated fade below-h2" id="message"><p>Project completed.</p></div>';
		}
		$this->index();
	}
}

?>