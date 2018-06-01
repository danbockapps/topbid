<?php
require_once('pdo_mysql.php');
session_start();

$dbh=pdo_connect("topbidfa_insert");

$data = array("user" => $_SESSION['user_id'],
              "uri" => $_SESSION['REQUEST_URI'],
              "ip" => $_SERVER['REMOTE_ADDR'],
              "ua" => $_SERVER['HTTP_USER_AGENT'],
              "w" => $_GET['w'],
              "h" => $_GET['h']
);

$sth = $dbh->prepare("
   insert into pageviews (
      user_id,
      request_uri,
      remote_addr,
      user_agent,
      width,
      height
   )
   values (
      :user,
      :uri,
      :ip,
      :ua,
      :w,
      :h
   )
");

$sth->execute($data);



?>
