<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'指定日付から過去１週間以内の警告を表示（確認して勤怠編集で修正入力します）'"
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
                <message-data
                  v-bind:message-datas="messagedatasfromdate"
                  v-bind:message-class="'warning'">
                </message-data>
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
              <div class="col-md-6 pb-2">
                <message-data-server v-bind:message-datas="messagedatasserver" v-bind:message-class="'warning'"></message-data-server>
              </div>
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
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3 align-self-stretch">
        <div class="card shadow-pl">
          <!-- panel body -->
          <!-- ----------- 項目部 START ---------------- -->
          <daily-working-alert-table
            v-if="showdailyworkingalerttable"
            ref="refdailyworkingalerttable"
            v-bind:alert-lists="details"
            v-bind:tablebody-height="'height: 400px !important;'"
            v-bind:is-edit="get_IsEdit"
            v-on:detaileditclick-event="detailEdtClick"
          ></daily-working-alert-table>
          <!-- ----------- 項目部 END ---------------- -->
          <!-- /panel body -->
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
          v-bind:heads="detailsEdt"
          v-bind:accountdatas="accountdatas"
          v-bind:halfautoset="get_IsAutoHalfSet"
          v-bind:feature-item-selections="feature_item_selections"
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
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

// CONST
const CONST_C025 = 'C025';
const CONST_C025_GENERALUSER_INDEX = 0;   // index
const CONST_C025_ADMINUSER_INDEX = 2;   // index
const CONST_HALF_HOLIDAY_SET_CODE = 2;
const CONST_INDEXORHOME_INDEX = 1;
const CONST_INDEXORHOME_HOME = 2;

export default {
  name: "dailyworkingtime",
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
    },
    indexorhome: {
        type: Number,
        default: 0
    }
  },
  data: function() {
    return {
      selectedDepartmentValue : "",
      valueDepartmentkillcheck : false,
      selectedUserValue : "",
      showuserlist: true,
      valueUserkillcheck : false,
      selectedEmploymentValue: "",
      getDo: 1,
      applytermdate: "",
      valuefromdate: "",
      DatePickerFormat: "yyyy年MM月dd日",
      defaultDate: new Date(),
      dateName: "",
      details: [],
      messagedatasserver: [],
      messagedatasfromdate: [],
      messagedatadepartment: [],
      messagedatauser: [],
      validate: false,
      selectMode: "",
      isEdit: false,
      detailsEdt: [],
      login_user_code: "",
      login_user_role: "",
      index_or_home: "",
      showeditworktimestable: true,
      showdailyworkingalerttable: true,
      const_C025_data: [],
      isAutoHalfSet: true
    };
  },
  computed: {
    get_C025: function() {
      console.log('get_C025 in');
      let $this = this;
      var i = 0;
      this.const_generaldatas.forEach( function( item ) {
        if (item.identification_id == CONST_C025) {
          console.log('get_C025 set');
          $this.const_C025_data.push($this.const_generaldatas[i]);
        }
        i++;
      });    
      return this.const_C025_data;
    },
    get_IsAutoHalfSet: function() {
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
    get_IsEdit: function() {
      this.get_C025;
      if (this.const_C025_data.length == 0) {
        this.get_C025;
      }
      this.isEdit = false;
      if (this.authusers['role'] == this.const_C025_data[CONST_C025_ADMINUSER_INDEX]['code']) {
        this.isEdit = true;
      }
      return this.isEdit;
    }
  },
  // マウント時
  mounted() {
    this.login_user_code = this.authusers['code'];
    this.login_user_role = this.authusers['role'];
    this.index_or_home = this.indexorhome;
    this.valuefromdate = this.defaultDate;
    moment.locale("ja");
    this.applytermdate = ""
    if (this.valuefromdate) {
      this.applytermdate = moment(this.valuefromdate).format("YYYYMMDD");
    }
    this.$refs.selectdepartmentlist.getList(this.applytermdate);
    this.getUserSelected();
    // 1:index 2:homeindex
    if (this.index_or_home == CONST_INDEXORHOME_HOME) {
      this.selectMode = '';
      this.itemClear();
      this.getItem();
      this.refreshDailyWorkingAlertTable();
    }
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション
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
      if (this.login_user_role < "8") {
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
      this.valuefromdate = value;
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
      this.selectMode = '';
      this.validate = this.checkForm(e);
      if (this.validate) {
        this.itemClear();
        this.getItem();
      }
      this.refreshDailyWorkingAlertTable();
    },
    // 明細編集ボタンクリックされた場合の処理
    detailEdtClick: function(e, arrayitem) {
      var index = arrayitem['rowIndex'];
      this.selectMode = 'EDT';
      this.detailsEdt = this.details[index];
      this.refreshEdtWorkingTimesTable();
    },
    // キャンセルボタンクリックされた場合の処理
    cancelClick: function(e, arrayitem) {
      this.selectMode = '';
      this.refreshDailyWorkingAlertTable();
    },
    // ------------------------ サーバー処理 ----------------------------
    // 日次警告取得処理
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
          var arrayParams = {
            alert_form_date : moment(this.valuefromdate).format("YYYYMMDD"),
            employmentstatus : this.selectedEmploymentValue,
            departmentcode : this.selectedDepartmentValue,
            usercode : this.selectedUserValue
          };
          this.postRequest("/daily_alert/show", arrayParams)
            .then(response  => {
              this.$swal.close();
              this.getThen(response);
            })
            .catch(reason => {
              this.$swal.close();
              this.serverCatch("日次警告", "取得");
            });
        }
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
    // 取得正常処理（アラート）
    getThen(response) {
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        this.dateName = res.datename;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("日次警告", "取得");
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
    itemClear: function() {
      this.details = [];
      this.messagedatasserver = [];
      this.messagedatasfromdate = [];
      this.messagedatadepartment = [];
      this.messagedatauser = [];
    },
    refreshEdtWorkingTimesTable() {
      // 最新リストの表示
      this.showeditworktimestable = false;
      this.$nextTick(() => (this.showeditworktimestable = true));
    },
    refreshDailyWorkingAlertTable() {
      // 最新リストの表示
      this.showdailyworkingalerttable = false;
      this.$nextTick(() => (this.showdailyworkingalerttable = true));
    },
  }
};
</script>
