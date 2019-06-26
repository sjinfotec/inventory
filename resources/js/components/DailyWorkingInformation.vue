<template>
  <table class="table" id="process_table">
    <thead>
      <tr>
        <th>社員名</th>
        <th>出勤</th>
        <th>退勤</th>
        <th>編集</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>aaa</td>
        <td>bbb</td>
        <td>ccc</td>
        <td>ddd</td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  mounted() {
    console.log("daily Component mounted.");
  }
  // props: {
  //   task: {
  //     type: Array,
  //     required: true
  //   }
  // }
};
</script>
