<?php
if(strpos($_SERVER['HTTP_HOST'], "topbidfantasy.com") === false) {
   // dev
   define("APP_ID", "383580835027931");
   define("SECRET", "55350d1f1ebadd62243ccaae81835f3e");
   define("CSSLINK", "draft.css?reload");
   define("CHANNEL", "http://danbock.servebeer.com/draft/channel.php");
   define("ENVIRONMENT", "dev");
}
else {
   // prod
   define("APP_ID", "336196596464308");
   define("SECRET", "eda5f34e2dda7864cde38e138a26c59c");
   define("CSSLINK", "draft.css");
   define("CHANNEL", "http://www.topbidfantasy.com/channel.php");
   define("ENVIRONMENT", "prod");
}

define("EMAIL_LOGGER", "email_logger@topbidfantasy.com");
define("OUTBID_PASSWORD", "No_break_Hazel");

?>