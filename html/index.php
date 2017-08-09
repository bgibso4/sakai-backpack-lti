<?php
function __autoload($className){
    $path= "ims-lti"."\\{$className}.php";
    $path= str_replace("\\", "/", $path);
    $path2= "classes"."\\{$className}.php";
    $path2= str_replace("\\", "/", $path2);
    if (file_exists($path)){
        require_once($path);
    }
    elseif (file_exists($path2)){
        require_once ($path2);
    }
    else{
        die("The file {$className}.php could not be found!");
    }
}


function isLtiLaunch($ok){
    // Check it is a POST request
    $ok = $ok && $_SERVER['REQUEST_METHOD'] === 'POST';

    // Check the LTI message type
    $ok = $ok && isset($_POST['lti_message_type']) && ($_POST['lti_message_type'] === 'basic-lti-launch-request');

    // Check the LTI version
    $ok = $ok && isset($_POST['lti_version']) && ($_POST['lti_version'] === 'LTI-1p0');

    // Check a consumer key exists
    $ok = $ok && !empty($_POST['oauth_consumer_key']);

    // Check a resource link ID exists
    $ok = $ok && !empty($_POST['resource_link_id']);


    return $ok;
}

function isValidLaunch($ok){

    $validationArray= array("12345"=>'secret');
    // Check the consumer key is recognised
    $ok = $ok && array_key_exists($_POST['oauth_consumer_key'], $validationArray);

//     Check the OAuth credentials (nonce, timestamp and signature)
    if ($ok) {

        try {
            $store = new ImsOAuthDataStore($_POST['oauth_consumer_key'], $validationArray[$_POST['oauth_consumer_key']]);
            $server = new IMSGlobal\LTI\OAuth\OAuthServer($store);
            $method = new IMSGlobal\LTI\OAuth\OAuthSignatureMethod_HMAC_SHA1();
            $server->add_signature_method($method);
            $request = IMSGlobal\LTI\OAuth\OAuthRequest::from_request();
            $server->verify_request($request);
        } catch (Exception $e) {
            $ok = FALSE;
        }
    }
    return $ok;
}

function checkingOtherRequirements($ok){

    // Check for a user ID
    $ok = $ok && !empty($_POST['lis_person_contact_email_primary']);

    return $ok;
}

function parseRoles($rolesString) {

    $rolesArray = explode(',', $rolesString);
    $roles = array();
    foreach ($rolesArray as $role) {
        $role = trim($role);
        if (!empty($role)) {
            if (substr($role, 0, 4) !== 'urn:') {
                $role = "urn:lti:role:ims/lis/{$role}";
            }
            $roles[] = $role;
        }
    }

    return array_unique($roles);

}

function newSession(){
    // Cancel any existing session
    session_start();
    $_SESSION = array();
    session_destroy();
    session_start();

    // Initialise the user session
    $_SESSION['userId'] = $_POST['user_id'];
    $_SESSION['email'] = $_POST['lis_person_contact_email_primary'];
    $_SESSION['consumerKey'] = $_POST['oauth_consumer_key'];
    $_SESSION['resourceLinkId'] = $_POST['resource_link_id'];
}

function redirect($ok, $check){
    // Set destination page
    if (!$ok) {
        $page = getErrorPage('Sorry, an error occurred. Please contact the system Administrator to fix the issue.');
        header("Location: {$page}");
    }
    else if(!$check){
        $page= getErrorPage("We noticed that you do not have a Mozilla Backpack account yet. Please sign up for one using your western email to have access to this tool.");
        header("Location: {$page}");
    }
}

function getErrorPage($msg) {

    // Redirect back to the tool consumer with an error message if URL is available
    if (isset($_POST['launch_presentation_return_url']) && !empty($_POST['launch_presentation_return_url'])) {
        $page = $_POST['launch_presentation_return_url'];
        if (strpos($page, '?') === FALSE) {
            $page .= '?';
        } else {
            $page .= '&';
        }
        $page .= 'lti_errormsg=' . urlencode($msg);
    } else {
        $page = 'error.html';
    }

    return $page;

}
$ok=true;
//$ok= isLtiLaunch($ok);
//$ok= isValidLaunch($ok);
//$ok= checkingOtherRequirements($ok);
if($ok){
    newSession();
}
//converting email to userID to find badges
//$email= $_POST['lis_person_contact_email_primary'];
$email="gibby.b1212@gmail.com";
$userInfo= array("email"=>"$email");

$userRetrevial= new \IMSGlobal\LTI\HTTPMessage("https://backpack.openbadges.org/displayer/convert/email", "POST", $userInfo);
$userRetrevial->send();
redirect($ok, $userRetrevial->ok);


?>

<html>
    <head>
        <title>Western Badges</title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/index.css">
    </head>
    <body>
        <nav class="nav">
            <div class="navBar">
                <ul class="navbar-list">
                    <li>
                        <a href="HomePage.html">ABOUT</a>
                    </li>
                    <li>
                        <a href="passport.html">HELP</a>
                    </li>
                    <li>
                    </li>
                </ul>
            </div>
        </nav>
        <a href="web/selectingGroups.php">
            <div class="oval">
                
                <br>
                <div class="logo">
                    <br>
                    <img src="images/westernBadgeLogo.png" align="center" width="90%" height="70%">
                </div>
            </div>
        </a>
        
    </body>
</html>