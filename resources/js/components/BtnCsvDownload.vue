<template>
  <div>
    <btn-work-time
      v-on:csv-event="csvmain"
      v-bind:btn-mode="generalDescription"
      v-bind:is-push="isCsvbutton">
    </btn-work-time>
  </div>
</template>
<script>
import moment from "moment";
import encoding from 'encoding-japanese';
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "btnCsvDownload",
  mixins: [ dialogable, checkable, requestable ],
  props: {
    btnMode: {
      type: String,
      default: ""
    },
    csvData: {
      type: Array,
      required: true
    },
    generalData: {
      type: Array,
      default: []
    },
    generalDescription: {
      type: String,
      default: ""
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
      details: [],
      titles:[]
    };
  },
  methods: {
    // -------------------- イベント処理 ----------------------------
    csvmain() {
      this.getCsvItem();
    },
    // -------------------- サーバー処理 ----------------------------
    // CSV対象項目取得処理
    getCsvItem() {
      this.postRequest("get_csv_item", {
        selection_code: this.btnMode
      })
        .then(response => {
          this.getThenCsvItem(response);
        })
        .catch(reason => {
          this.serverCatch("CSV対象項目", "取得");
        });
    },
    // 氏名選択リスト取得処理
    getUserListCsv() {
      this.postRequest("/get_user_list/csv", {
        targetdate: null,
        departmentcode: null,
        employmentcode: null,
        usercode: null,
        killvalue: null,
      })
        .then(response => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("氏名", "取得");
        });
    },
    // -------------------- 共通 ----------------------------
    // 取得正常処理（ユーザー）
    getThenCsvItem(response) {
      this.details = [];
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        if (this.btnMode == this.generalData[0]['code']) {
          this.downloadCSVCalc();
        } else if (this.btnMode == this.generalData[1]['code']) {
          this.downloadCSVSalary();
        } else if (this.btnMode == this.generalData[2]['code']) {
          this.downloadCSVLog();
        } else if (this.btnMode == this.generalData[3]['code']) {
          this.getUserListCsv();
        }
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("氏名", "取得");
        }
      }
    },
    // 取得正常処理（ユーザー）
    getThen(response) {
      this.details = [];
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        this.edtCSVUsers();
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("氏名", "取得");
        }
      }
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + "情報" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    },

    // -------------------- CSV編集部 ----------------------------
    // 勤怠集計
    downloadCSVCalc() {
      var csv = "";
      var line = "";
      // タイトル
      var out_f = this.isTitle();
      if (out_f) {
        line =
          this.csvDate + "分" + "\r\n";
        csv += line;
      }
      // 項目名
      line = this.makeTitleLine();
      csv += line;
      // データ
      let $this = this;
      this.csvData.forEach(user => {
        line = $this.makeItemLineCsvcalc(user)
        csv += line;
      });
      // 文字コード変換
      this.convTosjis(csv, moment().format('YYYYMMDDhhmmss') + "_" + this.csvDate + "次集計" + ".csv");
    },

    // 給与計算
    downloadCSVSalary() {
      var csv = "";
      var line = "";
      let $this = this;
      // データ
      this.csvData.forEach(user => {
        let $this1 = $this;
        user.date.forEach(record => {
          line = $this1.makeItemLineCsvsalary(record, $this1)
          if (line != null) {
            csv += line;
          }
        });
      });
      // 文字コード変換
      this.convTosjis(csv, moment().format('YYYYMMDDhhmmss') + "_" + this.csvDate + "次給与連携" + ".csv");
    },

    // 勤怠ログCSV編集
    downloadCSVLog() {
      var csv = "";
      var line = "";
      // タイトル
      var out_f = this.isTitle();
      if (out_f) {
        line =
          this.csvDate + "\r\n";
        csv += line;
      }
      // 項目名
      line = this.makeTitleLine();
      csv += line;
      // データ
      let $this = this;
      this.csvData.forEach(user => {
        line = $this.makeItemLineCsvlog(user)
        csv += line;
      });
      // 文字コード変換
      this.convTosjis(csv, moment().format('YYYYMMDDhhmmss') + "_" + this.csvDate + "次勤怠ログ" + ".csv");
    },

    // ユーザー情報CSV編集
    edtCSVUsers() {
      var csv = "";
      var line = "";
      var user_code= "";
      var department_name= "";
      var employment_name= "";
      var user_name= "";
      var user_kana = "";
      var official_position= "";
      var apply_term_from= "";
      var kill_from_date= "";
      var working_timetable_name= "";
      var email= "";
      var mobile_email= "";
      var management= "";
      var role= "";
      if (this.details.length > 0) {
        // 項目名
        line =
          "社員コード（半角英数字10桁）" +
          "," +
          "部署名" +
          "," +
          "雇用形態名" +
          "," +
          "社員名（全角50文字以内）" +
          "," +
          "社員カナ名（半角30文字以内、全角15文字以内）" +
          "," +
          "役職（全角50文字以内）" +
          "," +
          "適用開始日" +
          "," +
          "退職日" +
          "," +
          "タイムテーブル名" +
          "," +
          "メールアドレス" +
          "," +
          "モバイルメールアドレス" +
          "," +
          "勤怠管理（半角数字）" +
          "," +
          "権限（半角数字）" +
          "\r\n";
        csv += line;
        this.details.forEach(record => {
          user_code = record["user_code"];
          department_name = record["department_name"];
          employment_name = record["employment_name"];
          user_name = record["user_name"];
          user_kana = record["user_kana"];
          official_position = record["official_position"];
          apply_term_from = record["apply_term_from"];
          kill_from_date = record["kill_from_date"];
          working_timetable_name = record["working_timetable_name"];
          email = record["email"];
          mobile_email = record["mobile_email"];
          management = record["management"];
          role = record["role"];
          line =
            user_code +
            "," +
            department_name +
            "," +
            employment_name +
            "," +
            user_name +
            "," +
            user_kana +
            "," +
            official_position +
            "," +  
            apply_term_from +
            "," +  
            kill_from_date +
            "," +  
            working_timetable_name +
            "," +  
            email +
            "," +  
            mobile_email +
            "," +  
            management +
            "," +  
            role +
            "\r\n";
          csv += line;
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
        link.download = moment().format('YYYYMMDDhhmmss') + "_ユーザー情報" + ".csv";
        link.click();
      }
    },

    // CSVタイトル作成処理
    makeTitleLine() {
      var linetext = "";
      var cnt = 0;
      this.details.forEach(item => {
        if (item['is_select'] == 1) {
          if (item['item_code'] != 99) {
            if (cnt > 0) {
              linetext += ",";
            }
            linetext += item['item_out_name'];
            cnt++;
          }
        }
      });
      linetext += "\r\n";
      return linetext;
    },

    // CSV項目作成処理（勤怠）
    makeItemLineCsvcalc(user) {
      var linetext = "";
      var cnt = 0;
      var item_name = "";
      this.details.forEach(item => {
        if (item['is_select'] == 1) {
          if (item['item_code'] != 99) {
            if (cnt > 0) {
              linetext += ",";
            }
            item_name = item['item_name'];
            linetext += user[item_name];
            cnt++;
          }
        }
      });
      linetext += "\r\n";
      return linetext;
    },

    // CSV項目作成処理（給与）
    makeItemLineCsvsalary(record, this1) {
      var attendance = "";
      var leaving = "";
      var remark_holiday_name = "";
      var workingdate = "";
      var item_data = "";
      var item_name = "";
      var linetext = "";
      var cnt = 0;
      this1.details.forEach(item => {
        if (item['is_select'] == 1) {
          if (item['item_code'] != 99) {
            if (cnt > 0) {
              linetext += ",";
            }
            item_name = item['item_name'];
            item_data = "";
            if (item_name == "attendance") {
              if (record[item_name] == null) {
                attendance = "";
              } else {
                attendance = record[item_name];
              }
              item_data = attendance;
            } else if (item_name == "leaving") {
              if (record[item_name] == null) {
                leaving = "";
              } else {
                leaving = record[item_name];
              }
              item_data = leaving;
            } else if (item_name == "remark_holiday_name") {
              remark_holiday_name = record[item_name]
              item_data = remark_holiday_name;
            } else if (item_name == "workingdate") {
              item_data =
                record[item_name].substr(0,4) + "/" + record[item_name].substr(4,2) + "/" +  record[item_name].substr(6,2);
            } else {
              item_data = record[item_name];
            }
            linetext += item_data;
            cnt++;
          }
        }
      });
      if ((attendance != "") || (leaving != "") || (remark_holiday_name != "" && remark_holiday_name != null)) {
        linetext += "\r\n";
      } else {
        linetext = null;
      }
      return linetext;
    },

    // CSV項目作成処理（勤怠ログ）
    makeItemLineCsvlog(user) {
      var linetext = "";
      var cnt = 0;
      var item_name = "";
      var item_data = "";
      this.details.forEach(item => {
        if (item['is_select'] == 1) {
          if (item['item_code'] != 99) {
            if (cnt > 0) {
              linetext += ",";
            }
            item_name = item['item_name'];
            item_data = "";
            if (user[item_name] != "" && user[item_name] != null) {
              item_data = user[item_name];
            }
            linetext += item_data;
            cnt++;
          }
        }
      });
      linetext += "\r\n";
      return linetext;
    },

    // CSV項目作成処理
    convTosjis(csv, filename) {
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
      link.download = filename;
      link.click();
    },
    // タイトルセット有無
    isTitle() {
      var out_f = false;
      this.details.forEach(item => {
        if (item['item_code'] == 99) {
          if (item['is_select'] == 1) {
            out_f = true;
          }
        }
      });

      return out_f;
    }
  }
};
</script>
