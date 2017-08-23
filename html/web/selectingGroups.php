<html>
    <head>
        <title>All Badges</title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="../css/platform.css">
    
    </head>
    <body>
        <?php
        session_start();
        function __autoload($className){
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
        $email= $_SESSION['email'];
        $userInfo= array("email"=>"$email");

        $userRetrevial= new \IMSGlobal\LTI\HTTPMessage("https://backpack.openbadges.org/displayer/convert/email", "POST", $userInfo);
        $userRetrevial->send();
        if ($userRetrevial->ok){
            $recData= json_decode($userRetrevial->response);
            $userid= $recData->userId;
            $groupsInfo= new \IMSGlobal\LTI\HTTPMessage("https://backpack.openbadges.org/displayer/$userid/groups.json", "GET");
            $groupsInfo->send();
            $groupData= json_decode($groupsInfo->response);
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

        ?>
        <header id="navbar">
            
            <nav class="navbar">
                <div class="logo">
                    <img src="../images/westernBadgeLogo.png"  width="200px" height="60px">
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
                Passport / Badge Groups
            </p>
        </div>
        <div class="pageInstructions">
            <h2>Select a Badge Group</h2>
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

                    <form action="displayingBadges.php" method="get" id="group" >
                        <div class="search-Criteria">
                            <table>
                                <tr class="form-group">

                                    <td>
                                        <label class="search-labels" for="groupSearch">Pick from one of your groups:</label>
                                    </td>
                                    <td>
                                        <div class="groupChoices">
                                            <select  name="groupChoice" id="groupSearch" form="group" class="groupText">
                                            </select>
                                            <script type="text/javascript">
                                                var allGroups = <?php echo json_encode($groups)?>;

                                                var options='';
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
                                        <br>
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
                                        <input type="radio" id="radio-date" name="order" value="creation" class="radio">

                                        by creation date
                                    </label>
                                    <label class="radio-inline" for="radio-expiration">
                                        <input type="radio" id="radio-expiration" name="order" value="expires_on" class="radio">

                                        by expiration date
                                    </label>
                                </div>
                            </fieldset>
                            <br>
                            <input type="submit" value="Confirm" class="submitButton">
                        </div>

                    </form>

                </td>
            </tr>
        </table>
    </body>
</html>