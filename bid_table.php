<?php
date_default_timezone_set("America/New_York");
define('SHOW_OPEN', 5);
define('SHOW_CLOSED', 5);

require_once("draft_functions.php");
session_start();
$time_start = microtime(true);

$result = bid_result(
   $_GET['league'],
   $_SESSION['user_id'],
   null,
   "order by auction_end, rank asc"
);

// If any draft times are null, no draft for you.
/* Commenting this out for now. 18dec2012
foreach($result as $row) {
   if($row['auction_end'] == null) {
      // To do: make this error message friendlier.
      exit("There are players with no auction end time set.");
   }
}
*/

// This query doesn't have to happen once a second.
$teams_result = pdo_select("
   select
      f.user_id,
      f.fantasy_team_name,
      l.dollar_limit as dollars_available
   from
      fantasy_teams f
      left join leagues l
      using (league_id)
   where league_id=" . $_GET['league']
);

// This can be made more efficient.
$num_players = array();
$next_auction_ending = 0;
foreach($result as $key => $row) {

   // Count the number of players each team has
   if(substr($row['time_remaining'], 0, 1) == '-') {
      if(!isset($num_players[$row['user_id']])) {
         $num_players[$row['user_id']] = 0;
      }
      $num_players[$row['user_id']]++;
   }

   // Piggybacking this loop to find the next auction ending
   else if(
      $next_auction_ending == 0 &&
      substr($result[$key-1]['time_remaining'], 0, 1) == '-'
   ) {
      $next_auction_ending = $key;
   }

   // Count the money each team has
   foreach($teams_result as &$teams_row) {
      if($row['fantasy_team_name'] == $teams_row['fantasy_team_name']) {
         $teams_row['dollars_available'] -= $row['dollars_committed'];
      }
   }
}
?>




<!-- TEAMS AND BUDGETS -->
<h2>Teams and Budgets</h2>
<table id="teams">
<tr>
<?php
// The ampersand is necessary for some reason.
foreach($teams_result as &$teams_row) {
   echo "<th>" . $teams_row['fantasy_team_name'] . "</th>";
}
?>

</tr>
<tr>
<?php
// Here too.
foreach($teams_result as &$teams_row) {
   echo "<td>&#36;" . $teams_row['dollars_available'] . "</td>";
}
?>

</tr>
<tr>
<?php
foreach($teams_result as &$teams_row) {
   echo
      "<td><a href=\"team.php?league=" . $_GET['league'] .
      "&user=" . $teams_row['user_id'] .
      "\">" . (
         !isset($num_players[$teams_row['user_id']]) ? 0 : (
            $num_players[$teams_row['user_id']] == '' ? 0 :
            $num_players[$teams_row['user_id']]
         )
      ) . " players</a></td>";
}
?>

</tr>
</table>



<h2>
   Next 5 auctions ending
   <span class="server_time">
      (Server time: <?php echo date("Y-m-d H:i:s"); ?>)
   </span>
</h2>
<?php
player_table(
   $result, false, true, false,
   $next_auction_ending, $next_auction_ending + SHOW_OPEN - 1
);
?>
<p class="cling_to_above">
   <a href="players.php?league=<?php
      echo $_GET['league'];
   ?>">More players</a>
</p>




<h2>Last 5 auctions ended</h2>
<?php
player_table(
   $result, true, false, false,
   $next_auction_ending - SHOW_CLOSED, $next_auction_ending - 1
);




$time_end = microtime(true);
echo "Bid tables printed in "
   . round($time_end - $time_start, 4)
   . " seconds.";
?>
