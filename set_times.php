<?php
require_once("set_times_functions.php");
session_start();

if(!isset($_GET['league'])) {
   exit("No 'league' index in _GET array.");
}

if(!isset($_SESSION['user_id'])) {
   exit("Not logged in.");
}

if(isset($_POST['auction_end'])) {
   process_new_times();
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <title></title>
      <script type="text/javascript" src="set_times_functions.js">
      </script>
   </head>
   <body>
      Date to start:
      <input type="text" id="month_start" maxlength="2" size="2" value="7"/>
      &#47;
      <input type="text" id="day_start" maxlength="2" size="2" value="1"/>
      &#47;
      <input type="text" id="year_start" maxlength="4" size="4" value="2012"/>
      <br/>
      
      Earliest time of day:
      <input type="text" id="hour_start" maxlength="2" size="2" value="12"/>
      &#58;
      <input type="text" id="minute_start" maxlength="2" size="2" value="00"/>
      <br/>
      
      Latest time of day:
      <input type="text" id="hour_end" maxlength="2" size="2" value="13"/>
      &#58;
      <input type="text" id="minute_end" maxlength="2" size="2" value="00"/>
      <br/>
      
      Interval (in minutes):
      <input type="text" id="interval" maxlength="4" size="4" value="5"/>
      <br/>
      
      <!-- To do: change hard-coded "4" below-->
      <button onclick="fillAuctionEnd(1100)">Click here</button>
      
      <?php
      print_time_table();
      ?>
   </body>
</html>
