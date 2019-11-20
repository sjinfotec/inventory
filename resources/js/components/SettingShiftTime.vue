<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <div class="card-header bg-transparent pb-0 border-0">
            <h1 class="float-sm-left font-size-rg">シフトの割り当てを編集する</h1>
            <span class="float-sm-right font-size-sm">勤務時間設定で登録したタイムテーブルを割り当てることができます</span>
          </div>
          <!-- /.panel header -->
          <div class="card-body pt-2" v-if="errors.length">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-12 pb-2">
                <ul class="error-red">
                  <li v-for="(error,index) in errors" v-bind:key="index">{{ error }}</li>
                </ul>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputGroupSelect01"
                    >所属部署<span class="color-red">[*]</span></label>
                  </div>
                  <select-department
                    ref="selectdepartment"
                    v-bind:blank-data="true"
                    v-on:change-event="departmentChanges"
                  ></select-department>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      id="basic-addon1"
                      for="shift_start"
                    >社員名<span class="color-red">[*]</span></span>
                  </div>
                  <select-user
                    ref="selectuser"
                    v-bind:blank-data="true"
                    v-bind:get-Do="getDo"
                    v-on:change-event="userChanges"
                  ></select-user>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      id="basic-addon1"
                      for="shift_end"
                    >開始日付<span class="color-red">[*]</span></span>
                  </div>
                  <datepicker
                    :language="ja"
                    :value="this.default"
                    :format="DatePickerFormat"
                    v-model="from"
                  ></datepicker>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      id="basic-addon1"
                      for="shift_end"
                    >終了日付<span class="color-red">[*]</span></span>
                  </div>
                  <datepicker
                    :language="ja"
                    :value="this.default"
                    :format="DatePickerFormat"
                    v-model="to"
                  ></datepicker>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      id="basic-addon1"
                      for="shift_start"
                    >シフト選択</span>
                  </div>
                  <select class="form-control" v-model="timeTable">
                    <option></option>
                    <option
                      v-for="option in timeTableList"
                      v-bind:value="{no: option.no, name: option.name,apply_term_from:option.apply_term_from}"
                      v-bind:key="option.no"
                    >{{ option.name }}</option>
                  </select>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <div class="btn-group d-flex">
                  <button
                    type="button"
                    class="btn btn-success btn-lg font-size-rg w-100"
                    @click="StoreShiftTime()"
                  >この条件で登録する</button>
                </div>
              </div>
              <div class="col-md-12 pb-2">
                <div class="btn-group d-flex">
                  <button
                    type="button"
                    class="btn btn-primary btn-lg font-size-rg w-100"
                    @click="getUserShift()"
                  >指定した期間のシフトを表示する</button>
                </div>
              </div>
              <!-- <div class="col-md-12 pb-2">
                <div class="btn-group d-flex">
                  <button
                    type="button"
                    class="btn btn-danger btn-lg font-size-rg w-100"
                    @click="alertRangeDelConf('info')"
                  >指定した期間を削除する</button>
                </div>
              </div> -->
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.panel contents -->
          </div>
        </div>
      </div>
      <!-- /.panel -->
    </div>
    <!-- /main contentns row -->
    <!-- main contentns row -->
    <div class="row justify-content-between" v-if="shiftInfo.length ">
      <!-- .panel -->
      <div class="col-md pt-3 align-self-stretch">
        <div class="card shadow-pl">
          <!-- panel header -->
          <div class="card-header bg-transparent pt-3 border-0">
            <h1 class="float-sm-left font-size-rg">登録済みシフト</h1>
            <span class="float-sm-right font-size-sm">シフト割り当てされたタイムテーブルの一覧です</span>
          </div>
          <!-- /.panel header -->
          <!-- panel body -->
          <div class="card-body mb-3 p-0 border-top">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table table-striped border-bottom font-size-sm text-nowrap">
                    <thead>
                      <tr>
                        <td class="text-center align-middle">日付</td>
                        <td class="text-center align-middle">タイムテーブル</td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="item in shiftInfo" v-bind:key="item.id">
                        <input type="hidden" v-model="item.id" />
                        <td class="text-center align-middle">{{item.date_name}}</td>
                        <td class="text-center align-middle">{{item.working_timetable_name}}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <!-- /.row -->
            <!-- /panel contents -->
          </div>
          <!-- /panel body -->
        </div>
        <!-- .panel -->
      </div>
      <!-- /main contentns row -->
    </div>
  </div>
</template>
<script>
import toasted from "vue-toasted";
import moment from "moment";
import Datepicker from "vuejs-datepicker";
import { ja } from "vuejs-datepicker/dist/locale";

export default {
  name: "SettingShiftTime",
  data() {
    return {
      ja: ja,
      default: new Date(),
      DatePickerFormat: "yyyy年MM月dd日",
      from: "",
      to: "",
      shiftTimes: [],
      userList: [],
      shiftInfo: [],
      timeTableList: [],
      csvData: [{}],
      selectedUser: "",
      timeTable: [{}],
      errors: [],
      valuedepartment: "",
      valueemploymentstatus: "",
      getDo: 1,
      valueuser: "",
      validate: false
    };
  },
  components: {
    Datepicker
  },
  // マウント時
  mounted() {
    console.log("create shift time Component mounted.");
    this.getTimeTableList();
    this.getUserList();
  },
  methods: {
    // バリデーション
    checkForm: function() {
      var flag = true;
      this.errors = [];

      if (!this.valuedepartment) {
        flag = false;
        this.errors.push("部署を選択してください");
      }
      if (!this.selectedUser) {
        flag = false;
        this.errors.push("社員を選択してください");
      }
      if (!this.timeTable.no) {
        flag = false;
        this.errors.push("シフトを選択をしてください");
      }
      if (!this.from) {
        flag = false;
        this.errors.push("開始日を入力してください");
      }
      if (!this.to) {
        flag = false;
        this.errors.push("終了日を入力してください");
      }
      if (flag) {
        if (this.from > this.to) {
          flag = false;
          this.errors.push("開始日＞終了日となっています");
        }
      }
      return flag;
    },
    // 検索・削除のバリデーション
    checkFormSearch: function() {
      var flag = true;
      this.errors = [];

      if (!this.valuedepartment) {
        flag = false;
        this.errors.push("部署を選択してください");
      }
      if (!this.selectedUser) {
        flag = false;
        this.errors.push("社員を選択してください");
      }
      if (!this.from) {
        flag = false;
        this.errors.push("開始日付を入力してください");
      }
      if (!this.to) {
        flag = false;
        this.errors.push("終了日付を入力してください");
      }
      if (flag) {
        if (this.from > this.to) {
          flag = false;
          this.errors.push("開始日＞終了日となっています");
        }
      }
      return flag;
    },
    alert: function(state, message, title) {
      this.$swal(title, message, state);
    },
    /*alertRangeDelConf: function(state) {
      this.validate = this.checkFormSearch();
      if (this.validate) {
        this.$swal({
          title: "確認",
          text: "選択した日付範囲のシフトを削除しますか？",
          icon: state,
          buttons: true,
          dangerMode: true
        }).then(willDelete => {
          if (willDelete) {
            this.rangeDell();
          } else {
          }
        });
      } else {
      }
    }, */
    alertDelConf: function(state, id) {
      this.$swal({
        title: "確認",
        text: "シフトを削除しますか？",
        icon: state,
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.delShiftTimes(id);
        } else {
        }
      });
    },
    // 登録ボタン押下
    StoreShiftTime() {
      this.validate = this.checkForm();
      if (this.validate) {
        this.fromdate = ""
        if (this.from) {
          this.fromdate = moment(this.from).format("YYYYMMDD");
        }
        this.todate = ""
        if (this.to) {
          this.todate = moment(this.to).format("YYYYMMDD");
        }
        this.$axios
          .post("/setting_shift_time/store", {
            user_code: this.selectedUser,
            department_code: this.valuedepartment,
            time_table_no: this.timeTable.no,
            apply_term_from: this.timeTable.apply_term_from,
            from: this.fromdate,
            to: this.todate
          })
          .then(response => {
            var res = response.data;
            console.log(res.result);
            if (res.result == 0) {
              this.$toasted.show("シフトを登録しました");
              this.errors = [];
              // this.getUserShift(this.selectedUser);
            } else {
              this.alert("error", "シフトの登録に失敗しました", "エラー");
            }
          })
          .catch(reason => {
            this.alert("error", "シフト登録に失敗しました", "エラー");
          });
      } else {
      }
    },
    // ユーザーリスト
    getUserList() {
      this.$refs.selectuser.getUserList(this.getDo, "");
    },
    // タイムテーブルリスト
    getTimeTableList() {
      this.$axios
        .get("/get_time_table_list")
        .then(response => {
          this.timeTableList = response.data;
        })
        .catch(reason => {
          alert("error");
        });
    },
    // ユーザーリスト変更時
    getUserShift: function() {
      this.validate = this.checkFormSearch();
      if (this.validate) {
        this.fromdate = ""
        if (this.from) {
          this.fromdate = moment(this.from).format("YYYYMMDD");
        }
        this.todate = ""
        if (this.to) {
          this.todate = moment(this.to).format("YYYYMMDD");
        }
        this.$axios
          .post("/get_user_shift", {
            code: this.selectedUser,
            from: this.fromdate,
            to: this.todate,
            no: this.timeTable.no
          })
          .then(response => {
            this.shiftInfo = response.data;
            console.log("this.shiftInfo length = " + this.shiftInfo.length);
            this.errors = [];
          })
          .catch(reason => {});
      } else {
      }
    },
    // 部署選択が変更された場合の処理
    departmentChanges: function(value) {
      this.valuedepartment = value;
      this.valueuser = "";
      this.getDo = 1;
      this.fromdate = ""
      if (this.from) {
        this.fromdate = moment(this.from).format("YYYYMMDD");
      }
      if (this.valueemploymentstatus == "") {
        if (this.valuedepartment == "") {
          this.$refs.selectuser.getUserList(this.getDo, this.valueuser, this.fromdate);
        } else {
          this.$refs.selectuser.getUserListByDepartment(
            this.getDo,
            this.valuedepartment,
            this.valueuser,
            this.fromdate
          );
        }
      } else {
        if (this.valuedepartment == "") {
          this.$refs.selectuser.getUserListByEmployment(
            this.getDo,
            this.valueemploymentstatus,
            this.valueuser,
            this.fromdate
          );
        } else {
          this.$refs.selectuser.getUserListByDepartmentEmployment(
            this.getDo,
            this.valuedepartment,
            this.valueemploymentstatus,
            this.valueuser,
            this.fromdate
          );
        }
      }
    },
    // ユーザー選択が変更された場合の処理
    userChanges: function(value) {
      this.valueuser = value;
    },
    // 削除
    delShiftTimes: function(itemid) {
      console.log(itemid);
      this.$axios
        .post("/setting_shift_time/del", {
          id: itemid
        })
        .then(response => {
          var res = response.data;
          if (res.result == 0) {
            this.$toasted.show("シフトを削除しました");
            this.getUserShift();
            this.errors = [];
          } else {
          }
        })
        .catch(reason => {});
    },
    // 範囲削除
    /*rangeDell: function() {
      this.$axios
        .post("/setting_shift_time/range_del", {
          user_code: this.selectedUser,
          from: this.from,
          to: this.to
        })
        .then(response => {
          var res = response.data;
          if (res.result == 0) {
            this.alert(
              "success",
              "選択した日付のシフトを削除しました",
              "削除成功"
            );
            this.getUserShift();
            this.errors = [];
          } else {
            this.alert("error", "削除に失敗しました", "エラー");
          }
        })
        .catch(reason => {});
    }, */
    // ユーザー選択が変更された場合の処理
    userChanges: function(value) {
      this.selectedUser = value;
      // this.getUserShift(value);
      console.log("userChanges = " + value);
    }
  }
};
</script>
