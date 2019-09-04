<template>
  <table class="table table-striped table-bordered">
    <tr>
      <th>日付</th>
      <th>雇用形態</th>
      <th>部署</th>
      <th>氏名</th>
      <th>打刻時刻</th>
      <th>打刻モード</th>
      <th>警告内容</th>
    </tr>
    <tr
      v-for="(alertList,index) in alertLists"
      class="card-body mb-3 py-0 pt-4 border-top"
    >
      <td class="text-align-center">{{ alertList.record_date }}</td>
      <td class="text-align-center">{{ alertList.employment_status_name }}</td>
      <td class="text-align-center">{{ alertList.department_name }}</td>
      <td class="text-align-center">{{ alertList.user_name }}</td>
      <td class="text-align-center">{{ alertList.record_time }}</td>
      <td class="text-align-center">{{ alertList.mode_name }}</td>
      <td class="text-align-center">{{ alertList.alert_memo }}</td>
    </tr>
  </table>
</template>
<script>

export default {
  name: "dailyworkingalerttable",
  props: {
    alertLists: {
      type: Array
    }
  },
  // マウント時
  mounted() {
    console.log(this.name + "マウント");
  }
};
</script>
