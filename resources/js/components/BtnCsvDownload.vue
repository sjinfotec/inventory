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
      var data_remark = "";
      var line = "";
      // 1ユーザーごと
      this.csvData.forEach(user => {
        user_line = "";

        user.date.forEach(record => {
          line =
          '\ufeff' + 
            record["user_code"] +
            "," +
            record["workingdate"] +
            "," +
            record["attendance"] +
            "," +
            record["leaving"] +
            "," +
            record["public_going_out_hours"] +
            "," +
            record["missing_middle_hours"] +
            "," +  
            record["remark_holiday_name"] +
            "\n";
          csv += line;
        });
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
