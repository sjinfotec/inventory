<template>
  <!-- panel body -->
  <div class="panel-body">
    <div class="col-md-12 padding-dis-left padding-dis-right">
      <div class="form-group col-md-6">
        <div v-if="errors.length">
          <ul class="error-red">
            <li v-for="(error,index) in errors" v-bind:key="index">{{ error }}</li>
          </ul>
        </div>
        <label for="shift_start" class>シフトを設定する社員</label>
        <select-user ref="selectuser" v-bind:get-do="getDo" v-on:change-event="userChanges"></select-user>&nbsp;
      </div>
      <div class="form-group col-md-6">
        <label for="shift_end" class>タイムテーブル選択</label>
        <select class="form-control" v-model="no">
          <option v-for="option in timeTableList" v-bind:value="option.no">{{ option.name }}</option>
        </select>
      </div>
    </div>
    <div class="form-group col-md-6">
      <datepicker :language="ja" :value="this.default" :format="DatePickerFormat" v-model="from"></datepicker>
    </div>
    <div class="form-group col-md-6">
      <datepicker :language="ja" :value="this.default" :format="DatePickerFormat" v-model="to"></datepicker>
    </div>
    <!-- <btn-csv-download :csvData="shiftInfo" v-model="shiftInfo"></btn-csv-download> -->
    <div>
      <button class="btn btn-success" @click="StoreShiftTime()">登録</button>
    </div>
    <div>
      <button class="btn btn-danger" @click="alertRangeDelConf('info')">選択した日付のシフトを削除</button>
    </div>
    <!-- /panel body -->
    <div v-if="shiftInfo.length ">
      登録済みシフト
      <table class="table">
        <thead>
          <tr>
            <th>日付</th>
            <th>タイムテーブル</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in shiftInfo" v-bind:key="item.id">
            <input type="hidden" v-model="item.id" />
            <td>{{item.target_date}}</td>
            <td>{{item.name}}</td>
            <td>
              <button class="btn btn-danger" @click="alertDelConf('info',item.id)">削除</button>
            </td>
          </tr>
        </tbody>
      </table>
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
