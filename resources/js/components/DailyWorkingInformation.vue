<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'日付を指定して集計を表示する'"
            v-bind:header-text2="'雇用形態や所属部署でフィルタリングして表示できます'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="target_fromdate"
                    >
                      指定日付
                      <span class="color-red">[必須]</span>
                    </span>
                  </div>
                  <input-datepicker
                    v-bind:default-date="valuedate"
                    v-bind:date-format="DatePickerFormat"
                    v-bind:place-holder="'指定日付を選択してください'"
                    v-on:change-event="fromdateChanges"
                    v-on:clear-event="fromdateCleared"
                  ></input-datepicker>
                </div>
                <message-data
                  v-bind:message-datas="messagedatasfromdate"
                  v-bind:message-class="'warning'"
                ></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="target_employmentstatus"
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
                      for="target_department"
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
                    v-bind:row-index="0"
                    v-on:change-event="departmentChanges"
                  ></select-departmentlist>
                </div>
                <message-data
                  v-bind:message-datas="messagedatadepartment"
                  v-bind:message-class="'warning'"
                ></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="target_users"
                    >氏 名</label>
                  </div>
                  <select-userlist
                    v-if="showuserlist"
                    ref="selectuserlist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'氏名を選択してください'"
                    v-bind:selected-value="selectedUserValue"
                    v-bind:add-new="false"
                    v-bind:get-do="getDo"
                    v-bind:date-value="applytermdate"
                    v-bind:kill-value="valueUserkillcheck"
                    v-bind:row-index="0"
                    v-bind:department-value="selectedDepartmentValue"
                    v-bind:employment-value="selectedEmploymentValue"
                    v-on:change-event="userChanges"
                  ></select-userlist>
                </div>
                <message-data
                  v-bind:message-datas="messagedatauser"
                  v-bind:message-class="'warning'"
                ></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <message-data-server
                  v-bind:message-datas="messagedatasserver"
                  v-bind:message-class="'warning'"
                ></message-data-server>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:searchclick-event="searchclick"
                  v-bind:btn-mode="'search'"
                  v-bind:is-push="issearchbutton"
                ></btn-work-time>
                <!-- <message-waiting v-bind:is-message-show="messageshowsearch"></message-waiting> -->
              </div>
              <!-- /.col -->
              <!-- col -->
              <div v-if="isswitchvisible" class="col-md-12 pb-2">
                <btn-work-time
                  v-on:switchclick-event="switchclick"
                  v-bind:btn-mode="btnmodeswitch"
                  v-bind:is-push="isswitchbutton"
                ></btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.panel contents -->
          </div>
        </div>
      </div>
      <!-- /.panel -->
    </div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3 align-self-stretch" v-if="calcresults.length">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="stringtext"
            v-bind:header-text2="'時間の単位は　時:分　です'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- panel body -->
          <div class="card-body pt-2">
            <!-- ----------- 選択ボタン類 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-3 pb-2">
                <btn-work-time
                  v-on:gosubateclick-event="gosubateclick"
                  v-bind:btn-mode="'gosubdate'"
                  v-bind:is-display="isgosubdatebutton"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-md-3 pb-2">
                <btn-work-time
                  v-on:goaddateclick-event="goaddateclick"
                  v-bind:btn-mode="'goadddate'"
                  v-bind:is-display="isgoadddatebutton"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- 選択ボタン類 END ---------------- -->
          </div>
          <div>
            <div class="card-body mb-3 border-top">
              <!-- ----------- 日次集計テーブル START ---------------- -->
              <daily-working-info-table
                v-bind:detail-or-total="'detail'"
                v-bind:calc-lists="calcresults"
                v-bind:date-name="dateName"
                v-bind:predeter-time-name="predetertimename"
                v-bind:predeter-night-time-name="predeternighttimename"
                v-bind:predeter-time-secondname="predetertimesecondname"
                v-bind:predeter-night-time-secondname="predeternighttimesecondname"
                v-bind:btn-mode="btnmodeswitch"
              ></daily-working-info-table>
              <!-- ----------- 日次集計テーブル END ---------------- -->
            </div>
          </div>
          <!-- /panel body -->
        </div>
        <div class="card shadow-pl" v-if="sumresults.length">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'合計'"
            v-bind:header-text2="'集計日の合計が表示されます'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- panel body -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <daily-working-info-sum-table
              v-bind:detail-or-total="'total'"
              v-bind:calc-lists="sumresults"
              v-bind:predeter-time-name="predetertimename"
              v-bind:predeter-night-time-name="predeternighttimename"
              v-bind:predeter-time-secondname="predetertimesecondname"
              v-bind:predeter-night-time-secondname="predeternighttimesecondname"
              v-bind:btn-mode="btnmodeswitch"
            ></daily-working-info-sum-table>
            <!-- /.row -->
            <!-- /panel contents -->
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
import { dialogable } from "../mixins/dialogable.js";
import { checkable } from "../mixins/checkable.js";
import { requestable } from "../mixins/requestable.js";

export default {
  name: "dailyworkingtime",
  mixins: [dialogable, checkable, requestable],
  data: function() {
    return {
      valuedate: "",
      selectedDepartmentValue: "",
      valueDepartmentkillcheck: false,
      showdepartmentlist: true,
      selectedUserValue: "",
      showuserlist: true,
      valueUserkillcheck: false,
      selectedEmploymentValue: "",
      getDo: 1,
      applytermdate: "",
      valuefromdate: "",
      valuesubadddate: "",
      userrole: "",
      DatePickerFormat: "yyyy年MM月dd日",
      defaultDate: new Date(),
      stringtext: "",
      datejaFormat: "",
      hrefindex: "",
      resresults: [],
      calcresults: [],
      sumresults: [],
      messages: [],
      messagedatasserver: [],
      messagedatasfromdate: [],
      messagedatadepartment: [],
      messagedatauser: [],
      predetertimename: "",
      predeternighttimename: "",
      predetertimesecondname: "",
      predeternighttimesecondname: "",
      dateName: "",
      messageshowsearch: false,
      issearchbutton: false,
      isgosubdatebutton: false,
      isgoadddatebutton: false,
      btnmodeswitch: "basicswitch",
      isswitchbutton: false,
      isswitchvisible: false,
      validate: true,
      initialized: false
    };
  },
  // マウント時
  mounted() {
    this.valuedate = this.defaultDate;
    this.valuefromdate = moment(this.defaultDate).format("YYYYMMDD");
    this.valuesubadddate = this.valuefromdate;
    this.getUserRole();
    this.applytermdate = "";
    if (this.valuefromdate) {
      this.applytermdate = this.valuefromdate;
    }
    this.$refs.selectdepartmentlist.getList(this.applytermdate);
    this.getUserSelected();
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    checkForm: function(e) {
      this.messagedatasserver = [];
      this.messagedatasfromdate = [];
      this.messagedatadepartment = [];
      this.messagedatauser = [];
      var chkArray = [];
      this.validate = true;
      // 指定日付
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = "指定日付";
      chkArray = this.checkHeader(
        this.valuedate,
        required,
        equalength,
        maxlength,
        itemname
      );
      if (chkArray.length > 0) {
        if (this.messagedatasfromdate.length == 0) {
          this.messagedatasfromdate = chkArray;
        } else {
          this.messagedatasfromdate = this.messagedatasfromdate.concat(
            chkArray
          );
        }
        this.validate = false;
      }
      // 所属部署
      if (this.userrole < "8") {
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
          if (this.messagedatadepartment.length == 0) {
            this.messagedatadepartment.push(
              "一般ユーザーは所属部署は必ず入力してください。"
            );
          } else {
            this.messagedatadepartment = this.messagedatadepartment.concat(
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
          if (this.messagedatauser.length == 0) {
            this.messagedatauser.push(
              "一般ユーザーは氏名は必ず入力してください。"
            );
          } else {
            this.messagedatauser = this.messagedatauser.concat(chkArray);
          }
          this.validate = false;
        }
      }
      if (this.validate) {
        return this.validate;
      }

      e.preventDefault();
    },
    // ------------------------ イベント処理 ------------------------------------
    // 指定日付が変更された場合の処理
    fromdateChanges: function(value) {
      moment.locale("ja");
      this.stringtext = "";
      this.valuedate = value;
      this.valuefromdate = moment(value).format("YYYYMMDD");
      this.valuesubadddate = this.valuefromdate;
      if (this.valuedate == null || this.valuedate == "") {
        this.stringtext = "";
      } else {
        this.datejaFormat = moment(this.valuedate).format(
          "YYYY年MM月DD日 (ddd)"
        );
        this.stringtext = "日次集計 " + this.datejaFormat;
      }
      // 再取得
      this.applytermdate = "";
      if (this.valuedate) {
        this.applytermdate = moment(this.valuedate).format("YYYYMMDD");
      }
      this.$refs.selectdepartmentlist.getList(this.applytermdate);
      this.getUserSelected();
    },
    // 指定日付がクリアされた場合の処理
    fromdateCleared: function() {
      this.valuedate = "";
      this.valuefromdate = "";
      this.valuesubadddate = "";
      this.applytermdate = "";
      this.stringtext = "";
    },
    // 雇用形態が変更された場合の処理
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
    userChanges: function(value, arrayitem) {
      this.selectedUserValue = value;
    },
    // 表示するボタンがクリックされた場合の処理
    searchclick: function(e) {
      this.isswitchvisible = false;
      this.valuesubadddate = "";
      this.validate = this.checkForm(e);
      if (this.validate) {
        this.issearchbutton = true;
        this.messageshowsearch = true;
        // 入力項目クリア
        this.itemClear();
        this.valuesubadddate = moment(this.valuedate).format("YYYYMMDD");
        this.selectedName = this.user_name + "　" + moment(this.valuesubadddate).format("YYYY年MM月DD日") + "分勤怠編集" ;
        this.getItem(moment(this.valuedate).format("YYYYMMDD"));
        this.isgosubdatebutton = true;
        this.isgoadddatebutton = true;
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
    //前日ボタンクリック処理
    gosubateclick(e) {
      this.issearchbutton = true;
      this.messageshowsearch = true;
      // 入力項目クリア
      this.itemClear();
      this.messagevalidatesSearch = [];
      this.messagevalidatesEdt = [];
      if (this.checkForm(e)) {
        this.valuesubadddate = moment(this.valuesubadddate).subtract(1, 'days').format("YYYYMMDD");
        this.datejaFormat = moment(this.valuesubadddate).format(
          "YYYY年MM月DD日 (ddd)"
        );
        this.stringtext = "日次集計 " + this.datejaFormat;
        this.getItem(this.valuesubadddate);
      }
    },
    //翌日ボタンクリック処理
    goaddateclick(e) {
      this.issearchbutton = true;
      this.messageshowsearch = true;
      // 入力項目クリア
      this.itemClear();
      this.messagevalidatesSearch = [];
      this.messagevalidatesEdt = [];
      if (this.checkForm(e)) {
        this.valuesubadddate = moment(this.valuesubadddate).add(1, 'days').format("YYYYMMDD");
        this.datejaFormat = moment(this.valuesubadddate).format(
          "YYYY年MM月DD日 (ddd)"
        );
        this.stringtext = "日次集計 " + this.datejaFormat;
        this.getItem(this.valuesubadddate);
      }
    },
    // ------------------------ サーバー処理 ----------------------------
    // ログインユーザーの権限を取得
    getUserRole: function() {
      var arrayParams = [];
      this.postRequest("/get_login_user_role", arrayParams)
        .then(response => {
          this.getThenrole(response);
        })
        .catch(reason => {
          this.serverCatch("ユーザー権限", "取得");
        });
    },
    // 日次集計取得処理
    getItem(datevalue) {
      // 処理中メッセージ表示
      this.$swal({
        title: "処　理　中...",
        html: "",
        allowOutsideClick: false, //枠外をクリックしても画面を閉じない
        showConfirmButton: false,
        showCancelButton: true,
        onBeforeOpen: () => {
          this.$swal.showLoading();
          this.postRequest("/daily/calc",
            { datefrom : datevalue,
              dateto : datevalue,
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
              this.messageshowsearch = false;
              this.issearchbutton = false;
              this.serverCatch("日次集計","取得");
            });
        }
      });
    },

    // ----------------- 共通メソッド ----------------------------------
    // ユーザー選択コンポーネント取得メソッド
    getUserSelected: function() {
      this.applytermdate = "";
      if (this.valuedate) {
        this.applytermdate = moment(this.valuedate).format("YYYYMMDD");
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
          this.htmlMessageSwal(
            "エラー",
            res.messagedata,
            "error",
            true,
            false
          );
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
      }
      if (this.resresults.sumresults != null) {
        this.sumresults = this.resresults.sumresults;
      }
      if (this.resresults.datename != null) {
        this.dateName = this.resresults.datename;
        this.stringtext = "日次集計 " + this.dateName;
      }
      if (this.resresults.messagedata != null) {
        this.messagedatasserver = this.resresults.messagedata;
      }
      for (var key in this.calcresults) {
        this.isswitchvisible = true;
        this.predetertimename = this.calcresults[key][
          "predeter_time_name"
        ];
        this.predeternighttimename = this.calcresults[key][
          "predeter_night_time_name"
        ];
        this.predetertimesecondname = this.calcresults[key][
          "predeter_time_secondname"
        ];
        this.predeternighttimesecondname = this.calcresults[key][
          "predeter_night_time_secondname"
        ];
        break;
      }
      this.$forceUpdate();
      this.messageshowsearch = false;
      this.issearchbutton = false;
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + "情報" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    //  クリアメソッド
    itemClear: function() {
      this.resresults = [];
      this.calcresults = [];
      this.sumresults = [];
      this.messages = [];
      this.messagedatasserver = [];
      this.messagedatasfromdate = [];
      this.messagedatadepartment = [];
      this.messagedatauser = [];
    }
  }
};
</script>
