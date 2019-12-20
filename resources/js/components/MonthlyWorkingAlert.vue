<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between print-none">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'期首月または１月（検索区分）から指定年月の間でアラートを表示'"
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
                    <span class="input-group-text font-size-sm line-height-xs label-width-120" id="basic-addon1">指定年月<span class="color-red">[必須]</span></span>
                  </div>
                  <input-datepicker
                    v-bind:default-date="valuefromym"
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
                    <label class="input-group-text font-size-sm line-height-xs label-width-120" for="inputGroupSelect01">
                      検索区分<span class="color-red">[必須]</span></label>
                  </div>
                  <select-generallist
                    v-bind:blank-data="false"
                    v-bind:placeholder-data="'検索区分を選択してください'"
                    v-bind:selected-value="selecteSearchvalue"
                    v-bind:add-new="false"
                    v-bind:get-do="'1'"
                    v-bind:date-value="''"
                    v-bind:kill-value="false"
                    v-bind:row-index="'0'"
                    v-bind:identification-id="'C022'"
                    v-on:change-event="displayChange"
                  ></select-generallist>
                </div>
                <message-data v-bind:message-datas="messagedatadisplay" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label class="input-group-text font-size-sm line-height-xs label-width-120" for="inputGroupSelect01">雇用形態</label>
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
                    <label class="input-group-text font-size-sm line-height-xs label-width-120" for="inputGroupSelect01">所属部署</label>
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
                    <label class="input-group-text font-size-sm line-height-xs label-width-120" for="inputGroupSelect01">氏　　名</label>
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
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time v-bind:btn-mode="'search'" v-on:searchclick-event="searchclick"></btn-work-time>
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
    <!-- /main contentns row -->
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3 align-self-stretch">
        <div class="card shadow-pl">
          <!-- panel header -->
          <div class="card-header bg-transparent pt-3 border-0 print-none">
            <daily-working-information-panel-header
              v-bind:header-text1="stringtext"
              v-bind:header-text2="'各月の時間は残業時間合計（深夜時間含む）です'"
            ></daily-working-information-panel-header>
          </div>
          <!-- /.panel header -->
          <!-- panel body -->
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
          <!-- /panel body -->
          <!-- panel body -->
          <!-- panel contents -->
          <!-- .row -->
          <div
            v-for="(timeitem,index) in timeitems"
            :key="timeitem.user_code"
            class="col-12 p-0 border-secondary"
          >
            <div class="row">
              <!-- col -->
              <div class="col-sm-6 col-md-6 col-lg-6 pb-2 align-self-stretch">
                <a
                  class="float-left mr-2 px-2 py-2 font-size-rg btn-lg print-none"
                >
                  <img class="icon-size-rg" src="/images/round-person-b.svg" alt />
                </a>
                <h1 class="font-size-sm m-0 mb-1 pt-2">氏名</h1>
                <p class="font-size-rg font-weight-bold m-0">{{ timeitem.user_name }}（{{ timeitem.employment_status_name }}）</p>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-sm-6 col-md-6 col-lg-6 pb-2 align-self-stretch">
                <h1 class="font-size-sm m-0 mb-1 text-sm-right pt-2">所属部署　</h1>
                <p class="font-size-rg m-0 text-sm-right">{{ timeitem.department_name }}　</p>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <monthly-working-alert-table
              v-bind:time-items="timeitem"
            ></monthly-working-alert-table>
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
  name: "monthlyworkingtime",
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
      placeholderdata: "",
      company_name: "",
      valuefromym: "",
      selecteSearchvalue: 0,
      getDo: 1,
      applytermdate: "",
      valuefromdate: "",
      userrole: "",
      DatePickerFormat: "yyyy年MM月",
      defaultDate: new Date(),
      stringtext: "",
      datejaFormat: "",
      hrefindex: "",
      resresults: [],
      timeitems: [],
      timevalues: [],
      warningitems: [],
      warningvalues: [],
      messagedatasserver: [],
      messagedatasfromdate: [],
      messagedatastodate: [],
      messagedatadisplay: [],
      messagedatadepartment: [],
      messagedatauser: [],
      validate: false,
      initialized: false
    };
  },
  // マウント時
  mounted() {
    this.valuefromym = this.defaultDate;
    this.getUserRole();
    },
  methods: {
    // バリデーション
    checkForm: function(e) {
      this.validate = true;
      this.messagedatasserver = [];
      this.messagedatasfromdate = [];
      this.messagedatadisplay = [];
      this.messagedatadepartment = [];
      this.messagedatauser = [];
      if (!this.valuefromym) {
        this.messagedatasfromdate.push("指定年月は必ず入力してください。");
        this.validate = false;
      }
      if (!this.selecteSearchvalue) {
        this.messagedatadisplay.push("検索区分は必ず入力してください。");
        this.validate = false;
      } else if (this.selecteSearchvalue == 0) {
        this.messagedatadisplay.push("検索区分は必ず入力してください。");
        this.validate = false;
      }
      if (this.serchorupdate == "update") {
        if (!this.selectedDepartmentValue) {
          this.messagedatadepartment.push("所属部署は必ず入力してください。");
          this.validate = false;
        }
        if (this.userrole < "8") {
          if (!this.selectedUserValue) {
            this.messagedatauser.push("氏名は必ず入力してください。");
            this.validate = false;
          }
        }
      } else {
        if (this.userrole < "8") {
          if (!this.selectedDepartmentValue) {
            this.messagedatadepartment.push("所属部署は必ず入力してください。");
            this.validate = false;
          }
          if (!this.selectedUserValue) {
            this.messagedatauser.push("氏名は必ず入力してください。");
            this.validate = false;
          }
        }
      }

      if (this.validate) {
        return this.validate;
      }

      e.preventDefault();
    },
    // 指定年月が変更された場合の処理
    fromdateChanges: function(value) {
      this.valuefromym = value;
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
    // 指定日付がクリアされた場合の処理
    fromdateCleared: function() {
      this.valuefromym = ""
      // パネルに表示
      this.setPanelHeader();
      this.applytermdate = "";
      this.valuefromdate = "";
    },
    // 表示区分が変更された場合の処理
    displayChange: function(value, name) {
      this.selecteSearchvalue = value;
      this.setPanelHeader();
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
    // 検索開始ボタンがクリックされた場合の処理
    searchclick: function(e) {
      this.serchorupdate = "search";
      this.validate = this.checkForm(e);
      if (this.validate) {
        this.itemClear();
        this.$axios
          .get("/monthly_alert/show", {
            params: {
              datefrom: moment(this.valuefromdate).format("YYYYMM"),
              displaykbn: this.selecteSearchvalue,
              employmentstatus: this.selectedEmploymentValue,
              departmentcode: this.selectedDepartmentValue,
              usercode: this.selectedUserValue
            }
          })
          .then(response => {
            this.resresults = response.data;
            if (this.resresults.alert_result == true) {
              if (this.resresults.timeitems != null) {
                this.timeitems = this.resresults.timeitems;
              }
            }
            if (this.resresults.messagedata != null) {
              this.messagedatasserver = this.resresults.messagedata;
            }
            console.log("timeitems" + Object.keys(this.timeitems).length);
            console.log("messagedatasserver" + Object.keys(this.messagedatasserver).length);
            this.$forceUpdate();
          })
          .catch(reason => {
            alert("月次警告通知エラー");
          });
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
      this.timeitems = [];
      this.timevalues = [];
      this.warningitems = [];
      this.warningvalues = [];
      this.messagedatasserver = [];
      this.messagedatasfromdate = [];
      this.messagedatastodate = [];
      this.messagedatadisplay = [];
      this.messagedatadepartment = [];
      this.messagedatauser = [];
    },
    // ユーザー選択コンポーネント取得メソッド
    getUserSelected: function() {
      this.$refs.selectuserlist.getList(
        '',
        this.valueUserkillcheck,
        this.getDo,
        this.selectedDepartmentValue,
        this.selectedEmploymentValue
      );
    },
    // 集計パネルヘッダ文字列編集処理
    setPanelHeader: function() {
      moment.locale("ja");
      if (this.valuefromym == null || this.valuefromym == "") {
        this.stringtext = "";
      } else {
        if (this.selecteSearchvalue == null || this.selecteSearchvalue == "" || this.selecteSearchvalue == 0) {
          this.stringtext = "";
        } else {
          this.valuefromdate = this.valuefromym;
          if (
            moment(this.valuefromdate).format("YYYYMM") !=
            moment().format("YYYYMM")
          ) {
            this.valuefromdate = moment(this.valuefromdate)
              .endOf("month")
              .format("YYYYMMDD");
          } else {
            this.valuefromdate = moment().format("YYYYMMDD");
          }
          this.datejaFormat = moment(this.valuefromdate).format("YYYY年MM月");
          if (this.selecteSearchvalue == 1) {
            this.stringtext = this.datejaFormat + "のアラートを締日集計で検索";
          } else {
            if (this.selecteSearchvalue == 2) {
              this.stringtext = this.datejaFormat + "のアラートを１か月集計で検索";
            } else {
              if (this.selecteSearchvalue == 3) {
                this.stringtext = this.datejaFormat + "のアラートを締日集計で検索";
              } else {
                if (this.selecteSearchvalue == 4) {
                  this.stringtext = this.datejaFormat + "のアラートを１か月集計で検索";
                } else {
                  this.stringtext = "";
                }
              }
            }
          }
        }
      }
    },
    // メッセージ処理
    dispmessage: function(items) {
      items.forEach(function(value) {
        this.messagedatasserver.push(value);
      });
    }
  }

};
</script>
