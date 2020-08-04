-- ***************************************** --
-- *                                       * --
-- * コードマスタデータ追加                * --
-- *     ver 1.0.0 20200804                * --
-- *                                       * --
-- ***************************************** --

-- <<generalcodes start コードマスタ>> --
  delete from generalcodes where identification_id = 'C038' and code = 32 and sort_seq = 20 and is_deleted = 0;
  select @row := max(id) from generalcodes;
  set character_set_client = utf8;
  set @identification_name="処理項目選択（メニュー含む）";
  set @description="メニュー";
  insert into generalcodes (
  id
  , identification_id
  , code
  , sort_seq
  , identification_name
  , description
  , physical_name
  , code_name
  , secound_code_name
  , use_free_item
  , created_user
  , updated_user
  , created_at
  , updated_at
  , is_deleted
  ) values ( 
  @row := @row+1
  , 'C038'
  , 32
  , 20
  , @identification_name
  , @description
  , 'setting_employment'
  , ''
  , ''
  , NULL
  , 'systemuser'
  , NULL
  , now()
  , NULL
  , 0
) ;
