<template>
  <button type="button" class="btn btn-success btn-lg font-size-rg w-100"　:disabled="isCsvbutton" v-on:click="downloadCSV">
    <img class="icon-size-sm mr-2 pb-1" src="/images/round-get-app-w.svg" alt />集計結果をCSVファイルに出力する
  </button>
</template>
<script>
import moment from "moment";

export default {
  name: "btnCsvDownload",
  props: {
    csvData: {
      type: Array,
      required: true
    },
    csvDate: {
      type: String,
      required: ""
    },
    isCsvbutton: {
      type: Boolean,
      required: true
    }
  },
  data() {
    return {
      items: [{}]
    };
  },
  // マウント時
  mounted() {console.log("btnCsvDownload マウント");},
  methods: {
    downloadCSV() {
      console.log("downloadCSV");
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
          "社員名,部署,雇用形態\n";

        user_line =
          user["user_name"] +
          "," +
          user["department"] +
          "," +
          user["employment"] +
          "\n";
        data_head =
          "\ufeff" +
          "日付,出勤,退勤,実働時間,所定時間,残業時間,深夜時間,備考\n";

        csv += user_head;
        csv += user_line;
        csv += data_head;

        user.date.forEach(record => {
          console.log(record);

          line =
            record["workingdate"] +
            "," +
            record["attendance"] +
            "," +
            record["leaving"] +
            "," +
            record["total_working_times"] +
            "," +
            record["regular_working_times"] +
            "," +
            record["off_hours_working_hours"] +
            "," +
            record["late_night_overtime_hours"] +
            "," +
            record["remark_data"] +
            "\n";
          csv += line;
        });
        user_line =
          '合　　計' +
          "," +
          '' +
          "," +
          '' +
          "," +
          user["total_working_times"] +
          "," +
          user["regular_working_times"] +
          "," +
          user["off_hours_working_hours"] +
          "," +
          user["late_night_overtime_hours"] +
          "\n\n";
        csv += user_line;
      });
      let blob = new Blob([csv], { type: "text/csv" });
      let link = document.createElement("a");
      link.href = window.URL.createObjectURL(blob);
      link.download = moment().format('YYYYMMDDhhmmss') + "_" + this.csvDate + "次集計" + ".csv";
      link.click();
    }
  }
};
</script>
