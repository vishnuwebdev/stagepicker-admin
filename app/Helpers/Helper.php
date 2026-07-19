<?php
namespace App\Helpers;
use App\Models\Category;
use App\Models\User;
use App\Models\Postaudition;
use App\Models\Skill;


function adminAsset($url) { 
    return URL::asset("public/admin/" . $url);
}

function frontAsset($url) {
    return URL::asset("public/front/" . $url);
}


