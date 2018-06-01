function twoDigits(d) {
   if(0 <= d && d < 10) return "0" + d.toString();
   if(-10 < d && d < 0) return "-0" + (-1*d).toString();
   return d.toString();
}

function fillAuctionEnd(numPlayers) {
   var monthStart = document.getElementById("month_start").value - 1;
   var dayStart = document.getElementById("day_start").value;
   var yearStart = document.getElementById("year_start").value;
   var hourStart = document.getElementById("hour_start").value;
   var minuteStart = document.getElementById("minute_start").value;
   var hourEnd = document.getElementById("hour_end").value;
   var minuteEnd = document.getElementById("minute_end").value;
   var interval = document.getElementById("interval").value;
   var startDate = new Date(yearStart, monthStart, dayStart);
   var startTime = startDate.getTime();
   var draftDate;
   var draftDateString;
   var numPlayersAssgd = 0;
   var j = 0;
   var day = 0;
   var endGtStart;

   if(
      (hourEnd == hourStart && minuteEnd > minuteStart) ||
      (hourEnd > hourStart)
   ) {
      endGtStart = true;
   }
   else {
      endGtStart = false;
   }

   while(numPlayersAssgd < numPlayers) {
      draftDate = new Date(
         startTime + 
         day * 1000 * 60 * 60 * 24 +
         hourStart * 1000 * 60 * 60 +
         minuteStart * 1000 * 60 +
         j++ * interval * 1000 * 60
      );
      
      if(
         (
            endGtStart && (
               draftDate.getHours() < hourEnd || (
                  draftDate.getHours() == hourEnd &&
                  draftDate.getMinutes() <= minuteEnd
               )
            )
         )
         || (
            !endGtStart && (
               (
                  draftDate.getHours() > hourStart || (
                     draftDate.getHours() == hourStart &&
                     draftDate.getMinutes() >= minuteStart
                  )
               )
               || (
                  draftDate.getHours() < hourEnd || (
                     draftDate.getHours() == hourEnd &&
                     draftDate.getMinutes() <= minuteEnd
                  )
               )
            )
         )
      ) {
         draftDateString =
            draftDate.getFullYear() + "-" +
            twoDigits(draftDate.getMonth() + 1) + "-" +
            twoDigits(draftDate.getDate()) + " " +
            twoDigits(draftDate.getHours()) + ":" +
            twoDigits(draftDate.getMinutes()) + ":00";
         document
            .getElementById("auction_end[" + (++numPlayersAssgd) + "]")
            .setAttribute("value", draftDateString); 
      }
      else {
         day++;
         j = 0;
      }
   }
}