<?php

require("template.php");
generate_page();

function head_element_contents() {
   ?>
   <script type="text/javascript" src="draft_functions.js.php?league=<?php
      echo $_GET['league'];
   ?>"></script>
   <?php
}

function page_content() {
   require_once("draft_functions.php");
   if(!isset($_GET['league'])) {
      exit("No 'league' index in _GET array.");
   }
   ?>
   <p>
      <form class="players" method="get" action="players.php">
         <input type="hidden" 
                name="league"
                value="<?php echo $_GET['league']; ?>" />
         <input class="filter"
                type="text" 
                name="search_terms"
            <?php
            if(isset($_GET['search_terms'])) {
               echo "value=\"" . $_GET['search_terms'] . "\"";
            }
            ?>
         />
         <input type="submit" value="Search" />
         <a href="players.php?league=<?php
               echo $_GET['league'] . "&search_terms=";
            ?>">
            All players
         </a>
      </form>
   </p>
   <p>
      <form class="players" method="get" action="players.php">
         <input type="hidden" 
                name="league"
                value="<?php echo $_GET['league']; ?>" />
         <select class="filter" name="st">
            <option value="ava" <?php opt_def("st", "ava"); ?>>
               All available players
            </option>
            <option value="all" <?php opt_def("st", "all"); ?>>
               All players
            </option>
         </select>
         
         <select name="pos">
            <option value="all" <?php opt_def("pos", "all"); ?>>
               All positions
            </option>
            <option value="DEF" <?php opt_def("pos", "DEF"); ?>>
               DEF
            </option>
            <option value="K" <?php opt_def("pos", "K"); ?>>
               K
            </option>
            <option value="QB" <?php opt_def("pos", "QB"); ?>>
               QB
            </option>
            <option value="RB" <?php opt_def("pos", "RB"); ?>>
               RB
            </option>
            <option value="TE" <?php opt_def("pos", "TE"); ?>>
               TE
            </option>
            <option value="WR" <?php opt_def("pos", "WR"); ?>>
               WR
            </option>
         </select>
         <!-- This will be implemented later.
         <input class="filter"
                type="checkbox"
                name="my" />Show my team
         -->
         <input type="submit"
                value="Filter" />
      </form>
   </p>
   <?php
   if(isset($_GET['search_terms'])) {
      $result = bid_result(
         $_GET['league'],
         $_SESSION['user_id'],
         "where player_name like '%" . $_GET['search_terms'] . "%'",
         order_by_clause()
      );
      player_table($result, true);
   }
   else if(isset($_GET['st']) || isset($_GET['pos'])) {
      $print_ended = false;
      if(isset($_GET['st']) && $_GET['st'] == "all") {
         $print_ended = true;
      }
      
      $where_clause = null;
      if(isset($_GET['pos']) && $_GET['pos'] != "all") {
         $where_clause = "where position_id='" . $_GET['pos'] . "'";
      }
      
      $result = bid_result(
         $_GET['league'],
         $_SESSION['user_id'],
         $where_clause,
         order_by_clause()
      );
      player_table($result, $print_ended);
   }
}

//Short for "option - default"
function opt_def($select, $option) {
   if(isset($_GET[$select]) && $_GET[$select] == $option) {
      echo "selected=\"selected\"";
   }
}

?>
