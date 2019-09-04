<template>
  <table class="table table-striped table-bordered">
    <tr>
      <th>日付</th>
      <th>雇用形態</th>
      <th>部署</th>
      <th>氏名</th>
      <th>開始時刻</th>
      <th>終了時刻</th>
      <th>打刻モード</th>
      <th>警告内容</th>
    </tr>
    <tr
      v-for="(alertlist,index) in alertlists"
      class="card-body mb-3 py-0 pt-4 border-top"
    >
      <td class="text-align-center">{{ working_date }}</td>
      <td class="text-align-center">{{ employment_status_name }}</td>
      <td class="text-align-center">{{ department_name }}</td>
      <td class="text-align-center">{{ user_name }}</td>
      <td class="text-align-center">{{ from_time }}</td>
      <td class="text-align-center">{{ to_time }}</td>
      <td class="text-align-center">{{ mode_name }}</td>
      <td class="text-align-center">{{ note }}</td>
    </tr>
  </table>
</template>
<script>

export default {
  name: "dailyworkingalerttable",
  props: {
    alertLists: []
  },
  data() {
    return {
      selectedDepartment: '',
      departmentList: []
    };
  },
  // マウント時
  mounted() {
    console.log(this.name + "マウント");
  }
};
</script>
