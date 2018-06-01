<?php
function pdo_connect($db_user) {
   if($db_user == "topbidfa_select")
      $password = "MZP7K2DGbzRjmFRW";
   else if ($db_user == "topbidfa_insert")
      $password = "X7vCRUuvZnmPeMEx";
   else if ($db_user == "topbidfa_inselup")
      $password = "";

   try {
      $dbh = new PDO("mysql:host=localhost;dbname=topbidfa_draft",
                     $db_user,
                     $password);
      $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   }
   catch(PDOException $e) {
      echo $e->getMessage();
   }

   return $dbh;
}

function pdo_select($query) {
   $dbh = pdo_connect("topbidfa_select");
   $sth = $dbh->query($query);
   $dbh = null;
   $sth->setFetchMode(PDO::FETCH_ASSOC);
   return $sth->fetchAll();
}

function pdo_insert($league, $player, $user, $amount) {
   $dbh = pdo_connect("topbidfa_insert");
   $data = array("league" => $league,
                 "player" => $player,
                 "user" => $user,
                 "amount" => $amount
    );
   $sth = $dbh->prepare("
      insert into bids (
         league_id,
         player_id,
         user_id,
         amount
      )
      values (
         :league,
         :player,
         :user,
         :amount
      )
   ");
   $sth->execute($data);
}

function pdo_chat_insert($message, $user, $league) {
   $dbh = pdo_connect("topbidfa_insert");
   $data = array("message" => $message,
                 "user" => $user,
                 "league" => $league
   );
   $sth = $dbh->prepare("
      insert into messages (
         message,
         user_id,
         league_id
      )
      values (
         :message,
         :user,
         :league
      )
   ");
   $sth->execute($data);
}
?>
