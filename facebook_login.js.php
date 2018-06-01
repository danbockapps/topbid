<?php
Header("content-type: application/x-javascript");
require_once("config.php");
?>

window.fbAsyncInit = function() {
   FB.init({
      appId      : '<?php echo APP_ID ?>', // App ID
      channelUrl : '<?php echo CHANNEL; ?>', // Channel File
      status     : true, // check login status
      cookie     : true, // enable cookies to allow the server to access the session
      xfbml      : true  // parse XFBML
   });
   FB.Event.subscribe('auth.login', function(response) {
      window.location.reload();
   });
};
// Load the SDK Asynchronously
(function(d){
   var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
   if (d.getElementById(id)) {
      return;
   }
   js = d.createElement('script');
   js.id = id;
   js.async = true;
   js.src = "//connect.facebook.net/en_US/all.js";
   ref.parentNode.insertBefore(js, ref);
}(document));