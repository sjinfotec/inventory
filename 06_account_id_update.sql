-- ***************************************** --
-- *                                       * --
-- * ACCOUNTID設定                         * --
-- *     ver 1.0.0 20200817                * --
-- *                                       * --
-- ***************************************** --

  set @account_id="SSJJOO00";

-- <<work_times>> --

  update work_times set account_id = @account_id
  ;
