<template>
  <div v-if="calcLists.length">
    <table class="table table-striped table-bordered">
      <tr v-if="detailOrTotal === 'detail'" bgcolor="#add8e6">
        <th>部署</th>
        <th>雇用形態</th>
        <th>氏名</th>
        <th>出勤</th>
        <th>退勤</th>
        <th colspan="2">公用外出</th>
        <th colspan="2">私用外出</th>
        <th>勤務状態</th>
        <th>労働合計時間</th>
        <th>{{ predeterTimeName }}</th>
        <th>{{ predeterNightTimeName }}</th>
        <th>未就労時間</th>
        <th>備考</th>
      </tr>
      <tr v-if="detailOrTotal === 'total'" bgcolor="#7fffd4">
        <th>労働合計時間</th>
        <th>{{ predeterTimeName }}</th>
        <th>{{ predeterNightTimeName }}</th>
        <th>未就労時間</th>
        <th>出勤者数</th>
        <th>外出者数</th>
        <th>休暇者数</th>
        <th>欠勤者数</th>
        <th>備考</th>
      </tr>
      <tr v-if="detailOrTotal === 'detail'"
        v-for="(calcList,index) in calcLists"
        class="card-body mb-3 py-0 pt-4 border-top"
      >
        <td class="text-align-center">{{ calcList.department_name }}</td>
        <td class="text-align-center">{{ calcList.employment_status_name }}</td>
        <td class="text-align-center">{{ calcList.user_name }}</td>
        <td class="text-align-center">{{ calcList.attendance_time_1 }}</td>
        <td class="text-align-center">{{ calcList.leaving_time_1 }}</td>
        <td class="text-align-center">{{ calcList.public_going_out_time_1 }}</td>
        <td class="text-align-center">{{ calcList.public_going_out_return_time_1 }}</td>
        <td class="text-align-center">{{ calcList.missing_middle_time_1 }}</td>
        <td class="text-align-center">{{ calcList.missing_middle_return_time_1 }}</td>
        <td class="text-align-center">{{ calcList.working_status_name }}</td>
        <td class="text-align-center">{{ calcList.total_working_times }}</td>
        <td class="text-align-center">{{ calcList.off_hours_working_hours }}</td>
        <td class="text-align-center">{{ calcList.late_night_overtime_hours }}</td>
        <td class="text-align-center">{{ calcList.not_employment_working_hours }}</td>
        <td class="text-align-center">{{ calcList.remark_data }}　{{ calcList.unused_holiday_name }}</td>
      </tr>
      <tr v-if="detailOrTotal === 'total'"
        v-for="(calcList,index) in calcLists"
        class="card-body mb-3 py-0 pt-4 border-top"
      >
        <td class="text-align-center">{{ calcList.total_working_times }}</td>
        <td class="text-align-center">{{ calcList.off_hours_working_hours }}</td>
        <td class="text-align-center">{{ calcList.late_night_overtime_hours }}</td>
        <td class="text-align-center">{{ calcList.not_employment_working_hours }}</td>
        <td class="text-align-center">{{ calcList.total_working_status }}</td>
        <td class="text-align-center">{{ calcList.total_go_out }}</td>
        <td class="text-align-center">{{ calcList.total_holiday_kubun }}</td>
        <td class="text-align-center">{{ calcList.total_absence }}</td>
        <td class="text-align-center"> </td>
      </tr>
    </table>
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
  }
};
</script>
