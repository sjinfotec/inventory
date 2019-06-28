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
    </div>
    <!-- /panel body -->
    <div v-if="shiftTimes.length ">
      登録済みシフト
      <table class="table">
        <thead>
          <tr>
            <th>シフト開始時間</th>
            <th>シフト終了時間</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in shiftTimes">
            <td>{{item.shift_start_time}}</td>
            <td>{{item.shift_end_time}}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div>
      <button class="btn btn-default" @click="createShiftBtn()">作成</button>
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
      shiftTimes:[]
    };
  },
  mounted() {
    console.log("create shift time Component mounted.");
    this.$axios
      .get("/create_shift_time/get")
      .then(response => {
        this.shiftTimes = response.data;
        console.log(this.shiftTimes);
      })
      .catch(reason => {
        alert("error");
      });
  },
  methods: {
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
  }
};
</script>
