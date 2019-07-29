<template>
  <!-- panel body -->
  <div class="panel-body">
    <!-- form wrapper -->
    <fvl-form
      method="post"
      :data="form"
      url="/user_add/store"
      @success="addSuccess()"
      @error="error()"
    >
      <fvl-search-select
        :selected.sync="userCode"
        label="ユーザー"
        name="userCode"
        :options="userList"
        placeholder="ユーザーを選択すると編集モードになります"
        :allowEmpty="true"
        :search-keys="['code']"
        option-key="code"
        option-value="name"
      />
      <!-- Text input component -->
      <fvl-input :value.sync="form.name" label="社員名" name="name" />
      <fvl-input :value.sync="form.kana" label="ふりがな" name="kana" />
      <fvl-input
        :value.sync="form.code"
        label="ログインID"
        name="code"
        title="半角英数字4-10文字"
        pattern="^[a-zA-Z0-9]{4,10}$"
      />
      <span v-if="userCode=='' || userCode == null ">
        <fvl-input
          :value.sync="form.password"
          label="パスワード"
          name="password"
          title="半角英数字6-12文字"
          pattern="^[a-zA-Z0-9]{6,12}$"
        />
      </span>
      <fvl-input :value.sync="form.email" label="メールアドレス" name="email" />
      <!-- Textarea component -->
      <fvl-search-select
        :selected.sync="form.departmentCode"
        label="部署"
        name="departmentCode"
        :options="departmentList"
        :search-keys="['name']"
        option-key="id"
        option-value="name"
      />

      <fvl-search-select
        :selected.sync="form.status"
        label="雇用形態"
        name="status"
        :options="employStatusList"
        :search-keys="['code']"
        option-key="code"
        option-value="code_name"
      />

      <fvl-search-select
        :selected.sync="form.table_no"
        label="タイムテーブル"
        name="timetable_no"
        :options="timeTableList"
        :search-keys="['name']"
        option-key="no"
        option-value="name"
      />
      <!-- Submit button -->
      <fvl-submit v-if="userCode=='' || userCode==null ">追加</fvl-submit>
      <fvl-submit id="edit" v-if="userCode != ''">編集</fvl-submit>
    </fvl-form>
    <span class="padding-set-small margin-set-top-regular" v-if="userCode != ''">
      <button class="btn btn-danger" @click="del">削除</button>
    </span>
    <span class="padding-set-small margin-set-top-regular" v-if="userCode != ''">
      <button class="btn btn-info" v-on:click="show">パスワード変更</button>
    </span>
    <modal name="password-change" v-model="userCode">
      
      <div class="card">
          <div class="card-header">パスワード変更</div>

          <div class="card-body">
            <div class="form-group col-md-6">
              <label for="shift_end" class>新しいパスワード</label>
              <input class="form-control" v-model="enterPass" maxlength='12' type="password" title="半角英数字12文字以内" pattern="^[a-zA-Z0-9]{6,12}$"></input>
            </div>
            <div class="form-group col-md-6">
              <label for="shift_end" class>新しいパスワード（再入力）</label>
              <input class="form-control"  v-model="reEnterPass"  maxlength='12' type="password" title="半角英数字12文字以内" pattern="^[a-zA-Z0-9]{6,12}$"></input>
            </div>
            <button class="btn btn-success" v-on:click="passChange">確定</button>
            <button class="btn btn-warning" v-on:click="hide">キャンセル</button>
          </div>
      </div>
    </modal>
  </div>
</template>
<script>
import toasted from "vue-toasted";
import {
  FvlForm,
  FvlInput,
  FvlSelect,
  FvlSearchSelect,
  FvlSubmit
} from "formvuelar";

export default {
  name: "UserAdd",
  components: {
    FvlForm,
    FvlInput,
    FvlSelect,
    FvlSearchSelect,
    FvlSubmit,
    FvlSelect,
    getDo: 1
  },
  data() {
    return {
      form: {
        id: "",
        name: "",
        kana: "",
        email: "",
        code: "",
        password: "",
        status: "",
        table_no: "",
        departmentCode: ""
      },
      valuedepartment: "",
      departmentList: [],
      employStatusList: [],
      timeTableList: [],
      userList: [],
      userDetails: [],
      userCode: "",
      enterPass:"",
      reEnterPass:"",
      oldCode: ""
    };
  },
  // マウント時
  mounted() {
    console.log("UserAdd Component mounted.");
    this.getDepartmentList();
    this.getEmploymentStatusList();
    this.getTimeTableList();
    this.getUserList(1, null);
  },
  watch: {
    userCode: function(val, oldVal) {
      console.log(this.userCode);
      console.log(val + " " + oldVal);
      if (this.userCode != "") {
        this.$axios
          .get("/user_add/get", {
            params: {
              code: this.userCode
            }
          })
          .then(response => {
            this.userDetails = response.data;
            this.form.id = this.userDetails[0].id;
            this.form.name = this.userDetails[0].name;
            this.form.kana = this.userDetails[0].kana;
            this.form.code = this.userDetails[0].code;
            this.form.password = this.userDetails[0].password;
            this.form.email = this.userDetails[0].email;
            this.form.departmentCode = this.userDetails[0].department_id;
            this.form.status = "" + this.userDetails[0].employment_status + "";
            this.form.table_no =
              "" + this.userDetails[0].working_timetable_no + "";
            // hidden
            this.oldCode = this.userDetails[0].code;
            console.log("ユーザー詳細情報取得");
          })
          .catch(reason => {
            alert("error");
          });
      } else {
        this.inputClear();
      }
    }
  },
  methods: {
    show: function() {
      this.$modal.show("password-change");
    },
    hide: function() {
      this.$modal.hide("password-change");
      this.inputPassClear();
    },
    passChange: function(){
      if(this.enterPass == this.reEnterPass){
        // パスワード変更
        var confirm = window.confirm("パスワードを変更しますか？");
        if (confirm) {
          this.$axios
            .post("/user_add/passchange", {
              user_code: this.userCode,
              password: this.enterPass
            })
            .then(response => {
              var res = response.data;
              if (res.result == 0) {
                this.$toasted.show("パスワードを変更しました");
                this.hide();
              } else {
              }
            })
            .catch(reason => {});
        } else {
        }
      }else{
        // 不一致
        alert("入力したパスワードが不一致です");
      }
    },
    getDepartmentList() {
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
    getEmploymentStatusList() {
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
    addSuccess() {
      this.getUserList(1, null);
      this.$toasted.show("登録しました");
    },
    getUserList(getdovalue, value) {
      console.log("getdovalue = " + getdovalue);
      this.$axios
        .get("/get_user_list", {
          params: {
            getdo: getdovalue,
            code: value
          }
        })
        .then(response => {
          this.userList = response.data;
          this.object = { code: "", name: "新規登録" };
          this.userList.unshift(this.object);
          console.log("ユーザーリスト取得");
        })
        .catch(reason => {
          alert("error");
        });
    },
    error() {
      var options = {
        position: "bottom-center",
        duration: 2000,
        fullWidth: false,
        type: "error"
      };
      this.$toasted.show("ユーザー追加に失敗しました", options);
    },
    // 削除
    del: function() {
      var confirm = window.confirm("選択したユーザーを削除しますか？");
      if (confirm) {
        this.$axios
          .post("/user_add/del", {
            user_code: this.userCode
          })
          .then(response => {
            var res = response.data;
            if (res.result == 0) {
              this.$toasted.show("選択したユーザーを削除しました");
              this.inputClear();
              this.getUserList(1, null);
            } else {
            }
          })
          .catch(reason => {});
      } else {
      }
    },
    inputClear() {
      this.form.id = "";
      this.form.name = "";
      this.form.kana = "";
      this.form.code = "";
      this.form.password = "";
      this.form.email = "";
      this.form.departmentCode = "";
      this.form.status = "";
      this.form.table_no = "";
    },
    inputPassClear(){
      this.enterPass = "";
      this.reEnterPass = "";
    }
  }
};
</script>
<style scoped>
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: table;
  transition: opacity 0.3s ease;
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}

.modal-container {
  width: 300px;
  margin: 0px auto;
  padding: 20px 30px;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
  transition: all 0.3s ease;
  font-family: Helvetica, Arial, sans-serif;
}

.modal-header h3 {
  margin-top: 0;
  color: #42b983;
}

.modal-body {
  margin: 20px 0;
}

.modal-default-button {
  float: right;
}

/*
  * The following styles are auto-applied to elements with
  * transition="modal" when their visibility is toggled
  * by Vue.js.
  *
  * You can easily play with the modal transition by editing
  * these styles.
  */

.modal-enter {
  opacity: 0;
}

.modal-leave-active {
  opacity: 0;
}

.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}
</style>

