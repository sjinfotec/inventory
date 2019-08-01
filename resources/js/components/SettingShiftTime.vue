<template>
<div>
  <!-- main contentns row -->
  <div class="row justify-content-between">
    <!-- .panel -->
    <div class="col-md pt-3">
      <div class="card shadow-pl">
        <!-- panel header -->
        <div class="card-header bg-transparent pb-0 border-0">
          <h1 class="float-sm-left font-size-rg">シフトを割り当てを編集する</h1>
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
                  <span class="input-group-text font-size-sm line-height-xs label-width-120" id="basic-addon1" for="shift_start">社員名</span>
                </div>
                <select-user ref="selectuser" class="p-0" v-bind:get-do="getDo" v-on:change-event="userChanges"></select-user>
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-120" id="basic-addon1" for="shift_start">シフト選択</span>
                </div>
                <select class="form-control" v-model="no">
                  <option v-for="option in timeTableList" v-bind:value="option.no">{{ option.name }}</option>
                </select>
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-120" id="basic-addon1" for="shift_end">開始日付</span>
                </div>
                <datepicker :language="ja" :value="this.default" :format="DatePickerFormat" v-model="from"></datepicker>
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-120" id="basic-addon1" for="shift_end">終了日付</span>
                </div>
                <datepicker :language="ja" :value="this.default" :format="DatePickerFormat" v-model="to"></datepicker>
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
                <button type="button" class="btn btn-success btn-lg font-size-rg w-100" @click="StoreShiftTime()">この条件で登録する</button>
              </div>
            </div>
            <div class="col-md-12 pb-2">
              <div class="btn-group d-flex">
                <button type="button" class="btn btn-danger btn-lg font-size-rg w-100" @click="alertRangeDelConf('info')">指定した期間を削除する</button>
              </div>
            </div>
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
                      <td class="text-center align-middle">操作</td>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in shiftInfo" v-bind:key="item.id">
                      <input type="hidden" v-model="item.id" />
                      <td class="text-center align-middle">{{item.target_date}}</td>
                      <td class="text-center align-middle">{{item.name}}</td>
                      <td class="text-center align-middle">
                        <div class="btn-group d-flex">
                          <button class="font-size-sm btn btn-danger btn-sm w-100" @click="alertDelConf('info',item.id)">削除</button>
                        </div>
                      </td>
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
import Datepicker from "vuejs-datepicker";
import { ja } from "vuejs-datepicker/dist/locale";

export default {
  name: "SettingShiftTime",
  data() {
    return {
      ja: ja,
      default: "2019/10/24",
      DatePickerFormat: "yyyy/MM/dd",
      from: "",
      to: "",
      shiftTimes: [],
      userList: [],
      shiftInfo: [],
      timeTableList: [],
      csvData: [{}],
      selectedUser: "",
      no: "",
      errors: [],
      getDo: 1,
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
      var flag = false;
      if (this.selectedUser && this.no && this.from && this.to) {
        flag = true;
        return flag;
      } else {
        this.errors = [];

        if (!this.selectedUser) {
          flag = false;
          this.errors.push("ユーザーを選択してください");
        }
        if (!this.no) {
          flag = false;
          this.errors.push("タイムテーブル選択をしてください");
        }
        if (!this.from) {
          flag = false;
          this.errors.push("開始日を入力してください");
        }
        if (!this.to) {
          flag = false;
          this.errors.push("終了日を入力してください");
        }
        return flag;
      }
    },
    alert: function(state, message, title) {
      this.$swal(title, message, state);
    },
    alertRangeDelConf: function(state) {
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
    },
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
        this.$axios
          .post("/setting_shift_time/store", {
            user_code: this.selectedUser,
            time_table_no: this.no,
            from: this.from,
            to: this.to
          })
          .then(response => {
            var res = response.data;
            console.log(res.result);
            if (res.result == 0) {
              this.$toasted.show("シフトを登録しました");
              this.getUserShift(this.selectedUser);
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
    getTimeTableList() {
      this.$axios
        .get("/get_time_table_list")
        .then(response => {
          this.timeTableList = response.data;
          console.log("タイムテーブルリスト取得");
        })
        .catch(reason => {
          alert("error");
        });
    },
    // ユーザーリスト変更時
    getUserShift: function(code) {
      console.log(code);
      this.$axios
        .post("/get_user_shift", {
          code: code
        })
        .then(response => {
          this.shiftInfo = response.data;
        })
        .catch(reason => {});
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
            this.getUserShift(this.selectedUser);
          } else {
          }
        })
        .catch(reason => {});
    },
    // 範囲削除
    rangeDell: function() {
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
            this.getUserShift(this.selectedUser);
          } else {
            this.alert("error", "削除に失敗しました", "エラー");
          }
        })
        .catch(reason => {});
    },
    // ユーザー選択が変更された場合の処理
    userChanges: function(value) {
      this.selectedUser = value;
      this.getUserShift(value);
      console.log("userChanges = " + value);
    }
  }
};
</script>
