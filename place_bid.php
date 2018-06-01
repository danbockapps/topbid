<?php
session_start();
require_once('draft_functions.php');

$error_string = "";
$confirmation = "";
$successful_bid = false;
$current_high_bidder = -1;

if(isset($_POST['amount'])) {
   //process new bid
   $_POST['amount'] = trim($_POST['amount']);

   $result = bid_result($_GET['league'], $_SESSION['user_id']);
   $roster_size = pdo_select("
      select roster_size
      from leagues
      where league_id=" . $_GET['league']
   );

   $players_on_roster = 0;

   $player_row = array();
   foreach($result as $row) {
      if($row['player_id'] == $_GET['player']) {
         $player_row = $row;
         if(
            $row['user_id'] != $_SESSION['user_id'] &&
            $row['user_id'] != null
         ) {
            // Set $current_high_bidder only if an email should
            // be sent to that person. Do not send email to the
            // person making this new bid or a null person.
            $current_high_bidder = $row['user_id'];
         }
      }
      if($row['user_id'] == $_SESSION['user_id']) {
         $players_on_roster++;
      }
   }
   

   if(
      $players_on_roster >= $roster_size[0]['roster_size'] &&
      // If I'm already high bidder, I can bid again even with full roster.
      $player_row['user_id'] != $_SESSION['user_id']
   ) {
      $error_string = "You are already high bidder on " .
         $players_on_roster . " players, which is the maximum " .
         "roster size for your league. ";
   }
   else {
      if($_POST['amount'] < 1) {
         $error_string .= "Your bid is not a positive number. ";
      }

      // bid must be an integer.
      else if(!ctype_digit($_POST['amount'])) {
         $error_string .= "Your bid is not an integer. ";
      }

      // auction must not have ended yet.
      if(substr($player_row['time_remaining'], 0, 1) == '-') {
         $error_string .= "The auction for this player has ended. ";
      }

      // bid must be more than current high bid.
      if($_POST['amount'] <= $player_row['high_bid']) {
         $error_string .= "Your bid is too low. ";
      }

      // bid must be > current user max bid.
      if($_POST['amount'] <= $player_row['your_top_bid']) {
         $error_string .= "Your bid must be greater than " .
               "your current max bid for that player. ";
      }
   }

   $amount_being_added = 0;

   if($error_string == "") {
      $my_dollars_committed = 0;
      foreach($result as $row) {
         if($row['user_id'] == $_SESSION['user_id']) {
            $my_dollars_committed += $row['dollars_committed'];
         }
      }

      if($_POST['amount'] != '') { // If I am entering a bid
         if($player_row['user_id'] == $_SESSION['user_id']) { //me high biddr
            $amount_being_added = ($_POST['amount'] - $player_row['first_bid']);
            $confirmation =
               "You have increased your max bid from &#36;" .
               $player_row['first_bid'] . " to &#36;" . $_POST['amount'] .
               ". You are still the high bidder.";
            $successful_bid = true;
         }
         else { //I'm not the current high bidder
            $amount_being_added = $_POST['amount'];
            if($_POST['amount'] > $player_row['first_bid']) {
               //I'm the new high bidder
               $confirmation = "You are the new high bidder.";
               $successful_bid = true;
            }
            else {
               //I have been outbid by an automatic bid
               $confirmation = "You have been outbid by an automatic bid.";
            }
         }
      }

      //to do: unhardcode this.
      if($my_dollars_committed + $amount_being_added > 200) {
         $error_string = "You don't have enough money.";
      }
   }
   // At this point, either error_string or confirmation is non-blank.
}

?>

<!DOCTYPE html>
<html>
   <head>
      <title>Place bid</title>
      <style type="text/css">
         .fail {
            color: red;
            background-color: #fcc;
         }
         .win {
            color: green;
            background-color: #cfc;
         }
         #tip {
            font-size: 80%;
            background-color: #ddd;
         }
      </style>
      <script type="text/javascript">
         window.onload = function() {
            document.getElementById("amount").focus();
         };
      </script>
   </head>
   <body>
      <?php
         if($error_string != "") {
            ?><p class="fail"><?php echo $error_string; ?></p><?php
         }
         else if($confirmation != "") {
            // TODO Make sure user is in the league 
            try {
               pdo_insert(
                  $_GET['league'],
                  $_GET['player'],
                  $_SESSION['user_id'],
                  $_POST['amount']
               );
            }
            catch(PDOException $e) {
               if(strpos($e->getMessage(), "LPUA") == false) {
                  exit($e->getMessage());
               }
               //else, it's a duplicate bid and can be ignored.
            }
            if($successful_bid == false) {
               ?><p class="fail"><?php echo $confirmation; ?></p><?php
            }
            else if($successful_bid == true) {
               ?><p class="win"><?php echo $confirmation; ?></p><?php

               if($current_high_bidder != -1) {
                  // Send outbid notice by email
                  $user_row = pdo_select("
                     select
                        fb_email,
                        pref_outbid_email
                     from users
                     where user_id=" . $current_high_bidder
                  );
                  
                  if($user_row[0]['pref_outbid_email']) {
                     $player_row = pdo_select("
                        select player_name
                        from players
                        where player_id=" . $_GET['player']
                     );
                     
                     $league_row = pdo_select("
                        select league_name
                        from leagues
                        where league_id=" . $_GET['league']
                     );
                     
                     topbid_mail(
                        $user_row[0]['fb_email'],
                        "Top Bid Fantasy Drafts <outbid@topbidfantasy.com>",
                        "You have been outbid for " .
                           $player_row[0]['player_name'] .
                           " on Top Bid Fantasy Drafts",
                        "Someone has outbid you!" .
                           "\nPlayer: " .
                           $player_row[0]['player_name'] .
                           "\nLeague: " .
                           $league_row[0]['league_name'] .
                           "\n\nPlace a new bid now: " .
                           "http://www.topbidfantasy.com/players.php?league=" .
                           $_GET['league'] . "&search_terms=" . 
                           str_replace(" ", "+", $player_row[0]['player_name']) .
                           "\n\nTo unsubscribe: danbock@gmail.com"
                     );
                  }
               }
            }
         }
         else 
      ?>
      <h3>
      <?php
         $result = bid_result(
            $_GET['league'],
            $_SESSION['user_id'],
            "where player_id=" . $_GET['player']
         );
         echo $result[0]['player_name'] .
            " (" . $result[0]['position_id'] . " - " . 
                 $result[0]['nfl_team_id'] . ")";
      ?>
      </h3>

      <p>
      Current top bid: &#36;<?php echo $result[0]['high_bid']; ?><br/>
      Current top bidder: <?php echo $result[0]['fantasy_team_name']; ?>
      </p>
      <p>
      Your max bid:
         <form
            id="one_bid"
            name="one_bid"
            method="post"
            action="place_bid.php?league=<?php
               echo $_GET['league']; 
            ?>&player=<?php
               echo $_GET['player'];
            ?>"
         >
            &#36;<input
               type="text"
               name="amount"
               id="amount"
               size="3"
               maxlength="3"
            />
            <input type="submit" value="Submit"/>
         </form>
      </p>
      
      <p id="tip">
         Tip: enter the <b>maximum</b> amount you are willing to pay for this player.
         Top Bid will <b>automatically</b> outbid other bidders by $1, up to this amount.
      </p>
   
      <p>
         <a href="javascript:self.close()">Close this window</a>
      </p>
      
   </body>
</html>