<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
   <head>
      <title></title>
      <script type="text/javascript">
         var refreshMillis = 1000;
         var x;
         var lastUpdate;
         var offset;

         function loadXMLDoc(url, cfunc) {
            if (window.XMLHttpRequest) { // code for normal browsers
               x=new XMLHttpRequest();
            }
            else { // code for IE6, IE5
               x=new ActiveXObject("Microsoft.XMLHTTP");
            }
            x.onreadystatechange=cfunc;
            x.open("POST", url, true);
            x.send();
         }

         function setOffset() {
            loadXMLDoc(
               "current_time.php",
               function() {
                  if (x.readyState==4 && x.status==200) {
                     document.getElementById("serverTime").innerHTML =
                        x.responseText * 1000;
                     document.getElementById("localTime").innerHTML =
                        new Date().getTime();
                     offset = new Date().getTime() - x.responseText * 1000;
                     document.getElementById("offset").innerHTML = offset;
                     loadLastUpdateTime(0);
                  }
               }
            );
         }

         function loadLastUpdateTime(lastLocalUpdate) {
            loadXMLDoc(
               "last_update.php",
               function() {
                  if (x.readyState==4 && x.status==200) {
                     lastUpdate = x.responseText;
                     document.getElementById("lastUpdate").innerHTML =
                        x.responseText;
                     if(x.responseText > lastLocalUpdate) {
                        // Update is needed.
                        document.getElementById("needsUpdate").innerHTML = "yes";
                        updateBidTableFromServer();
                     }
                     else if (x.responseText == lastLocalUpdate) {
                        // We already have the latest update
                        document.getElementById("needsUpdate").innerHTML = "no";
                        updateBidTableLocally();
                        window.setTimeout(
                           "loadLastUpdateTime(" + lastUpdate + ");",
                           refreshMillis
                        );
                     }
                     else {
                        // Our update is somehow more recent than the server's
                        document.getElementById("needsUpdate").innerHTML = "ERROR";
                     }
                  }
               }
            );
         }

         function updateBidTableFromServer() {
            loadXMLDoc(
               "bid_table.php?league=1",
               function() {
                  if (x.readyState==4 && x.status==200) {
                     document.getElementById("bidTable").innerHTML = x.responseText;
                     window.setTimeout(
                        "loadLastUpdateTime(" + lastUpdate + ");",
                        refreshMillis
                     );
                  }
               }
            );
         }

         function updateBidTableLocally() {
            var aes = document.getElementsByName("ae");
            var trs = document.getElementsByName("tr");
            for(i=0; i<aes.length; i++) {
               var t = aes[i].innerHTML.split(/[- :]/);
               var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
               if(d.getTime() > new Date().getTime()) {
                  trs[i].innerHTML = Math.round((d.getTime() -
                     new Date().getTime() + offset)/1000);
               }
            }
         }

      </script>
   </head>
   <body onLoad="setOffset();">
      <p>
         Last update: <span id="lastUpdate">loading...</span>
      </p>
      <p>
         Needs update: <span id="needsUpdate">loading...</span>
      </p>
      <p>
         Server time: <span id="serverTime">loading...</span>
      </p>
      <p>
         Local time: <span id="localTime">loading...</span>
      </p>
      <p>
         Offset: <span id="offset">loading...</span>
      </p>

      <div id="bidTable">
         Bid table loading...
      </div>
   </body>
</html>
