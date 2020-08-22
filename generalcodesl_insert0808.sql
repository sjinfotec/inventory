-- ***************************************** --
-- *                                       * --
-- * コードマスタデータ追加                * --
-- *     ver 1.0.0 20200804                * --
-- *                                       * --
-- ***************************************** --

-- <<generalcodes start コードマスタ>> --
  delete from generalcodes where identification_id = 'C044' and is_deleted = 0;
  select @row := max(id) from generalcodes;
  set character_set_client = utf8;
  set @identification_name="問い合わせ種類";
  set @description="顧客の問い合わせ区分";
  set @code_name1="無料お試し";
  set @secound_code_name1="お試し";
  set @code_name1="御見積・ご相談";
  set @secound_code_name1="見積・相談";
  set @code_name1="申し込み";
  set @secound_code_name1="申込";
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
  , 'C044'
  , 1
  , 1
  , @identification_name
  , @description
  , 'entry_type_free'
  , @code_name1
  , @secound_code_name1
  , NULL
  , 'systemuser'
  , NULL
  , now()
  , NULL
  , 0
) ;
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
  , 'C044'
  , 2
  , 2
  , @identification_name
  , @description
  , 'entry_type_estimate'
  , @code_name2
  , @secound_code_name2
  , NULL
  , 'systemuser'
  , NULL
  , now()
  , NULL
  , 0
) ;
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
  , 'C044'
  , 3
  , 3
  , @identification_name
  , @description
  , 'entry_type_entry'
  , @code_name3
  , @secound_code_name3
  , NULL
  , 'systemuser'
  , NULL
  , now()
  , NULL
  , 0
) ;
