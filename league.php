<?php
require("template.php");
generate_page();

function page_content() {
   $conn_s = mysql_connect("localhost",
                           "topbidfa_select",
                           "MZP7K2DGbzRjmFRW");
   if(!$conn_s) {
      die('Could not connect: ' . mysql_error());
   }
   mysql_select_db("topbidfa_draft", $conn_s);

   $query = "
      select league_name
      from leagues
      where league_id = " . $_GET['league'];

   $result = mysql_fetch_array(mysql_query($query));

   echo "<h1>" . $result['league_name'] . "</h1>";

   $query = "
      select
         f.fantasy_team_name, 
         u.fb_name,
         l.user_id = u.user_id as commish,
         u.user_id /* For un-implemented league membership check */
      from fantasy_teams f
      natural join users u
      join leagues l
         using (league_id)
      where f.league_id = " . $_GET['league'];

   $result = mysql_query($query);

   ?>
   <table>
   <tr>
   <th>Team Name</th>
   <th>Owner</th>
   </tr>
   <?php

   while($row = mysql_fetch_array($result)) {
      echo "<tr><td>";
      echo $row['fantasy_team_name'];
      echo "</td><td>";
      echo $row['fb_name'];
      if($row['commish']) {
         echo "*";
      }
      echo "</td></tr>";
   }

   ?>
   </table></br>
   * = commish
<?php
}
?>
