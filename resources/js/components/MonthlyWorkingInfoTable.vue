<template>
  <div >
    <div class="card-body mb-3 p-0 border-top">
      <!-- panel contents -->
      <!-- .row -->
      <div class="row">
        <div class="col-lg-12">
          <div class="table-responsive">
            <div class="col-lg-12 p-0">
              <table class="table table-striped border-bottom font-size-sm">
                <thead>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='basicswitch'" bgcolor="#e3f0fb">
                    <td class="text-center align-middle mw-rem-5">雇用形態</td>
                    <td class="text-center align-middle mw-rem-4 color-royalblue"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定 + 残業（法定休日,法定外休日） + 深夜残業', '','','')"
                    >実働</td>
                    <td class="text-center align-middle mw-rem-4">所定</td>
                    <td class="text-center align-middle mw-rem-4">残業</td>
                    <td class="text-center align-middle mw-rem-4">深夜残業</td>
                    <td class="text-center align-middle mw-rem-4">法定休日</td>
                    <td class="text-center align-middle mw-rem-4">法定外休日</td>
                    <td class="text-center align-middle mw-rem-4">深夜労働</td>
                    <td class="text-center align-middle mw-rem-4">公用外出</td>
                    <td class="text-center align-middle mw-rem-4">私用外出</td>
                    <td class="text-center align-middle mw-rem-4"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                    >不就労</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='detailswitch'" bgcolor="#e3f0fb">
                    <td class="text-center align-middle mw-rem-5">雇用形態</td>
                    <td class="text-center align-middle mw-rem-4 color-royalblue"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定 + 残業（法定休日,法定外休日） + 深夜残業', '','','')"
                    >実働時間</td>
                    <td class="text-center align-middle mw-rem-4">所定</td>
                    <td class="text-center align-middle mw-rem-4">所外</td>
                    <td class="text-center align-middle mw-rem-4">残業</td>
                    <td class="text-center align-middle mw-rem-4">深夜残業</td>
                    <td class="text-center align-middle mw-rem-4">法定休日</td>
                    <td class="text-center align-middle mw-rem-4">法定外休日</td>
                    <td class="text-center align-middle mw-rem-4">深夜労働</td>
                    <td class="text-center align-middle mw-rem-4">法定</td>
                    <td class="text-center align-middle mw-rem-4">法外</td>
                    <td class="text-center align-middle mw-rem-4">公外</td>
                    <td class="text-center align-middle mw-rem-4">私外</td>
                    <td class="text-center align-middle mw-rem-4"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                    >不就労</td>
                  </tr>
                  <tr v-if="calcLists.length && detailOrTotal === 'total' && btnMode ==='basicswitch'" bgcolor="#7fffd4">
                    <td class="text-center align-middle mw-rem-5"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定時間 + 普通残業時間（法定休日労働時間,法定外休日労働時間） + 深夜残業時間', '','','')"
                    >実働時間</td>
                    <td class="text-center align-middle mw-rem-5">所定時間</td>
                    <td class="text-center align-middle mw-rem-5">普通残業時間</td>
                    <td class="text-center align-middle mw-rem-5">深夜残業時間</td>
                    <td class="text-center align-middle mw-rem-5">法定休日労働時間</td>
                    <td class="text-center align-middle mw-rem-5">法定外休日労働時間</td>
                    <td class="text-center align-middle mw-rem-5">深夜労働時間</td>
                    <td class="text-center align-middle mw-rem-5">公用外出時間</td>
                    <td class="text-center align-middle mw-rem-5">私用外出時間</td>
                    <td class="text-center align-middle mw-rem-5"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                    >不就労時間</td>
                  </tr>
                  <tr v-if="calcLists.length && detailOrTotal === 'total' && btnMode ==='detailswitch'" bgcolor="#7fffd4">
                    <td class="text-center align-middle mw-rem-5"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定 + 普通残業時間（法定休日労働時間,法定外休日労働時間） + 深夜残業時間', '','','')"
                    >実働時間</td>
                    <td class="text-center align-middle mw-rem-5">所定時間</td>
                    <td class="text-center align-middle mw-rem-5">所定外時間</td>
                    <td class="text-center align-middle mw-rem-5">普通残業時間</td>
                    <td class="text-center align-middle mw-rem-5">深夜残業時間</td>
                    <td class="text-center align-middle mw-rem-5">法定休日労働時間</td>
                    <td class="text-center align-middle mw-rem-5">法定外休日労働時間</td>
                    <td class="text-center align-middle mw-rem-5">深夜労働時間</td>
                    <td class="text-center align-middle mw-rem-5">法定時間</td>
                    <td class="text-center align-middle mw-rem-5">法定外時間</td>
                    <td class="text-center align-middle mw-rem-5">公用外出時間</td>
                    <td class="text-center align-middle mw-rem-5">私用外出時間</td>
                    <td class="text-center align-middle mw-rem-5"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                    >不就労時間</td>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='basicswitch'"
                  >
                    <td class="text-center align-middle mw-rem-5">{{ calcLists.employment }}</td>
                    <td class="text-center align-middle mw-rem-4"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定 + 残業（法定休日,法定外休日） + 深夜残業', '','','')"
                    >{{ calcLists.total_working_times }}</td>
                    <!-- 所定労働時間 -->
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.regular_working_times }}</td>
                    <!-- /所定労働時間 -->
                    <!-- 時間外労働時間 -->
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.off_hours_working_hours }}</td>
                    <!-- /時間外労働時間 -->
                    <!-- 深夜残業時間 -->
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.late_night_overtime_hours }}</td>
                    <!-- /深夜残業時間 -->
                    <!-- 法定休日労働時間 -->
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.legal_working_holiday_hours }}</td>
                    <!-- /法定休日労働時間 -->
                    <!-- 法定外（所定休日）休日労働時間 -->
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.out_of_legal_working_holiday_hours }}</td>
                    <!-- /法定外（所定休日）休日労働時間 -->
                    <!-- 深夜労働時間 -->
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.late_night_working_hours }}</td>
                    <!-- /深夜労働時間 -->
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.public_going_out_hours }}</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.missing_middle_hours }}</td>
                    <td class="text-center align-middle mw-rem-4"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                    >{{ calcLists.not_employment_working_hours }}</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='detailswitch'"
                  >
                    <td class="text-center align-middle mw-rem-5">{{ calcLists.employment }}</td>
                    <td class="text-center align-middle mw-rem-4"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定 + 残業（法定休日,法定外休日） + 深夜残業', '','','')"
                    >{{ calcLists.total_working_times }}</td>
                    <!-- 所定労働時間 -->
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.regular_working_times }}</td>
                    <!-- /所定労働時間 -->
                    <!-- 所定外労働時間 -->
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.out_of_regular_working_times }}</td>
                    <!-- /所定外労働時間 -->
                    <!-- 時間外労働時間 -->
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.off_hours_working_hours }}</td>
                    <!-- /時間外労働時間 -->
                    <!-- 深夜残業時間 -->
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.late_night_overtime_hours }}</td>
                    <!-- /深夜残業時間 -->
                    <!-- 法定休日労働時間 -->
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.legal_working_holiday_hours }}</td>
                    <!-- /法定休日労働時間 -->
                    <!-- 法定外（所定休日）休日労働時間 -->
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.out_of_legal_working_holiday_hours }}</td>
                    <!-- /法定外（所定休日）休日労働時間 -->
                    <!-- 深夜労働時間 -->
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.late_night_working_hours }}</td>
                    <!-- /深夜労働時間 -->
                    <!-- 法定労働時間 -->
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.legal_working_times }}</td>
                    <!-- /法定労働時間 -->
                    <!-- 法定外労働時間 -->
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.out_of_legal_working_times }}</td>
                    <!-- /法定外労働時間 -->
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.public_going_out_hours }}</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.missing_middle_hours }}</td>
                    <td class="text-center align-middle mw-rem-4"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                    >{{ calcLists.not_employment_working_hours }}</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'total' && btnMode ==='basicswitch'"
                    v-for="(total,index) in calcLists"
                  >
                    <!-- 実働時間 -->
                    <td class="text-center align-middle mw-rem-5"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定 + 普通残業時間（法定休日労働時間,法定外休日労働時間） + 深夜残業時間', '','','')"
                    >{{ total.total_working_times }}</td>
                    <!-- /実働時間 -->
                    <!-- 所定労働時間 -->
                    <td class="text-center align-middle mw-rem-5">{{ total.regular_working_times }}</td>
                    <!-- /所定労働時間 -->
                    <!-- 時間外労働時間 -->
                    <td class="text-center align-middle mw-rem-5">{{ total.off_hours_working_hours }}</td>
                    <!-- /時間外労働時間 -->
                    <!-- 深夜残業時間 -->
                    <td class="text-center align-middle mw-rem-5">{{ total.late_night_overtime_hours }}</td>
                    <!-- /深夜残業時間 -->
                    <!-- 法定休日労働時間 -->
                    <td class="text-center align-middle mw-rem-5">{{ total.legal_working_holiday_hours }}</td>
                    <!-- /法定休日労働時間 -->
                    <!-- 法定外（所定休日）休日労働時間 -->
                    <td class="text-center align-middle mw-rem-5">{{ total.out_of_legal_working_holiday_hours }}</td>
                    <!-- /法定外（所定休日）休日労働時間 -->
                    <!-- 深夜労働時間 -->
                    <td class="text-center align-middle mw-rem-5">{{ total.late_night_working_hours }}</td>
                    <!-- /深夜労働時間 -->
                    <td class="text-center align-middle mw-rem-5">{{ total.public_going_out_hours }}</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.missing_middle_hours }}</td>
                    <td class="text-center align-middle mw-rem-5"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                    >{{ total.not_employment_working_hours }}</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'total' && btnMode ==='detailswitch'"
                    v-for="(total,index) in calcLists"
                  >
                    <td class="text-center align-middle mw-rem-5"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定 + 普通残業時間（法定休日労働時間,法定外休日労働時間） + 深夜残業時間', '','','')"
                    >{{ total.total_working_times }}</td>
                    <!-- 所定労働時間 -->
                    <td class="text-center align-middle mw-rem-5">{{ total.regular_working_times }}</td>
                    <!-- /所定労働時間 -->
                    <!-- 所定外労働時間 -->
                    <td class="text-center align-middle mw-rem-5">{{ total.out_of_regular_working_times }}</td>
                    <!-- /所定外労働時間 -->
                    <!-- 時間外労働時間 -->
                    <td class="text-center align-middle mw-rem-5">{{ total.off_hours_working_hours }}</td>
                    <!-- /時間外労働時間 -->
                    <!-- 深夜残業時間 -->
                    <td class="text-center align-middle mw-rem-5">{{ total.late_night_overtime_hours }}</td>
                    <!-- /深夜残業時間 -->
                    <!-- 法定休日労働時間 -->
                    <td class="text-center align-middle mw-rem-5">{{ total.legal_working_holiday_hours }}</td>
                    <!-- /法定休日労働時間 -->
                    <!-- 法定外（所定休日）休日労働時間 -->
                    <td class="text-center align-middle mw-rem-5">{{ total.out_of_legal_working_holiday_hours }}</td>
                    <!-- /法定外（所定休日）休日労働時間 -->
                    <!-- 深夜労働時間 -->
                    <td class="text-center align-middle mw-rem-5">{{ total.late_night_working_hours }}</td>
                    <!-- /深夜労働時間 -->
                    <!-- 法定労働時間 -->
                    <td class="text-center align-middle mw-rem-5">{{ total.legal_working_times }}</td>
                    <!-- /法定労働時間 -->
                    <!-- 法定外労働時間 -->
                    <td class="text-center align-middle mw-rem-5">{{ total.out_of_legal_working_times }}</td>
                    <!-- /法定外労働時間 -->
                    <td class="text-center align-middle mw-rem-5">{{ total.public_going_out_hours }}</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.missing_middle_hours }}</td>
                    <td class="text-center align-middle mw-rem-5"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                    >{{ total.not_employment_working_hours }}</td>
                  </tr>
                </tbody>
              </table>
              
            </div>
          </div>
          <div class="table-responsive">
            <div class="col-lg-12 p-0">
              <table class="table table-striped border-bottom font-size-sm">
                <thead>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='basicswitch'" bgcolor="#e3f0fb">
                    <td class="text-center align-middle mw-rem-4">出勤日数</td>
                    <td class="text-center align-middle mw-rem-4">有休日数</td>
                    <td class="text-center align-middle mw-rem-4">特休日数</td>
                    <td class="text-center align-middle mw-rem-4">早退日数</td>
                    <td class="text-center align-middle mw-rem-4">遅刻日数</td>
                    <td class="text-center align-middle mw-rem-4">欠勤日数</td>
                    <td class="text-center align-middle mw-rem-4">みなし日数</td>
                    <td class="text-center align-middle mw-rem-4">公傷日数</td>
                    <td class="text-center align-middle mw-rem-4">慶弔日数</td>
                    <td class="text-center align-middle mw-rem-4">代休日数</td>
                    <td class="text-center align-middle mw-rem-4">振休日数</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='detailswitch'" bgcolor="#e3f0fb">
                    <td class="text-center align-middle mw-rem-4">出勤日数</td>
                    <td class="text-center align-middle mw-rem-4">有休日数</td>
                    <td class="text-center align-middle mw-rem-4">特休日数</td>
                    <td class="text-center align-middle mw-rem-4">早退日数</td>
                    <td class="text-center align-middle mw-rem-4">遅刻日数</td>
                    <td class="text-center align-middle mw-rem-4">欠勤日数</td>
                    <td class="text-center align-middle mw-rem-4">みなし日数</td>
                    <td class="text-center align-middle mw-rem-4">公傷日数</td>
                    <td class="text-center align-middle mw-rem-4">慶弔日数</td>
                    <td class="text-center align-middle mw-rem-4">代休日数</td>
                    <td class="text-center align-middle mw-rem-4">振休日数</td>
                  </tr>
                  <tr v-if="calcLists.length && detailOrTotal === 'total' && btnMode ==='basicswitch'" bgcolor="#7fffd4">
                    <td class="text-center align-middle mw-rem-5">出勤日数</td>
                    <td class="text-center align-middle mw-rem-5">有給休暇日数</td>
                    <td class="text-center align-middle mw-rem-5">特別休暇日数</td>
                    <td class="text-center align-middle mw-rem-5">早退日数</td>
                    <td class="text-center align-middle mw-rem-5">遅刻日数</td>
                    <td class="text-center align-middle mw-rem-5">欠勤日数</td>
                    <td class="text-center align-middle mw-rem-5">みなし日数</td>
                    <td class="text-center align-middle mw-rem-5">公傷日数</td>
                    <td class="text-center align-middle mw-rem-5">慶弔休暇日数</td>
                    <td class="text-center align-middle mw-rem-5">代替休日日数</td>
                    <td class="text-center align-middle mw-rem-5">振替休日日数</td>
                  </tr>
                  <tr v-if="calcLists.length && detailOrTotal === 'total' && btnMode ==='detailswitch'" bgcolor="#7fffd4">
                    <td class="text-center align-middle mw-rem-5">出勤日数</td>
                    <td class="text-center align-middle mw-rem-5">有給休暇日数</td>
                    <td class="text-center align-middle mw-rem-5">特別休暇日数</td>
                    <td class="text-center align-middle mw-rem-5">早退日数</td>
                    <td class="text-center align-middle mw-rem-5">遅刻日数</td>
                    <td class="text-center align-middle mw-rem-5">欠勤日数</td>
                    <td class="text-center align-middle mw-rem-5">みなし日数</td>
                    <td class="text-center align-middle mw-rem-5">公傷日数</td>
                    <td class="text-center align-middle mw-rem-5">慶弔休暇日数</td>
                    <td class="text-center align-middle mw-rem-5">代替休日日数</td>
                    <td class="text-center align-middle mw-rem-5">振替休日日数</td>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='basicswitch'"
                  >
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_working_status }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_paid_holidays }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_holiday_kubun }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_leave_early }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_late }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_absence }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_deemed }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_public_damage }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_congratulatory }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_substitute_holiday }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_compensation_holiday }} 日</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='detailswitch'"
                  >
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_working_status }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_paid_holidays }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_holiday_kubun }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_leave_early }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_late }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_absence }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_deemed }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_public_damage }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_congratulatory }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_substitute_holiday }} 日</td>
                    <td class="text-center align-middle mw-rem-4">{{ calcLists.total_compensation_holiday }} 日</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'total' && btnMode ==='basicswitch'"
                    v-for="(total,index) in calcLists"
                  >
                    <td class="text-center align-middle mw-rem-5">{{ total.total_working_status }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_paid_holidays }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_holiday_kubun }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_leave_early }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_late }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_absence }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_deemed }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_public_damage }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_congratulatory }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_substitute_holiday }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_compensation_holiday }} 日</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'total' && btnMode ==='detailswitch'"
                    v-for="(total,index) in calcLists"
                  >
                     <td class="text-center align-middle mw-rem-5">{{ total.total_working_status }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_paid_holidays }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_holiday_kubun }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_leave_early }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_late }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_absence }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_deemed }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_public_damage }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_congratulatory }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_substitute_holiday }} 日</td>
                    <td class="text-center align-middle mw-rem-5">{{ total.total_compensation_holiday }} 日</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!-- /.row -->
    </div>
  </div>
</template>
<script>

export default {
  name: "monthlyworkinginfotable",
  props: {
    detailOrTotal: {
      type: String,
      default: 'detail'
    },
    calcLists: {
      type: Array,
      required: true
    },
    btnMode: {
      type: String,
      default: ''
    }
  },
  data: function() {
    return {
      edtString: ""
    };
  },
  methods: {
    // tooltips
    edttooltips: function(value1, value2, value3, value4) {
      if (value1.length > 0) {
        this.edtString = value1;
      }
      if (value2.length > 0) {
        this.edtString = this.edtString + '\n' + value2;
      }
      if (value3.length > 0) {
        this.edtString = this.edtString + '\n' + value3;
      }
      if (value4.length > 0) {
        this.edtString = this.edtString + '\n' + value4;
      }
    }
  }
};
</script>
<style scoped>

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
  min-width: 3rem !important;
}

.mw-rem-4 {
  min-width: 4rem !important;
}

.mw-rem-5 {
  min-width: 7rem !important;
}
</style>
