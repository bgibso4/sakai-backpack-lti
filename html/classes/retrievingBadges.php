<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 7/17/2017
 * Time: 3:32 PM
 */
function __autoload($className){
    //$className= strtolower($className);
    $path= "..\ims-lti"."\\{$className}.php";
    $path= str_replace("\\", "/", $path);
    if (file_exists($path)){
        require_once($path);
    }
    else{
        die("The file {$className}.php could not be found!");
    }
}

//converting email to userID to find badges
$email="gibby.b1212@gmail.com";
$userInfo= array("email"=>"$email");

$userRetrevial= new \IMSGlobal\LTI\HTTPMessage("https://backpack.openbadges.org/displayer/convert/email", "POST", $userInfo);
$userRetrevial->send();
if ($userRetrevial->ok){
    $recData= json_decode($userRetrevial->response);
    $userid= $recData->userId;
    $groupsInfo= new \IMSGlobal\LTI\HTTPMessage("https://backpack.openbadges.org/displayer/$userid/groups.json", "GET");
    $groupsInfo->send();
    $groupData= json_decode($groupsInfo->response);
    if($groupData->groups !== ""){
        print_r($groupData->groups);
        foreach($groupData->groups as $element){
            print_r($element);
        }
    }


}
else{
    $recData= json_decode($userRetrevial->response);
    $id_error= $recData->error;
}


$allBadges= new \IMSGlobal\LTI\HTTPMessage("https://backpack.openbadges.org/displayer/$userid/group/126909.json", "GET");
$allBadges->send();
print_r($allBadges);
$badgeInfo= json_decode($allBadges->response);
print_r($badgeInfo);
