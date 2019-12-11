<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'各種申請の承認者ルート設定'"
            v-bind:header-text2="'各部署ごとに承認者と最終承認者を登録します。'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contentns row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label class="input-group-text font-size-sm line-height-xs label-width-90" for="inputGroupSelect01">部署</label>
                  </div>
                  <select-department
                    ref="selectdepartment"
                    v-bind:blank-data="true"
                    v-on:change-event="departmentChanges"
                  ></select-department>
                  <message-data v-bind:message-datas="messagedatadepartment" v-bind:message-class="'warning'"></message-data>
                </div>
              </div>
              <div class="col-md-6 pb-2" v-if="show_result">
                <message-data-server v-bind:message-datas="messagedatasserver" v-bind:message-class="'info'"></message-data-server>
              </div>
              <div class="col-md-6 pb-2" v-else>
                <message-data-server v-bind:message-datas="messagedatasserver" v-bind:message-class="'warning'"></message-data-server>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time v-bind:btn-mode="'search'" v-on:searchclick-event="searchclick"></btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.panel contents -->
            <div class="row justify-content-between" v-if="show_result">
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
                    <span class="float-sm-right font-size-sm">「＋」アイコンをクリックすることで承認者情報を追加できます</span>
                  </div>
                  <daily-working-information-panel-header
                    v-bind:header-text1="'承認順番は行順とします。'"
                    v-bind:header-text2="''"
                  ></daily-working-information-panel-header>
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
                                <td class="text-center align-middle w-35 mw-rem-10">部署<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-35 mw-rem-10">社員名<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-35 mw-rem-10"
                                  data-toggle="tooltip"
                                  data-placement="top"
                                  v-bind:title="'正の承認者と直前の正の代理承認者となる副の承認者の区別を選択します。'"
                                >正副区分<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-35 mw-rem-10">操作</td>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(item,index) in confirms" v-bind:key="item.id">
                                <td class="text-center align-middle">
                                  <div class="input-group">
                                    <select-department
                                      ref="selectdepartment"
                                      v-bind:blank-data="true"
                                      v-bind:selected-department="confirms[index].confirm_department_code"
                                      v-bind:row-index="index"
                                      v-on:change-event="departmentChangesConfirm"
                                    ></select-department>
                                  </div>
                                </td>
                                <td class="text-center align-middle">
                                  <div class="input-group">
                                    <select-user
                                      ref="selectuser"
                                      v-bind:blank-data="true"
                                      v-bind:get-Do="'1'"
                                      v-bind:date-value="fromdate"
                                      v-bind:selected-user="confirms[index].user_code"
                                      v-bind:selected-department="confirms[index].confirm_department_code"
                                      v-bind:row-index="index"
                                      v-on:change-event="userChanges"
                                    ></select-user>
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-toggle="tooltip"
                                  data-placement="top"
                                  v-bind:title="'正の承認者と直前の正の代理承認者となる副の承認者の区別を選択します。'"
                                >
                                  <div class="input-group">
                                    <select
                                      class="custom-select"
                                      v-model="confirms[index].main_sub"
                                    >
                                      <option value></option>
                                      <option
                                        v-for="glist in generalList"
                                        :value="glist.code"
                                        v-bind:key="glist.code"
                                      >{{ glist.code_name }}</option>
                                    </select>
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-toggle="tooltip"
                                  data-placement="top"
                                  v-bind:title="'データは削除されません。「この内容で登録する」押下で削除されます。'"
                                >
                                  <div class="btn-group">
                                    <button
                                      type="button"
                                      class="btn btn-danger btn-lg font-size-rg"
                                      @click="alertDelConf('info',item.id,index)"
                                    >行削除</button>
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
              <!-- .panel -->
              <div class="col-md pt-3 align-self-stretch">
                <div class="card shadow-pl">
                  <!-- panel header -->
                  <div class="card-header bg-transparent pt-3 border-0">
                    <h1 class="float-sm-left font-size-rg">
                      <span>
                        <button class="btn btn-success btn-lg font-size-rg" @click="appendFinal">+</button>
                      </span>
                      最終承認者情報
                    </h1>
                    <span class="float-sm-right font-size-sm">「＋」アイコンをクリックすることで最終承認者情報を追加できます</span>
                  </div>
                  <daily-working-information-panel-header
                    v-bind:header-text1="'承認順番は行順とします。'"
                    v-bind:header-text2="''"
                  ></daily-working-information-panel-header>
                  <!-- /.panel header -->
                  <!-- panel body -->
                  <!-- .row -->
                  <div class="row justify-content-between" v-if="finalerrors.length">
                    <!-- col -->
                    <div class="col-md-12 pb-2">
                      <ul class="error-red color-red">
                        <li v-for="(error,index) in finalerrors" v-bind:key="index">{{ error }}</li>
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
                                <td class="text-center align-middle w-35 mw-rem-10">部署<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-35 mw-rem-10">社員名<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-35 mw-rem-10"
                                  data-toggle="tooltip"
                                  data-placement="top"
                                  v-bind:title="'正の承認者と直前の正の代理承認者となる副の承認者の区別を選択します。'"
                                >正副区分<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-35 mw-rem-10">操作</td>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(item,index) in final_confirms" v-bind:key="item.id">
                                <td class="text-center align-middle">
                                  <div class="input-group">
                                    <select-department
                                      ref="selectdepartment2"
                                      v-bind:blank-data="true"
                                      v-bind:selected-department="final_confirms[index].confirm_department_code"
                                      v-bind:row-index="index"
                                      v-on:change-event="departmentChangesFinalConfirm"
                                    ></select-department>
                                  </div>
                                </td>
                                <td class="text-center align-middle">
                                  <div class="input-group">
                                    <select-user
                                      ref="selectuser2"
                                      v-bind:blank-data="true"
                                      v-bind:get-Do="'1'"
                                      v-bind:date-value="fromdate"
                                      v-bind:selected-user="final_confirms[index].user_code"
                                      v-bind:selected-department="final_confirms[index].confirm_department_code"
                                      v-bind:row-index="index"
                                      v-on:change-event="userFinalChanges"
                                    ></select-user>
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-toggle="tooltip"
                                  data-placement="top"
                                  v-bind:title="'正の承認者と直前の正の代理承認者となる副の承認者の区別を選択します。'"
                                >
                                  <div class="input-group">
                                    <select
                                      class="custom-select"
                                      v-model="final_confirms[index].main_sub"
                                    >
                                      <option value></option>
                                      <option
                                        v-for="glist in generalList"
                                        :value="glist.code"
                                        v-bind:key="glist.code"
                                      >{{ glist.code_name }}</option>
                                    </select>
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-toggle="tooltip"
                                  data-placement="top"
                                  v-bind:title="'データは削除されません。「この内容で登録する」押下で削除されます。'"
                                >
                                  <div class="btn-group">
                                    <button
                                      type="button"
                                      class="btn btn-danger btn-lg font-size-rg"
                                      @click="alertFinalDelConf('info',item.id,index)"
                                    >行削除</button>
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
            </div>
            <div class="row justify-content-between" v-if="show_result">
              <!-- .row -->
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time v-bind:btn-mode="'store'" v-on:storeclick-event="storeclick"></btn-work-time>
              </div>
              <!-- /.col -->
              <!-- /.row -->
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
import {dialogable} from '../mixins/dialogable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "SettingRoot",
  mixins: [ dialogable, requestable ],
  data() {
    return {
      valuedepartment: "",
      messagedatadepartment: [],
      departmentList: [],
      userList: [],
      confirms: [],
      final_confirms: [],
      generalList: [],
      userCode: "",
      departmentCode: "",
      validate: false,
      show_result: false,
      messagedatasserver: [],
      fromdate : '',
      getDo : 1,
      errors: [],
      finalerrors: []
    };
  },
  // マウント時
  mounted() {
    this.confirms = [];
    this.getDepartmentList();
    this.getUserList(1, null);
    this.getGeneralList("C027");
  },
  methods: {
    // 検索部署選択が変更された場合の処理
    departmentChanges: function(value) {
      this.valuedepartment = value;
    },
    // 承認者部署選択が変更された場合の処理
    departmentChangesConfirm: function(value, index) {
      this.confirms[index].confirm_department_code = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getUserSelected(value, index);
    },
    // 最終承認者部署選択が変更された場合の処理
    departmentChangesFinalConfirm: function(value, index) {
      this.final_confirms[index].confirm_department_code = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getUserSelectedFinal(value, index);
    },
    // 承認者ユーザー選択が変更された場合の処理
    userChanges: function(value, index) {
      this.confirms[index].user_code = value;
    },
    // 最終承認者ユーザー選択が変更された場合の処理
    userFinalChanges: function(value, index) {
      this.final_confirms[index].user_code = value;
    },
    // 検索開始ボタンがクリックされた場合の処理
    searchclick: function(e) {
      this.show_result = false;
      this.validate = this.checkFormMain(e);
      if (this.validate) {
        this.itemClear();
        this.$axios
          .get("/confirm/show", {
            params: {
              departmentcode: this.valuedepartment,
            }
          })
          .then(response => {
            this.resresults = response.data;
            this.show_result = this.resresults.show_result;
            if (this.show_result == true) {
              if (this.resresults.confirms != null) {
                this.confirms = this.resresults.confirms;
              }
              if (this.resresults.final_confirms != null) {
                this.final_confirms = this.resresults.final_confirms;
              }
            }
            if (this.resresults.messagedata != null) {
              this.messagedatasserver = this.resresults.messagedata;
            }
            this.$forceUpdate();
          })
          .catch(reason => {
            alert("承認者情報取得エラー");
          });
      }
    },
    append: function() {
      this.confirms.push({
        id: "",
        department_code: "",
        user_code: "",
        main_sub: ""
      });
    },
    appendFinal: function() {
      this.final_confirms.push({
        id: "",
        department_code: "",
        user_code: "",
        main_sub: ""
      });
    },
    alert: function(state, message, title) {
      this.$swal(title, message, state);
    },
    alertDelConf: function(state, id, index) {
      if (this.confirms[index].confirm_department_code ||
          this.confirms[index].user_code ||
          this.confirms[index].main_sub) {
        this.$swal({
          title: "確認",
          text: "行削除しますか？",
          icon: state,
          buttons: true,
          dangerMode: true
        }).then(willDelete => {
          if (willDelete) {
            this.confirms.splice(index, 1);
          } else {
          }
        });
      } else {
        this.confirms.splice(index, 1);
      }
    },
    alertFinalDelConf: function(state, id, index) {
      if (this.final_confirms[index].confirm_department_code ||
          this.final_confirms[index].user_code ||
          this.final_confirms[index].main_sub) {
        this.$swal({
          title: "確認",
          text: "行削除しますか？",
          icon: state,
          buttons: true,
          dangerMode: true
        }).then(willDelete => {
          if (willDelete) {
            this.final_confirms.splice(index, 1);
          } else {
          }
        });
      } else {
        this.final_confirms.splice(index, 1);
      }
    },
    storeclick: function(e) {
      this.validate = this.checkForm(e);
      if (this.validate) {
        this.$axios
          .post("/confirm/store", {
            departmentcode: this.valuedepartment,
            confirms: this.confirms,
            final_confirms: this.final_confirms
          })
          .then(response => {
            this.resresults = response.data;
            this.store_result = this.resresults.store_result;
            if (this.store_result == true) {
              this.alert("success", "登録しました", "登録完了");
            } else {
            this.alert("error", "登録に失敗しました", "エラー");
            }
            if (this.resresults.messagedata != null) {
              this.messagedatasserver = this.resresults.messagedata;
            }
            this.$forceUpdate();
          })
          .catch(reason => {
            this.alert("error", "登録に失敗しました", "エラー");
          });
      }
    },
    // バリデーション
    checkFormMain: function(e) {
      var flag = true;
      this.messagedatadepartment = [];
      if (!this.valuedepartment) {
        this.messagedatadepartment.push("部署を選択してください");
        flag = false;
      }

      if (flag) {
        return flag;
      }
      e.preventDefault();
    },
    checkForm: function(e) {
      var flag = true;
      this.errors = [];
      this.finalerrors = [];
      var cnt = 0;
      this.confirms.forEach(element => {
        cnt++;
        if (!element.confirm_department_code) {
          this.errors.push("部署を選択してください (" + cnt + "行目）");
          flag = false;
        }
        if (!element.user_code) {
          this.errors.push("社員を入力してください (" + cnt + "行目）");
          flag = false;
        }
        if (!element.main_sub) {
          this.errors.push("正副区分を選択してください (" + cnt + "行目）");
          flag = false;
        }
      });
      cnt = 0;
      this.final_confirms.forEach(element => {
        cnt++;
        if (!element.confirm_department_code) {
          this.finalerrors.push("部署を選択してください (" + cnt + "行目）");
          flag = false;
        }
        if (!element.user_code) {
          this.finalerrors.push("社員を入力してください (" + cnt + "行目）");
          flag = false;
        }
        if (!element.main_sub) {
          this.finalerrors.push("正副区分を選択してください (" + cnt + "行目");
          flag = false;
        }
      });

      if (flag) {
        return flag;
      }
      e.preventDefault();
    },
    getDepartmentList() {
      this.postRequest("/get_departments_list", [])
        .then(response  => {
          this.departmentList = response.data;
        })
        .catch(reason => {
          var messages = [];
          messages.push("部署選択リスト作成エラー");
          this.messageswal("エラー", messages, "error", true, false, true);
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
          if (value == "C027") {
            this.generalList = response.data;
          }
        })
        .catch(reason => {
          this.alert("error", "勤怠権限リスト取得に失敗しました", "エラー");
        });
    },
    addSuccess() {
      this.$toasted.show("登録しました");
      this.itemClear();
    },
    getUserList(getdovalue, value) {
      this.$axios
        .get("/get_user_list", {
          params: {
            getdo: getdovalue,
            code: value
          }
        })
        .then(response => {
          this.userList = response.data;
        })
        .catch(reason => {
          alert("ユーザーリスト取得エラー");
        });
    },
    error() {
      this.alert("error", "登録に失敗しました", "エラー");
    },
    itemClear() {
      this.messagedatadepartment = [];
      this.messagedatasserver = [];
      this.errors = [];
      this.confirms = [];
      this.final_confirms = [];
    },
    // ユーザー選択コンポーネント取得メソッド
    getUserSelected: function(departmentvalue, index) {
      this.getDo = 1;
      this.fromdate = ""
      if (!departmentvalue) {
        this.$refs.selectuser[index].getUserList(this.getDo, this.fromdate);
      } else {
        this.$refs.selectuser[index].getUserListByDepartment(
          this.getDo,
          departmentvalue,
          this.fromdate
        );
      }
    },
    // 最終承認者ユーザー選択コンポーネント取得メソッド
    getUserSelectedFinal: function(departmentvalue, index) {
      this.getDo = 1;
      this.fromdate = ""
      if (!departmentvalue) {
        this.$refs.selectuser2[index].getUserList(this.getDo, this.fromdate);
      } else {
        this.$refs.selectuser2[index].getUserListByDepartment(
          this.getDo,
          departmentvalue,
          this.fromdate
        );
      }
    }
  }
};
</script>

