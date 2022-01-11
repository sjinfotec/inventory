<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div id="input-area_3" class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'営業所を設定する'"
            v-bind:header-text2="'営業所の登録や変更ができます'"
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
                    >営業所<!--<span class="color-red">[必須]</span>--></span>
                  </div>
                  <select-officelist v-if="showofficelist"
                    ref="selectOfficeList"
                    v-bind:blank-data="false"
                    v-bind:placeholder-data="'営業所を選択すると編集モードになります'"
                    v-bind:setting-value="selectedValue"
                    v-bind:add-new="true"
                    v-bind:date-value="''"
                    v-bind:row-index=0
                    v-on:change-event="officeChanges"
                  ></select-officelist>
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
            v-bind:header-text1="'◆営業所登録'"
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
                    >営業所名<span class="color-red">[必須]</span></span>
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.name"
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
            v-bind:header-text1="'◆営業所編集'"
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
                              <td class="text-center align-middle w-35 mw-rem-10">営業所名</td>
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
                                    v-model="details[index].name"
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
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "CreateOffice",
  mixins: [ dialogable, checkable, requestable ],
  props: {
    authusers: {
      type: Array,
      default: []
    },
    settingcompanies: {
      type: String,
      default: ""
    },
    settingsettings: {
      type: String,
      default: ""
    },
    settingusers: {
      type: String,
      default: ""
    }
  },
  data() {
    return {
      form: {
        name: "",
        id: "",
        code: ""
      },
      dbtablename: "",
      listitemname: "",
      messagevalidatesNew: [],
      messagevalidatesEdt: [],
      officeList: [],
      selectMode: "",
      details: [],
      selectedValue: "",
      selectedName: "",
      count: 0,
      before_count: 0,
      showofficelist: true,
      confirmresult: true,
      oldId: "",
      infoMsgcnt: 0,
      settingmessage: [],
      actmode: "",
      actvalue: ""
    };
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション（新規作成）
    checkFormStore: function() {
      this.messagevalidatesNew = [];
      var chkArray = [];
      var flag = true;
      // 営業所名
      var required = true;
      var equalength = 0;
      var maxlength = 50;
      var itemname = '営業所名';
      chkArray = 
        this.checkHeader(this.form.name, required, equalength, maxlength, itemname);
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
      // 営業所名
      var required = true;
      var equalength = 0;
      var maxlength = 50;
      var itemname = '営業所名';
      chkArray =
        this.checkDetail(this.details[index].name, required, equalength, maxlength, itemname, index+1);
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
    
    // 部署選択が変更された場合の処理
    officeChanges: function(value, arrayitem) {
      // 入力項目の部署クリア
      this.inputClear();
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.selectedValue = value;
      if (value == "" || value == null) {
        this.selectMode = 'NEW';
      } else {
        this.selectMode = 'EDT';
        this.selectedName = arrayitem['name'];
        this.getOffice();
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
    // 営業所取得処理
    getOffice(actvalue) {
      this.details = [];
      var messages = [];
      console.log('CreateOffice getoffice act = ' + actvalue);
      this.postRequest("/create_office/get", { code : this.selectedValue, actmode : actvalue})
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("取得");
        });
    },
    // 営業所登録処理
    store() {
      var messages = [];
      var arrayParams = { code : this.form.code, name : this.form.name };
      this.postRequest("/create_office/store", arrayParams)
        .then(response  => {
          this.putThenHead(response, "登録");
        })
        .catch(reason => {
          this.serverCatch("登録");
        });
    },
    // 営業所更新処理（明細）
    FixDetail(kbnname, index) {
      var messages = [];
      var arrayParams = { details : this.details[index] };
      this.postRequest("/create_office/fix", arrayParams)
        .then(response  => {
          this.putThenDetail(response, kbnname);
        })
        .catch(reason => {
          this.serverCatch(kbnname);
        });
    },
    // 営業所削除処理（明細）
    DelDetail(index) {
      var messages = [];
      var arrayParams = { id : this.details[index].id };
      //this.selectedValue = value;
      //this.selectedValue = 0;
      var delmode = 1;
      console.log('CreateOffice DelDetail go-in ');
      console.log('CreateOffice DelDetail delmode = ' + delmode);

      this.postRequest("/create_office/del", arrayParams)
        .then(response  => {
          this.delThenHead(response, "削除", delmode);
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
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("ERROR getThen", res.messagedata, "error", true, false);
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
        this.$toasted.show("営業所を" + eventtext + "しました");
        this.refreshOfficeList();
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    // 更新系正常処理
    putThenDetail(response, eventtext, actmode) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.$toasted.show("営業所を" + eventtext + "しました");
        this.getOffice(actmode);
        this.refreshOfficeList();
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    // 削除系正常処理
    delThenHead(response, eventtext, actmode) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.$toasted.show("営業所を" + eventtext + "しました");
        this.refreshOfficeList();
        this.selectMode = "viewoff";
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
      messages.push("営業所情報" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    inputClear() {
      this.details = [];
      this.form.name = "";
      this.form.id = "";
      this.form.code = "";
      this.selectedValue = "";
      this.selectedName = "";
      this.selectMode = "";
      this.count = 0;
      this.before_count = 0;
    },
    refreshOfficeList() {
      // 最新リストの表示
      this.showofficelist = false;
      this.$nextTick(() => (this.showofficelist = true));
    }
  }
};
</script>
