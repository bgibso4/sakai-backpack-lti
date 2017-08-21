<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 7/21/2017
 * Time: 12:57 PM
 */
session_start();
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

function sortByName($badgeArray, $type){
    // find array size
    $length = count($badgeArray);

    // base case test, if array of length 0 then just return array to caller
    if($length <= 1){
        return $badgeArray;
    }
    else{
        if($type === "badgeName"){
            // select an item to act as our pivot point, since list is unsorted first position is easiest
            $pivot = $badgeArray[0];

            // declare our two arrays to act as partitions
            $left = $right = array();

            // loop and compare each item in the array to the pivot value, place item in appropriate partition
            for($i = 1; $i < count($badgeArray); $i++)
            {
                if(ord(strtolower($badgeArray[$i]->getName()[0])) < ord(strtolower($pivot->getName()[0]))){
                    $left[] = $badgeArray[$i];
                }
                else{
                    $right[] = $badgeArray[$i];
                }
            }
        }
        else{
            // select an item to act as our pivot point, since list is unsorted first position is easiest
            $pivot = $badgeArray[0];

            // declare our two arrays to act as partitions
            $left = $right = array();

            // loop and compare each item in the array to the pivot value, place item in appropriate partition
            for($i = 1; $i < count($badgeArray); $i++)
            {
                if(ord(strtolower($badgeArray[$i]->getIssuer()->getName()[0])) < ord(strtolower($pivot->getIssuer()->getName()[0]))){
                    $left[] = $badgeArray[$i];
                }
                else{
                    $right[] = $badgeArray[$i];
                }
            }
        }

        // use recursion to now sort the left and right lists
        return array_merge(sortByName($left, $type), array($pivot), sortByName($right, $type));
    }
}

function sortByTimestamp($badgeArray, $type){
    // find array size
    $length = count($badgeArray);

    // base case test, if array of length 0 then just return array to caller
    if($length <= 1){
        return $badgeArray;
    }
    else{
        if($type === "creation"){
            // select an item to act as our pivot point, since list is unsorted first position is easiest
            $pivot = $badgeArray[0];

            // declare our two arrays to act as partitions
            $left = $right = array();

            // loop and compare each item in the array to the pivot value, place item in appropriate partition
            for($i = 1; $i < count($badgeArray); $i++)
            {
                if($badgeArray[$i]->getCreationTime() < $pivot->getCreationTime()){
                    $left[] = $badgeArray[$i];
                }
                else{
                    $right[] = $badgeArray[$i];
                }
            }
        }
        else{
            // select an item to act as our pivot point, since list is unsorted first position is easiest
            $pivot = $badgeArray[0];

            // declare our two arrays to act as partitions
            $left = $right = array();

            // loop and compare each item in the array to the pivot value, place item in appropriate partition
            for($i = 1; $i < count($badgeArray); $i++)
            {
                if($badgeArray[$i]->getExpiry() < $pivot->getExpiry()){
                    $left[] = $badgeArray[$i];
                }
                else{
                    $right[] = $badgeArray[$i];
                }
            }
        }

        // use recursion to now sort the left and right lists
        return array_merge(sortByName($left, $type), array($pivot), sortByName($right, $type));
    }
}

?>
<html>
    <head>
        <title>All Badges</title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="../css/basic2.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    </head>
    <?php
        //converting email to userID to find badges
        $email= $_SESSION['email'];
        //$email="gibby.b1212@gmail.com";
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
            $groups= array();
            foreach($groupData->groups as $ele){
                array_push($groups, $ele->name);
            }
            $numGroups= sizeof($groups);
            $allBadges= new \IMSGlobal\LTI\HTTPMessage("https://backpack.openbadges.org/displayer/$userid/group/$groupID.json", "GET");
            $allBadges->send();
            $badgeInfo= json_decode($allBadges->response);
            $numBadges= sizeof($badgeInfo->badges);
            $imageUrl= $badgeInfo->badges[0]->imageUrl;

            $badgeArray= [];
            $newBadge= new badge();
            $newBadge->setName("Ultimate Coder");
            $newBadge->setDescription("Superior being displaying other worldly skills in coding");
            $newBadge->setId("2132312313");
            $newBadge->setExpiry(time());
            $newBadge->setCreationTime(time());
            $newIssuer= new obf_issuer("God","god@gmail.com", "http://www.google.ca");
            $newBadge->setIssuer($newIssuer);
            $newBadge->setImage("https://pbs.twimg.com/profile_images/869496879880568832/peABezDn_400x400.jpg");
            $newBadge->setCriteriaUrl('https://openbadgefactory.com/v1/badge/_/OTNL2EaFL1a3S/criteria.html?v=2.0&event=OTNLQ5aFL1a3X');
            $newBadge->setTags(['Ben', 'Gibson']);


            for($i=0; $i<$numBadges; $i++){
                $badgeArray[$i]= initializingBadge($badgeInfo->badges[$i]);
            }
            array_push($badgeArray, $newBadge);


            //to sort the badges if the option gets provided (time restriction maybe) use a sorting algorithm such as quick sort??
            $tempArray= [];
            if(isset($_GET['badgeTag']) && !empty($_GET['badgeTag'])){
                foreach($badgeArray as $badge){
                    if(in_array($_GET['badgeTag'],$badge->getTags())){
                        array_push($tempArray, $badge);
                    }
                }
                $badgeArray= $tempArray;
            }
            if(isset($_GET['order']) && !empty($_GET['order'])){
                if($_GET['order']==="creation"){
                    $tempArray= sortByTimestamp($badgeArray, "creation");
                }
                else if ($_GET['order']==="name"){
                    $tempArray= sortByName($badgeArray, "badgeName");
                }
                else if ($_GET['order']==="issuer_name"){
                    $tempArray= sortByName($badgeArray, "issuerName");
                }
                else{
                    $tempArray= sortByTimestamp($badgeArray, "expiry");
                }
                $badgeArray=$tempArray;
            }


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
                    <img src="../images/westernBadgeLogo.png"  width="225px" height="50px">
                </div>
                <div class="nav-list">
                    <br>
                    <ul class="navbar-list">
                        <li>
                            <a href="selectingGroups.php"><b>BADGES</b></a>
                        </li>
                        <li>
                            <a href="about.html">ABOUT</a>
                        </li>
                        <li>
                            <a href="help.html">HELP</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <div class="title-bar">
            <p class="title-row">
                Passport / Badge Groups / <?php echo $_GET['groupChoice']?>
            </p>
        </div>
        <table width="95%">
            <tr>
                <td width=15%>
                    <div class="side-menu">
                        <ul>
                            <li><a href="selectingGroups.php"><b>MY BADGES</b></a></li>
                            <li><a href="about.html"><b>ABOUT BADGES</b></a></li>
                            <li><a href="help.html"><b>HELP</b></a></li>
                        </ul>
                    </div>
                </td>
                <td width="78%">
                    <form action="displayingBadges.php" method="get" id="group">
                        <div class="search-Criteria">
                            <table>
                                <tr class="form-group">

                                    <td>
                                        <label class="search-labels" for="groupSearch">Select a group:</label>
                                    </td>
                                    <td>
                                        <div class="groupChoices">
                                            <!--<input list="choices" id="grouSearch" name="grouChoice">-->
                                            <select  name="groupChoice" id="groupSearch" form="group" class="groupText">
                                            </select>
                                            <script type="text/javascript">
                                                var allGroups = <?php echo json_encode($groups)?>;

                                                //var options = '<option class="groupText" selected="selected" disabled="disabled" hidden="hidden"> </option>';
                                                var options = '';

                                                for(var i = 0; i < allGroups.length; i++) {
                                                    options += '<option class="groupText" value="'+allGroups[i]+'">'+allGroups[i]+'</option>';
                                                }
                                                document.getElementById('groupSearch').innerHTML = options;
                                            </script>
                                        </div>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3>Badge Ordering:</h3>
                                    </td>
                                </tr>
                                <tr class="form-group">
                                    <td>
                                        <label class="filter-labels" for="grid-search-tagFiltered">Tags:</label>
                                    </td>
                                    <td>
                                        <input type="text" name="badgeTag" id="grid-search-tagFiltered"><br>
                                    </td>
                                </tr>
                            </table>


                            <fieldset class="form-group">
                                <legend class="filter-labels">Order:</legend>
                                <div class="ordering-options-text">
                                    <label class="radio-inline" for="radio-name">
                                        <input type="radio" id="radio-name" name="order" value="name" class="radio">

                                        by name
                                    </label>
                                    <label class="radio-inline" for="radio-issuer">
                                        <input type="radio" id="radio-issuer" name="order" value="issuer_name" class="radio">

                                        by issuer name
                                    </label>
                                    <label class="radio-inline" for="radio-date">
                                        <input type="radio" id="radio-date" name="order" value="mtime" class="radio">

                                        by creation date
                                    </label>
                                    <label class="radio-inline" for="radio-expiration">
                                        <input type="radio" id="radio-expiration" name="order" value="expires_on" class="radio">

                                        by expiration date
                                    </label>
                                </div>
                            </fieldset>
                        </div>
                        <input type="submit" value="Filter" class="submitButton">
                    </form>
                    <div class="badgeDisplayGrid">
                        <table>
                            <tr>
                                <td>
                                    <div id="allBadges"></div>

                                    <?php
                                    if(sizeof($badgeArray)===0){
                                        echo("<br>");
                                        echo("<h3 class='noResults'>There are no badges matching that search criteria</h3>");
                                    }
                                    else{
                                        foreach($badgeArray as $inst){
                                            $name= $inst->getName();
                                            $imageLink= $inst->getImage();
                                            $description= $inst->getDescription();
                                            $expiry= $inst->convertTimestamp($inst->getExpiry());
                                            $created= $inst->convertTimestamp($inst->getCreationTime());
                                            $criteria= $inst->getCriteriaUrl();
                                            $tags= $inst->tagsToString();

                                            $issuerName=$inst->getIssuer()->getName();
                                            $issuerEmail=$inst->getIssuer()->getEmail();
                                            $issuerUrl= $inst->getIssuer()->getUrl();

                                            echo("<div role=\"button\" onclick=\"showDetails(this)\" class=\"badgeButton\" data-badgeName='$name' data-issuerName='$issuerName' data-badgeImage='$imageLink' data-description='$description'
data-expiry='$expiry' data-creation='$created' data-criteria='$criteria' data-issuerEmail='$issuerEmail' data-issuerUrl='$issuerUrl' data-tags='$tags'>");

                                            echo("<img src='$imageLink' width=\"150px\" height=\"150px\">");
                                            echo("<br>");
                                            echo("<div class=\"badgeTitle\" id=\"\">$name</div>");
                                            echo("<hr>");
                                            echo("<div class=\"badgeIssuerName\">$issuerName</div>");

                                            echo("</div>");
                                        }
                                    }



                                    ?>

                                    <!-- The Modal -->
                                    <div id="myModal" class="modal">

                                        <!-- Modal content -->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <span class="close">&times;</span>
                                                <h2 id="modalTitle"></h2>
                                            </div>
                                            <div class="modal-body">
                                                <div id="modal-left-col" class="modal-left-col">
                                                    <img src="" id="badgeImage"/>
                                                    <h5 class="timeLabels">Created:</h5>
                                                    <h4 id="creationLabel"></h4>
                                                    <h5 class="timeLabels">Expires:</h5>
                                                    <h4 id="expiryLabel"></h4>
                                                </div>
                                                <div id="modal-right-col" class="modal-right-col">
                                                    <h3 class="detailsTitles">Issuer Details</h3>
                                                    <p id="issueNameDisplay"></p>
                                                    <p id="issueUrlDisplay">URL: <a id="issuerLink" href=""></a></p>
                                                    <p id="issueEmailDisplay"></p>
                                                    <hr>
                                                    <br>
                                                    <h3 class="detailsTitles">Badge Details</h3>
                                                    <p id="badgeNameDisplay"></p>
                                                    <p id="badgeDescriptionDisplay"></p>
                                                    <p id="badgeTags"></p>
                                                    <p id="badgeCriteriaDisplay">Criteria: <a id="criteriaLink" href=""></a></p>


                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <br>
                                                <div role="button" class="shareButton" onclick="linkedinOptions()"><img src="../images/linkedin_button.png" alt="LinkedIn Add to Profile button" width="30px" height="30px"></div>
                                                <div class="linkedinModal" id="linkedinModal">
                                                    <div class="linkedin-content">
                                                        <div class="linkedinHead">
                                                            <span class="close">&times;</span>
                                                        </div>
                                                        <div class="linkedinBody">
                                                            <h3>Share on LinkedIn</h3>
                                                            <p class="linked-paragraph">Make your skills, knowledge and achievements visible to your network and become discovered! Add this Open Badge to your LinkedIn Profile:</p>
                                                            <!-- <div role="button" onclick="linkedinProfile()" class="shareButton"><a href="https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME" rel="nofollow" target="_blank"><img src="https://download.linkedin.com/desktop/add2profile/buttons/en_US.png " alt="LinkedIn Add to Profile button"></a></div>
                                                            -->
                                                            <div role="button" onclick="linkedinProfile(<?php $this?>)" class="shareButton"><img src="https://download.linkedin.com/desktop/add2profile/buttons/en_US.png " alt="LinkedIn Add to Profile button"></div>

                                                        </div>
<!--                                                        <div class="linkedinHead">-->
<!--                                                            <br>-->
<!--                                                            <p class="linked-paragraph">Add this Open Badge as an Update to Newsfeed:</p>-->
<!--                                                        </div>-->
<!--                                                        <div class="linkedinBody">-->
<!--                                                            <div><a href="https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME" rel="nofollow" target="_blank"><img src="https://download.linkedin.com/desktop/add2profile/buttons/en_US.png " alt="LinkedIn Add to Profile button"></a></div>-->
<!--                                                        </div>-->
                                                    </div>


                                                </div>
                                                <div class="twitter">
                                                    <a class="twitter-share-button"
                                                       href="https://twitter.com/intent/tweet">
                                                        Tweet</a>
                                                </div>

                                                <br>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="linkedinProfile-modal" id="linkedinProfile-modal">
                                        <div class="linkedinProfile-content">
                                            <div class="LN-profile-header">
                                                <span class="close">&times;</span>
                                                <h2>Add this open Badge to Linkedin</h2>
                                                <p class="linked-paragraph">Click the copy buttons below to fill in the information in the Add Certification block of your LinkedIn profile.</p>
                                                <div class="reopen-image">
                                                    <a href="https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME" rel="nofollow" target="_blank"><img src="https://download.linkedin.com/desktop/add2profile/buttons/en_US.png " alt="LinkedIn Add to Profile button"></a>
                                                </div>
                                                <div class="reopen-link">
                                                    <br>
                                                    <a href="https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME" target="_blank" class="no-enlarge"> Click here if LinkedIn window doesn't appear automatically.</a>
                                                </div>

                                            </div>
                                            <br>
                                            <div class="LN-profile-body">
                                                <table class="linedin-table">
                                                    <tr>
                                                        <td>
                                                            <fieldset>
                                                                <div class="copy-section">
                                                                    <label class="sub-heading">Certification name</label>
                                                                    <br>
                                                                    <div class="input-group">
                                                                        <input type="text" class="js-copied-input1" id="cert-name" name="email-text" readonly value="">
                                                                    </div>
                                                                    <div class="copyButton">

                                                                        <div role="button" class="js-textareacopybtn1" ><img src="../images/copy1.png" alt="Copy to clipboard" width="35px" height="35px"></div>
                                                                    </div>
                                                                </div>
                                                                <br>
                                                                <div class="copy-section">
                                                                    <label class="sub-heading">Certification authority</label>
                                                                    <br>

                                                                    <div class="input-group">

                                                                        <input type="text" class="js-copied-input2" id="cert-authority" name="email-text" readonly value="">
                                                                    </div>
                                                                    <div class="copyButton">
                                                                        <div role="button" class="js-textareacopybtn2"><img src="../images/copy1.png" alt="Copy to clipboard" width="35px" height="35px"></div>
                                                                    </div>
                                                                </div>
                                                                <br>
                                                                <div class="copy-section">
                                                                    <label class="sub-heading">License number</label>
                                                                    <br>

                                                                    <div class="input-group">
                                                                        <input type="text" class="js-copied-input3" id="license-num" name="email-text" readonly value="">
                                                                    </div>
                                                                    <div class="copyButton">
                                                                        <div role="button" class="js-textareacopybtn3"><img src="../images/copy1.png" alt="Copy to clipboard" width="35px" height="35px"></div>
                                                                    </div>
                                                                </div>
                                                                <br>
                                                                <div class="copy-section">
                                                                    <label class="sub-heading">Time Period</label>

                                                                    <div>
                                                                        <p class="linked-paragraph" id="time-frame"></p>
                                                                    </div>
                                                                </div>
                                                                <br>
                                                                <div class="copy-section">
                                                                    <label class="sub-heading">Certification url</label>
                                                                    <br>

                                                                    <div class="input-group">
                                                                        <input type="text" class="js-copied-input4" id="cert-url" name="email-text" readonly value="">
                                                                    </div>
                                                                    <div class="copyButton">
                                                                        <div role="button" class="js-textareacopybtn4"><img src="../images/copy1.png" alt="Copy to clipboard" width="35px" height="35px"></div>
                                                                    </div>
                                                                </div>

                                                            </fieldset>
                                                        </td>
                                                        <td>
                                                            <label class="sub-heading">Tutorial video</label>
                                                            <div class="tutorial-video">
                                                                <iframe width="350" height="250" src="https://www.youtube.com/embed/YJJ8lZshqbY" frameborder="0"></iframe>
                                                            </div>

                                                        </td>

                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>


                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>



        <!-- need to send the userID and the groupID with the form therefore we gotta make it a post and do something to figure out how to get the group ID
                Or i guess we could just send the group name and userID and then figure out everything else on the next page(group search twice, little inefficient but fuck it
        -->


    </body>
    <script>
        // Get the modal
        var modal = document.getElementById('myModal');
        var linkedinModal = document.getElementById('linkedinModal');
        var linkedinProfileModal= document.getElementById('linkedinProfile-modal');

        // Get the button that opens the modal
        var btn = document.getElementsByClassName("badgeButton");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];
        var span2 = document.getElementsByClassName("close")[1];
        var span3 = document.getElementsByClassName("close")[2];

        // When the user clicks the button, open the modal
        btn.onclick = function() {
            modal.style.display = "block";
        };

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
            linkedinModal.style.display= "none";
        };
        span2.onclick = function() {
            linkedinModal.style.display= "none";
        };
        span3.onclick = function(){
            linkedinProfileModal.style.display= "none";
        };

        // When the user clicks anywhere outside of the modal, close it

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
                linkedinModal.style.display= "none";
                linkedinProfileModal.style.display= "none";
            }
            else if (event.target == linkedinProfileModal) {
                modal.style.display = "none";
                linkedinModal.style.display= "none";
                linkedinProfileModal.style.display= "none";
            }
            else if (event.target == linkedinModal) {
                linkedinModal.style.display= "none";
            }
        };
        function showDetails(badge) {

            document.getElementById('modalTitle').innerHTML = badge.getAttribute("data-badgeName");
            document.getElementById('badgeImage').src= badge.getAttribute('data-badgeImage');
            document.getElementById('badgeImage').width= 200;
            document.getElementById('badgeImage').height=200;
            document.getElementById('creationLabel').innerHTML= badge.getAttribute('data-creation');
            document.getElementById('expiryLabel').innerHTML= badge.getAttribute('data-expiry');

            document.getElementById('badgeNameDisplay').innerHTML= "Name: " + badge.getAttribute('data-badgeName');
            document.getElementById('badgeDescriptionDisplay').innerHTML= "Details: " + badge.getAttribute('data-description');
            document.getElementById('badgeTags').innerHTML= "Tags: "+badge.getAttribute('data-tags');
            document.getElementById('criteriaLink').innerHTML= badge.getAttribute('data-criteria');
            document.getElementById('criteriaLink').href= badge.getAttribute('data-criteria');
            document.getElementById('issueNameDisplay').innerHTML= "Name: " + badge.getAttribute('data-issuerName');
            document.getElementById('issuerLink').innerHTML= badge.getAttribute('data-issuerUrl');
            document.getElementById('issuerLink').href= badge.getAttribute('data-issuerUrl');
            document.getElementById('issueEmailDisplay').innerHTML= "Email: " + badge.getAttribute('data-issuerEmail');
            modal.style.display = "block";

            document.getElementById('cert-name').value= badge.getAttribute('data-badgeName');
            document.getElementById('cert-authority').value= badge.getAttribute('data-issuerName');
            document.getElementById('license-num').value= badge.getAttribute('data-badgeName');
            document.getElementById('time-frame').innerHTML= badge.getAttribute('data-creation') + " - " + badge.getAttribute('data-expiry');
            document.getElementById('cert-url').value= badge.getAttribute('data-criteria');
        }
        function linkedinOptions(){
            linkedinModal.style.display= "block";
        }
        function linkedinProfile(){
            linkedinProfileModal.style.display= "block";
            linkedinModal.style.display= "none";
            modal.style.display= "none";
            document.getElementById('cert-name').value= badge.getAttribute('data-badgeName')


        }

        var copyTextareaBtn1 = document.querySelector('.js-textareacopybtn1');

        copyTextareaBtn1.addEventListener('click', function(event) {
            var copyTextarea = document.querySelector('.js-copied-input1');
            copyTextarea.select();

            try {
                var successful = document.execCommand('copy');
                var msg = successful ? 'successful' : 'unsuccessful';
                console.log('Copying text command was ' + msg);
            } catch (err) {
                console.log('Oops, unable to copy');
            }
        });
        var copyTextareaBtn2 = document.querySelector('.js-textareacopybtn2');

        copyTextareaBtn2.addEventListener('click', function(event) {
            var copyTextarea = document.querySelector('.js-copied-input2');
            copyTextarea.select();

            try {
                var successful = document.execCommand('copy');
                var msg = successful ? 'successful' : 'unsuccessful';
                console.log('Copying text command was ' + msg);
            } catch (err) {
                console.log('Oops, unable to copy');
            }
        });
        var copyTextareaBtn3 = document.querySelector('.js-textareacopybtn3');

        copyTextareaBtn3.addEventListener('click', function(event) {
            var copyTextarea = document.querySelector('.js-copied-input3');
            copyTextarea.select();

            try {
                var successful = document.execCommand('copy');
                var msg = successful ? 'successful' : 'unsuccessful';
                console.log('Copying text command was ' + msg);
            } catch (err) {
                console.log('Oops, unable to copy');
            }
        });
        var copyTextareaBtn4 = document.querySelector('.js-textareacopybtn4');

        copyTextareaBtn4.addEventListener('click', function(event) {
            var copyTextarea = document.querySelector('.js-copied-input4');
            copyTextarea.select();

            try {
                var successful = document.execCommand('copy');
                var msg = successful ? 'successful' : 'unsuccessful';
                console.log('Copying text command was ' + msg);
            } catch (err) {
                console.log('Oops, unable to copy');
            }
        });

//    </script>

</html>
