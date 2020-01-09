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
                    >指定日付<span class="color-red">[必須]</span></span>
                  </div>
                  <input-datepicker
                    v-bind:default-date="valuefromdate"
                    v-bind:date-format="DatePickerFormat"
                    v-bind:place-holder="'指定日付を選択してください'"
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
                      for="target_users"
                    >氏 名</label>
                  </div>
                  <select-userlist v-if="showuserlist"
                    ref="selectuserlist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'氏名を選択してください'"
                    v-bind:selected-value="selectedUserValue"
                    v-bind:add-new="false"
                    v-bind:get-do="getDo"
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
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <message-data-server v-bind:message-datas="messagedatasserver" v-bind:message-class="'warning'"></message-data-server>
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
                  v-bind:is-push="issearchbutton">
                </btn-work-time>
                <message-waiting v-bind:is-message-show="messageshowsearch"></message-waiting>
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
      <div class="col-md pt-3 align-self-stretch">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="stringtext"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- panel body -->
          <daily-working-info-table
            v-bind:detail-or-total="'detail'"
            v-bind:calc-lists="calcresults"
            v-bind:predeter-time-name="predetertimename"
            v-bind:predeter-night-time-name="predeternighttimename"
            v-bind:btn-mode="btnmodeswitch"
          ></daily-working-info-table>
          <!-- /panel body -->
        </div>
      </div>
      <!-- /.panel -->
    </div>
    <!-- /main contentns row -->
    <!-- main contentns row -->
    <div>
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'合計'"
            v-bind:header-text2="'集計日の合計が表示されます'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <daily-working-info-table
              v-bind:detail-or-total="'total'"
              v-bind:calc-lists="sumresults"
              v-bind:predeter-time-name="predetertimename"
              v-bind:predeter-night-time-name="predeternighttimename"
              v-bind:btn-mode="btnmodeswitch"
            ></daily-working-info-table>
            <!-- /.row -->
            <!-- /panel contents -->
          </div>
        </div>
        <!-- /panel -->
      </div>
      <!-- /main contentns row -->
    </div>
  </div>
</template>

<script>
import toasted from "vue-toasted";
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "dailyworkingtime",
  mixins: [ dialogable, checkable, requestable ],
  data: function() {
    return {
      selectedDepartmentValue : "",
      valueDepartmentkillcheck : false,
      showdepartmentlist: true,
      selectedUserValue : "",
      showuserlist: true,
      valueUserkillcheck : false,
      selectedEmploymentValue: "",
      getDo: 1,
      applytermdate: "",
      valuefromdate: "",
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
      dateName: "",
      messageshowsearch: false,
      issearchbutton: false,
      btnmodeswitch: "basicswitch",
      isswitchbutton: false,
      isswitchvisible: false,
      validate: true,
      initialized: false
    };
  },
  // マウント時
  mounted() {
    this.valuefromdate = this.defaultDate;
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
      var itemname = '指定日付';
      chkArray = 
        this.checkHeader(this.valuefromdate, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagedatasfromdate.length == 0) {
          this.messagedatasfromdate = chkArray;
        } else {
          this.messagedatasfromdate = this.messagedatasfromdate.concat(chkArray);
        }
        this.validate = false;
      }
      // 所属部署
      if (this.userrole < "8") {
        required = true;
        equalength = 0;
        maxlength = 0;
        itemname = '所属部署';
        chkArray = 
          this.checkHeader(this.selectedDepartmentValue, required, equalength, maxlength, itemname);
        if (chkArray.length > 0) {
          if (this.messagedatadepartment.length == 0) {
            this.messagedatadepartment.push("一般ユーザーは所属部署は必ず入力してください。");
          } else {
            this.messagedatadepartment = this.messagedatadepartment.concat(chkArray);
          }
          this.validate = false;
        }
        required = true;
        equalength = 0;
        maxlength = 0;
        itemname = '氏名';
        chkArray = 
          this.checkHeader(this.selectedUserValue, required, equalength, maxlength, itemname);
        if (chkArray.length > 0) {
          if (this.messagedatauser.length == 0) {
            this.messagedatauser.push("一般ユーザーは氏名は必ず入力してください。");
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
      this.valuefromdate = value;
      if (this.valuefromdate == null || this.valuefromdate == "") {
        this.stringtext = "";
      } else {
        this.datejaFormat = moment(this.valuefromdate).format(
          "YYYY年MM月DD日 (ddd)"
        );
        this.stringtext = "日次集計 " + this.datejaFormat;
      }
      // 再取得
      this.applytermdate = ""
      if (this.valuefromdate) {
        this.applytermdate = moment(this.valuefromdate).format("YYYYMMDD");
      }
      this.$refs.selectdepartmentlist.getList(this.applytermdate);
      this.getUserSelected();
    },
    // 指定日付がクリアされた場合の処理
    fromdateCleared: function() {
      this.valuefromdate = ""
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
    // 集計開始ボタンがクリックされた場合の処理
    searchclick: function(e) {
      this.isswitchvisible = false;
      this.validate = this.checkForm(e);
      if (this.validate) {
        this.issearchbutton = true;
        this.messageshowsearch = true;
        this.itemClear();
        this.$axios
          .get("/daily/calc", {
            params: {
              datefrom: moment(this.valuefromdate).format("YYYYMMDD"),
              dateto: moment(this.valuefromdate).format("YYYYMMDD"),
              employmentstatus: this.selectedEmploymentValue,
              departmentcode: this.selectedDepartmentValue,
              usercode: this.selectedUserValue
            }
          })
          .then(response => {
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
              this.predetertimename = this.calcresults[key]['predeter_time_name'];
              this.predeternighttimename = this.calcresults[key]['predeter_night_time_name'];
              break;
            };
            this.$forceUpdate();
            this.messageshowsearch = false;
            this.issearchbutton = false;
          })
          .catch(reason => {
            this.messageshowsearch = false;
            this.issearchbutton = false;
            alert("日次集計エラー");
          });
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

    // ----------------- 共通メソッド ----------------------------------
    // ユーザー選択コンポーネント取得メソッド
    getUserSelected: function() {
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
          this.messageswal("エラー", res.messagedata, "error", true, false, true);
        } else {
          this.serverCatch("ユーザー権限", "取得");
        }
      }
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + "情報" + eventtext + "に失敗しました");
      this.messageswal("エラー", messages, "error", true, false, true);
    },

    // クリアメソッド
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
