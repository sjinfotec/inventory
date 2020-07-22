-- ***************************************** --
-- *                                       * --
-- * CSV項目選定データ追加                 * --
-- *     ver 1.0.0 20200721                * --
-- *                                       * --
-- ***************************************** --

-- <<generalcodes start コードマスタ>> --
  delete from csv_item_selections where account_id = 'CSD1000L' and selection_code = '1' and item_code in (28,29);
  select @row := max(id) from csv_item_selections;
  insert into csv_item_selections (
  id
  , account_id
  , selection_code
  , item_code
  , item_seq
  , item_name
  , item_kanji_name
  , item_out_name
  , is_select
  , created_user
  , updated_user
  , created_at
  , updated_at
  , is_deleted
  ) values ( 
  @row := @row+1
  , 'CSD1000L'
  , 1
  , 28
  , 23
  , 'total_substitute_holiday'
  , '代替休日日数'
  , '代休日数'
  , 1
  , 'systemuser'
  , ''
  , now()
  , NULL
  , 0
) ,
 ( 
  @row := @row+1
  , 'CSD1000L'
  , 1
  , 29
  , 24
  , 'total_compensation_holiday'
  , '振替休日日数'
  , '振休日数'
  , 1
  , 'systemuser'
  , ''
  , now()
  , NULL
  , 0
) ;
