-- ***************************************** --
-- *                                       * --
-- * CSV項目選定データ追加                 * --
-- *     ver 1.0.0 20200721                * --
-- *                                       * --
-- ***************************************** --

-- <<generalcodes start コードマスタ>> --
  set character_set_client = utf8;
  set @kanji_item1='代替休日日数';
  set @out_item1='代休日数';
  set @kanji_item2='振替休日日数';
  set @out_item2='振休日数';
  delete from csv_item_selections where account_id = 'SSJJOO00' and selection_code = '1' and item_code in (28,29);
  delete from csv_item_selections where account_id = 'SD03TA00' and selection_code = '1' and item_code in (28,29);
  delete from csv_item_selections where account_id = 'CSD1000L' and selection_code = '1' and item_code in (28,29);
  delete from csv_item_selections where account_id = 'S02DTA00' and selection_code = '1' and item_code in (28,29);
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
  , 'SSJJOO00'
  , 1
  , 28
  , 23
  , 'total_substitute_holiday'
  , @kanji_item1
  , @out_item1
  , 0
  , 'systemuser'
  , ''
  , now()
  , NULL
  , 0
) ,
 ( 
  @row := @row+1
  , 'SSJJOO00'
  , 1
  , 29
  , 24
  , 'total_compensation_holiday'
  , @kanji_item2
  , @out_item2
  , 0
  , 'systemuser'
  , ''
  , now()
  , NULL
  , 0
) ,
 ( 
  @row := @row+1
  , 'SD03TA00'
  , 1
  , 28
  , 23
  , 'total_substitute_holiday'
  , @kanji_item1
  , @out_item1
  , 0
  , 'systemuser'
  , ''
  , now()
  , NULL
  , 0
) ,
 ( 
  @row := @row+1
  , 'SD03TA00'
  , 1
  , 29
  , 24
  , 'total_compensation_holiday'
  , @kanji_item2
  , @out_item2
  , 0
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
  , 28
  , 23
  , 'total_substitute_holiday'
  , @kanji_item1
  , @out_item1
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
  , @kanji_item2
  , @out_item2
  , 1
  , 'systemuser'
  , ''
  , now()
  , NULL
  , 0
) ,
 ( 
  @row := @row+1
  , 'S02DTA00'
  , 1
  , 28
  , 23
  , 'total_substitute_holiday'
  , @kanji_item1
  , @out_item1
  , 0
  , 'systemuser'
  , ''
  , now()
  , NULL
  , 0
) ,
 ( 
  @row := @row+1
  , 'S02DTA00'
  , 1
  , 29
  , 24
  , 'total_compensation_holiday'
  , @kanji_item2
  , @out_item2
  , 0
  , 'systemuser'
  , ''
  , now()
  , NULL
  , 0
) ;
