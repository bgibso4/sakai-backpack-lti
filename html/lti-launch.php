<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 6/22/2017
 * Time: 11:23 AM
 */
//require_once("ims-lti/IMSGlobal/LTI/ToolProvider/ToolProvider.php");
//require_once
use IMSGlobal\LTI;

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
$_REQUEST= $_POST;

//$cur_url = curPageURL();
$cur_url= "http://localhost:63342/new-lti-app/html/lti-launch.php";
$key = isset($_REQUEST["key"])? $_REQUEST["key"] : false;
if ( ! $key ) $key = "12345";
$secret = isset($_REQUEST["secret"])? $_REQUEST["secret"] : false;
if ( ! $secret ) $secret = "secret";
$endpoint = isset($_REQUEST["endpoint"])? $_REQUEST["endpoint"] : false;
$b64 = base64_encode($key.":::".$secret);
if ( ! $endpoint ) $endpoint = str_replace("lti-launch.php","launchVerification.php",$cur_url);

?>
<a id="displayText" href="javascript:lmsdataToggle();">Toggle Resource and Launch Data</a>
<?php
  echo("<div id=\"lmsDataForm\" style=\"display:block\">\n");
  echo("<form method=\"post\">\n");
  echo("<input type=\"submit\" value=\"Recompute Launch Data\">\n");
  echo("<input type=\"submit\" name=\"reset\" value=\"Reset\">\n");
  echo("<fieldset><legend>BasicLTI Resource</legend>\n");
  $disabled = '';
  echo("Launch URL: <input size=\"60\" type=\"text\" $disabled name=\"endpoint\" value=\"$endpoint\">\n");
  echo("<br/>Key: <input type='text' name=\"key\" $disabled size=\"60\" value=\"$key\">\n");
  echo("<br/>Secret: <input type='text' name=\"secret\" $disabled size=\"60\" value=\"$secret\">\n");
  echo("</fieldset><p>");
  echo("<fieldset><legend>Launch Data</legend>\n");
  foreach ($lmsdata as $k => $val ) {
      echo($k.": <input type=\"text\" name=\"".$k."\" value=\"");
      echo(htmlspecialchars($val));
      echo("\"><br/>\n");
  }
  echo("</fieldset>\n");
  echo("</form>\n");
  echo("</div>\n");
  echo("<hr>");

$parms = $lmsdata;
// Cleanup parms before we sign
foreach( $parms as $k => $val ) {
    if (strlen(trim($parms[$k]) ) < 1 ) {
        unset($parms[$k]);
    }
}

//echo($endpoint);
$_REQUEST["\"lti_message_type\""]= "basic-lti-launch-request";
$_REQUEST["lti_version"]= "LTI-1p0";
//echo($_REQUEST["\"lti_message_type\""]);
//echo($_REQUEST["lti_version"]);


//$parms = signParameters($parms, $endpoint, "POST", $key, $secret, "Press to Launch", $tool_consumer_instance_guid, $tool_consumer_instance_description);

//$content = postLaunchHTML($parms, $endpoint, true, "width=\"100%\" height=\"900\" scrolling=\"auto\" frameborder=\"1\" transparency");
//$formSub= new \IMSGlobal\LTI\HTTPMessage('http://www.google.ca', 'POST', $parms);
$formSub= new \IMSGlobal\LTI\HTTPMessage('http://www.google.ca', 'GET');

echo "Answer: ".$formSub->send();

echo("<form action='http://www.google.ca' method='get'><input type='submit' value='LAUNCH' name='launch'/></form>");
