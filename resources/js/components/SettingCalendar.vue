<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- ========================== 検索部 START ========================== -->
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'年（または月）を指定して出勤カレンダーを設定する'"
            v-bind:header-text2="'全従業員共通または部署ごと個人ごとに設定可能です。'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- ----------- 選択リスト START ---------------- -->
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-4 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >指定年<span class="color-red">[必須]</span></span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="number"
                      name="fromyear"
                      title="指定年"
                      max="2050"
                      v-bind:min="year"
                      step="1"
                      class="form-control"
                      v-model="valueyear"
                      v-on:onblur="fromyearChanges"
                    />
                  </div>
                  <!-- <input-datepicker
                    v-bind:default-date="valueyear"
                    v-bind:date-format="'yyyy年'"
                    v-on:change-event="fromyearChanges"
                    v-on:clear-event="fromyearCleared"
                  ></input-datepicker> -->
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-4 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >指定月<font color="blue">[表示時必須]</font></span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="number"
                      name="frommonth"
                      title="指定月"
                      max="12"
                      min="1"
                      step="1"
                      class="form-control"
                      v-model="valuemonth"
                      v-on:onblur="frommonthChanges"
                    />
                  </div>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-4 pb-2">
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
            <div class="row justify-content-between" v-if="messagevalidatesDsp.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li v-for="(messagevalidate,index) in messagevalidatesDsp" v-bind:key="index">{{ messagevalidate }}</li>
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
            <!-- .row -->
            <!-- <div class="row justify-content-between" v-if="messagevalidatesCopyinit.length"> -->
              <!-- col -->
              <!-- <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li v-for="(messagevalidate,index) in messagevalidatesCopyinit" v-bind:key="index">{{ messagevalidate }}</li>
                </ul>
              </div> -->
              <!-- /.col -->
            <!-- </div> -->
            <!-- /.row -->
            <!-- ----------- メッセージ部 END ---------------- -->
            <!-- ----------- 選択ボタン類 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:initclick-event="initclick"
                  v-bind:btn-mode="'init'"
                  v-bind:is-push="isinitbutton">
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
                  v-on:searchclick-event="searchclick"
                  v-bind:btn-mode="'search'"
                  v-bind:is-push="issearchbutton"
                ></btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <!-- <div class="row justify-content-between"> -->
              <!-- col -->
              <!-- <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:copyinitclick-event="copyinitclick"
                  v-bind:btn-mode="'copyinit'"
                  v-bind:is-push="isinitbutton">
                </btn-work-time>
              </div> -->
              <!-- /.col -->
            <!-- </div> -->
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
      <div class="col-md-12 pt-3" v-if="selectMode=='DSP'">
        <div class="card shadow-pl" v-if="details.length">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆カレンダー表示'"
            v-bind:header-text2="stringtext"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- main contentns row -->
          <!-- ----------- 項目部 START ---------------- -->
          <table-calendarmonth
            v-bind:detail-dates="detail_dates"
            v-bind:details="details"
            v-bind:is-edtbutton="true"
            v-on:detaileditclick-event="detailEdtClick"
          ></table-calendarmonth>
          <!-- ----------- 項目部 END ---------------- -->
          <!-- /main contentns row -->
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
            v-bind:header-text1="'◆カレンダー編集'"
            v-bind:header-text2="'設定済みのカレンダーを編集できます。'"
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
                          <td class="text-left align-middle w-10 mw-rem-5">日付</td>
                          <td class="text-left align-middle w-35 mw-rem-10">営業日区分<span class="color-red">[必須]</span></td>
                          <td class="text-left align-middle w-35 mw-rem-10">休暇区分</td>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(item1,index1) in detailsEdt['array_user_date_data']" v-bind:key="item1['date']">
                          <td class="text-left align-middle">{{item1['md_name']}}</td>
                          <td class="text-center align-middle">
                            <select class="form-control" v-model="business[index1]" @change="businessDayChanges(business[index1], index1)">
                              <option value></option>
                              <option
                                v-for="blist in BusinessDayList"
                                :value="blist.code"
                                v-bind:key="blist.code"
                              >{{ blist.code_name }}</option>
                            </select>
                          </td>
                          <td class="text-center align-middle">
                            <select class="form-control" v-model="holiday[index1]" @change="holiDayChanges(holiday[index1], index1)">
                              <option value></option>
                              <option
                                v-for="hlist in HoliDayList"
                                :value="hlist.code"
                                v-bind:key="hlist.code"
                              >{{ hlist.code_name }}</option>
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
      <!-- ========================== 初期設定部 START ========================== -->
      <!-- .panel -->
      <div class="col-md-12 pt-3" v-if="selectMode=='INT'">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆カレンダー設定'"
            v-bind:header-text2="stringtext"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- ----------- メッセージ部 START ---------------- -->
          <!-- .row -->
          <div class="row justify-content-between" v-if="messagevalidatesInitstore.length">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <ul class="error-red color-red">
                <li v-for="(messagevalidate,index) in messagevalidatesInitstore" v-bind:key="index">{{ messagevalidate }}</li>
              </ul>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- ----------- メッセージ部 END ---------------- -->
          <div class="card-body pt-2">
            <!-- ----------- 選択リスト START ---------------- -->
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between" v-if="showC024list">
              <!-- .col -->
              <div class="col-md-4 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputGroupSelect01"
                    >設定区分<span class="color-red">[必須]</span></label>
                  </div>
                  <select-generallist
                      ref="selectC024list"
                      v-bind:blank-data="false"
                      v-bind:placeholder-data="'設定区分を選択してください'"
                      v-bind:setting-value="selectedC024Value"
                      v-bind:add-new="true"
                      v-bind:date-value="''"
                      v-bind:kill-value="valueC024killcheck"
                      v-bind:row-index=0
                      v-bind:identification-id="'C024'"
                      v-on:change-event="C024Changes"
                  ></select-generallist>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- ----------- 選択リスト END ---------------- -->
          </div>
          <!-- /.row -->
          <!-- ----------- 項目部 START ---------------- -->
          <!-- main contentns row -->
          <!-- panel contents -->
          <div class="card-body pt-2">
            <!-- ----------- 編集入力部 START ---------------- -->
            <!-- .sub panel -->
            <div class="card shadow-pl">
              <!-- panel header -->
              <daily-working-information-panel-header
                v-bind:header-text1="'設定内容指定'"
                v-bind:header-text2="'設定内容を指定します。'"
              ></daily-working-information-panel-header>
              <!-- /.panel header -->
              <div class="card-body pt-2">
                <!-- .row -->
                <div class="row justify-content-between">
                  <div class="col-md-12 pb-2">
                    <div class="form-group">
                      <div class="form-check">
                        <div
                            v-for="formptn in formptns"
                            :key="formptn.key"
                        >
                          <label>
                            <input
                              type="radio"
                              v-model="form.initptn"
                              :value="formptn.value"
                            />
                            {{ formptn.label }}
                          </label>
                        </div>
                      </div>
                      <!-- ----------- 設定パターン２部 START ---------------- -->
                      <!-- .sub panel -->
                      <div class="card shadow-pl">
                        <div class="card-body mb-3 p-0 border-top">
                          <!-- .row -->
                          <div class="row">
                            <div class="col-12">
                              <div class="table-responsive">
                                <div class="col-12 p-0">
                                  <table class="table table-striped border-bottom font-size-sm text-nowrap">
                                    <thead>
                                      <tr>
                                        <td class="text-center align-middle w-10">曜日</td>
                                        <td class="text-center align-middle w-20 mw-rem-10">営業日区分<span class="color-red">[必須]</span></td>
                                        <td class="text-center align-middle w-20 mw-rem-10">休暇区分</td>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <tr v-for="(n,index) in 7" :key="index">
                                        <td class="text-center align-middle">{{ formweekdays[index] }}</td>
                                        <td class="text-center align-middle">
                                          <div class="input-group">
                                            <select class="form-control" v-model="form.initptn_business[index]" @change="formbusinessDayChanges(index)">
                                              <option value></option>
                                              <option
                                                v-for="blist in BusinessDayList"
                                                :value="blist.code"
                                                v-bind:key="blist.code"
                                              >{{ blist.code_name }}</option>
                                            </select>
                                          </div>
                                        </td>
                                        <td class="text-center align-middle">
                                          <div class="input-group">
                                            <select class="form-control" v-model="form.initptn_holiday[index]" @change="formholiDayChanges(index)">
                                              <option value></option>
                                              <option
                                                v-for="hlist in HoliDayList"
                                                :value="hlist.code"
                                                v-bind:key="hlist.code"
                                              >{{ hlist.code_name }}</option>
                                            </select>
                                          </div>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- /.row -->
                        </div>
                      </div>
                      <!-- ----------- 設定パターン２部 END ---------------- -->
                    </div>
                  </div>
                </div>
                <!-- /.row -->
              </div>
            </div>
            <!-- /.sub panel -->
            <!-- ----------- 編集入力部 END ---------------- -->
            <!-- ----------- ボタン部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:initstoreclick-event="initstoreclick"
                  v-bind:btn-mode="'initstore'"
                  v-bind:is-push="isstorebutton">
                </btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- ボタン部 END ---------------- -->
          </div>
          <!-- ----------- 項目部 END ---------------- -->
          <!-- /.panel contents -->
        </div>
      </div>
      <!-- /.panel -->
      <!-- ========================== 初期設定部 END ========================== -->
    </div>
    <!-- /main contentns row -->
  </div>
</template>
<script>
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "SettingCalendar",
  mixins: [ dialogable, checkable, requestable ],
  data() {
    return {
      selectedEmploymentValue: "",
      selectedDepartmentValue : "",
      valueDepartmentkillcheck : false,
      department_name: "",
      selectedUserValue : "",
      valueUserkillcheck : false,
      user_name: "",
      applytermdate: "",
      defaultYm: new Date(),
      defaultYear: new Date(),
      defaultMonth: new Date(),
      valueym: "",
      valueyear: "",
      valuemonth: "",
      year: "",
      month: "",
      datejaFormat: "",
      issearchbutton: false,
      isinitbutton: false,
      isfixbutton: false,
      isstorebutton: false,
      selectMode: "",
      messagevalidatesInit: [],
      messagevalidatesDsp: [],
      messagevalidatesEdt: [],
      messagevalidatesInitstore: [],
      messagevalidatesCopyinit: [],
      showC034list: true,
      selectedC034Value: "",
      valueC034killcheck: false,
      showC024list: false,
      selectedC024Value: "",
      valueC024killcheck: false,
      stringtext: "",
      details: [],
      detail_dates: [],
      BusinessDayList: [],
      HoliDayList: [],
      business: [{}],
      holiday: [{}],
      formptns: [
        {
          key: 1,
          value: 1,
          label: '平日【出勤日】土曜日祝日【法定外休日】日曜日【法定休日】の内容で自動設定',
          checked: true,
        },
        {
          key: 2,
          value: 2,
          label: '曜日ごとに自動設定（下記内容を指定します。）',
          checked: false,
        }
      ],
      formweekdays: [
        '月曜日',
        '火曜日',
        '水曜日',
        '木曜日',
        '金曜日',
        '土曜日',
        '日曜日'
      ],
      form: {
        initptn : 1,
        initptn_business : ["","","","","","",""],
        initptn_holiday : ["","","","","","",""]
      },
      detailsEdt: [],
      detailsEdtlength: 0
    };
  },
  // マウント時
  mounted() {
    this.valueym = this.defaultYm;
    this.year = moment(this.valueym).format("YYYY");
    this.month = moment(this.valueym).format("MM");
    this.valueyear = this.year;
    this.valuemonth = this.month;
    this.getGeneralList("C007");
    this.getGeneralList("C008");
    this.setPanelHeader();
  },
  created() {
    this.form.initptn = this.formptns.find(formptn => formptn.checked).value
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション（表示）
    checkFormEdt: function() {
      this.messagevalidatesDsp = [];
      var flag = true;
      // 指定年
      var chkArray = [];
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '指定年';
      chkArray = 
        this.checkHeader(this.valueyear, required, equalength, maxlength, itemname);
      if (chkArray.length == 0) {
        if (this.valueyear < 2000 || this.valuemonth > 2050) {
          chkArray.push("正しい年を入力してください。");
        }
      }
      if (chkArray.length > 0) {
        if (this.messagevalidatesDsp.length == 0) {
          this.messagevalidatesDsp = chkArray;
        } else {
          this.messagevalidatesDsp = this.messagevalidatesDsp.concat(chkArray);
        }
      }
      // 指定月
      chkArray = [];
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = '指定月';
      chkArray = 
        this.checkHeader(this.valuemonth, required, equalength, maxlength, itemname);
      if (chkArray.length == 0) {
        if (this.valuemonth < 1 || this.valuemonth > 12) {
          chkArray.push("1月から12月を入力してください。");
        }
      }
      if (chkArray.length > 0) {
        if (this.messagevalidatesDsp.length == 0) {
          this.messagevalidatesDsp = chkArray;
        } else {
          this.messagevalidatesDsp = this.messagevalidatesDsp.concat(chkArray);
        }
      }


      if (this.messagevalidatesDsp.length > 0) {
        flag = false;
      }
      return flag;
    },
    // バリデーション（更新）
    checkFormFix: function() {
      this.messagevalidatesEdt = [];
      var flag = true;
      // 営業日区分
      var chkArray = [];
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '営業日区分';
      for ( var i=0; i<this.business.length;i++ ) {
        chkArray = 
          this.checkDetailtext(this.business[i], required, equalength, maxlength, itemname, this.detailsEdt['array_user_date_data'][i]['date_name']);
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
    // バリデーション（初期設定）
    checkFormInit: function() {
      this.messagevalidatesInit = [];
      var flag = true;
      // 指定年
      var chkArray = [];
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '指定年';
      chkArray = 
        this.checkHeader(this.valueyear, required, equalength, maxlength, itemname);
      if (chkArray.length == 0) {
        if (this.valueyear < 2000 || this.valuemonth > 2050) {
          chkArray.push("正しい年を入力してください。");
        }
      }
      if (chkArray.length > 0) {
        if (this.messagevalidatesInit.length == 0) {
          this.messagevalidatesInit = chkArray;
        } else {
          this.messagevalidatesInit = this.messagevalidatesInit.concat(chkArray);
        }
      }
      // 指定月
      chkArray = [];
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = '指定月';
      if (this.valuemonth != "" && this.valuemonth != null ) {
        if (this.valuemonth < 1 || this.valuemonth > 12) {
          chkArray.push("1月から12月を入力してください。");
        }
      }
      if (chkArray.length > 0) {
        if (this.messagevalidatesInit.length == 0) {
          this.messagevalidatesInit = chkArray;
        } else {
          this.messagevalidatesInit = this.messagevalidatesInit.concat(chkArray);
        }
      }

      if (this.messagevalidatesInit.length > 0) {
        flag = false;
      }
      return flag;
    },
    // バリデーション（初期設定登録）
    checkFormInitstore: function() {
      this.messagevalidatesInitstore = [];
      var flag = true;
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '';
      // // 設定区分選択
      var chkArray = [];
      if (this.showC024list) {
        required = true;
        equalength = 0;
        maxlength = 0;
        itemname = '設定区分選択';
        chkArray = 
          this.checkHeader(this.selectedC024Value, required, equalength, maxlength, itemname);
        if (chkArray.length > 0) {
          if (this.messagevalidatesInitstore.length == 0) {
            this.messagevalidatesInitstore = chkArray;
          } else {
            this.messagevalidatesInitstore = this.messagevalidatesInitstore.concat(chkArray);
          }
        }
      }
      // パターン
      if (this.form.initptn != 1 && this.form.initptn != 2) {
        this.messagevalidatesInitstore.push(
          "設定内容指定は必ず選択してください。"
        );
      }

      // 営業日区分
      if (this.form.initptn == 2) {
        itemname = '営業日区分';
        for (let index = 0; index < this.form.initptn_business.length; index++) {
          if (this.form.initptn_business[index] == "" || this.form.initptn_business[index] == null) {
            this.messagevalidatesInitstore.push(this.formweekdays[index] + "の" + itemname + "を入力してください");
          }
        }
      }

      if (this.messagevalidatesInitstore.length > 0) {
        flag = false;
      }
      return flag;
    },
    // バリデーション（複写設定）
    checkFormCopyinit: function() {
      this.messagevalidatesCopyinit = [];
      var flag = true;
      // 指定年
      var chkArray = [];
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '指定年';
      chkArray = 
        this.checkHeader(this.valueyear, required, equalength, maxlength, itemname);
      if (chkArray.length == 0) {
        if (this.valueyear < 2000 || this.valuemonth > 2050) {
          chkArray.push("正しい年を入力してください。");
        }
      }
      if (chkArray.length > 0) {
        if (this.messagevalidatesCopyinit.length == 0) {
          this.messagevalidatesCopyinit = chkArray;
        } else {
          this.messagevalidatesCopyinit = this.messagevalidatesCopyinit.concat(chkArray);
        }
      }
      // 指定月
      chkArray = [];
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = '指定月';
      chkArray = 
        this.checkHeader(this.valuemonth, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesCopyinit.length == 0) {
          this.messagevalidatesCopyinit = chkArray;
        } else {
          this.messagevalidatesCopyinit = this.messagevalidatesCopyinit.concat(chkArray);
        }
      }

      if (this.messagevalidatesCopyinit.length > 0) {
        flag = false;
      }
      return flag;
    },
    // ------------------------ イベント処理 ------------------------------------
    
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
      this.isinitbutton = false;
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
      this.isinitbutton = false;
    },
    // ユーザー選択が変更された場合の処理
    userChanges: function(value, arrayitem) {
      this.selectedUserValue = value;
      this.user_name = arrayitem['name'];
      this.selectedName = this.user_name + "　" + this.date_name + "分勤怠編集" ;
      this.selectMode = '';
      this.isinitbutton = false;
    },
    // 指定年が変更された場合の処理
    fromyearChanges: function(value) {
      this.valueyear = value;
      // パネルに表示
      this.setPanelHeader();
      this.selectMode = '';
      this.isinitbutton = false;
    },
    // 指定年がクリアされた場合の処理
    fromyearCleared: function() {
      this.valueyear = "";
      // パネルに表示
      this.setPanelHeader();
      this.selectMode = '';
      this.isinitbutton = false;
    },
    // 指定月が変更された場合の処理
    frommonthChanges: function(value) {
      this.valuemonth = value;
      this.showC024list = false;
      this.showC034list = true;
      this.selectedC024Value = "";
      this.selectedC034Value = "";
      // this.refreshC024C034list(this.showC024list ,this.showC034list);
      // パネルに表示
      this.setPanelHeader();
      this.selectMode = '';
      this.isinitbutton = false;
    },
    // 指定月がクリアされた場合の処理
    frommonthCleared: function() {
      this.valuemonth = "";
      this.selectedC024Value = "";
      this.selectedC034Value = "";
      this.showC024list = true;
      this.showC034list = false;
      // this.refreshC024C034list(this.showC024list ,this.showC034list);
      // パネルに表示
      this.setPanelHeader();
      this.selectMode = '';
      this.isinitbutton = false;
    },
    // 設定区分が変更された場合の処理
    C024Changes: function(value, arrayItem) {
      this.selectedC024Value = value;
      // パネルに表示
      this.setPanelHeader();
    },
    // 設定区分が変更された場合の処理
    C034Changes: function(value, arrayItem) {
      this.selectedC034Value = value;
      // パネルに表示
      this.setPanelHeader();
    },
    // 出勤区分がクリアされた場合の処理
    businessDayChanges: function(value, index) {
      if (value < 2) {
        this.holiday[index] = null;
      }
    },
    // 休暇区分がクリアされた場合の処理
    holiDayChanges: function(value, index) {
      if (value < 1) {
        this.business[index] = 1;
      } else {
        this.business[index] = 3;
      }
    },
    // 出勤区分がクリアされた場合の処理
    formbusinessDayChanges: function(value, index) {
      if (value < 2) {
        this.initptn_holiday[index] = null;
      }
    },
    // 休暇区分がクリアされた場合の処理
    formholiDayChanges: function(value, index) {
      if (value < 1) {
        this.initptn_business[index] = 1;
      } else {
        this.initptn_business[index] = 3;
      }
    },
    // 表示ボタンクリック処理
    searchclick() {
      // 入力項目クリア
      this.inputClear();
      this.messagevalidatesInit = [];
      this.messagevalidatesDsp = [];
      this.messagevalidatesEdt = [];
      this.messagevalidatesInitstore = [];
      this.messagevalidatesCopyinit = [];
      if (this.checkFormEdt()) {
        this.selectMode = 'DSP';
        this.isinitbutton = false;
        this.getItem();
      }
    },
    // 明細編集ボタンクリックされた場合の処理
    detailEdtClick: function(e, arrayitem) {
      var index = arrayitem['rowIndex'];
      this.selectMode = 'EDT';
      this.isinitbutton = false;
      this.detailsEdt = this.details[index];
      this.detailsEdtlength = Object.keys(this.detailsEdt).length;
      var detailsEdtdatedata = this.detailsEdt['array_user_date_data'];
      var detailsEdtdatedatalength = Object.keys(detailsEdtdatedata).length;
      for (var i=0; i<detailsEdtdatedata.length; i++) {
        this.business[i] = detailsEdtdatedata[i]['business_kubun'];
        this.holiday[i] = detailsEdtdatedata[i]['holiday_kubun'];
      }
    },
    // 初期設定ボタンクリック処理
    initclick() {
      // 入力項目クリア
      this.inputClear();
      this.messagevalidatesInit = [];
      this.messagevalidatesDsp = [];
      this.messagevalidatesEdt = [];
      this.messagevalidatesInitstore = [];
      this.messagevalidatesCopyinit = [];
      var flag = this.checkFormInit();
      if (flag) {
        this.selectMode = 'INT';
        if (this.valuemonth == "" || this.valuemonth == null) {
          this.frommonthCleared();
        } else {
          if (this.valuemonth > 0) {
            this.frommonthChanges(this.valuemonth);
          } else {
            this.frommonthCleared();
          }
        }
        this.selectMode = 'INT';
      // 項目数が多い場合以下コメントアウト
      // } else {
      //   this.countswal("エラー", this.messagevalidatesInit, "error", true, false, true)
      //     .then(result  => {
      //       if (result) {
      //       }
      //   });
      }
    },
    //更新ボタンクリック処理
    fixclick() {
      this.messagevalidatesInit = [];
      this.messagevalidatesDsp = [];
      this.messagevalidatesEdt = [];
      this.messagevalidatesInitstore = [];
      this.messagevalidatesCopyinit = [];
      var flag = this.checkFormFix();
      if (flag) {
        var messages = [];
        messages.push("この内容で更新しますか？");
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
    //初期設定登録ボタンクリック処理
    initstoreclick() {
      this.messagevalidatesInit = [];
      this.messagevalidatesDsp = [];
      this.messagevalidatesEdt = [];
      this.messagevalidatesInitstore = [];
      this.messagevalidatesCopyinit = [];
      var flag = this.checkFormInitstore();
      if (flag) {
        var messages = [];
        messages.push("指定年（月）に登録しているデータを上書きしますが、");
        messages.push("初期設定登録してもよろしいですか？");
        messages.push("※指定期間が長いと処理には数分かかる場合があります。");
        this.htmlMessageSwal("確認", messages, "info", true, true)
          .then(result  => {
            if (result) {
              this.initStore("初期設定登録", this.form.initptn);
            }
        });
      // 項目数が多い場合以下コメントアウト
      } else {
        this.countswal("エラー", this.messagevalidatesInitstore, "error", true, false, true)
          .then(result  => {
            if (result) {
            }
        });
      }
    },
    // 複写設定ボタンクリック処理
    copyinitclick() {
      // 入力項目クリア
      this.inputClear();
      this.messagevalidatesInit = [];
      this.messagevalidatesDsp = [];
      this.messagevalidatesEdt = [];
      this.messagevalidatesInitstore = [];
      this.messagevalidatesCopyinit = [];
      var flag = this.checkFormCopyinit();
      if (flag) {
        this.selectMode = 'INT';
        // パネルに表示
        this.setPanelHeader();
        this.copyInit();
      // 項目数が多い場合以下コメントアウト
      // } else {
      //   this.countswal("エラー", this.messagevalidatesCopyinit, "error", true, false, true)
      //     .then(result  => {
      //       if (result) {
      //       }
      //   });
      }
    },

    // -------------------- サーバー処理 ----------------------------
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
    // カレンダー取得処理
    getItem() {
      var parammonth = null;
      if (this.valuemonth != "") {
        parammonth = moment(this.valuemonth).format("MM");
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
            dateyear : moment(this.valueyear + '0115').format("YYYY"),
            datemonth : parammonth,
            employmentstatus : this.selectedEmploymentValue,
            departmentcode : this.selectedDepartmentValue,
            usercode : this.selectedUserValue
          };
          this.postRequest("/setting_calendar/get", arrayParams)
            .then(response  => {
              this.$swal.close();
              this.getThen(response);
            })
            .catch(reason => {
              this.$swal.close();
              this.serverCatch("カレンダー", "取得");
            });
        }
      });
    },
    // カレンダー登録処理
    initStore(eventname, ptn) {
      var messages = [];
      var parammonth = null;
      if (this.valuemonth != "") {
        parammonth = moment(this.valuemonth).format("MM");
      }
      var paramselectedC024Value = this.selectedC024Value;
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
            ptn : ptn,
            dateyear : moment(this.valueyear + '0115').format("YYYY"),
            datemonth : parammonth,
            displaykbn : paramselectedC024Value,
            employmentstatus : this.selectedEmploymentValue,
            departmentcode : this.selectedDepartmentValue,
            usercode : this.selectedUserValue,
            formdata : this.form
          };
          this.postRequest("/setting_calendar/init", arrayParams)
            .then(response  => {
              this.$swal.close();
              this.putThenDetail(response, eventname);
            })
            .catch(reason => {
              this.$swal.close();
              this.serverCatch("カレンダー", eventname);
          });
        }
      });
    },
    // カレンダー複写処理
    copyInit(eventname) {
      var messages = [];
      var parammonth = null;
      if (this.valuemonth != "") {
        parammonth = moment(this.valuemonth).format("MM");
      }
      var paramselectedC024Value = this.selectedC024Value;
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
            dateyear : moment(this.valueyear + '0115').format("YYYY"),
            datemonth : parammonth,
            employmentstatus : this.selectedEmploymentValue,
            departmentcode : this.selectedDepartmentValue,
            usercode : this.selectedUserValue,
          };
          this.postRequest("/setting_calendar/copyinit", arrayParams)
            .then(response  => {
              this.$swal.close();
              this.putThenDetail(response, eventname);
            })
            .catch(reason => {
              this.$swal.close();
              this.serverCatch("カレンダー", eventname);
          });
        }
      });
    },
    // カレンダー更新処理（明細）
    FixDetail(eventname) {
      var arrayParams = { details : this.detailsEdt, businessdays : this.business, holidays : this.holiday };
      this.postRequest("/setting_calendar/fix", arrayParams)
        .then(response  => {
          this.putThenDetail(response, eventname);
        })
        .catch(reason => {
          this.serverCatch("カレンダー", eventname);
        });
    },
    // コード選択リスト取得処理
    getGeneralList(value) {
      var arrayParams = { identificationid : value };
      this.postRequest("/get_general_list", arrayParams)
        .then(response  => {
          if (value == "C007") {
            this.getThenbusinesskbn(response, "出勤区分");
          }
          if (value == "C008") {
            this.getThenholidaykbn(response, "休暇区分");
          }
        })
        .catch(reason => {
          if (value == "C007") {
            this.serverCatch("出勤区分", "取得");
          }
          if (value == "C008") {
            this.serverCatch("休暇区分", "取得");
          }
        });
    },
    // -------------------- 共通 ----------------------------
    // 取得正常処理
    getThen(response) {
      this.details = [];
      this.business = [{}];
      this.holiday = [{}];
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        this.detail_dates = res.detail_dates;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false)
        } else {
          this.serverCatch("カレンダー", "取得");
        }
      }
    },
    // 更新系正常処理
    putThenHead(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.$toasted.show("カレンダーを" + eventtext + "しました");
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false)
        } else {
          this.serverCatch("カレンダー",eventtext);
        }
      }
    },
    // 更新系正常処理（明細）
    putThenDetail(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.$toasted.show("カレンダーを" + eventtext + "しました");
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false)
        } else {
          this.serverCatch("カレンダー", eventtext);
        }
      }
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false)
    },
    // 取得正常処理（明細出勤区分）
    getThenbusinesskbn(response) {
      var res = response.data;
      if (res.result) {
        this.BusinessDayList = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false)
        } else {
          this.serverCatch("出勤区分", "取得");
        }
      }
    },
    // 取得正常処理（明細休暇区分）
    getThenholidaykbn(response, value) {
      var res = response.data;
      if (res.result) {
        this.HoliDayList = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false)
        } else {
          this.serverCatch("休暇区分", "取得");
        }
      }
    },
    inputClear() {
      this.details = [];
    },
    // 集計パネルヘッダ文字列編集処理
    setPanelHeader: function() {
      moment.locale("ja");
      var datejaFormat = "";
      this.stringtext = "";
      if (this.valueyear != null && this.valueyear != "") {
        if (this.valuemonth != null && this.valuemonth != "") {
          datejaFormat +=  moment(this.valueyear + this.valuemonth + '15').format("YYYY年MM月");
          if (this.selectMode == 'INT') {
            this.stringtext =
              datejaFormat + "のカレンダーを1日から設定";
          } else {
            this.stringtext =
              datejaFormat + "のカレンダーを1日から表示";
          }
        } else {
          datejaFormat +=  moment(this.valueyear + '0115').format("YYYY年");
          if (this.selectedC024Value != null && this.selectedC024Value != "") {
            if (this.selectedC024Value == "1") {
              this.stringtext =
                datejaFormat + "のカレンダーを期首月１日から設定";
            } else {
              this.stringtext =
                datejaFormat + "のカレンダーを１月１日から設定";
            }
          }
        }
      }
      console.log('this.datejaFormat = ' + this.datejaFormat);
      console.log('this.stringtext = ' + this.stringtext);
    },
    // 最新リストの表示
    refreshC024C034list(showC024, showC034) {
      if (showC024) {
        this.showC024list = false;
        this.$nextTick(() => (this.showC024list = showC024));
      } else {
        this.showC024list = false;
        this.$nextTick(() => (this.showC024list = showC024));
      }
      // if (showC034) {
      //   this.showC034list = false;
      //   this.$nextTick(() => (this.showC034list = showC034));
      // } else {
      //   this.showC034list = false;
      //   this.$nextTick(() => (this.showC034list = showC034));
      // }
    }
  }
};
</script>
<style scoped>
.table th, .table td {
    padding: 0rem !important;
}
</style>
