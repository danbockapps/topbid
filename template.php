<?php
function generate_page() {
   if(
      !isset($_GET['league']) &&
      strpos($_SERVER['REQUEST_URI'], "index.php") === false &&
      strpos($_SERVER['REQUEST_URI'], "join_league.php") === false
   ) {
      header("Location: index.php");
   }
   session_start();
   //uses the PHP SDK.  Download from https://github.com/facebook/php-sdk
   require 'facebook-php-sdk/src/facebook.php';
   require_once('pdo_mysql.php');
   require_once("config.php");

   $facebook = new Facebook(array(
   'appId'  => APP_ID,
   'secret' => SECRET,
   ));

   $user_id = $facebook->getUser();

   if($user_id) {
      $user_info = $facebook->api('/' + $user_id);

      // Does this need to be on every page?
      if(!user_already_in_db($user_info)) {
         insert_user($user_info);
      }

      if(!isset($_SESSION['user_id'])) {
         $_SESSION['user_id'] = get_user_id($user_info);
         $_SESSION['user_name'] = $user_info['name'];
      }
   }

   // pageviews table
   $_SESSION['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
   <head>
      <title>Top Bid Fantasy</title>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <link rel="stylesheet" type="text/css" href="<?php echo CSSLINK ?>">
      <?php
         // OPTIONAL: inside head tags
         if(function_exists('head_element_contents')) {
            head_element_contents();
         }
      ?>

      <?php if(ENVIRONMENT == "prod") { ?>
         <!-- Google Analytics -->
         <script type="text/javascript">
         var _gaq = _gaq || [];
         _gaq.push(['_setAccount', 'UA-34054972-1']);
         _gaq.push(['_trackPageview']);

         (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
         })();
         </script>
      <?php } ?>

      <script type="text/javascript" src="ajax.js">
      </script>
      <script type="text/javascript">
      sendDimensions();
      </script>

   </head>
   <?php
      // OPTIONAL: body attributes
      if(function_exists('body_attributes')) {
         body_attributes();
      }
      else {
         ?><body><?php
      }
   ?>
      <div id="container">
         <div id="topbar">
            <img id="logo" alt="logo" src="logo-small.png" />
            <?php if($user_id) { ?>
               <p>
                  Logged in as:
                  <img src="fb-faction-less-icon.png" alt="facebook" />
                  <?php echo $user_info['name']; ?>
               </p>
            <?php } ?>
            <?php if($user_id && isset($_GET['league'])) { // USER IS LOGGED IN ?>
               <ul>
                  <li><a href="index.php?league=<?php
                        echo $_GET['league']
                     ?>">My Leagues</a>
                  </li>
                  <li><a href="team.php?league=<?php
                        echo $_GET['league']
                     ?>&user=<?php
                        echo $_SESSION['user_id']
                     ?>">My Team</a>
                  </li>
                  <li><a href="draft.php?league=<?php
                        echo $_GET['league']
                     ?>">Draft</a>
                  </li>
                  <li><a href="players.php?league=<?php
                        echo $_GET['league']
                     ?>&search_terms=">Players</a>
                  </li>
                  <li><a href="chat.php?league=<?php
                        echo $_GET['league']
                     ?>">Smack Talk</a>
                  </li>
               </ul>
            <?php } ?>
         </div>

         <div id="main">
            <div id="fb-root"></div>
            <?php
            // TODO should this be $user_id && isset($_SESSION['user_id'])?
            if($user_id) {
               // USER IS LOGGED IN
               if(isset($_GET['league'])) {
                  $league_name = pdo_select("
                     select league_name
                     from leagues
                     where league_id=" . $_GET['league']
                  );
                  echo "<h1>Current league: " .
                       $league_name[0]['league_name'] . "</h1>";
               }

               // REQUIRED: page content
               page_content();
            }
            else {
               // USER IS NOT LOGGED IN
               ?>
               <div id="flb">
                  <h1>
                     Please log in.
                  </h1>
                  <div class="fb-login-button" scope="email">
                     Login with Facebook
                  </div>
               </div>
            <?php
            }
            ?>
            <script type="text/javascript" src="facebook_login.js.php">
            </script>
         </div>
      </div>
   </body>
</html>

<?php }


function user_already_in_db($user_profile) {
   $conn_s = mysql_connect("localhost",
                           "topbidfa_select",
                           "MZP7K2DGbzRjmFRW");
   if(!$conn_s) {
      die('Could not connect: ' . mysql_error());
   }
   mysql_select_db("topbidfa_draft", $conn_s);

   $query = "select count(*) as count
             from users
             where fb_id = " . $user_profile['id'];

   $result = mysql_fetch_array(mysql_query($query));
   return $result['count'];
}

function insert_user($user_profile) {
   $conn_i = mysql_connect("localhost",
                           "topbidfa_insert",
                           "X7vCRUuvZnmPeMEx");
   if(!$conn_i) {
      die('Could not connect: ' . mysql_error());
   }
   mysql_select_db("topbidfa_draft", $conn_i);

   $query = "insert into users (fb_id, fb_name, fb_email) values ("
      . $user_profile['id'] . ", '"
      . $user_profile['name'] . "', '"
      . $user_profile['email'] . "')";

   if(!mysql_query($query, $conn_i)) {
      die('Error inserting into database: ' . mysql_error());
   }
}

function get_user_id($user_profile) {
   $conn_s = mysql_connect("localhost",
                           "topbidfa_select",
                           "MZP7K2DGbzRjmFRW");
   if(!$conn_s) {
      die('Could not connect: ' . mysql_error());
   }
   mysql_select_db("topbidfa_draft", $conn_s);

   $query = "
      select user_id
      from users
      where fb_id = " . $user_profile['id'];

   $result = mysql_fetch_array(mysql_query($query));
   return $result['user_id'];
}



?>
