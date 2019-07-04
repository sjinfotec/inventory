<template>
  <!-- panel body -->
  <div class="panel-body">
    <!-- form wrapper -->
<fvl-form method="post" :data="form" url="/user_add/store" @success="addSuccess()">
  <!-- Text input component -->
  <fvl-input :value.sync="form.name" label="社員名" name="name" />
  <fvl-input :value.sync="form.kana" label="ふりがな" name="kana" />
  <fvl-input :value.sync="form.loginid" label="ログインID" name="loginid" title="半角英数字4-10文字" pattern="^[a-zA-Z0-9]{4,10}$"/>
  <fvl-input :value.sync="form.password" label="パスワード" name="password" title="半角英数字4-10文字" pattern="^[a-zA-Z0-9]{4,10}$"/>
  <!-- Textarea component -->
  <!-- <fvl-textarea :value.sync="form.bio" label="Bio" name="bio" /> -->
  <!-- Submit button -->
   <div class="form-group">
      <label>部署</label>
      <select-department v-bind:selectdepartment="valuedepartment" v-on:change-event="departmentChanges"></select-department>&nbsp;
    </div>
  <fvl-submit>追加</fvl-submit>
</fvl-form>
  </div>
</template>
<script>
import toasted from "vue-toasted";
import { FvlForm, FvlInput, FvlSubmit } from 'formvuelar'

export default {
  name: "CreateShiftTime",
  components: {
        FvlForm,
        FvlInput,
        FvlSubmit,
    },
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
    // 部署選択が変更された場合の処理
    departmentChanges: function(value){
      this.form.departmentCode = value;
    },
    addSuccess(){
      this.$toasted.show("ユーザーを追加しました");
    }
  }
};
</script>
