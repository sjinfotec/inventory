<template>
  <!-- panel body -->
  <div class="panel-body">
    <div class="col-md-12 padding-dis-left padding-dis-right">
      <div class="form-group col-md-6">
        <p v-if="errors.length">
          <ul class="error-red">
            <li v-for="error in errors">{{ error }}</li>
          </ul>
        </p>
        <label for="shift_start" class>シフトを設定する社員</label>
        <select-user ref="selectuser" v-bind:get-do="getDo" v-on:change-event="userChanges"></select-user>&nbsp;
      </div>
      <div class="form-group col-md-6">
        <label for="shift_end" class>シフト選択</label>
        <select class="form-control" v-model="selectedShift">
        <option v-for="option in shiftTimes" v-bind:value="option">
          {{ option.shift_start_time }} ~ {{ option.shift_end_time }}
        </option>
      </select>
      </div>
    </div>
    <div class="form-group col-md-6">
      <datepicker :language="ja"
                  :value="this.default"
                  :format="DatePickerFormat"
                  v-model="from"></datepicker>
    </div>
    <div class="form-group col-md-6">
      <datepicker :language="ja"
                  :value="this.default"
                  :format="DatePickerFormat"
                  v-model="to"></datepicker>
    </div>
    <div>
      <button class="btn btn-success" @click="StoreShiftTime()">登録</button>
    </div>
    <div>
      <button class="btn btn-danger" @click="rangeDell()">選択した日付のシフトを削除</button>
    </div>
    <!-- /panel body -->
    <div v-if="shiftInfo.length ">
      登録済みシフト
      <table class="table">
        <thead>
          <tr>
            <th>日付</th>
            <th>シフト開始時間</th>
            <th>シフト終了時間</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in shiftInfo">
            <input type="hidden" v-model="item.id"></input>
            <td>{{item.target_date}}</td>
            <td>{{item.shift_start_time}}</td>
            <td>{{item.shift_end_time}}</td>
            <td><button class="btn btn-danger" @click="delShiftTimes(item.id)">削除</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script>
import toasted from "vue-toasted";
import Datepicker from "vuejs-datepicker";
import {ja} from 'vuejs-datepicker/dist/locale'

export default {
  name: "CreateShiftTime",
  data() {
    return {
      ja:ja,
      default: '2019/10/24',
      DatePickerFormat: 'yyyy/MM/dd',
      from: "",
      to: "",
      shiftTimes: [],
      userList: [],
      shiftInfo: [],
      selectedShift: [],
      selectedUser: "",
      getDo: 1,
      errors: [],
      validate: false
    };
  },
  components: {
    Datepicker
  },
  // マウント時
  mounted() {
    console.log("create shift time Component mounted.");
    this.getShiftTimes();
    this.getUserList();
  },
  methods: {
    // バリデーション
    checkForm: function () {
      var flag = false;
      if (this.selectedUser && this.selectedShift.id && this.from && this.to) {
        flag = true;
        return flag;
      }else{
        this.errors = [];

        if (!this.selectedUser) {
          flag = false;
          this.errors.push('ユーザーを選択してください');
        }
        if (!this.selectedShift.id) {
          flag = false;
          this.errors.push('シフト選択をしてください');
        }
        if (!this.from) {
          flag = false;
          this.errors.push('開始日を入力してください');
        }
        if (!this.to) {
          flag = false;
          this.errors.push('終了日を入力してください');
        }
        return flag;
      }
    },
    // 登録ボタン押下
    StoreShiftTime() {
      this.validate = this.checkForm();
      if(this.validate){
        this.$axios
        .post("/setting_shift_time/store", {
          user_code: this.selectedUser,
          shift_start_time: this.selectedShift.shift_start_time,
          shift_end_time: this.selectedShift.shift_end_time,
          from: this.from,
          to: this.to
        })
        .then(response => {
          var res = response.data;
          console.log(res.result);
          if(res.result == 0){
            this.$toasted.show("シフトを登録しました");
            this.getUserShift(this.selectedUser);
          }else{
            var options = {
              position: "bottom-center",
              duration: 2000,
              fullWidth: false,
              type: "error"
            };
            this.$toasted.show("シフトの登録に失敗しました",options);
          }
        })
        .catch(reason => {
          var options = {
              position: "bottom-center",
              duration: 2000,
              fullWidth: false,
              type: "error"
            };
            this.$toasted.show("シフト時間の登録に失敗しました",options);
        });

      }else{

      }
      
    },
    // ユーザーリスト
    getUserList(){
      this.$refs.selectuser.getUserList(this.getDo, "");
    },
    // シフトタイムリスト
    getShiftTimes(){
      this.$axios
        .get("/create_shift_time/get")
        .then(response => {
          this.shiftTimes = response.data;
          console.log("登録済みシフト一覧更新");
        })
        .catch(reason => {
          alert("error");
        });
    },
    // ユーザーリスト変更時
    getUserShift: function (code) {
      console.log(code);
      this.$axios
      .post("/get_user_shift", {
          code: code,
        })
        .then(response => {
          this.shiftInfo = response.data;
          
        })
        .catch(reason => {
        });
    },
    // 削除
    delShiftTimes: function (itemid) {
      console.log(itemid);
      this.$axios
      .post("/setting_shift_time/del", {
          id: itemid,
        })
        .then(response => {
          var res = response.data;
          if(res.result == 0){
            this.$toasted.show("シフトを削除しました");
            this.getUserShift(this.selectedUser);
          }else{
          }
        })
        .catch(reason => {
        });
    },
    // 範囲削除
    rangeDell: function () {
      var confirm = window.confirm("選択した日付のシフトを削除しますか？");
      if(confirm){
        console.log(confirm);
        this.$axios
        .post("/setting_shift_time/range_del", {
            user_code: this.selectedUser,
            from: this.from,
            to: this.to
          })
          .then(response => {
            var res = response.data;
            if(res.result == 0){
              this.$toasted.show("選択した日付のシフトを削除しました");
              this.getUserShift(this.selectedUser);
            }else{
            }
          })
          .catch(reason => {
          });
      }else{
        console.log(confirm);
      }
    },
      // ユーザー選択が変更された場合の処理
    userChanges: function(value){
      this.selectedUser = value;
      this.getUserShift(value);
      console.log("userChanges = " + value);
    }
  
  }
};
</script>
