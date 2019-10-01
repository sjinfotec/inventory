<template>
  <div v-if="calcLists.length">
    <div class="card-body mb-3 p-0 border-top">
      <!-- panel contents -->
      <!-- .row -->
      <div class="row">
        <div class="col-12">
          <div class="table-responsive">
            <div class="col-12 p-0">
              <table class="table table-striped border-bottom font-size-sm text-nowrap">
                <thead>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='basicswitch'">
                    <td class="text-center align-middle w-15">部署</td>
                    <td class="text-center align-middle w-15">雇用形態</td>
                    <td class="text-center align-middle w-15">氏名</td>
                    <td class="text-center align-middle w-15">出勤</td>
                    <td class="text-center align-middle w-15">退勤</td>
                    <td class="text-center align-middle w-15">公用外出</td>
                    <td class="text-center align-middle w-15">公用外出戻り</td>
                    <td class="text-center align-middle w-15">私用外出</td>
                    <td class="text-center align-middle w-15">私用外出戻り</td>
                    <td class="text-center align-middle w-15">勤務状態</td>
                    <td class="text-center align-middle w-15 color-royalblue"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定時間 + ',predeterTimeName,predeterNightTimeName,'')"
                    >実働時間</td>
                    <td class="text-center align-middle w-15">所定時間</td>
                    <td class="text-center align-middle w-15">{{ predeterTimeName }}</td>
                    <td class="text-center align-middle w-15">{{ predeterNightTimeName }}</td>
                    <td class="text-center align-middle w-15 color-royalblue"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >不就労時間</td>
                    <td class="text-center align-middle w-35 mw-rem-20">備考</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='detailswitch'">
                    <td class="text-center align-middle w-15">部署</td>
                    <td class="text-center align-middle w-15">雇用形態</td>
                    <td class="text-center align-middle w-15">氏名</td>
                    <td class="text-center align-middle w-15">出勤</td>
                    <td class="text-center align-middle w-15">退勤</td>
                    <td class="text-center align-middle w-15">公用外出</td>
                    <td class="text-center align-middle w-15">公用外出戻り</td>
                    <td class="text-center align-middle w-15">私用外出</td>
                    <td class="text-center align-middle w-15">私用外出戻り</td>
                    <td class="text-center align-middle w-15">勤務状態</td>
                    <td class="text-center align-middle w-15 color-royalblue"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定時間 + ',predeterTimeName,predeterNightTimeName,'')"
                    >実働時間</td>
                    <td class="text-center align-middle w-15">所定時間</td>
                    <td class="text-center align-middle w-15">所定外時間</td>
                    <td class="text-center align-middle w-15">{{ predeterTimeName }}</td>
                    <td class="text-center align-middle w-15">{{ predeterNightTimeName }}</td>
                    <td class="text-center align-middle w-15">法定時間</td>
                    <td class="text-center align-middle w-15">法定外時間</td>
                    <td class="text-center align-middle w-15 color-royalblue"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >不就労時間</td>
                    <td class="text-center align-middle w-35 mw-rem-20">備考</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'total' && btnMode ==='basicswitch'">
                    <td class="text-center align-middle w-15 color-royalblue"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定時間 + ',predeterTimeName,predeterNightTimeName,'')"
                    >実働時間</td>
                    <td class="text-center align-middle w-15">所定時間</td>
                    <td class="text-center align-middle w-15">{{ predeterTimeName }}</td>
                    <td class="text-center align-middle w-15">{{ predeterNightTimeName }}</td>
                    <td class="text-center align-middle w-15"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >不就労時間</td>
                    <td class="text-center align-middle w-15">出勤者数</td>
                    <td class="text-center align-middle w-15">外出者数</td>
                    <td class="text-center align-middle w-15">有給休暇者数</td>
                    <td class="text-center align-middle w-15">特別休暇者数</td>
                    <td class="text-center align-middle w-15">早退者数</td>
                    <td class="text-center align-middle w-15">遅刻者数</td>
                    <td class="text-center align-middle w-15">欠勤者数</td>
                    <td class="text-center align-middle w-35 mw-rem-20">備考</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'total' && btnMode ==='detailswitch'">
                    <td class="text-center align-middle w-15 color-royalblue"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定時間 + ',predeterTimeName,predeterNightTimeName,'')"
                    >実働時間</td>
                    <td class="text-center align-middle w-15">所定時間</td>
                    <td class="text-center align-middle w-15">所定外時間</td>
                    <td class="text-center align-middle w-15">{{ predeterTimeName }}</td>
                    <td class="text-center align-middle w-15">{{ predeterNightTimeName }}</td>
                    <td class="text-center align-middle w-15">法定時間</td>
                    <td class="text-center align-middle w-15">法定外時間</td>
                    <td class="text-center align-middle w-15"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >不就労時間</td>
                    <td class="text-center align-middle w-15">出勤者数</td>
                    <td class="text-center align-middle w-15">外出者数</td>
                    <td class="text-center align-middle w-15">有給休暇者数</td>
                    <td class="text-center align-middle w-15">特別休暇者数</td>
                    <td class="text-center align-middle w-15">早退者数</td>
                    <td class="text-center align-middle w-15">遅刻者数</td>
                    <td class="text-center align-middle w-15">欠勤者数</td>
                    <td class="text-center align-middle w-35 mw-rem-20">備考</td>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='basicswitch'"
                    v-for="(calcList,index) in calcLists"
                  >
                    <td class="text-center align-middle">{{ calcList.department_name }}</td>
                    <td class="text-center align-middle">{{ calcList.employment_status_name }}</td>
                    <td class="text-center align-middle">{{ calcList.user_name }}</td>
                    <td class="text-center align-middle">{{ calcList.attendance_time }}</td>
                    <td class="text-center align-middle">{{ calcList.leaving_time }}</td>
                    <td class="text-center align-middle">{{ calcList.public_going_out_time }}</td>
                    <td class="text-center align-middle">{{ calcList.public_going_out_return_time }}</td>
                    <td class="text-center align-middle">{{ calcList.missing_middle_time }}</td>
                    <td class="text-center align-middle">{{ calcList.missing_middle_return_time }}</td>
                    <td class="text-center align-middle">{{ calcList.working_status_name }}</td>
                    <td class="text-center align-middle">{{ calcList.total_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.regular_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.off_hours_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.late_night_overtime_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.not_employment_working_hours }}</td>
                    <td class="text-left align-middle">{{ calcList.remark_holiday_name }} {{ calcList.remark_check_result }} {{ calcList.remark_check_max_times }} {{ calcList.remark_check_interval }}</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='detailswitch'"
                    v-for="(calcList,index) in calcLists"
                  >
                    <td class="text-center align-middle">{{ calcList.department_name }}</td>
                    <td class="text-center align-middle">{{ calcList.employment_status_name }}</td>
                    <td class="text-center align-middle">{{ calcList.user_name }}</td>
                    <td class="text-center align-middle">{{ calcList.attendance_time }}</td>
                    <td class="text-center align-middle">{{ calcList.leaving_time }}</td>
                    <td class="text-center align-middle">{{ calcList.public_going_out_time }}</td>
                    <td class="text-center align-middle">{{ calcList.public_going_out_return_time }}</td>
                    <td class="text-center align-middle">{{ calcList.missing_middle_time }}</td>
                    <td class="text-center align-middle">{{ calcList.missing_middle_return_time }}</td>
                    <td class="text-center align-middle">{{ calcList.working_status_name }}</td>
                    <td class="text-center align-middle">{{ calcList.total_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.regular_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.out_of_regular_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.off_hours_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.late_night_overtime_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.legal_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.out_of_legal_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.not_employment_working_hours }}</td>
                    <td class="text-left align-middle">{{ calcList.remark_holiday_name }} {{ calcList.remark_check_result }} {{ calcList.remark_check_max_times }} {{ calcList.remark_check_interval }}</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'total' && btnMode ==='basicswitch'"
                    v-for="(calcList,index) in calcLists"
                  >
                    <td class="text-center align-middle">{{ calcList.total_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.regular_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.off_hours_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.late_night_overtime_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.not_employment_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.total_working_status }}</td>
                    <td class="text-center align-middle">{{ calcList.total_go_out }}</td>
                    <td class="text-center align-middle">{{ calcList.total_paid_holidays }}</td>
                    <td class="text-center align-middle">{{ calcList.total_holiday_kubun }}</td>
                    <td class="text-center align-middle">{{ calcList.total_leave_early }}</td>
                    <td class="text-center align-middle">{{ calcList.total_late }}</td>
                    <td class="text-center align-middle">{{ calcList.total_absence }}</td>
                    <td class="text-left align-middle"> </td>
                  </tr>
                  <tr v-if="detailOrTotal === 'total' && btnMode ==='detailswitch'"
                    v-for="(calcList,index) in calcLists"
                  >
                    <td class="text-center align-middle">{{ calcList.total_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.regular_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.out_of_regular_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.off_hours_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.late_night_overtime_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.legal_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.out_of_legal_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.not_employment_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.total_working_status }}</td>
                    <td class="text-center align-middle">{{ calcList.total_go_out }}</td>
                    <td class="text-center align-middle">{{ calcList.total_paid_holidays }}</td>
                    <td class="text-center align-middle">{{ calcList.total_holiday_kubun }}</td>
                    <td class="text-center align-middle">{{ calcList.total_leave_early }}</td>
                    <td class="text-center align-middle">{{ calcList.total_late }}</td>
                    <td class="text-center align-middle">{{ calcList.total_absence }}</td>
                    <td class="text-left align-middle"> </td>
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
  name: "dailyworkinginfotable",
  props: {
    detailOrTotal: {
      type: String,
      default: 'detail'
    },
    calcLists: {
      type: Array
    },
    btnMode: {
      type: String,
      default: ''
    },
    predeterTimeName: {
      type: String,
      default: '残業時間'
    },
    predeterNightTimeName: {
      type: String,
      default: '深夜残業時間'
    }
  },
  // マウント時
  mounted() {
    console.log("dailyworkinginfotable  マウント");
  },
  data: function() {
    return {
      edtString: ""
    };
  },
  methods: {
    // tooltips
    edttooltips: function(value1, value2) {
      if (value1.length > 0) {
        this.edtString = value1;
      }
      if (value2.length > 0) {
        this.edtString = this.edtString + '\n' + value2;
      }
    }
  }
};
</script>
