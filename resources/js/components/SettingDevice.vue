<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3 print-none">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'機器情報を設定する'"
            v-bind:header-text2="'機器の登録や変更ができます'"
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
                    >機器選択<span class="color-red">[必須]</span></span>
                  </div>
                  <select-devicelist v-if="showdevicelist"
                    ref="selectdevicelist"
                    v-bind:blank-data="false"
                    v-bind:placeholder-data="'機器を選択すると編集モードになります'"
                    v-bind:setting-value="selectedValue"
                    v-bind:add-new="addNew"
                    v-bind:date-value="''"
                    v-on:change-event="deviceChanges"
                  ></select-devicelist>
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
      <div class="col-md-12 pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆機器登録'"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- panel contents -->
          <div class="card-body pt-2">
            <!-- .row -->
            <div class="row justify-content-between  print-none" v-if="messagevalidatesNew.length">
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
            <div class="row justify-content-between  print-none">
              <!-- .col -->
              <div class="col-md-3 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-60"
                      id="basic-addon1"
                    >コード<span class="color-red">[必須]</span></span>
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.code"
                    name="name"
                  />
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >機器名<span class="color-red">[必須]</span></span>
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.name"
                    name="name"
                  />
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-3 pb-2">
                <div class="input-group">
                  <a @click="qrcodeClick" class="btn btn-primary">QRコード作成</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-3 pb-2" v-if="form.qrText1">
                <span>----[作業開始]----[{{ form.code }}] [{{ form.name }}]</span>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-9 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <vue-qrcode v-if="form.qrText1" :value="form.qrText1" :options="qroption1" tag="img"></vue-qrcode>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-3 pb-2" v-if="form.qrText2">
                <span>----[作業終了]----[{{ form.code }}] [{{ form.name }}]</span>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-9 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <vue-qrcode v-if="form.qrText2" :value="form.qrText2" :options="qroption1" tag="img"></vue-qrcode>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-3 pb-2" v-if="form.qrText3">
                <span>----[作業中断]----[{{ form.code }}] [{{ form.name }}]</span>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-9 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <vue-qrcode v-if="form.qrText3" :value="form.qrText3" :options="qroption1" tag="img"></vue-qrcode>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-3 pb-2" v-if="form.qrText9">
                <span>----[作業完了]----[{{ form.code }}] [{{ form.name }}]</span>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-9 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <vue-qrcode v-if="form.qrText9" :value="form.qrText9" :options="qroption1" tag="img"></vue-qrcode>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between  print-none">
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
import VueQrcode from "@chenfengyuan/vue-qrcode";

export default {
  name: "Createdevice",
  mixins: [ dialogable, checkable, requestable ],
  props: {
    authusers: {
      type: Array,
      default: []
    }
  },
  components: {
    VueQrcode
  },
  data() {
    return {
      form: {
        code: "",
        floor_pos: "",
        name: "",
        qrText1: "",
        qrText2: "",
        qrText3: "",
        qrText9: ""
      },
      addNew: true,
      details: [],
      count: 0,
      before_count: 0,
      qroption1: {
        errorCorrectionLevel: "M",
        maskPattern: 0,
        margin: 10,
        scale: 2,
        width: 300,
        color: {
          dark: "#000000FF",
          light: "#FFFFFFFF"
        }
      },
      qroption2: {
        errorCorrectionLevel: "M",
        maskPattern: 0,
        margin: 10,
        scale: 2,
        width: 200,
        color: {
          dark: "#000000FF",
          light: "#FFFFFFFF"
        }
      },
      qroption3: {
        errorCorrectionLevel: "M",
        maskPattern: 0,
        margin: 10,
        scale: 2,
        width: 100,
        color: {
          dark: "#000000FF",
          light: "#FFFFFFFF"
        }
      },
      qroption4: {
        errorCorrectionLevel: "M",
        maskPattern: 0,
        margin: 10,
        scale: 2,
        width: 50,
        color: {
          dark: "#000000FF",
          light: "#FFFFFFFF"
        }
      },
      dbtablename: "",
      listitemname: "",
      messagevalidatesNew: [],
      messagevalidatesEdt: [],
      valuedevice: "",
      valuekillcheck: false,
      deviceList: [],
      selectMode: "",
      applyTerms: [],
      selectedValue: "",
      selectedName: "",
      selectApplyTerm: "",
      showdevicelist: true,
      confirmresult: true,
      oldId: "",
      infoMsgcnt: 0,
      settingmessage: []
    };
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション（新規作成）
    checkFormStore: function() {
      this.messagevalidatesNew = [];
      var chkArray = [];
      var flag = true;
      // 機器名
      var required = true;
      var equalength = 0;
      var maxlength = 50;
      var itemname = '機器名';
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
      // 適用開始日
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '適用開始日';
      chkArray = 
        this.checkDetail(this.details[index].apply_term_from, required, equalength, maxlength, itemname, index+1);
      if (chkArray.length > 0) {
        this.messagevalidatesEdt = chkArray;
        flag = false;
      }
      // 機器名
      required = true;
      equalength = 0;
      maxlength = 50;
      itemname = '機器名';
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
    
    // 機器選択が変更された場合の処理
    deviceChanges: function(value, arrayitem) {
      // 入力項目の機器クリア
      this.inputClear();
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.selectedValue = value;
      if (value == "" || value == null) {
        this.addNew = true;
      } else {
        this.addNew = false;
        this.form.code = arrayitem['code'];
        this.form.name = arrayitem['name'];
        this.form.floor_pos = arrayitem['floor_pos'];
        this.getdevice();
      }
    },
    
    // QRコード作成ボタンクリック処理
    qrcodeClick() {
      this.form.qrText1 = "&kind='1'&device='" + this.form.code + "'";
      this.form.qrText2 = "&kind='2'&device='" + this.form.code + "'";
      this.form.qrText3 = "&kind='3'&device='" + this.form.code + "'";
      this.form.qrText9 = "&kind='9'&device='" + this.form.code + "'";
      this.$forceUpdate();
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
      this.htmlMessageSwal("確認", messages, "info", true, true)
        .then(result  => {
          if (result) {
            this.DelDetail(index);
          }
      });
    },
    // -------------------- サーバー処理 ----------------------------
    // 機器取得処理
    getdevice() {
      this.details = [];
      var messages = [];
      this.postRequest("get_device_list", { code : null })
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("取得");
        });
    },
    // 機器登録処理
    store() {
      var messages = [];
      var arrayParams = { code : this.form.code, name : this.form.name };
      this.postRequest("/create_device/store", arrayParams)
        .then(response  => {
          this.putThenHead(response, "登録");
        })
        .catch(reason => {
          this.serverCatch("登録");
        });
    },
    // 機器更新処理（明細）
    FixDetail(kbnname, index) {
      var messages = [];
      var arrayParams = { details : this.details[index] };
      this.postRequest("/create_device/fix", arrayParams)
        .then(response  => {
          this.putThenDetail(response, kbnname);
        })
        .catch(reason => {
          this.serverCatch(kbnname);
        });
    },
    // 機器削除処理（明細）
    DelDetail(index) {
      var messages = [];
      var arrayParams = { id : this.details[index].id };
      this.postRequest("/create_device/del", arrayParams)
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
        this.$toasted.show("機器を" + eventtext + "しました");
        this.refreshtdeviceList();
        this.getNotSetting();
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
        this.$toasted.show("機器を" + eventtext + "しました");
        this.refreshtdeviceList();
        this.getdevice();
        this.count = this.details.length;
        this.before_count = this.count;
        this.getNotSetting();
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    
    // 設定要否取得処理
    getNotSetting() {
      if (this.infoMsgcnt > 1) { return; }
      if (this.settingsettings == 0) {
        this.getThenSetting();
      } else if (this.settingworkingtimetables == 0) {
        this.getThenWorkingtimetables();
      } else if (this.settingusers == 0) {
        this.getThenUsers();
      } else if (this.settingcalendarsettinginformations == 0) {
        this.getThenCalendarSettingInfos();
      } else if (this.isexistdownload == 0) {
        this.getThenDownload();
      }
      this.infoMsgcnt++;
    },
    // 異常処理
    serverCatch(eventtext) {
      var messages = [];
      messages.push("機器情報" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    inputClear() {
      this.details = [];
      this.form.name = "";
      this.form.floor_pos = "";
      this.form.id = "";
      this.form.code = "";
      this.selectedValue = "";
      this.selectedName = "";
      this.selectMode = "";
      this.count = 0;
      this.before_count = 0;
    },
    checkRowData(index) {
      if (this.details[index].apply_term_from != "" && this.details[index].apply_term_from != null) { return true; }
      if (this.details[index].kill_from_date != "" && this.details[index].kill_from_date != null) { return true; }
      if (this.details[index].name != "" && this.details[index].name != null) { return true; }
      return false;
    },
    refreshtdeviceList() {
      // 最新リストの表示
      this.showdevicelist = false;
      this.$nextTick(() => (this.showdevicelist = true));
    }
  }
};
</script>
