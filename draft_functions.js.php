<?php
header('Content-Type: text/javascript; charset=UTF-8');
?>
function confirmBid(
   playerId
) {
   window.open(
         "place_bid.php?league=" + <?php echo $_GET['league'] ?> +
         "&player=" + playerId,
      "place_bid_window",
         "height=400, width=300, location=no, " +
         "menubar=no, status=no, toolbar=no"
   ).focus();
}

function startAjax(league, reloadsLeft) {
   if(reloadsLeft > 0) {
      $("#bid_table").load("bid_table.php?league=" + league);
      window.setTimeout("startAjax(" + league + ", " + (reloadsLeft - 1) + ");", 1000); 
   }
}

var numReloads = 5400; // 5400 seconds = 90 minutes
$(startAjax(<?php echo $_GET['league']; ?>, numReloads));
