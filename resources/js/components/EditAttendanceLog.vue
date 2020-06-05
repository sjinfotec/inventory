<template>
  <div>
	  <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- ========================== 検索部 START ========================== -->
      <!-- .panel -->
      <div class="col-md pt-3  print-none">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'勤怠ログを編集します。'"
            v-bind:header-text2="'差異時間を指定ない場合はログがないデータも対象となります。'"
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
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      id="basic-addon1"
                    >開始日付<span class="color-red">[必須]</span></span>
                  </div>
                  <input-datepicker
                    v-bind:default-date="selectFromdateValue"
                    v-bind:date-format="DatePickerFormat"
                    v-bind:place-holder="'開始日付を選択してください'"
                    v-on:change-event="fromdateChanges"
                    v-on:clear-event="fromdateCleared"
                  ></input-datepicker>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      id="basic-addon1"
                    >終了日付<span class="color-red">[必須]</span></span>
                  </div>
                  <input-datepicker
                    v-bind:default-date="selectTodateValue"
                    v-bind:date-format="DatePickerFormat"
                    v-bind:place-holder="'終了日付を選択してください'"
                    v-on:change-event="todateChanges"
                    v-on:clear-event="todateCleared"
                  ></input-datepicker>
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
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputGroupSelect01"
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
                      for="inputGroupSelect01"
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
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputGroupSelect01"
                    >氏 名</label>
                  </div>
                  <select-userlist v-if="showuserlist"
                    ref="selectuserlist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'氏名を選択してください'"
                    v-bind:selected-value="selectedUserValue"
                    v-bind:add-new="false"
                    v-bind:get-do="'1'"
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
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      id="basic-addon1"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="'指定時刻以上の差異があるログを対象とする。'"
                    >差異時間（分）</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="number"
                      name="differencetime"
                      title="指定時刻以上の差異があるログを対象とする。"
                      max="60"
                      min="1"
                      step="1"
                      class="form-control"
                      v-model="differencetime"
                    />
                  </div>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- 選択リスト END ---------------- -->
            <!-- ----------- メッセージ部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between  print-none" v-if="messagevalidatesSearch.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li v-for="(messagedata,index) in messagevalidatesSearch" v-bind:key="index">{{ messagedata }}</li>
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
            <!-- /.panel contents -->
            <!-- ----------- 選択ボタン類 END ---------------- -->
            <!-- /.panel contents -->
          </div>
        </div>
      </div>
      <!-- /.panel -->
      <!-- ========================== 編集部 START ========================== -->
      <!-- .panel -->
      <div class="col-md-12 pt-3" v-if="selectMode=='EDT'">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆勤怠ログ編集'"
            v-bind:header-text2="'PC起動時刻・PC終了時刻がない場合は差異の理由入力不可です。'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- ----------- 編集入力部 START ---------------- -->
          <!-- main contentns row -->
          <div class="card-body pt-2" v-if="details.length">
            <!-- panel contents -->
            <!-- ----------- メッセージ部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between  print-none" v-if="messagevalidatesEdt.length">
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
            <div
              v-for="(detail,index) in details"
              :key="detail.user_code"
            >
              <!-- .row -->
              <div class="row justify-content-between  pt-3 print-none">
                <!-- col -->
                <div class="col-sm-6 col-md-6 col-lg-6 pb-2 align-self-stretch">
                  <h1 class="font-size-sm m-0 mb-1">氏名</h1>
                  <p class="font-size-rg font-weight-bold m-0">{{ detail.user_name }}</p>
                </div>
                <!-- /.col -->
                <!-- col -->
                <div class="col-sm-6 col-md-6 col-lg-6 pb-2 align-self-stretch">
                  <h1 class="font-size-sm m-0 mb-1 text-sm-right">所属部署</h1>
                  <p class="font-size-rg m-0 text-sm-right">{{ detail.department_name }}</p>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <!-- .row -->
              <div class="row">
                <div class="col-12">
                  <!-- ----------- 項目table部 START ---------------- -->
                  <div class="table-responsive">
                    <table class="table table-striped border-bottom font-size-sm text-nowrap">
                      <thead>
                        <tr>
                          <td class="text-center align-middle w-10">日付</td>
                          <td class="text-center align-middle w-10">出勤時刻</td>
                          <td class="text-center align-middle w-10">退勤時刻</td>
                          <td class="text-center align-middle w-10">PC起動時刻</td>
                          <td class="text-center align-middle w-10">PC終了時刻</td>
                          <td class="text-center align-middle mw-rem-50">差異の理由</td>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(detaildate,index) in detail.date" v-bind:key="detaildate.working_date">
                          <td
                            class="text-center align-middle"
                          >{{ detaildate.working_date_name }}</td>
                          <td
                            v-if="detaildate.attendance_time != '00:00' && detaildate.attendance_time != '' && detaildate.attendance_time != null && detaildate.red_result_start != 1"
                            class="text-center align-middle"
                            >{{ detaildate.attendance_time }}</td>
                          <td
                            v-if="detaildate.attendance_time != '00:00' && detaildate.attendance_time != '' && detaildate.attendance_time != null && detaildate.red_result_start == 1"
                            class="text-center align-middle time-color-red"
                            >{{ detaildate.attendance_time }}</td>
                          <td
                            v-if="detaildate.attendance_time == '00:00' || detaildate.attendance_time == '' || detaildate.attendance_time == null"
                            class="text-center align-middle"
                            ></td>
                          <td
                            v-if="detaildate.leaving_time != '00:00' && detaildate.leaving_time != '' && detaildate.leaving_time != null && detaildate.red_result_end != 1"
                            class="text-center align-middle"
                            >{{ detaildate.leaving_time }}</td>
                          <td
                            v-if="detaildate.leaving_time != '00:00' && detaildate.leaving_time != '' && detaildate.leaving_time != null && detaildate.red_result_end == 1"
                            class="text-center align-middle time-color-red"
                            >{{ detaildate.leaving_time }}</td>
                          <td
                            v-if="detaildate.leaving_time == '00:00' || detaildate.leaving_time == '' || detaildate.leaving_time == null"
                            class="text-center align-middle"
                            ></td>
                          <td
                            v-if="detaildate.pcstart_time != '00:00' && detaildate.pcstart_time != '' && detaildate.pcstart_time != null && detaildate.red_result_start != 1"
                            class="text-center align-middle"
                            >{{ detaildate.pcstart_time }}</td>
                          <td
                            v-if="detaildate.pcstart_time != '00:00' && detaildate.pcstart_time != '' && detaildate.pcstart_time != null && detaildate.red_result_start == 1"
                            class="text-center align-middle time-color-red"
                            >{{ detaildate.pcstart_time }}</td>
                          <td
                            v-if="detaildate.pcstart_time == '00:00' || detaildate.pcstart_time == '' || detaildate.pcstart_time == null"
                            class="text-center align-middle"
                            ></td>
                          <td
                            v-if="detaildate.pcend_time != '00:00' && detaildate.pcend_time != '' && detaildate.pcend_time != null && detaildate.red_result_end != 1"
                            class="text-center align-middle"
                            >{{ detaildate.pcend_time }}</td>
                          <td
                            v-if="detaildate.pcend_time != '00:00' && detaildate.pcend_time != '' && detaildate.pcend_time != null && detaildate.red_result_end == 1"
                            class="text-center align-middle time-color-red"
                            >{{ detaildate.pcend_time }}</td>
                          <td
                            v-if="detaildate.pcend_time == '00:00' || detaildate.pcend_time == '' || detaildate.pcend_time == null"
                            class="text-center align-middle"
                            ></td>
                          <td class="text-center align-middle"
                            v-if="(detaildate.pcstart_time != '00:00' && detaildate.pcstart_time != '' && detaildate.pcstart_time != null) || (detaildate.pcend_time != '00:00' && detaildate.pcend_time != '' && detaildate.pcend_time != null)"
                          >
                            <div class="input-group">
                              <input
                                type="text"
                                class="form-control"
                                maxlength="255"
                                v-model="detaildate.difference_reason"
                                data-toggle="tooltip"
                                data-placement="top"
                                v-bind:title="'差異の理由を255文字以内で入力します'"
                                name="difference_reason"
                              />
                            </div>
                          </td>
                          <td class="text-center align-middle"
                            v-else
                          >
                            <div class="input-group">
                              <input
                                type="text"
                                class="form-control"
                                maxlength="255"
                                v-model="detaildate.difference_reason"
                                data-toggle="tooltip"
                                data-placement="top"
                                v-bind:title="'差異の理由を255文字以内で入力します'"
                                name="difference_reason"
                                disabled
                              />
                            </div>
                          </td>
                        </tr>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <!-- /.row -->
            </div>
            <!-- ----------- ボタン部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between print-none">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:reasonstoreclick-event="reasonstoreClick"
                  v-bind:btn-mode="'reasonstore'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.row -->
            <div class="row justify-content-between print-none">
              <div class="col-md-12 pb-2">
                <!-- col -->
                <btn-csv-download
                  v-bind:btn-mode="'csvlog'"
                  v-bind:csv-data="details"
                  v-bind:is-csvbutton="iscsvbutton"
                  v-bind:csv-date="stringtext"
                >
                </btn-csv-download>
                <!-- /.col -->
              </div>
            </div>
            <!-- /.row -->
            <!-- /panel body -->
          </div>
        </div>
      </div>
      <!-- /.panel -->
    </div>
    <!-- /main contentns row -->
  </div>
</template>
<script>
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

// CONST
const CONST_C025 = 'C025';


export default {
  name: "monthlyworkingtime",
  mixins: [ dialogable, checkable, requestable ],
  props: {
    authusers: {
        type: Array,
        default: []
    },
    const_generaldatas: {
        type: Array,
        default: []
    }
  },
  data: function() {
    return {
      selectFromdateValue: "",
      selectTodateValue: "",
      messagevalidatesSearch: [],
      messagevalidatesEdt: [],
      stringtext: "",
      DatePickerFormat: "yyyy年MM月dd日",
      selectedEmploymentValue : "",
      selectedDepartmentValue : "",
      valueDepartmentkillcheck : false,
      selectedUserValue : "",
      showuserlist: true,
      valueUserkillcheck : false,
      differencetime: "",
      valuefromdate: "",
      valuetodate: "",
      details: [],
      selectMode: "",
      iscsvbutton: true,
      datejaFormat: "",
      target_user_name: "",
      login_user_code: "",
      login_user_role: 1,
      getDo: 1,
      applytermdate: "",
      issearchbutton: false,
      defaultDate: new Date(),
      const_C025_data: []
    };
  },
  // マウント時
  mounted() {
    // メソッドで使用するのでcomputedでなくてOK
    var i = 0;
    let $this = this;
    this.const_generaldatas.forEach( function( item ) {
      if (item.identification_id == CONST_C025) {
        $this.const_C025_data.push($this.const_generaldatas[i]);
      }
      i++;
    });
    this.selectMode = "";
    this.selectFromdateValue = this.defaultDate;
    this.selectTodateValue = this.defaultDate;
    this.valuefromdate = this.defaultDate;
    this.valuetodate = this.defaultDate;
    this.applytermdate = ""
    if (this.valuefromdate) {
      this.applytermdate = moment(this.valuefromdate).format("YYYYMMDD");
    }
    this.$refs.selectdepartmentlist.getList(this.applytermdate);
    this.getUserSelected();
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    checkFormSearch: function(e) {
      this.messagevalidatesSearch = [];
      var chkArray = [];
      var flag = true;
      // 開始日付
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = "開始日付";
      chkArray = this.checkHeader(
        this.selectFromdateValue,
        required,
        equalength,
        maxlength,
        itemname
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesSearch.length == 0) {
          this.messagevalidatesSearch = chkArray;
        } else {
          this.messagevalidatesSearch = this.messagevalidatesSearch.concat(chkArray);
        }
      }
      // 終了日付
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = "終了日付";
      chkArray = this.checkHeader(
        this.selectTodateValue,
        required,
        equalength,
        maxlength,
        itemname
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesSearch.length == 0) {
          this.messagevalidatesSearch = chkArray;
        } else {
          this.messagevalidatesSearch = this.messagevalidatesSearch.concat(chkArray);
        }
      }

      if (this.messagevalidatesSearch.length == 0) {
        if (this.selectFromdateValue > this.selectTodateValue) {
          var chkArray = [];
          chkArray.push("開始日付が終了日付より未来日となっています");
          this.messagevalidatesSearch = this.messagevalidatesSearch.concat(chkArray);
        }
      }

      // 所属部署
      if (this.authusers['role'] < this.const_C025_data[2]['code']) {
        required = true;
        equalength = 0;
        maxlength = 0;
        itemname = "所属部署";
        chkArray = this.checkHeader(
          this.selectedDepartmentValue,
          required,
          equalength,
          maxlength,
          itemname
        );
        if (chkArray.length > 0) {
          if (this.messagevalidatesSearch.length == 0) {
            this.messagevalidatesSearch.push(
              "一般ユーザーは所属部署は必ず入力してください。"
            );
          } else {
            this.messagevalidatesSearch = this.messagevalidatesSearch.concat(
              chkArray
            );
          }
          this.validate = false;
        }
        required = true;
        equalength = 0;
        maxlength = 0;
        itemname = "氏名";
        chkArray = this.checkHeader(
          this.selectedUserValue,
          required,
          equalength,
          maxlength,
          itemname
        );
        if (chkArray.length > 0) {
          if (this.messagevalidatesSearch.length == 0) {
            this.messagevalidatesSearch.push(
              "一般ユーザーは氏名は必ず入力してください。"
            );
          } else {
            this.messagevalidatesSearch = this.messagevalidatesSearch.concat(chkArray);
          }
          this.validate = false;
        }
      }

      if (this.messagevalidatesSearch.length > 0) {
        flag = false;
      }
      return flag;
    },
    // ------------------------ イベント処理 ------------------------------------

    // 開始日付が変更された場合の処理
    fromdateChanges: function(value) {
      this.selectFromdateValue = value;
      // パネルに表示
      this.setPanelHeader();
      // 再取得
      this.applytermdate = ""
      if (this.valuefromdate) {
          this.applytermdate = moment(this.valuefromdate).format("YYYYMMDD");
      }
      this.$refs.selectdepartmentlist.getList(this.applytermdate);
      this.getUserSelected();
    },
    // 開始日付がクリアされた場合の処理
    fromdateCleared: function() {
      this.selectFromdateValue = ""
      // パネルに表示
      this.setPanelHeader();
      this.applytermdate = "";
      this.valuefromdate = "";
    },
    // 終了日付が変更された場合の処理
    todateChanges: function(value) {
      this.selectTodateValue = value;
    },
    // 終了日付がクリアされた場合の処理
    todateCleared: function() {
      this.selectTodateValue = ""
      // パネルに表示
      this.setPanelHeader();
      this.valuetodate = "";
    },
    // 雇用形態選択が変更された場合の処理
    employmentChanges: function(value) {
      this.selectedEmploymentValue = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getUserSelected();
    },
    // 部署選択が変更された場合の処理
    departmentChanges: function(value, arrayitem) {
      this.selectedDepartmentValue = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getUserSelected();
    },
    // ユーザー選択が変更された場合の処理
    userChanges: function(value) {
      this.selectedUserValue = value;
    },
    
    // 表示ボタンがクリックされた場合の処理
    // searchclick: function(e) {
    //   // 入力項目クリア
    //   // リソースアクセスエラー
    //   this.$axios.get("file:///C:/TimeRecordForWin/log/winlog.txt").then(response => (this.items = response))
    // },
    
    // 表示ボタンがクリックされた場合の処理
    searchclick: function(e) {
      // 入力項目クリア
      this.inputClear();
      this.messagevalidatesSearch = [];
      this.messagevalidatesEdt = [];
      if (this.checkFormSearch()) {
        this.selectMode = 'EDT';
        this.getItem();
      }
    },
    // 登録ボタンがクリックされた場合の処理
    reasonstoreClick: function() {
      var messages = [];
      messages.push("この内容で登録しますか？");
      this.htmlMessageSwal("確認", messages, "info", true, true).then(
        result => {
          if (result) {
            this.FixDetail("登録");
          }
        }
      );
    },
    // ------------------------ サーバー処理 ----------------------------
    // 勤怠ログ取得処理
    getItem() {
      // 処理中メッセージ表示
      this.$swal({
        title: "処　理　中...",
        html: "",
        allowOutsideClick: false, //枠外をクリックしても画面を閉じない
        showConfirmButton: false,
        showCancelButton: true,
        onBeforeOpen: () => {
          this.$swal.showLoading();
          this.postRequest("/edit_attendancelog/get", {
            fromdate : moment(this.selectFromdateValue).format("YYYYMMDD"),
            todate : moment(this.selectTodateValue).format("YYYYMMDD"),
            employment_status : this.selectedEmploymentValue,
            department_code : this.selectedDepartmentValue,
            user_code : this.selectedUserValue,
            differencetime : this.differencetime
            })
            .then(response  => {
              this.$swal.close();
              this.getThen(response);
            })
            .catch(reason => {
              this.$swal.close();
              this.serverCatch("勤怠ログ","取得");
            });
        }
      });
    },
    // 勤怠更新処理
    FixDetail(kbnname) {
      var messages = [];
      var arrayParams = { details : this.details };
      this.postRequest("/edit_attendancelog/fix", arrayParams)
        .then(response  => {
          this.putThenDetail(response, kbnname);
        })
        .catch(reason => {
          this.serverCatch("勤怠ログ編集", kbnname);
        });
    },

    // ----------------- 共通メソッド ----------------------------------
    // ユーザー選択コンポーネント取得メソッド
    getUserSelected: function() {
      // 再取得
      this.applytermdate = ""
      if (this.valuefromdate) {
          this.applytermdate = moment(this.valuefromdate).format("YYYYMMDD");
      }
      this.$refs.selectuserlist.getList(
        this.applytermdate,
        this.valueUserkillcheck,
        this.getDo,
        this.selectedDepartmentValue,
        this.selectedEmploymentValue
      );
    },
    // 取得正常処理
    getThen(response) {
      this.iscsvbutton = true;
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        if (this.details.length > 0) {
          this.iscsvbutton = false;
          this.target_user_name = this.details[0].user_name;
          this.setPanelHeader();
        } else {
          var messages = [];
          messages.push("該当するデータはありませんでした。");
          this.htmlMessageSwal("確認", messages, "info", true, false);
        }
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("勤怠ログ", "取得");
        }
      }
    },
    // 更新系正常処理（明細）
    putThenDetail(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.$toasted.show("勤怠ログを" + eventtext + "しました");
        this.getItem();
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch("勤怠ログ", eventtext);
        }
      }
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + "情報" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    // クリアメソッド
    inputClear() {
      this.details = [];
    },
    // 集計パネルヘッダ文字列編集処理
    setPanelHeader: function() {
      this.stringtext = "社員名：" + this.target_user_name + ",";
      moment.locale("ja");
      if (this.selectFromdateValue == null || this.selectFromdateValue == "") {
        this.stringtext += "";
      } else {
        this.valuefromdate = this.selectFromdateValue;
        this.datejaFormat = moment(this.valuefromdate)
          .format("YYYY年MM月DD日");
        this.stringtext += this.datejaFormat;
        if (this.selectTodateValue == null || this.selectTodateValue == "") {
          this.stringtext += "";
        } else {
          this.valuetodate = this.selectTodateValue;
          this.datejaFormat = moment(this.valuetodate)
            .format("YYYY年MM月DD日");
          this.stringtext += " － " + this.datejaFormat;
        }
      }
    }
  }
};
</script>
<style lang="scss" scoped>
.svg_img {
  color: #dc143c;
  cursor: pointer;
}

.custom-bg-dark {
  background-color: #606266 !important;
  color: white !important;
}

.text-align-right {
  text-align: right;
}

.text-align-left {
  text-align: left !important;
}

.padding-dis {
  padding: 0.75rem 0rem !important;
}

.color-chartreuse {
  color: chartreuse;
}

table {
   border-collapse: collapse !important;
   border: 1px solid #95c5ed !important;
}

.table th, .table td {
    padding: 0rem !important;
    border-style: solid dashed !important;
    border-width: 1px !important;
    border-color: #95c5ed #dee2e6 !important;
}

.mw-rem-3 {
  min-width: 3rem;
}

.mw-rem-4 {
  min-width: 4rem;
}

.time-color-red { color: red; }

</style>
