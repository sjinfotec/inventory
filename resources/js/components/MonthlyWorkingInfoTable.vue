<template>
  <div >
    <table class="table table-striped table-bordered">
      <tr v-if="detailOrTotal === 'detail'" bgcolor="#add8e6">
        <th>雇用形態</th>
        <th>労働合計時間</th>
        <th>残業時間</th>
        <th>深夜残業時間</th>
        <th>公用外出時間</th>
        <th>私用外出時間</th>
        <th>未就労時間</th>
        <th>出勤日数</th>
        <th>休暇日数</th>
        <th>欠勤日数</th>
        <th>有給休暇取得日数</th>
        <th>備考</th>
      </tr>
      <tr v-if="calcLists.length && detailOrTotal === 'total'" bgcolor="#7fffd4">
        <th>労働合計時間</th>
        <th>残業時間</th>
        <th>深夜残業時間</th>
        <th>公用外出時間</th>
        <th>私用外出時間</th>
        <th>未就労時間</th>
        <th>出勤日数</th>
        <th>休暇日数</th>
        <th>欠勤日数</th>
        <th>有給休暇取得日数</th>
        <th>備考</th>
      </tr>
      <tr v-if="detailOrTotal === 'detail'"
        class="card-body mb-3 py-0 pt-4 border-top"
      >
        <td class="text-align-center">{{ calcLists.employment }}</td>
        <td class="text-align-center">{{ calcLists.total_working_times }}</td>
        <td class="text-align-center">{{ calcLists.overtime_hours }}</td>
        <td class="text-align-center">{{ calcLists.late_night_overtime_hours }}</td>
        <td class="text-align-center">{{ calcLists.not_employment_working_hours }}</td>
        <td class="text-align-center">{{ calcLists.not_employment_working_hours }}</td>
        <td class="text-align-center">{{ calcLists.not_employment_working_hours }}</td>
        <td class="text-align-center">{{ calcLists.total_working_status }} 日</td>
        <td class="text-align-center">{{ calcLists.total_holiday_kubun }} 日</td>
        <td class="text-align-center">{{ calcLists.total_go_out }} 日</td>
        <td class="text-align-center">{{ calcLists.total_go_out }} 日</td>
        <td class="text-align-center"> </td>
      </tr>
      <tr v-if="detailOrTotal === 'total'"
        v-for="(calcList,index) in calcLists"
        class="card-body mb-3 py-0 pt-4 border-top"
      >
        <td class="text-align-center">{{ calcList.total_working_times }}</td>
        <td class="text-align-center">{{ calcList.overtime_hours }}</td>
        <td class="text-align-center">{{ calcList.late_night_overtime_hours }}</td>
        <td class="text-align-center">{{ calcList.not_employment_working_hours }}</td>
        <td class="text-align-center">{{ calcList.not_employment_working_hours }}</td>
        <td class="text-align-center">{{ calcList.not_employment_working_hours }}</td>
        <td class="text-align-center">{{ calcList.total_working_status }} 日</td>
        <td class="text-align-center">{{ calcList.total_holiday_kubun }} 日</td>
        <td class="text-align-center">{{ calcList.total_go_out }} 日</td>
        <td class="text-align-center">{{ calcList.total_go_out }} 日</td>
        <td class="text-align-center"> </td>
      </tr>
    </table>
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
      type: Array
    }
  }
};
</script>
