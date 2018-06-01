<?php
require_once('pdo_mysql.php');
function get_league($league, $password) {
   try {
      $result = pdo_select("
         select 
            league_id,
            password,
            league_name
         from leagues
         where league_id=" . $league
      );
   } catch(PDOException $e) {
      return "Error: SQL select error.";
   }

   if(count($result) == 0) {
      return "Error: League not found.";
   }
   
   else if(count($result) > 1) {
      //This should never happen.
      return "Error: More than one league selected.";
   }
   
   else if($result[0]['password'] != $password) {
      return "Error: Incorrect password.";
   }
   
   else {
      return $result;
   }
}
?>
