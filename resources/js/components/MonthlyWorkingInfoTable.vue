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
                    <td class="text-center align-middle w-15">雇用形態</td>
                    <td class="text-center align-middle w-15"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定時間 + 残業時間 + 深夜残業時間', '')"
                    >実働時間</td>
                    <td class="text-center align-middle w-15">所定時間</td>
                    <td class="text-center align-middle w-15">残業時間</td>
                    <td class="text-center align-middle w-15">深夜残業時間</td>
                    <td class="text-center align-middle w-15">公用外出時間</td>
                    <td class="text-center align-middle w-15">私用外出時間</td>
                    <td class="text-center align-middle w-15"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >不就労時間</td>
                    <td class="text-center align-middle w-15">出勤日数</td>
                    <td class="text-center align-middle w-15">有給休暇日数</td>
                    <td class="text-center align-middle w-15">特別休暇日数</td>
                    <td class="text-center align-middle w-15">早退日数</td>
                    <td class="text-center align-middle w-15">遅刻日数</td>
                    <td class="text-center align-middle w-15">欠勤日数</td>
                    <td class="text-center align-middle w-35 mw-rem-20">備考</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='detailswitch'">
                    <td class="text-center align-middle w-15">雇用形態</td>
                    <td class="text-center align-middle w-15"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定時間 + 残業時間 + 深夜残業時間', '')"
                    >実働時間</td>
                    <td class="text-center align-middle w-15">所定時間</td>
                    <td class="text-center align-middle w-15">所定外時間</td>
                    <td class="text-center align-middle w-15">残業時間</td>
                    <td class="text-center align-middle w-15">深夜残業時間</td>
                    <td class="text-center align-middle w-15">法定時間</td>
                    <td class="text-center align-middle w-15">法定外時間</td>
                    <td class="text-center align-middle w-15">公用外出時間</td>
                    <td class="text-center align-middle w-15">私用外出時間</td>
                    <td class="text-center align-middle w-15"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >不就労時間</td>
                    <td class="text-center align-middle w-15">出勤日数</td>
                    <td class="text-center align-middle w-15">有給休暇日数</td>
                    <td class="text-center align-middle w-15">特別休暇日数</td>
                    <td class="text-center align-middle w-15">早退日数</td>
                    <td class="text-center align-middle w-15">遅刻日数</td>
                    <td class="text-center align-middle w-15">欠勤日数</td>
                    <td class="text-center align-middle w-35 mw-rem-20">備考</td>
                  </tr>
                  <tr v-if="calcLists.length && detailOrTotal === 'total' && btnMode ==='basicswitch'" bgcolor="#7fffd4">
                    <td class="text-center align-middle w-15"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定時間 + 残業時間 + 深夜残業時間', '')"
                    >実働時間</td>
                    <td class="text-center align-middle w-15">所定時間</td>
                    <td class="text-center align-middle w-15">残業時間</td>
                    <td class="text-center align-middle w-15">深夜残業時間</td>
                    <td class="text-center align-middle w-15">公用外出時間</td>
                    <td class="text-center align-middle w-15">私用外出時間</td>
                    <td class="text-center align-middle w-15"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >不就労時間</td>
                    <td class="text-center align-middle w-15">出勤日数</td>
                    <td class="text-center align-middle w-15">有給休暇日数</td>
                    <td class="text-center align-middle w-15">特別休暇日数</td>
                    <td class="text-center align-middle w-15">早退日数</td>
                    <td class="text-center align-middle w-15">遅刻日数</td>
                    <td class="text-center align-middle w-15">欠勤日数</td>
                    <td class="text-center align-middle w-35 mw-rem-20">備考</td>
                  </tr>
                  <tr v-if="calcLists.length && detailOrTotal === 'total' && btnMode ==='detailswitch'" bgcolor="#7fffd4">
                    <td class="text-center align-middle w-15"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定時間 + 残業時間 + 深夜残業時間', '')"
                    >実働時間</td>
                    <td class="text-center align-middle w-15">所定時間</td>
                    <td class="text-center align-middle w-15">所定外時間</td>
                    <td class="text-center align-middle w-15">残業時間</td>
                    <td class="text-center align-middle w-15">深夜残業時間</td>
                    <td class="text-center align-middle w-15">法定時間</td>
                    <td class="text-center align-middle w-15">法定外時間</td>
                    <td class="text-center align-middle w-15">公用外出時間</td>
                    <td class="text-center align-middle w-15">私用外出時間</td>
                    <td class="text-center align-middle w-15"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >不就労時間</td>
                    <td class="text-center align-middle w-15">出勤日数</td>
                    <td class="text-center align-middle w-15">有給休暇日数</td>
                    <td class="text-center align-middle w-15">特別休暇日数</td>
                    <td class="text-center align-middle w-15">早退日数</td>
                    <td class="text-center align-middle w-15">遅刻日数</td>
                    <td class="text-center align-middle w-15">欠勤日数</td>
                    <td class="text-center align-middle w-35 mw-rem-20">備考</td>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='basicswitch'"
                  >
                    <td class="text-align-center">{{ calcLists.employment }}</td>
                    <td class="text-align-right"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定時間 + 残業時間 + 深夜残業時間', '')"
                    >{{ calcLists.total_working_times }}</td>
                    <td class="text-align-right">{{ calcLists.regular_working_times }}</td>
                    <td class="text-align-right">{{ calcLists.off_hours_working_hours }}</td>
                    <td class="text-align-right">{{ calcLists.late_night_overtime_hours }}</td>
                    <td class="text-align-right">{{ calcLists.public_going_out_hours }}</td>
                    <td class="text-align-right">{{ calcLists.missing_middle_hours }}</td>
                    <td class="text-align-right"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >{{ calcLists.not_employment_working_hours }}</td>
                    <td class="text-align-right">{{ calcLists.total_working_status }} 日</td>
                    <td class="text-align-right">{{ calcLists.total_paid_holidays }} 日</td>
                    <td class="text-align-right">{{ calcLists.total_holiday_kubun }} 日</td>
                    <td class="text-align-right">{{ calcLists.total_leave_early }} 日</td>
                    <td class="text-align-right">{{ calcLists.total_late }} 日</td>
                    <td class="text-align-right">{{ calcLists.total_absence }} 日</td>
                    <td class="text-align-left"> </td>
                  </tr>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='detailswitch'"
                  >
                    <td class="text-align-center">{{ calcLists.employment }}</td>
                    <td class="text-align-right"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定時間 + 残業時間 + 深夜残業時間', '')"
                    >{{ calcLists.total_working_times }}</td>
                    <td class="text-align-right">{{ calcLists.regular_working_times }}</td>
                    <td class="text-align-right">{{ calcLists.out_of_regular_working_times }}</td>
                    <td class="text-align-right">{{ calcLists.off_hours_working_hours }}</td>
                    <td class="text-align-right">{{ calcLists.late_night_overtime_hours }}</td>
                    <td class="text-align-right">{{ calcLists.legal_working_times }}</td>
                    <td class="text-align-right">{{ calcLists.out_of_legal_working_times }}</td>
                    <td class="text-align-right">{{ calcLists.public_going_out_hours }}</td>
                    <td class="text-align-right">{{ calcLists.missing_middle_hours }}</td>
                    <td class="text-align-right"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >{{ calcLists.not_employment_working_hours }}</td>
                    <td class="text-align-right">{{ calcLists.total_working_status }} 日</td>
                    <td class="text-align-right">{{ calcLists.total_paid_holidays }} 日</td>
                    <td class="text-align-right">{{ calcLists.total_holiday_kubun }} 日</td>
                    <td class="text-align-right">{{ calcLists.total_leave_early }} 日</td>
                    <td class="text-align-right">{{ calcLists.total_late }} 日</td>
                    <td class="text-align-right">{{ calcLists.total_absence }} 日</td>
                    <td class="text-align-left"> </td>
                  </tr>
                  <tr v-if="detailOrTotal === 'total' && btnMode ==='basicswitch'"
                    v-for="(calcList,index) in calcLists"
                  >
                    <td class="text-align-center"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定時間 + 残業時間 + 深夜残業時間', '')"
                    >{{ calcList.total_working_times }}</td>
                    <td class="text-align-center">{{ calcList.regular_working_times }}</td>
                    <td class="text-align-center">{{ calcList.off_hours_working_hours }}</td>
                    <td class="text-align-center">{{ calcList.late_night_overtime_hours }}</td>
                    <td class="text-align-center">{{ calcList.public_going_out_hours }}</td>
                    <td class="text-align-center">{{ calcList.missing_middle_hours }}</td>
                    <td class="text-align-center"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >{{ calcList.not_employment_working_hours }}</td>
                    <td class="text-align-center">{{ calcList.total_working_status }} 日</td>
                    <td class="text-align-center">{{ calcList.total_paid_holidays }} 日</td>
                    <td class="text-align-center">{{ calcList.total_holiday_kubun }} 日</td>
                    <td class="text-align-center">{{ calcList.total_leave_early }} 日</td>
                    <td class="text-align-center">{{ calcList.total_late }} 日</td>
                    <td class="text-align-center">{{ calcList.total_absence }} 日</td>
                    <td class="text-align-center"> </td>
                  </tr>
                  <tr v-if="detailOrTotal === 'total' && btnMode ==='detailswitch'"
                    v-for="(calcList,index) in calcLists"
                  >
                    <td class="text-align-center"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定時間 + 残業時間 + 深夜残業時間', '')"
                    >{{ calcList.total_working_times }}</td>
                    <td class="text-align-center">{{ calcList.regular_working_times }}</td>
                    <td class="text-align-center">{{ calcList.out_of_regular_working_times }}</td>
                    <td class="text-align-center">{{ calcList.off_hours_working_hours }}</td>
                    <td class="text-align-center">{{ calcList.late_night_overtime_hours }}</td>
                    <td class="text-align-center">{{ calcList.legal_working_times }}</td>
                    <td class="text-align-center">{{ calcList.out_of_legal_working_times }}</td>
                    <td class="text-align-center">{{ calcList.public_going_out_hours }}</td>
                    <td class="text-align-center">{{ calcList.missing_middle_hours }}</td>
                    <td class="text-align-center"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >{{ calcList.not_employment_working_hours }}</td>
                    <td class="text-align-center">{{ calcList.total_working_status }} 日</td>
                    <td class="text-align-center">{{ calcList.total_paid_holidays }} 日</td>
                    <td class="text-align-center">{{ calcList.total_holiday_kubun }} 日</td>
                    <td class="text-align-center">{{ calcList.total_leave_early }} 日</td>
                    <td class="text-align-center">{{ calcList.total_late }} 日</td>
                    <td class="text-align-center">{{ calcList.total_absence }} 日</td>
                    <td class="text-align-center"> </td>
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
    }
  }
};
</script>
