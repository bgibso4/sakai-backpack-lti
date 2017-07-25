<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 7/21/2017
 * Time: 12:57 PM
 */
function __autoload($className){
    $path= "..\ims-lti"."\\{$className}.php";
    $path= str_replace("\\", "/", $path);
    $path2= "..\classes"."\\{$className}.php";
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
function initializingBadge($instance){
    $badge= $instance->assertion->badge;

    //all mandatory fields that the badge will have
    $newBadge= new badge();
    $newBadge->setImage($instance->imageUrl);
    $newBadge->setCreationTime($instance->assertion->issuedOn);
    $newBadge->setName($badge->name);
    $newBadge->setDescription($badge->description);
    $newBadge->setCriteriaUrl($badge->criteria);
    $newBadge->setId($badge->id);

    //these checks should handle either case of non-mandatory fields being present
    if($instance->assertion->expires != null && property_exists($instance->assertion, "expires")){
        $newBadge->setExpiry($instance->assertion->expires);
    }
    if($badge->tags != null && property_exists($badge, "tags")){
        $newBadge->setTags($badge->tags);
    }

    //all these fields should be mandatory with the creation of an issuer
    $issuer= new obf_issuer($badge->issuer->name, $badge->issuer->email, $badge->issuer->url);
    $newBadge->setIssuer($issuer);

    return $newBadge;
}
?>
<html>
    <head>
        <title>All Badges</title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="../css/basic.css">

    </head>
    <?php
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
            foreach($groupData->groups as $ele){
                if($ele->name=== $_GET['groupChoice']){
                    $groupID= $ele->groupId;
                    break;
                }
            }
            $allBadges= new \IMSGlobal\LTI\HTTPMessage("https://backpack.openbadges.org/displayer/$userid/group/$groupID.json", "GET");
            $allBadges->send();
            $badgeInfo= json_decode($allBadges->response);
            $numBadges= sizeof($badgeInfo->badges);
            $imageUrl= $badgeInfo->badges[0]->imageUrl;

            $badgeArray= [];
            for($i=0; $i<$numBadges; $i++){
                $badgeArray[$i]= initializingBadge($badgeInfo->badges[$i]);
            }

            //to sort the badges if the option gets provided (time restriction maybe) use a sorting algorithm such as quick sort??
        }
        else{
            $recData= json_decode($userRetrevial->response);
            $id_error= $recData->error;
        }
    ?>
    <body>
        <header id="navbar">

            <nav class="navbar">
                <div class="logo">
                    <img src="../images/cancred-passport-logo.png"  width="300px" height="42px">
                </div>
                <div class="nav-list">
                    <br>
                    <ul class="navbar-list">
                        <li>
                            <a href="HomePage.html">HOME</a>
                        </li>
                        <li>
                            <a href="profile.html">PROFILE</a>
                        </li>
                        <li>
                            <a href="passport.html"><b>BADGES</b></a>
                        </li>
                        <li>
                            <a href="issuingPage.html">ISSUE</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <div class="title-bar">
            <p class="title-row">
                Passport / Badge Groups
            </p>
        </div>
        <div class="badgeDisplayGrid">
            <table>
                <tr>
                    <td>
                        <!--
                        <button>
                            <img src="<?php echo($badgeArray[0]->getImage())?>" width="150px" height="150px">

                            <br>
                            <?php echo $badgeArray[0]->getName()?>
                            <br>
                            <small><?php echo $badgeArray[0]->getIssuer()->getName()?></small>
                        </button>
                        -->
                        <div role="button" onclick="alert('You are clicking on me');" class="badgeButton">
                            <img src="<?php echo($badgeArray[0]->getImage())?>" width="150px" height="150px">
                            <br>
                            <div class="badgeTitle">
                                <?php echo $badgeArray[0]->getName()?>
                            </div>

                            <br>
                            <div class="badgeIssuerName">
                                <?php echo $badgeArray[0]->getIssuer()->getName()?>
                            </div>
                        </div>

                        <div role="button" class="badgeButton" id="mod">
                            <img src="<?php echo($badgeArray[0]->getImage())?>" width="150px" height="150px">
                            <br>
                            <div class="badgeTitle">
                                <?php echo $badgeArray[0]->getName()?>
                            </div>

                            <br>
                            <div class="badgeIssuerName">
                                <?php echo $badgeArray[0]->getIssuer()->getName()?>
                            </div>

                        </div>
                        
                        <!-- The Modal -->
                        <div id="myModal" class="modal">

                            <!-- Modal content -->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <span class="close">&times;</span>
                                    <h2>Modal Header</h2>
                                </div>
                                <div class="modal-body">
                                    <p>Some text in the Modal Body</p>
                                    <p>Testing git</p>
                                </div>
                                <div class="modal-footer">
                                    <h3>Modal Footer</h3>
                                </div>
                            </div>

                        </div>

                        <script>
                            // Get the modal
                            var modal = document.getElementById('myModal');

                            // Get the button that opens the modal
                            var btn = document.getElementById("mod");

                            // Get the <span> element that closes the modal
                            var span = document.getElementsByClassName("close")[0];

                            // When the user clicks the button, open the modal
                            btn.onclick = function() {
                                modal.style.display = "block";
                            }

                            // When the user clicks on <span> (x), close the modal
                            span.onclick = function() {
                                modal.style.display = "none";
                            }

                            // When the user clicks anywhere outside of the modal, close it
                            window.onclick = function(event) {
                                if (event.target == modal) {
                                    modal.style.display = "none";
                                }
                            }
                        </script>
                    </td>
                </tr>

            </table>

        </div>


        <!-- need to send the userID and the groupID with the form therefore we gotta make it a post and do something to figure out how to get the group ID
                Or i guess we could just send the group name and userID and then figure out everything else on the next page(group search twice, little inefficient but fuck it
        -->


    </body>

</html>
