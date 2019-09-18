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
                  <tr v-if="detailOrTotal === 'detail'" bgcolor="#add8e6">
                    <td class="text-center align-middle w-15">雇用形態</td>
                    <td class="text-center align-middle w-15">労働合計時間</td>
                    <td class="text-center align-middle w-15">残業時間</td>
                    <td class="text-center align-middle w-15">深夜残業時間</td>
                    <td class="text-center align-middle w-15">公用外出時間</td>
                    <td class="text-center align-middle w-15">私用外出時間</td>
                    <td class="text-center align-middle w-15">未就労時間</td>
                    <td class="text-center align-middle w-15">出勤日数</td>
                    <td class="text-center align-middle w-15">有給休暇日数</td>
                    <td class="text-center align-middle w-15">特別休暇日数</td>
                    <td class="text-center align-middle w-15">早退日数</td>
                    <td class="text-center align-middle w-15">遅刻日数</td>
                    <td class="text-center align-middle w-15">欠勤日数</td>
                    <td class="text-center align-middle w-35 mw-rem-20">備考</td>
                  </tr>
                  <tr v-if="calcLists.length && detailOrTotal === 'total'" bgcolor="#7fffd4">
                    <td class="text-center align-middle w-15">労働合計時間</td>
                    <td class="text-center align-middle w-15">残業時間</td>
                    <td class="text-center align-middle w-15">深夜残業時間</td>
                    <td class="text-center align-middle w-15">公用外出時間</td>
                    <td class="text-center align-middle w-15">私用外出時間</td>
                    <td class="text-center align-middle w-15">未就労時間</td>
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
                  <tr v-if="detailOrTotal === 'detail'"
                  >
                    <td class="text-align-center">{{ calcLists.employment }}</td>
                    <td class="text-align-right">{{ calcLists.total_working_times }}</td>
                    <td class="text-align-right">{{ calcLists.off_hours_working_hours }}</td>
                    <td class="text-align-right">{{ calcLists.late_night_overtime_hours }}</td>
                    <td class="text-align-right">{{ calcLists.public_going_out_hours }}</td>
                    <td class="text-align-right">{{ calcLists.missing_middle_hours }}</td>
                    <td class="text-align-right">{{ calcLists.not_employment_working_hours }}</td>
                    <td class="text-align-right">{{ calcLists.total_working_status }} 日</td>
                    <td class="text-align-right">{{ calcLists.total_paid_holidays }} 日</td>
                    <td class="text-align-right">{{ calcLists.total_holiday_kubun }} 日</td>
                    <td class="text-align-right">{{ calcLists.total_leave_early }} 日</td>
                    <td class="text-align-right">{{ calcLists.total_late }} 日</td>
                    <td class="text-align-right">{{ calcLists.total_absence }} 日</td>
                    <td class="text-align-left"> </td>
                  </tr>
                  <tr v-if="detailOrTotal === 'total'"
                    v-for="(calcList,index) in calcLists"
                  >
                    <td class="text-align-center">{{ calcList.total_working_times }}</td>
                    <td class="text-align-center">{{ calcList.off_hours_working_hours }}</td>
                    <td class="text-align-center">{{ calcList.late_night_overtime_hours }}</td>
                    <td class="text-align-center">{{ calcList.public_going_out_hours }}</td>
                    <td class="text-align-center">{{ calcList.missing_middle_hours }}</td>
                    <td class="text-align-center">{{ calcList.not_employment_working_hours }}</td>
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
  }
};
</script>
