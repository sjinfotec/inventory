<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- ========================== 検索部 START ========================== -->
      <!-- .panel -->
      <div class="col-md pt-3" v-if="selectMode==''">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'開始終了日付を指定して従業員のシフトを登録します。'"
            v-bind:header-text2="'全従業員共通または部署ごと個人ごとに登録可能です。'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
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
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-4 pb-2">
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
              <div class="col-md-4 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >所属部署</span>
                  </div>
                  <select-departmentlist
                    ref="selectdepartmentlist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'部署を選択してください'"
                    v-bind:selected-value="selectedDepartmentValue"
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
              <div class="col-md-4 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      for="target_users"
                    >氏 　名</label>
                  </div>
                  <select-userlist
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
            <!-- .row -->
            <div class="row justify-content-between" v-if="messagevalidatesInit.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li v-for="(messagevalidate,index) in messagevalidatesInit" v-bind:key="index">{{ messagevalidate }}</li>
                </ul>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
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
            <!-- ----------- 選択ボタン類 END ---------------- -->
          </div>
          <!-- panel contents -->
        </div>
      </div>
      <!-- /.panel -->
      <!-- ========================== 検索部 END ========================== -->
      <!-- /main contentns row -->
      <!-- ========================== 表示部 START ========================== -->
      <!-- .panel -->
      <div class="col-md-12 pt-3" v-if="selectMode=='SER'">
        <div class="card shadow-pl" v-if="details.length">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆シフト個別表示'"
            v-bind:header-text2="stringtext"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- main contentns row -->
          <!-- ----------- 個別編集部 START ---------------- -->
          <!-- panel contents -->
          <!-- .row -->
          <div class="col-md-6 pb-2 w-15 text-center align-middle">
            <col-note
              v-bind:item-name="'個別編集'"
              v-bind:item-control="'INFO'"
              v-bind:item-note="''"
              data-toggle="tooltip"
              data-placement="top"
            ></col-note>
          </div>
          <!-- /.col -->
          <!-- ----------- 項目部 START ---------------- -->
          <table-shift-time
            v-bind:detail-dates="detail_dates"
            v-bind:details="details"
            v-bind:is-edtbutton="true"
            v-on:detaileditclick-event="detailEdtClick"
          ></table-shift-time>
          <!-- ----------- 項目部 END ---------------- -->
          <!-- ----------- 個別編集部 END ---------------- -->
          <!-- ----------- CSVボタン START ---------------- -->
          <div class="card-body pt-2">
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-2 pb-2">
                <btn-work-time
                  v-bind:btn-mode="'dummy'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-md-2 pb-2" v-for="(item,index) in get_C037_CSV">
                <btn-csv-download
                  v-bind:btn-mode="item['code']"
                  v-bind:csv-data="detail_dates"
                  v-bind:csv-data-sub="details"
                  v-bind:general-data="get_C037"
                  v-bind:general-physicalname="item['physical_name']"
                  v-bind:is-csvbutton="iscsvbutton"
                  v-bind:csv-date="datejaFormat"
                >
                </btn-csv-download>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- ----------- CSVボタン END ---------------- -->
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆シフト一括編集'"
            v-bind:header-text2="stringtext2"
          ></daily-working-information-panel-header>
          <!-- main contentns row -->
          <!-- ----------- 一括編集部 START ---------------- -->
          <!-- panel contents -->
          <!-- .row -->
          <div class="col-md-3 pb-2 w-15 text-center align-middle">
            <col-note
              v-bind:item-name="'一括編集'"
              v-bind:item-control="'INFO'"
              v-bind:item-note="''"
              data-toggle="tooltip"
              data-placement="top"
            ></col-note>
          </div>
          <!-- /.row -->
          <div class="card-body pt-2">
            <!-- ----------- メッセージ部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between" v-if="messagevalidatesBatch.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li v-for="(messagevalidate,index) in messagevalidatesBatch" v-bind:key="index">{{ messagevalidate }}</li>
                </ul>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- メッセージ部 END ---------------- -->
            <!-- ----------- 日付指定変数部 START ---------------- -->
            <!-- .row -->
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table table-striped border-bottom font-size-sm text-nowrap">
                    <thead>
                      <tr>
                        <td class="text-center align-middle w-20">開始日<span class="color-red">[必須]</span></td>
                        <td class="text-center align-middle w-20">終了日</span></td>
                        <td class="text-center align-middle w-40">シフト<span class="color-red">[必須]</span></td>
                        <td class="text-center align-middle w-20">操作</td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="text-left align-middle">
                          <input
                            type="date"
                            class="form-control"
                            v-model="valuefromday"
                            :min="date_min"
                            :max="date_max"
                             @change="valuefromdayChanges(valuefromday)"
                          />
                        </td>
                        <td class="text-left align-middle">
                          <input
                            type="date"
                            class="form-control"
                            v-model="valuetoday"
                            :min="date_min"
                            :max="date_max"
                          />
                        </td>
                        <td class="text-center align-middle">
                          <select class="form-control" v-model="workingtimetablenobatch" @change="timetablenobatchChanges(workingtimetablenobatch)">
                            <option value></option>
                            <option
                              v-for="tlist in timetableList"
                              :value="tlist.no"
                              v-bind:key="tlist.no"
                            >{{ tlist.name }}</option>
                          </select>
                        </td>
                        <td class="text-center align-middle">
                          <div class="btn-group">
                            <button
                              type="button"
                              class="btn btn-success"
                              @click="fixbatchclick()"
                            >この内容で一括更新する</button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <!-- /.row -->
            <!-- ----------- 日付指定編集部 END ---------------- -->
            <!-- ----------- 曜日指定編集部 START ---------------- -->
            <!-- .row -->
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table table-striped border-bottom font-size-sm text-nowrap">
                    <thead>
                      <tr>
                        <td class="text-center align-middle w-40 mw-rem-6">曜日<span class="color-red">[必須]</span></td>
                        <td class="text-center align-middle w-40">シフト<span class="color-red">[必須]</span></td>
                        <td class="text-center align-middle w-20">操作</td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="text-center align-middle">
                          <select class="form-control" v-model="weekbatch" @change="weekdaysChanges(weekbatch)">
                            <option value></option>
                            <option
                              v-for="(wlist,index) in this.formweekdays"
                              :value="index"
                              v-bind:key="index"
                            >{{ wlist }}</option>
                          </select>
                        </td>
                        <td class="text-center align-middle">
                          <select class="form-control" v-model="workingtimetablenobatch_w" @change="timetablenobatchWChanges(workingtimetablenobatch_w)">
                            <option value></option>
                            <option
                              v-for="tlist in timetableList"
                              :value="tlist.no"
                              v-bind:key="tlist.no"
                            >{{ tlist.name }}</option>
                          </select>
                        </td>
                        <td class="text-center align-middle">
                          <div class="btn-group">
                            <button
                              type="button"
                              class="btn btn-success"
                              @click="fixbatchWclick()"
                            >この内容で一括更新する</button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:backclick-event="serchbackclick"
                  v-bind:btn-mode="'back'"
                ></btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- 日付指定編集部 END ---------------- -->
          </div>
          <!-- /main contentns row -->
          <!-- ----------- 一括編集部 START ---------------- -->
        </div>
      </div>
      <!-- ========================== 表示部 END ========================== -->
      <!-- /.panel -->
      <!-- ========================== 編集部 START ========================== -->
      <!-- .panel -->
      <div class="col-md-12 pt-3" v-if="selectMode=='EDT'">
        <div class="card shadow-pl" v-if="detailsEdtlength > 0">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆シフト編集'"
            v-bind:header-text2="'設定済みのシフトを編集できます。'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- panel header -->
          <div class="card-header bg-transparent pt-3 border-0">
            <h1 class="float-sm-left font-size-rg">
              【 {{ detailsEdt['user_name'] }}】{{ detailsEdt['department_name'] }}：{{ detailsEdt['employment_name'] }}
            </h1>
          </div>
          <!-- /.panel header -->
          <!-- ----------- 編集入力部 START ---------------- -->
          <!-- main contentns row -->
          <div class="card-body pt-2">
            <!-- panel contents -->
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
            <div class="card-body pt-2">
              <!-- panel contents -->
              <!-- .row -->
              <!-- ----------- ボタン部 START ---------------- -->
              <div class="row justify-content-between">
                <!-- col -->
                <div class="col-md-12 pb-2">
                  <btn-work-time
                    v-on:fixclick-event="fixclick"
                    v-bind:btn-mode="'fix'"
                    v-bind:is-push="isfixbutton">
                  </btn-work-time>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <!-- ----------- ボタン部 END ---------------- -->
              <!-- .row -->
              <div class="row">
                <div class="col-12">
                  <div class="table-responsive">
                    <table class="table table-striped border-bottom font-size-sm text-nowrap">
                      <thead>
                        <tr>
                          <td class="text-center align-middle w-10">日付</td>
                          <td class="text-center align-middle w-10">営業日区分</td>
                          <td class="text-center align-middle w-10">休暇</td>
                          <td class="text-center align-middle w-45">シフト<span class="color-red">[必須]</span></td>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(item1,index1) in detailsEdt['array_user_date_data']" v-bind:key="item1['date']">
                          <td class="text-left align-middle">{{item1['md_name']}}{{item1['public_holidays_name']}}</td>
                          <td class="text-center align-middle">{{item1['business_kubun_name']}}</td>
                          <td class="text-center align-middle">{{item1['holiday_kubun_name']}}</td>
                          <td class="text-center align-middle">
                            <select class="form-control" v-model="array_working_timetable_no[index1]"
                              @change="timetablenoChanges(array_working_timetable_no[index1], index1)">
                              <option value></option>
                              <option
                                v-for="tlist in timetableList"
                                :value="tlist.no"
                                v-bind:key="tlist.no"
                              >{{ tlist.name }}</option>
                            </select>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <!-- /.row -->
              <!-- .row -->
              <!-- ----------- ボタン部 START ---------------- -->
              <div class="row justify-content-between">
                <!-- col -->
                <div class="col-md-12 pb-2">
                  <btn-work-time
                    v-on:fixclick-event="fixclick"
                    v-bind:btn-mode="'fix'"
                    v-bind:is-push="isfixbutton">
                  </btn-work-time>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <!-- .row -->
              <div class="row justify-content-between">
                <!-- col -->
                <div class="col-md-12 pb-2">
                  <btn-work-time
                    v-on:backclick-event="fixbackclick"
                    v-bind:btn-mode="'back'"
                    v-bind:is-push="isfixbutton"
                  ></btn-work-time>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <!-- ----------- ボタン部 END ---------------- -->
            </div>
            <!-- /.panel contents -->
          </div>
          <!-- /main contentns row -->
          <!-- ----------- 編集入力部 END ---------------- -->
        </div>
      </div>
      <!-- ========================== 編集部 END ========================== -->
      <!-- /.panel -->
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
const CONST_C037 = 'C037';
const CONST_C037_CSVSHIFT_CODE = 5;

export default {
  name: "SettingCalendar",
  mixins: [ dialogable, checkable, requestable ],
  props: {
    const_generaldatas: {
        type: Array,
        default: []
    }
  },
  data() {
    return {
      const_C037_data: [],
      const_C037_csv: [],
      valuefromdate: "",
      valuetodate: "",
      selectedEmploymentValue: "",
      selectedDepartmentValue : "",
      valueDepartmentkillcheck : false,
      department_name: "",
      selectedUserValue : "",
      valueUserkillcheck : false,
      user_name: "",
      applytermdate: "",
      DatePickerFormat: "yyyy年MM月dd日",
      search_valuefromdate: "",
      search_valuetodate: "",
      search_selectedEmploymentValue: "",
      search_selectedDepartmentValue: "",
      search_selectedUserValue: "",
      datejaFormat: "",
      datejaFormat2: "",
      issearchbutton: false,
      isfixbutton: false,
      selectMode: "",
      messagevalidatesInit: [],
      messagevalidatesSearch: [],
      messagevalidatesEdt: [],
      messagevalidatesBatch: [],
      stringtext: "",
      stringtext2: "",
      details: [],
      detail_dates: [],
      use_free_item: [{}],
      array_working_timetable_no: [{}],
      formweekdays: [
        '月曜日',
        '火曜日',
        '水曜日',
        '木曜日',
        '金曜日',
        '土曜日',
        '日曜日'
      ],
      detailsEdt: [],
      detailsEdtlength: 0,
      valuefromday : "",
      valuetoday : "",
      workingtimetablenobatch : "",
      weekbatch : "",
      workingtimetablenobatch_w : "",
      timetableList: [],
      date_min : "2019-01-01",
      date_max : "2099-12-31",
      iscsvbutton: false
    };
  },
  computed: {
    get_C037: function() {
      let $this = this;
      var i = 0;
      this.const_generaldatas.forEach( function( item ) {
        if (item.identification_id == CONST_C037) {
          $this.const_C037_data.push($this.const_generaldatas[i]);
        }
        i++;
      });    
      return this.const_C037_data;
    },
    get_C037_CSV: function() {
      let $this = this;
      var i = 0;
      this.const_generaldatas.forEach( function( item ) {
        if (item.identification_id == CONST_C037) {
          if (item.code == CONST_C037_CSVSHIFT_CODE) {
            $this.const_C037_csv.push($this.const_generaldatas[i]);
          }
        }
        i++;
      });    
      return this.const_C037_csv;
    }
  },
  // マウント時
  mounted() {
    // 今月初末を取得
    const defaultfromDate = moment().startOf('month');
    const defaulttoDate = moment().endOf('month');
    this.valuefromdate = new Date(defaultfromDate);
    this.valuetodate = new Date(defaulttoDate);
    this.getTimetableList(this.valuefromdate);
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション（表示）
    checkFormEdt: function() {
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

      if (this.messagevalidatesSearch.length > 0) {
        flag = false;
      }
      if (flag) {
        if (this.valuefromdate > this.valuetodate) {
          flag = false;
          this.messagevalidatesSearch.push("開始日付＞終了日付となっています");
        }
      }
      return flag;
    },
    // バリデーション（更新）
    checkFormFix: function() {
      this.messagevalidatesEdt = [];
      var flag = true;
      // シフト
      var chkArray = [];
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = 'シフト';
      for ( var i=0; i<this.array_working_timetable_no.length;i++ ) {
        chkArray = 
          this.checkDetailtext(this.array_working_timetable_no[i]
            , required, equalength, maxlength, itemname,
            this.detailsEdt['array_user_date_data'][i]['date_name']);
        if (chkArray.length > 0) {
          if (this.messagevalidatesEdt.length == 0) {
            this.messagevalidatesEdt = chkArray;
          } else {
            this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
          }
        }
      }

      if (this.messagevalidatesEdt.length > 0) {
        flag = false;
      }
      return flag;
    },
    // バリデーション（一括更新日付）
    checkFormBatch: function() {
      this.messagevalidatesBatch = [];
      var flag = true;
      // 開始日
      var chkArray = [];
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '開始日';
      chkArray = 
        this.checkHeader(this.valuefromday, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesBatch.length == 0) {
          this.messagevalidatesBatch = chkArray;
        } else {
          this.messagevalidatesBatch = this.messagevalidatesBatch.concat(chkArray);
        }
      }
      // 終了日
      chkArray = [];
      required = false;
      equalength = 0;
      maxlength = 0;
      itemname = '終了日';
      if (this.valuetoday != "" && this.valuetoday != null) {
        if (this.valuefromday > this.valuetoday) {
          chkArray.push("開始日＞終了日となっています");
        }
        if (chkArray.length > 0) {
          if (this.messagevalidatesBatch.length == 0) {
            this.messagevalidatesBatch = chkArray;
          } else {
            this.messagevalidatesBatch = this.messagevalidatesBatch.concat(chkArray);
          }
        }
      }

      // シフト
      chkArray = [];
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = '日付指定のシフト';
      chkArray = 
        this.checkHeader(this.workingtimetablenobatch, required, equalength, maxlength, itemname, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesBatch.length == 0) {
          this.messagevalidatesBatch = chkArray;
        } else {
          this.messagevalidatesBatch = this.messagevalidatesBatch.concat(chkArray);
        }
      }

      if (this.messagevalidatesBatch.length > 0) {
        flag = false;
      }
      return flag;
    },
    // バリデーション（一括更新曜日）
    checkFormBatchW: function() {
      this.messagevalidatesBatch = [];
      var flag = true;
      // 曜日
      var chkArray = [];
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '曜日';
      chkArray = 
        this.checkHeader(this.weekbatch, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesBatch.length == 0) {
          this.messagevalidatesBatch = chkArray;
        } else {
          this.messagevalidatesBatch = this.messagevalidatesBatch.concat(chkArray);
        }
      }

      // シフト
      chkArray = [];
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = '曜日指定のシフト';
      chkArray = 
        this.checkHeader(this.workingtimetablenobatch_w, required, equalength, maxlength, itemname, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesBatch.length == 0) {
          this.messagevalidatesBatch = chkArray;
        } else {
          this.messagevalidatesBatch = this.messagevalidatesBatch.concat(chkArray);
        }
      }

      if (this.messagevalidatesBatch.length > 0) {
        flag = false;
      }
      return flag;
    },
    // ------------------------ イベント処理 ------------------------------------
    
    // 開始日付が変更された場合の処理
    fromdateChanges: function(value) {
      this.valuefromdate = value;
      this.valuetodate = new Date(moment(this.valuefromdate).add(1, 'months').subtract(1, 'days'));
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
      this.valuefromdate = "";
      // 再取得
      this.applytermdate = moment(new Date()).format("YYYYMMDD");
      this.$refs.selectdepartmentlist.getList(this.applytermdate);
      this.getUserSelected();
      this.applytermdate = "";
    },
    // 終了日付が変更された場合の処理
    fromtoChanges: function(value, arrayitem) {
      this.valuetodate = value;
    },
    // 終了日付がクリアされた場合の処理
    fromtoCleared: function() {
      this.valuetodate = "";
    },
    // 雇用形態が変更された場合の処理
    employmentChanges: function(value) {
      this.selectedEmploymentValue = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.selectedUserValue = "";
      this.getDo = 1;
      this.getUserSelected();
    },
    // 部署選択が変更された場合の処理
    departmentChanges: function(value, arrayitem) {
      this.selectedDepartmentValue = value;
      this.department_name = arrayitem['name'];
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.selectedUserValue = "";
      this.getDo = 1;
      this.getUserSelected();
    },
    // ユーザー選択が変更された場合の処理
    userChanges: function(value, arrayitem) {
      this.selectedUserValue = value;
      this.user_name = arrayitem['name'];
    },
    //  シフトが変更された場合の処理
    timetablenoChanges: function(value, index) {
      for(var i=index+1;i<this.detailsEdt['array_user_date_data'].length;i++) {
        if (this.array_working_timetable_no[i] == null || this.array_working_timetable_no[i] == "") {
          this.array_working_timetable_no[i] = value;
        }
      }
    },
    // 日付指定が変更された場合の処理
    valuefromdayChanges: function(value) {
      if (this.valuetoday == "" || this.valuetoday == null) {
        this.valuetoday = this.valuefromday;
      }
    },
    // シフト（日付指定）が変更された場合の処理
    timetablenobatchChanges: function(value) {
    },
    // 曜日が変更された場合の処理
    weekdaysChanges: function(value) {
      // パネルに表示
      this.setPanelHeader();
    },
    // シフト（曜日指定）が変更された場合の処理
    timetablenobatchWChanges: function(value) {
    },
    // 表示ボタンクリック処理
    searchclick() {
      // 入力項目クリア
      this.inputClear();
      this.messageClear();
      if (this.checkFormEdt()) {
        this.search_valuefromdate = this.valuefromdate;
        this.search_valuetodate = this.valuetodate;
        this.search_selectedEmploymentValue = this.selectedEmploymentValue;
        this.search_selectedDepartmentValue = this.selectedDepartmentValue;
        this.search_selectedUserValue = this.selectedUserValue;
        this.getItem();
      }
      // パネルに表示
      this.setPanelHeader();
    },
    // 明細編集ボタンクリックされた場合の処理
    detailEdtClick: function(e, arrayitem) {
      var index = arrayitem['rowIndex'];
      this.detailsEdt = this.details[index];
      this.detailsEdtlength = Object.keys(this.detailsEdt).length;
      var detailsEdtdatedata = this.detailsEdt['array_user_date_data'];
      var detailsEdtdatedatalength = Object.keys(detailsEdtdatedata).length;
      var messages = [];
      for (var i=0; i<detailsEdtdatedata.length; i++) {
        if (detailsEdtdatedata[i]['working_timetable_name'] ==  "カレンダー未設定") {
          messages.push("カレンダーが設定されていない日付があるため");
          messages.push("編集できません");
          messages.push("先にカレンダー設定を実施してください");
          i = detailsEdtdatedata.length;
        }
      }
      if (messages.length == 0) {
        this.selectMode = 'EDT';
        for (var i=0; i<detailsEdtdatedata.length; i++) {
          this.business[i] = detailsEdtdatedata[i]['business_kubun'];
          this.holiday[i] = detailsEdtdatedata[i]['holiday_kubun'];
          this.use_free_item[i] = detailsEdtdatedata[i]['use_free_item'];
          this.array_working_timetable_no[i] = detailsEdtdatedata[i]['working_timetable_no'];
        }
      } else {
        this.htmlMessageSwal("確認", messages, "info", true, false)
          .then(result  => {
        });
      }
    },
    //更新ボタンクリック処理
    fixclick() {
      this.messageClear();
      var flag = this.checkFormFix();
      if (flag) {
        var messages = [];
        messages.push("すでに入力した日にデータがある場合は");
        messages.push("入力内容で上書きします。");
        messages.push("更新してよろしいですか？");
        this.htmlMessageSwal("確認", messages, "info", true, true)
          .then(result  => {
            if (result) {
              this.FixDetail("更新");
            }
        });
      // 項目数が多い場合以下コメントアウト
      } else {
        this.countswal("エラー", this.messagevalidatesEdt, "error", true, false, true)
          .then(result  => {
            if (result) {
            }
        });
      }
    },
    // キャンセルボタンクリック処理
    serchbackclick() {
      this.selectMode = '';
    },
    // 戻るボタンクリック処理
    fixbackclick() {
      this.selectMode = 'SER';
    },
    // 一括更新ボタンクリック処理（日付）
    fixbatchclick() {
      this.messageClear();
      var flag = this.checkFormBatch();
      if (flag) {
        var messages = [];
        messages.push("更新してよろしいですか？");
        this.htmlMessageSwal("確認", messages, "info", true, true)
          .then(result  => {
            if (result) {
              this.FixDetailbatch("一括更新");
            }
        });
      // 項目数が多い場合以下コメントアウト
      } else {
        this.countswal("エラー", this.messagevalidatesBatch, "error", true, false, true)
          .then(result  => {
            if (result) {
            }
        });
      }
    },
    // 一括更新ボタンクリック処理（曜日）
    fixbatchWclick() {
      this.messageClear();
      var flag = this.checkFormBatchW();
      if (flag) {
        var messages = [];
        messages.push("更新してよろしいですか？");
        this.htmlMessageSwal("確認", messages, "info", true, true)
          .then(result  => {
            if (result) {
              this.FixDetailbatchW("一括更新");
            }
        });
      // 項目数が多い場合以下コメントアウト
      } else {
        this.countswal("エラー", this.messagevalidatesBatch, "error", true, false, true)
          .then(result  => {
            if (result) {
            }
        });
      }
    },

    // -------------------- サーバー処理 ----------------------------
    // タイムテーブル選択リスト取得処理
    getTimetableList: function(targetdate) {
      this.timetableList = [];
      if (targetdate == "") {
        targetdate = moment(new Date()).format("YYYYMMDD");
      }
      var arrayParams = {
        targetdate : targetdate
      };
      this.postRequest("/get_time_table_list", arrayParams)
        .then(response => {
          this.getThentimetable(response);
        })
        .catch(reason => {
          this.serverCatch("タイムテーブル", "取得");
        });
    },
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
    // シフト取得処理
    getItem() {
      var fromdate = null;
      var todate = null;
      if (this.search_valuefromdate != null && this.search_valuefromdate != "") {
        fromdate = moment(this.search_valuefromdate).format("YYYYMMDD");
      }
      if (this.search_valuetodate != null && this.search_valuetodate != "") {
        todate = moment(this.search_valuetodate).format("YYYYMMDD");
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
            fromdate : fromdate,
            todate : todate,
            employmentstatus : this.search_selectedEmploymentValue,
            departmentcode : this.search_selectedDepartmentValue,
            usercode : this.search_selectedUserValue
          };
          this.postRequest("/edit_shift_time/get", arrayParams)
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
    // シフト更新処理（明細）
    FixDetail(eventname) {
      var arrayParams = {
        details : this.detailsEdt,
        working_timetable_no : this.array_working_timetable_no
      };
      this.postRequest("/edit_shift_time/fix", arrayParams)
        .then(response  => {
          this.putThenDetail(response, eventname);
        })
        .catch(reason => {
          this.serverCatch("シフト", eventname);
        });
    },
    // シフト一括更新処理
    FixDetailbatch(eventname) {
      var paramfromdate = null;
      var paramtodate = null;
      if (this.valuefromday != "") {
        paramfromdate = moment(this.valuefromday).format("YYYYMMDD");
        if (this.valuetoday != "") {
          paramtodate = moment(this.valuetoday).format("YYYYMMDD");
        } else {
          paramtodate = paramfromdate;
        }
        var arrayParams = {
            employmentstatus : this.search_selectedEmploymentValue,
            departmentcode : this.search_selectedDepartmentValue,
            usercode : this.search_selectedUserValue,
            fromdate : paramfromdate,
            todate : paramtodate,
            working_timetable_no: this.workingtimetablenobatch
        };
        this.postRequest("/edit_shift_time/fixbatch", arrayParams)
          .then(response  => {
            this.putThenBatch(response, eventname);
          })
          .catch(reason => {
            this.serverCatch("シフト", eventname);
          });
      }
    },
    // シフト一括更新処理（曜日）
    FixDetailbatchW(eventname) {
      var paramfromdate = null;
      var paramtodate = null;
      if (this.search_valuefromdate != "") {
        paramfromdate = moment(this.search_valuefromdate).format("YYYYMMDD");
        if (this.search_valuetodate != "") {
          paramtodate = moment(this.search_valuetodate).format("YYYYMMDD");
        } else {
          paramtodate = paramfromdate;
        }
        var arrayParams = {
            employmentstatus : this.search_selectedEmploymentValue,
            departmentcode : this.search_selectedDepartmentValue,
            usercode : this.search_selectedUserValue,
            fromdate : paramfromdate,
            todate : paramtodate,
            weekdays : this.weekbatch,
            working_timetable_no: this.workingtimetablenobatch_w
        };
        this.postRequest("/edit_shift_time/fixbatchw", arrayParams)
          .then(response  => {
            this.putThenBatch(response, eventname);
          })
          .catch(reason => {
            this.serverCatch("シフト", eventname);
          });
      }
    },
    // -------------------- 共通 ----------------------------
    // 取得正常処理
    getThen(response) {
      this.details = [];
      this.business = [{}];
      this.holiday = [{}];
      this.use_free_item = [{}];
      this.array_working_timetable_no = [{}];
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        this.detail_dates = res.detail_dates;
        this.valuefromday = moment(this.valuefromdate).format("YYYY-MM-DD");
        this.valuetoday = moment(this.valuetodate).format("YYYY-MM-DD");
        this.date_min = moment(this.valuefromdate).format("YYYY-MM-DD");
        this.date_max = moment(this.valuetodate).format("YYYY-MM-DD");
        if (res.messagedata.length > 0) {
          if (this.details.length == 0) {
            var getmessagedata = res.messagedata;
            getmessagedata.push("シフト一括設定で初期設定してから");
            getmessagedata.push("再度実行してください");
            this.htmlMessageSwal("情報", getmessagedata, "info", true, false)
          } else {
            this.htmlMessageSwal("情報", res.messagedata, "info", true, false)
          }
        } else {
          this.selectMode = 'SER';
        }
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false)
        } else {
          this.serverCatch("シフト", "取得");
        }
      }
    },
    // 取得正常処理（明細タイムテーブル対象選択リスト）
    getThentimetable(response) {
      var res = response.data;
      if (res.result) {
        this.timetableList = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("明細タイムテーブル", "取得");
        }
      }
    },
    // 更新系正常処理（明細）
    putThenDetail(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.$toasted.show("シフトを" + eventtext + "しました");
        this.selectMode = '';
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false)
        } else {
          this.serverCatch("シフト", eventtext);
        }
      }
    },
    // 一括更新系正常処理
    putThenBatch(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.$toasted.show("シフトを" + eventtext + "しました");
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false)
        } else {
          this.serverCatch("シフト", eventtext);
        }
      }
      this.selectMode = 'SER';
      this.getItem();
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false)
    },
    inputClear() {
      this.details = [];
    },
    // メッセージクリア
    messageClear() {
      this.messagevalidatesSearch = [];
      this.messagevalidatesEdt = [];
      this.messagevalidatesBatch = [];
    },
    // 集計パネルヘッダ文字列編集処理
    setPanelHeader: function() {
      moment.locale("ja");
      this.datejaFormat = "";
      this.datejaFormat2 = "";
      this.stringtext = "";
      this.stringtext2 = "";
      if (this.search_valuefromdate != null && this.search_valuefromdate != "") {
        if (this.search_valuetodate != null && this.search_valuetodate != "") {
          this.datejaFormat +=  moment(this.search_valuefromdate).format("YYYY年MM月DD日");
          this.datejaFormat +=  "から";
          this.datejaFormat +=  moment(this.search_valuetodate).format("YYYY年MM月DD日");
          this.stringtext =
            this.datejaFormat + "のシフトを表示";
        }
      }
      if (this.valuefromday != null && this.valuefromday != "") {
        if (this.valuetoday != null && this.valuetoday != "") {
          this.datejaFormat2 +=  moment(this.valuefromday).format("YYYY年MM月DD日");
          this.datejaFormat2 +=  "から";
          this.datejaFormat2 +=  moment(this.valuetoday).format("YYYY年MM月DD日");
        }
        if (this.weekbatch != null && this.weekbatch != "") {
          this.datejaFormat2 +=  "または";
          this.datejaFormat2 +=  this.formweekdays[this.weekbatch];
        }
        this.stringtext2 =
          this.datejaFormat2 + "のシフトを一括編集";
      }
    }
  }
};
</script>
<style scoped>

.table th, .table td {
    padding: 0rem !important;
}

.mw-rem-3 {
  min-width: 3rem;
}

.mw-rem-6 {
  min-width: 3rem;
}
</style>
