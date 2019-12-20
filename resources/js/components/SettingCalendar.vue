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
            v-bind:header-text1="'年月を指定してカレンダーを設定する'"
            v-bind:header-text2="'初期設定では期首月または１月から１年分を自動初期設定します。'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- ----------- 選択リスト START ---------------- -->
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
                    >指定年月</span>
                  </div>
                  <input-datepicker
                    v-bind:default-date="valueym"
                    v-bind:date-format="'yyyy年MM月'"
                    v-on:change-event="fromymChanges"
                    v-on:clear-event="fromymCleared"
                  ></input-datepicker>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- 選択リスト END ---------------- -->
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
            <!-- ----------- 選択ボタン類 START ---------------- -->
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
            <!-- /.row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:initclick-event="initclick"
                  v-bind:btn-mode="'init'"
                  v-bind:is-push="isinitbutton">
                </btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- 選択ボタン類 END ---------------- -->
          </div>
          <!-- panel contents -->
        </div>
      </div>
      <!-- /.panel -->
      <!-- ========================== 検索部 END ========================== -->
      <!-- /main contentns row -->
      <!-- ========================== 編集部 START ========================== -->
      <!-- .panel -->
      <div class="col-md-12 pt-3" v-if="selectMode=='EDT'">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆カレンダー編集'"
            v-bind:header-text2="'設定済みのカレンダーを編集できます。'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- ----------- 編集入力部 START ---------------- -->
          <!-- main contentns row -->
          <div class="card-body pt-2" v-if="details.length">
            <!-- panel contents -->
            <!-- ----------- メッセージ部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between" v-if="messagevalidatesFix.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li v-for="(messagevalidate,index) in messagevalidatesFix" v-bind:key="index">{{ messagevalidate }}</li>
                </ul>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- メッセージ部 END ---------------- -->
            <!-- ----------- 項目部 START ---------------- -->
            <!-- panel contents -->
            <!-- .row -->
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table table-striped border-bottom font-size-sm text-nowrap">
                    <thead>
                      <tr>
                        <td class="text-left align-middle w-30">日付</td>
                        <td class="text-left align-middle w-35 mw-rem-10">営業日区分<span class="color-red">[必須]</span></td>
                        <td class="text-left align-middle w-35 mw-rem-10">休暇区分</td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(item,index) in details" v-bind:key="item.date">
                        <td class="text-left align-middle">{{item.date_name}}</td>
                        <td class="text-center align-middle">
                          <select class="form-control" v-model="business[index]" @change="businessDayChanges(business[index], index)">
                            <option value></option>
                            <option
                              v-for="blist in BusinessDayList"
                              :value="blist.code"
                              v-bind:key="blist.code"
                            >{{ blist.code_name }}</option>
                          </select>
                        </td>
                        <td class="text-center align-middle">
                          <select class="form-control" v-model="holiday[index]" @change="holiDayChanges(holiday[index], index)">
                            <option value></option>
                            <option
                              v-for="hlist in HoliDayList"
                              :value="hlist.code"
                              v-bind:key="hlist.code"
                            >{{ hlist.code_name }}</option>
                          </select>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <!-- /.row -->
            <!-- ----------- ボタン部 START ---------------- -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:fixclick-event="fixclick"
                  v-bind:btn-mode="'fix'"
                  v-bind:is-push="isfixbutton">
                </btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- ボタン部 END ---------------- -->
            <!-- /.panel contents -->
          </div>
          <!-- /main contentns row -->
          <!-- ----------- 編集入力部 END ---------------- -->
        </div>
      </div>
      <!-- ========================== 編集部 END ========================== -->
      <!-- /.panel -->
      <!-- ========================== 初期設定部 START ========================== -->
      <!-- .panel -->
      <div class="col-md-12 pt-3" v-if="selectMode=='INT'">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆カレンダー初期設定'"
            v-bind:header-text2="'１年のカレンダーを初期設定します。'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- ----------- 編集入力部 START ---------------- -->
          <!-- main contentns row -->
          <!-- panel contents -->
          <!-- ----------- メッセージ部 START ---------------- -->
          <!-- .row -->
          <div class="row justify-content-between" v-if="messagevalidatesInt.length">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <ul class="error-red color-red">
                <li v-for="(messagevalidate,index) in messagevalidatesInt" v-bind:key="index">{{ messagevalidate }}</li>
              </ul>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- ----------- メッセージ部 END ---------------- -->
          <!-- ----------- 項目部 START ---------------- -->
          <div class="card-body pt-2">
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >指定年<span class="color-red">[必須]</span></span>
                  </div>
                  <input-datepicker
                    v-bind:default-date="valueyear"
                    v-bind:date-format="'yyyy年'"
                    v-on:change-event="fromyearChanges"
                    v-on:clear-event="fromyearCleared"
                  ></input-datepicker>
                </div>
              </div>
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      for="inputGroupSelect01"
                    >設定区分選択<span class="color-red">[必須]</span></label>
                  </div>
                  <select-generallist v-if="showC024list"
                      ref="selectC024list"
                      v-bind:blank-data="false"
                      v-bind:placeholder-data="'設定区分を選択してください'"
                      v-bind:setting-value="selectedC024Value"
                      v-bind:add-new="true"
                      v-bind:date-value="''"
                      v-bind:kill-value="valueC024killcheck"
                      v-bind:row-index=0
                      v-bind:identification-id="'C024'"
                      v-on:change-event="C024Changes"
                  ></select-generallist>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:storeclick-event="storeclick"
                  v-bind:btn-mode="'store'"
                  v-bind:is-push="isstorebutton">
                </btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .panel -->
              <div class="col-md pt-3 align-self-stretch">
                <div class="card shadow-pl">
                  <!-- panel header -->
                  <div class="card-header bg-transparent pt-3 border-0 print-none">
                    <daily-working-information-panel-header
                      v-bind:header-text1="stringtext"
                      v-bind:header-text2="'土曜日祝日【法定外休日】日曜日【法定休日】を自動設定'"
                    ></daily-working-information-panel-header>
                  </div>
                  <!-- /panel header -->
                </div>
              </div>
              <!-- /.panel -->
            </div>
            <!-- /.row -->
          </div>
          <!-- ----------- 項目部 END ---------------- -->
        </div>
      </div>
      <!-- /.panel -->
      <!-- ========================== 初期設定部 END ========================== -->
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
  name: "SettingCalendar",
  mixins: [ dialogable, checkable, requestable ],
  data() {
    return {
      defaultYm: new Date(),
      defaultYear: new Date(),
      valueym: "",
      valueyear: "",
      year: "",
      month: "",
      yearinit: "",
      issearchbutton: false,
      isinitbutton: false,
      isfixbutton: false,
      isstorebutton: false,
      selectMode: "",
      messagevalidatesInt: [],
      messagevalidatesEdt: [],
      messagevalidatesFix: [],
      showC024list: true,
      selectedC024Value: "",
      valueC024killcheck: false,
      stringtext: "",
      details: [],
      BusinessDayList: [],
      HoliDayList: [],
      business: [{}],
      holiday: [{}]
    };
  },
  // マウント時
  mounted() {
    this.valueym = this.defaultYm;
    this.year = moment(this.valueym).format("YYYY");
    this.month = moment(this.valueym).format("MM");
    this.valueyear = this.defaultYm;
    this.yearinit = moment(this.valueyear).format("YYYY");
    this.getGeneralList("C007");
    this.getGeneralList("C008");
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション（表示）
    checkFormEdt: function() {
      this.messagevalidatesEdt = [];
      var chkArray = [];
      var flag = true;
      // 指定年月
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '指定年月';
      chkArray = 
        this.checkHeader(this.valueym, required, equalength, maxlength, itemname);
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
    // バリデーション（更新）
    checkFormFix: function() {
      this.messagevalidatesFix = [];
      var chkArray = [];
      var flag = true;
      // 営業日区分
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '営業日区分';
      for ( var i=0; i<this.business.length;i++ ) {
        chkArray = 
          this.checkDetailtext(this.business[i], required, equalength, maxlength, itemname, this.details[i]['date_name']);
        if (chkArray.length > 0) {
          if (this.messagevalidatesFix.length == 0) {
            this.messagevalidatesFix = chkArray;
          } else {
            this.messagevalidatesFix = this.messagevalidatesFix.concat(chkArray);
          }
        }
      }

      if (this.messagevalidatesFix.length > 0) {
        flag = false;
      }
      return flag;
    },
    // バリデーション（初期設定）
    checkFormInit: function() {
      this.messagevalidatesInt = [];
      var chkArray = [];
      var flag = true;
      // 指定年
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '指定年';
      chkArray = 
        this.checkHeader(this.valueyear, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesInt = chkArray;
        } else {
          this.messagevalidatesInt = this.messagevalidatesInt.concat(chkArray);
        }
      }
      // 設定区分選択
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = '設定区分選択';
      chkArray = 
        this.checkHeader(this.selectedC024Value, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesInt.length == 0) {
          this.messagevalidatesInt = chkArray;
        } else {
          this.messagevalidatesInt = this.messagevalidatesInt.concat(chkArray);
        }
      }

      if (this.messagevalidatesInt.length > 0) {
        flag = false;
      }
      return flag;
    },
    // ------------------------ イベント処理 ------------------------------------
    
    // 指定年月が変更された場合の処理
    fromymChanges: function(value) {
      this.valueym = value;
      this.year = moment(this.valueym).format("YYYY");
      this.month = moment(this.valueym).format("MM");
    },
    // 指定年月がクリアされた場合の処理
    fromymCleared: function() {
      this.valueym = "";
      this.year = "";
      this.month = "";
    },
    // 出勤区分がクリアされた場合の処理
    businessDayChanges: function(value, index) {
      if (value < 2) {
        this.holiday[index] = null;
      }
    },
    // 休暇区分がクリアされた場合の処理
    holiDayChanges: function(value, index) {
      if (value < 1) {
        this.business[index] = 1;
      } else {
        this.business[index] = 3;
      }
    },
    // 指定年が変更された場合の処理
    fromyearChanges: function(value) {
      this.valueyear = value;
      this.yearinit = moment(this.defaultYear).format("YYYY");
      // パネルに表示
      this.setPanelHeader();
    },
    // 指定年がクリアされた場合の処理
    fromyearCleared: function() {
      this.valueyear = "";
      this.yearinit = "";
      // パネルに表示
      this.setPanelHeader();
    },
    // 設定区分が変更された場合の処理
    C024Changes: function(value, arrayItem) {
      this.selectedC024Value = value;
      // パネルに表示
      this.setPanelHeader();
    },
    // 表示ボタンクリック処理
    searchclick() {
      // 入力項目クリア
      this.inputClear();
      this.messagevalidatesInt = [];
      this.messagevalidatesEdt = [];
      this.messagevalidatesFix = [];
      if (this.checkFormEdt()) {
        this.selectMode = 'EDT';
        this.getItem();
      }
    },
    // 初期設定ボタンクリック処理
    initclick() {
      // 入力項目クリア
      this.inputClear();
      this.messagevalidatesInt = [];
      this.messagevalidatesEdt = [];
      this.messagevalidatesFix = [];
      this.selectMode = 'INT';
      // パネルに表示
      this.setPanelHeader();
    },
    //更新ボタンクリック処理
    fixclick() {
      var flag = this.checkFormFix();
      if (flag) {
        var messages = [];
        messages.push("この内容で更新しますか？");
        this.messageswal("確認", messages, "info", true, true, true)
          .then(result  => {
            if (result) {
              this.FixDetail("更新");
            }
        });
      // 項目数が多い場合以下コメントアウト
      } else {
        this.countswal("エラー", this.messagevalidatesFix, "error", true, false, true)
          .then(result  => {
            if (result) {
            }
        });
      }
    },
    //初期設定登録ボタンクリック処理
    storeclick() {
      var flag = this.checkFormInit();
      if (flag) {
        var messages = [];
        messages.push("指定年に登録しているデータは消えますが、初期設定登録してもよろしいですか？");
        messages.push("※処理には数分かかります。");
        this.messageswal("確認", messages, "info", true, true, true)
          .then(result  => {
            if (result) {
              this.init("初期設定登録");
            }
        });
      // 項目数が多い場合以下コメントアウト
      } else {
        this.countswal("エラー", this.messagevalidatesInt, "error", true, false, true)
          .then(result  => {
            if (result) {
            }
        });
      }
    },

    // -------------------- サーバー処理 ----------------------------
    // カレンダー取得処理
    getItem() {
      this.postRequest("/setting_calendar/get", { year : this.year, month : this.month})
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("カレンダー", "取得");
        });
    },
    // カレンダー登録処理
    init(eventname) {
      var messages = [];
      messages.push("処理終了まで画面はそのままにしてください。");
      this.waitswal("処理中", messages, "info");
      var arrayParams = { datefrom : this.yearinit, displaykbn : this.selectedC024Value };
      this.postRequest("/setting_calendar/init", arrayParams)
        .then(response  => {
          this.putThenHead(response, eventname);
        })
        .catch(reason => {
          this.serverCatch("カレンダー", eventname);
        });
    },
    // カレンダー更新処理（明細）
    FixDetail(eventname) {
      var arrayParams = { details : this.details, businessdays : this.business, holidays : this.holiday };
      this.postRequest("/setting_calendar/fix", arrayParams)
        .then(response  => {
          this.putThenDetail(response, eventname);
        })
        .catch(reason => {
          this.serverCatch("カレンダー", eventname);
        });
    },
    // コード選択リスト取得処理
    getGeneralList(value) {
      var arrayParams = { identificationid : value };
      this.postRequest("/get_general_list", arrayParams)
        .then(response  => {
          if (value == "C007") {
            this.getThenbusinesskbn(response, "出勤区分");
          }
          if (value == "C008") {
            this.getThenholidaykbn(response, "休暇区分");
          }
        })
        .catch(reason => {
          if (value == "C007") {
            this.serverCatch("出勤区分", "取得");
          }
          if (value == "C008") {
            this.serverCatch("休暇区分", "取得");
          }
        });
    },
    // -------------------- 共通 ----------------------------
    // 取得正常処理
    getThen(response) {
      this.details = [];
      this.business = [{}];
      this.holiday = [{}];
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        if (this.details.length > 0) {
          this.details.forEach((detail, i) => {
            this.business[i] = detail.business_kubun;
            this.holiday[i] = detail.holiday_kubun;
          });
        }
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("エラー", res.messagedata, "error", true, false, true);
        } else {
          this.serverCatch("カレンダー", "取得");
        }
      }
    },
    // 更新系正常処理
    putThenHead(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        messages.push("カレンダーを" + eventtext + "しました");
        this.messageswal(eventtext + "完了", messages, "success", true, false, true);
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("警告", res.messagedata, "warning", true, false, true);
        } else {
          this.serverCatch("カレンダー",eventtext);
        }
      }
    },
    // 更新系正常処理（明細）
    putThenDetail(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        messages.push("カレンダーを" + eventtext + "しました");
        this.messageswal(eventtext + "完了", messages, "success", true, false, true);
        this.getItem();
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("警告", res.messagedata, "warning", true, false, true);
        } else {
          this.serverCatch("カレンダー", eventtext);
        }
      }
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + eventtext + "に失敗しました");
      this.messageswal("エラー", messages, "error", true, false, true);
    },
    // 取得正常処理（明細出勤区分）
    getThenbusinesskbn(response) {
      var res = response.data;
      if (res.result) {
        this.BusinessDayList = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("エラー", res.messagedata, "error", true, false, true);
        } else {
          this.serverCatch("出勤区分", "取得");
        }
      }
    },
    // 取得正常処理（明細休暇区分）
    getThenholidaykbn(response, value) {
      var res = response.data;
      if (res.result) {
        this.HoliDayList = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("エラー", res.messagedata, "error", true, false, true);
        } else {
          this.serverCatch("休暇区分", "取得");
        }
      }
    },
    inputClear() {
      this.details = [];
    },
    // 集計パネルヘッダ文字列編集処理
    setPanelHeader: function() {
      moment.locale("ja");
      var datejaFormat = "";
      if (this.yearinit == null || this.yearinit == "") {
        this.stringtext = "";
      } else {
        if (this.selectedC024Value == null || this.selectedC024Value == "") {
          this.stringtext = "";
        } else {
          datejaFormat = moment(this.yearinit).format("YYYY年");
          if (this.selectedC024Value == "1") {
            this.stringtext =
              datejaFormat + "のカレンダーを期首月から１年分初期設定";
          } else {
            if (this.selectedC024Value == "2") {
              this.stringtext =
                datejaFormat + "のカレンダーを1月から１年分初期設定";
            } else {
              this.stringtext = "";
            }
          }
        }
      }
    }
  }
};
</script>
