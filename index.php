<?php

require("template.php");
generate_page();

function page_content() {
   $result = pdo_select("
      select
         league_id,
         league_name
      from leagues
      where league_id in (
         select league_id
         from fantasy_teams
         where user_id = " . $_SESSION['user_id'] . "
      )
   ");
   
   if(count($result) > 0) {
      ?>
      <div id="league_list">
      <h2>Your leagues</h2>
      <ul>
      <?php
      foreach($result as $row) {
         echo "<li>";
         echo $row['league_name'];
         echo " <a href=\"league.php?league=";
         echo $row['league_id'];
         echo "\">teams</a> <a href=\"draft.php?league=";
         echo $row['league_id'];
         echo "\">draft</a></li>";
      }
      ?>
      </ul>
      </div>
      <?php
   }
   ?>

   <p id="p_join_league">
   <form id="join_league" method="get" action="join_league.php">
   <em>Join a league</em><br/>
   League ID: <input type="text" name="league_to_join"><br/>
   Password: <input type="text" name="password"><br/>
   <input type="submit" value="Submit">
   </form>
   </p>
   <?php
}

?>