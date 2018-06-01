use topbidfa_draft
drop view if exists winners;
drop view if exists second_highest_bids;
drop view if exists winners_max_bids;
drop view if exists winners_tiebreaker;
drop view if exists winners_dupes;
drop view if exists winning_bids;
drop view if exists user_top_bids;
drop view if exists user_top_bids_nodttm;
drop view if exists bids_plus;
drop table if exists messages;
drop table if exists player_times;
drop table if exists fantasy_teams;
drop table if exists bids;
drop table if exists leagues;
drop table if exists players;
drop table if exists users;
drop table if exists positions;
drop table if exists nfl_teams;
source draft.sql
source insert_draft.sql
