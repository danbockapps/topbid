var x;

function loadXMLDoc(url, cfunc, method) {
   if (window.XMLHttpRequest) { // code for normal browsers
      x=new XMLHttpRequest();
   }
   else { // code for IE6, IE5
      x=new ActiveXObject("Microsoft.XMLHTTP");
   }
   x.onreadystatechange=cfunc;
   x.open(method, url, true);
   x.send();
}

function sendDimensions() {
   loadXMLDoc(
      "pv_log.php?w=" + screen.width + "&h=" + screen.height,
      function() {},
      "GET"
   );
}
