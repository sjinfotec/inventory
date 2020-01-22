<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'パスワード変更入力'"
            v-bind:header-text2="'新しいパスワードに変更します。'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="target_employmentstatus"
                    >雇用形態</label>
                  </div>
                  <select-employmentstatuslist
                    ref="selectemploymentstatuslist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'雇用形態を選択してください'"
                    v-bind:selected-value="selectedEmploymentValue"
                    v-on:change-event="employmentChanges"
                  ></select-employmentstatuslist>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="target_department"
                    >所属部署</label>
                  </div>
                  <select-departmentlist
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
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="target_users"
                    >氏 名<span class="color-red">[必須]</span></label>
                  </div>
                  <select-userlist v-if="showuserlist"
                    ref="selectuserlist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'氏名を選択してください'"
                    v-bind:selected-value="selectedUserValue"
                    v-bind:add-new="false"
                    v-bind:get-do="getDo"
                    v-bind:date-value="applytermdate"
                    v-bind:kill-value="valueUserkillcheck"
                    v-bind:row-index=0
                    v-bind:department-value="selectedDepartmentValue"
                    v-bind:employment-value="selectedEmploymentValue"
                    v-on:change-event="userChanges"
                  ></select-userlist>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-sm label-width-230"
                    >新しいパスワード<span class="color-red">[必須]</span></span>
                  </div>
                  <input
                    class="form-control"
                    v-model="enterPass"
                    maxlength="12"
                    type="text"
                    title="半角英数字12文字以内"
                    pattern="^[a-zA-Z0-9]{6,12}$"
                  />
                </div>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-sm label-width-230"
                    >新しいパスワード（再入力）<span class="color-red">[必須]</span></span>
                  </div>
                  <input
                    class="form-control"
                    v-model="reEnterPass"
                    maxlength="12"
                    type="password"
                    title="半角英数字12文字以内"
                    pattern="^[a-zA-Z0-9]{6,12}$"
                  />
                </div>
                <p class="rf-mini rf-mt-5">※確認のため再入力してください。</p>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- メッセージ部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between" v-if="messagevalidatesEdt.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li v-for="(messagevalidate,index) in messagevalidatesEdt" v-bind:key="index">{{ messagevalidate }}</li>
                </ul>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- メッセージ部 END ---------------- -->
            <!-- ----------- ボタン部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:passreset-event="passresetclick"
                  v-bind:btn-mode="'passreset'"
                  v-bind:is-push="ispassresetbutton">
                </btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- ボタン部 END ---------------- -->
            <!-- /.panel contents -->
          </div>
        </div>
      </div>
      <!-- /.panel -->
    </div>
    <!-- /main contentns row -->
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
  name: "UserPass",
  mixins: [ dialogable, checkable, requestable ],
  data() {
    return {
      selectedDepartmentValue : "",
      valueDepartmentkillcheck : false,
      showdepartmentlist: true,
      showadddepartmentlist: true,
      selectedUserValue : "",
      valueUserkillcheck : false,
      ispassresetbutton : false,
      showuserlist: true,
      selectedEmploymentValue : "",
      valueTimeTablekillcheck: false,
      messagevalidatesEdt: [],
      selectedUserName: "",
      getDo : 1,
      departmentList: [],
      applytermdate: moment(new Date()).format("YYYYMMDD"),
      enterPass: "",
      reEnterPass: "",
      validate: false,
      errors: []
    };
  },
  // マウント時
  mounted() {
    console.log("UserPass Component mounted.");
  },
  methods: {
    // バリデーション
    checkFormFix: function() {
      this.messagevalidatesEdt = [];
      var chkArray = [];
      var flag = true;
      // 氏名
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '氏名';
      chkArray = 
        this.checkHeader(this.selectedUserValue, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      // 新しいパスワード
      required = true;
      equalength = 0;
      maxlength = 12;
      itemname = '新しいパスワード';
      chkArray =
        this.checkHeader(this.enterPass, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      // 新しいパスワード（再入力）
      required = true;
      equalength = 0;
      maxlength = 12;
      itemname = '新しいパスワード（再入力）';
      chkArray =
        this.checkHeader(this.reEnterPass, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      // ログインＩＤ
      if (this.messagevalidatesEdt.length == 0) {
        if (this.reEnterPass != this.enterPass) {
          this.messagevalidatesEdt.push("入力したパスワードが一致していません");
        }
      }
      if (this.messagevalidatesEdt.length > 0) {
        flag = false;
      }
      return flag;
    },
    // ------------------------ イベント処理 ------------------------------------
    
    // 雇用形態が変更された場合の処理
    employmentChanges: function(value, arrayitem) {
      this.selectedEmploymentValue = value;
      this.inputClear()
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getUserSelected();
    },
    // 部署選択が変更された場合の処理
    departmentChanges: function(value, arrayitem) {
      this.selectedDepartmentValue = value;
      this.inputClear()
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getUserSelected();
    },
    // ユーザー選択が変更された場合の処理
    userChanges: function(value, arrayitem) {
      // 入力項目の部署クリア
      this.messagevalidatesEdt = [];
      this.selectedUserValue = value;
      this.selectedUserName = arrayitem['name'];
    },
    // 更新ボタンクリック処理
    passresetclick() {
      this.messagevalidatesEdt = [];
      var flag = this.checkFormFix();
      if (flag) {
        var messages = [];
        messages.push("パスワード変更します。よろしいですか？");
        this.messageswal("確認", messages, "info", true, true, true)
          .then(result  => {
            if (result) {
              this.Fixpass("変更");
            }
        });
      // 項目数が多い場合以下コメントアウト
      } else {
        this.countswal("エラー", this.messagevalidatesEdt, "error", true, false, true)
          .then(result  => {
            if (result) {
            }
        });
      }
    },
    // -------------------- サーバー処理 ----------------------------
    // パスワード更新処理
    Fixpass(kbnname) {
      var arrayParams = { user_id : this.selectedUserValue, pass_word : this.enterPass };
      this.postRequest("/user_pass/passchange", arrayParams)
        .then(response  => {
          this.putThenPass(response, kbnname);
        })
        .catch(reason => {
          this.serverCatch("パスワード", "変更");
        });
    },
    // 部署選択リスト取得処理
    getDepartmentList(targetdate){
      if (targetdate == '') {
          targetdate = moment(new Date()).format("YYYYMMDD");
      }
      var arrayParams = { targetdate : targetdate, killvalue: this.valueDepartmentkillcheck };
      this.postRequest("/get_departments_list", arrayParams)
        .then(response  => {
          this.getThendepartment(response);
        })
        .catch(reason => {
          this.serverCatch("部署", "取得");
        });
    },
    // 氏名選択リスト取得処理
    getUserList(targetdate){
      if (targetdate == '') {
        targetdate = moment(new Date()).format("YYYYMMDD");
      }
      if (this.selectedDepartmentValue == "") { this.selectedDepartmentValue = null; }
      if (this.selectedEmploymentValue == "") { this.selectedEmploymentValue = null; }
      this.postRequest("/get_user_list",
        { targetdate: targetdate,
          killvalue: this.killValue,
          getDo : this.getDo,
          departmentcode : this.selectedDepartmentValue,
          employmentcode : this.selectedEmploymentValue,
          managementcode : "ALL"
        })
        .then(response  => {
          this.getThenuser(response);
          if (this.selectedDepartmentValue == null) { this.selectedDepartmentValue = ""; }
          if (this.selectedEmploymentValue == null) { this.selectedEmploymentValue = ""; }
          // 固有処理 END
        })
        .catch(reason => {
          this.serverCatch("氏名", "取得");
          if (this.selectedDepartmentValue == null) { this.selectedDepartmentValue = ""; }
          if (this.selectedEmploymentValue == null) { this.selectedEmploymentValue = ""; }
        });
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
        this.selectedEmploymentValue,
        99
      );
    },
    // 取得正常処理（ユーザーリスト）
    getThenuser(response) {
      this.details = [];
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        this.count = this.details.length;
        this.before_count = this.count;
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("エラー", res.messagedata, "error", true, false, true);
        } else {
          this.serverCatch("氏名", "取得");
        }
      }
    },
    // 更新系正常処理
    putThenPass(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        messages.push("パスワードを" + eventtext + "しました");
        this.messageswal(eventtext + "完了", messages, "success", true, false, true);
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("警告", res.messagedata, "warning", true, false, true);
        } else {
          this.serverCatch("パスワード", eventtext);
        }
      }
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + "情報" + eventtext + "に失敗しました");
      this.messageswal("エラー", messages, "error", true, false, true);
    },
    inputClear() {
      this.enterPass = "";
      this.reEnterPass = "";
    }
  }
};
</script>

