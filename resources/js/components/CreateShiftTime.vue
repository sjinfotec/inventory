<template>
  <!-- panel body -->
  <div class="panel-body">
    <div class="col-md-6 padding-dis-left padding-dis-right">
      <div class="form-group">
        <label for="shift_start" class>シフト開始</label>
        <input class="form-control" v-model="start" placeholder="例 13:00">
      </div>
      <div class="form-group">
        <label for="shift_end" class>シフト終了</label>
        <input class="form-control" v-model="end" placeholder="例 22:00">
      </div>
    </div>
    <div>
      <button class="btn btn-default" @click="createShiftBtn()">作成</button>
    </div>
    <button class="btn btn-default" @click="doClick()">Show!</button>
    <!-- /panel body -->
  </div>
</template>

<script>
import toasted from "vue-toasted";

export default {
  name: "CreateShiftTime",
  data() {
    return {
      start: "",
      end: ""
    };
  },
  mounted() {
    console.log("create shift time Component mounted.");
    // this.$axios
    //   .get("/daily/show")
    //   .then(response => {
    //     this.dailies = response.data;
    //     console.log(this.dailies);
    //     // alert(this.contents.work_times[0].user_code);
    //   })
    //   .catch(reason => {
    //     alert("error");
    //   });
  },
  methods: {
    createShiftBtn() {
      this.$axios
        .post("/create_shift_time/store", {
          start: this.start,
          end: this.end,
          toastCount: 0
        })
        .then(response => {
          console.log(response.data);
          // alert(this.contents.work_times[0].user_code);
        })
        .catch(reason => {});
    },
    doClick() {
      var options = {
        position: "top-center",
        duration: 1000,
        fullWidth: true,
        type: "error"
      };
      this.$toasted.show("hello billo", options);
    }
  }
};
</script>
