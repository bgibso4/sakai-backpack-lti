<html>
    <head>
        <title>All Badges</title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="../css/basic.css">
    
    </head>
    <body>
        <?php
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
                foreach($groupData->groups as $element){
                    //print_r($element);
                }
            }
            $groups= array();
            foreach($groupData->groups as $ele){
                array_push($groups, $ele->name);
            }
            $numGroups= sizeof($groups);
        }
        else{
            $recData= json_decode($userRetrevial->response);
            $id_error= $recData->error;
        }

        // LOOK INTO LAUNCHING A PAGE FOR IF THE EMAIL HAS NOT BEEN REGISTERED YET. THIS WILL CAUSE A HUGE ERROR IF NOT
        ?>
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
                            <a href="selectingGroups.php"><b>BADGES</b></a>
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
        <div class="pageInstructions">
            <h2>Select a Badge Group</h2>
        </div>
        <table width="95%">
            <tr>
                <td width=10%>
                    <div class="side-menu">
                        <ul>
                            <li><a href="selectingGroups.php"><b>MY BADGES</b></a></li>
                        </ul>
                    </div>
                </td>
                <td width="80%">
                    <form action="displayingBadges.php" method="get">
                        <div class="search-Criteria">
                            <table>
                                <tr class="form-group">

                                    <td>
                                        <label class="search-labels" for="groupSearch">Search:</label>
                                    </td>
                                    <td>
                                        <div class="groupChoices">
                                            <input list="choices" id="groupSearch" name="groupChoice">
                                            <datalist id="choices">
                                                <!--
                                                <option></option>
                                                <option>Luke</option>
                                                <option>Anthony</option>
                                                -->
                                            </datalist>
                                            <script type="text/javascript">
                                                var allGroups = <?php echo json_encode($groups)?>;

                                                var options = '';

                                                for(var i = 0; i < allGroups.length; i++)
                                                    options += '<option value="'+allGroups[i]+'" />';

                                                document.getElementById('choices').innerHTML = options;
                                            </script>
                                        </div>
                                        
                                    </td>
                                </tr>
                                <!--
                                <tr>
                                    <td>
                                        <br>
                                        <h3>Badge Ordering:</h3>
                                    </td>
                                </tr>
                                <tr class="form-group">
                                    <td>
                                        <label class="search-labels" for="grid-search-badgeSearch">Tags:</label>
                                    </td>
                                    <td>
                                        <input type="text" name="badgeTags" id="grid-search-tagFiltered"><br>
                                    </td>
                                </tr>
                            </table>
                            
                                
                            <fieldset class="form-group">
                                <legend class="search-labels">Order:</legend>
                                <div class="ordering-options-text">
                                    <label class="radio-inline" for="radio-date">
                                        <input type="radio" id="radio-date" name="order" value="mtime">

                                        by date modified
                                    </label>
                                    <label class="radio-inline" for="radio-name">
                                        <input type="radio" id="radio-name" name="order" value="name">

                                        by name
                                    </label>
                                    <label class="radio-inline" for="radio-issuer">
                                        <input type="radio" id="radio-issuer" name="order" value="issuer_name">

                                        by issuer name
                                    </label>
                                    <label class="radio-inline" for="radio-expiration">
                                        <input type="radio" id="radio-expiration" name="order" value="expires_on">

                                        by expiration date
                                    </label>
                                </div>
                            </fieldset>
                            -->


                                <!--this tag is temporary -->
                            </table>

                            <br>
                            <input type="submit" value="Confirm">
                        </div>

                    </form>
                </td>
            </tr>
            


        </table>




        
    </body>


</html>