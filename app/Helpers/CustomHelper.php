<?php


if (!function_exists('checkAdminUserPermission')) {

	function checkAdminUserPermission($moduleId,$permissionType) {
	  
	    $user = Auth::User();

	    // The root/superadmin account (id 1) is excluded from the Admin
	    // Users management screen (see AdminUserController::list(), which
	    // filters out id 1), so there is no UI path to add newly introduced
	    // modules (e.g. Animal Audition Management, p10) to its stored
	    // `permissions` JSON. Give it unconditional access here instead of
	    // relying on that JSON staying in sync with every module added
	    // after the account was created.
	    if ($user->id == 1) {
	        return true;
	    }

	    $permission = json_decode($user->permissions);

	    foreach($permission as $key => $per) { 

	    	if($per->moduleId == $moduleId && $per->$permissionType) {
	    		return true;
	    	}

	    }

	}

}