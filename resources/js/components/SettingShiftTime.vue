<template>
  <!--------------------------------------------- 未使用 --------------------------------------->
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- ========================== 検索部 START ========================== -->
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'シフトの割り当てを編集する'"
            v-bind:header-text2="'勤務時間設定で登録したタイムテーブルを割り当てることができます'"
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
                      class="input-group-text font-size-sm line-height-xs label-width-200"
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
                      class="input-group-text font-size-sm line-height-xs label-width-200"
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
                    <span
                    class="input-group-text font-size-sm line-height-xs label-width-200"
                    id="basic-addon1"
                    >所属部署<font color="blue">[登録時必須]</font></span>
                  </div>
                  <select-departmentlist v-if="showdepartmentlist"
                    ref="selectdepartmentlist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'部署を選択してください'"
                    v-bind:selected-department="selectedDepartmentValue"
                    v-bind:add-new="false"
                    v-bind:date-value="applytermdate"
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
                    class="input-group-text font-size-sm line-height-xs label-width-200"
                    for="target_users"
                    >氏名<span class="color-red">[必須]</span></label>
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
                    v-bind:employment-value="''"
                    v-on:change-event="userChanges"
                  ></select-userlist>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                    class="input-group-text font-size-sm line-height-xs label-width-200"
                    id="basic-addon1"
                    >タイムテーブル<font color="blue">[登録時必須]</font></span>
                  </div>
                  <select-timetablelist v-if="showtimetablelist"
                    ref="selecttimetablelist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'タイムテーブルを選択してください'"
                    v-bind:setting-value="selectedTimetableValue"
                    v-bind:add-new="false"
                    v-bind:date-value="''"
                    v-bind:kill-value="valueTimetablekillcheck"
                    v-bind:row-index=0
                    v-bind:set-shift="false"
                    v-on:change-event="timetableChanges"
                  ></select-timetablelist>
                </div>
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
                  v-on:searchclick-event="searchClick"
                  v-bind:btn-mode="'search'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:condstoreclick-event="condstoreClick"
                  v-bind:btn-mode="'condstore'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- ボタン部 END ---------------- -->
            <!-- /.row -->
            <!-- /.panel contents -->
          </div>
        </div>
      </div>
      <!-- /.panel -->
      <!-- ========================== 検索部 END ========================== -->
    </div>
    <!-- /main contentns row -->
    <!-- ========================== 編集部 START ========================== -->
    <!-- main contentns row -->
    <div class="row justify-content-between" v-if="details.length ">
      <!-- .panel -->
      <div class="col-md pt-3 align-self-stretch">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'登録済みシフト'"
            v-bind:header-text2="'シフト割り当てされたタイムテーブルの一覧です'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- panel body -->
          <div class="card-body mb-3 p-0 border-top">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table table-striped border-bottom font-size-sm text-nowrap">
                    <thead>
                      <tr>
                        <td class="text-center align-middle">日付</td>
                        <td class="text-center align-middle">タイムテーブル</td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="item in details" v-bind:key="item.id">
                        <input type="hidden" v-model="item.id" />
                        <td class="text-center align-middle">{{item.date_name}}</td>
                        <td class="text-center align-middle">{{item.working_timetable_name}}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <!-- /.row -->
            <!-- /panel contents -->
          </div>
          <!-- /panel body -->
        </div>
        <!-- .panel -->
      </div>
      <!-- /main contentns row -->
    </div>
    <!-- ========================== 編集部 END ========================== -->
  </div>
</template>
<script>
import toasted from "vue-toasted";
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "SettingShiftTime",
  mixins: [ dialogable, checkable, requestable ],
  data() {
    return {
      getDo : 1,
      selectedDepartmentValue : "",
      valueDepartmentkillcheck : false,
      showdepartmentlist: true,
      selectedUserValue : "",
      showuserlist: true,
      valueUserkillcheck : false,
      selectedTimetableValue : "",
      valueTimetablekillcheck : false,
      showtimetablelist: true,
      valuefromdate: "",
      valuetodate: "",
      applytermdate: "",
      getDo : 1,
      details: [],
      messagevalidatesSearch: [],
      closingYm: "",
      closingYmd: "",
      DatePickerFormat: "yyyy年MM月dd日",
      defaultfromDate: new Date(),
      defaulttoDate: new Date(),
      dt: "",
      shift_informations: {
        working_timetable_no: "",
        user_code: "",
        department_code: "",
        target_date_from: "",
        target_date_to: ""
      },
    };
  },
  // マウント時
  mounted() {
    this.valuefromdate = this.defaultfromDate;
    this.valuetodate = this.defaulttoDate;
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // 検索時のバリデーション
    checkFormSearch: function() {
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
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '終了日付';
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
      chkArray = 
        this.checkHeader(this.selectedUserValue, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesSearch.length == 0) {
          this.messagevalidatesSearch = chkArray;
        } else {
          this.messagevalidatesSearch = this.messagevalidatesSearch.concat(chkArray);
        }
      }
      if (this.messagevalidatesSearch.length > 0) {
        flag  = false;
      }
      if (flag) {
        if (this.valuefromdate > this.valuetodate) {
          flag = false;
          this.messagevalidatesSearch.push("開始日付＞終了日付となっています");
        }
      }
      if (this.messagevalidatesSearch.length > 0) {
        flag  = false;
      }

      return flag;
    },
    // 登録時のバリデーション
    checkFormStore: function() {
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
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '終了日付';
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
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = '部署';
      chkArray = 
        this.checkHeader(this.selectedDepartmentValue, required, equalength, maxlength, itemname);
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
      chkArray = 
        this.checkHeader(this.selectedUserValue, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesSearch.length == 0) {
          this.messagevalidatesSearch = chkArray;
        } else {
          this.messagevalidatesSearch = this.messagevalidatesSearch.concat(chkArray);
        }
      }
      // タイムテーブル
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = 'タイムテーブル';
      chkArray = 
        this.checkHeader(this.selectedTimetableValue, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesSearch.length == 0) {
          this.messagevalidatesSearch = chkArray;
        } else {
          this.messagevalidatesSearch = this.messagevalidatesSearch.concat(chkArray);
        }
      }
      if (this.messagevalidatesSearch.length > 0) {
        flag  = false;
      }
      if (flag) {
        if (this.valuefromdate > this.valuetodate) {
          flag = false;
          this.messagevalidatesSearch.push("開始日付＞終了日付となっています");
        }
      }
      if (this.messagevalidatesSearch.length > 0) {
        flag  = false;
      }

      return flag;
    },
    // ------------------------ イベント処理 ------------------------------------
    
    // 開始日付が変更された場合の処理
    fromdateChanges: function(value) {
      this.valuefromdate = value;
    },
    // 開始日付がクリアされた場合の処理
    fromdateCleared: function() {
      this.valuefromdate = "";
    },
    // 終了日付が変更された場合の処理
    fromtoChanges: function(value, arrayitem) {
      this.valuetodate = value;
    },
    // 終了日付がクリアされた場合の処理
    fromtoCleared: function() {
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
    userChanges: function(value, arrayitem) {
      this.messagevalidatesSearch = [];
      this.selectedUserValue = value;
    },
    // タイムテーブル選択が変更された場合の処理
    timetableChanges: function(value, arrayitem) {
      this.messagevalidatesSearch = [];
      this.selectedTimetableValue = value;
    },
    // 検索ボタン押下
    searchClick: function() {
      var flag = this.checkFormSearch();
      if (flag) {
        this.getItem();
      }
    },
    // 登録ボタン押下
    condstoreClick() {
      var flag = this.checkFormStore();
      if (flag) {
        this.getclosingItem();
      }
    },
    // 通常登録確認
    store_confirm: function() {
      var messages = [];
      messages.push("このデータで登録しますか？");
      this.htmlMessageSwal("確認", messages, "info", true, true)
        .then(result  => {
          if (result) {
            this.store("追加");
          }
      });
    },
    // 締日登録確認
    store_warniong_confirm: function(state) {
      var messages = [];
      messages.push("前月の締日" + moment(this.closingYmd).format('YYYY年MM月DD日') + "以前のデータが含まれますが、");
      messages.push("前月締日以前のデータは自動集計されません。");
      messages.push("月次集計での手動集計が必要となります。");
      messages.push("このデータで登録しますか？");
      this.htmlMessageSwal("確認", messages, "info", true, true)
        .then(result  => {
          if (result) {
            this.store("追加");
          }
      });
    },
    // -------------------- サーバー処理 ----------------------------
    // シフト取得処理
    getItem() {
      this.fromdate = ""
      if (this.valuefromdate) {
        this.fromdate = moment(this.valuefromdate).format("YYYYMMDD");
      }
      this.todate = ""
      if (this.valuetodate) {
        this.todate = moment(this.valuetodate).format("YYYYMMDD");
      }
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
            departmentcode : this.selectedDepartmentValue,
            usercode : this.selectedUserValue,
            no : this.selectedTimetableValue,
            from: this.fromdate,
            to: this.todate };
          this.postRequest("/get_user_shift", arrayParams)
            .then(response  => {
              this.$swal.close();
              this.getThen(response);
            })
            .catch(reason => {
              this.$swal.close();
              this.serverCatch("シフト", "取得");
          });
        }
      });
    },
    // 締日取得処理
    getclosingItem() {
      this.closingYm = moment(new Date()).subtract(1, 'M').format('YYYYMM');
      var arrayParams = { target_date: this.closingYm };
      this.postRequest("/get_closing_day", arrayParams)
        .then(response  => {
          this.getThenclosing(response);
        })
        .catch(reason => {
          this.serverCatch("ユーザ", "取得");
        });
    },
    // シフト登録処理
    store() {
      this.fromdate = ""
      if (this.valuefromdate) {
        this.fromdate = moment(this.valuefromdate).format("YYYYMMDD");
      }
      this.todate = ""
      if (this.valuetodate) {
        this.todate = moment(this.valuetodate).format("YYYYMMDD");
      }
      this.shift_informations['working_timetable_no'] = this.selectedTimetableValue;
      this.shift_informations['user_code'] = this.selectedUserValue;
      this.shift_informations['department_code'] = this.selectedDepartmentValue;
      this.shift_informations['shift_start_time'] = this.fromdate;
      this.shift_informations['shift_end_time'] = this.todate;
      var arrayParams = { form : this.shift_informations };
      this.postRequest("/setting_shift_time/store", arrayParams)
        .then(response  => {
          this.putThenHead(response, "登録");
        })
        .catch(reason => {
          this.serverCatch("シフト", "登録");
        });
    },

    // -------------------- 共通 ----------------------------
    // ユーザー選択コンポーネント取得メソッド
    getUserSelected: function() {
      this.$refs.selectuserlist.getList(
        '',
        this.valueUserkillcheck,
        this.getDo,
        this.selectedDepartmentValue,
        ''
      );
    },
    // 取得正常処理（シフト）
    getThen(response) {
      this.details = [];
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        if (this.details.length == 0) {
          var messages = [];
          messages.push("該当期間に登録されている期間はありません");
          this.htmlMessageSwal("エラー", messages, "error", true, false);
        }
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("シフト", "取得");
        }
      }
    },
    // 取得正常処理（締日）
    getThenclosing(response) {
      this.details = [];
      var res = response.data;
      if (res.result) {
        this.closingYmd =String(this.closingYm) + String(res.closing);
        this.dt = moment(this.valuefromdate).format('YYYYMMDD');
        if (this.closingYmd >= this.dt) {
          this.store_warniong_confirm();
        } else {
          this.store_confirm();
        }
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("締日", "取得");
        }
      }
    },
    // 登録正常処理
    putThenHead(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.$toasted.show("シフトを" + eventtext + "しました");
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch("シフト",eventtext);
        }
      }
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + "情報" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    }
  }
};
</script>
