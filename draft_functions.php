<?php
error_reporting(E_ALL ^ E_STRICT);
require_once('pdo_mysql.php');

function bid_result(
   $league_id,
   $user_id,
   $where_clause=null,
   $order_by_clause=null
) {
   if($user_id > 0) {
      return pdo_select("
         select
            *,
            timediff(auction_end, now()) as time_remaining,
            case
               when auction_end < now() then high_bid
               when user_id <> " . $user_id . " then high_bid
               else your_top_bid
            end as dollars_committed
         from (
            select
               " . $league_id . " as league_id,
               player_id,
               player_name,
               rank,
               nfl_team_id,
               position_id,
               user_id,
               fantasy_team_name,
               first_bid,
               high_bid,
               your_top_bid
            from players p
            natural left join (
               select
                  league_id,
                  player_id,
                  user_id,
                  fantasy_team_name,
                  first_bid,
                  amount as high_bid
               from winners
               where league_id=" . $league_id . "
            ) w
            left join (
               select
                  league_id,
                  player_id,
                  amount as your_top_bid
               from user_top_bids
               where user_id=" . $user_id . "
            ) u
            using (
               player_id,
               league_id
            )
         ) a
         natural join player_times "
         . $where_clause . " "
         . $order_by_clause
      );
   }
   else {
      //TODO Log these errors.
      //TODO Figure out why this is happening.
      //This happens on place_bid.php after the referring page has
      //been sitting for a while.
      exit("Your session has timed out. Please reload the page.");
   }
}

function player_table(
   $result,
   $print_closed=true,
   $print_open=true,
   $sortable=true,
   $start=null,
   $end=null
) {
   ?>
   
<table>
<tr>
<th><?php player_th("Rank", $sortable, "rank"); ?></th>
<th><?php player_th("Player", $sortable, "player_name"); ?></th>
<th><?php player_th("Position", $sortable, "position_id"); ?></th>
<th><?php player_th("Team", $sortable, "nfl_team_id"); ?></th>
<th><?php player_th("Current Top Bid", $sortable, "high_bid"); ?></th>
<th><?php player_th("Top Bidder", $sortable, "fantasy_team_name"); ?></th>
<th><?php player_th("Your Max Bid", $sortable, "your_top_bid"); ?></th>
<th>New Bid</th>
<th><?php player_th("Auction End Time", $sortable, "auction_end"); ?></th>
<th><?php player_th("Time Remaining", $sortable, "time_remaining"); ?></th>
</tr>

      <?php

      foreach($result as $key => $row) {
         if(
            ($start == null || $key >= $start)
            &&
            ($end == null || $key <= $end)
            &&
            (
               ($print_closed && substr($row['time_remaining'], 0, 1) == '-')
               ||
               ($print_open && substr($row['time_remaining'], 0, 1) != '-')
            )
         ) {
            if($row['high_bid'] == null) {
               $row['high_bid'] = " - ";
            }
            if($row['your_top_bid'] == null) {
               $row['your_top_bid'] = " - ";
            }
            ?>
<tr>
<td align="center"><?php echo $row['rank']; ?></td>
<td><?php echo $row['player_name']; ?></td>
<td align="center"><?php echo $row['position_id']; ?></td>
<td align="center"><?php echo $row['nfl_team_id']; ?></td>
<td align="center">&#36;<?php echo $row['high_bid']; ?></td>
<td<?php
   if($row['user_id'] == $_SESSION['user_id']) {
      ?> class="mine"<?php
   }
   ?>
><?php echo $row['fantasy_team_name']; ?></td>
<td align="center">&#36;<?php echo $row['your_top_bid']; ?></td>
<td align="center">
   <?php if(substr($row['time_remaining'], 0, 1) != '-') { ?>
   <button onclick="confirmBid(
      '<?php echo $row['player_id']; ?>'
   );">New bid</button>
   <?php } ?>
</td>
<td name="ae" align="center"><?php echo $row['auction_end']; ?></td>
<td name="tr" align="center">
<?php
   if(substr($row['time_remaining'], 0, 1) != '-') {
      echo $row['time_remaining'];
   }
   else {
      echo "Ended";
   }
   ?>
</td>
</tr><?php
         }
      }
      ?>
   </table>
   
   <?php
}

// use this for sorting
function player_th($column_name, $sortable, $column_var=null) {
   if($sortable) {
      echo "<a href=\"";
      echo "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      echo "&sort=" . $column_var . "\">";
   }
   
   echo $column_name;
   
   if($sortable) {
      echo "<img src=\"sort_icon.png\" alt=\"sort\" /></a>";
   }
}

function order_by_clause() {
   if(isset($_GET['sort'])) {
      return "order by " . $_GET['sort'];
   }
   else {
      return "order by rank";
   }
}

function topbid_mail($to, $from, $subject, $body) {
   require_once("Mail.php");
   require_once("config.php");
   
   /* mail setup recipients, subject etc */
   $bcc = EMAIL_LOGGER;
   $recipients = $to.",".$bcc;
   $headers["From"] = $from;
   $headers["To"] = $to;
   $headers["Subject"] = $subject;
   $mailmsg = $body;
   
   /* SMTP server name, port, user/passwd */
   $smtpinfo["host"] = "mail.topbidfantasy.com";
   $smtpinfo["port"] = "26";
   $smtpinfo["auth"] = true;
   $smtpinfo["username"] = "outbid+topbidfantasy.com";
   $smtpinfo["password"] = OUTBID_PASSWORD;
   
   /* Create the mail object using the Mail::factory method */
   $mail_object =& Mail::factory("smtp", $smtpinfo);
   
   /* Ok send mail */
   $mail_object->send($recipients, $headers, $mailmsg);
}

?>
