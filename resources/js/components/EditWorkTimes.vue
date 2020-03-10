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
                    v-bind:employment-value="''"
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
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆勤怠編集'"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- ----------- 「＋」アイコン部 START ---------------- -->
          <!-- panel header -->
          <div class="card-header bg-transparent pt-3 border-0">
            <h1 class="float-sm-left font-size-rg">
              <span>
                <button class="btn btn-success btn-lg font-size-rg" v-on:click="appendRowClick">+</button>
              </span>
              {{ this.selectedName }}
            </h1>
            <span class="float-sm-right font-size-sm">「＋」アイコンで新規に追加することができます</span>
          </div>
          <!-- /.panel header -->
          <!-- ----------- 「＋」アイコン部 END ---------------- -->
          <!-- ----------- 編集入力部 START ---------------- -->
          <!-- main contentns row -->
          <div class="card-body pt-2">
            <!-- ----------- 選択ボタン類 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-3 pb-2">
                <btn-work-time
                  v-on:gosubateclick-event="gosubateclick"
                  v-bind:btn-mode="'gosubdate'"
                  v-bind:is-push="isgosubdatebutton"
                ></btn-work-time>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-md-3 pb-2">
                <btn-work-time
                  v-on:goaddateclick-event="goaddateclick"
                  v-bind:btn-mode="'goadddate'"
                  v-bind:is-push="isgoadddatebutton"
                ></btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- 選択ボタン類 END ---------------- -->
          </div>
          <div class="card-body pt-2" v-if="details.length">
            <!-- ----------- メッセージ部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between" v-if="messagevalidatesEdt.length">
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
                              <td class="text-center align-middle w-10">No.</td>
                              <td class="text-center align-middle w-30">勤怠モード</td>
                              <td class="text-center align-middle w-30">時刻</td>
                              <td class="text-center align-middle w-35 mw-rem-10">休暇</td>
                              <td colspan="2" class="text-center align-middle w-35 mw-rem-10">操作</td>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="(item,index) in details" v-bind:key="item.id">
                              <td class="text-right align-middle">
                                <div class="input-group">
                                  <label>{{ index+1 }}</label>
                                </div>
                              </td>
                              <td class="text-center align-middle">
                                <div class="input-group">
                                  <select
                                    class="custom-select"
                                    v-model="details[index].mode"
                                    @change="changeMode(index)"
                                  >
                                    <option value></option>
                                    <option
                                      v-for="mlist in generalList_c005"
                                      :value="mlist.code"
                                      v-bind:key="mlist.code"
                                    >{{ mlist.code_name }}</option>
                                  </select>
                                </div>
                              </td>
                              <td class="text-center align-middle">
                                <div class>
                                  <input
                                    type="time"
                                    class="form-control"
                                    v-model="details[index].time"
                                    @change="changeTime(index)"
                                  />
                                </div>
                              </td>
                              <td class="text-center align-middle" v-if="index==0">
                                <div class="input-group">
                                  <div class="input-group-prepend">
                                    <label
                                      class="input-group-text font-size-sm line-height-xs label-width-90"
                                      for="inputGroupSelect01"
                                    >休暇区分</label>
                                  </div>
                                  <select
                                    class="form-control"
                                    v-model="details[index].user_holiday_kbn"
                                    @change="changeHolidayKbn(index)"
                                  >
                                    <option value></option>
                                    <option
                                      v-for="list in generalList_c013"
                                      :value="list.code"
                                    >{{ list.code_name }}</option>
                                  </select>
                                </div>
                              </td>
                              <td v-else></td>
                              <td class="text-center align-middle">
                                <div class="btn-group" v-if="details[index].id != ''">
                                  <button
                                    type="button"
                                    class="btn btn-success"
                                    @click="fixclick(index)"
                                  >この内容で更新する</button>
                                </div>
                                <div class="btn-group" v-if="details[index].id == ''">
                                  <button
                                    type="button"
                                    class="btn btn-success"
                                    @click="addClick(index)"
                                  >この内容で追加する</button>
                                </div>
                              </td>
                              <td class="text-center align-middle">
                                <div class="btn-group" v-if="details[index].id != ''">
                                  <button
                                    type="button"
                                    class="btn btn-danger"
                                    @click="delClick(index)"
                                  >この内容を削除する</button>
                                </div>
                                <div class="btn-group" v-if="details[index].id == ''">
                                  <button
                                    type="button"
                                    class="btn btn-danger"
                                    @click="rowDelClick(index)"
                                  >行削除</button>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <!-- ----------- 項目table部 START ---------------- -->
                    </div>
                  </div>
                  <!-- /.row -->
                  <!-- /.panel contents -->
                </div>
                <!-- /panel body -->
              </div>
            </div>
            <!-- ----------- 項目部 END ---------------- -->
            <!-- ----------- 選択ボタン類 START ---------------- -->
            <!-- .row -->
            <!-- <div class="row justify-content-between">
              <!-- col -->
              <!-- <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:editfixclick-event="editfixclick"
                  v-bind:btn-mode="'editfix'"
                  v-bind:is-push="issearchbutton"
                ></btn-work-time>
              </div>-->
              <!-- /.col -->
            <!-- </div> -->
            <!-- /.row -->
            <!-- /.panel contents -->
            <!-- ----------- 選択ボタン類 END ---------------- -->
          </div>
          <!-- /main contentns row -->
          <!-- ----------- 編集入力部 END ---------------- -->
          <!-- /panel body -->
        </div>
      </div>
      <!-- /.panel -->
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

export default {
  name: "EditWorkTimes",
  mixins: [ dialogable, checkable, requestable ],
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
      generalList_c005: [],
      generalList_c013: [],
      count: 0,
      before_count: 0,
      valuefromdate: "",
      valuesubadddate: "",

      valueuser: "",
      messagevalidatesSearch: [],
      messagevalidatesEdt: [],

      messageshowsearch: [],
      messagedatasfromdate: [],
      issearchbutton: false,
      isgosubdatebutton: false,
      isgoadddatebutton: false,
      valueBusinessDay: "",
      valueholiDay: "",
      year: "",
      month: "",
      selectMonth: "",
      baseYear: "",
      userLeaveKbnList: [],
      details: [],
      addDate: "",
      addTime: "",
      addMode: "",
      addKbn: "",
      ja: ja,
      default: "2019/10/24",
      DatePickerFormat: "yyyy/MM/dd",
      validate: false,
      errors: [],
      modeList: []
    };
  },
  components: {
    Datepicker
  },
  // マウント時
  mounted() {
    this.valuedate = this.defaultDate;
    this.valuefromdate = moment(this.defaultDate).format("YYYYMMDD");
    this.valuesubadddate = this.valuefromdate;
    this.date_name = moment(this.defaultDate).format("YYYY年MM月DD日");
    this.getGeneralList("C005");
    this.getGeneralList("C013");
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
    // バリデーション（更新）
    checkFormFix: function(index) {
      this.messagevalidatesEdt = [];
      var chkArray = [];
      var flag = true;
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '';
      // 勤怠モードと時刻休暇
      var isrow = this.checkRowData(index);
      if (!isrow) {
        this.messagevalidatesEdt.push("No." + index+1 + "の" + "勤怠モード・時刻または休暇を入力してください");
      } else {
        if (this.details[index].user_holiday_kbn == "" || this.details[index].user_holiday_kbn == null) {
          // 勤怠モードと時刻
          if (this.details[index].mode != "" && this.details[index].mode != null) {
            required = true;
            equalength = 0;
            maxlength = 0;
            itemname = '時刻';
            chkArray = 
              this.checkDetail(this.details[index].time, required, equalength, maxlength, itemname, index+1) ;
            if (chkArray.length > 0) {
              if (this.messagevalidatesEdt.length == 0) {
                this.messagevalidatesEdt = chkArray;
              } else {
                this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
              }
            }
          } else {
            required = true;
            equalength = 0;
            maxlength = 0;
            itemname = '勤怠モード';
            chkArray =
              this.checkDetail(this.details[index].mode, required, equalength, maxlength, itemname, index+1);
            if (chkArray.length > 0) {
              if (this.messagevalidatesEdt.length == 0) {
                this.messagevalidatesEdt = chkArray;
              } else {
                this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
              }
            }
          }
        //} else {
          //this.details[index].mode = "";
          //this.details[index].time = "";
        }
      }
      if (this.messagevalidatesEdt.length > 0) {
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
      this.getDo = 1;
      this.applytermdate = this.valuefromdate;
      this.getDo = 1;
      this.getUserSelected();
      this.selectMode = '';
    },
    // 部署選択が変更された場合の処理
    departmentChanges: function(value, arrayitem) {
      this.selectedDepartmentValue = value;
      this.department_name = arrayitem['name'];
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.selectedUserValue = "";
      this.getDo = 1;
      this.getUserSelected();
      this.selectMode = '';
    },
    // ユーザー選択が変更された場合の処理
    userChanges: function(value, arrayitem) {
      this.selectedUserValue = value;
      this.user_name = arrayitem['name'];
      this.selectedName = this.user_name + "　" + this.date_name + "分勤怠編集" ;
      this.selectMode = '';
    },
    // 勤怠モードが変更された場合の処理
    changeMode: function(index) {
      /*if ((this.details[index].mode != "" && this.details[index].mode != null) &&
        (this.details[index].time != "" && this.details[index].time != null)){
        this.details[index].user_holiday_kbn = "";
        this.details[index].kbn_flag = 0;
      } */
    },
    // 時間が変更された場合の処理
    changeTime: function(index) {
      /*if ((this.details[index].mode != "" && this.details[index].mode != null) &&
        (this.details[index].time != "" && this.details[index].time != null)){
        this.details[index].user_holiday_kbn = "";
        this.details[index].kbn_flag = 0;
      } */
    },
    // 休暇区分が変更された場合の処理
    changeHolidayKbn: function(index) {
      if (this.details[index].user_holiday_kbn != "" && this.details[index].user_holiday_kbn != null) {
      //  this.details[index].mode = "";
       // this.details[index].time = "";
        this.details[index].kbn_flag = 1;
      } else {
        this.details[index].kbn_flag = 0;
      }
    },
    // 表示ボタンクリック処理
    searchclick() {
      // 入力項目クリア
      this.inputClear();
      this.messagevalidatesSearch = [];
      this.messagevalidatesEdt = [];
      this.valuesubadddate = "";
      if (this.checkFormSearch()) {
        this.selectMode = 'EDT';
        this.valuesubadddate = moment(this.valuedate).format("YYYYMMDD");
        this.selectedName = this.user_name + "　" + moment(this.valuesubadddate).format("YYYY年MM月DD日") + "分勤怠編集" ;
        this.getItem(moment(this.valuedate).format("YYYYMMDD"));
      }
    },
    //前日ボタンクリック処理
    gosubateclick() {
      // 入力項目クリア
      this.inputClear();
      this.messagevalidatesSearch = [];
      this.messagevalidatesEdt = [];
      if (this.checkFormSearch()) {
        this.selectMode = 'EDT';
        this.valuesubadddate = moment(this.valuesubadddate).subtract(1, 'days').format("YYYYMMDD");
        this.selectedName = this.user_name + "　" + moment(this.valuesubadddate).format("YYYY年MM月DD日") + "分勤怠編集" ;
        this.getItem(this.valuesubadddate);
      }
    },
    //翌日ボタンクリック処理
    goaddateclick() {
      // 入力項目クリア
      this.inputClear();
      this.messagevalidatesSearch = [];
      this.messagevalidatesEdt = [];
      if (this.checkFormSearch()) {
        this.selectMode = 'EDT';
        this.valuesubadddate = moment(this.valuesubadddate).add(1, 'days').format("YYYYMMDD");
        this.selectedName = this.user_name + "　" + moment(this.valuesubadddate).format("YYYY年MM月DD日") + "分勤怠編集" ;
        this.getItem(this.valuesubadddate);
      }
    },
    // プラス追加ボタンクリック処理
    appendRowClick: function() {
      if (this.before_count < this.count) {
        var messages = [];
        messages.push("１度に追加できる情報は１個です。追加してから再実行してください");
        this.messageswal("エラー", messages, "error", true, false, true);
      } else {
        this.object = {
          id: "",
          user_code: this.selectedUserValue,
          department_code: this.selectedDepartmentValue,
          record_time: "",
          mode: "",
          x_positions: "",
          y_positions: "",
          user_name: this.user_name,
          department_name: this.department_name,
          code_name: "",
          kbn_flag: 0,
          user_holiday_kbn: "",
          date: moment(this.valuesubadddate).format("YYYY/MM/DD"),
          time: ""
          };
        this.details.push(this.object);
        this.count = this.details.length
      }
    },
    // 更新ボタンクリック処理
    fixclick(index) {
      var flag = this.checkFormFix(index);
      if (flag) {
        var messages = [];
        messages.push("この内容で更新しますか？");
        this.messageswal("確認", messages, "info", true, true, true)
          .then(result  => {
            if (result) {
              this.FixDetail("更新", index);
            }
        });
      // 項目数が多い場合以下コメントアウト
      /* } else {
        this.countswal("エラー", this.messagevalidatesEdt, "error", true, false, true)
          .then(result  => {
            if (result) {
            }
        }); */
      }
    },
    // 追加ボタンクリック処理
    addClick(index) {
      var flag = this.checkFormFix(index);
      if (flag) {
        var messages = [];
        messages.push("この内容で追加しますか？");
        this.messageswal("確認", messages, "info", true, true, true)
          .then(result  => {
            if (result) {
              this.addDetail(index);
            }
        });
      // 項目数が多い場合以下コメントアウト
      /* } else {
        this.countswal("エラー", this.messagevalidatesEdt, "error", true, false, true)
          .then(result  => {
            if (result) {
            }
        }); */
      }
    },
    // 削除ボタンクリック処理
    delClick(index) {
      var messages = [];
      messages.push("この行内容を削除しますか？");
      this.messageswal("確認", messages, "info", true, true, true)
        .then(result  => {
          if (result) {
            this.DelDetail(index);
          }
      });
    },
    // 行削除ボタンクリック処理
    rowDelClick: function(index) {
      if (this.checkRowData(index)) {
        var messages = [];
        messages.push("行削除してよろしいですか？");
        this.messageswal("確認", messages, "info", true, true, true)
          .then(result  => {
            if (result) {
              this.details.splice(index, 1);
              this.count = this.details.length
            }
        });
      } else {
        this.details.splice(index, 1);
        this.count = this.details.length
      }
    },
    // -------------------- サーバー処理 ----------------------------
    // 勤怠取得処理
    getItem(datevalue) {
      this.postRequest("/edit_work_times/get",
        { ymd : datevalue, code : this.selectedUserValue})
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("勤怠時間","取得");
        });
    },
    // コード選択リスト取得処理
    getGeneralList(value) {
      var arrayParams = { identificationid : value };
      this.postRequest("/get_general_list", arrayParams)
        .then(response  => {
          if (value == "C005") {
            this.getThenc005(response, "勤怠モード");
          }
          if (value == "C013") {
            this.getThenc013(response, "休暇区分");
          }
        })
        .catch(reason => {
          if (value == "C005") {
            this.serverCatch("勤怠モード", "取得");
          }
          if (value == "C013") {
            this.serverCatch("休暇区分", "取得");
          }
        });
    },
    // 勤怠追加処理
    addDetail(index) {
      var messages = [];
      var arrayParams = { details : this.details[index] };
      this.postRequest("/edit_work_times/store", arrayParams)
        .then(response  => {
          this.putThenDetail(response, "追加");
        })
        .catch(reason => {
          this.serverCatch("勤怠編集", "追加");
        });
    },
    // 勤怠更新処理（明細）
    FixDetail(kbnname, index) {
      var messages = [];
      var arrayParams = { details : this.details[index] };
      this.postRequest("/edit_work_times/fix", arrayParams)
        .then(response  => {
          this.putThenDetail(response, kbnname);
        })
        .catch(reason => {
          this.serverCatch("勤怠編集", kbnname);
        });
    },
    // 部署削除処理（明細）
    DelDetail(index) {
      var messages = [];
      var arrayParams = { id : this.details[index].id };
      this.postRequest("/edit_work_times/del", arrayParams)
        .then(response  => {
          this.putThenDetail(response, "削除");
        })
        .catch(reason => {
          this.serverCatch("勤怠編集", "削除");
        });
    },

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
    // 取得正常処理
    getThen(response) {
      this.details = [];
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        this.count = this.details.length;
        this.before_count = this.count;
        if (res.details.length == 0) {
          var messages = [];
          messages.push("勤怠データありませんでした。\nプラスアイコンで追加できます。");
          this.messageswal("確認", messages, "info", true, false, false);
        }
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("エラー", res.messagedata, "error", true, false, true);
        } else {
          this.serverCatch("勤怠編集", "取得");
        }
      }
    },
    // 取得正常処理（勤怠モード選択リスト）
    getThenc005(response, value) {
      var res = response.data;
      if (res.result) {
        this.generalList_c005 = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("エラー", res.messagedata, "error", true, false, true);
        } else {
          this.serverCatch("勤怠モード選択リスト", "取得");
        }
      }
    },
    // 取得正常処理（休暇区分選択リスト）
    getThenc013(response, value) {
      var res = response.data;
      if (res.result) {
        this.generalList_c013 = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("エラー", res.messagedata, "error", true, false, true);
        } else {
          this.serverCatch("明細勤怠休暇区分選択リスト管理", "取得");
        }
      }
    },
    // 更新系正常処理（明細）
    putThenDetail(response, eventtext) {
      var res = response.data;
      if (res.result) {
        this.$toasted.show('勤怠編集を' + eventtext + 'しました', this.$toasted.options);
        this.getItem(this.valuesubadddate);
        this.count = this.details.length;
        this.before_count = this.count;
      } else {
        console.log('false');
        if (res.messagedata.length > 0) {
          this.messageswal("警告", res.messagedata, "warning", true, false, true);
        } else {
          this.serverCatch("勤怠編集", eventtext);
        }
      }
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + eventtext + "に失敗しました");
      this.messageswal("エラー", messages, "error", true, false, true);
    },
    inputClear() {
      this.details = [];
      this.count = 0;
      this.before_count = 0;
    },
    checkRowData(index) {
      if (this.details[index].mode != "" && this.details[index].mode != null) { return true; }
      if (this.details[index].time != "" && this.details[index].time != null) { return true; }
      if (this.details[index].user_holiday_kbn != "" && this.details[index].user_holiday_kbn != null) { return true; }
      console.log('checkRowData false');
      return false;
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
    }
  }
};
</script>
