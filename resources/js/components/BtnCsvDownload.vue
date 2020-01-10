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
          if (record["attendance"] != "" && record["attendance"] != null) {
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
    }
  }
};
</script>
