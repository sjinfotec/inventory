<template>
  <div>
    <!-- .row -->
    <div class="row">
      <div class="col-12">
        <div class="table-responsive">
          <div class="col-12 p-0">
            <table class="table table-striped border-bottom font-size-sm text-nowrap">
              <thead>
                <tr v-if="detailOrTotal === 'detail' && btnMode ==='basicswitch'" bgcolor="#e3f0fb">
                  <td class="text-center align-middle w-15 mw-rem-5">部署</td>
                  <td class="text-center align-middle w-15 mw-rem-10">雇用形態</td>
                  <td class="text-center align-middle w-15 mw-rem-10">氏名</td>
                  <td class="text-left align-middle w-15 mw-rem-4">出勤</td>
                  <td class="text-left align-middle w-15 mw-rem-4">退勤</td>
                  <td class="text-left align-middle w-15 mw-rem-4">公外</td>
                  <td class="text-left align-middle w-15 mw-rem-4">公外戻</td>
                  <td class="text-left align-middle w-15 mw-rem-4">私外</td>
                  <td class="text-left align-middle w-15 mw-rem-4">私外戻</td>
                  <td class="text-center align-middle w-15 mw-rem-5">勤務状態</td>
                  <td class="text-center align-middle w-15 mw-rem-10">勤務帯</td>
                  <td
                    class="text-center align-middle w-15 mw-rem-5 color-royalblue"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('実働時間 = 所定 + ',predeterTimeSecondName,' + ' + predeterNightTimeSecondName,'')"
                  >実働時間</td>
                  <!--  <td class="text-center align-middle css-fukidashi"
                    @mouseover="edttooltips('実働時間 = 所定 + ',predeterTimeName,predeterNightTimeName,'')">
                    <span class="text">実働時間</span>
                    <span class="fukidashi">{{ edtString }}</span>
                  </td>-->
                  <td class="text-center align-middle w-15 mw-rem-3">所定</td>
                  <td class="text-center align-middle w-15 mw-rem-5">{{ predeterTimeSecondName }}</td>
                  <td class="text-center align-middle w-15 mw-rem-5">{{ predeterNightTimeSecondName }}</td>
                  <td class="text-center align-middle w-15 mw-rem-3">深夜労働</td>
                  <td
                    class="text-center align-middle w-15 mw-rem-5 color-royalblue"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                  >不就労時間</td>
                  <td class="text-center align-middle w-35 mw-rem-15">備考</td>
                </tr>
                <tr v-if="detailOrTotal === 'detail' && btnMode ==='detailswitch'" bgcolor="#e3f0fb">
                  <td class="text-center align-middle w-15 mw-rem-5">部署</td>
                  <td class="text-center align-middle w-15 mw-rem-10">雇用形態</td>
                  <td class="text-center align-middle w-15 mw-rem-10">氏名</td>
                  <td class="text-left align-middle w-15 mw-rem-4">出勤</td>
                  <td class="text-center align-middle w-15 mw-rem-10">編集氏名</td>
                  <td class="text-left align-middle w-15 mw-rem-4">退勤</td>
                  <td class="text-center align-middle w-15 mw-rem-10">編集氏名</td>
                  <td class="text-left align-middle w-15 mw-rem-4">公外</td>
                  <td class="text-center align-middle w-15 mw-rem-10">編集氏名</td>
                  <td class="text-left align-middle w-15 mw-rem-4">公外戻</td>
                  <td class="text-center align-middle w-15 mw-rem-10">編集氏名</td>
                  <td class="text-left align-middle w-15 mw-rem-4">私外</td>
                  <td class="text-center align-middle w-15 mw-rem-10">編集氏名</td>
                  <td class="text-left align-middle w-15 mw-rem-4">私外戻</td>
                  <td class="text-center align-middle w-15 mw-rem-10">編集氏名</td>
                  <td class="text-center align-middle w-15 mw-rem-5">勤務状態</td>
                  <td class="text-center align-middle w-15 mw-rem-10">勤務帯</td>
                  <td
                    class="text-center align-middle w-15 color-royalblue"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('実働時間 = 所定 + ',predeterTimeSecondName,' + ' + predeterNightTimeSecondName,'')"
                  >実働時間</td>
                  <td class="text-center align-middle w-15 mw-rem-3">所定</td>
                  <td class="text-center align-middle w-15 mw-rem-3">所定外</td>
                  <td class="text-center align-middle w-15 mw-rem-5">{{ predeterTimeSecondName }}</td>
                  <td class="text-center align-middle w-15 mw-rem-5">{{ predeterNightTimeSecondName }}</td>
                  <td class="text-center align-middle w-15 mw-rem-3">深夜労働</td>
                  <td class="text-center align-middle w-15 mw-rem-3">法定</td>
                  <td class="text-center align-middle w-15 mw-rem-3">法定外</td>
                  <td
                    class="text-center align-middle w-15 mw-rem-5 color-royalblue"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                  >不就労時間</td>
                  <td class="text-center align-middle w-35 mw-rem-15">備考</td>
                </tr>
                <tr v-if="detailOrTotal === 'total' && btnMode ==='basicswitch'" bgcolor="#e3fbef">
                  <td
                    class="text-center align-middle w-15 mw-rem-5 color-royalblue"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @click="edttooltips('実働時間 = 所定労働時間 + ',predeterTimeName,' + ' + predeterNightTimeName,'')"
                    @mouseover="edttooltips('実働時間 = 所定労働時間 + ',predeterTimeName,' + ' + predeterNightTimeName,'')"
                  >実働時間</td>
                  <td class="text-center align-middle w-15 mw-rem-3">所定労働時間</td>
                  <td class="text-center align-middle w-15 mw-rem-5">{{ predeterTimeSecondName }}</td>
                  <td class="text-center align-middle w-15 mw-rem-5">{{ predeterNightTimeSecondName }}</td>
                  <td class="text-center align-middle w-15 mw-rem-3">深夜労働時間</td>
                  <td
                    class="text-center align-middle w-15 mw-rem-5"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                  >不就労時間</td>
                  <td class="text-center align-middle w-15 mw-rem-5">出勤者数</td>
                  <td class="text-center align-middle w-15 mw-rem-5">外出者数</td>
                  <td class="text-center align-middle w-15 mw-rem-5">有給休暇者数</td>
                  <td class="text-center align-middle w-15 mw-rem-5">特別休暇者数</td>
                  <td class="text-center align-middle w-15 mw-rem-5">早退者数</td>
                  <td class="text-center align-middle w-15 mw-rem-5">遅刻者数</td>
                  <td class="text-center align-middle w-15 mw-rem-5">欠勤者数</td>
                  <td class="text-center align-middle w-35 mw-rem-15">備考</td>
                </tr>
                <tr v-if="detailOrTotal === 'total' && btnMode ==='detailswitch'" bgcolor="#e3fbef">
                  <td
                    class="text-center align-middle w-15 color-royalblue"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('実働時間 = 所定時間 + ',predeterTimeName,' + ' + predeterNightTimeName,'')"
                  >実働時間</td>
                  <td class="text-center align-middle w-15 mw-rem-5">所定時間</td>
                  <td class="text-center align-middle w-15 mw-rem-5">所定外時間</td>
                  <td class="text-center align-middle w-15 mw-rem-10">{{ predeterTimeName }}</td>
                  <td class="text-center align-middle w-15 mw-rem-10">{{ predeterNightTimeName }}</td>
                  <td class="text-center align-middle w-15 mw-rem-5">深夜労働時間</td>
                  <td class="text-center align-middle w-15 mw-rem-5">法定時間</td>
                  <td class="text-center align-middle w-15 mw-rem-5">法定外時間</td>
                  <td
                    class="text-center align-middle w-15"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                  >不就労時間</td>
                  <td class="text-center align-middle w-15 mw-rem-5">出勤者数</td>
                  <td class="text-center align-middle w-15 mw-rem-5">外出者数</td>
                  <td class="text-center align-middle w-15 mw-rem-5">有給休暇者数</td>
                  <td class="text-center align-middle w-15 mw-rem-5">特別休暇者数</td>
                  <td class="text-center align-middle w-15 mw-rem-5">早退者数</td>
                  <td class="text-center align-middle w-15 mw-rem-5">遅刻者数</td>
                  <td class="text-center align-middle w-15 mw-rem-5">欠勤者数</td>
                  <td class="text-center align-middle w-35 mw-rem-15">備考</td>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-if="detailOrTotal === 'detail' && btnMode ==='basicswitch'"
                  v-for="(calcList,index) in calcLists"
                >
                  <td class="text-left align-middle">{{ calcList.department_name }}</td>
                  <td class="text-left align-middle">{{ calcList.employment_status_name }}</td>
                  <td class="text-left align-middle">{{ calcList.user_name }}</td>
                  <!-- 出勤 -->
                  <daily-working-info-time-table
                    v-bind:calc-list="{
                      x_positions: calcList.x_attendance_time_position,
                      y_positions: calcList.y_attendance_time_positions,
                      editor_department_code: calcList.attendance_editor_department_code,
                      editor_department_name: calcList.attendance_editor_department_name,
                      editor_user_name: calcList.attendance_editor_user_name,
                      working_time: calcList.attendance_time,
                    }"
                    v-on:click-event="showMap(
                        calcList.attendance_time,
                        calcList.user_name,
                        calcList.x_attendance_time_positions,
                        calcList.y_attendance_time_positions,
                        index,
                        mode_attendance)"
                  >
                  </daily-working-info-time-table>
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
                    }"
                    v-on:click-event="showMap(
                        calcList.leaving_time,
                        calcList.user_name,
                        calcList.x_leaving_time_positions,
                        calcList.y_leaving_time_positions,
                        index,
                        mode_leaving)"
                  >
                  </daily-working-info-time-table>
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
                    }"
                    v-on:click-event="showMap(
                        calcList.public_going_out_time,
                        calcList.user_name,
                        calcList.x_public_going_out_time_positions,
                        calcList.y_public_going_out_time_positions,
                        index,
                        mode_official_out_start)"
                  >
                  </daily-working-info-time-table>
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
                    }"
                    v-on:click-event="showMap(
                        calcList.public_going_out_return_time,
                        calcList.user_name,
                        calcList.x_public_going_out_return_time_positions,
                        calcList.y_public_going_out_return_time_positions,
                        index,
                        mode_official_out_end)"
                  >
                  </daily-working-info-time-table>
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
                    }"
                    v-on:click-event="showMap(
                        calcList.missing_middle_time,
                        calcList.user_name,
                        calcList.x_missing_middle_time_positions,
                        calcList.y_missing_middle_time_positions,
                        index,
                        mode_private_out_start)"
                  >
                  </daily-working-info-time-table>
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
                    }"
                    v-on:click-event="showMap(
                        calcList.missing_middle_return_time,
                        calcList.user_name,
                        calcList.x_missing_middle_return_time_positions,
                        calcList.y_missing_middle_return_time_positions,
                        index,
                        mode_private_out_end)"
                  >
                  </daily-working-info-time-table>
                  <!-- /私用外出戻り　終了 -->
                  <td class="text-center align-middle">{{ calcList.working_status_name }}</td>
                  <td class="text-center align-middle">{{ calcList.working_timetable_name }}</td>
                  <td
                    class="text-center align-middle"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('実働時間 = 所定 + ',predeterTimeSecondName,' + ' + predeterNightTimeSecondName,'')"
                  >{{ calcList.total_working_times }}</td>
                  <td class="text-center align-middle">{{ calcList.regular_working_times }}</td>
                  <td class="text-center align-middle">{{ calcList.off_hours_working_hours }}</td>
                  <td class="text-center align-middle">{{ calcList.late_night_overtime_hours }}</td>
                  <td class="text-center align-middle">{{ calcList.late_night_working_hours }}</td>
                  <td
                    class="text-center align-middle"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                  >{{ calcList.not_employment_working_hours }}</td>
                  <td
                    class="text-left align-middle"
                  >{{ calcList.remark_holiday_name }} {{ calcList.remark_check_result }} {{ calcList.remark_check_max_times }} {{ calcList.remark_check_interval }}</td>
                </tr>
                <tr
                  v-if="detailOrTotal === 'detail' && btnMode ==='detailswitch'"
                  v-for="(calcList,index) in calcLists"
                >
                  <td class="text-left align-middle">{{ calcList.department_name }}</td>
                  <td class="text-left align-middle">{{ calcList.employment_status_name }}</td>
                  <td class="text-left align-middle">{{ calcList.user_name }}</td>
                  <!-- 出勤 -->
                  <daily-working-info-time-table
                    v-bind:calc-list="{
                      x_positions: calcList.x_attendance_time_position,
                      y_positions: calcList.y_attendance_time_positions,
                      editor_department_code: calcList.attendance_editor_department_code,
                      editor_department_name: calcList.attendance_editor_department_name,
                      editor_user_name: calcList.attendance_editor_user_name,
                      working_time: calcList.attendance_time,
                    }"
                    v-on:click-event="showMap(
                        calcList.attendance_time,
                        calcList.user_name,
                        calcList.x_attendance_time_positions,
                        calcList.y_attendance_time_positions,
                        index,
                        mode_attendance)"
                  >
                  </daily-working-info-time-table>
                  <td
                    class="text-left text-align-left"
                    v-if="calcList.attendance_editor_department_name"
                  >
                    {{ calcList.attendance_editor_department_name }}：{{ calcList.attendance_editor_user_name }}
                  </td>
                  <td
                    class="text-left text-align-left"
                    v-else
                  >
                  </td>
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
                    }"
                    v-on:click-event="showMap(
                        calcList.leaving_time,
                        calcList.user_name,
                        calcList.x_leaving_time_positions,
                        calcList.y_leaving_time_positions,
                        index,
                        mode_leaving)"
                  >
                  </daily-working-info-time-table>
                  <td
                    class="text-left text-align-left"
                    v-if="calcList.leaving_editor_department_name"
                  >
                    {{ calcList.leaving_editor_department_name }}：{{ calcList.attendance_editor_user_name }}
                  </td>
                  <td
                    class="text-left text-align-left"
                    v-else
                  >
                  </td>
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
                    }"
                    v-on:click-event="showMap(
                        calcList.public_going_out_time,
                        calcList.user_name,
                        calcList.x_public_going_out_time_positions,
                        calcList.y_public_going_out_time_positions,
                        index,
                        mode_official_out_start)"
                  >
                  </daily-working-info-time-table>
                  <td
                    class="text-left text-align-left"
                    v-if="calcList.public_editor_department_name"
                  >
                    {{ calcList.public_editor_department_name }}：{{ calcList.public_editor_user_name }}
                  </td>
                  <td
                    class="text-left text-align-left"
                    v-else
                  >
                  </td>
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
                    }"
                    v-on:click-event="showMap(
                        calcList.public_going_out_return_time,
                        calcList.user_name,
                        calcList.x_public_going_out_return_time_positions,
                        calcList.y_public_going_out_return_time_positions,
                        index,
                        mode_official_out_end)"
                  >
                  </daily-working-info-time-table>
                  <td
                    class="text-left text-align-left"
                    v-if="calcList.public_return_editor_department_name"
                  >
                    {{ calcList.public_return_editor_department_name }}：{{ calcList.public_return_editor_user_name }}
                  </td>
                  <td
                    class="text-left text-align-left"
                    v-else
                  >
                  </td>
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
                    }"
                    v-on:click-event="showMap(
                        calcList.missing_middle_time,
                        calcList.user_name,
                        calcList.x_missing_middle_time_positions,
                        calcList.y_missing_middle_time_positions,
                        index,
                        mode_private_out_start)"
                  >
                  </daily-working-info-time-table>
                  <td
                    class="text-left text-align-left"
                    v-if="calcList.missing_editor_department_name"
                  >
                    {{ calcList.missing_editor_department_name }}：{{ calcList.missing_editor_user_name }}
                  </td>
                  <td
                    class="text-left text-align-left"
                    v-else
                  >
                  </td>
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
                    }"
                    v-on:click-event="showMap(
                        calcList.missing_middle_return_time,
                        calcList.user_name,
                        calcList.x_missing_middle_return_time_positions,
                        calcList.y_missing_middle_return_time_positions,
                        index,
                        mode_private_out_end)"
                  >
                  </daily-working-info-time-table>
                  <td
                    class="text-left text-align-left"
                    v-if="calcList.missing_return_editor_department_name"
                  >
                    {{ calcList.missing_return_editor_department_name }}：{{ calcList.missing_return_editor_user_name }}
                  </td>
                  <td
                    class="text-left text-align-left"
                    v-else
                  >
                  </td>
                  <!-- /私用外出戻り　終了 -->
                  <td class="text-center align-middle">{{ calcList.working_status_name }}</td>
                  <td class="text-center align-middle">{{ calcList.working_timetable_name }}</td>
                  <td
                    class="text-center align-middle"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('実働時間 = 所定 + ',predeterTimeSecondName,' + ' + predeterNightTimeSecondName,'')"
                  >{{ calcList.total_working_times }}</td>
                  <td class="text-center align-middle">{{ calcList.regular_working_times }}</td>
                  <td class="text-center align-middle">{{ calcList.out_of_regular_working_times }}</td>
                  <td class="text-center align-middle">{{ calcList.off_hours_working_hours }}</td>
                  <td class="text-center align-middle">{{ calcList.late_night_overtime_hours }}</td>
                  <td class="text-center align-middle">{{ calcList.late_night_working_hours }}</td>
                  <td class="text-center align-middle">{{ calcList.legal_working_times }}</td>
                  <td class="text-center align-middle">{{ calcList.out_of_legal_working_times }}</td>
                  <td
                    class="text-center align-middle"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                  >{{ calcList.not_employment_working_hours }}</td>
                  <td
                    class="text-left align-middle"
                  >{{ calcList.remark_holiday_name }} {{ calcList.remark_check_result }} {{ calcList.remark_check_max_times }} {{ calcList.remark_check_interval }}</td>
                </tr>
                <tr
                  v-if="detailOrTotal === 'total' && btnMode ==='basicswitch'"
                  v-for="(calcList,index) in calcLists"
                >
                  <td
                    class="text-center align-middle"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('実働時間 = 所定時間 + ',predeterTimeName,' + ' + predeterNightTimeName,'')"
                  >{{ calcList.total_working_times }}</td>
                  <td class="text-center align-middle">{{ calcList.regular_working_times }}</td>
                  <td class="text-center align-middle">{{ calcList.off_hours_working_hours }}</td>
                  <td class="text-center align-middle">{{ calcList.late_night_overtime_hours }}</td>
                  <td class="text-center align-middle">{{ calcList.late_night_working_hours }}</td>
                  <td
                    class="text-center align-middle"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                  >{{ calcList.not_employment_working_hours }}</td>
                  <td class="text-center align-middle">{{ calcList.total_working_status }}</td>
                  <td class="text-center align-middle">{{ calcList.total_go_out }}</td>
                  <td class="text-center align-middle">{{ calcList.total_paid_holidays }}</td>
                  <td class="text-center align-middle">{{ calcList.total_holiday_kubun }}</td>
                  <td class="text-center align-middle">{{ calcList.total_leave_early }}</td>
                  <td class="text-center align-middle">{{ calcList.total_late }}</td>
                  <td class="text-center align-middle">{{ calcList.total_absence }}</td>
                  <td class="text-left align-middle"></td>
                </tr>
                <tr
                  v-if="detailOrTotal === 'total' && btnMode ==='detailswitch'"
                  v-for="(calcList,index) in calcLists"
                >
                  <td
                    class="text-center align-middle"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('実働時間 = 所定時間 + ',predeterTimeName,' + ' + predeterNightTimeName,'')"
                  >{{ calcList.total_working_times }}</td>
                  <td class="text-center align-middle">{{ calcList.regular_working_times }}</td>
                  <td class="text-center align-middle">{{ calcList.out_of_regular_working_times }}</td>
                  <td class="text-center align-middle">{{ calcList.off_hours_working_hours }}</td>
                  <td class="text-center align-middle">{{ calcList.late_night_overtime_hours }}</td>
                  <td class="text-center align-middle">{{ calcList.late_night_working_hours }}</td>
                  <td class="text-center align-middle">{{ calcList.legal_working_times }}</td>
                  <td class="text-center align-middle">{{ calcList.out_of_legal_working_times }}</td>
                  <td
                    class="text-center align-middle"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                  >{{ calcList.not_employment_working_hours }}</td>
                  <td class="text-center align-middle">{{ calcList.total_working_status }}</td>
                  <td class="text-center align-middle">{{ calcList.total_go_out }}</td>
                  <td class="text-center align-middle">{{ calcList.total_paid_holidays }}</td>
                  <td class="text-center align-middle">{{ calcList.total_holiday_kubun }}</td>
                  <td class="text-center align-middle">{{ calcList.total_leave_early }}</td>
                  <td class="text-center align-middle">{{ calcList.total_late }}</td>
                  <td class="text-center align-middle">{{ calcList.total_absence }}</td>
                  <td class="text-left align-middle"></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- /.row -->
    <el-dialog
      custom-class="custom-bg-dark"
      v-bind:title="dateName + ' 打刻時の位置情報'"
      :visible.sync="dialogVisible"
      width="60%"
      center="true"
    >
      <!-- <div class="col-xs-12 padding-dis-left"> -->
      <!-- <div>{{ user_name }} さん {{mode_name}} : {{ record_time }}</div> -->
      <div class="card bg-dark text-white">
        <!-- <div class="card-header">打刻時の位置情報</div> -->
        <div class="card-body">
          <h5 class="card-title">{{ user_name }} さん</h5>
          <h5 class="card-subtitle mb-2 color-chartreuse">{{ record_time }} {{mode_name}}</h5>
          <div style="width: 100%; overflow: hidden; width:100%;">
            <iframe
              v-bind:src="'https://www.google.com/maps/embed/v1/place?q='+longitude+','+latitude+'&key='+apiKey"
              width="100%"
              height="600"
              frameborder="0"
              style="border:0; margin-top: -150px;"
            ></iframe>
            <div class="card-footer text-align-right padding-dis">
              <span class="float-left">※機種の設定により位置情報が多少ずれる事があります。</span>
              <el-button type="danger" @click="dialogVisible = false">閉じる</el-button>
            </div>
          </div>
          <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
        </div>
      </div>

      <!-- <div class="dialog-footer text-align-right"> -->
      <!-- <span slot="footer" class="dialog-footer"> -->
      <!-- <el-button type="danger" @click="dialogVisible = false">閉じる</el-button> -->
      <!-- <el-button type="success" @click="dialogVisible = false">承認</el-button> -->
      <!-- </span> -->
      <!-- </div> -->
      <!-- </div> -->
    </el-dialog>
  </div>
</template>
<script>
// 打刻モード
const ATTENDANCE = 1;
const LEAVING = 2;
const OFFICIAL_OUT_START = 11;
const OFFICIAL_OUT_END = 12;
const PRIVATE_OUT_START = 21;
const PRIVATE_OUT_END = 22;

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
    predeterTimeName: {
      type: String,
      default: "残業時間"
    },
    predeterNightTimeName: {
      type: String,
      default: "深夜残業時間"
    },
    predeterTimeSecondName: {
      type: String,
      default: "残業時間"
    },
    predeterNightTimeSecondName: {
      type: String,
      default: "深夜残業"
    },
    // TODO: 本来は .envに記載して取得したい
    apiKey: {
      type: String,
      default: "AIzaSyDmNKensj6Y3qEY9t0v1kbQqUxdOrhq3X8"
    }
  },
  // マウント時
  mounted() {
    console.log("dailyworkinginfotable  マウント");
  },
  created() {
    // scriptjs(
    //       "https://maps.googleapis.com/maps/api/js?key=AIzaSyDmNKensj6Y3qEY9t0v1kbQqUxdOrhq3X8&callback=initMap",
    //       "loadGoogleMap"
    //     );
    // scriptjs.ready("loadGoogleMap", this.loadMap);
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
      mode_name: ""
    };
  },
  computed: {
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
    }
  },
  methods: {
    // tooltips
    edttooltips: function(value1, value2, value3, value4) {
      if (value1.length > 0) {
        this.edtString = value1;
      }
      if (value2.length > 0) {
        this.edtString = this.edtString + "\n" + value2;
      }
      if (value3.length > 0) {
        this.edtString = this.edtString + "\n" + value3;
      }
      if (value4.length > 0) {
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
    },
    hide: function() {
      this.$modal.hide("hello-world");
    },
    show: function() {
      this.$modal.show("hello-world");
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

table {
   border-collapse: collapse !important;
   border: 1px solid #95c5ed !important;
}

.table th, .table td {
    padding: 0rem !important;
    border-style: solid dashed !important;
    border-width: 1px !important;
    border-color: #95c5ed #dee2e6 !important;
}

.mw-rem-3 {
  min-width: 3rem;
}

.mw-rem-4 {
  min-width: 4rem;
}
</style>
