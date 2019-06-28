<template>
  <table class="table" id="daily_table">
    <thead>
      <tr>
        <th>No</th>
        <th>社員名</th>
        <th>打刻時間</th>
        <th>モード</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="item in dailies" :key="item.id">
        <td>{{ item.id }}</td>
        <td>{{ item.user_code }}</td>
        <td>{{ item.record_time }}</td>
        <td>{{ item.mode }}</td>
        <td>
          <button type="button" class="btn btn-default">編集</button>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  name: "Daily",
  data() {
    return {
      dailies: []
    };
  },
  mounted() {
    console.log("RRR Component mounted.");
    this.$axios
      .get("/daily/show")
      .then(response => {
        this.dailies = response.data;
        console.log(this.dailies);
        // alert(this.contents.work_times[0].user_code);
      })
      .catch(reason => {
        alert("error");
      });
  }
};
</script>
