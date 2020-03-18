<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between print-none">
      <!-- ========================== 検索部 START ========================== -->
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'開始日付から終了日付までの集計を表示する'"
            v-bind:header-text2="'雇用形態や所属部署でフィルタリングして表示できます'"
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
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      id="basic-addon1"
                    >開始日付<span class="color-red">[必須]</span></span>
                  </div>
                  <input-datepicker
                    v-bind:default-date="valuefromdate"
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
                    v-bind:default-date="valuetodate"
                    v-bind:date-format="DatePickerFormat"
                    v-bind:place-holder="'終了日付を選択してください'"
                    v-on:change-event="fromtoChanges"
                    v-on:clear-event="fromtoCleared"
                  ></input-datepicker>
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
                <message-data v-bind:message-datas="messagedatadepartment" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
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
                <message-data v-bind:message-datas="messagedatauser" v-bind:message-class="'warning'"></message-data>
              </div>
              <div class="col-md-6 pb-2">
                <message-data-server v-bind:message-datas="messagedatasserver" v-bind:message-class="'warning'"></message-data-server>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- 選択リスト END ---------------- -->
            <!-- ----------- ボタン部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:searchclick-event="searchclick"
                  v-bind:btn-mode="'search'"
                  v-bind:is-push="issearchbutton">
                </btn-work-time>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div v-if="isswitchvisible" class="col-md-12 pb-2">
                <btn-work-time
                  v-on:switchclick-event="switchclick"
                  v-bind:btn-mode="btnmodeswitch"
                  v-bind:is-push="isswitchbutton">
                </btn-work-time>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:updateclick-event="updateclick"
                  v-bind:btn-mode="'update'"
                  v-bind:is-push="isupdatebutton">
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
    <!-- main contentns row -->
    <!-- ========================== 表示部 START ========================== -->
    <div class="row justify-content-between" v-if="serchorshow === 'show'">
      <!-- .panel -->
      <div class="col-md pt-3 align-self-stretch">
        <div class="card shadow-pl">
          <!-- panel header -->
          <div class="card-header bg-transparent pt-3 border-0 print-none">
            <daily-working-information-panel-header
              v-bind:header-text1="stringtext"
              v-bind:header-text2="'虫眼鏡アイコンをクリックするとタイムカードが表示されます'"
            ></daily-working-information-panel-header>
            <daily-working-information-panel-header
              v-bind:header-text1="stringtext2"
              v-bind:header-text2="'タイムカードを印刷する場合は Ctrl+P で印刷してください。'"
            ></daily-working-information-panel-header>
          </div>
          <!-- /.panel header -->
          <!-- panel body -->
          <div class="card-body mb-3 py-0 pt-4 border-top print-none">
            <!-- panel contents -->
            <!-- ----------- ボタン部 START ---------------- -->
            <!-- .row -->
            <div class="row">
              <div class="col-md-12 pb-2">
                <!-- col -->
                <btn-csv-download
                  v-bind:btn-mode="'csvcalc'"
                  v-bind:csv-data="calcresults"
                  v-bind:is-csvbutton="iscsvbutton"
                  v-bind:csv-date="datejaFormat"
                >
                </btn-csv-download>
                <!-- /.col -->
              </div>
            </div>
            <!-- /.row -->
            <div class="row">
              <div class="col-md-12 pb-2">
                <!-- col -->
                <btn-csv-download
                  v-bind:btn-mode="'csvsalary'"
                  v-bind:csv-data="calcresults"
                  v-bind:is-csvbutton="iscsvbutton"
                  v-bind:csv-date="datejaFormat"
                >
                </btn-csv-download>
                <!-- /.col -->
              </div>
            </div>
            <!-- /.row -->
            <!-- ----------- ボタン部 END ---------------- -->
            <!-- /.panel contents -->
          </div>
          <!-- /panel body -->
          <!-- panel body -->
          <div class="card-body mb-3 py-0 pt-4 border-top print-none">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <h1 class="float-md-left font-size-rg">{{ company_name }}</h1>
                <!-- <span class="float-md-right font-size-sm">{{ datejaFormat }}</span> -->
                <span class="float-md-right font-size-sm">時間の単位は　時間:分　です</span>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.panel contents -->
          </div>
          <!-- /panel body -->
          <!-- panel body -->
          <div
            v-for="(calclist,index) in calcresults"
            :key="calclist.user_code"
          >
            <!-- panel contents -->
            <!-- .row -->
            <div class="row">
              <div class="card-body mb-3 py-0 pt-4 border-top print-only print-space">
                <!-- panel contents -->
                <!-- .row -->
                <div class="row">
                  <!-- col -->
                  <div class="col-md-12 pb-2">
                    <h1 class="float-md-left font-size-rg">{{ company_name }}</h1>
                    <span class="float-md-right font-size-sm">{{ datejaFormat }}</span>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
                <!-- /.panel contents -->
              </div>
            </div>
            <div class="row">
              <!-- col -->
              <div class="col-sm-6 col-md-6 col-lg-6 pb-2 align-self-stretch">
                <a
                  class="float-left mr-2 px-2 py-2 font-size-rg btn btn-primary btn-lg print-none"
                  data-toggle="collapse"
                  v-bind:href="'#collapseUser' + index"
                  role="button"
                  aria-expanded="true"
                  v-bind:aria-controls="'collapseUser' + index"
                >
                  <img class="icon-size-rg" src="/images/round-search-w.svg" alt />
                </a>
                <h1 class="font-size-sm m-0 mb-1">氏名</h1>
                <p class="font-size-rg font-weight-bold m-0">{{ calclist.user_name }}</p>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-sm-6 col-md-6 col-lg-6 pb-2 align-self-stretch">
                <h1 class="font-size-sm m-0 mb-1 text-sm-right">所属部署</h1>
                <p class="font-size-rg m-0 text-sm-right">{{ calclist.department }}</p>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <monthly-working-info-table
              v-bind:detail-or-total="'detail'"
              v-bind:calc-lists="calclist"
              v-bind:btn-mode="btnmodeswitch"
            ></monthly-working-info-table>
            <!-- /.row -->
            <!-- /.panel contents -->
            <!-- /panel body -->
            <!-- collapse -->
            <div class="collapse page-break-after" v-bind:id="'collapseUser' + index">
              <!-- panel body -->
              <div class="card-body mb-3 p-0 border-top">
                <!-- panel contents -->
                <!-- .row -->
                <div class="row">
                  <div class="col-12">
                    <div class="table-responsive">
                      <div class="col-12 p-0">
                        <table class="table table-striped border-bottom font-size-sm text-nowrap">
                          <div v-if="calclist.date.length">
                            <thead>
                              <tr>
                                <td class="text-center align-middle w-15">日付</td>
                                <td class="text-center align-middle w-15">出勤時間</td>
                                <td class="text-center align-middle w-15">退勤時間</td>
                                <td class="text-center align-middle w-15">実働時間</td>
                                <td class="text-center align-middle w-15">所定時間</td>
                                <td class="text-center align-middle w-15">残業時間</td>
                                <td class="text-center align-middle w-15">深夜時間</td>
                                <td class="text-center align-middle mw-rem-20">備考</td>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="calclisttimedate in calclist.date">
                                <td
                                  class="text-center align-middle"
                                >{{ calclisttimedate.workingdatename }}</td>
                                <td
                                  v-if="calclisttimedate.attendance != '00:00' || calclisttimedate.leaving != '00:00'"
                                  class="text-center align-middle"
                                >{{ calclisttimedate.attendance }}</td>
                                <td
                                  v-if="calclisttimedate.attendance != '00:00' || calclisttimedate.leaving != '00:00'"
                                  class="text-center align-middle"
                                >{{ calclisttimedate.leaving }}</td>
                                <td
                                  class="text-center align-middle"
                                >{{ calclisttimedate.total_working_times }}</td>
                                <td
                                  class="text-center align-middle"
                                >{{ calclisttimedate.regular_working_times }}</td>
                                <td
                                  class="text-center align-middle"
                                >{{ calclisttimedate.off_hours_working_hours }}</td>
                                <td
                                  class="text-center align-middle"
                                >{{ calclisttimedate.late_night_overtime_hours }}</td>
                                <td
                                  class="text-left align-middle"
                                >{{ calclisttimedate.remark_holiday_name }}</td>
                              </tr>
                            </tbody>
                          </div>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.row -->
                <!-- /.panel contents -->
              </div>
              <!-- /panel body -->
            </div>
          </div>
          <!-- /collapse -->
        </div>
      </div>
      <!-- /.panel -->
    </div>
    <!-- ========================== 表示部 END ========================== -->
    <!-- /main contentns row -->
    <!-- main contentns row -->
    <div class="row justify-content-between print-none" v-if="serchorshow === 'show'">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'合計'"
            v-bind:header-text2="'集計月の合計が表示されます'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <monthly-working-info-table
              v-bind:detail-or-total="'total'"
              v-bind:calc-lists="sumresults"
              v-bind:btn-mode="btnmodeswitch"
            ></monthly-working-info-table>
            <!-- /.row -->
            <!-- /panel contents -->
          </div>
          <!-- /collapse -->
          <!-- panel body -->
          <div class="card-body mb-3 py-0 pt-4 border-top print-none">
            <!-- panel contents -->
            <!-- ----------- ボタン部 START ---------------- -->
            <!-- .row -->
            <div class="row">
              <div class="col-md-12 pb-2">
                <!-- col -->
                <btn-csv-download
                  v-bind:btn-mode="'csvcalc'"
                  v-bind:csv-data="calcresults"
                  v-bind:is-csvbutton="iscsvbutton"
                  v-bind:csv-date="datejaFormat"
                >
                </btn-csv-download>
                <!-- /.col -->
              </div>
            </div>
            <!-- /.row -->
            <div class="row">
              <div class="col-md-12 pb-2">
                <!-- col -->
                <btn-csv-download
                  v-bind:btn-mode="'csvsalary'"
                  v-bind:csv-data="calcresults"
                  v-bind:is-csvbutton="iscsvbutton"
                  v-bind:csv-date="datejaFormat"
                >
                </btn-csv-download>
                <!-- /.col -->
              </div>
            </div>
            <!-- /.row -->
            <!-- ----------- ボタン部 END ---------------- -->
          </div>
        </div>
        <!-- /panel -->
      </div>
      <!-- /main contentns row -->
    </div>
  </div>
</template>
<script>
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "monthlyworkingtime",
  mixins: [ dialogable, checkable, requestable ],
  data: function() {
    return {
      valuefromdate: "",
      valuetodate: "",
      messagevalidatesSearch: [],
      DatePickerFormat: "yyyy年MM月dd日",
      applytermdate: "",
      selectedEmploymentValue: "",
      getDo: 1,
      selectedDepartmentValue : "",
      valueDepartmentkillcheck : false,
      selectedUserValue : "",
      valueUserkillcheck : false,
      showuserlist: true,
      messagedatasserver: [],
      isswitchvisible: false,
      validate: false,
      messageshowupdate: false,
      issearchbutton: false,
      isupdatebutton: false,
      iscsvbutton: true,
      btnmodeswitch: "basicswitch",
      isswitchbutton: false,
      serchorshow: "search",
      company_name: "",
      userrole: "",
      stringtext: "",
      stringtext2: "",
      datejaFormat: "",
      resresults: [],
      calcresults: [],
      sumresults: [],
      messagedatasserver: [],
      messagedatadepartment: [],
      messagedatauser: []
    };
  },
  // マウント時
  mounted() {
    // 今月初末を取得
    const defaultfromDate = moment().startOf('month');
    const defaulttoDate = moment().endOf('month');
    this.valuefromdate = new Date(defaultfromDate);
    this.valuetodate = new Date(defaulttoDate);
    this.getUserRole();
    this.applytermdate = ""
    if (this.valuetodate) {
      this.applytermdate = moment(this.valuetodate).format("YYYYMMDD");
    }
    this.$refs.selectdepartmentlist.getList(this.applytermdate);
    this.getUserSelected();
    this.showorupdate = "search";
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // 検索時のバリデーション
    checkFormSearch: function(e) {
      this.messagevalidatesSearch = [];
      var chkArray = [];
      var flag = true;
      // 開始日付
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '開始日付';
      chkArray = 
        this.checkHeader(this.valuefromdate, required, equalength, maxlength, itemname);
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
      itemname = '終了日付';
      chkArray = 
        this.checkHeader(this.valuetodate, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesSearch.length == 0) {
          this.messagevalidatesSearch = chkArray;
        } else {
          this.messagevalidatesSearch = this.messagevalidatesSearch.concat(chkArray);
        }
      }
      // 氏名
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = '氏名';
      if (this.userrole < "8") {
        chkArray = 
          this.checkHeader(this.selectedUserValue, required, equalength, maxlength, itemname);
        if (chkArray.length > 0) {
          if (this.messagevalidatesSearch.length == 0) {
            this.messagevalidatesSearch = chkArray;
          } else {
            this.messagevalidatesSearch = this.messagevalidatesSearch.concat(chkArray);
          }
        }
      }
      if (this.messagevalidatesSearch.length > 0) {
        flag  = false;
      }

      e.preventDefault();
      return flag;
    },
    // 最新更新時のバリデーション
    checkFormupdNew: function(e) {
      this.messagevalidatesSearch = [];
      var chkArray = [];
      var flag = true;
      // 開始日付
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '開始日付';
      chkArray = 
        this.checkHeader(this.valuefromdate, required, equalength, maxlength, itemname);
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
      itemname = '終了日付';
      chkArray = 
        this.checkHeader(this.valuetodate, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesSearch.length == 0) {
          this.messagevalidatesSearch = chkArray;
        } else {
          this.messagevalidatesSearch = this.messagevalidatesSearch.concat(chkArray);
        }
      }
      // 部署
      // required = true;
      // equalength = 0;
      // maxlength = 0;
      // itemname = '部署';
      // chkArray = 
      //   this.checkHeader(this.selectedDepartmentValue, required, equalength, maxlength, itemname);
      // if (chkArray.length > 0) {
      //   if (this.messagevalidatesSearch.length == 0) {
      //     this.messagedatadepartment.push("指定年月締め一括集計の場合は所属部署は必ず入力してください。");
      //   } else {
      //     this.messagevalidatesSearch = this.messagevalidatesSearch.concat(chkArray);
      //   }
      // }
      // 氏名
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = '氏名';
      if (this.userrole < "8") {
        chkArray = 
          this.checkHeader(this.selectedUserValue, required, equalength, maxlength, itemname);
        if (chkArray.length > 0) {
          if (this.messagevalidatesSearch.length == 0) {
            this.messagevalidatesSearch = chkArray;
          } else {
            this.messagevalidatesSearch = this.messagevalidatesSearch.concat(chkArray);
          }
        }
      }

      if (this.messagevalidatesSearch.length > 0) {
        flag  = false;
      }

      e.preventDefault();
      return flag;
    },
    // ------------------------ イベント処理 ------------------------------------
    
    // 開始日付が変更された場合の処理
    fromdateChanges: function(value) {
      this.valuefromdate = value;
      var fromm = moment(this.valuefromdate).format("MM");
      var tom = moment(this.valuetodate).format("MM");
      if (fromm != tom) {
        this.valuetodate =  new Date(moment(this.valuefromdate).endOf('month'));
      }
      // パネルに表示
      this.setPanelHeader();
    },
    // 開始日付がクリアされた場合の処理
    fromdateCleared: function() {
      this.valuefromdate = "";
      // パネルに表示
      this.setPanelHeader();
    },
    // 終了日付が変更された場合の処理
    fromtoChanges: function(value, arrayitem) {
      this.valuetodate = value;
      // パネルに表示
      this.setPanelHeader();
      // 再取得
      this.applytermdate = ""
      if (this.valuetodate) {
          this.applytermdate = moment(this.valuetodate).format("YYYYMMDD");
      }
      this.$refs.selectdepartmentlist.getList(this.applytermdate);
      this.getUserSelected();
    },
    // 終了日付がクリアされた場合の処理
    fromtoCleared: function() {
      this.valuetodate = "";
      // パネルに表示
      this.setPanelHeader();
      // 再取得
      this.applytermdate = moment(new Date()).format("YYYYMMDD");
      this.$refs.selectdepartmentlist.getList(this.applytermdate);
      this.getUserSelected();
      this.applytermdate = "";
    },
    // 雇用形態が変更された場合の処理
    employmentChanges: function(value) {
      this.selectedEmploymentValue = value;
      this.getDo = 1;
      // ユーザー選択コンポーネントの取得メソッドを実行
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
    searchclick: function(e) {
      this.isswitchvisible = false;
      this.validate = this.checkFormSearch(e);
      if (this.validate) {
        this.issearchbutton = true;
        this.isupdatebutton = true;
        this.iscsvbutton = true;
        // 入力項目クリア
        this.itemClear();
        this.getItem("show");
      }
    },
    // 詳細表示ボタンがクリックされた場合の処理
    switchclick: function() {
      if (this.btnmodeswitch == "basicswitch") {
        this.btnmodeswitch = "detailswitch";
      } else {
        this.btnmodeswitch = "basicswitch";
      }
    },
    // 最新更新開始ボタンがクリックされた場合の処理
    updateclick: function(e) {
      var messages = [];
      messages.push("一括集計してよろしいですか？");
      this.htmlMessageSwal("確認", messages, "info", true, true)
        .then(result  => {
          if (result) {
            this.isswitchvisible = false;
            this.validate = this.checkFormupdNew(e);
            if (this.validate) {
              this.issearchbutton = true;
              this.isupdatebutton = true;
              this.iscsvbutton = true;
              // 入力項目クリア
              this.itemClear();
              this.getItem("update");
            }
          }
      });
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
    // 月次集計取得処理
    getItem(showorupdate) {
      // 処理中メッセージ表示
      this.$swal({
        title: "処　理　中...",
        html: "",
        allowOutsideClick: false, //枠外をクリックしても画面を閉じない
        showConfirmButton: false,
        showCancelButton: true,
        onBeforeOpen: () => {
          this.$swal.showLoading();
          this.postRequest("/monthly/show",
            { showorupdate : showorupdate,
              datefrom : moment(this.valuefromdate).format("YYYYMMDD"),
              dateto : moment(this.valuetodate).format("YYYYMMDD"),
              employmentstatus : this.selectedEmploymentValue,
              departmentcode : this.selectedDepartmentValue,
              usercode : this.selectedUserValue
            })
            .then(response  => {
              this.$swal.close();
              this.getThen(response);
            })
            .catch(reason => {
              this.$swal.close();
              this.issearchbutton = false;
              this.isupdatebutton = false;
              this.serverCatch("月次集計","取得");
            });
        }
      });
    },
    // 最新更新
    // updNew: function() {
    //   this.postRequest("/monthly/calc",
    //     { datefrom : moment(this.valuefromdate).format("YYYYMMDD"),
    //       dateto : moment(this.valuetodate).format("YYYYMMDD"),
    //       employmentstatus : this.selectedEmploymentValue,
    //       departmentcode : this.selectedDepartmentValue,
    //       usercode : this.selectedUserValue
    //     })
    //     .then(response  => {
    //       this.getThenupdNew(response);
    //     })
    //     .catch(reason => {
    //       this.messagewaiting = false;
    //       this.issearchbutton = false;
    //       this.isupdatebutton = false;
    //       this.iscsvbutton = true;
    //       this.serverCatch("月次集計最新更新","取得");
    //     });
    // },

    // ----------------- 共通メソッド ----------------------------------
    // ユーザー選択コンポーネント取得メソッド
    getUserSelected: function() {
      // 再取得
      this.applytermdate = ""
      if (this.valuetodate) {
          this.applytermdate = moment(this.valuetodate).format("YYYYMMDD");
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
      this.resresults = response.data;
      if (this.resresults.calcresults != null) {
        this.calcresults = this.resresults.calcresults;
        if (Object.keys(this.calcresults).length > 0) {
          this.iscsvbutton = false;
          this.isswitchvisible = true;
        }
      }
      if (this.resresults.sumresults != null) {
        this.sumresults = this.resresults.sumresults;
      }
      this.company_name = this.resresults.company_name;
      if (this.resresults.messagedata.length == 0) {
        this.serchorshow = "show";
      } else {
        this.htmlMessageSwal("エラー", this.resresults.messagedata, "error", true, false);
        this.serchorshow = "search";
      }
      this.issearchbutton = false;
      this.isupdatebutton = false;
      this.$forceUpdate();
    },
    // 最新更新正常処理
    // getThenupdNew(response) {
    //   this.resresults = response.data;
    //   if (this.resresults.calcresults != null) {
    //     this.calcresults = this.resresults.calcresults;
    //   }
    //   if (this.resresults.sumresults != null) {
    //     this.sumresults = this.resresults.sumresults;
    //   }
    //   this.company_name = this.resresults.company_name;
    //   if (this.resresults.messagedata != null) {
    //     this.messageswal(
    //       "エラー",
    //       res.messagedata,
    //       "error",
    //       true,
    //       false,
    //       true
    //     );
    //     this.messageshowupdate = false;
    //     this.issearchbutton = false;
    //     this.isupdatebutton = false;
    //     this.showorupdate = "search";
    //     this.$forceUpdate();
    //   } else {
    //     this.messageshowupdate = false;
    //     this.issearchbutton = false;
    //     this.isupdatebutton = false;
    //     this.showorupdate = "show";
    //     this.$forceUpdate();
    //   }
    // },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + "情報" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
      this.serchorshow = "search";
    },
    // クリアメソッド
    itemClear: function() {
      this.resresults = [];
      this.calcresults = [];
      this.sumresults = [];
      this.messagedatasserver = [];
      this.messagedatadepartment = [];
      this.messagedatauser = [];
    },
    // 集計パネルヘッダ文字列編集処理
    setPanelHeader: function() {
      var fromtext = "";
      var totext = "";
      moment.locale("ja");
      if (this.valuefromdate != null && this.valuefromdate != "") {
        fromtext = moment(this.valuefromdate).format("YYYY年MM月DD日");
      }
      if (this.valuetodate != null && this.valuetodate != "") {
        totext = moment(this.valuetodate).format("YYYY年MM月DD日");
      }
      if (fromtext != "" && totext != "") {
        fromtext = fromtext + "から";
      }
      this.datejaFormat = fromtext + totext;
      this.stringtext = this.datejaFormat + "分を集計";
    }
  }
};
</script>
<style scoped>
.table th, .table td {
    padding: 0.4rem !important;
}

.mw-rem-3 {
  min-width: 3rem !important;
}
</style>
