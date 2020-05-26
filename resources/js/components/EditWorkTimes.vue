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
            v-bind:header-text1="'年月日を指定して勤怠時刻を表示する'"
            v-bind:header-text2="'雇用形態や所属部署でフィルタリングして表示できます'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- panel body -->
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
                    >指定日付<span class="color-red">[必須]</span></span>
                  </div>
                  <input-datepicker
                    v-bind:default-date="valuedate"
                    v-bind:date-format="'yyyy年MM月dd日'"
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
                    <label
                    class="input-group-text font-size-sm line-height-xs label-width-150"
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
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >所属部署</span>
                  </div>
                  <select-departmentlist v-if="showdepartmentlist"
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
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      for="target_users"
                    >氏 　名<span class="color-red">[必須]</span></label>
                  </div>
                  <select-userlist v-if="showuserlist"
                    ref="selectuserlist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'氏名を選択してください'"
                    v-bind:selected-value="selectedUserValue"
                    v-bind:add-new="false"
                    v-bind:get-do="1"
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
            </div>
            <!-- /.row -->
            <!-- ----------- 選択リスト END ---------------- -->
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
          </div>
        </div>
      </div>
      <!-- /.panel -->
      <!-- ========================== 編集部 START ========================== -->
      <!-- .panel -->
      <div class="col-md-12 pt-3" v-if="selectMode=='EDT'">
        <edit-work-times-table
          v-if="showeditworktimestable"
          ref="refeditworktimestable"
          v-bind:authusers="authusers"
          v-bind:const_c025="get_C025"
          v-bind:const_generaldatas="const_generaldatas"
          v-bind:heads="heads"
          v-bind:accountdatas="accountdatas"
          v-bind:halfautoset="get_AutoHalfSet"
          
        >
        </edit-work-times-table>
      </div>
      <!-- /.panel -->
      <!-- ========================== 編集部 END =========================== -->
    </div>
    <!-- /main contentns row -->
  </div>
</template>
<script>
import Datepicker from "vuejs-datepicker";
import { ja } from "vuejs-datepicker/dist/locale";
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

// CONST
const CONST_C025 = 'C025';
const CONST_C025_GENERALUSER_INDEX = 0;   // index
const CONST_C025_ADMINUSER_INDEX = 2;   // index
const CONST_HALF_HOLIDAY_SET_CODE = 2;

export default {
  name: "EditWorkTimes",
  mixins: [ dialogable, checkable, requestable ],
  props: {
    accountdatas: {
        type: Array,
        default: []
    },
    authusers: {
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
  data() {
    return {
      defaultDate: new Date(),
      valuedate: "",
      date_name: "",
      selectedEmploymentValue: "",
      selectedDepartmentValue : "",
      showdepartmentlist: true,
      valueDepartmentkillcheck : false,
      department_name: "",
      selectedUserValue : "",
      valueUserkillcheck : false,
      showuserlist: true,
      user_name: "",
      selectMode: "",
      applytermdate: "",
      getDo: 0,
      selectedName: "",
      valuefromdate: "",
      valuesubadddate: "",
      messagevalidatesSearch: [],
      issearchbutton: false,
      ja: ja,
      default: "2019/10/24",
      validate: false,
      heads: [],
      login_user_code: "",
      login_user_role: "",
      showeditworktimestable: true,
      accountdata_data: [],
      const_C025_data: [],
      isAutoHalfSet: true
    };
  },
  components: {
    Datepicker
  },
  computed: {
    get_AutoHalfSet: function() {
      this.isAutoHalfSet = false;
      let $this = this;
      this.feature_item_selections.forEach( function( item ) {
        if (item.item_code == CONST_HALF_HOLIDAY_SET_CODE) {
          if (item.value_select == 1) {
            $this.isAutoHalfSet = true;
          } else {
            $this.isAutoHalfSet = false;
          }
          return $this.isAutoHalfSet;
        }
      });

      return this.isAutoHalfSet;
    },
    get_Account: function() {
      let $this = this;
      this.accountdata_data = [];
      this.accountdatas.forEach( function( item ) {
        $this.accountdata_data.push($this.accountdatas[i]);
        return this.accountdata_data;
      });    
      return this.accountdata_data;
    },
    get_C025: function() {
      let $this = this;
      var i = 0;
      this.const_generaldatas.forEach( function( item ) {
        if (item.identification_id == CONST_C025) {
          $this.const_C025_data.push($this.const_generaldatas[i]);
        }
        i++;
      });    
      return this.const_C025_data;
    }
  },
  // マウント時
  mounted() {
    this.login_user_code = this.authusers['code'];
    this.login_user_role = this.authusers['role'];
    if (this.login_user_role == this.const_C025_data[CONST_C025_ADMINUSER_INDEX]) {
      this.isEdit = true;
    }
    this.valuedate = this.defaultDate;
    this.valuefromdate = moment(this.defaultDate).format("YYYYMMDD");
    this.valuesubadddate = this.valuefromdate;
    this.date_name = moment(this.defaultDate).format("YYYY年MM月DD日");
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション（検索）
    checkFormSearch: function() {
      this.messagevalidatesSearch = [];
      var chkArray = [];
      var flag = true;
      // 氏名
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '氏名';
      chkArray = 
        this.checkHeader(this.selectedUserValue, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesSearch.length == 0) {
          this.messagevalidatesSearch = chkArray;
        } else {
          this.messagevalidatesSearch = this.messagevalidatesSearch.concat(chkArray);
        }
      }
      // 指定日付
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = '指定日付';
      chkArray = 
        this.checkHeader(this.valuedate, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesSearch.length == 0) {
          this.messagevalidatesSearch = chkArray;
        } else {
          this.messagevalidatesSearch = this.messagevalidatesSearch.concat(chkArray);
        }
      }
      if (this.messagevalidatesSearch.length > 0) {
        flag = false;
      }
      return flag;
    },
    // ------------------------ イベント処理 ------------------------------------
    
    // 指定年月が変更された場合の処理
    fromdateChanges: function(value) {
      this.valuedate = value;
      this.valuefromdate = moment(value).format("YYYYMMDD");
      this.valuesubadddate = this.valuefromdate;
      this.date_name = moment(value).format("YYYY年MM月DD日");
      this.selectedName = this.user_name + "　" + this.date_name + "分勤怠編集" ;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.selectedEmploymentValue = "";
      this.selectedDepartmentValue = "";
      this.selectedUserValue = "";
      this.getDo = 1;
      this.applytermdate = this.valuefromdate;
      this.getDepartmentSelected();
      this.getUserSelected();
      this.selectMode = '';
    },
    // 指定年月がクリアされた場合の処理
    fromdateCleared: function() {
      this.valuedate = "";
      this.valuefromdate = "";
      this.valuesubadddate = "";
      this.applytermdate = "";
      this.date_name = "";
      this.selectedName = this.user_name + "　" + this.date_name + "分勤怠編集" ;
    },
    // 雇用形態が変更された場合の処理
    employmentChanges: function(value) {
      this.selectedEmploymentValue = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.selectedUserValue = "";
      this.selectMode = '';
      this.getDo = 1;
      this.getUserSelected();
    },
    // 部署選択が変更された場合の処理
    departmentChanges: function(value, arrayitem) {
      this.selectedDepartmentValue = value;
      this.department_name = arrayitem['name'];
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.selectedUserValue = "";
      this.selectMode = '';
      this.getDo = 1;
      this.getUserSelected();
    },
    // ユーザー選択が変更された場合の処理
    userChanges: function(value, arrayitem) {
      this.selectedUserValue = value;
      this.user_name = arrayitem['name'];
      this.selectedName = this.user_name + "　" + this.date_name + "分勤怠編集" ;
      this.selectMode = '';
    },
    // 表示ボタンクリック処理
    searchclick() {
      this.messagevalidatesSearch = [];
      this.valuesubadddate = "";
      if (this.checkFormSearch()) {
        this.selectMode = 'EDT';
        this.valuesubadddate = moment(this.valuedate).format("YYYYMMDD");
        this.selectedName = this.user_name + "　" + moment(this.valuesubadddate).format("YYYY年MM月DD日") + "分勤怠編集" ;
        this.heads = {
          user_code : this.selectedUserValue,
          user_name : this.user_name,
          department_code : this.selectedDepartmentValue,
          department_name : this.department_name,
          record_date_name : moment(this.valuesubadddate).format("YYYY年MM月DD日"),
          current_record_date : moment(this.valuedate).format("YYYYMMDD")
        }
      }
      this.refeditworktimestable();
    },
    // -------------------- サーバー処理 ----------------------------

    // -------------------- 共通 ----------------------------
    // 部署選択コンポーネント取得メソッド
    getDepartmentSelected: function() {
      this.$refs.selectdepartmentlist.getList(
        this.applytermdate
      );
      this.refreshDepartmentList();
    },
    // ユーザー選択コンポーネント取得メソッド
    getUserSelected: function() {
      this.$refs.selectuserlist.getList(
        this.applytermdate,
        this.valueUserkillcheck,
        this.getDo,
        this.selectedDepartmentValue,
        this.selectedEmploymentValue
      );
      this.refreshUserList();
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    refreshDepartmentList() {
      // 最新リストの表示
      this.showdepartmentlist = false;
      this.$nextTick(() => (this.showdepartmentlist = true));
    },
    refreshUserList() {
      // 最新リストの表示
      this.showuserlist = false;
      this.$nextTick(() => (this.showuserlist = true));
    },
    refeditworktimestable() {
      // 最新リストの表示
      this.showeditworktimestable = false;
      this.$nextTick(() => (this.showeditworktimestable = true));
    }
  }
};
</script>
