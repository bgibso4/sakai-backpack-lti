<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 7/11/2017
 * Time: 3:17 PM
 */

use IMSGlobal\LTI\ToolProvider;

function __autoload($className){
    //$className= strtolower($className);
    $path= "ims-lti"."\\{$className}.php";
    $path= str_replace("\\", "/", $path);
    if (file_exists($path)){
        require_once($path);
    }
    else{
        die("The file {$className}.php could not be found!");
    }
}
class ImsToolProvider extends ToolProvider\ToolProvider {

    function onLaunch() {

        // Initialise the user session
        $_SESSION['userId'] = $this->user->getId();
        $_SESSION['isInstructor'] = $this->user->isStaff();
        $_SESSION['firstname'] = $this->user->firstname;
        $_SESSION['lastname'] = $this->user->lastname;
        $_SESSION['consumerKey'] = $this->resourceLink->getKey();
        $_SESSION['resourceLinkId'] = $this->resourceLink->getId();

        $this->redirectUrl = 'welcome.php';

    }

    function onError() {

        $this->isOK = empty($this->returnUrl);
        $this->redirectUrl = 'error.php';

        return false;

    }

}

// Cancel any existing session
session_start();
$_SESSION = array();
session_destroy();
session_start();

$db_host= 'localhost';
$db_port= '3307';
$db_name= 'sakai';
$db_username= 'ben';
$db_password= 'hershey';
//$dsn= 'toolDSN';
$dsn= 'mysql:host=localhost;port=3307;dbname=sakai';

//setting all the values for a valid lti launch
$_POST['lti_message_type']= 'basic-lti-launch-request';
$_POST['lti_version']= 'LTI-1p0';
$_POST['resource_link_id']='120988f929-274612';
$_POST['tool_consumer_instance_guid'] ='uwo.ca';
$_POST['oauth_consumer_key']= 'OPP4V4a2PUa54';

$lmsdata = array(
    "resource_link_id" => "120988f929-274612",
    "resource_link_title" => "Weekly Blog",
    "resource_link_description" => "A weekly blog.",
    "user_id" => "292832126",
    "roles" => "Instructor",  // or Learner
    "lis_person_name_full" => 'Jane Q. Public',
    "lis_person_name_family" => 'Public',
    "lis_person_name_given" => 'Given',
    "lis_person_contact_email_primary" => "user@school.edu",
    "lis_person_sourcedid" => "school.edu:user",
    "context_id" => "456434513",
    "context_title" => "Design of Personal Environments",
    "context_label" => "SI182",
    "tool_consumer_info_product_family_code" => "ims",
    "tool_consumer_info_version" => "1.1",
    "tool_consumer_instance_guid" => "lmsng.school.edu",
    "tool_consumer_instance_description" => "University of School (LMSng)",
);
foreach ($lmsdata as $k => $val ) {
    if ( $lmsdata[$k] && strlen($lmsdata[$k]) > 0 ) {
        $_POST[$k]= $lmsdata[$k]  ;
    }
}

try {
    $db = new PDO($dsn, $db_username, $db_password);  // Database constants not defined here
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
$data_connector = ToolProvider\DataConnector\DataConnector::getDataConnector("testTable", $db);
$tool = new ImsToolProvider($data_connector);
$tool->setParameterConstraint('user_id');
$tool->setParameterConstraint('roles');
$tool->handleRequest();

