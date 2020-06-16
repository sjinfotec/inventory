-- ***************************************** --
-- *                                       * --
-- * カレンダー情報初期設定                * --
-- *     ver 1.0.0 20200527                * --
-- *                                       * --
-- ***************************************** --

-- <<calendar_setting_informations start カレンダーテーブル>> --
-- << users working_timetable_no set>> --
  update
    calendar_setting_informations t10
  left join (
    select
      t5.date
      , t1.code
      , t1.department_code
      , t1.working_timetable_no
    from users t1
      inner join (
      select
        t4.user_code
        , t4.date
        , max(t4.apply_term_from) as mmax
      from 
      (
        select
          t2.user_code
          , t2.date
          ,t3.working_timetable_no
          ,t3.apply_term_from
        from
          calendar_setting_informations t2
        left join users as t3
          on t3.code = t2.user_code 
          and t3.department_code = t2.department_code
          and t3.is_deleted = 0 
      ) t4
      where 
        t4.date >= t4.apply_term_from
      group by
        t4.user_code
        , t4.date
    ) t5
    on 
      t5.user_code = t1.code
      and t5.mmax = t1.apply_term_from
    where 
      t1.is_deleted = 0
  ) t11
  on
    t10.date = t11.date
	and t10.department_code = t11.department_code
	and t10.user_code = t11.code
  set
    t10.working_timetable_no = t11.working_timetable_no
  where
    t10.working_timetable_no is null;
    
