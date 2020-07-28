-- ***************************************** --
-- *                                       * --
-- * CSV項目選定データ更新                 * --
-- *     ver 1.0.0 20200721                * --
-- *                                       * --
-- ***************************************** --

-- <<csv_item_selections start コードマスタ>> --
  update csv_item_selections
    set is_select = 1
  where
    account_id = 'CSD1000L'
    and selection_code = 2
    and is_deleted = 0;
