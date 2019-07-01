<template>
  <!-- panel body -->
  <div class="panel-body">
    <div class="col-md-12 padding-dis-left padding-dis-right">
      <div class="form-group col-md-6">
        <label for="shift_start" class>シフトを設定する社員</label>
        <select class="form-control" v-model="selectedUser">
        <option v-for="user in userList" v-bind:value="user.code">
          {{ user.name }}
        </option>
      </select>
      </div>
      <div class="form-group col-md-6">
        <label for="shift_end" class>シフト選択</label>
        <select class="form-control" v-model="selectedTime">
        <option v-for="option in shiftTimes" v-bind:value="option.id">
          {{ option.shift_start_time }} ~ {{ option.shift_end_time }}
        </option>
      </select>
      </div>
    </div>
    <datepicker></datepicker>
    <!-- /panel body -->
    <div v-if="shiftTimes.length ">
      登録済みシフト
      <table class="table">
        <thead>
          <tr>
            <th>シフト開始時間</th>
            <th>シフト終了時間</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in shiftTimes">
            <input type="hidden" v-model="item.id"></input>
            <td>{{item.shift_start_time}}</td>
            <td>{{item.shift_end_time}}</td>
            <td><button class="btn btn-danger" @click="delShiftTimes(item.id)">削除</button></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div>
      <button class="btn btn-success" @click="createShiftBtn()">作成</button>
    </div>
  </div>
</template>
<script>
import toasted from "vue-toasted";
import Datepicker from "vuejs-datepicker";

export default {
  name: "CreateShiftTime",
  data() {
    return {
      start: "",
      end: "",
      shiftTimes:[],
      userList:[],
      selectedTime:"",
      selectedUser:""
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
    // シフト作成ボタン押下
    createShiftBtn() {
      this.$axios
        .post("/create_shift_time/store", {
          start: this.start,
          end: this.end,
        })
        .then(response => {
          var res = response.data;
          console.log(res.result);
          if(res.result == 0){
            this.$toasted.show("シフト時間を登録しました");
            this.getShiftTimes();
          }else{
            var options = {
              position: "bottom-center",
              duration: 2000,
              fullWidth: false,
              type: "error"
            };
            this.$toasted.show("シフト時間の登録に失敗しました",options);
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
    },
    getUserList(){
      this.$axios
        .get("/get_user_list")
        .then(response => {
          this.userList = response.data;
          console.log("ユーザーリスト取得");
        })
        .catch(reason => {
          alert("error");
        });
    },
    // 登録済みシフト再描画
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
    // 削除
    delShiftTimes: function (itemid) {
      console.log(itemid);
      this.$axios
      .post("/create_shift_time/del", {
          id: itemid,
        })
        .then(response => {
          var res = response.data;
          if(res.result == 0){
            this.$toasted.show("シフト時間を削除しました");
            this.getShiftTimes();
          }else{
          }
        })
        .catch(reason => {
        });
    }
  }
};
</script>
