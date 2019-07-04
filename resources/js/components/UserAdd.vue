<template>
  <!-- panel body -->
  <div class="panel-body">
    <form @submit.prevent="onsubmit">
    <div class="form-group">
      <label>社員名</label>
      <input
        class="form-control"
        v-model="form.name"
        type="text"
        required
        placeholder="三条　太郎">
    </div>
    <div class="form-group">
      <label>ふりがな</label>
      <input
        class="form-control"
        v-model="form.kana"
        type="text"
        maxlength="30"
        required
        placeholder="sanjyo taro">
    </div>
    <div class="form-group">
      <label>メールアドレス</label>
      <input
        class="form-control"
        v-model="form.email"
        type="email"
        required
        placeholder="test@example.com">
    </div>
    <div class="form-group">
      <label>ログインID</label>
      <input
        class="form-control"
        v-model="form.id"
        type="text"
        maxlength="10"
        required
        title="半角英数字4-10文字"
        pattern="^[a-zA-Z0-9]{4,10}$"
        placeholder=""
        @blur="test()"
        >
    </div>
    <div class="form-group">
      <label>パスワード</label>
      <input
        class="form-control"
        v-model="form.password"
        type="text"
        maxlength="10"
        required
        title="半角英数字4-10文字"
        pattern="^[a-zA-Z0-9]{4,10}$"
        placeholder="">
    </div>
    <div class="form-group">
      <label>部署</label>
      <!-- <select-department v-bind:selectdepartment="valuedepartment" v-on:change-event="departmentChanges"></select-department>&nbsp; -->
    </div>
    <div class="form-group">
      <button
        class="btn btn-primary"
        type="submit">ユーザー追加</button>
    </div>
  </form>
  </div>
</template>
<script>
import toasted from "vue-toasted";
export default {
  name: "CreateShiftTime",
  data() {
    return {
      form: {},
      valuedepartment: '',
    };
  },
  // マウント時
  mounted() {
    console.log("UserAdd Component mounted.");
  },
  methods: {
    onsubmit () {
      this.result = Object.assign({}, this.form);
      console.log(this.form);
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
    },
    // 部署選択が変更された場合の処理
    departmentChanges: function(value){
      console.log("departmentChanges = " + value);
    },
    // フォーカスアウト
    test: function(){
      alert("フォーカスアウト");
      
    }
  }
};
</script>
