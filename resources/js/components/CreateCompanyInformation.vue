<template>
<div>
  <!-- main contentns row -->
  <div class="row justify-content-between">
    <!-- .panel -->
    <div class="col-md pt-3">
      <div class="card shadow-pl">
        <!-- panel header -->
        <daily-working-information-panel-header
          v-bind:header-text1="'◆会社情報'"
          v-bind:header-text2="'会社所在地や連絡先を設定できます'"
        ></daily-working-information-panel-header>
        <!-- /.panel header -->
        <div class="card-body pt-2">
          <!-- panel contents -->
          <!-- .row -->
          <div class="row justify-content-between">
            <div class="row justify-content-between" v-if="messagevalidatesNew.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li v-for="(messagevalidate,index) in messagevalidatesNew" v-bind:key="index">{{ messagevalidate }}</li>
                </ul>
              </div>
              <!-- /.col -->
            </div>
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">会社名<span class="color-red">[必須]</span></span>
                </div>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.name"
                  maxlength="191"
                  name="name"
                />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">会社フリガナ</span>
                </div>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.kana"
                  maxlength="191"
                  name="kana"
                />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">郵便番号</span>
                </div>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.post_code"
                  maxlength="10"
                  name="post_code"
                />
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">住所１</span>
                </div>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.address1"
                  maxlength="191"
                  name="address1"
                />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">住所２</span>
                </div>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.address2"
                  maxlength="191"
                  name="address2"
                />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">住所フリガナ</span>
                </div>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.address_kana"
                  maxlength="191"
                  name="address_kana"
                />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between">
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">電話番号</span>
                </div>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.tel_no"
                  maxlength="15"
                  name="tel_no"
                />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">FAX</span>
                </div>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.fax_no"
                  maxlength="15"
                  name="fax_no"
                />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">代表者氏名</span>
                </div>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.represent_name"
                  maxlength="191"
                  name="represent_name"
                />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">代表者フリガナ</span>
                </div>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.represent_kana"
                  maxlength="191"
                  name="represent_kana"
                />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">メールアドレス</span>
                </div>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.email"
                  maxlength="191"
                  name="email"
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
              <btn-work-time
                v-on:storeclick-event="storeclick"
                v-bind:btn-mode="'store'"
              ></btn-work-time>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /panel body -->
      </div>
    </div>
    <!-- /.panel -->
  </div>
  <!-- /main contentns row -->
</div>
</template>
<script>
import toasted from "vue-toasted";
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "Createdetailsrmation",
  mixins: [ dialogable, checkable, requestable ],
  data() {
    return {
      form: {
        name: "",
        kana: "",
        post_code: "",
        address1: "",
        address2: "",
        address_kana: "",
        tel_no: "",
        fax_no: "",
        represent_name: "",
        represent_kana: "",
        email: ""
      },
      messagevalidatesNew: [],
      details: []
    };
  },
  // マウント時
  mounted() {
    this.getItem();
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション
    checkFormStore: function() {
      this.messagevalidatesNew = [];
      var chkArray = [];
      var flag = true;
      // 会社名
      var required = true;
      var equalength = 0;
      var maxlength = 191;
      var itemname = '会社名';
      chkArray = 
        this.checkHeader(this.form.name, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // 会社カナ
      required = false;
      equalength = 0;
      maxlength = 191;
      itemname = '会社カナ';
      chkArray = 
        this.checkHeader(this.form.kana, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // 郵便番号
      required = false;
      equalength = 0;
      maxlength = 10;
      itemname = '郵便番号';
      chkArray = 
        this.checkHeader(this.form.post_code, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // 住所1
      required = false;
      equalength = 0;
      maxlength = 191;
      itemname = '住所１';
      chkArray = 
        this.checkHeader(this.form.address1, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // 住所２
      required = false;
      equalength = 0;
      maxlength = 191;
      itemname = '住所２';
      chkArray = 
        this.checkHeader(this.form.address2, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // 住所カナ
      required = false;
      equalength = 0;
      maxlength = 191;
      itemname = '住所カナ';
      chkArray = 
        this.checkHeader(this.form.address_kana, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // 電話番号
      required = false;
      equalength = 0;
      maxlength = 15;
      itemname = '電話番号';
      chkArray = 
        this.checkHeader(this.form.tel_no, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // FAX番号
      required = false;
      equalength = 0;
      maxlength = 15;
      itemname = 'FAX番号';
      chkArray = 
        this.checkHeader(this.form.fax_no, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // 代表者氏名
      required = false;
      equalength = 0;
      maxlength = 191;
      itemname = '代表者氏名';
      chkArray = 
        this.checkHeader(this.form.represent_name, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // 代表者カナ
      required = false;
      equalength = 0;
      maxlength = 191;
      itemname = '代表者カナ';
      chkArray = 
        this.checkHeader(this.form.represent_kana, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // email
      required = false;
      equalength = 0;
      maxlength = 191;
      itemname = 'メールアドレス';
      chkArray = 
        this.checkHeader(this.form.email, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }

      if (this.messagevalidatesNew.length > 0) {
        flag = false;
      }
      return flag;
    },
    // ------------------------ イベント処理 ------------------------------------
    // -------------------- サーバー処理 ----------------------------
    // 会社取得処理
    getItem() {
      this.inputClear();
      this.postRequest("/create_company_information/get",  [])
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("取得");
        });
    },
    // 会社登録処理
    storeclick() {
      if (this.checkFormStore()) {
        var messages = [];
        var arrayParams = { form : this.form };
        this.postRequest("/create_company_information/store", arrayParams)
          .then(response  => {
            this.putThenHead(response, "登録");
          })
          .catch(reason => {
            this.serverCatch("登録");
          });
      }
    },
    // -------------------- 共通 ----------------------------
    // 取得正常処理
    getThen(response) {
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        this.count = this.details.length;
        this.before_count = this.count;
        if ( this.details.length > 0) {
          this.form.name = this.details[0].name;
          this.form.kana = this.details[0].kana;
          this.form.address1 = this.details[0].address1;
          this.form.address2 = this.details[0].address2;
          this.form.address_kana = this.details[0].address_kana;
          this.form.post_code = this.details[0].post_code;
          this.form.tel_no = this.details[0].tel_no;
          this.form.fax_no = this.details[0].fax_no;
          this.form.represent_name = this.details[0].represent_name;
          this.form.represent_kana = this.details[0].represent_kana;
          this.form.email = this.details[0].email;
        }
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("エラー", res.messagedata, "error", true, false, true);
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
        messages.push("会社情報を" + eventtext + "しました");
        this.messageswal(eventtext + "完了", messages, "success", true, false, true);
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("警告", res.messagedata, "warning", true, false, true);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    // 異常処理
    serverCatch(eventtext) {
      messages.push("会社情報" + eventtext + "に失敗しました");
      this.messageswal("エラー", messages, "error", true, false, true);
    },
    inputClear() {
      this.details = [];
      this.count = 0;
      this.before_count = 0;
      this.form.name = "";
      this.form.kana = "";
      this.form.address1 = "";
      this.form.address2 = "";
      this.form.address_kana = "";
      this.form.post_code = "";
      this.form.tel_no = "";
      this.form.fax_no = "";
      this.form.represent_name = "";
      this.form.represent_kana = "";
      this.form.email = "";
    }
  }
};
</script>
