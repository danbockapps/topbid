<?php
require("template.php");
generate_page();

function head_element_contents() {
   ?>
   <script type="text/javascript" src="draft_functions.js.php?league=<?php
      echo $_GET['league'];
   ?>"></script>
   <?php
}

function page_content() {
   require_once("draft_functions.php");
   if(!isset($_GET['league'])) {
      exit("No 'league' index in _GET array.");
   }
   if(!isset($_GET['user'])) {
      exit("No 'user' index in _GET array.");
   }
   
   $team_name = pdo_select("
      select fantasy_team_name as f
      from fantasy_teams
      where user_id=" . $_GET['user'] . "
      and league_id=" . $_GET['league']
   );
   
   echo "<h2>" . $team_name[0]['f'] . ": closed auctions</h2>";
   $result = bid_result(
      $_GET['league'],
      $_SESSION['user_id'],
      "where user_id=" . $_GET['user'] .
      " and timediff(auction_end, now()) < 0",
      order_by_clause()
   );
   player_table($result);
   
   echo "<h2>Open auctions: players " . $team_name[0]['f'] . " have bid on</h2>";
   $result = bid_result(
      $_GET['league'],
      $_SESSION['user_id'],
      "where player_id in (
         select distinct player_id
         from bids
         where user_id=" . $_GET['user'] . "
         and league_id=" . $_GET['league'] . "
      )
      and timediff(auction_end, now()) >= 0",
      order_by_clause()
   );
   player_table($result);
}