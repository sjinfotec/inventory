-- ***************************************** --
-- *                                       * --
-- * 機能選択データ追加                    * --
-- *     ver 1.0.0 20200811                * --
-- *                                       * --
-- ***************************************** --

-- <<feature_item_selections start コードマスタ>> --
  delete from feature_item_selections where item_code = 9 and is_deleted = 0;
  select @row := max(id) from feature_item_selections;
  set character_set_client = utf8;
  set @item_kanji_name="アカウントID有効";
  set @description="アクセスにアカウントIDを含める";
  insert into feature_item_selections (
  id
  , account_id
  , selection_code
  , item_code
  , item_seq
  , item_name
  , item_kanji_name
  , item_out_name
  , value_select
  , description
  , created_user
  , updated_user
  , created_at
  , updated_at
  , is_deleted
  ) values ( 
  @row := @row+1
  , 'SSJJOO00'
  , 1
  , 9
  , 1
  , 'account_id_valid'
  , @item_kanji_name
  , ''
  , 0
  , @description
  , 'systemuser'
  , NULL
  , now()
  , NULL
  , 0
) ;
  insert into feature_item_selections (
  id
  , account_id
  , selection_code
  , item_code
  , item_seq
  , item_name
  , item_kanji_name
  , item_out_name
  , value_select
  , description
  , created_user
  , updated_user
  , created_at
  , updated_at
  , is_deleted
  ) values ( 
  @row := @row+1
  , 'SSJJOO00'
  , 2
  , 9
  , 1
  , 'account_id_valid'
  , @item_kanji_name
  , ''
  , 0
  , @description
  , 'systemuser'
  , NULL
  , now()
  , NULL
  , 0
) ;
  insert into feature_item_selections (
  id
  , account_id
  , selection_code
  , item_code
  , item_seq
  , item_name
  , item_kanji_name
  , item_out_name
  , value_select
  , description
  , created_user
  , updated_user
  , created_at
  , updated_at
  , is_deleted
  ) values ( 
  @row := @row+1
  , 'SSJJOO00'
  , 3
  , 9
  , 1
  , 'account_id_valid'
  , @item_kanji_name
  , ''
  , 0
  , @description
  , 'systemuser'
  , NULL
  , now()
  , NULL
  , 0
) ;
  insert into feature_item_selections (
  id
  , account_id
  , selection_code
  , item_code
  , item_seq
  , item_name
  , item_kanji_name
  , item_out_name
  , value_select
  , description
  , created_user
  , updated_user
  , created_at
  , updated_at
  , is_deleted
  ) values ( 
  @row := @row+1
  , 'SSJJOO00'
  , 5
  , 9
  , 1
  , 'account_id_valid'
  , @item_kanji_name
  , ''
  , 0
  , @description
  , 'systemuser'
  , NULL
  , now()
  , NULL
  , 0
) ;
  insert into feature_item_selections (
  id
  , account_id
  , selection_code
  , item_code
  , item_seq
  , item_name
  , item_kanji_name
  , item_out_name
  , value_select
  , description
  , created_user
  , updated_user
  , created_at
  , updated_at
  , is_deleted
  ) values ( 
  @row := @row+1
  , 'SD03TA00'
  , 3
  , 9
  , 1
  , 'account_id_valid'
  , @item_kanji_name
  , ''
  , 0
  , @description
  , 'systemuser'
  , NULL
  , now()
  , NULL
  , 0
) ;
  insert into feature_item_selections (
  id
  , account_id
  , selection_code
  , item_code
  , item_seq
  , item_name
  , item_kanji_name
  , item_out_name
  , value_select
  , description
  , created_user
  , updated_user
  , created_at
  , updated_at
  , is_deleted
  ) values ( 
  @row := @row+1
  , 'CSD1000L'
  , 5
  , 9
  , 1
  , 'account_id_valid'
  , @item_kanji_name
  , ''
  , 1
  , @description
  , 'systemuser'
  , NULL
  , now()
  , NULL
  , 0
) ;
  insert into feature_item_selections (
  id
  , account_id
  , selection_code
  , item_code
  , item_seq
  , item_name
  , item_kanji_name
  , item_out_name
  , value_select
  , description
  , created_user
  , updated_user
  , created_at
  , updated_at
  , is_deleted
  ) values ( 
  @row := @row+1
  , 'S02DTA00'
  , 3
  , 9
  , 1
  , 'account_id_valid'
  , @item_kanji_name
  , ''
  , 0
  , @description
  , 'systemuser'
  , NULL
  , now()
  , NULL
  , 0
) ;
