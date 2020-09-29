<template>
<div>
  <!-- main contentns row -->
  <div class="row justify-content-between">
    <!-- .panel -->
    <div class="col-md pt-3">
      <div class="card shadow-pl">
        <!-- panel header -->
        <daily-working-information-panel-header
          v-bind:header-text1="'◆カスタマ登録情報検索'"
          v-bind:header-text2="'登録IDか登録会社名（どちらか入力）で検索'"
        ></daily-working-information-panel-header>
        <!-- /.panel header -->
        <div class="card-body pt-2">
          <!-- panel contents -->
          <!-- .row -->
          <!------------------------- 検索項目部 start -------------------------->
          <div class="row justify-content-between">
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">アカウントID</span>
                </div>
                <input
                  type="text"
                  class="form-control"
                  v-model="sel.account_id"
                  maxlength="8"
                  name="account_id"
                />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">会社名</span>
                </div>
                <input
                  type="text"
                  class="form-control"
                  v-model="sel.name"
                  maxlength="191"
                  name="name"
                />
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!------------------------- 検索項目部 end -------------------------->
          <!------------------------- 検索ボタン部 start -------------------------->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <btn-work-time
                v-on:searchclick-event="searchclick"
                v-bind:btn-mode="'search'"
                v-bind:is-push="issearchbutton"
              ></btn-work-time>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!------------------------- 検索ボタン部 end -------------------------->
          <!------------------------- 検索メッセージ部 start -------------------------->
          <!-- .row -->
          <div class="row justify-content-between">
            <div class="row justify-content-between" v-if="messagesel.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li v-for="(item,index) in messagesel" v-bind:key="index">{{ item }}</li>
                </ul>
              </div>
              <!-- /.col -->
            </div>
          </div>
          <!-- /.row -->
          <!------------------------- 検索メッセージ部 end -------------------------->
        </div>
      </div>
    </div>
    <!-- /.panel -->
  </div>
  <div class="row justify-content-between">
    <!-- .panel -->
    <div class="col-md pt-3" v-if="lists.length">
      <div class="card shadow-pl">
        <!-- panel header -->
        <daily-working-information-panel-header
          v-bind:header-text1="'◆カスタマ登録情報一覧'"
          v-bind:header-text2="''"
        ></daily-working-information-panel-header>
        <!-- /.panel header -->
        <div class="card-body pt-2">
          <!-- panel contents -->
          <!------------------------- カスタマ登録情報一覧部 start -------------------------->
          <!-- .row -->
          <div v-if="isList && lists.length">
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table table-striped border-bottom font-size-sm text-nowrap">
                    <thead>
                      <tr>
                        <td class="text-center align-middle mw-rem-5">No.</td>
                        <td class="text-center align-middle mw-rem-5">アカウントID</td>
                        <td class="text-center align-middle mw-rem-20">会社名</td>
                        <td class="text-center align-middle mw-rem-5">操作</td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(item,index) in lists"
                      >
                        <td class="text-center align-middle mw-rem-5">{{ index + 1 }}</td>
                        <td class="text-center align-middle mw-rem-5">{{ item.account_id }}</td>
                        <td class="text-left align-middle mw-rem-20">{{ item.company_name }}</td>
                        <td class="text-center align-middle mw-rem-5" style="text-align:center">
                          <i class="fa" v-on:click="detailClick(index)">
                            <span style="color: #0000FF;cursor: hand; cursor:pointer;">詳細</span>
                          </i>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- .row -->
          <!------------------------- カスタマ登録情報一覧部 end -------------------------->
          <!------------------------- カスタマ登録情報詳細部 start -------------------------->
          <!-- .row -->
          <div v-if="isDetail && details.length">
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table table-striped border-bottom font-size-sm text-nowrap">
                    <thead>
                      <tr>
                        <td class="text-center align-middle mw-rem-3">No.</td>
                        <td class="text-center align-middle mw-rem-5">アカウントID</td>
                        <td class="text-center align-middle mw-rem-15">会社名</td>
                        <td class="text-center align-middle mw-rem-5">問い合わせ日付</td>
                        <td class="text-center align-middle mw-rem-5">問い合わせ時刻</td>
                        <td class="text-center align-middle mw-rem-10">問い合わせ種類</td>
                        <td class="text-center align-middle mw-rem-5">担当者氏名</td>
                        <td class="text-center align-middle mw-rem-5">電話番号</td>
                        <td class="text-center align-middle mw-rem-5">email</td>
                        <td class="text-center align-middle mw-rem-5">郵便番号</td>
                        <td class="text-center align-middle mw-rem-20">住所</td>
                        <td class="text-center align-middle mw-rem-20">問い合わせ内容</td>
                        <td class="text-center align-middle mw-rem-5">受付担当者氏名</td>
                        <td class="text-center align-middle mw-rem-5">操作</td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(item,index) in details"
                      >
                        <td class="text-center align-middle mw-rem-3">{{ index + 1 }}</td>
                        <td class="text-center align-middle mw-rem-5">{{ item.account_id }}</td>
                        <td class="text-left align-middle mw-rem-15">{{ item.company_name }}</td>
                        <td class="text-left align-middle mw-rem-5">{{ item.entry_date }}</td>
                        <td class="text-left align-middle mw-rem-5">{{ item.entry_time }}</td>
                        <td class="text-left align-middle mw-rem-10">{{ item.entry_type_name }}</td>
                        <td class="text-left align-middle mw-rem-5">{{ item.representative_name }}</td>
                        <td class="text-left align-middle mw-rem-5">{{ item.phone_number }}</td>
                        <td class="text-left align-middle mw-rem-5">{{ item.email_value }}</td>
                        <td class="text-left align-middle mw-rem-5">{{ item.post_code }}</td>
                        <td class="text-left align-middle mw-rem-20">{{ item.address_value }}</td>
                        <td class="text-left align-middle mw-rem-20">{{ item.entry_contents }}</td>
                        <td class="text-left align-middle mw-rem-5">{{ item.ssjjoo_representative_name }}</td>
                        <td class="text-center align-middle mw-rem-5" style="text-align:center">
                          <i class="fa" v-on:click="detailClick(index)">
                            <span style="color: #0000FF;cursor: hand; cursor:pointer;">詳細</span>
                          </i>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- .row -->
          <!------------------------- カスタマ登録情報詳細部 end -------------------------->
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
      messagesel: [],
      sel: {
        account_id: "",
        name: ""
      },
      issearchbutton: false,
      isList: false,
      isDetail: false,
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
      settingmessage: [],
      lists: [],
      details: []
    };
  },
  // マウント時
  mounted() {
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
    
    // 検索ボタンの処理
    searchclick: function() {
      this.getItemList();
      this.isList = false;
      this.isDetail = false;
    },
    // 詳細ボタンの処理
    detailClick: function(index) {
      this.getItemDetail(index);
      this.isList = false;
      this.isDetail = true;
    },
    // -------------------- サーバー処理 ----------------------------
    // カスタマ取得処理
    getItemList() {
      var messages = [];
      var arrayParams = { sel : this.sel };
      this.postRequest("/customer_information/getList",  arrayParams)
        .then(response  => {
          this.getThenList(response);
        })
        .catch(reason => {
          this.serverCatch("取得");
        });
    },
    // カスタマ詳細取得処理
    getItemDetail(index) {
      var messages = [];
      var arraysel = {
        account_id : this.lists[index]['account_id'],
        name : null
      };
      var arrayParams = {
        sel : arraysel
      };
      this.postRequest("/customer_information/getDetail",  arrayParams)
        .then(response  => {
          this.getThenDetail(response);
        })
        .catch(reason => {
          this.serverCatch("詳細取得");
        });
    },
    // 会社登録処理
    storeclick() {
      if (this.checkFormStore()) {
        var messages = [];
        var arrayParams = { form : this.form };
        this.postRequest("/create_company_information/store", arrayParams)
          .then(response  => {
            this.putThenList(response, "登録");
          })
          .catch(reason => {
            this.serverCatch("登録");
          });
      }
    },
    // -------------------- 共通 ----------------------------
    // 取得正常処理
    getThenList(response) {
      var res = response.data;
      if (res.result) {
        this.lists = res.details;
        this.isList = true;
        this.isDetail = false;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("一覧取得");
        }
      }
    },
    // 詳細取得正常処理
    getThenDetail(response) {
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        console.log('ompanyinformation getThenDetail this.details = ' + this.details.length)
        this.isList = false;
        this.isDetail = true;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("詳細取得");
        }
      }
    },
    // 更新系正常処理
    putThenList(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.$toasted.show("会社情報を" + eventtext + "しました");
        this.getNotSetting();
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
      messages.push("会社情報" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    
    // 設定要否取得処理
    getNotSetting() {
      if (this.settingdepartments == 0) {
        this.getThenDepartment();
      } else if (this.settingsettings == 0) {
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
<style scoped>

.mw-rem-3 {
  min-width: 2rem;
}

.mw-rem-8 {
  min-width: 8rem;
}

.mw-rem-12 {
  min-width: 12rem;
}

</style>
