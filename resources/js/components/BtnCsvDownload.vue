<template>
  <div>
    <btn-work-time v-if="btnMode === 'csvcalc'"
      v-on:csvcalc-event="downloadCSVCalc"
      v-bind:btn-mode="btnMode"
      v-bind:is-push="isCsvbutton">
    </btn-work-time>
    <btn-work-time v-if="btnMode === 'csvsalary'"
      v-on:csvsalary-event="downloadCSVSalary"
      v-bind:btn-mode="btnMode"
      v-bind:is-push="isCsvbutton">
    </btn-work-time>
    <btn-work-time v-if="btnMode === 'csvlog'"
      v-on:csvlog-event="downloadCSVLog"
      v-bind:btn-mode="btnMode"
      v-bind:is-push="isCsvbutton">
    </btn-work-time>
  </div>
</template>
<script>
import moment from "moment";
import encoding from 'encoding-japanese';

export default {
  name: "btnCsvDownload",
  props: {
    btnMode: {
      type: String,
      required: ""
    },
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
    downloadCSVCalc() {
      var csv = "";
      var line = "";
      // タイトル
      line =
        this.csvDate + "分"
        "\r\n";
      csv += line;
      // 項目名
      line =
        "社員コード" +
        "," +
        "氏名" +
        "," +
        "出勤日数" +
        "," +
        "欠勤日数" +
        "," +
        "有給休暇日数" +
        "," +
        "特別休暇日数" +
        "," +
        "不就労時間" +
        "," +
        "普通残業時間" +
        "," +  
        "深夜残業時間" +
        "," +  
        "夜勤手当" +
        "," +  
        "法定休日手当" +
        "," +  
        "所定休日勤手当" +
        "\r\n";
      csv += line;
      /*this.csvData.forEach(user => {

        //  '\ufeff' + 
        user.date.forEach(record => {
          line =
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
            "\r\n";
          csv += line;
        });
      }); */
      // csvを文字コードの数値の配列に変換
      const unicodeList = [];
      for (let i = 0; i < csv.length; i += 1) {
        unicodeList.push(csv.charCodeAt(i));
      }
      // 変換処理の実施
      const shiftJisCodeList = encoding.convert(unicodeList, 'sjis', 'unicode');
      const uInt8List = new Uint8Array(shiftJisCodeList);
      let blob = new Blob([uInt8List], { type: "text/csv" });
      let link = document.createElement("a");
      link.href = window.URL.createObjectURL(blob);
      link.download = moment().format('YYYYMMDDhhmmss') + "_" + this.csvDate + "次集計" + ".csv";
      link.click();
    },
    downloadCSVSalary() {
      var csv = "";
      var line = "";
      var workingdate = "";
      // 1ユーザーごと
      this.csvData.forEach(user => {
        //  '\ufeff' + 
        user.date.forEach(record => {
          if ((record["attendance"] != "" && record["attendance"] != null) || (record["remark_holiday_name"] != "" && record["remark_holiday_name"] != null)) {
            workingdate = record["workingdate"].substr(0,4) + "/" + record["workingdate"].substr(4,2) + "/" +  record["workingdate"].substr(6,2);
            line =
              record["user_code"] +
              "," +
              workingdate +
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
              "\r\n";
            csv += line;
          }
        });
      });
      // csvを文字コードの数値の配列に変換
      const unicodeList = [];
      for (let i = 0; i < csv.length; i += 1) {
        unicodeList.push(csv.charCodeAt(i));
      }
      // 変換処理の実施
      const shiftJisCodeList = encoding.convert(unicodeList, 'sjis', 'unicode');
      const uInt8List = new Uint8Array(shiftJisCodeList);
      let blob = new Blob([uInt8List], { type: "text/csv" });
      let link = document.createElement("a");
      link.href = window.URL.createObjectURL(blob);
      link.download = moment().format('YYYYMMDDhhmmss') + "_" + this.csvDate + "次給与連携" + ".csv";
      link.click();
    },
    downloadCSVLog() {
      var csv = "";
      var line = "";
      var workingdate = "";
      var attendance_time = "";
      var leaving_time = "";
      var pcstart_time = "";
      var pcend_time = "";
      var difference_reason = "";
      // 1ユーザーごと
      this.csvData.forEach(user => {
        //  '\ufeff' + 
        // タイトル
        line =
          this.csvDate +
          "\r\n";
        csv += line;
        // 項目名
        line =
          "日付" +
          "," +
          "出勤時刻" +
          "," +
          "退勤時刻" +
          "," +
          "PC起動時刻" +
          "," +
          "PC終了時刻" +
          "," +
          "差異の理由" +
          "\r\n";
        csv += line;
        user.date.forEach(record => {
          workingdate = "";
          attendance_time = "";
          leaving_time = "";
          pcstart_time = "";
          pcend_time = "";
          difference_reason = "";
          if (record["working_date_name"] != "" && record["working_date_name"] != null) {
            workingdate = record["working_date_name"];
          }
          if (record["attendance_time"] != "" && record["attendance_time"] != null) {
            attendance_time = record["attendance_time"];
          }
          if (record["leaving_time"] != "" && record["leaving_time"] != null) {
            leaving_time = record["leaving_time"];
          }
          if (record["pcstart_time"] != "" && record["pcstart_time"] != null) {
            pcstart_time = record["pcstart_time"];
          }
          if (record["pcend_time"] != "" && record["pcend_time"] != null) {
            pcend_time = record["pcend_time"];
          }
          if (record["difference_reason"] != "" && record["difference_reason"] != null) {
            difference_reason = record["difference_reason"];
          }
          line =
            workingdate +
            "," +
            attendance_time +
            "," +
            leaving_time +
            "," +
            pcstart_time +
            "," +
            pcend_time +
            "," +  
            difference_reason +
            "\r\n";
          csv += line;
        });
      });
      // csvを文字コードの数値の配列に変換
      const unicodeList = [];
      for (let i = 0; i < csv.length; i += 1) {
        unicodeList.push(csv.charCodeAt(i));
      }
      // 変換処理の実施
      const shiftJisCodeList = encoding.convert(unicodeList, 'sjis', 'unicode');
      const uInt8List = new Uint8Array(shiftJisCodeList);
      let blob = new Blob([uInt8List], { type: "text/csv" });
      let link = document.createElement("a");
      link.href = window.URL.createObjectURL(blob);
      link.download = moment().format('YYYYMMDDhhmmss') + "_" + this.csvDate + "次勤怠ログ" + ".csv";
      link.click();
    }
  }
};
</script>
