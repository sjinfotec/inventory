<template>
  <!-- panel body -->
  <div class="panel-body">
    <div class="col-md-6 padding-dis-left padding-dis-right">
      <div class="form-group">
        <label for="shift_start" class>シフト開始</label>
        <input class="form-control" type="time" v-model="start" placeholder="例 13:00">
      </div>
      <div class="form-group">
        <label for="shift_end" class>シフト終了</label>
        <input class="form-control" type="time" v-model="end" placeholder="例 22:00">
        
      </div>
    <select class="form-control" v-model="selected">
      <option v-for="option in shiftTimes" v-bind:value="option.id">
        {{ option.shift_start_time }} ~ {{ option.shift_end_time }}
      </option>
    </select>
    <span>{{selected}}</span>
    </div>
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

export default {
  name: "CreateShiftTime",
  data() {
    return {
      start: "",
      end: "",
      shiftTimes:[],
      selected:""
    };
  },
  // マウント時
  mounted() {
    console.log("create shift time Component mounted.");
    this.getShiftTimes();
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
