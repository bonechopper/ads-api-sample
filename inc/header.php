<?php
/**
 * Copyright 2010 Facebook, Inc.
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

function print_header($facebook, $me, $session)
{
  // login or logout url will be needed depending on current user state.
  if ($me) {
    $logoutUrl = $facebook->getLogoutUrl();
  } else {
    $loginUrl = $facebook->getLoginUrl();
  }
  echo '<!doctype html>
    <html xmlns:fb="http://www.facebook.com/2008/fbml">
      <head>
        <title>Facebook Sample Ads Management App</title>
    <style>
      body {
        font-family: "Lucida Grande", Verdana, Arial, sans-serif;
      }
      h1 a {
        text-decoration: none;
        color: #3b5998;
      }
      h1 a:hover {
        text-decoration: underline;
      }
    </style>
  <link type="text/css" rel="stylesheet" href="common.css"/>
  </head>
  <body>
    <!--
      We use the JS SDK to provide a richer user experience. For more info,
      look here: http://github.com/facebook/connect-js
    -->
    <div id="fb-root"></div>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId   : '. $facebook->getAppId() .',
          //dont refetch the session when PHP already has it
          session : '. json_encode($session) .',
          status  : true, // check login status
          // enable cookies to allow the server to access the session
          cookie  : true,
          xfbml   : true // parse XFBML
        });

        // whenever the user logs in, we refresh the page
        FB.Event.subscribe("auth.login", function() {
          window.location.reload();
        });
        FB.Event.subscribe("auth.logout", function() {
          window.location.reload();
        });

      };

      (function() {
        var e = document.createElement("script");
        e.src = document.location.protocol +
          "//connect.facebook.net/en_US/all.js";
        e.async = true;
        document.getElementById("fb-root").appendChild(e);
      }());
    </script>
    <script type="text/javascript">
    function pauseAdGroup(adgroup_id) {
      document.getElementById("action").value = "pauseAdGroup";
      document.getElementById("adgroup_id").value = adgroup_id;
      document.getElementById("form").submit();
    }

    function resumeAdGroup(adgroup_id) {
      document.getElementById("action").value = "resumeAdGroup";
      document.getElementById("adgroup_id").value = adgroup_id;
      document.getElementById("form").submit();
    }

    function deleteAdGroup(adgroup_id) {
      document.getElementById("action").value = "deleteAdGroup";
      document.getElementById("adgroup_id").value = adgroup_id;
      document.getElementById("form").submit();
    }

    function pauseCampaign(campaign_id) {
      document.getElementById("action").value = "pauseCampaign";
      document.getElementById("campaign_id").value = campaign_id;
      document.getElementById("form").submit();
    }

    function resumeCampaign(campaign_id) {
      document.getElementById("action").value = "resumeCampaign";
      document.getElementById("campaign_id").value = campaign_id;
      document.getElementById("form").submit();
    }

    function deleteCampaign(campaign_id) {
      document.getElementById("action").value = "deleteCampaign";
      document.getElementById("campaign_id").value = campaign_id;
      document.getElementById("form").submit();
    }

    function updateAdGroup(adgroup_id) {
      document.getElementById("action").value = "updateAdGroup";
      document.getElementById("adgroup_id").value = adgroup_id;
      document.getElementById("ad_name").value =
        document.getElementById("ad_" + adgroup_id + "_name").value;
      document.getElementById("max_bid").value =
        document.getElementById("ad_" + adgroup_id + "_bid").value;
      document.getElementById("form").submit();
    }
  </script>

  <h1><a href="index.php">Facebook Ads Sample App</a></h1>';

    if ($me) {
      echo '<a href="'.htmlspecialchars($logoutUrl).'">
        <img src="images/logout.gif">
      </a>';
    } else {
      echo '<div>
        Using JavaScript &amp; XFBML:
        <fb:login-button perms="offline_access,ads_management">
        </fb:login-button>
      </div>
      <!-- div>
        Without using JavaScript &amp; XFBML:
        <a href="<?php echo htmlspecialchars($loginUrl); ?>">
        <img src="images/login.gif">
        </a>
      </div -->
    </body>
  </html>';
  exit();
  }

  echo '
  <br/>
  <a href="index.php">Home</a> |
  <a href="create_campaign.php">Create Campaign</a> |
  <a href="create_adgroup.php">Create Ad Group</a> |
  <a href="multi_create_nobatch.php">Multi-create (no-batch)</a><br><br>
  ';

}
