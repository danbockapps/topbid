<?php
session_start();
require_once('join_league_functions.php');

$result = get_league($_POST['league'], $_POST['password']);
if(is_array($result)) {
   $dbh = pdo_connect("topbidfa_insert");
   $sth = $dbh->prepare("
      insert into fantasy_teams (
         fantasy_team_name,
         league_id,
         user_id
      )
      values (?, ?, ?)
   ");

   $team = $_POST['team'];
   $league = $_POST['league'];
   $user = $_SESSION['user_id'];

   $sth->bindParam(1, $team);
   $sth->bindParam(2, $league);
   $sth->bindParam(3, $user);

   try {
      $sth->execute();
   }
   catch(PDOException $e) {
      if(
         strpos($e->getMessage(), "Duplicate entry") != false &&
         strpos($e->getMessage(), "PRIMARY") != false
      ) {
         exit("Error: You are already in that league.");
      }
      else {
         exit($e->getMessage());
      }
   }

   header("Location: index.php?league=" . $_POST['league']);
}
else {
   exit($result . "<br/>");
}
?>
