<?php


require("template.php");
generate_page();

function body_attributes() {
   echo "<body a=b>";
}

function head_element_contents() {
   ?>
   <script type="text/javascript" src="draft_functions.js.php?league=<?php
      echo $_GET['league'];
   ?>"></script>
   <?php
}

function page_content() {
   echo "<p>this is the inside page. banana banana banana banana banana banana banana banana banana banana banana banana banana banana banana banana banana banana banana banana</p>";
}
?>
