create table nfl_teams (
   nfl_team_id varchar(3) primary key
);

create table positions (
   position_id varchar(3) primary key
); 

create table users (
   user_id int unsigned auto_increment primary key,
   fb_id bigint unsigned not null,
   fb_name varchar(100),
   fb_email varchar(100),
   user_create_dttm timestamp,
   pref_outbid_email boolean
);

create table players (
   player_id smallint unsigned auto_increment primary key,
   player_name varchar(100),
   rank smallint unsigned,
   nfl_team_id varchar(3),
   position_id varchar(3),
   url varchar(100),
   constraint foreign key (nfl_team_id) references nfl_teams (nfl_team_id),
   constraint foreign key (position_id) references positions (position_id),
   constraint unique key (rank)
);

create table leagues (
   league_id int unsigned auto_increment primary key,
   league_name varchar(100),
   user_id int unsigned, # commissioner
   dollar_limit decimal(6, 2),
   roster_size tinyint unsigned,
   password varchar(100),
   constraint foreign key (user_id) references users (user_id)
);

create table bids (
   bid_id bigint unsigned auto_increment primary key,
   user_id int unsigned,
   league_id int unsigned,
   player_id smallint unsigned,
   amount decimal(6,2), # maximum $9,999.99
   dttm timestamp,
   constraint foreign key (user_id) references users (user_id),
   constraint foreign key (league_id) references leagues (league_id),
   constraint foreign key (player_id) references players (player_id),
   constraint unique key LPUA (league_id, player_id, user_id, amount),
   constraint unique key LPAD (league_id, player_id, amount, dttm)
);

create table fantasy_teams (
   fantasy_team_name varchar(100),
   user_id int unsigned,
   league_id int unsigned,
   constraint primary key (user_id, league_id),
   constraint foreign key (user_id) references users (user_id),
   constraint foreign key (league_id) references leagues (league_id),
   constraint unique key (league_id, fantasy_team_name)
);

create table player_times (
   player_id smallint unsigned,
   league_id int unsigned,
   auction_end datetime,
   constraint primary key (player_id, league_id),
   constraint foreign key (player_id) references players (player_id),
   constraint foreign key (league_id) references leagues (league_id)
);

create table messages (
   message_id bigint unsigned auto_increment primary key,
   message text,
   league_id int unsigned,
   user_id int unsigned,
   dttm timestamp,
   constraint foreign key (user_id) references users (user_id),
   constraint foreign key (league_id) references leagues (league_id)
);

create table pageviews (
   pageview_id bigint unsigned auto_increment primary key,
   user_id int unsigned,
   request_uri text,
   remote_addr varchar(30),
   user_agent text,
   width int,
   height int,
   dttm timestamp
);

create view vv as
   select
      fb_name,
      dttm,
      request_uri
   from
      pageviews
      natural join users
   order by dttm desc
   limit 20;

create view vb as
   select
      league_id as lid,
      fb_name,
      player_id as pid,
      player_name,
      amount,
      dttm
   from
      bids
      natural join users
      natural join players
   order by dttm desc
   limit 45;

create view bids_plus as
   select
      bids.*,
      players.player_name,
      users.fb_name
   from bids
   natural join players
   natural join users;

create view user_top_bids_nodttm as
   select distinct
      user_id,
      league_id,
      player_id,
      max(amount) as amount
   from bids
   group by
      user_id,
      league_id,
      player_id;

create view user_top_bids as
   select
      a.*,
      b.dttm
   from user_top_bids_nodttm a
   join bids b
   using (
      user_id,
      league_id,
      player_id,
      amount
   )
   order by
      league_id,
      player_id,
      amount desc,
      dttm asc;

create view winning_bids as
   select
      league_id,
      player_id,
      max(amount) as amount
   from bids
   group by
      league_id,
      player_id;

create view winners_dupes as
   select
      user_id,
      league_id,
      player_id,
      amount,
      dttm
   from
      user_top_bids
      natural join winning_bids;

create view winners_tiebreaker as 
   select
      league_id,
      player_id,
      amount,
      min(dttm) as dttm
   from winners_dupes
   group by
      league_id,
      player_id;

create view winners_max_bids as
   select
      league_id,
      player_id,
      user_id,
      amount as first_bid
   from winners_tiebreaker
   natural left join winners_dupes;

create view second_highest_bids as
   select
      league_id,
      player_id,
      max(amount) as second_bid
   from user_top_bids
   where (
      league_id,
      player_id,
      user_id,
      amount
   )
   not in (
      select
         league_id,
         player_id,
         user_id,
         amount
      from winners_max_bids
   )
   group by
      league_id,
      player_id;

create view winners as
   select
      league_id,
      player_id,
      user_id,
      fantasy_team_name,
      first_bid,
      case
         when first_bid is null then null
         when first_bid = second_bid then first_bid
         when second_bid is null then 1
         else second_bid + 1
      end as amount
   from winners_max_bids
   natural join fantasy_teams
   natural left join second_highest_bids;
