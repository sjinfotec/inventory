-- ***************************************** --
-- *                                       * --
-- * PrimaryKey作成                        * --
-- *     ver 1.0.0 20200817                * --
-- *                                       * --
-- ***************************************** --

-- <<work_times>> --
  ALTER TABLE work_times ADD PRIMARY KEY (account_id, user_code, department_code, id)
  ;
