<template>
  <button class="btn btn-primary" v-on:click="downloadCSV">集計結果をCSVファイルに出力する</button>
</template>
<script>
export default {
  name: "btnCsvDownload",
  props: {
    csvData: {
      type: Array,
      required: true
    },
    date: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      items: [{}]
    };
  },
  // マウント時
  mounted() {},
  methods: {
    downloadCSV() {
      var csv = "";
      var user_head = "";
      var user_line = "";
      var data_head = "";
      var line = "";
      // 1ユーザーごと
      this.csvData.forEach(user => {
        user_head = "";
        user_line = "";
        data_head = "";

        user_head =
          "\ufeff" +
          "社員名,部署,雇用形態,勤務時間,所定労働時間,所定外労働時間,残業時間,深夜残業時間\n";

        user_line =
          user["user_name"] +
          "," +
          user["department"] +
          "," +
          user["employment"] +
          "," +
          user["total_working_times"] +
          "," +
          user["regular_working_times"] +
          "," +
          user["out_of_regular_working_times"] +
          "," +
          user["overtime_hours"] +
          "," +
          user["late_night_overtime_hours"] +
          "\n\n";
        data_head =
          "\ufeff" +
          "日付,出勤1,出勤2,出勤3,出勤4,出勤5,退勤1,退勤2,退勤3,退勤4,退勤5\n";

        csv += user_head;
        csv += user_line;
        csv += data_head;

        user.date.forEach(record => {
          console.log(record);

          line =
            record["workingdate"] +
            "," +
            record["attendance1"] +
            "," +
            record["attendance2"] +
            "," +
            record["attendance3"] +
            "," +
            record["attendance4"] +
            "," +
            record["attendance5"] +
            "," +
            record["leaving1"] +
            "," +
            record["leaving2"] +
            "," +
            record["leaving3"] +
            "," +
            record["leaving4"] +
            "," +
            record["leaving5"] +
            "\n";
          csv += line;
        });
        line = "";
      });
      let blob = new Blob([csv], { type: "text/csv" });
      let link = document.createElement("a");
      link.href = window.URL.createObjectURL(blob);
      link.download = this.date + "_月次集計.csv";
      link.click();
    }
  }
};
</script>
