-- ***************************************** --
-- *                                       * --
-- * メニュー項目選定データ追加            * --
-- *     ver 1.0.0 20200721                * --
-- *                                       * --
-- ***************************************** --

-- <<generalcodes start コードマスタ>> --
  delete from menu_item_selections where account_id = 'SSJJOO00' and selection_code = '1' and item_code in (32);
  delete from menu_item_selections where account_id = 'SSJJOO00' and selection_code = '2' and item_code in (32);
  delete from menu_item_selections where account_id = 'SSJJOO00' and selection_code = '3' and item_code in (32);
  delete from menu_item_selections where account_id = 'SD03TA00' and selection_code = '3' and item_code in (32);
  delete from menu_item_selections where account_id = 'CSD1000L' and selection_code = '5' and item_code in (32);
  delete from menu_item_selections where account_id = 'S02DTA00' and selection_code = '3' and item_code in (32);
  select @row := max(id) from menu_item_selections;
  insert into menu_item_selections (
  id
  , account_id
  , selection_code
  , item_code
  , item_name
  , item_kanji_name
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
  , 32
  , 'setting_employment'
  , '雇用形態設定'
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
  , 2
  , 32
  , 'setting_employment'
  , '雇用形態設定'
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
  , 3
  , 32
  , 'setting_employment'
  , '雇用形態設定'
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
  , 3
  , 32
  , 'setting_employment'
  , '雇用形態設定'
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
  , 5
  , 32
  , 'setting_employment'
  , '雇用形態設定'
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
  , 3
  , 32
  , 'setting_employment'
  , '雇用形態設定'
  , 0
  , 'systemuser'
  , ''
  , now()
  , NULL
  , 0
) ;
