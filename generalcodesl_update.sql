-- ***************************************** --
-- *                                       * --
-- * コードマスタデータ更新                 * --
-- *     ver 1.0.0 20200721                * --
-- *                                       * --
-- ***************************************** --

-- <<generalcodes start コードマスタ>> --
  update generalcodes
  set
    physical_name = 'compensation_holiday'
  where
    identification_id = 'C013'
    and code = 4
    and sort_seq = 12
    and is_deleted = 0
  ;
  update generalcodes
  set
    physical_name = 'substitute_holiday'
  where
    identification_id = 'C013'
    and code = 5
    and sort_seq = 13
    and is_deleted = 0
  ;
