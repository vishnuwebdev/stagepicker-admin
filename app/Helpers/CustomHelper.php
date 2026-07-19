<?php


if (!function_exists('checkAdminUserPermission')) {

	function checkAdminUserPermission($moduleId,$permissionType) {
	  
	    $user = Auth::User();
	    $permission = json_decode($user->permissions);

	    foreach($permission as $key => $per) { 

	    	if($per->moduleId == $moduleId && $per->$permissionType) {
	    		return true;
	    	}

	    }

	}

}