<?php

require("template.php");
generate_page();

function head_element_contents() {
   ?>
   <script
   src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js">
   </script>
   <script type="text/javascript" src="draft_functions.js.php?league=<?php
      echo $_GET['league'];
   ?>"></script>
   <?php
}

/*
function body_attributes() {
   echo "<body onLoad=\"startAjax(" . $_GET['league'] . ");\">";
}
*/

function page_content() {
   require_once("draft_functions.php");
   if(!isset($_GET['league'])) {
      exit("No 'league' index in _GET array.");
   }
   
   ?>
   <div id="bid_table">
      Loading...
   </div>   
   <?php
}
?>
