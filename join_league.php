<?php
require("template.php");
generate_page();

function page_content() {
   require_once('join_league_functions.php');

   $result = get_league($_GET['league_to_join'], $_GET['password']);
   if(is_array($result)) {
      echo "You are joining <b>" . $result[0]['league_name'] . "</b>";
      ?>
      <form id="join_league" method="post" action="join_league_insert.php">

      League ID:
      <input
         type="text"
         name="league"
         value="<?php echo $_GET['league_to_join']; ?>"
      >
      <br/>

      Password:
      <input
         type="text"
         name="password"
         value="<?php echo $_GET['password']; ?>"
      >
      <br/>

      Your team name:
      <input type="text" name="team">
      <br/>

      <input type="submit" value="Submit">
      </form>
      <?php
   }
   else {
      echo $result;
   }   
}



?>
