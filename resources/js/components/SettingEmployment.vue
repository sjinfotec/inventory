<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div id="input-area_3" class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'雇用形態を設定する'"
            v-bind:header-text2="'雇用形態の登録や変更ができます'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >雇用形態選択<span class="color-red">[必須]</span></span>
                  </div>
                  <select-employmentstatuslist v-if="showemploymentlist"
                    ref="selectemploymentlist"
                    v-bind:blank-data="false"
                    v-bind:placeholder-data="'雇用形態を選択すると編集モードになります'"
                    v-bind:setting-value="selectedValue"
                    v-bind:add-new="true"
                    v-bind:date-value="''"
                    v-bind:kill-value="valuekillcheck"
                    v-bind:row-index=0
                    v-on:change-event="employmentChanges"
                  ></select-employmentstatuslist>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- .row -->
          </div>
          <!-- panel contents -->
        </div>
      </div>
      <!-- .panel -->
      <div id="input-area_3" class="col-md-12 pt-3" v-if="selectMode=='NEW'">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆雇用形態登録'"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- panel contents -->
          <div class="card-body pt-2">
            <!-- .row -->
            <div class="row justify-content-between" v-if="messagevalidatesNew.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li v-for="(messagevalidate,index) in messagevalidatesNew" v-bind:key="index">{{ messagevalidate }}</li>
                </ul>
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
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >雇用形態名<span class="color-red">[必須]</span></span>
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.code_name"
                    name="code_name"
                  />
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div id="btn_cnt6" class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:storeclick-event="storeclick"
                  v-bind:btn-mode="'store'"
                ></btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.panel contents -->
        </div>
      </div>
      <div class="col-md-12 pt-3" v-if="selectMode=='EDT'">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆雇用形態編集'"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2" v-if="details.length">
            <!-- panel contents -->
            <!-- .panel -->
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
            <div class="col-md pt-3 align-self-stretch">
              <div class="card shadow-pl">
                <!-- panel body -->
                <div class="card-body mb-3 p-0 border-top">
                  <!-- panel contents -->
                  <div class="row">
                    <div class="col-12">
                      <div class="table-responsive">
                        <table id="table_cnt6" class="table table-striped border-bottom font-size-sm text-nowrap">
                          <thead>
                            <tr>
                              <td class="text-center align-middle w-35 mw-rem-10">雇用形態名</td>
                              <td colspan="2" class="text-center align-middle w-35 mw-rem-10">操作</td>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="(item,index) in details" v-bind:key="item.code">
                              <td class="text-center align-middle">
                                <div class="input-group">
                                  <input
                                    type="text"
                                    maxlength="50"
                                    class="form-control"
                                    v-model="details[index].code_name"
                                  />
                                </div>
                              </td>
                              <td class="text-center align-middle">
                                <div class="btn-group">
                                  <button
                                    type="button"
                                    class="btn btn-success"
                                    @click="fixclick(index)"
                                  >この内容で更新する</button>
                                </div>
                              </td>
                              <td class="text-center align-middle">
                                <div class="btn-group">
                                  <button
                                    type="button"
                                    class="btn btn-danger"
                                    @click="delClick(index)"
                                  >削除する</button>
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
            <!-- /.panel -->
          </div>
          <!-- /main contentns row -->
        </div>
      </div>
    </div>
    <!-- /.panel -->
    <!-- /main contentns row -->
  </div>
</template>
<script>
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

// CONST
const CONST_C001 = 'C001';

export default {
  name: "settingEmployment",
  mixins: [ dialogable, checkable, requestable ],
  data() {
    return {
      form: {
        code: "",
        code_name: ""
      },
      dbtablename: "",
      listitemname: "",
      messagevalidatesNew: [],
      messagevalidatesEdt: [],
      valueemployment: "",
      valuekillcheck: false,
      employmentList: [],
      selectMode: "",
      details: [],
      applyTerms: [],
      selectedValue: "",
      selectedName: "",
      selectApplyTerm: "",
      count: 0,
      before_count: 0,
      showemploymentlist: true,
      confirmresult: true,
      oldId: ""
    };
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション（新規作成）
    checkFormStore: function() {
      this.messagevalidatesNew = [];
      var chkArray = [];
      var flag = true;
      // 雇用形態名
      var required = true;
      var equalength = 0;
      var maxlength = 50;
      var itemname = '雇用形態名';
      chkArray = 
        this.checkHeader(this.form.code_name, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        this.messagevalidatesNew = chkArray;
        flag = false;
      }
      return flag;
    },
    // バリデーション（更新）
    checkFormFix: function(index) {
      this.messagevalidatesEdt = [];
      var chkArray = [];
      var flag = true;
      // 雇用形態名
      var required = true;
      var equalength = 0;
      var maxlength = 50;
      var itemname = '雇用形態名';
      chkArray =
        this.checkDetail(this.details[index].code_name, required, equalength, maxlength, itemname, index+1);
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
    // ------------------------ イベント処理 ------------------------------------
    
    // 雇用形態選択が変更された場合の処理
    employmentChanges: function(value, arrayitem) {
      // 入力項目の雇用形態クリア
      this.inputClear();
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.selectedValue = value;
      if (value == "" || value == null) {
        this.selectMode = 'NEW';
      } else {
        this.selectMode = 'EDT';
        this.selectedName = arrayitem['code_name'];
        this.getemployment();
      }
    },
    // 新規作成ボタンクリック処理
    storeclick() {
      var flag = this.checkFormStore();
      if (flag) {
        var messages = [];
        messages.push("この内容で登録しますか？");
        this.htmlMessageSwal("確認", messages, "info", true, true)
          .then(result  => {
            if (result) {
              this.store();
            }
        });
      }
    },
    // 更新ボタンクリック処理
    fixclick(index) {
      var flag = this.checkFormFix(index);
      if (flag) {
        var messages = [];
        messages.push("この内容で更新しますか？");
        messages.push("OKボタンクリックで更新されます。");
        this.htmlMessageSwal("確認", messages, "info", true, true)
          .then(result  => {
            if (result) {
              this.FixDetail("更新", index);
            }
        });
      }
    },
    // 削除ボタンクリック処理
    delClick(index) {
      var messages = [];
      messages.push("この内容を削除しますか？");
      messages.push("OKボタンクリックで削除されます。");
      this.htmlMessageSwal("確認", messages, "info", true, true)
        .then(result  => {
          if (result) {
            this.DelDetail(index);
          }
      });
    },
    
    // -------------------- サーバー処理 ----------------------------
    // 雇用形態取得処理
    getemployment() {
      this.details = [];
      var messages = [];
      this.postRequest("/setting_employment/get", { code : this.selectedValue , identification_id : CONST_C001})
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("取得");
        });
    },
    // 雇用形態登録処理
    store() {
      var messages = [];
      var arrayParams = { identification_id : CONST_C001, code_name : this.form.code_name };
      this.postRequest("/setting_employment/store", arrayParams)
        .then(response  => {
          this.putThenHead(response, "登録");
        })
        .catch(reason => {
          this.serverCatch("登録");
        });
    },
    // 雇用形態更新処理（明細）
    FixDetail(kbnname, index) {
      var messages = [];
      var arrayParams = { details : this.details[index] };
      this.postRequest("/setting_employment/fix", arrayParams)
        .then(response  => {
          this.putThenDetail(response, kbnname);
        })
        .catch(reason => {
          this.serverCatch(kbnname);
        });
    },
    // 雇用形態削除処理（明細）
    DelDetail(index) {
      var messages = [];
      var arrayParams = { id : this.details[index].id };
      this.postRequest("/setting_employment/del", arrayParams)
        .then(response  => {
          this.putThenDetail(response, "削除");
        })
        .catch(reason => {
          this.serverCatch("削除");
        });
    },
    // -------------------- 共通 ----------------------------
    // 取得正常処理
    getThen(response) {
      this.details = [];
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        this.count = this.details.length;
        this.before_count = this.count;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("取得");
        }
      }
    },
    // 更新系正常処理
    putThenHead(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.$toasted.show("雇用形態を" + eventtext + "しました");
        this.refreshtemploymentList();
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    // 更新系正常処理（明細）
    putThenDetail(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.$toasted.show("雇用形態を" + eventtext + "しました");
        this.refreshtemploymentList();
        this.getemployment();
        this.count = this.details.length;
        this.before_count = this.count;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    // 異常処理
    serverCatch(eventtext) {
      var messages = [];
      messages.push("雇用形態情報" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    inputClear() {
      this.details = [];
      this.form.code_name = "";
      this.form.code = "";
      this.selectedValue = "";
      this.selectedName = "";
      this.selectMode = "";
      this.count = 0;
      this.before_count = 0;
    },
    refreshtemploymentList() {
      // 最新リストの表示
      this.showemploymentlist = false;
      this.$nextTick(() => (this.showemploymentlist = true));
    }
  }
};
</script>
