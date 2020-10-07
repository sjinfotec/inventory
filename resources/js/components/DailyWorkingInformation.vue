<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3 print-none">
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
              <div class="col-md-6 pb-2" v-if="!this.get_CalcListAllselectValue && this.get_LoginUserRole >= this.get_AdminUserRole">
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
              <div class="col-md-6 pb-2" v-if="this.get_CalcListAllselectValue">
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
              <div class="col-md-6 pb-2" v-if="!this.get_CalcListAllselectValue && this.get_LoginUserRole >= this.get_AdminUserRole">
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
              <div class="col-md-6 pb-2" v-if="this.get_CalcListAllselectValue">
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
                    v-bind:blank-data="get_IsUserblank"
                    v-bind:placeholder-data="'氏名を選択してください'"
                    v-bind:selected-value="get_SelectedUserCode"
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
            </div>
            <!-- /.row -->
            <!-- /.panel contents -->
          </div>
        </div>
      </div>
      <!-- /.panel -->
    </div>
    <!-- main contentns row -->
    <div class="row justify-content-between print-none">
      <!-- .panel -->
      <div class="col-md-12 pt-3 align-self-stretch print-none" v-if="selectmode === 'DSP'">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="stringtext"
            v-bind:header-text2="'時間の単位は　時:分　です'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
        </div>
        <div class="card shadow-pl">
          <!-- panel body -->
          <div class="card-body pt-2">
            <!-- ----------- 印刷ボタン START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-2 pb-2" v-if="calcresults.length">
                <btn-work-time
                  v-on:dailycalc-event="printclick"
                  v-bind:btn-mode="'dailycalc'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
            </div>
            <!-- ----------- 印刷ボタン END ---------------- -->
            <!-- ----------- 選択ボタン類 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-4 pb-2">
                <btn-work-time
                  v-on:gosubateclick-event="gosubateclick"
                  v-bind:btn-mode="'gosubdate'"
                  v-bind:is-display="isgosubdatebutton"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-md-4 pb-2">
                <btn-work-time
                  v-on:switchclick-event="switchclick"
                  v-bind:btn-mode="btnmodeswitch"
                  v-bind:is-push="isswitchbutton"
                ></btn-work-time>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-md-4 pb-2">
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
          <div v-if="calcresults.length">
            <div class="card-body mb-3 border-top">
              <!-- ----------- 日次集計テーブル START ---------------- -->
              <daily-working-info-table
                v-bind:detail-or-total="'detail'"
                v-bind:calc-lists="calcresults"
                v-bind:date-name="dateName"
                v-bind:predeter-time-name="predetertimename"
                v-bind:predeter-night-time-name="predeternighttimename"
                v-bind:predeter-time-second-name="predetertimesecondname"
                v-bind:predeter-night-time-second-name="predeternighttimesecondname"
                v-bind:btn-mode="btnmodeswitch"
                v-bind:login-user="get_LoginUserCode"
                v-bind:login-role="get_LoginUserRole"
                v-bind:admin-role="get_AdminUserRole"
                v-bind:account-data="authusers['account_id']"
                v-bind:menu-data="menudatas"
                v-bind:edituser-index="get_EditUserIndex"
                v-bind:edituser-con-index="get_EditUserConIndex"
                v-bind:c005-data="get_C005"
              ></daily-working-info-table>
              <!-- ----------- 日次集計テーブル END ---------------- -->
            </div>
          </div>
          <!-- /panel body -->
        </div>
      </div>
      <!-- /.panel -->
      <div class="col-md pt-3 align-self-stretch" v-if="selectmode === 'DSP'">
        <div class="card shadow-pl" v-if="sumresults.length">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'合計'"
            v-bind:header-text2="'集計日の合計が表示されます'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
        </div>
        <div class="card shadow-pl" v-if="sumresults.length">
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
    <div class="row justify-content-between print-only printdailypages" v-if="selectmode === 'DSP'">
      <!-- 以下印刷のみ print-only -->
      <!-- .panel -->
      <div class="col-md pt-3 align-self-stretch">
        <div class="card shadow-pl">
          <!-- panel header -->
          <div class="card-header bg-transparent pb-2 border-0">
            <h1 class="float-sm-left font-size-rg line-height-0-5 mb-0">{{ stringtext }}</h1>
          </div>
          <!-- /.panel header -->
          <!-- panel body -->
          <div class="card-body pt-2 print-none">
            <!-- ----------- 選択ボタン類 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-4 pb-2">
                <btn-work-time
                  v-on:gosubateclick-event="gosubateclick"
                  v-bind:btn-mode="'gosubdate'"
                  v-bind:is-display="isgosubdatebutton"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-md-4 pb-2">
                <btn-work-time
                  v-on:switchclick-event="switchclick"
                  v-bind:btn-mode="btnmodeswitch"
                  v-bind:is-push="isswitchbutton"
                ></btn-work-time>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-md-4 pb-2">
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
        </div>
        <div class="card shadow-pl" v-if="sumresults.length">
          <div class="card-body mb-3 border-top" v-if="calcresults.length">
            <!-- ----------- 日次集計テーブル START ---------------- -->
            <daily-working-info-table-print
              v-bind:detail-or-total="'detail'"
              v-bind:calc-lists="calcresults"
              v-bind:date-name="dateName"
              v-bind:predeter-time-name="predetertimename"
              v-bind:predeter-night-time-name="predeternighttimename"
              v-bind:predeter-time-second-name="predetertimesecondname"
              v-bind:predeter-night-time-second-name="predeternighttimesecondname"
              v-bind:btn-mode="btnmodeswitch"
              v-bind:login-user="get_LoginUserCode"
              v-bind:login-role="get_LoginUserRole"
              v-bind:account-data="authusers['account_id']"
              v-bind:menu-data="menudatas"
              v-bind:edituser-index="get_EditUserIndex"
              v-bind:edituser-con-index="get_EditUserConIndex"
            ></daily-working-info-table-print>
          </div>
          <!-- /panel body -->
        </div>
      </div>
      <!-- /.panel -->
      <!-- .panel -->
      <div class="col-md pt-3 align-self-stretch">
        <div class="card shadow-pl">
          <!-- panel header -->
          <div class="card-header bg-transparent pb-2 border-0">
              <h1 class="float-sm-left font-size-rg line-height-0-5 mb-0">合  計</h1>
          </div>
          <!-- /.panel header -->
          <div class="card shadow-pl" v-if="sumresults.length">
            <!-- panel body -->
            <div class="card-body mb-3 border-top">
              <!-- panel contents -->
              <!-- .row -->
              <!-- ----------- 日次集計合計テーブル START ---------------- -->
              <daily-working-info-sum-table
                v-bind:detail-or-total="'total'"
                v-bind:calc-lists="sumresults"
                v-bind:predeter-time-name="predetertimename"
                v-bind:predeter-night-time-name="predeternighttimename"
                v-bind:predeter-time-secondname="predetertimesecondname"
                v-bind:predeter-night-time-secondname="predeternighttimesecondname"
                v-bind:btn-mode="btnmodeswitch"
              ></daily-working-info-sum-table>
              <!-- ----------- 日次集計合計テーブル END ---------------- -->
              <!-- /.row -->
              <!-- /panel contents -->
            </div>
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
import { dialogable } from "../mixins/dialogable.js";
import { checkable } from "../mixins/checkable.js";
import { requestable } from "../mixins/requestable.js";

// CONST
const CONST_C005 = 'C005';
const CONST_C025 = 'C025';
const CONST_C025_ADMINUSER_PHYSICAL_NAME= "admin_user";
const CONST_MENU_EDITUSER_PHYSICAL_NAME= "edit_worktime_user";
const CONST_MENU_EDITUSER_CON_PHYSICAL_NAME= "edit_worktime_user_conditional";
const CONST_CALCLIST_ALLSELECT_ITEM_CODE = 8;

export default {
  name: "dailyworkingtime",
  mixins: [dialogable, checkable, requestable],
  props: {
    authusers: {
        type: Array,
        default: []
    },
    edition: {
      type: String,
      default: ""
    },
    menudatas: {
        type: Array,
        default: []
    },
    feature_item_selections: {
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
      selectmode: "",
      // messageshowsearch: false,
      issearchbutton: false,
      isgosubdatebutton: false,
      isgoadddatebutton: false,
      btnmodeswitch: "basicswitch",
      isswitchbutton: false,
      isswitchvisible: false,
      validate: true,
      initialized: false,
      general_C025_data: [],
      general_C005_data: [],
      isUserblank: true,
      login_user_code: "",
      login_user_role: "",
      adminuserrole: "",
      isdefault: true,
      isAllselect: false,
      isSetAllselect: false
    };
  },
  computed: {
    get_C005: function() {
      let $this = this;
      var i = 0;
      this.const_generaldatas.forEach( function( item ) {
        if (item.identification_id == CONST_C005) {
          $this.general_C005_data.push($this.const_generaldatas[i]);
        }
        i++;
      });    
      return this.general_C005_data;
    },
    get_C025: function() {
      let $this = this;
      var i = 0;
      this.const_generaldatas.forEach( function( item ) {
        if (item.identification_id == CONST_C025) {
          $this.general_C025_data.push($this.const_generaldatas[i]);
        }
        i++;
      });    
      return this.general_C025_data;
    },
    get_AdminUserRole: function() {
      if (this.adminuserrole == null || this.adminuserrole == "") {
        if (this.general_C025_data.length == 0) {
          let $this = this;
          this.get_C025.forEach( function( item ) {
            if (item.physical_name == CONST_C025_ADMINUSER_PHYSICAL_NAME) {
              $this.adminuserrole = item.code;
            }
          });    
        } else {
          let $this = this;
          this.general_C025_data.forEach( function( item ) {
            if (item.physical_name == CONST_C025_ADMINUSER_PHYSICAL_NAME) {
              $this.adminuserrole = item.code;
            }
          });    
        }
      }
      return this.adminuserrole;
    },
    get_IsEdit: function() {
      this.isEdit = false;
      if (this.authusers['role'] == this.get_AdminUserRole) {
        this.isEdit = true;
      }
      return this.isEdit;
    },
    get_IsUserblank: function() {
      this.isUserblank = true;
      var isAllselectValue = this.get_CalcListAllselectValue;
      if ((!isAllselectValue) && (this.get_LoginUserRole < this.get_AdminUserRole)) {
        this.isUserblank = false;
      }
      return this.isUserblank;
    },
    get_LoginUserCode: function() {
      this.login_user_code = this.authusers['code'];
      return this.login_user_code;
    },
    get_LoginUserRole: function() {
      this.login_user_role = this.authusers['role'];
      return this.login_user_role;
    },
    get_SelectedUserCode: function() {
      if (this.isdefault) {
        if (this.selectedUserValue == null || this.selectedUserValue == "") {
          var isAllselectValue = this.get_CalcListAllselectValue;
          if ((!isAllselectValue) && (this.get_LoginUserRole < this.get_AdminUserRole)) {
            this.selectedUserValue = this.get_LoginUserCode;
          }
        }
      }
      this.isdefault = false;
      return this.selectedUserValue;
    },
    get_EditUserIndex: function() {
      var edituser_index = 0;
      if (this.menudatas.length > 0) {
        let $this = this;
        var i = 0;
        this.menudatas.forEach( function( item ) {
          if (item.item_name == CONST_MENU_EDITUSER_PHYSICAL_NAME) {
            edituser_index = i;
          }
          i++;
        });
      }
      return edituser_index;
    },
    get_EditUserConIndex: function() {
      var edituser_con_index = 0;
      if (this.feature_item_selections.length > 0) {
        let $this = this;
        var i = 0;
        this.feature_item_selections.forEach( function( item ) {
          if (item.item_name == CONST_MENU_EDITUSER_CON_PHYSICAL_NAME) {
            edituser_con_index = i;
          }
          i++;
        });
      }
      return edituser_con_index;
    },
    get_CalcListAllselectValue: function() {
      if (this.isSetAllselect) { return this.isAllselect; }
      var isAllselectValue = false;
      if (this.feature_item_selections.length > 0) {
        this.isSetAllselect = true;
        let $this = this;
        this.feature_item_selections.forEach( function( item ) {
          if (item.item_code == CONST_CALCLIST_ALLSELECT_ITEM_CODE) {
            if (item.value_select != null && item.value_select != "") {
              if (item.value_select == "1") {
                isAllselectValue = true;
              }
            }
          }
        });
      }
      this.isAllselect = isAllselectValue;
      return isAllselectValue;
    }
  },
  // マウント時
  mounted() {
    // メソッドで使用するのでcomputedでなくてOK
    this.get_C025;
    this.valuedate = this.defaultDate;
    this.valuefromdate = moment(this.defaultDate).format("YYYYMMDD");
    this.valuesubadddate = this.valuefromdate;
    this.applytermdate = "";
    if (this.valuefromdate) {
      this.applytermdate = this.valuefromdate;
    }
    // this.$refs.selectdepartmentlist.getList(this.applytermdate);
    // this.getUserSelected();
    this.selectmode = "";
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
      if ((!this.get_CalcListAllselectValue) && (this.get_LoginUserRole < this.get_AdminUserRole)) {
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
      if (this.get_LoginUserRole >= this.get_AdminUserRole) {
        this.$refs.selectdepartmentlist.getList(this.applytermdate);
      }
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
      this.isswitchbutton = false;
      this.selectmode = "";
      if (this.validate) {
        this.issearchbutton = true;
        // this.messageshowsearch = true;
        // 入力項目クリア
        this.itemClear();
        this.valuesubadddate = moment(this.valuedate).format("YYYYMMDD");
        this.selectedName = this.user_name + "　" + moment(this.valuesubadddate).format("YYYY年MM月DD日") + "分勤怠編集" ;
        this.getItem(moment(this.valuedate).format("YYYYMMDD"));
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
      // this.messageshowsearch = true;
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
      // this.messageshowsearch = true;
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
    //印刷ボタンクリック処理
    printclick(e) {
      this.btnMode = "basicswitch";
      window.print();
    },
    
    // ------------------------ サーバー処理 ----------------------------
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
              // this.messageshowsearch = false;
              this.issearchbutton = false;
              this.serverCatch("日次集計","取得");
              this.isgosubdatebutton = true;
              this.isgoadddatebutton = true;
              this.selectmode = "DSP";
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
      // this.messageshowsearch = false;
      this.issearchbutton = false;
      this.isgosubdatebutton = true;
      this.isgoadddatebutton = true;
      this.selectmode = "DSP";
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + "情報" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
      this.selectmode = "";
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
<style scoped>
@page {
  size: auto;
}

@media print {
  .printdailypages{
      /* page : pageauto; */
      page-break-after: always;
      /* zoom: 85% !important; */
  }
  /*最後のページは改ページを入れない*/
  .printdailypages:last-child{
      page-break-after: auto;
  }
}
/* .print_pages{ */
/*A4横*/
  /* width: 297mm;
  height: 210mm;
  page-break-before: always;
} */
/*最後のページは改ページを入れない*/
.print_pages:last-child{
    page-break-after: auto;
}

.table th, .table td {
    padding: 0.4rem !important;
}

.mw-rem-3 {
  min-width: 3rem !important;
}

.line-height-0-5 {
  line-height: 0.5;
}

</style>
