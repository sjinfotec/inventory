<template>
  <div class="col-md-12 pb-2">
    <!-- .row -->
    <div class="row justify-content-between">
      <!-- .col -->
      <div class="col-md-6 pb-2">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text label-width-120" id="basic-addon1">
              年度指定
              <span class="color-red">[必須]</span>
            </span>
          </div>
          <input
            type="number"
            name="fromyear"
            title="指定年"
            max="2050"
            v-bind:min="year"
            step="1"
            class="form-control"
            v-model="valueyear"
            v-on:onblur="fromyearChanges"
          />
        </div>
      </div>
      <!-- /.col -->
      <!-- .col -->
      <div class="col-md-6 pb-2">
        <div class="input-group">
          <div class="input-group-prepend">
            <label class="input-group-text label-width-120" for="target_department">所属部署</label>
          </div>
          <select-departmentlist
            ref="selectdepartmentlist"
            v-bind:blank-data="true"
            v-bind:placeholder-data="'部署を選択してください'"
            v-bind:selected-department="selectedDepartmentValue"
            v-bind:add-new="false"
            v-bind:date-value="''"
            v-bind:kill-value="valueDepartmentkillcheck"
            v-bind:row-index="0"
            v-on:change-event="departmentChanges"
          ></select-departmentlist>
        </div>
        <message-data v-bind:message-datas="messagedatadepartment" v-bind:message-class="'warning'"></message-data>
      </div>
      <!-- /.col -->
      <!-- .col -->
      <div class="col-md-6 pb-2">
        <div class="input-group">
          <div class="input-group-prepend">
            <label class="input-group-text label-width-120" for="target_users">氏 名</label>
          </div>
          <select-userlist
            v-if="showuserlist"
            ref="selectuserlist"
            v-bind:blank-data="true"
            v-bind:placeholder-data="'氏名を選択してください'"
            v-bind:selected-value="selectedUserValue"
            v-bind:add-new="false"
            v-bind:get-do="getDo"
            v-bind:date-value="applytermdate"
            v-bind:kill-value="valueUserkillcheck"
            v-bind:row-index="0"
            v-bind:department-value="selectedDepartmentValue"
            v-bind:employment-value="selectedEmploymentValue"
            v-on:change-event="userChanges"
          ></select-userlist>
        </div>
        <message-data v-bind:message-datas="messagedatauser" v-bind:message-class="'warning'"></message-data>
      </div>
      <!-- /.col -->
    </div>
    <!-- .row -->
    <div class="row justify-content-between mt-3">
      <!-- col -->
      <div class="col-md-12 pb-2">
        <btn-work-time
          v-on:searchclick-event="searchclick"
          v-bind:btn-mode="'search'"
          v-bind:is-push="issearchbutton"
        ></btn-work-time>
        <message-waiting v-bind:is-message-show="messageshowsearch"></message-waiting>
      </div>
      <!-- /.col -->
    </div>

    <div class="row justify-content-between" v-if="details.length">
      <div class="card bg-light col-md-12 mb-3">
        <div class="card-header">
          <span class="align-middle">{{selectedYear}}年度の有給情報(小数点２桁まで)</span>
          <span class="float-right">
            <select class="form-control" @change="allChangeType()" v-model="selectedType">
              <option value="0">単位一括変更の場合は選択してください</option>
              <option value="1">日</option>
              <option value="2">時間</option>
            </select>
          </span>
        </div>
        <div class="card-body">
          <!-- /.row -->
          <table class="table-sm table-hover table-bordered thead-light table-striped">
            <thead>
              <tr>
                <th style="width:10%">No</th>
                <th style="width:15%">氏名</th>
                <th style="width:15%">所属部署</th>
                <th style="width:15%">本年度付与</th>
                <th style="width:15%">昨年度残</th>
                <th style="width:15%">付与(合計)</th>
                <th style="width:15%">単位(日/時)</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(detail,index) in details" v-bind:key="detail.code">
                <td>{{ index + 1}}</td>
                <td>{{ detail.name }}</td>
                <td>{{ detail.department_name }}</td>
                <td>
                  <input
                    type="number"
                    step="0.01"
                    style="width:70%"
                    v-model="detail.paid_this_year"
                    v-bind:class="[detail.error_flag1 ? bgColorRed : '','']"
                    @input="sumPaid(index)"
                  />
                </td>
                <td>
                  <input
                    type="number"
                    step="0.01"
                    style="width:70%"
                    v-model="detail.paid_last_year"
                    v-bind:class="[detail.error_flag2 ? bgColorRed : '','']"
                    @input="sumPaid(index)"
                  />
                </td>
                <td>
                  <input
                    type="number"
                    step="0.01"
                    style="width:70%"
                    class="font-weight-bold"
                    v-bind:class="[detail.error_flag3 ? bgColorRed : '','']"
                    v-model="detail.paid_sum"
                  />
                </td>
                <td>
                  <select
                    class="form-control"
                    v-model="detail.type"
                    v-bind:class="[detail.error_flag4 ? bgColorRed : '','']"
                  >
                    <option value></option>
                    <option
                      v-for="item in paidTypes"
                      :value="item.code"
                      v-bind:key="item.code"
                    >{{ item.code_name }}</option>
                  </select>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="row justify-content-between" v-if="errors.length">
      <!-- col -->
      <div class="col-md-12 pb-2">
        <ul class="error-red color-red">
          <li v-for="(error,index) in errors" v-bind:key="index">{{ error }}</li>
        </ul>
      </div>
      <!-- /.col -->
    </div>
    <!-- .row -->
    <div class="row justify-content-between mt-3" v-if="details.length">
      <!-- col -->
      <div class="col-md-12 pb-2">
        <btn-work-time
          v-on:fixclick-event="fixclick"
          v-bind:btn-mode="'fix'"
          v-bind:is-push="isfixbutton"
        ></btn-work-time>
        <message-waiting v-bind:is-message-show="messageshowsearch"></message-waiting>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
</template>
<script>
import toasted from "vue-toasted";
import moment from "moment";
import { dialogable } from "../mixins/dialogable.js";
import { checkable } from "../mixins/checkable.js";
import { requestable } from "../mixins/requestable.js";

export default {
  name: "SettingPaidHoliday",
  mixins: [dialogable, checkable, requestable],
  data() {
    return {
      defaultDate: new Date(),
      details: [],
      paidTypes: [],
      informations: [],
      messagedatauser: [],
      messagedatadepartment: [],
      errors: [],
      messageshowsearch: false,
      valueUserkillcheck: false,
      valueDepartmentkillcheck: false,
      valueyear: "",
      content: "",
      selectMode: "",
      selectedDepartmentValue: "",
      selectedUserValue: "",
      selectedEmploymentValue: "",
      selectedYear: "",
      selectedType: "0",
      year: "",
      showdepartmentlist: true,
      showuserlist: true,
      applytermdate: "",
      getDo: 1,
      userrole: "",
      isinitbutton: false,
      issearchbutton: false,
      isfixbutton: false,
      bgColorRed: "bg-color-red"
    };
  },
  // マウント時
  mounted() {
    this.year = moment(this.valueym).format("YYYY");
    this.valueyear = this.year;
    this.getPaidList();
  },
  methods: {
    // -------------------- 共通 ----------------------------
    // 表示するボタンがクリックされた場合の処理
    searchclick: function(e) {
      // 処理中メッセージ表示
      this.$swal({
        title: "処　理　中...",
        html: "",
        allowOutsideClick: false, //枠外をクリックしても画面を閉じない
        showConfirmButton: false,
        showCancelButton: true,
        onBeforeOpen: () => {
          this.$swal.showLoading();
          this.selectedYear = this.year;
          this.$axios
            .get("/setting_paid_holiday/get", {
              params: {
                year: this.valueyear,
                department_code: this.selectedDepartmentValue,
                user_code: this.selectedUserValue
              }
            })
            .then(response => {
              this.$swal.close();
              this.details = response.data;
            })
            .catch(reason => {
              this.$swal.close();
            });
        }
      });
    },
    // チェック（小数点桁数チェック）
    // digit は小数点桁数
    // 2の場合は　10 ** 2 = 100 をかけて判定する
    checkDetail(required, maxlength, digit) {
      this.errors = [];
      var check_num = 10 ** digit;
      var check_this_year = [];
      var check_last_year = [];
      var check_sum = [];
      this.details.forEach((element, index) => {
        console.log(element.paid_this_year);
        // エラーフラグ初期化 v-ifで1の場合背景色を赤くする
        element.error_flag1 = 0;
        element.error_flag2 = 0;
        element.error_flag3 = 0;
        element.error_flag4 = 0;
        // 必須チェック
        if (required) {
          if (element.paid_this_year == "" || element.paid_this_year == null) {
            element.error_flag1 = 1;
            this.errors.push(
              "No." +
                index +
                " : " +
                element.name +
                "さんの 本年度付与 が入力されていないか異常値になっています"
            );
          }
          if (element.paid_last_year == "" || element.paid_last_year == null) {
            element.error_flag2 = 1;
            this.errors.push(
              "No." +
                index +
                " : " +
                element.name +
                "さんの 昨年度残 が入力されていないか異常値になっています"
            );
          }
          if (element.paid_sum == "" || element.paid_sum == null) {
            element.error_flag3 = 1;
            this.errors.push(
              "No." +
                index +
                " : " +
                element.name +
                "さんの 付与(合計) が入力されていないか異常値になっています"
            );
          }
          if (element.type == "" || element.type == null) {
            element.error_flag4 = 1;
            this.errors.push(
              "No." +
                index +
                " : " +
                element.name +
                "さんの 単位(日/時) を入力してください"
            );
          }
        }
        // 小数点桁数チェック
        // . でsplitして後ろの文字数で判定する
        if (element.paid_this_year != "" && element.paid_this_year != null) {
          check_this_year = element.paid_this_year.toString().split(".");
          // 小数点桁数が指定の桁より多い場合
          if (check_this_year[1].length > digit) {
            element.error_flag1 = 1;
            this.errors.push(
              "No." +
                index +
                " : " +
                element.name +
                "さんの 本年度付与 の小数点桁数は２桁までです"
            );
          }
        }
        if (element.paid_last_year != "" && element.paid_last_year != null) {
          check_last_year = element.paid_last_year.toString().split(".");
          // 小数点桁数が指定の桁より多い場合
          if (check_last_year[1].length > digit) {
            element.error_flag2 = 1;
            this.errors.push(
              "No." +
                index +
                " : " +
                element.name +
                "さんの 	昨年度残 の小数点桁数は２桁までです"
            );
          }
        }
        if (element.paid_sum != "" && element.paid_sum != null) {
          check_sum = element.paid_sum.toString().split(".");
          // 小数点桁数が指定の桁より多い場合
          if (check_sum[1].length > digit) {
            element.error_flag3 = 1;
            this.errors.push(
              "No." +
                index +
                " : " +
                element.name +
                "さんの 付与(合計) の小数点桁数は２桁までです"
            );
          }
        }
        // 最大数チェック 999999.99まで
        if (element.paid_this_year != "" && element.paid_this_year != null) {
          if (
            element.paid_this_year < 0 ||
            element.paid_this_year > 999999.99
          ) {
            element.error_flag1 = 1;
            this.errors.push(
              "No." +
                index +
                " : " +
                element.name +
                "さんの 本年度付与 は0～999999.99までです。"
            );
          }
        }
        if (element.paid_last_year != "" && element.paid_last_year != null) {
          if (
            element.paid_last_year < 0 ||
            element.paid_last_year > 999999.99
          ) {
            element.error_flag2 = 1;
            this.errors.push(
              "No." +
                index +
                " : " +
                element.name +
                "さんの 昨年度残 は0～999999.99までです。"
            );
          }
        }
        if (element.paid_sum != "" && element.paid_sum != null) {
          if (element.paid_sum < 0 || element.paid_sum > 999999.99) {
            element.error_flag3 = 1;
            this.errors.push(
              "No." +
                index +
                " : " +
                element.name +
                "さんの 付与(合計) は0～999999.99までです。"
            );
          }
        }
      });
    },
    // 更新ボタンクリック処理
    fixclick() {
      this.checkDetail(true, 8, 2);
      if (this.errors.length == 0) {
        var messages = [];
        messages.push("この内容で更新しますか？");
        this.htmlMessageSwal("確認", messages, "info", true, true).then(
          result => {
            if (result) {
              axios
                .post("/update_paid_informations", {
                  details: this.details,
                  year: this.selectedYear
                })
                .then(res => {
                  if (res.data.result) {
                    this.$toasted.show("更新しました。");
                  } else {
                  }
                })
                .catch(err => {
                  //例外処理を行う
                  this.serverCatch("有給設定", "更新");
                });
            }
          }
        );
        // 項目数が多い場合以下コメントアウト
      } else {
        this.countswal("エラー", this.errors, "error", true, false, true).then(
          result => {
            if (result) {
            }
          }
        );
      }
    },
    // 指定年が変更された場合の処理
    fromyearChanges: function(value) {
      this.valueyear = value;
      // パネルに表示
      this.setPanelHeader();
      this.selectMode = "";
      this.isinitbutton = false;
    },
    // 付与＋残
    sumPaid(index) {
      var temp_this_year = parseFloat(this.details[index].paid_this_year);
      var temp_last_year = parseFloat(this.details[index].paid_last_year);

      if (isNaN(temp_this_year)) {
        temp_this_year = 0;
      }
      if (isNaN(temp_last_year)) {
        temp_last_year = 0;
      }
      this.details[index].paid_sum = (temp_this_year + temp_last_year).toFixed(
        2
      );
    },
    // 単位一括変更
    allChangeType() {
      if (this.selectedType != "0") {
        this.details.forEach(element => {
          element.type = this.selectedType;
        });
      } else {
        this.details.forEach(element => {
          element.type = "";
        });
      }
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    // 部署選択が変更された場合の処理
    departmentChanges: function(value, arrayitem) {
      this.selectedDepartmentValue = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getUserSelected();
    },
    // ユーザー選択が変更された場合の処理
    userChanges: function(value, arrayitem) {
      this.selectedUserValue = value;
    },
    // ユーザー選択コンポーネント取得メソッド
    getUserSelected: function() {
      this.applytermdate = "";
      if (this.valuedate) {
        this.applytermdate = moment(this.valuedate).format("YYYYMMDD");
      }
      this.$refs.selectuserlist.getList(
        this.applytermdate,
        this.valueUserkillcheck,
        this.getDo,
        this.selectedDepartmentValue,
        this.selectedEmploymentValue
      );
    },
    // 有給単位リスト取得
    getPaidList() {
      this.$axios
        .get("/get_paid_list", {})
        .then(response => {
          this.paidTypes = response.data.details;
        })
        .catch(reason => {
          alert("error");
        });
    },
    // 指定年月が変更された場合の処理
    fromdateChanges: function(value) {
      this.valueymd = value;
      this.year = moment(this.valueymd).format("YYYY");
      this.getItem();
    },
    // 指定日付がクリアされた場合の処理
    fromdateCleared: function() {
      this.valueymd = "";
      this.year = "";
      this.defaultDate = "";
      this.inputClear();
      this.messageClear();
    }
  }
};
</script>
<style scoped>
.font-color-black {
  color: black;
}
.margin-top-regular {
  margin-top: 30px;
}
.margin-left-small {
  margin-left: 15px;
}
.my-red {
  color: red;
}
.my-skyblue {
  color: skyblue;
}
.my-orange {
  color: #fecb81;
}
.bg-color {
  background-color: floralwhite;
}
.bg-color-red {
  background-color: orangered;
}
.padding-dis {
  padding: 0.75rem 0rem !important;
}
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
