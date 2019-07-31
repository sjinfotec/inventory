<template>
<div>
  <!-- main contentns row -->
  <div class="row justify-content-between">
    <!-- .panel -->
    <div class="col-md pt-3">
      <div class="card shadow-pl">
        <!-- panel header -->
        <div class="card-header bg-transparent pb-0 border-0">
          <h1 class="float-sm-left font-size-rg">ユーザー登録および編集</h1>
          <span class="float-sm-right font-size-sm">ユーザーを選択すると登録済みのユーザー情報を編集できます</span>
        </div>
        <!-- /.panel header -->
        <div class="card-body pt-2">
          <!-- panel contents -->
          <fvl-form
            method="post"
            :data="form"
            url="/user_add/store"
            @success="addSuccess()"
            @error="error()"
          >
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- .col -->
            <div class="col-12 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-120" id="basic-addon1">ユーザー</span>
                </div>
                <fvl-search-select
                  :selected.sync="userCode"
                  class="p-0"
                  name="userCode"
                  :options="userList"
                  placeholder="ユーザーを選択すると編集モードになります"
                  :allowEmpty="true"
                  :search-keys="['code']"
                  option-key="code"
                  option-value="name"
                />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-120" id="basic-addon1">社員名</span>
                </div>
                <input type="text" class="form-control" :value.sync="form.name" label="社員名" name="name">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-120" id="basic-addon1">ふりがな</span>
                </div>
                <input type="text" class="form-control" :value.sync="form.kana" label="ふりがな" name="kana">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-120" id="basic-addon1">ログインID</span>
                </div>
                <input type="text" class="form-control" :value.sync="form.code" label="ログインID" name="code" title="半角英数字4-10文字" pattern="^[a-zA-Z0-9]{4,10}$">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2" v-if="userCode=='' || userCode == null ">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-120" id="basic-addon1">パスワード</span>
                </div>
                <input type="text" class="form-control" :value.sync="form.password" label="パスワード" name="password" title="半角英数字6-12文字" pattern="^[a-zA-Z0-9]{6,12}$">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-120" id="basic-addon1">メールアドレス</span>
                </div>
                <input type="text" class="form-control" :value.sync="form.email" label="メールアドレス" name="email">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-120" id="basic-addon1">所属部署</span>
                </div>
                <fvl-search-select
                  :selected.sync="form.departmentCode"
                  class="p-0"
                  name="departmentCode"
                  :options="departmentList"
                  :search-keys="['name']"
                  option-key="id"
                  option-value="name"
                />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-120" id="basic-addon1">雇用形態</span>
                </div>
                <fvl-search-select
                  :selected.sync="form.status"
                  class="p-0"
                  name="status"
                  :options="employStatusList"
                  :search-keys="['code']"
                  option-key="code"
                  option-value="code_name"
                />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-120" id="basic-addon1">通常勤務時間</span>
                </div>
                <fvl-search-select
                  :selected.sync="form.table_no"
                  class="p-0"
                  name="timetable_no"
                  :options="timeTableList"
                  :search-keys="['name']"
                  option-key="no"
                  option-value="name"
                />
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <div class="btn-group d-flex">
                <button type="submit" class="btn btn-success" v-if="userCode=='' || userCode==null ">追加</button>
                <button type="submit" class="btn btn-success" id="edit" v-if="userCode != ''">編集</button>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          </fvl-form>
          <!-- .row -->
          <div class="row justify-content-between" v-if="userCode != ''">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <div class="btn-group d-flex">
                <button class="btn btn-danger" @click="alertDelConf('warning')">削除</button>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between" v-if="userCode != ''">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <div class="btn-group d-flex">
                <button class="btn btn-success" v-on:click="show">パスワード変更</button>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- modal -->
          <modal name="password-change" :width="800" :height="600" v-model="userCode">
            <div class="card">
              <div class="card-header">パスワード変更</div>
              <div class="card-body">
                <div v-if="errors.length">
                  <ul class="error-red color-red">
                    <li v-for="(error,index) in errors" v-bind:key="index">{{ error }}</li>
                  </ul>
                </div>
                <div class="form-group col-md-6">
                  <label for="shift_end" class>新しいパスワード</label>
                  <input
                    class="form-control"
                    v-model="enterPass"
                    maxlength="12"
                    type="password"
                    title="半角英数字12文字以内"
                    pattern="^[a-zA-Z0-9]{6,12}$"
                  />
                </div>
                <div class="form-group col-md-6">
                  <label for="shift_end" class>新しいパスワード（再入力）</label>
                  <input
                    class="form-control"
                    v-model="reEnterPass"
                    maxlength="12"
                    type="password"
                    title="半角英数字12文字以内"
                    pattern="^[a-zA-Z0-9]{6,12}$"
                  />
                </div>
                <button class="btn btn-success" v-on:click="alertPassConf('warning')">確定</button>
                <button class="btn btn-warning" v-on:click="hide">キャンセル</button>
              </div>
            </div>
          </modal>
          <!-- /modal -->
        </div>
      </div>
    </div>
    <!-- /.panel -->
  </div>
  <!-- main contentns row -->
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
      enterPass: "",
      reEnterPass: "",
      validate: false,
      errors: [],
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
    alert: function(state, message, title) {
      this.$swal(title, message, state);
    },
    show: function() {
      this.$modal.show("password-change");
    },
    hide: function() {
      this.$modal.hide("password-change");
      this.inputPassClear();
    },
    alertPassConf: function(state) {
      this.$swal({
        title: "確認",
        text: "パスワードを変更しますか？",
        icon: state,
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.passChange();
        } else {
        }
      });
    },
    // バリデーション
    checkForm: function() {
      var flag = false;
      this.errors = [];

      if (this.reEnterPass && this.enterPass) {
        if (this.reEnterPass != this.enterPass) {
          flag = false;
          this.errors.push("入力したパスワードが一致していません");
        } else {
          flag = true;
        }
        return flag;
      } else {
        if (!this.enterPass) {
          flag = false;
          this.errors.push("新しいパスワードを入力してください");
        }
        if (!this.reEnterPass) {
          flag = false;
          this.errors.push("新しいパスワード（再入力）を入力してください");
        }
        return flag;
      }
    },
    passChange: function() {
      this.validate = this.checkForm();
      if (this.validate) {
        this.$axios
          .post("/user_add/passchange", {
            user_code: this.userCode,
            password: this.enterPass
          })
          .then(response => {
            var res = response.data;
            if (res.result == 0) {
              this.alert("success", "パスワードを変更しました", "変更完了");
              this.hide();
            } else {
            }
          })
          .catch(reason => {
            this.alert("error", "パスワード変更に失敗しました", "エラー");
          });
      } else {
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
      this.alert("error", "登録に失敗しました", "エラー");
    },
    alertDelConf: function(state) {
      this.$swal({
        title: "確認",
        text: "削除してもよろしいですか？",
        icon: state,
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.del();
        } else {
        }
      });
    },
    // 削除
    del: function() {
      this.$axios
        .post("/user_add/del", {
          user_code: this.userCode
        })
        .then(response => {
          var res = response.data;
          if (res.result == 0) {
            this.alert("success", "ユーザーを削除しました", "削除成功");
            this.inputClear();
            this.getUserList(1, null);
          } else {
            this.alert("error", "削除に失敗しました", "エラー");
          }
        })
        .catch(reason => {
          this.alert("error", "削除に失敗しました", "エラー");
        });
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
    inputPassClear() {
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

