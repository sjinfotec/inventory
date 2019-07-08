<template>
  <!-- panel body -->
  <div class="panel-body">
    <!-- form wrapper -->
    <fvl-form method="post" :data="form" url="/user_add/store" @success="addSuccess()" @error="error()">
      <!-- Text input component -->
      <fvl-input :value.sync="form.name" label="社員名" name="name" />
      <fvl-input :value.sync="form.kana" label="ふりがな" name="kana" />
      <fvl-input :value.sync="form.loginid" label="ログインID" name="loginid" title="半角英数字4-10文字" pattern="^[a-zA-Z0-9]{4,10}$"/>
      <fvl-input :value.sync="form.password" label="パスワード" name="password" title="半角英数字4-10文字" pattern="^[a-zA-Z0-9]{4,10}$"/>
      <fvl-input :value.sync="form.email" label="メールアドレス" name="email"/>
      <!-- Textarea component -->
     <fvl-search-select :selected.sync="form.departmentCode" label="部署" name="departmentCode"
      :options="departmentList"
      :search-keys="['code']"
      option-key="code"
      option-value="name"/>
  
     <fvl-search-select :selected.sync="form.status" label="雇用形態" name="status"
      :options="employStatusList"
      :search-keys="['code']"
      option-key="code"
      option-value="code_name"/>
      <!-- Submit button -->
      <fvl-submit>追加</fvl-submit>
    </fvl-form>
  </div>
</template>
<script>
import toasted from "vue-toasted";
import { FvlForm, FvlInput, FvlSelect, FvlSearchSelect, FvlSubmit } from 'formvuelar'

export default {
  name: "CreateShiftTime",
  components: {
        FvlForm,
        FvlInput,
        FvlSelect,
        FvlSearchSelect,
        FvlSubmit,
    },
  data() {
    return {
      form: {},
      valuedepartment: '',
      departmentList:[],
      employStatusList:[]
    };
  },
  // マウント時
  mounted() {
    console.log("UserAdd Component mounted.");
    this.getDepartmentList();
    this.getEmploymentStatusList();
  },
  methods: {
    getDepartmentList(){
      this.$axios
        .get("/get_departments_list")
        .then(response => {
          this.departmentList = response.data;
          console.log("部署リスト取得");
        })
        .catch(reason => {
          alert("error");
        });
    },
    getEmploymentStatusList(){
      this.$axios
        .get("/get_employment_status_list")
        .then(response => {
          this.employStatusList = response.data;
          console.log("雇用形態リスト取得");
        })
        .catch(reason => {
          alert("error");
        });
    },
    addSuccess(){
      this.$toasted.show("ユーザーを追加しました");
    },
    error(){
      var options = {
          position: "bottom-center",
          duration: 2000,
          fullWidth: false,
          type: "error"
        };
      this.$toasted.show("ユーザー追加に失敗しました",options);
    }
  }
};
</script>
