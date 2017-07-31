<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 6/22/2017
 * Time: 12:43 PM
 */


//print_r(get_declared_classes());
use IMSGlobal\LTI\ToolProvider;

//require_once('vendor/autoload.php');

$servername = "localhost";
$username = "root";
$password = "root";
$dbname= 'mysql';
$port= '3307';
$dtPrefix= 'obf';


////Used to create the MySQL server
////try {
////    $conn = new PDO("mysql:host=localhost:3307;dbname=mysql", $username, $password);
////    // set the PDO error mode to exception
////    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
////    $sql = "CREATE DATABASE myDBPDO";
////    // use exec() because no results are returned
////    $conn->exec($sql);
////    echo "Database created successfully<br>";
////}
////catch(PDOException $e)
////{
////    echo $sql . "<br>" . $e->getMessage();
////}
////
////$db= new \IMSGlobal\LTI\ToolProvider\DataConnector\DataConnector_pdo($conn);
//
////Used once the database has already been created
//$link = mysqli_connect($servername, $username, $password, $dbname, $port);
//echo mysqli_ping($link);
////if (!$link) {
////    //die('Could not connect: ' . mysqli_error());
////}
////echo 'Connected successfully';
//$db= new \IMSGlobal\LTI\ToolProvider\DataConnector\DataConnector_pdo($link);
//
//$toolCheck= new \IMSGlobal\LTI\ToolProvider\ToolProvider($db);
//$toolCheck->handleRequest();

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

function gen_oauth_creds() {
    // Get a whole bunch of random characters from the OS
    $fp = fopen('/dev/urandom','rb');
    $entropy = fread($fp, 32);
    fclose($fp);

    // Takes our binary entropy, and concatenates a string which represents the current time to the microsecond
    $entropy .= uniqid(mt_rand(), true);

    // Hash the binary entropy
    $hash = hash('sha512', $entropy);

    // Base62 Encode the hash, resulting in an 86 or 85 character string
    $hash = gmp_strval(gmp_init($hash, 16), 62);

    // Chop and send the first 80 characters back to the client
    return array(
        'consumer_key' => substr($hash, 0, 32),
        'shared_secret' => substr($hash, 32, 48)
    );
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

//setting all the values for a valid lti launch
$_POST['lti_message_type']= 'basic-lti-launch-request';
$_POST['lti_version']= 'LTI-1p0';
$_POST['resource_link_id']='120988f929-274612';
$_POST['oauth_consumer_key']= '';
$_POST['tool_consumer_instance_guid'] ='uwo.ca';

//$oauth_info= gen_oauth_creds();
//$oauth_key= $oauth_info[0];
$_POST['oauth_consumer_key']= 'OPP4V4a2PUa54';
//$_POST['oauth_consumer_key']= '';
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


$db = new PDO("mysql:dbname=obf;host=localhost;port=3307", 'ben', '');  // Database constants not defined here
//$db= mysqli_connect('localhost','gibby12','Hershey12!', 'sakai', '3306');
$error= mysqli_connect_error();
//$db= mysql_connect('localhost', 'root', 'root');
//$db=NULL;
$data_connector = ToolProvider\DataConnector\DataConnector_mysql::getDataConnector($dtPrefix, $db);
$tool = new ImsToolProvider($data_connector);

$formSub= new \IMSGlobal\LTI\HTTPMessage('http://www.google.ca', 'GET');
$formSub->send();
$tool->setParameterConstraint('user_id');
$tool->setParameterConstraint('roles');
$tool->handleRequest();
