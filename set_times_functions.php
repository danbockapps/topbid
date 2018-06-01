<?php

function process_new_times() {
   $num_times_updated = 0;
   foreach($_POST['auction_end'] as $player_id => $auction_end) {

      if($auction_end != '') {
         $conn_i = mysql_connect("localhost",
                                 "topbidfa_inselup",
                                 "JpSB3KterPp947Z3");
         if(!$conn_i) {
            die('Could not connect: ' . mysql_error());
         }
         mysql_select_db("topbidfa_draft", $conn_i);

         $query = "
            insert into player_times (
               player_id,
               league_id,
               auction_end
            )
            values ("
            . $player_id .           ", "
            . $_GET['league'] .      ", '"
            . $auction_end .         "') "
            . "on duplicate key update auction_end = values(auction_end)";

         if(!mysql_query($query, $conn_i)) {
            die('Error inserting into database: ' . mysql_error());
         }

         mysql_close();
         $num_times_updated++;
      }
   }
}

function print_time_table() {
   echo "<form action=\"set_times.php?league=";
   echo $_GET['league'];
   echo "\" method=\"post\">\n";
   ?>

   <table border="1">
      <tr>
         <th>Rank</th>
         <th>Player</th>
         <th>Position</th>
         <th>Team</th>
         <th>Current auction end time</th>
         <th>New auction end time</th>
      </tr>

      <?php

      $conn_s = mysql_connect("localhost",
                           "topbidfa_select",
                           "MZP7K2DGbzRjmFRW");
      if (!$conn_s) {
         die('Could not connect: ' . mysql_error());
      }

      mysql_select_db("topbidfa_draft", $conn_s);

      $result = mysql_query("
         select *
         from players p
         natural left join (
            select *
            from player_times
            where league_id=" . $_GET['league'] . "
            ) d
         where p.rank < 5000
         order by p.rank
       ");

      while($row = mysql_fetch_array($result)) {
         echo "<tr>\n<td>";
         echo $row['rank'];
         echo "</td>\n<td>";
         echo $row['player_name'];
         echo "</td>\n<td>";
         echo $row['position_id'];
         echo "</td>\n<td>";
         echo $row['nfl_team_id'];
         echo "</td>\n<td>";
         echo $row['auction_end'];
         echo "</td>\n<td>";
         echo "<input type=\"text\" name=\"auction_end[";
         echo $row['player_id'];
         echo "]\" id= \"auction_end[";
         echo $row['player_id'];
         echo "]\" value=\"";
         echo $row['auction_end'];
         echo "\" maxlength=\"19\" size=\"19\"/>";
         echo "</td>\n</tr>\n";
      }

      mysql_close($conn_s);

      ?>
   </table>
   <input type="submit" value="Submit">
   </form>
   <?php
}
?>
