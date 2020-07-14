-- ***************************************** --
-- *                                       * --
-- * カレンダー情報エラーリスト            * --
-- *     ver 1.0.0 20200527                * --
-- *                                       * --
-- ***************************************** --
-- * <<1. shift_informations>> * --
  select
    t1.target_date as shift_informations_date,
    t1.department_code as shift_informations_department_code,
    t1.user_code as shift_informations_user_code,
    t2.date as calendars_date
  from shift_informations t1
  left join calendars t2
  on
    t2.date = t1.target_date
    and t2.department_code = t1.department_code
    and t2.user_code = t1.user_code
    and t2.is_deleted = 0
  where
    t1.is_deleted = 0
    and t2.date is null
  INTO OUTFILE '/var/lib/mysql-files/shift_informations.log';

-- * <<2. user_holiday_kubuns>> * --
  select
    t1.working_date as user_holiday_kubuns_working_date,
    t1.department_code as user_holiday_kubuns_department_code,
    t1.user_code as user_holiday_kubuns_user_code,
    t2.date as calendars_date
  from user_holiday_kubuns t1
  left join calendars t2
  on
    t2.date = t1.working_date
    and t2.department_code = t1.department_code
    and t2.user_code = t1.user_code
    and t2.is_deleted = 0
  where
    t1.is_deleted = 0
    and t2.date is null
  INTO OUTFILE '/var/lib/mysql-files/user_holiday_kubuns.log';

-- * <<3. shift_informations>> * --
  select 
    t1.department_code as department_code,
    t1.user_code as user_code,
    t1.target_date as target_date,
    t1.rcnt
  from (
    select
      t2.department_code as department_code,
      t2.user_code as user_code,
      t2.target_date as target_date,
      count(*) as rcnt
    from shift_informations t2
    where
      t2.is_deleted = 0
    group by t2.department_code,t2.user_code,t2.target_date
  ) t1
  where t1.rcnt > 1
  INTO OUTFILE '/var/lib/mysql-files/shift_informations_dup.log';


-- * <<4. user_holiday_kubuns>> * --
-- * user_holiday_kubunsはみなし系が2件作成されているのでエラーとはならない * --
  select 
    t1.department_code as department_code,
    t1.user_code as user_code,
    t1.working_date as working_date,
    t1.rcnt
  from (
    select
      t2.department_code as department_code,
      t2.user_code as user_code,
      t2.working_date as working_date,
      count(*) as rcnt
    from user_holiday_kubuns t2
    where
      t2.is_deleted = 0
    group by t2.department_code,t2.user_code,t2.working_date
  ) t1
  where t1.rcnt > 1
  INTO OUTFILE '/var/lib/mysql-files/user_holiday_kubuns_dup.log';



