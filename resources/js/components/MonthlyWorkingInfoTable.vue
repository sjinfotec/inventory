<template>
  <div >
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
                    <td class="text-center align-middle w-15 mw-rem-7">雇用形態</td>
                    <td class="text-center align-middle w-15 mw-rem-5 color-royalblue"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定 + 残業 + 深夜残業 + 深夜労働', '','','')"
                    >実働</td>
                    <td class="text-center align-middle w-15 mw-rem-5">所定</td>
                    <td class="text-center align-middle w-15 mw-rem-5">残業</td>
                    <td class="text-center align-middle w-15 mw-rem-5">深夜残業</td>
                    <td class="text-center align-middle w-15 mw-rem-5">深夜労働</td>
                    <td class="text-center align-middle w-15 mw-rem-5">公外</td>
                    <td class="text-center align-middle w-15 mw-rem-5">私外</td>
                    <td class="text-center align-middle w-15 mw-rem-5"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                    >不就労</td>
                    <td class="text-center align-middle w-15 mw-rem-5">出勤日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">有休日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">特休日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">早退日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">遅刻日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">欠勤日数</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='detailswitch'">
                    <td class="text-center align-middle w-15 mw-rem-7">雇用形態</td>
                    <td class="text-center align-middle w-15 mw-rem-3 color-royalblue"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定 + 残業 + 深夜残業 + 深夜労働', '','','')"
                    >実働時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5">所定</td>
                    <td class="text-center align-middle w-15 mw-rem-5">所外</td>
                    <td class="text-center align-middle w-15 mw-rem-5">残業</td>
                    <td class="text-center align-middle w-15 mw-rem-5">深夜残業</td>
                    <td class="text-center align-middle w-15 mw-rem-5">深夜労働</td>
                    <td class="text-center align-middle w-15 mw-rem-5">法定</td>
                    <td class="text-center align-middle w-15 mw-rem-5">法外</td>
                    <td class="text-center align-middle w-15 mw-rem-5">公外</td>
                    <td class="text-center align-middle w-15 mw-rem-5">私外</td>
                    <td class="text-center align-middle w-15 mw-rem-5"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                    >不就労</td>
                    <td class="text-center align-middle w-15 mw-rem-5">出勤日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">有休日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">特休日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">早退日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">遅刻日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">欠勤日数</td>
                  </tr>
                  <tr v-if="calcLists.length && detailOrTotal === 'total' && btnMode ==='basicswitch'" bgcolor="#7fffd4">
                    <td class="text-center align-middle w-15 mw-rem-5"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定時間 + 普通残業時間 + 深夜残業時間 + 深夜労働時間', '','','')"
                    >実働時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5">所定時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5">普通残業時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5">深夜残業時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5">深夜労働時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5">公用外出時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5">私用外出時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                    >不就労時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5">出勤日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">有給休暇日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">特別休暇日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">早退日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">遅刻日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">欠勤日数</td>
                  </tr>
                  <tr v-if="calcLists.length && detailOrTotal === 'total' && btnMode ==='detailswitch'" bgcolor="#7fffd4">
                    <td class="text-center align-middle w-15 mw-rem-5"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定 + 普通残業時間 + 深夜残業時間 + 深夜労働時間', '','','')"
                    >実働時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5">所定時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5">所定外時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5">普通残業時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5">深夜残業時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5">深夜労働時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5">法定時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5">法定外時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5">公用外出時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5">私用外出時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                    >不就労時間</td>
                    <td class="text-center align-middle w-15 mw-rem-5">出勤日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">有給休暇日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">特別休暇日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">早退日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">遅刻日数</td>
                    <td class="text-center align-middle w-15 mw-rem-5">欠勤日数</td>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='basicswitch'"
                  >
                    <td class="text-center align-middle">{{ calcLists.employment }}</td>
                    <td class="text-center align-middle"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定 + 残業 + 深夜残業 + 深夜労働', '','','')"
                    >{{ calcLists.total_working_times }}</td>
                    <td class="text-center align-middle">{{ calcLists.regular_working_times }}</td>
                    <td class="text-center align-middle">{{ calcLists.off_hours_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcLists.late_night_overtime_hours }}</td>
                    <td class="text-center align-middle">{{ calcLists.late_night_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcLists.public_going_out_hours }}</td>
                    <td class="text-center align-middle">{{ calcLists.missing_middle_hours }}</td>
                    <td class="text-center align-middle"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                    >{{ calcLists.not_employment_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcLists.total_working_status }} 日</td>
                    <td class="text-center align-middle">{{ calcLists.total_paid_holidays }} 日</td>
                    <td class="text-center align-middle">{{ calcLists.total_holiday_kubun }} 日</td>
                    <td class="text-center align-middle">{{ calcLists.total_leave_early }} 日</td>
                    <td class="text-center align-middle">{{ calcLists.total_late }} 日</td>
                    <td class="text-center align-middle">{{ calcLists.total_absence }} 日</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='detailswitch'"
                  >
                    <td class="text-center align-middle">{{ calcLists.employment }}</td>
                    <td class="text-center align-middle"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定 + 残業時間 + 深夜残業 + 深夜労働', '','','')"
                    >{{ calcLists.total_working_times }}</td>
                    <td class="text-center align-middle">{{ calcLists.regular_working_times }}</td>
                    <td class="text-center align-middle">{{ calcLists.out_of_regular_working_times }}</td>
                    <td class="text-center align-middle">{{ calcLists.off_hours_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcLists.late_night_overtime_hours }}</td>
                    <td class="text-center align-middle">{{ calcLists.late_night_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcLists.legal_working_times }}</td>
                    <td class="text-center align-middle">{{ calcLists.out_of_legal_working_times }}</td>
                    <td class="text-center align-middle">{{ calcLists.public_going_out_hours }}</td>
                    <td class="text-center align-middle">{{ calcLists.missing_middle_hours }}</td>
                    <td class="text-center align-middle"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                    >{{ calcLists.not_employment_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcLists.total_working_status }} 日</td>
                    <td class="text-center align-middle">{{ calcLists.total_paid_holidays }} 日</td>
                    <td class="text-center align-middle">{{ calcLists.total_holiday_kubun }} 日</td>
                    <td class="text-center align-middle">{{ calcLists.total_leave_early }} 日</td>
                    <td class="text-center align-middle">{{ calcLists.total_late }} 日</td>
                    <td class="text-center align-middle">{{ calcLists.total_absence }} 日</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'total' && btnMode ==='basicswitch'"
                    v-for="(calcList,index) in calcLists"
                  >
                    <td class="text-center align-middle"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定 + 普通残業時間 + 深夜残業時間+ 深夜労働時間', '','','')"
                    >{{ calcList.total_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.regular_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.off_hours_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.late_night_overtime_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.late_night_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.public_going_out_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.missing_middle_hours }}</td>
                    <td class="text-center align-middle"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                    >{{ calcList.not_employment_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.total_working_status }} 日</td>
                    <td class="text-center align-middle">{{ calcList.total_paid_holidays }} 日</td>
                    <td class="text-center align-middle">{{ calcList.total_holiday_kubun }} 日</td>
                    <td class="text-center align-middle">{{ calcList.total_leave_early }} 日</td>
                    <td class="text-center align-middle">{{ calcList.total_late }} 日</td>
                    <td class="text-center align-middle">{{ calcList.total_absence }} 日</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'total' && btnMode ==='detailswitch'"
                    v-for="(calcList,index) in calcLists"
                  >
                    <td class="text-center align-middle"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定 + 普通残業時間 + 深夜残業時間 + 深夜労働時間', '','','')"
                    >{{ calcList.total_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.regular_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.out_of_regular_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.off_hours_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.late_night_overtime_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.late_night_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.legal_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.out_of_legal_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.public_going_out_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.missing_middle_hours }}</td>
                    <td class="text-center align-middle"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                    >{{ calcList.not_employment_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.total_working_status }} 日</td>
                    <td class="text-center align-middle">{{ calcList.total_paid_holidays }} 日</td>
                    <td class="text-center align-middle">{{ calcList.total_holiday_kubun }} 日</td>
                    <td class="text-center align-middle">{{ calcList.total_leave_early }} 日</td>
                    <td class="text-center align-middle">{{ calcList.total_late }} 日</td>
                    <td class="text-center align-middle">{{ calcList.total_absence }} 日</td>
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
    edttooltips: function(value1, value2) {
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
.table th, .table td {
    padding: 0rem !important;
}

.mw-rem-3 {
  min-width: 3rem !important;
}

.mw-rem-7 {
  min-width: 7rem !important;
}
</style>
