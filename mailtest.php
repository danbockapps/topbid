<?php
require_once("draft_functions.php");
                     topbid_mail(
                        "danbock@gmail.com",
                        "Top Bid Fantasy Drafts <outbid@topbidfantasy.com>",
                        "You have been outbid for " .
                           " on Top Bid Fantasy Drafts",
                        "Someone has outbid you!" .
                           "\nPlayer: " .
                           "\nLeague: " .
                           "\n\nPlace a new bid now: " .
                           "http://www.topbidfantasy.com/players.php?league=" .
                           "\n\nTo unsubscribe: danbock@gmail.com"
                     );

?>
