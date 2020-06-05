-- ***************************************** --
-- *                                       * --
-- * カレンダー情報初期設定test            * --
-- *     ver 1.0.0 20200527                * --
-- *                                       * --
-- ***************************************** --

-- <<calendar_setting_informations start カレンダーテーブル>> --
  SET @row =0;
  select
    @row:=@row+1
    , t1.date
    , t1.department_code
    , t1.employment_status
    , t1.user_code
    , t1.weekday_kubun
    , t1.business_kubun
    , t2.working_timetable_no
    , ifnull(t6.holiday_kubun,0)
    , t1.created_user
    , t1.updated_user
    , t1.created_at
    , t1.updated_at
    , t1.is_deleted
  from calendars t1
    left join shift_informations t2
    on
      t2.target_date = t1.date
      and t2.department_code = t1.department_code
      and t2.user_code = t1.user_code
      and t2.is_deleted = 0
    left join (
      select
        t3.working_date,
        t3.department_code,
        t3.user_code,
        t3.holiday_kubun
      from user_holiday_kubuns t3
      inner join (
        select
          t4.working_date,
          t4.department_code,
          t4.user_code,
          min(t4.id) as mid
        from user_holiday_kubuns t4
        where t4.is_deleted = 0
        group by
          t4.working_date,
          t4.department_code,
          t4.user_code
      ) t5
      on  t3.id = t5.mid
    ) t6
    on
      t6.working_date = t1.date
      and t6.department_code = t1.department_code
      and t6.user_code = t1.user_code
  where
    t1.is_deleted = 0
  INTO OUTFILE '/var/lib/mysql-files/select_calendar.log';
  
