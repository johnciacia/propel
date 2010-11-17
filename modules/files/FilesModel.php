<?php 

class FilesModel
{
	public $error = "";

    public function checkFileConfig ()
    {
    	$path = get_option('propel_file_path');
    	if(empty($path)) {
    		$this->error = "Upload Path not set.";
    		return false;
    	}
    	
    	if (!file_exists($path)) {
    		$this->error = "Upload path does not exist or is not readable.";
    		return false;
		}
		
    	if (!is_writable($path)) {
    		$this->error = "Upload path is not writable.";
    		return false;
		}

		
        $list = get_option('propel_list');
    	if(empty($list)) {
    		$this->error = "List type is not set.";
    		return false;
    	}
    	
    	
        $size = get_option('propel_file_size');
    	if(empty($size)) {
    		$this->error = "File size is not set.";
    		return false;
    	}
    	

		/**
		 * @todo Check to make sure get_option('propel_file_size') is
		 * less than or equal to ini_get('upload_max_filesize')
		 */
		return true;
    	
    }
    
    public function insertFile()
    {
		$path = get_option('propel_file_path');
		$max = get_option('propel_file_size');
		$list = get_option('propel_list');
		$white_list = get_option('propel_white_list');
		$black_list = get_option('propel_black_list');
                      	
						
		if ($_FILES["file"]["error"] > 0) {
			$this->error = "Return Code: " . $_FILES["file"]["error"] . ". Upload failed.<br />";
			return false;
		}
                      
		if (($_FILES["file"]["size"] < $max)) {
			$this->error = "You tried uploading a file with size '" . $_FILES["file"]["size"] . "' but the maximum allowed file size is '" . $max . "'. Upload failed.<br />";
			return false;
		}
		
		/**
		 * If the black list is empty, it will allow anything to be uploaded.
		 */
		if($list == "black") {
			$types = explode(",", $black_list);
			print_r($types);
			if(in_array($_FILES['file']['type'], $types) == true) {
				$this->error = "The file type '" . $_FILES['file']['type'] . "' is blacklisted. Upload failed.<br />";
				return false;
			}
		}

		/**
		 * If the white list empty, it wont allow anything to be uploaded.
		 */
		if($list == "white") {
			$types = explode(",", $black_list);
			if(in_array($_FILES['file']['type'], $types) == true) {
				$this->error = "The file type '" . $_FILE['file']['type'] . "' is not whitelisted. Upload failed.<br />";
				return false;
			}
		}

		if (file_exists("upload/" . $_FILES["file"]["name"])) {
			$this->error = $_FILES["file"]["name"] . " already exists. ";
			return false;
		} else {
		
			$tag = md5($_FILES["file"]["name"] . time() . rand(0,9999));
			
			/**
			 * @todo check to make sure this completes
			 */
			$this->_insertFile(0, 0, 0, $_FILES["file"]["name"], $tag, $_FILES['file']['size'], $_FILES['file']['type']);
			
			move_uploaded_file($_FILES["file"]["tmp_name"], $path . $tag . "-" . $_FILES["file"]["name"]);
		}  

		return true;
    }
    
    private function _insertFile ($pid, $tid, $uid, $name, $tag, $size, $type)
    {	
    	global $wpdb;
    	
    	/**
    	 * @todo sanitize data
    	 * @todo make sure $name and $type isnt more than 255. if so, return an error
    	 */
		$data = array('pid' => $pid, 
                      'tid' => $tid, 
                      'uid' => $uid, 
                      'name' => $name,
                      'tag' => $tag,
                      'size' => $size,
                      'type' => $type);
        
        $table = $wpdb->prefix . "files";
        
        $wpdb->insert( $table, (array) $data );
    }
     
  
}

?>