select
   *,
   timediff(auction_end, now()) as time_remaining,
   case
      when auction_end < now() then high_bid
      when user_id <> 1 then high_bid
      else your_top_bid
   end as dollars_committed
from (
   select
      1 as league_id,
      player_id,
      player_name,
      rank,
      nfl_team_id,
      position_id,
      user_id,
      fantasy_team_name,
      first_bid,
      high_bid,
      your_top_bid
   from players p
   natural left join (
      select
         league_id,
         player_id,
         user_id,
         fantasy_team_name,
         first_bid,
         amount as high_bid
      from winners
      where league_id=1
   ) w
   left join (
      select
         league_id,
         player_id,
         amount as your_top_bid
      from user_top_bids
      where user_id=1
   ) u
   using (
      player_id,
      league_id
   )
) a
natural left join player_times