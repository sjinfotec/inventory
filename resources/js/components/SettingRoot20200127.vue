<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- ========================== 検索部 START ========================== -->
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
            <!-- ----------- 選択リスト START ---------------- -->
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >所属部署</span>
                  </div>
                  <select-departmentlist v-if="showdepartmentlist"
                    ref="selectdepartmentlist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'部署を選択してください'"
                    v-bind:selected-department="selectedDepartmentValue"
                    v-bind:add-new="false"
                    v-bind:date-value="''"
                    v-bind:kill-value="valueDepartmentkillcheck"
                    v-bind:row-index=0
                    v-on:change-event="departmentChanges"
                  ></select-departmentlist>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- 選択リスト END ---------------- -->
            <!-- ----------- 選択ボタン類 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:searchclick-event="searchclick"
                  v-bind:btn-mode="'search'"
                  v-bind:is-push="issearchbutton">
                </btn-work-time>
              </div>
              <!-- /.col -->
              <!-- .col -->
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
                                    <select-departmentlist
                                      ref="selectdepartmentlist"
                                      v-bind:blank-data="true"
                                      v-bind:selected-department="confirms[index].confirm_department_code"
                                      v-bind:row-index="index"
                                      v-on:change-event="departmentChangesConfirm"
                                    ></select-departmentlist>
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
                                    <select-departmentlist
                                      ref="selectdepartmentlist"
                                      v-bind:blank-data="true"
                                      v-bind:selected-department="final_confirms[index].confirm_department_code"
                                      v-bind:row-index="index"
                                      v-on:change-event="departmentChangesFinalConfirm"
                                    ></select-departmentlist>
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
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "SettingRoot",
  mixins: [ dialogable, checkable, requestable ],
  data() {
    return {
      selectedDepartmentValue : "",
      valueDepartmentkillcheck : false,
      showdepartmentlist: true,
      showadddepartmentlist: true,
      departmentList: [],
      valueUserkillcheck : false,
      userList: [],
      confirms: [],
      final_confirms: [],
      generalList: [],
      userCode: "",
      departmentCode: "",
      validate: false,
      show_result: false,
      messagedatasserver: [],
      messagevalidatesEdt: [],
      fromdate : '',
      issearchbutton: false,
      getDo : 1,
      errors: [],
      finalerrors: []
    };
  },
  // マウント時
  mounted() {
    this.confirms = [];
    this.getUserList('');
    this.getGeneralList("C027");
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    checkFormSearch: function() {
      this.messagevalidatesEdt = [];
      var chkArray = [];
      var flag = true;
      // 氏名
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '部署';
      chkArray = 
        this.checkHeader(this.selectedDepartmentValue, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      if (this.messagevalidatesEdt.length > 0) {
        flag = false;
      }
      return flag;
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
    // ------------------------ イベント処理 ------------------------------------
    
    // 検索部署選択が変更された場合の処理
    departmentChanges: function(value, arrayitem) {
      this.selectedDepartmentValue = value;
    },
    // 承認者部署選択が変更された場合の処理
    departmentChangesConfirm: function(value, arrayitem) {
      this.confirms[arrayitem['index']].confirm_department_code = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getUserSelected(value, index);
    },
    // 最終承認者部署選択が変更された場合の処理
    departmentChangesFinalConfirm: function(value, arrayitem) {
      this.final_confirms[arrayitem['index']].confirm_department_code = value;
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
    searchclick: function() {
      this.show_result = false;
      this.validate = this.checkFormSearch();
      if (this.validate) {
        this.itemClear();
        this.getConfirm();
      } else {
        this.messageswal("エラー", this.messagevalidatesEdt, "error", true, false, true)
          .then(result  => {
            if (result) {
            }
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
            departmentcode: this.selectedDepartmentValue,
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
    // -------------------- サーバー処理 ----------------------------
    // ユーザーリスト取得処理
    getUserList(targetdate){
      if (targetdate == '') {
        targetdate = moment(new Date()).format("YYYYMMDD");
      }
      if (this.selectedDepartmentValue == "") { this.selectedDepartmentValue = null; }
      this.postRequest("/get_user_list",
        { targetdate: targetdate,
          killvalue: this.killValue,
          getDo : this.getDo,
          departmentcode : this.selectedDepartmentValue,
          employmentcode : null,
          managementcode : "ALL"
        })
        .then(response  => {
          this.getThenuser(response);
          if (this.selectedDepartmentValue == null) { this.selectedDepartmentValue = ""; }
          // 固有処理 END
        })
        .catch(reason => {
          this.serverCatch("ユーザー", "取得");
          if (this.selectedDepartmentValue == null) { this.selectedDepartmentValue = ""; }
        });
    },
    // コード選択リスト取得処理
    getGeneralList(value) {
      var arrayParams = { identificationid : value };
      this.postRequest("/get_general_list", arrayParams)
        .then(response  => {
          if (value == "C027") {
            this.getThenmanagement(response, "勤怠権限");
          }
        })
        .catch(reason => {
          if (value == "C027") {
            this.serverCatch("勤怠権限", "取得");
          }
        });
    },
    // 承認者取得処理
    getConfirm(){
      this.postRequest("/confirm/show",
        { departmentcode : this.selectedDepartmentValue })
        .then(response  => {
          this.getThenconfirm(response);
        })
        .catch(reason => {
          this.serverCatch("承認者", "取得");
        });
    },
    error() {
      this.alert("error", "登録に失敗しました", "エラー");
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
    },
    // -------------------- 共通 ----------------------------
    // ユーザー選択コンポーネント取得メソッド
    getUserSelected: function() {
      // managementcode=99 → すべて
      this.$refs.selectuserlist.getList(
        '',
        this.valueUserkillcheck,
        this.getDo,
        this.selectedDepartmentValue,
        null,
        99
      );
    },
    // 取得正常処理（ユーザーリスト）
    getThenuser(response) {
      var res = response.data;
      if (res.result) {
        this.userList = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("エラー", res.messagedata, "error", true, false, true);
        } else {
          this.serverCatch("ユーザー", "取得");
        }
      }
    },
    // 取得正常処理（明細勤怠管理対象選択リスト）
    getThenmanagement(response, value) {
      var res = response.data;
      if (res.result) {
        this.generalList_m = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("エラー", res.messagedata, "error", true, false, true);
        } else {
          this.serverCatch("明細勤怠管理", "取得");
        }
      }
    },
    // 取得正常処理（承認者）
    getThenconfirm(response) {
      var res = response.data;
      if (res.result) {
        this.confirms = res.confirms;
        this.final_confirms = res.final_confirms;
        if (res.messagedata.length > 0) {
          this.messageswal("警告", res.messagedata, "info", true, false, true);
        }
        this.show_result = true;
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("エラー", res.messagedata, "error", true, false, true);
        } else {
          this.serverCatch("承認者", "取得");
        }
      }
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + "情報" + eventtext + "に失敗しました");
      this.messageswal("エラー", messages, "error", true, false, true);
    },
    itemClear() {
      this.confirms = [];
      this.final_confirms = [];
    }
  }
};
</script>

