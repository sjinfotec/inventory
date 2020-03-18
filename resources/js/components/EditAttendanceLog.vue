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
            v-bind:header-text1="'日付範囲を指定して勤怠ログの編集を行います'"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- ----------- メッセージ部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between" v-if="messagevalidatesSearch.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li v-for="(messagevalidate,index) in messagevalidatesSearch" v-bind:key="index">{{ messagevalidate }}</li>
                </ul>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- メッセージ部 END ---------------- -->
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
                <message-data v-bind:message-datas="messagedatasfromdate" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
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
                <message-data v-bind:message-datas="messagedatasfromdate" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- .row -->
            <!-- <div class="row justify-content-between"> -->
              <!-- .col -->
              <!-- <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >所属部署</span>
                  </div>
                  <select-departmentlist
                    v-if="showdepartmentlist"
                    ref="selectdepartmentlist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'部署を選択してください'"
                    v-bind:selected-department="selectedDepartmentValue"
                    v-bind:add-new="false"
                    v-bind:date-value="''"
                    v-bind:kill-value="valueDepartmentkillcheck"
                    v-bind:row-index="0"
                    v-on:change-event="departmentChanges"
                  ></select-departmentlist>
                </div>
              </div> -->
              <!-- /.col -->
              <!-- .col -->
              <!-- <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      for="target_users"
                    >氏 名</label>
                  </div>
                  <select-userlist
                    v-if="showuserlist"
                    ref="selectuserlist"
                    v-bind:blank-data="false"
                    v-bind:placeholder-data="'氏名を選択すると編集モードになります'"
                    v-bind:selected-value="selectedUserValue"
                    v-bind:add-new="true"
                    v-bind:get-do="getDo"
                    v-bind:date-value="applytermdate"
                    v-bind:kill-value="valueUserkillcheck"
                    v-bind:row-index="0"
                    v-bind:department-value="selectedDepartmentValue"
                    v-bind:employment-value="''"
                    v-bind:management-value="'99'"
                    v-on:change-event="userChanges"
                  ></select-userlist>
                </div>
              </div> -->
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- 選択リスト END ---------------- -->
            <!-- ----------- ファイル選択 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-6 pb-2">
                <input type="file" class="file_input" name="wmi" @change="onFileChange" accept="text/plain,text/csv" />
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- ファイル選択 END ---------------- -->
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
            v-bind:header-text2="''"
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
              <!-- row -->
              <div class="row">
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
                                  v-if="detaildate.attendance_time != '00:00' && detaildate.attendance_time != '' && detaildate.attendance_time != null"
                                  class="text-center align-middle"
                                  >{{ detaildate.attendance_time }}</td>
                                <td
                                  v-if="detaildate.attendance_time == '00:00' || detaildate.attendance_time == '' || detaildate.attendance_time == null"
                                  class="text-center align-middle"
                                  ></td>
                                <td
                                  v-if="detaildate.leaving_time != '00:00' && detaildate.leaving_time != '' && detaildate.leaving_time != null"
                                  class="text-center align-middle"
                                  >{{ detaildate.leaving_time }}</td>
                                <td
                                  v-if="detaildate.leaving_time == '00:00' || detaildate.leaving_time == '' || detaildate.leaving_time == null"
                                  class="text-center align-middle"
                                  ></td>
                                <td
                                  v-if="detaildate.pcstart_time != '00:00' && detaildate.pcstart_time != '' && detaildate.pcstart_time != null"
                                  class="text-center align-middle"
                                  >{{ detaildate.pcstart_time }}</td>
                                <td
                                  v-if="detaildate.pcstart_time == '00:00' || detaildate.pcstart_time == '' || detaildate.pcstart_time == null"
                                  class="text-center align-middle"
                                  ></td>
                                <td
                                  v-if="detaildate.pcend_time != '00:00' && detaildate.pcend_time != '' && detaildate.pcend_time != null"
                                  class="text-center align-middle"
                                  >{{ detaildate.pcend_time }}</td>
                                <td
                                  v-if="detaildate.pcend_time == '00:00' || detaildate.pcend_time == '' || detaildate.pcend_time == null"
                                  class="text-center align-middle"
                                  ></td>
                                <td class="text-center align-middle">
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
                              </tr>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- /.row -->
                </div>
              </div>
              <!-- /.panel contents -->
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
import toasted from "vue-toasted";
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "monthlyworkingtime",
  mixins: [ dialogable, checkable, requestable ],
  data: function() {
    return {
      selectFromdateValue: "",
      selectTodateValue: "",
      messagevalidatesSearch: [],
      messagevalidatesEdt: [],
      stringtext: "",
      stringtext2: "",
      DatePickerFormat: "yyyy年MM月dd日",
      selectedDepartmentValue : "",
      valueDepartmentkillcheck : false,
      showdepartmentlist: true,
      selectedUserValue : "",
      showuserlist: true,
      valueUserkillcheck : false,
      valuefromdate: "",
      valuetodate: "",
      details: [],
      eventlogs: [],
      selectMode: "",
      iscsvbutton: true,
      datejaFormat: "",
      headdata: "",
      target_user_name: "",


      company_name: "",
      selecteTallyvalue: "",
      getDo: 1,
      applytermdate: "",
      issearchbutton: false,

      userrole: "",
      defaultDate: new Date(),
      hrefindex: "",
      resresults: [],
      calcresults: [],
      sumresults: [],
      serchorupdate: "",
      messagedatasserver: [],
      messagedatasfromdate: [],
      messagedatastodate: [],
      messagedatadisplay: [],
      messagedatadepartment: [],
      messagedatauser: [],
      messageshowsearch: false,
      messageshowupdate: false,
      isupdatebutton: false,
      btnmodeswitch: "basicswitch",
      isswitchbutton: false,
      isswitchvisible: false,
      validate: false,
      initialized: false
    };
  },
  // マウント時
  mounted() {
    this.selectMode = "";
    this.selectFromdateValue = this.defaultDate;
    this.selectTodateValue = this.defaultDate;
    this.valuefromdate = this.defaultDate;
    this.valuetodate = this.defaultDate;
    this.getUserRole();
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
    // ファイル選択が変更された場合の処理
    onFileChange: function(e) {
      this.handleFileSelect(e)
    },
    
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
    // ログインユーザーの権限を取得
    getUserRole: function() {
      var arrayParams = [];
      this.postRequest("/get_login_user_role", arrayParams)
        .then(response  => {
          this.getThenrole(response);
        })
        .catch(reason => {
          this.serverCatch("ユーザー権限", "取得");
        });
    },
    // 勤怠ログ取得処理
    getItem() {
      this.postRequest("/edit_attendancelog/get", {
        fromdate : moment(this.selectFromdateValue).format("YYYYMMDD"),
        todate : moment(this.selectTodateValue).format("YYYYMMDD"),
        department_code : this.selectedDepartmentValue,
        user_code : this.selectedUserValue,
        eventlogs : this.eventlogs})
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("勤怠ログ","取得");
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
    // イベントログファイル操作
    handleFileSelect: function(e) {
      var file_data = e.target.files[0];
      // 読み込み
      var reader = new FileReader();
      // 読み込んだファイルの中身を取得する
      reader.readAsText( file_data );
      let $this = this;
      //ファイルの中身を取得後に処理を行う
      reader.addEventListener( 'load', function() {
        var array_linetext = reader.result.split('\r\n');
        var event_mode = "";
        var event_date = "";
        var event_time = "";
        var linetext = "";
        var array_object = [];
        for(var i=0; i < array_linetext.length; i++) {
          linetext = array_linetext[i];
          if (linetext.length >= 4) {
            event_mode = linetext.slice(0, 4);
          }
          if (linetext.length >= 15) {
            let str = linetext.slice(5, 15).split('/');
            event_date = str.join('');
          }
          if (linetext.length >= 23) {
            event_time = linetext.slice(16, 24);
            if (event_time.slice(1, 2) == ":") {
              event_time = "0" + event_time; 
              console.log('event_time = ' + event_time);
            }
          }
          array_object.push({
            event_mode: event_mode,
            event_date: event_date,
            event_time: event_time
          })
        }
        $this.eventlogs = array_object;
      });
    },
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
    // 取得正常処理（ユーザー権限）
    getThenrole(response) {
      var res = response.data;
      if (res.result) {
        this.userrole = res.role;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("ユーザー権限", "取得");
        }
      }
    },
    // 取得正常処理
    getThen(response) {
      this.iscsvbutton = true;
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        if (this.details.length > 0) {
          this.iscsvbutton = false;
          console.log('this.details[0].user_name = ' + this.details[0].user_name);
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
