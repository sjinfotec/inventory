<template>
  <div>
    <!-- .row -->
    <div class="row">
      <div class="col-12">
        <div class="table-responsive">
          <table class="table table-striped border-bottom font-size-sm text-nowrap">
            <thead>
              <tr v-if="detailOrTotal === 'detail' && btnMode ==='basicswitch'" bgcolor="#e3f0fb">
                <td class="text-center align-middle mw-rem-8">部署</td>
                <td class="text-center align-middle mw-rem-10">雇用形態</td>
                <td class="text-center align-middle mw-rem-10">氏名</td>
                <td class="text-left align-middle mw-rem-4">出勤</td>
                <td class="text-left align-middle mw-rem-4">退勤</td>
                <td class="text-left align-middle mw-rem-4">公外</td>
                <td class="text-left align-middle mw-rem-4">公外戻</td>
                <td class="text-left align-middle mw-rem-4">私外</td>
                <td class="text-left align-middle mw-rem-4">私外戻</td>
                <td class="text-center align-middle mw-rem-5">勤務状態</td>
                <td class="text-center align-middle mw-rem-10">勤務帯</td>
                <td
                  class="text-center align-middle mw-rem-5 color-royalblue"
                  data-toggle="tooltip"
                  data-placement="top"
                  v-bind:title="edtString"
                  @mouseover="edttooltips('実働時間 = 所定＋残業時間＋深夜残業', 'または','法定休日・法定外休日')"
                >実働時間</td>
                <!--  <td class="text-center align-middle css-fukidashi"
                  @mouseover="edttooltips('実働時間 = 所定 + ',predeterTimeName,predeterNightTimeName,'')">
                  <span class="text">実働時間</span>
                  <span class="fukidashi">{{ edtString }}</span>
                </td>-->
                <td class="text-center align-middle mw-rem-3">所定</td>
                <!-- <td class="text-center align-middle mw-rem-5">{{ predeterTimeSecondName }}</td>
                <td class="text-center align-middle mw-rem-5">{{ predeterNightTimeSecondName }}</td>-->
                <td class="text-center align-middle mw-rem-5">残業時間</td>
                <td class="text-center align-middle mw-rem-5">深夜残業</td>
                <td class="text-center align-middle mw-rem-5">法定休日</td>
                <!-- <td class="text-center align-middle mw-rem-5">法休深夜</td> -->
                <td class="text-center align-middle mw-rem-5">法定外休日</td>
                <!-- <td class="text-center align-middle mw-rem-5">法外休深夜</td> -->
                <td class="text-center align-middle mw-rem-3">深夜労働</td>
                <td
                  class="text-center align-middle mw-rem-5 color-royalblue"
                  data-toggle="tooltip"
                  data-placement="top"
                  v-bind:title="edtString"
                  @mouseover="edttooltips('所定時間内での','遅刻・早退・欠勤・私用外出などで労働時間に含めない（不就労）時間','給与控除対象','')"
                >不就労時間</td>
                <td class="text-center align-middle mw-rem-15">備考</td>
              </tr>
              <tr v-if="detailOrTotal === 'detail' && btnMode ==='detailswitch'" bgcolor="#e3f0fb">
                <td class="text-center align-middle mw-rem-8">部署</td>
                <td class="text-center align-middle mw-rem-10">雇用形態</td>
                <td class="text-center align-middle mw-rem-10">氏名</td>
                <td class="text-left align-middle mw-rem-4">出勤</td>
                <td class="text-center align-middle mw-rem-10">編集氏名</td>
                <td class="text-left align-middle mw-rem-4">退勤</td>
                <td class="text-center align-middle mw-rem-10">編集氏名</td>
                <td class="text-left align-middle mw-rem-4">公外</td>
                <td class="text-center align-middle mw-rem-10">編集氏名</td>
                <td class="text-left align-middle mw-rem-4">公外戻</td>
                <td class="text-center align-middle mw-rem-10">編集氏名</td>
                <td class="text-left align-middle mw-rem-4">私外</td>
                <td class="text-center align-middle mw-rem-10">編集氏名</td>
                <td class="text-left align-middle mw-rem-4">私外戻</td>
                <td class="text-center align-middle mw-rem-10">編集氏名</td>
                <td class="text-center align-middle mw-rem-5">勤務状態</td>
                <td class="text-center align-middle mw-rem-10">勤務帯</td>
                <td
                  class="text-center align-middle mw-rem-5 color-royalblue"
                  data-toggle="tooltip"
                  data-placement="top"
                  v-bind:title="edtString"
                  @mouseover="edttooltips('実働時間 = 所定＋残業時間＋深夜残業', 'または','法定休日・法定外休日')"
                >実働時間</td>
                <td class="text-center align-middle mw-rem-3">所定</td>
                <td class="text-center align-middle mw-rem-3">所定外</td>
                <!-- <td class="text-center align-middle mw-rem-5">{{ predeterTimeSecondName }}</td>
                <td class="text-center align-middle mw-rem-5">{{ predeterNightTimeSecondName }}</td>-->
                <td class="text-center align-middle mw-rem-5">残業時間</td>
                <td class="text-center align-middle mw-rem-5">深夜残業</td>
                <td class="text-center align-middle mw-rem-5">法定休日</td>
                <!-- <td class="text-center align-middle mw-rem-5">法休深夜</td> -->
                <td class="text-center align-middle mw-rem-5">法定外休日</td>
                <!-- <td class="text-center align-middle mw-rem-5">法外休深夜</td> -->
                <td class="text-center align-middle mw-rem-3">深夜労働</td>
                <td class="text-center align-middle mw-rem-3">法定</td>
                <td class="text-center align-middle mw-rem-3">法定外</td>
                <td
                  class="text-center align-middle mw-rem-5 color-royalblue"
                  data-toggle="tooltip"
                  data-placement="top"
                  v-bind:title="edtString"
                  @mouseover="edttooltips('所定時間内での','遅刻・早退・欠勤・私用外出などで労働時間に含めない（不就労）時間','給与控除対象','')"
                >不就労時間</td>
                <td class="text-center align-middle mw-rem-15">備考</td>
              </tr>
            </thead>
            <tbody>
              <tr
                v-if="detailOrTotal === 'detail' && btnMode ==='basicswitch'"
                v-for="(calcList,index) in calcLists"
              >
                <td class="text-left align-middle mw-rem-8">{{ calcList.department_name }}</td>
                <td class="text-left align-middle mw-rem-10">{{ calcList.employment_status_name }}</td>
                <td class="text-left align-middle mw-rem-10">{{ calcList.user_name }}</td>
                <!-- 出勤 -->
                <daily-working-info-time-table
                  v-bind:calc-list="{
                    x_positions: calcList.x_attendance_time_positions,
                    y_positions: calcList.y_attendance_time_positions,
                    editor_department_code: calcList.attendance_editor_department_code,
                    editor_department_name: calcList.attendance_editor_department_name,
                    editor_user_name: calcList.attendance_editor_user_name,
                    working_time: calcList.attendance_time,
                    holiday_description: calcList.holiday_description
                  }"
                  v-bind:login-user="loginUser"
                  v-bind:login-role="loginRole"
                  v-bind:account-data="accountData"
                  v-bind:menu-data="menuData"
                  v-bind:user-index="userindex"
                  v-bind:usercon-index="userconindex"
                  v-bind:ssjjoo-id="ssjjoo_id"
                  v-bind:edituser-id="edit_user_id"
                  v-on:click-event="showMap(
                      calcList.attendance_time,
                      calcList.user_name,
                      calcList.x_attendance_time_positions,
                      calcList.y_attendance_time_positions,
                      index,
                      mode_attendance)"
                ></daily-working-info-time-table>
                <!-- /出勤 -->
                <!-- 退勤 -->
                <daily-working-info-time-table
                  v-bind:calc-list="{
                    x_positions: calcList.x_leaving_time_positions,
                    y_positions: calcList.y_leaving_time_positions,
                    editor_department_code: calcList.leaving_editor_department_code,
                    editor_department_name: calcList.leaving_editor_department_name,
                    editor_user_name: calcList.attendance_editor_user_name,
                    working_time: calcList.leaving_time,
                    holiday_description: calcList.holiday_description
                  }"
                  v-bind:login-user="loginUser"
                  v-bind:login-role="loginRole"
                  v-bind:account-data="accountData"
                  v-bind:menu-data="menuData"
                  v-bind:user-index="userindex"
                  v-bind:usercon-index="userconindex"
                  v-bind:ssjjoo-id="ssjjoo_id"
                  v-bind:edituser-id="edit_user_id"
                  v-on:click-event="showMap(
                      calcList.leaving_time,
                      calcList.user_name,
                      calcList.x_leaving_time_positions,
                      calcList.y_leaving_time_positions,
                      index,
                      mode_leaving)"
                ></daily-working-info-time-table>
                <!-- /退勤 -->
                <!-- 公用外出　開始 -->
                <daily-working-info-time-table
                  v-bind:calc-list="{
                    x_positions: calcList.x_public_going_out_time_positions,
                    y_positions: calcList.y_public_going_out_time_positions,
                    editor_department_code: calcList.public_editor_department_code,
                    editor_department_name: calcList.public_editor_department_name,
                    editor_user_name: calcList.public_editor_user_name,
                    working_time: calcList.public_going_out_time,
                    holiday_description: calcList.holiday_description
                  }"
                  v-bind:login-user="loginUser"
                  v-bind:login-role="loginRole"
                  v-bind:account-data="accountData"
                  v-bind:menu-data="menuData"
                  v-bind:user-index="userindex"
                  v-bind:usercon-index="userconindex"
                  v-bind:ssjjoo-id="ssjjoo_id"
                  v-bind:edituser-id="edit_user_id"
                  v-on:click-event="showMap(
                      calcList.public_going_out_time,
                      calcList.user_name,
                      calcList.x_public_going_out_time_positions,
                      calcList.y_public_going_out_time_positions,
                      index,
                      mode_official_out_start)"
                ></daily-working-info-time-table>
                <!-- /公用外出　終了 -->
                <!-- 公用外出戻り　開始 -->
                <daily-working-info-time-table
                  v-bind:calc-list="{
                    x_positions: calcList.x_public_going_out_return_time_positions,
                    y_positions: calcList.y_public_going_out_return_time_positions,
                    editor_department_code: calcList.public_return_editor_department_code,
                    editor_department_name: calcList.public_return_editor_department_name,
                    editor_user_name: calcList.public_return_editor_user_name,
                    working_time: calcList.public_going_out_return_time,
                    holiday_description: calcList.holiday_description
                  }"
                  v-bind:login-user="loginUser"
                  v-bind:login-role="loginRole"
                  v-bind:account-data="accountData"
                  v-bind:menu-data="menuData"
                  v-bind:user-index="userindex"
                  v-bind:usercon-index="userconindex"
                  v-bind:ssjjoo-id="ssjjoo_id"
                  v-bind:edituser-id="edit_user_id"
                  v-on:click-event="showMap(
                      calcList.public_going_out_return_time,
                      calcList.user_name,
                      calcList.x_public_going_out_return_time_positions,
                      calcList.y_public_going_out_return_time_positions,
                      index,
                      mode_official_out_end)"
                ></daily-working-info-time-table>
                <!-- /公用外出戻り　終了 -->
                <!-- 私用外出　開始 -->
                <daily-working-info-time-table
                  v-bind:calc-list="{
                    x_positions: calcList.x_missing_middle_time_positions,
                    y_positions: calcList.y_missing_middle_time_positions,
                    editor_department_code: calcList.missing_editor_department_code,
                    editor_department_name: calcList.missing_editor_department_name,
                    editor_user_name: calcList.missing_editor_user_name,
                    working_time: calcList.missing_middle_time,
                    holiday_description: calcList.holiday_description
                  }"
                  v-bind:login-user="loginUser"
                  v-bind:login-role="loginRole"
                  v-bind:account-data="accountData"
                  v-bind:menu-data="menuData"
                  v-bind:user-index="userindex"
                  v-bind:usercon-index="userconindex"
                  v-bind:ssjjoo-id="ssjjoo_id"
                  v-bind:edituser-id="edit_user_id"
                  v-on:click-event="showMap(
                      calcList.missing_middle_time,
                      calcList.user_name,
                      calcList.x_missing_middle_time_positions,
                      calcList.y_missing_middle_time_positions,
                      index,
                      mode_private_out_start)"
                ></daily-working-info-time-table>
                <!-- /私用外出　終了 -->
                <!-- 私用外出戻り　開始 -->
                <daily-working-info-time-table
                  v-bind:calc-list="{
                    x_positions: calcList.x_missing_middle_return_time_positions,
                    y_positions: calcList.y_missing_middle_return_time_positions,
                    editor_department_code: calcList.missing_return_editor_department_code,
                    editor_department_name: calcList.missing_return_editor_department_name,
                    editor_user_name: calcList.missing_return_editor_user_name,
                    working_time: calcList.missing_middle_return_time,
                    holiday_description: calcList.holiday_description
                  }"
                  v-bind:login-user="loginUser"
                  v-bind:login-role="loginRole"
                  v-bind:account-data="accountData"
                  v-bind:menu-data="menuData"
                  v-bind:user-index="userindex"
                  v-bind:usercon-index="userconindex"
                  v-bind:ssjjoo-id="ssjjoo_id"
                  v-bind:edituser-id="edit_user_id"
                  v-on:click-event="showMap(
                      calcList.missing_middle_return_time,
                      calcList.user_name,
                      calcList.x_missing_middle_return_time_positions,
                      calcList.y_missing_middle_return_time_positions,
                      index,
                      mode_private_out_end)"
                ></daily-working-info-time-table>
                <!-- /私用外出戻り　終了 -->
                <!-- 勤務状態 -->
                <td
                  class="text-center align-middle mw-rem-5"
                  v-if="calcList.holiday_description === '1日集計対象休暇'"
                >{{ calcList.working_status_name }}</td>
                <td
                  class="text-center align-middle mw-rem-5"
                  v-else
                >{{ calcList.working_status_name }}</td>
                <!-- /勤務状態 -->
                <!-- タイムテーブル名 -->
                <td class="text-center align-middle mw-rem-10">{{ calcList.working_timetable_name }}</td>
                <!-- /タイムテーブル名 -->
                <!-- 実働時間 -->
                <td
                  class="text-center align-middle mw-rem-5"
                  data-toggle="tooltip"
                  data-placement="top"
                  v-bind:title="edtString"
                  @mouseover="edttooltips('実働時間 = 所定＋残業時間＋深夜残業', 'または','法定休日・法定外休日')"
                >{{ calcList.total_working_times }}</td>
                <!-- /実働時間 -->
                <!-- 所定労働時間 -->
                <td class="text-center align-middle mw-rem-3">{{ calcList.regular_working_times }}</td>
                <!-- /所定労働時間 -->
                <!-- 時間外労働時間 -->
                <td
                  class="text-center align-middle mw-rem-5"
                  v-if="calcList.business_kubun !== '2' && calcList.business_kubun !== '3'"
                >{{ calcList.off_hours_working_hours }}</td>
                <td class="text-center align-middle mw-rem-5" v-else>00:00</td>
                <!-- /時間外労働時間 -->
                <!-- 深夜残業時間 -->
                <td
                  class="text-center align-middle mw-rem-5"
                  v-if="calcList.business_kubun !== '2' && calcList.business_kubun !== '3'"
                >{{ calcList.late_night_overtime_hours }}</td>
                <td class="text-center align-middle mw-rem-5" v-else>00:00</td>
                <!-- /深夜残業時間 -->
                <!-- 法定休日労働時間 -->
                <td
                  class="text-center align-middle mw-rem-5"
                  v-if="calcList.business_kubun === '2'"
                >{{ calcList.legal_working_holiday_hours }}</td>
                <td
                  class="text-center align-middle mw-rem-5"
                  v-else-if="calcList.business_kubun === ''"
                ></td>
                <td class="text-center align-middle mw-rem-5" v-else>00:00</td>
                <!-- /法定休日労働時間 -->
                <!-- 法定休日深夜残業時間 -->
                <!-- <td
                  class="text-center align-middle mw-rem-5"
                  v-if="calcList.business_kubun === '2'"
                >{{ calcList.legal_working_holiday_night_overtime_hours }}</td>
                <td
                  class="text-center align-middle mw-rem-5"
                  v-else-if="calcList.business_kubun === ''"
                ></td>
                <td
                  class="text-center align-middle mw-rem-5"
                  v-else
                >00:00</td>-->
                <!-- /法定休日深夜残業時間 -->
                <!-- 法定外（所定休日）休日労働時間 -->
                <td
                  class="text-center align-middle mw-rem-5"
                  v-if="calcList.business_kubun === '3'"
                >{{ calcList.out_of_legal_working_holiday_hours }}</td>
                <td
                  class="text-center align-middle mw-rem-5"
                  v-else-if="calcList.business_kubun === ''"
                ></td>
                <td class="text-center align-middle mw-rem-5" v-else>00:00</td>
                <!-- /法定外（所定休日）休日労働時間
                <!-- 法定外（所定休日）休日深夜残業時間-->
                <!-- <td
                  class="text-center align-middle mw-rem-5"
                  v-if="calcList.business_kubun === '3'"
                >{{ calcList.out_of_legal_working_holiday_night_overtime_hours }}</td>
                <td
                  class="text-center align-middle mw-rem-5"
                  v-else-if="calcList.business_kubun === ''"
                ></td>
                <td
                  class="text-center align-middle mw-rem-5"
                  v-else
                >00:00</td>-->
                <!-- /法定外（所定休日）休日深夜残業時間 -->
                <!-- 深夜労働時間 -->
                <td
                  class="text-center align-middle mw-rem-3"
                >{{ calcList.late_night_working_hours }}</td>
                <!-- /深夜労働時間 -->
                <!-- 未就労労働時間 -->
                <td
                  class="text-center align-middle mw-rem-5"
                  data-toggle="tooltip"
                  data-placement="top"
                  v-bind:title="edtString"
                  @mouseover="edttooltips('所定時間内での','遅刻・早退・欠勤・私用外出などで労働時間に含めない（不就労）時間','給与控除対象','')"
                >{{ calcList.not_employment_working_hours }}</td>
                <!-- /未就労労働時間 -->
                <!-- 備考 -->
                <td
                  class="text-left align-middle mw-rem-15"
                >{{ calcList.remark_holiday_name }} {{ calcList.remark_check_result }} {{ calcList.remark_check_max_times }} {{ calcList.remark_check_interval }}</td>
                <!-- /備考 -->
              </tr>
              <tr
                v-if="detailOrTotal === 'detail' && btnMode ==='detailswitch'"
                v-for="(calcList,index) in calcLists"
              >
                <td class="text-left align-middle mw-rem-8">{{ calcList.department_name }}</td>
                <td class="text-left align-middle mw-rem-10">{{ calcList.employment_status_name }}</td>
                <td class="text-left align-middle mw-rem-10">{{ calcList.user_name }}</td>
                <!-- 出勤 -->
                <daily-working-info-time-table
                  v-bind:calc-list="{
                    x_positions: calcList.x_attendance_time_positions,
                    y_positions: calcList.y_attendance_time_positions,
                    editor_department_code: calcList.attendance_editor_department_code,
                    editor_department_name: calcList.attendance_editor_department_name,
                    editor_user_name: calcList.attendance_editor_user_name,
                    working_time: calcList.attendance_time,
                    holiday_description: calcList.holiday_description
                  }"
                  v-bind:login-user="loginUser"
                  v-bind:login-role="loginRole"
                  v-bind:account-data="accountData"
                  v-bind:menu-data="menuData"
                  v-bind:user-index="userindex"
                  v-bind:usercon-index="userconindex"
                  v-bind:ssjjoo-id="ssjjoo_id"
                  v-bind:edituser-id="edit_user_id"
                  v-on:click-event="showMap(
                      calcList.attendance_time,
                      calcList.user_name,
                      calcList.x_attendance_time_positions,
                      calcList.y_attendance_time_positions,
                      index,
                      mode_attendance)"
                ></daily-working-info-time-table>
                <td
                  class="text-left text-align-left mw-rem-10"
                  v-if="menuData[userindex]['is_select'] === 1 && calcList.attendance_editor_department_name"
                >{{ calcList.attendance_editor_department_name }}：{{ calcList.attendance_editor_user_name }}</td>
                <td
                  class="text-left text-align-left mw-rem-10"
                  v-else-if="menuData[userconindex]['is_select'] === 1 && calcList.attendance_editor_department_name && accountData === ssjjoo_id && loginUser === edit_user_id"
                >{{ calcList.attendance_editor_department_name }}：{{ calcList.attendance_editor_user_name }}</td>
                <td class="text-left text-align-left mw-rem-10" v-else></td>
                <!-- /出勤 -->
                <!-- 退勤 -->
                <daily-working-info-time-table
                  v-bind:calc-list="{
                    x_positions: calcList.x_leaving_time_positions,
                    y_positions: calcList.y_leaving_time_positions,
                    editor_department_code: calcList.leaving_editor_department_code,
                    editor_department_name: calcList.leaving_editor_department_name,
                    editor_user_name: calcList.attendance_editor_user_name,
                    working_time: calcList.leaving_time,
                    holiday_description: calcList.holiday_description
                  }"
                  v-bind:login-user="loginUser"
                  v-bind:login-role="loginRole"
                  v-bind:account-data="accountData"
                  v-bind:menu-data="menuData"
                  v-bind:user-index="userindex"
                  v-bind:usercon-index="userconindex"
                  v-bind:ssjjoo-id="ssjjoo_id"
                  v-bind:edituser-id="edit_user_id"
                  v-on:click-event="showMap(
                      calcList.leaving_time,
                      calcList.user_name,
                      calcList.x_leaving_time_positions,
                      calcList.y_leaving_time_positions,
                      index,
                      mode_leaving)"
                ></daily-working-info-time-table>
                <td
                  class="text-left text-align-left mw-rem-10"
                  v-if="menuData[userindex]['is_select'] === 1 && calcList.leaving_editor_department_name"
                >{{ calcList.leaving_editor_department_name }}：{{ calcList.leaving_editor_user_name }}</td>
                <td
                  class="text-left text-align-left mw-rem-10"
                  v-else-if="menuData[userconindex]['is_select'] === 1 && calcList.leaving_editor_department_name && accountData === ssjjoo_id && loginUser === edit_user_id"
                >{{ calcList.leaving_editor_department_name }}：{{ calcList.leaving_editor_user_name }}</td>
                <td class="text-left text-align-left mw-rem-10" v-else></td>
                <!-- /退勤 -->
                <!-- 公用外出　開始 -->
                <daily-working-info-time-table
                  v-bind:calc-list="{
                    x_positions: calcList.x_public_going_out_time_positions,
                    y_positions: calcList.y_public_going_out_time_positions,
                    editor_department_code: calcList.public_editor_department_code,
                    editor_department_name: calcList.public_editor_department_name,
                    editor_user_name: calcList.public_editor_user_name,
                    working_time: calcList.public_going_out_time,
                    holiday_description: calcList.holiday_description
                  }"
                  v-bind:login-user="loginUser"
                  v-bind:login-role="loginRole"
                  v-bind:account-data="accountData"
                  v-bind:menu-data="menuData"
                  v-bind:user-index="userindex"
                  v-bind:usercon-index="userconindex"
                  v-bind:ssjjoo-id="ssjjoo_id"
                  v-bind:edituser-id="edit_user_id"
                  v-on:click-event="showMap(
                      calcList.public_going_out_time,
                      calcList.user_name,
                      calcList.x_public_going_out_time_positions,
                      calcList.y_public_going_out_time_positions,
                      index,
                      mode_official_out_start)"
                ></daily-working-info-time-table>
                <td
                  class="text-left text-align-left mw-rem-10"
                  v-if="menuData[userindex]['is_select'] === 1 && calcList.public_editor_department_name"
                >{{ calcList.public_editor_department_name }}：{{ calcList.public_editor_user_name }}</td>
                <td
                  class="text-left text-align-left mw-rem-10"
                  v-else-if="menuData[userconindex]['is_select'] === 1 && calcList.public_editor_department_name && accountData === ssjjoo_id && loginUser === edit_user_id"
                >{{ calcList.public_editor_department_name }}：{{ calcList.public_editor_user_name }}</td>
                <td class="text-left text-align-left mw-rem-10" v-else></td>
                <!-- /公用外出　終了 -->
                <!-- 公用外出戻り　開始 -->
                <daily-working-info-time-table
                  v-bind:calc-list="{
                    x_positions: calcList.x_public_going_out_return_time_positions,
                    y_positions: calcList.y_public_going_out_return_time_positions,
                    editor_department_code: calcList.public_return_editor_department_code,
                    editor_department_name: calcList.public_return_editor_department_name,
                    editor_user_name: calcList.public_return_editor_user_name,
                    working_time: calcList.public_going_out_return_time,
                    holiday_description: calcList.holiday_description
                  }"
                  v-bind:login-user="loginUser"
                  v-bind:login-role="loginRole"
                  v-bind:account-data="accountData"
                  v-bind:menu-data="menuData"
                  v-bind:user-index="userindex"
                  v-bind:usercon-index="userconindex"
                  v-bind:ssjjoo-id="ssjjoo_id"
                  v-bind:edituser-id="edit_user_id"
                  v-on:click-event="showMap(
                      calcList.public_going_out_return_time,
                      calcList.user_name,
                      calcList.x_public_going_out_return_time_positions,
                      calcList.y_public_going_out_return_time_positions,
                      index,
                      mode_official_out_end)"
                ></daily-working-info-time-table>
                <td
                  class="text-left text-align-left mw-rem-10"
                  v-if="menuData[userindex]['is_select'] === 1 && calcList.public_return_editor_department_name"
                >{{ calcList.public_return_editor_department_name }}：{{ calcList.public_return_editor_user_name }}</td>
                <td
                  class="text-left text-align-left mw-rem-10"
                  v-else-if="menuData[userconindex]['is_select'] === 1 && calcList.public_return_editor_department_name && accountData === ssjjoo_id && loginUser === edit_user_id"
                >{{ calcList.public_return_editor_department_name }}：{{ calcList.public_return_editor_user_name }}</td>
                <td class="text-left text-align-left mw-rem-10" v-else></td>
                <!-- /公用外出戻り　終了 -->
                <!-- 私用外出　開始 -->
                <daily-working-info-time-table
                  v-bind:calc-list="{
                    x_positions: calcList.x_missing_middle_time_positions,
                    y_positions: calcList.y_missing_middle_time_positions,
                    editor_department_code: calcList.missing_editor_department_code,
                    editor_department_name: calcList.missing_editor_department_name,
                    editor_user_name: calcList.missing_editor_user_name,
                    working_time: calcList.missing_middle_time,
                    holiday_description: calcList.holiday_description
                  }"
                  v-bind:login-user="loginUser"
                  v-bind:login-role="loginRole"
                  v-bind:account-data="accountData"
                  v-bind:menu-data="menuData"
                  v-bind:user-index="userindex"
                  v-bind:usercon-index="userconindex"
                  v-bind:ssjjoo-id="ssjjoo_id"
                  v-bind:edituser-id="edit_user_id"
                  v-on:click-event="showMap(
                      calcList.missing_middle_time,
                      calcList.user_name,
                      calcList.x_missing_middle_time_positions,
                      calcList.y_missing_middle_time_positions,
                      index,
                      mode_private_out_start)"
                ></daily-working-info-time-table>
                <td
                  class="text-left text-align-left mw-rem-10"
                  v-if="menuData[userindex]['is_select'] === 1 && calcList.missing_editor_department_name"
                >{{ calcList.missing_editor_department_name }}：{{ calcList.missing_editor_user_name }}</td>
                <td
                  class="text-left text-align-left mw-rem-10"
                  v-else-if="menuData[userconindex]['is_select'] === 1 && calcList.missing_editor_department_name && accountData === ssjjoo_id && loginUser === edit_user_id"
                >{{ calcList.missing_editor_department_name }}：{{ calcList.missing_editor_user_name }}</td>
                <td class="text-left text-align-left mw-rem-10" v-else></td>
                <!-- /私用外出　終了 -->
                <!-- 私用外出戻り　開始 -->
                <daily-working-info-time-table
                  v-bind:calc-list="{
                    x_positions: calcList.x_missing_middle_return_time_positions,
                    y_positions: calcList.y_missing_middle_return_time_positions,
                    editor_department_code: calcList.missing_return_editor_department_code,
                    editor_department_name: calcList.missing_return_editor_department_name,
                    editor_user_name: calcList.missing_return_editor_user_name,
                    working_time: calcList.missing_middle_return_time,
                    holiday_description: calcList.holiday_description
                  }"
                  v-bind:login-user="loginUser"
                  v-bind:login-role="loginRole"
                  v-bind:account-data="accountData"
                  v-bind:menu-data="menuData"
                  v-bind:user-index="userindex"
                  v-bind:usercon-index="userconindex"
                  v-bind:ssjjoo-id="ssjjoo_id"
                  v-bind:edituser-id="edit_user_id"
                  v-on:click-event="showMap(
                      calcList.missing_middle_return_time,
                      calcList.user_name,
                      calcList.x_missing_middle_return_time_positions,
                      calcList.y_missing_middle_return_time_positions,
                      index,
                      mode_private_out_end)"
                ></daily-working-info-time-table>
                <td
                  class="text-left text-align-left mw-rem-10"
                  v-if="menuData[userindex]['is_select'] === 1 && calcList.missing_return_editor_department_name"
                >{{ calcList.missing_return_editor_department_name }}：{{ calcList.missing_return_editor_user_name }}</td>
                <td
                  class="text-left text-align-left mw-rem-10"
                  v-else-if="menuData[userconindex]['is_select'] === 1 && calcList.missing_return_editor_department_name && accountData === ssjjoo_id && loginUser === edit_user_id"
                >{{ calcList.missing_return_editor_department_name }}：{{ calcList.missing_return_editor_user_name }}</td>
                <td class="text-left text-align-left mw-rem-10" v-else></td>
                <!-- /私用外出戻り　終了 -->
                <!-- 勤務状態 -->
                <td
                  class="text-center align-middle mw-rem-5"
                  v-if="calcList.holiday_description === '1日集計対象休暇'"
                ></td>
                <td
                  class="text-center align-middle mw-rem-5"
                  v-else
                >{{ calcList.working_status_name }}</td>
                <!-- /勤務状態 -->
                <!-- タイムテーブル名 -->
                <td class="text-center align-middle mw-rem-10">{{ calcList.working_timetable_name }}</td>
                <!-- /タイムテーブル名 -->
                <!-- 実働時間 -->
                <td
                  class="text-center align-middle mw-rem-5"
                  data-toggle="tooltip"
                  data-placement="top"
                  v-bind:title="edtString"
                  @mouseover="edttooltips('実働時間 = 所定＋残業時間＋深夜残業', 'または','法定休日・法定外休日')"
                >{{ calcList.total_working_times }}</td>
                <!-- /実働時間 -->
                <!-- 所定労働時間 -->
                <td class="text-center align-middle mw-rem-3">{{ calcList.regular_working_times }}</td>
                <!-- /所定労働時間 -->
                <!-- 所定外労働時間 -->
                <td
                  class="text-center align-middle mw-rem-3"
                >{{ calcList.out_of_regular_working_times }}</td>
                <!-- /所定外労働時間 -->
                <!-- 時間外労働時間 -->
                <td
                  class="text-center align-middle mw-rem-5"
                  v-if="calcList.business_kubun !== '2' && calcList.business_kubun !== '3'"
                >{{ calcList.off_hours_working_hours }}</td>
                <td class="text-center align-middle mw-rem-5" v-else>00:00</td>
                <!-- /時間外労働時間 -->
                <!-- 深夜残業時間 -->
                <td
                  class="text-center align-middle mw-rem-5"
                  v-if="calcList.business_kubun !== '2' && calcList.business_kubun !== '3'"
                >{{ calcList.late_night_overtime_hours }}</td>
                <td class="text-center align-middle mw-rem-5" v-else>00:00</td>
                <!-- /深夜残業時間 -->
                <!-- 法定休日労働時間 -->
                <td
                  class="text-center align-middle mw-rem-5"
                  v-if="calcList.business_kubun === '2'"
                >{{ calcList.legal_working_holiday_hours }}</td>
                <td
                  class="text-center align-middle mw-rem-5"
                  v-else-if="calcList.business_kubun === ''"
                ></td>
                <td class="text-center align-middle mw-rem-5" v-else>00:00</td>
                <!-- /法定休日労働時間 -->
                <!-- 法定休日深夜残業時間 -->
                <!-- <td
                  class="text-center align-middle mw-rem-5"
                  v-if="calcList.business_kubun === '2'"
                >{{ calcList.legal_working_holiday_night_overtime_hours }}</td>
                <td
                  class="text-center align-middle mw-rem-5"
                  v-else-if="calcList.business_kubun === ''"
                ></td>q
                <td
                  class="text-center align-middle mw-rem-5"
                  v-else
                >00:00</td>-->
                <!-- /法定休日深夜残業時間 -->
                <!-- 法定外（所定休日）休日労働時間 -->
                <td
                  class="text-center align-middle mw-rem-5"
                  v-if="calcList.business_kubun === '3'"
                >{{ calcList.out_of_legal_working_holiday_hours }}</td>
                <td
                  class="text-center align-middle mw-rem-5"
                  v-else-if="calcList.business_kubun === ''"
                ></td>
                <td class="text-center align-middle mw-rem-5" v-else>00:00</td>
                <!-- /法定外（所定休日）休日労働時間 -->
                <!-- 法定外（所定休日）休日深夜残業時間 -->
                <!-- <td
                  class="text-center align-middle mw-rem-5"
                  v-if="calcList.business_kubun === '3'"
                >{{ calcList.out_of_legal_working_holiday_night_overtime_hours }}</td>
                <td
                  class="text-center align-middle mw-rem-5"
                  v-else-if="calcList.business_kubun === ''"
                ></td>
                <td
                  class="text-center align-middle mw-rem-5"
                  v-else
                >00:00</td>-->
                <!-- /法定外（所定休日）休日深夜残業時間 -->
                <!-- 深夜労働時間 -->
                <td
                  class="text-center align-middle mw-rem-3"
                >{{ calcList.late_night_working_hours }}</td>
                <!-- /深夜労働時間 -->
                <!-- 法定労働時間 -->
                <td class="text-center align-middle mw-rem-3">{{ calcList.legal_working_times }}</td>
                <!-- /法定労働時間 -->
                <!-- 法定外労働時間 -->
                <td
                  class="text-center align-middle mw-rem-3"
                >{{ calcList.out_of_legal_working_times }}</td>
                <!-- /法定外労働時間 -->
                <!-- 未就労労働時間 -->
                <td
                  class="text-center align-middle mw-rem-5"
                  data-toggle="tooltip"
                  data-placement="top"
                  v-bind:title="edtString"
                  @mouseover="edttooltips('所定時間内での','遅刻・早退・欠勤・私用外出などで労働時間に含めない（不就労）時間','給与控除対象','')"
                >{{ calcList.not_employment_working_hours }}</td>
                <!-- /未就労労働時間 -->
                <!-- 備考 -->
                <td
                  class="text-left align-middle mw-rem-15"
                >{{ calcList.remark_holiday_name }} {{ calcList.remark_check_result }} {{ calcList.remark_check_max_times }} {{ calcList.remark_check_interval }}</td>
                <!-- /備考 -->
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- /.row -->
    <!-- ----------- 地図表示部 START ---------------- -->
    <show-map-dialog
      v-bind:dateName="dateName"
      v-bind:dialogVisible="dialogVisible"
      v-bind:longitude="longitude"
      v-bind:latitude="latitude"
      v-bind:record_time="record_time"
      v-bind:user_name="user_name"
      v-bind:mode_name="mode_name"
    ></show-map-dialog>
  </div>
</template>
<script>
// CONST
// 打刻モード
const ATTENDANCE = 1;
const LEAVING = 2;
const OFFICIAL_OUT_START = 11;
const OFFICIAL_OUT_END = 12;
const PRIVATE_OUT_START = 21;
const PRIVATE_OUT_END = 22;
const C_USER_INDEX = 26; // 編集者表示
const C_USER_CON_INDEX = 27; // 条件付き編集者表示
const C_SSJJOO_ID = "SSJJOO00"; // 三条ID
const C_EDIT_USER = "23"; // 三条編集者ID

export default {
  name: "dailyworkinginfotable",
  props: {
    detailOrTotal: {
      type: String,
      default: "detail"
    },
    dateName: {
      type: String,
      default: ""
    },
    calcLists: {
      type: Array
    },
    btnMode: {
      type: String,
      default: ""
    },
    // predeterTimeName: {
    //   type: String,
    //   default: "残業時間"
    // },
    // predeterNightTimeName: {
    //   type: String,
    //   default: "深夜残業時間"
    // },
    // predeterTimeSecondName: {
    //   type: String,
    //   default: "残業時間"
    // },
    // predeterNightTimeSecondName: {
    //   type: String,
    //   default: "深夜残業"
    // },
    loginUser: {
      type: String,
      default: ""
    },
    loginRole: {
      type: String,
      default: ""
    },
    accountData: {
      type: String,
      default: ""
    },
    menuData: {
      type: Array,
      default: []
    },
    // TODO: 本来は .envに記載して取得したい
    apiKey: {
      type: String,
      default: "AIzaSyDmNKensj6Y3qEY9t0v1kbQqUxdOrhq3X8"
    }
  },
  computed: {
    // scriptjs(
    //       "https://maps.googleapis.com/maps/api/js?key=AIzaSyDmNKensj6Y3qEY9t0v1kbQqUxdOrhq3X8&callback=initMap",
    //       "loadGoogleMap"
    //     );
    // scriptjs.ready("loadGoogleMap", this.loadMap);
    mode_attendance: function() {
      return ATTENDANCE;
    },
    mode_leaving: function() {
      return LEAVING;
    },
    mode_official_out_start: function() {
      return OFFICIAL_OUT_START;
    },
    mode_official_out_end: function() {
      return OFFICIAL_OUT_END;
    },
    mode_private_out_start: function() {
      return PRIVATE_OUT_START;
    },
    mode_private_out_end: function() {
      return PRIVATE_OUT_END;
    },
    userindex: function() {
      return C_USER_INDEX;
    },
    userconindex: function() {
      return C_USER_CON_INDEX;
    },
    ssjjoo_id: function() {
      return C_SSJJOO_ID;
    },
    edit_user_id: function() {
      return C_EDIT_USER;
    }
  },
  data: function() {
    return {
      edtString: "",
      edtString1: "",
      tipStyle: {
        // 後述のスタイル用オブジェクト
        position: "absolute",
        top: "0px",
        left: "0px"
      },
      dialogVisible: false,
      longitude: "",
      latitude: "",
      record_time: "",
      user_name: "",
      mode_name: "",
      menu_data: []
    };
  },
  methods: {
    // tooltips
    edttooltips: function(value1, value2, value3, value4) {
      if (value1 != null && value1 != "") {
        this.edtString = value1;
      }
      if (value2 != null && value2 != "") {
        this.edtString = this.edtString + "\n" + value2;
      }
      if (value3 != null && value3 != "") {
        this.edtString = this.edtString + "\n" + value3;
      }
      if (value4 != null && value4 != "") {
        this.edtString = this.edtString + "\n" + value4;
      }
    },
    // モード名取得
    getMethodName(mode) {
      switch (mode) {
        case ATTENDANCE:
          this.mode_name = "出勤";
          break;
        case LEAVING:
          this.mode_name = "退勤";
          break;
        case PRIVATE_OUT_START:
          this.mode_name = "私用外出　開始";
          break;
        case PRIVATE_OUT_END:
          this.mode_name = "私用外出　終了";
          break;
        case OFFICIAL_OUT_START:
          this.mode_name = "公用外出　開始";
          break;
        case OFFICIAL_OUT_END:
          this.mode_name = "公用外出　終了";
          break;
        default:
          this.mode_name = "";
          break;
      }
      return this.mode_name;
    },
    /* １ユーザーに複数レコードが存在する場合
       ２行目以降にはユーザー名を持っていないので、取得する
    */
    getUserNameByIndex(index, name) {
      if (index != 0) {
        for (let i = index; i >= 0; i--) {
          if (this.calcLists[i].user_name != "") {
            return this.calcLists[i].user_name;
          }
        }
      } else {
        return name;
      }
    },
    // マップ表示
    showMap: function(time, name, x, y, index, mode) {
      this.latitude = x;
      this.longitude = y;
      this.user_name = this.getUserNameByIndex(index, name);
      this.record_time = time;
      this.mode_name = this.getMethodName(mode);
      this.dialogVisible = true;
    }
  }
};
</script>
<style lang="scss" scoped>
.svg_img {
  color: #dc143c;
  cursor: pointer;
}

.custom-bg-dark {
  background-color: #606266 !important;
  color: white !important;
}

.text-align-right {
  text-align: right;
}

.text-align-left {
  text-align: left !important;
}

.padding-dis {
  padding: 0.75rem 0rem !important;
}

.color-chartreuse {
  color: chartreuse;
}

thead,
tbody {
  display: block !important;
}

tbody {
  overflow-x: hidden !important;
  overflow-y: scroll !important;
  height: 300px !important;
}

.table th,
.table td {
  padding: 0rem !important;
  border-style: solid dashed !important;
  border-width: 1px !important;
  border-color: #95c5ed #dee2e6 !important;
}

table {
  border-collapse: collapse !important;
  border: 1px solid #95c5ed !important;
}

.mw-rem-3 {
  min-width: 3rem;
}

.mw-rem-4 {
  min-width: 4rem;
}

.mw-rem-8 {
  min-width: 8rem;
}
</style>
