<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <div
            class="card-header bg-transparent pb-0 border-0"
          >
            <h1 class="float-sm-left font-size-rg">各種申請の承認者ルート設定</h1>
            <span class="float-sm-right font-size-sm">承認者と最終承認者を登録します。</span>
          </div>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- main contentns row -->
            <div class="row justify-content-between">
              <!-- .panel -->
              <div class="col-md pt-3 align-self-stretch">
                <div class="card shadow-pl">
                  <!-- panel header -->
                  <div class="card-header bg-transparent pt-3 border-0">
                    <h1 class="float-sm-left font-size-rg">
                      <span>
                        <button class="btn btn-success btn-lg font-size-rg" @click="append">+</button>
                      </span>
                      承認者情報
                    </h1>
                    <span class="float-sm-right font-size-sm">登録済みの承認者情報を編集できます</span>
                  </div>
                  <!-- /.panel header -->
                  <!-- panel body -->
                  <!-- .row -->
                  <div class="row justify-content-between" v-if="errors.length">
                    <!-- col -->
                    <div class="col-md-12 pb-2">
                      <ul class="error-red color-red">
                        <li v-for="(error,index) in errors" v-bind:key="index">{{ error }}</li>
                      </ul>
                    </div>
                    <!-- /.col -->
                  </div>
                  <!-- /.row -->
                  <div class="card-body mb-3 p-0 border-top">
                    <!-- panel contents -->
                    <!-- .row -->
                    <div class="row">
                      <div class="col-12">
                        <div class="table-responsive">
                          <table class="table table-striped border-bottom font-size-sm text-nowrap">
                            <thead>
                              <tr>
                                <td class="text-center align-middle w-35 mw-rem-10">部署</td>
                                <td class="text-center align-middle w-35 mw-rem-10">社員名<span class="color-red">[*]</span></td>
                                <td class="text-center align-middle w-35">順番<span class="color-red">[*]</span></td>
                                <td class="text-center align-middle w-35 mw-rem-10"
                                  data-toggle="tooltip"
                                  data-placement="top"
                                  v-bind:title="'正の承認者と代理承認者となる副の承認者の区分を選択します。'"
                                >正副区分<span class="color-red">[*]</span></td>
                                <td class="text-center align-middle w-35 mw-rem-10">操作</td>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(item,index) in userDetails" v-bind:key="item.id">
                                <td class="text-center align-middle">
                                  <div class="input-group">
                                    <select
                                      class="custom-select"
                                      v-model="userDetails[index].department_code"
                                    >
                                      <option value></option>
                                      <option
                                        v-for="dlist in departmentList"
                                        :value="dlist.code"
                                        v-bind:key="dlist.code"
                                      >{{ dlist.name }}</option>
                                    </select>
                                  </div>
                                </td>
                                <td class="text-center align-middle">
                                  <div class="input-group">
                                    <select
                                      class="custom-select"
                                      v-model="userDetails[index].user_code"
                                    >
                                      <option value></option>
                                      <option
                                        v-for="dlist in departmentList"
                                        :value="dlist.code"
                                        v-bind:key="dlist.code"
                                      >{{ dlist.name }}</option>
                                    </select>
                                  </div>
                                </td>
                                <td class="text-center align-middle">
                                  <div class="input-group">
                                    <input
                                      type="text"
                                      maxlength="30"
                                      class="form-control"
                                      v-model="userDetails[index].kana"
                                    />
                                  </div>
                                </td>
                                <td class="text-center align-middle">
                                  <div class="input-group">
                                    <select
                                      class="custom-select"
                                      v-model="userDetails[index].management"
                                    >
                                      <option value></option>
                                      <option
                                        v-for="mlist in generalList_m"
                                        :value="mlist.code"
                                        v-bind:key="mlist.code"
                                      >{{ mlist.code_name }}</option>
                                    </select>
                                  </div>
                                </td>
                                <td class="text-center align-middle">
                                  <div class="btn-group">
                                    <button
                                      type="button"
                                      class="btn btn-danger btn-lg font-size-rg"
                                      @click="alertDelConf('info',item.id,index)"
                                    >削除</button>
                                  </div>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                    <!-- /.row -->
                    <!-- /.panel contents -->
                  </div>
                  <!-- /panel body -->
                </div>
              </div>
              <!-- col -->
              <div class="col-md-12 pb-2" v-if="cardId">
                <div class="btn-group d-flex">
                  <button
                    class="btn btn-warning"
                    @click="ReleaseCardInfo('warning')"
                    v-if="userCode != ''"
                  >ICカード情報を削除する</button>
                </div>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-md-12 pb-2">
                <div class="btn-group d-flex">
                  <button class="btn btn-success" @click="FixUser()" v-if="userCode != ''">修正する</button>
                </div>
              </div>
              <!-- /.col -->
            </div>
          </div>
          <!-- /.row -->
        </div>
      </div>
    </div>
    <!-- /.panel -->
  </div>
  <!-- main contentns row -->
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
        departmentCode: "",
        management: "",
        role: ""
      },
      valuedepartment: "",
      departmentList: [],
      employStatusList: [],
      timeTableList: [],
      userList: [],
      userDetails: [],
      generalList_m: [],
      generalList_r: [],
      userCode: "",
      departmentCode: "",
      validate: false,
      errors: [],
      oldCode: "",
      cardId: "",
      oldPass: ""
    };
  },
  // マウント時
  mounted() {
    this.userDetails = [];
    console.log("UserAdd Component mounted.");
    this.getDepartmentList();
    this.getEmploymentStatusList();
    this.getTimeTableList();
    this.getUserList(1, null);
    this.getGeneralList("C017");
    this.getGeneralList("C025");
  },
  watch: {
    userCode: function(val, oldVal) {
      if (this.userCode != "") {
        this.$axios
          .get("/user_add/get", {
            params: {
              code: this.userCode
            }
          })
          .then(response => {
            var res = response.data;
            console.log("res.result" + res.result);
            if (res.result == 0) {
              this.userDetails = res.details;
              if (this.userDetails.length > 0) {
                this.cardId = this.userDetails[0].card_idm;
              }
            } else {
              this.alert("error", "ユーザー情報取得に失敗しました", "エラー");
            }
            console.log("ユーザー詳細情報取得");
          })
          .catch(reason => {
            alert("ユーザー詳細情報取得エラー");
          });
      } else {
        this.inputClear();
      }
    },
    departmentCode: function(val, oldVal) {
      if (this.departmentCode != "") {
        this.getUserList(1, this.departmentCode);
      } else {
        this.userCode = "";
        this.getUserList(1, null);
      }
      console.log("ユーザー再取得");
    }
  },
  methods: {
    append: function() {
      this.userDetails.push({
        id: "",
        apply_term_from: "",
        code: this.userCode,
        department_code: "",
        email: "",
        kana: "",
        working_timetable_no: "",
        employment_status: "",
        name: "",
        password: "",
        management: "",
        role: ""
      });
    },
    alert: function(state, message, title) {
      this.$swal(title, message, state);
    },
    alertDelConf: function(state, id, index) {
      if (id >= 0) {
        this.$swal({
          title: "確認",
          text: "削除しますか？",
          icon: state,
          buttons: true,
          dangerMode: true
        }).then(willDelete => {
          if (willDelete) {
            this.del(id, index);
          } else {
          }
        });
      } else {
        this.userDetails.splice(index, 1);
      }
    },
    FixUser() {
      this.validate = this.checkForm();
      if (this.validate) {
        this.$axios
          .post("/user_add/fix", {
            details: this.userDetails,
            pass: this.oldPass
          })
          .then(response => {
            var res = response.data;
            this.alert("success", "修正をしました", "修正完了");
            this.userCode = "";
            this.getUserList(1, null);
          })
          .catch(reason => {
            this.alert("error", "修正に失敗しました", "エラー");
          });
      } else {
        console.log("fix error");
      }
    },
    ReleaseCardInfo: function(state) {
      this.$swal({
        title: "ユーザーに紐づいているICカードを解除します",
        text: "解除しますか？",
        icon: state,
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.$axios
            .post("/user_add/release_card_info", {
              card_idm: this.cardId
            })
            .then(response => {
              var res = response.data;
              if (res.result == 0) {
                this.alert("success", "カードを解除しました", "解除完了");
                this.cardId = "";
                // this.get;
              } else {
              }
            })
            .catch(reason => {});
        } else {
        }
      });
    },
    // 削除
    del: function(id, index) {
      this.userDetails.splice(index, 1);
      this.$axios
        .post("/user_add/del", {
          id: id
        })
        .then(response => {
          var res = response.data;
          if (res.result == 0) {
            this.alert("success", " 行削除しました", "削除成功");
            // this.getDepartmentList();
          } else {
          }
        })
        .catch(reason => {});
    },
    // バリデーション
    checkForm: function() {
      var flag = false;
      this.errors = [];
      this.userDetails.forEach(element => {
        flag = true;
        if (element.apply_term_from == "") {
          this.errors.push("有効期間を入力してください");
          flag = false;
        }
        if (element.name == "") {
          this.errors.push("社員名を入力してください");
          flag = false;
        }
        /*if (element.department_code == "") {
          this.errors.push("部署を選択してください");
          flag = false;
        } */
        if (element.employment_status == "") {
          this.errors.push("雇用形態を選択してください");
          flag = false;
        }
        if (element.working_timetable_no == "") {
          this.errors.push("タイムテーブルを選択してください");
          flag = false;
        }
        if (element.password == "") {
          this.errors.push("パスワードを入力してください");
          flag = false;
        }
        if (element.management == "") {
          this.errors.push("勤怠管理を選択してください");
          flag = false;
        }
        if (element.role == "") {
          this.errors.push("権限を選択してください");
          flag = false;
        }
        /*if (element.kana == "") {
          this.errors.push("ふりがなを入力してください");
          flag = false;
        }*/
      });
      return flag;
    },
    getDepartmentList() {
      this.$axios
        .get("/get_departments_list")
        .then(response => {
          this.departmentList = response.data;
          this.object = { code: "", name: "未選択" };
          this.departmentList.unshift(this.object);
          console.log("部署リスト取得");
        })
        .catch(reason => {
          this.alert("error", "部署リスト取得に失敗しました", "エラー");
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
          this.alert("error", "雇用形態リスト取得に失敗しました", "エラー");
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
          this.alert("error", "タイムテーブルリスト取得に失敗しました", "エラー");
        });
    },
    getGeneralList(value) {
      this.$axios
        .get("/get_general_list", {
          params: {
            identificationid: value
          }
        })
        .then(response => {
          if (value == "C017") {
            this.generalList_m = response.data;
          }
          if (value == "C025") {
            this.generalList_r = response.data;
          }
        })
        .catch(reason => {
          this.alert("error", "勤怠権限リスト取得に失敗しました", "エラー");
        });
    },
    addSuccess() {
      this.getUserList(1, null);
      this.$toasted.show("登録しました");
      this.getUserList(1, null);
      this.userCode = "";
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
          alert("ユーザーリスト取得エラー");
        });
    },
    error() {
      this.alert("error", "登録に失敗しました", "エラー");
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
      this.form.management = "";
      this.form.role = "";
      this.userCode = "";
      this.userDetails = [];
    }
  }
};
</script>

