<template>
  <div>
    <!-- ========================== 編集部 START ========================== -->
    <div class="card shadow-pl">
      <!-- panel header -->
      <daily-working-information-panel-header
        v-bind:header-text1="'◆勤怠編集'"
        v-bind:header-text2="''"
      ></daily-working-information-panel-header>
      <!-- /.panel header -->
      <!-- ----------- 「＋」アイコン部 START ---------------- -->
      <!-- panel header -->
      <div class="card-header bg-transparent pt-3 border-0">
        <h1 class="float-sm-left font-size-rg">
          <span>
            <button class="btn btn-success btn-lg font-size-rg"
            v-if="login_user_role === login_adminuser_role" v-on:click="appendRowClick">+</button>
          </span>
          {{ selectedName }}
        </h1>
        <span class="float-sm-right font-size-sm" v-if="login_user_role === login_adminuser_role">
          「＋」アイコンで新規に追加することができます</span>
      </div>
      <!-- /.panel header -->
      <!-- ----------- 「＋」アイコン部 END ---------------- -->
      <!-- ----------- 編集入力部 START ---------------- -->
      <!-- main contentns row -->
      <div class="card-body pt-2">
        <!-- ----------- 選択ボタン類 START ---------------- -->
        <!-- .row -->
        <div class="row justify-content-between">
          <!-- col -->
          <div class="col-md-3 pb-2">
            <btn-work-time
              v-on:gosubateclick-event="gosubateclick"
              v-bind:btn-mode="'gosubdate'"
              v-bind:is-push="isgosubdatebutton"
            ></btn-work-time>
          </div>
          <!-- /.col -->
          <!-- col -->
          <div class="col-md-3 pb-2">
            <btn-work-time
              v-on:goaddateclick-event="goaddateclick"
              v-bind:btn-mode="'goadddate'"
              v-bind:is-push="isgoadddatebutton"
            ></btn-work-time>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
        <!-- ----------- 選択ボタン類 END ---------------- -->
      </div>
      <div class="card-body pt-2" v-if="details.length">
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
        <!-- ----------- 項目部 START ---------------- -->
        <div class="col-md pt-3 align-self-stretch">
          <div class="card shadow-pl">
            <!-- panel body -->
            <div class="card-body mb-3 p-0 border-top">
              <!-- panel contents -->
              <div class="row">
                <div class="col-12">
                  <!-- ----------- 項目table部 START ---------------- -->
                  <div class="table-responsive">
                    <table class="table table-striped border-bottom font-size-sm text-nowrap">
                      <thead>
                        <tr>
                          <td class="text-center align-middle mw-rem-2">No.</td>
                          <td class="text-center align-middle mw-rem-20">打刻日付</td>
                          <td class="text-center align-middle mw-rem-10">勤怠モード</td>
                          <td class="text-center align-middle mw-rem-15">時刻</td>
                          <td class="text-center align-middle mw-rem-20">勤務区分</td>
                          <td class="text-center align-middle mw-rem-5">操作</td>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(item,index) in details" v-bind:key="item.id">
                          <td class="text-right align-middle mw-rem-2">
                            <label>{{ index+1 }}</label>
                          </td>
                          <td class="text-center align-middle mw-rem-20"
                            v-if="!details[index].record_ymd || valuesubadddate === details[index].record_ymd">
                            {{ details[index].record_date }}
                          </td>
                          <td class="text-center align-middle mw-rem-20 color-red" v-else>
                            {{ details[index].record_date }}で更新します。
                          </td>
                          <td class="text-center align-middle mw-rem-10">
                            <div class="input-group">
                              <select
                                class="custom-select"
                                v-model="details[index].mode"
                                @change="changeMode(index)"
                              >
                                <option value></option>
                                <option
                                  v-for="mlist in generalList_c005"
                                  :value="mlist.code"
                                  v-bind:key="mlist.code"
                                >{{ mlist.code_name }}</option>
                              </select>
                            </div>
                          </td>
                          <td class="text-center align-middle mw-rem-15">
                            <div class>
                              <input
                                type="time"
                                class="form-control"
                                v-model="details[index].time"
                                @change="changeTime(index)"
                              />
                            </div>
                          </td>
                          <td class="text-center align-middle mw-rem-20" v-if="index==0">
                            <div class="input-group">
                              <select
                                class="form-control"
                                v-model="details[index].user_holiday_kbn"
                                @change="changeHolidayKbn(index)"
                              >
                                <option value></option>
                                <option
                                  v-for="list in generalList_c013"
                                  :value="list.code"
                                >{{ list.code_name }}</option>
                              </select>
                            </div>
                          </td>
                          <td class="text-center align-middle mw-rem-20" v-else></td>
                          <td class="text-center align-middle mw-rem-5" v-if="login_user_role === login_adminuser_role">
                            <i class="fa fa-trash" style="color: #808080;ccursor: hand; cursor:pointer;" v-on:click="rowDelClick(index)">
                           </i>
                          </td>
                          <td class="text-center align-middle mw-rem-5" v-else></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <!-- ----------- 項目table部 START ---------------- -->
                </div>
              </div>
              <!-- /.row -->
              <!-- /.panel contents -->
            </div>
            <!-- /panel body -->
          </div>
        </div>
      </div>
      <!-- /main contentns row -->
      <!-- ----------- 編集入力部 END ---------------- -->
    </div>
    <!-- ----------- 項目部 END ---------------- -->
    <div class="card shadow-pl">
      <div class="card-body pt-2">
        <!-- ----------- 選択ボタン類 START ---------------- -->
        <!-- .row -->
        <div class="row justify-content-between">
          <!-- col -->
          <div class="col-md-12 pb-2" v-if="login_user_role === login_adminuser_role">
            <btn-work-time
              v-on:editfixclick-event="editfixclick"
              v-bind:btn-mode="'editfix'"
              v-bind:is-push="issearchbutton"
            ></btn-work-time>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
        <!-- /.panel contents -->
      </div>
      <!-- ----------- 選択ボタン類 END ---------------- -->
    </div>
    <!-- /.panel -->
  </div>
</template>
<script>
import Datepicker from "vuejs-datepicker";
import { ja } from "vuejs-datepicker/dist/locale";
import moment from "moment";
import encoding from 'encoding-japanese';
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

// 打刻モード
const ATTENDANCE = 1;
const LEAVING = 2;
const OFFICIAL_OUT_START = 11;
const OFFICIAL_OUT_END = 12;
const PRIVATE_OUT_START = 21;
const PRIVATE_OUT_END = 22;
// 休暇区別
const ALLDAY = 1;
const HALFDAY_AM = 2;
const HALFDAY_PM = 3;
const DEEMED = 4;
const ALLDAYCALC = 5;
const LATE = 6;
const EARLY = 7;
const ALLDAY_NAME = '1日休暇';
const HALFDAY_AM_NAME = '午前半休';
const HALFDAY_PM_NAME = '午後半休';
const DEEMED_NAME = 'みなし';
const DEEMED_BUSINESS_TRIP = 'みなし出張';
const DEEMED_DIRECT_GO = 'みなし直行';
const DEEMED_DIRECT_RETURN = 'みなし直帰';
const ALLDAY_CALC_NAME = '1日集計対象休暇';
const LATE_NAME = '遅刻';
const EARLY_NAME = '早退';

export default {
  name: "EditWorkTimesTable",
  mixins: [ dialogable, checkable, requestable ],
  props: {
    authusers: {
        type: Array,
        default: []
    },
    generaluser: {
        type: Number,
        default: 0
    },
    generalapproveruser: {
        type: Number,
        default: 0
    },
    adminuser: {
        type: Number,
        default: 0
    },
    heads: {
        type: Array,
        default: []
    }
  },
  data() {
    return {
      headsedt: [],
      edtUsercode: "",
      edtUsername: "",
      edtDatename: "",
      edtdepartmentcode: "",
      edtdepartmentname: "",
      login_user_code: "",
      login_user_role: "",
      login_generaluser_role: "",
      login_generalapproveruser_role: "",
      login_adminuser_role: "",
      messagevalidatesEdt: [],
      details: [],
      before_details: [],
      valuesubadddate: "",
      before_user_holiday_kbn: "",
      before_id: [],
      count: 0,
      before_count: 0,
      value_user_holiday_kbn: "",
      generalList_c005: [],
      generalList_c013: [],
      selectedName: "",
      issearchbutton: false,
      isgosubdatebutton: false,
      isgoadddatebutton: false,
      ja: ja,
      validate: false,
      dspdates: []
    };
  },
  components: {
    Datepicker
  },
  // マウント時
  mounted() {
    this.login_user_code = this.authusers['code'];
    this.login_user_role = this.authusers['role'];
    this.login_generaluser_role = this.generaluser;
    this.login_generalapproveruser_role = this.generalapproveruser;
    this.login_adminuser_role = this.adminuser;
    this.headsedt = this.heads;
    this.edtUsercode = this.headsedt['user_code'];
    this.edtUsername = this.headsedt['user_name'];
    this.edtdepartmentcode = this.headsedt['department_code'];
    this.edtdepartmentname = this.headsedt['department_name'];
    this.edtDatename = this.headsedt['record_date_name'];
    this.valuesubadddate = this.headsedt['current_record_date'];
    this.valuesubadddate = moment(this.valuesubadddate).format("YYYYMMDD");
    this.selectedName = this.edtUsername + "さん　" + moment(this.valuesubadddate).format("YYYY年MM月DD日") + "分勤怠編集" ;
    this.getWorkTime(this.valuesubadddate);
    this.getGeneralList("C005");
    this.getGeneralList("C013");
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション（更新）
    checkFormFix: function() {
      this.messagevalidatesEdt = [];
      var chkArray = [];
      var flag = true;
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '';
      var casename = '';
      var isrow = true;
      for (var index=0;index<this.details.length;index++) {
        // 勤怠モードと時刻勤務区分
        isrow = this.checkRowData(index);
        if (!isrow) {
          if (index == 0) {
            this.messagevalidatesEdt.push("No." + (index+1) + "の" + "勤怠モード・時刻または勤務区分を入力してください");
          } else {
            this.messagevalidatesEdt.push("No." + (index+1) + "の" + "勤怠モード・時刻を入力してください");
          }
        } else {
          if (this.details[index].user_holiday_kbn == "" || this.details[index].user_holiday_kbn == null) {
            // 勤怠モードと時刻
            if (this.details[index].mode != "" && this.details[index].mode != null) {
              required = true;
              equalength = 0;
              maxlength = 0;
              itemname = '時刻';
              chkArray = 
                this.checkDetail(this.details[index].time, required, equalength, maxlength, itemname, index+1) ;
              if (chkArray.length > 0) {
                if (this.messagevalidatesEdt.length == 0) {
                  this.messagevalidatesEdt = chkArray;
                } else {
                  this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
                }
              }
            } else {
              required = true;
              equalength = 0;
              maxlength = 0;
              itemname = '勤怠モード';
              chkArray =
                this.checkDetail(this.details[index].mode, required, equalength, maxlength, itemname, index+1);
              if (chkArray.length > 0) {
                if (this.messagevalidatesEdt.length == 0) {
                  this.messagevalidatesEdt = chkArray;
                } else {
                  this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
                }
              }
            }
          } else {
            var result = this.jdgeHolidayKbn(this.value_user_holiday_kbn);
            if (result == ALLDAY) {
              itemname = '勤怠モード';
              casename = '勤務区分入力の場合は';
              chkArray =
                this.checkDetailNotEnter(this.details[index].mode, itemname, casename, index+1);
              if (chkArray.length > 0) {
                if (this.messagevalidatesEdt.length == 0) {
                  this.messagevalidatesEdt = chkArray;
                } else {
                  this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
                }
              }
            } else if (result == HALFDAY_AM || result == HALFDAY_PM || result == LATE || result == EARLY) {
              // 勤怠モードと時刻
              if (this.details[index].mode != "" && this.details[index].mode != null) {
                required = true;
                equalength = 0;
                maxlength = 0;
                itemname = '時刻';
                chkArray = 
                  this.checkDetail(this.details[index].time, required, equalength, maxlength, itemname, index+1) ;
                if (chkArray.length > 0) {
                  if (this.messagevalidatesEdt.length == 0) {
                    this.messagevalidatesEdt = chkArray;
                  } else {
                    this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
                  }
                }
              } else {
                required = true;
                equalength = 0;
                maxlength = 0;
                itemname = '勤怠モード';
                chkArray =
                  this.checkDetail(this.details[index].mode, required, equalength, maxlength, itemname, index+1);
                if (chkArray.length > 0) {
                  if (this.messagevalidatesEdt.length == 0) {
                    this.messagevalidatesEdt = chkArray;
                  } else {
                    this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
                  }
                }
              }
            } else if (result == DEEMED) {
              // 勤怠モードと時刻
              if (this.details[index].mode != "" && this.details[index].mode != null) {
                required = true;
                equalength = 0;
                maxlength = 0;
                itemname = '時刻';
                chkArray = 
                  this.checkDetail(this.details[index].time, required, equalength, maxlength, itemname, index+1) ;
                if (chkArray.length > 0) {
                  if (this.messagevalidatesEdt.length == 0) {
                    this.messagevalidatesEdt = chkArray;
                  } else {
                    this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
                  }
                }
              } else {
                required = true;
                equalength = 0;
                maxlength = 0;
                itemname = '勤怠モード';
                chkArray =
                  this.checkDetail(this.details[index].mode, required, equalength, maxlength, itemname, index+1);
                if (chkArray.length > 0) {
                  if (this.messagevalidatesEdt.length == 0) {
                    this.messagevalidatesEdt = chkArray;
                  } else {
                    this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
                  }
                }
              }
            } else if (result == ALLDAYCALC) {
              // 勤怠モードと時刻
              if (this.details[index].mode != "" && this.details[index].mode != null) {
                chkArray.push("No." + (index+1) + "の勤務区分では" + "勤怠モードは入力できません");
              }
              if (this.details[index].time != "" && this.details[index].time != null) {
                chkArray.push("No." + (index+1) + "の勤務区分では" + "時刻は入力できません");
              }
              if (chkArray.length > 0) {
                if (this.messagevalidatesEdt.length == 0) {
                  this.messagevalidatesEdt = chkArray;
                } else {
                  this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
                }
              }
            }
          }
        }
      }
      if (this.messagevalidatesEdt.length > 0) {
        flag = false;
      }
      return flag;
    },
    // ------------------------ イベント処理 ------------------------------------
    // 勤怠モードが変更された場合の処理
    changeMode: function(index) {
      //
    },
    // 時間が変更された場合の処理
    changeTime: function(index) {
      //
    },
    // 休暇区分が変更された場合の処理
    changeHolidayKbn: function(index) {
      this.value_user_holiday_kbn = this.details[index].user_holiday_kbn;
      for (var i=0;i<this.details.length;i++) {
        this.details[i].kbn_flag = 0;
      }
      if (this.value_user_holiday_kbn != "" && this.value_user_holiday_kbn != null) {
        this.details[index].kbn_flag = 1;
        // 編集前の休暇区分と比較
        if(this.before_user_holiday_kbn != this.value_user_holiday_kbn) {
          var result = this.jdgeHolidayKbn(this.value_user_holiday_kbn);
          if (result == ALLDAY) {
            // 明細テーブル編集
            this.edtDetailes(this.details);
            this.count = this.details.length
            this.before_user_holiday_kbn = this.value_user_holiday_kbn;
          } else if (result == HALFDAY_AM) {
            this.getWorkinghour();
            this.before_user_holiday_kbn = this.value_user_holiday_kbn;
          } else if (result == HALFDAY_PM) {
            this.getWorkinghour();
            this.before_user_holiday_kbn = this.value_user_holiday_kbn;
          } else if (result == DEEMED) {
            // 該当日付の所定時刻を取得する（みなし）
            this.getWorkinghour();
            this.before_user_holiday_kbn = this.value_user_holiday_kbn;
          } else if (result == ALLDAYCALC) {
            this.edtDetailes(this.details);
            this.count = this.details.length
            this.before_user_holiday_kbn = this.value_user_holiday_kbn;
          }
        }        
      }
    },
    //前日ボタンクリック処理
    gosubateclick() {
      // 入力項目クリア
      this.inputClear();
      this.messagevalidatesEdt = [];
      this.valuesubadddate = moment(this.valuesubadddate).subtract(1, 'days').format("YYYYMMDD");
      this.selectedName = this.edtUsername + "　" + moment(this.valuesubadddate).format("YYYY年MM月DD日") + "分勤怠編集" ;
      this.getWorkTime(this.valuesubadddate);
    },
    //翌日ボタンクリック処理
    goaddateclick() {
      // 入力項目クリア
      this.inputClear();
      this.messagevalidatesEdt = [];
      this.valuesubadddate = moment(this.valuesubadddate).add(1, 'days').format("YYYYMMDD");
      this.selectedName = this.edtUsername + "　" + moment(this.valuesubadddate).format("YYYY年MM月DD日") + "分勤怠編集" ;
      this.getWorkTime(this.valuesubadddate);
    },
    // プラス追加ボタンクリック処理
    appendRowClick: function() {
      this.messagevalidatesEdt = [];
      // if (this.before_count < this.count) {
      //   var messages = [];
      //   messages.push("１度に追加できる情報は１個です。");
      //   messages.push("追加してから再実行してください。");
      //   this.htmlMessageSwal("エラー", messages, "error", true, false);
      // } else {
        var arrayobject = [];
        arrayobject = { 
            id : "",
            user_code : this.edtUsercode,
            department_code : this.edtdepartmentcode,
            record_time : "",
            mode : "",
            user_holiday_kubuns_id : "",
            user_name : this.edtUsername,
            department_name : this.edtdepartmentname,
            code_name : "",
            user_holiday_kbn : "",
            record_ymd : moment(this.valuesubadddate).format("YYYYMMDD"),
            record_date : moment(this.valuesubadddate).format("YYYY年MM月DD日"),
            date : moment(this.valuesubadddate).format("YYYY/MM/DD"),
            time : "",
            x_positions : "",
            y_positions : "",
            kbn_flag : 0
          };
        this.details.push(arrayobject);
        this.count = this.details.length
      // }
    },
    // 更新確定ボタンクリック処理
    editfixclick() {
      this.messagevalidatesEdt = [];
      var flag = this.checkFormFix();
      if (flag) {
        var messages = [];
        if (this.details.length > 0) {
          messages.push("この内容で更新しますか？");
        } else {
          messages.push("明細がないため削除されますがよろしいですか？");
        }
        this.htmlMessageSwal("確認", messages, "info", true, true)
          .then(result  => {
            if (result) {
              this.FixData("更新");
            }
        });
      // 項目数が多い場合以下コメントアウト
      /* } else {
        this.countswal("エラー", this.messagevalidatesEdt, "error", true, false, true)
          .then(result  => {
            if (result) {
            }
        }); */
      }
    },
    // 行削除ボタンクリック処理
    rowDelClick: function(index) {
      this.messagevalidatesEdt = [];
      if (this.checkRowData(index)) {
        var messages = [];
        messages.push("行削除してよろしいですか？");
        this.htmlMessageSwal("確認", messages, "info", true, true)
          .then(result  => {
            if (result) {
              this.details.splice(index, 1);
              this.count = this.details.length
            }
        });
      } else {
        this.details.splice(index, 1);
        this.count = this.details.length
      }
    },
    // -------------------- サーバー処理 ----------------------------
    // 勤怠取得処理
    getWorkTime(datevalue) {
      this.postRequest("/edit_work_times/get",
        { ymd : datevalue, code : this.edtUsercode})
        .then(response  => {
          this.getThenWorkTime(response);
        })
        .catch(reason => {
          this.serverCatch("勤怠時間","取得");
        });
    },
    // コード選択リスト取得処理
    getGeneralList(value) {
      var arrayParams = { identificationid : value };
      this.postRequest("/get_general_list", arrayParams)
        .then(response  => {
          if (value == "C005") {
            this.getThenc005(response, "勤怠モード");
          }
          if (value == "C013") {
            this.getThenc013(response, "休暇区分");
          }
        })
        .catch(reason => {
          if (value == "C005") {
            this.serverCatch("勤怠モード", "取得");
          }
          if (value == "C013") {
            this.serverCatch("休暇区分", "取得");
          }
        });
    },
    // 勤怠更新確定処理（明細）
    FixData(kbnname) {
      var messages = [];
      var arrayParams = {
        user_code : this.edtUsercode,
        target_date : this.valuesubadddate,
        details : this.details,
        beforeids : this.before_id
      };
      this.postRequest("/edit_work_times/fixtime", arrayParams)
        .then(response  => {
          this.putThenDetail(response, kbnname);
        })
        .catch(reason => {
          this.serverCatch("勤怠編集", kbnname);
        });
    },
    // 所定時刻取得処理
    getWorkinghour() {
      this.postRequest("/get_working_hours",
        { target_date : this.valuesubadddate,
          department_code : this.edtdepartmentcode,
          user_code : this.edtUsercode})
        .then(response  => {
          this.getThenWorkinghour(response);
        })
        .catch(reason => {
          this.serverCatch("所定時刻","取得");
        });
    },
    

    // -------------------- 共通 ----------------------------
    // 取得正常処理（勤怠モード選択リスト）
    getThenc005(response, value) {
      var res = response.data;
      if (res.result) {
        this.generalList_c005 = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("勤怠モード選択リスト", "取得");
        }
      }
    },
    // 取得正常処理（休暇区分選択リスト）
    getThenc013(response, value) {
      var res = response.data;
      if (res.result) {
        this.generalList_c013 = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("明細勤怠休暇区分選択リスト管理", "取得");
        }
      }
    },
    // 更新系正常処理（明細）
    putThenDetail(response, eventtext) {
      var res = response.data;
      if (res.result) {
        this.$toasted.show('勤怠編集を' + eventtext + 'しました', this.$toasted.options);
        this.getWorkTime(this.valuesubadddate);
        this.count = this.details.length;
        this.before_count = this.count;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch("勤怠編集", eventtext);
        }
      }
    },
    // 所定時刻取得正常処理
    getThenWorkinghour(response) {
      var res = response.data;
      var arrayobject = [];
      if (res.result) {
        // 明細テーブル編集
        if (Object.keys(res.details).length > 0) {
          this.edtDetailes(res.details);
          this.count = this.details.length
        }
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("所定時刻", "取得");
        }
      }
    },
    // 取得正常処理
    getThenWorkTime(response) {
      this.details = [];
      this.before_user_holiday_kbn = null;
      this.before_id = [];
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        this.before_details = this.details;
        for(var i=0;i<this.details.length;i++) {
          if (i==0) {
            this.value_user_holiday_kbn = this.details[i].user_holiday_kbn;
            this.before_user_holiday_kbn = this.details[i].user_holiday_kbn;
          }
          this.before_id[i] = this.details[i].id;
          if (this.details[i].time == "00:00") {
            this.details[i].time = null;
          }
          this.dspdates[i] = this.details[i].record_date;
        }
        this.count = this.details.length;
        this.before_count = this.count;
        if (res.details.length == 0) {
          var messages = [];
          messages.push("勤怠データありませんでした。");
          messages.push("プラスアイコンで追加できます。");
          this.htmlMessageSwal("確認", messages, "info", true, false);
        }
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("勤怠編集", "取得");
        }
      }
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    inputClear() {
      this.details = [];
      this.count = 0;
      this.before_count = 0;
      this.value_user_holiday_kbn = "";
    },
    checkRowData(index) {
      if (this.details[index].mode != "" && this.details[index].mode != null) { return true; }
      if (this.details[index].time != "" && this.details[index].time != null) { return true; }
      if (this.details[index].user_holiday_kbn != "" && this.details[index].user_holiday_kbn != null) { return true; }
      return false;
    },
    // 休暇区分判定 みなしは休暇ではないのでfalse
    jdgeHolidayKbn: function(holiday_kbn) {
      for (var i=0;i<this.generalList_c013.length;i++) {
        if (holiday_kbn == this.generalList_c013[i].code) {
          if (this.generalList_c013[i].description == ALLDAY_NAME) {
            return ALLDAY;
          } else if (this.generalList_c013[i].description == HALFDAY_AM_NAME) {
            return HALFDAY_AM;
          } else if (this.generalList_c013[i].description == HALFDAY_PM_NAME) {
            return HALFDAY_PM;
          } else if (this.generalList_c013[i].description == DEEMED_NAME) {
            return DEEMED;
          } else if (this.generalList_c013[i].description == ALLDAY_CALC_NAME) {
            return ALLDAYCALC;
          } else if (this.generalList_c013[i].description == LATE_NAME) {
            return LATE;
          } else if (this.generalList_c013[i].description == EARLY_NAME) {
            return EARLY;
          }
        }
      }
      return 0;
    },
    // 明細テーブル編集
    edtDetailes: function(edtdetail) {
      this.generalList_c013.forEach(item => {
        if (this.value_user_holiday_kbn == item.code) {
          var old_details = this.before_details;
          this.details = [];
          // 1日休暇
          if (item.description == ALLDAY_NAME) {
            this.details.push(this.edtDetailesAllDay());
          // 午前半休
          } else if (item.description == HALFDAY_AM_NAME) {
            // 出勤自動設定
            this.details.push(this.edtDetailesBreakAttendance(edtdetail));
            // if (this.before_count > 0) {
            //   // 旧detailsから出勤以外を設定
            //   old_details.forEach(detail => {
            //     if (detail.mode != "" && detail.mode != null && detail.mode != ATTENDANCE) {
            //       this.details.push(this.edtDetailesfromOld(detail));
            //     }
            //   });
            // }
          // 午後半休
          } else if (item.code_name == HALFDAY_PM_NAME) {
            // if (this.before_count > 0) {
            //   // 旧detailsから出勤以外を設定
            //   old_details.forEach(detail => {
            //     if (detail.mode != "" && detail.mode != null && detail.mode != LEAVING) {
            //       this.details.push(this.edtDetailesfromOld(detail));
            //     }
            //   });
            // }
            // 退勤自動設定
            this.details.push(this.edtDetailesBreakLeaving(edtdetail));
          // みなし
          } else if (item.code_name == DEEMED_BUSINESS_TRIP) {
            // 出勤自動設定
            this.details.push(this.edtDetailesAttendance(edtdetail));
            // 退勤自動設定
            this.details.push(this.edtDetailesLeaving(edtdetail));
          } else if (item.code_name == DEEMED_DIRECT_GO) {
            // 出勤自動設定
            this.details.push(this.edtDetailesAttendance(edtdetail));
            // if (this.before_count > 0) {
            //   // 旧detailsから出勤以外を設定
            //   old_details.forEach(detail => {
            //     if (detail.mode != "" && detail.mode != null && detail.mode != ATTENDANCE) {
            //       this.details.push(this.edtDetailesfromOld(detail));
            //     }
            //   });
            // }
          } else if (item.code_name == DEEMED_DIRECT_RETURN) {
            // if (this.before_count > 0) {
            //   // 旧detailsから退勤以外を設定
            //   old_details.forEach(detail => {
            //     if (detail.mode != "" && detail.mode != null && detail.mode != LEAVING) {
            //       this.details.push(this.edtDetailesfromOld(detail));
            //     }
            //   });
            // }
            // 退勤自動設定
            this.details.push(this.edtDetailesLeaving(edtdetail));
          // 1日集計対象休暇
          } else if (item.description == ALLDAY_CALC_NAME) {
            this.details.push(this.edtDetailesAllDay());
          }
        }
      });
    },
    // 明細テーブル編集（1日休暇）
    edtDetailesAllDay: function() {
      // 時刻をクリア
      var arrayobject = {
        id : "",
        user_code : this.edtUsercode,
        department_code : this.edtdepartmentcode,
        record_time : "",
        mode : "",
        user_holiday_kubuns_id : "",
        user_name : this.edtUsername,
        department_name : this.edtdepartmentname,
        code_name : "",
        user_holiday_kbn : this.value_user_holiday_kbn,
        record_ymd : moment(this.valuesubadddate).format("YYYYMMDD"),
        record_date : moment(this.valuesubadddate).format("YYYY年MM月DD日"),
        date : moment(this.valuesubadddate).format("YYYY/MM/DD"),
        time : "",
        x_positions : "",
        y_positions : "",
        kbn_flag : 1
      };
      return arrayobject;
    },
    // 明細テーブル編集（1日集計対象休暇）
    edtDetailesAllDayCalc: function() {
      // 時刻をクリア
      var arrayobject = {
        id : "",
        user_code : this.edtUsercode,
        department_code : this.edtdepartmentcode,
        record_time : "",
        mode : "",
        user_holiday_kubuns_id : "",
        user_name : this.edtUsername,
        department_name : this.edtdepartmentname,
        code_name : "",
        user_holiday_kbn : this.value_user_holiday_kbn,
        record_ymd : moment(this.valuesubadddate).format("YYYYMMDD"),
        record_date : moment(this.valuesubadddate).format("YYYY年MM月DD日"),
        date : moment(this.valuesubadddate).format("YYYY/MM/DD"),
        time : "",
        x_positions : "",
        y_positions : "",
        kbn_flag : 1
      };
      return arrayobject;
    },
    // 明細テーブル編集（出勤）
    edtDetailesAttendance: function(detail) {
      // 出勤を自動設定
      var arrayobject = {
        id : "",
        user_code : this.edtUsercode,
        department_code : this.edtdepartmentcode,
        record_time : detail.regular_start_recordtime,
        mode : ATTENDANCE,
        user_holiday_kubuns_id : "",
        user_name : this.edtUsername,
        department_name : this.edtdepartmentname,
        code_name : "",
        user_holiday_kbn : this.value_user_holiday_kbn,
        record_ymd : moment(detail.regular_start_recordtime).format("YYYYMMDD"),
        record_date : moment(detail.regular_start_recordtime).format("YYYY年MM月DD日"),
        date : moment(detail.regular_start_recordtime).format("YYYY/MM/DD"),
        time : detail.regular_start_time,
        x_positions : "",
        y_positions : "",
        kbn_flag : 1
      };
      return arrayobject;
    },
    // 明細テーブル編集（午前半休）
    edtDetailesBreakAttendance: function(detail) {
      // 出勤を自動設定
      var arrayobject = {
        id : "",
        user_code : this.edtUsercode,
        department_code : this.edtdepartmentcode,
        record_time : detail.lunch_end_recordtime,
        mode : ATTENDANCE,
        user_holiday_kubuns_id : "",
        user_name : this.edtUsername,
        department_name : this.edtdepartmentname,
        code_name : "",
        user_holiday_kbn : this.value_user_holiday_kbn,
        record_ymd : moment(detail.lunch_end_recordtime).format("YYYYMMDD"),
        record_date : moment(detail.lunch_end_recordtime).format("YYYY年MM月DD日"),
        date : moment(detail.lunch_end_recordtime).format("YYYY/MM/DD"),
        time : detail.lunch_end_time,
        x_positions : "",
        y_positions : "",
        kbn_flag : 1
      };
      return arrayobject;
    },
    // 明細テーブル編集（退勤）
    edtDetailesLeaving: function(detail) {
      // 出勤を自動設定
      var arrayobject = {
        id : "",
        user_code : this.edtUsercode,
        department_code : this.edtdepartmentcode,
        record_time : detail.regular_end_recordtime,
        mode : LEAVING,
        user_holiday_kubuns_id : "",
        user_name : this.edtUsername,
        department_name : this.edtdepartmentname,
        code_name : "",
        user_holiday_kbn : this.value_user_holiday_kbn,
        record_ymd : moment(detail.regular_end_recordtime).format("YYYYMMDD"),
        record_date : moment(detail.regular_end_recordtime).format("YYYY年MM月DD日"),
        date : moment(detail.regular_end_recordtime).format("YYYY/MM/DD"),
        time : detail.regular_end_time,
        x_positions : "",
        y_positions : "",
        kbn_flag : 1
      };
      return arrayobject;
    },
    // 明細テーブル編集（午後半休）
    edtDetailesBreakLeaving: function(detail) {
      // 出勤を自動設定
      var arrayobject = {
        id : "",
        user_code : this.edtUsercode,
        department_code : this.edtdepartmentcode,
        record_time : detail.lunch_start_recordtime,
        mode : LEAVING,
        user_holiday_kubuns_id : "",
        user_name : this.edtUsername,
        department_name : this.edtdepartmentname,
        code_name : "",
        user_holiday_kbn : this.value_user_holiday_kbn,
        record_ymd : moment(detail.lunch_start_recordtime).format("YYYYMMDD"),
        record_date : moment(detail.lunch_start_recordtime).format("YYYY年MM月DD日"),
        date : moment(detail.lunch_start_recordtime).format("YYYY/MM/DD"),
        time : detail.lunch_start_time,
        x_positions : "",
        y_positions : "",
        kbn_flag : 1
      };
      return arrayobject;
    },
    // 明細テーブル編集（旧）
    edtDetailesfromOld: function(detail) {
      // user_holiday_kbnは現在に置き換え
      var arrayobject = {
        id : detail.id,
        user_code : detail.user_code,
        department_code : detail.department_code,
        record_time : detail.record_time,
        mode : detail.mode,
        user_holiday_kubuns_id : "",
        user_name : detail.user_name,
        department_name : detail.department_name,
        code_name : detail.code_name,
        user_holiday_kbn : this.value_user_holiday_kbn,
        record_ymd : moment(detail.record_time).format("YYYYMMDD"),
        record_date : detail.record_date,
        date : detail.date,
        time : detail.time,
        x_positions : detail.x_positions,
        y_positions : detail.y_positions,
        kbn_flag : detail.kbn_flag
      };
      return arrayobject;
    },
    // 明細テーブル編集（空）
    edtDetailesDummy: function(mode) {
      // 出勤を自動設定
      var arrayobject = {
        id : "",
        user_code : this.edtUsercode,
        department_code : this.edtdepartmentcode,
        record_time : "",
        mode : mode,
        user_holiday_kubuns_id : "",
        user_name : this.edtUsername,
        department_name : this.edtdepartmentname,
        code_name : "",
        user_holiday_kbn : this.value_user_holiday_kbn,
        record_ymd : moment(this.valuesubadddate).format("YYYYMMDD"),
        record_date : moment(this.valuesubadddate).format("YYYY年MM月DD日"),
        date : moment(this.valuesubadddate).format("YYYY/MM/DD"),
        time : detail.working_timetable_from_time,
        x_positions : "",
        y_positions : "",
        kbn_flag : 1
      };
      return arrayobject;
    },
  }
};
</script>
<style scoped>

thead, tbody {
  display: block !important;
}

tbody {
  overflow-x: hidden !important;
  overflow-y: scroll !important;
}

.table th, .table td {
    padding: 0rem !important;
    border-style: solid dashed !important;
    border-width: 1px !important;
    border-color: #95c5ed #dee2e6 !important;
}

.mw-rem-2 {
  min-width: 2rem;
}

.mw-rem-8 {
  min-width: 8rem;
}

.mw-rem-12 {
  min-width: 12rem;
}

</style>
