<?php

require("template.php");
generate_page();

function page_content() {
   if(isset($_POST['message'])) {
      pdo_chat_insert($_POST['message'], $_SESSION['user_id'], $_GET['league']);
   }
   ?>

   <p>
      <form action="chat.php?league=<?php
         echo $_GET['league'];
         ?>" method="POST">
         Enter a message:
         <input type="text" name="message" size="110" />
         <input type="submit" value="Submit" />
      </form>
   </p>

   <?php
   $result = pdo_select("
      select
         m.message,
         m.dttm,
         u.fb_name
      from
         messages m
         natural join users u
      where m.league_id=" . $_GET['league'] . "
      order by dttm desc
   ");
   
   foreach($result as $row) {
      ?>
      <p class="message">
         <?php echo $row['message']; ?>
      </p>
      <p class="message_credit">
         Posted by <?php echo $row['fb_name']; ?>
         at <?php echo $row['dttm']; ?>
      </p>
      <hr />
      <?php
   }
   
}

?>