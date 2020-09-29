<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆年度指定'"
            v-bind:header-text2="'設定する年度と期首月を指定します'"
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
                      class="input-group-text font-size-sm line-height-xs label-width-230"
                      id="basic-addon1"
                    >年度指定<span class="color-red">[必須]</span></span>
                  </div>
                  <input-datepicker
                    v-bind:default-date="defaultDate"
                    v-bind:date-format="'yyyy年'"
                    v-bind:place-holder="'年度を選択してください'"
                    v-on:change-event="fromdateChanges"
                    v-on:clear-event="fromdateCleared"
                  ></input-datepicker>
                </div>
                <message-data v-bind:message-datas="messagedatayear" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-230"
                      id="basic-addon1"
                    >期首月<span class="color-red">[必須]</span></span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="number"
                      name="biginningMonth"
                      title="期首月"
                      max="12"
                      min="1"
                      step="1"
                      class="form-control"
                      v-model="form.biginningMonth"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedatabiginningMonth" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- panel header -->
              <div class="card-header col-12 bg-transparent pb-2 border-0">
                <h1 class="float-sm-left font-size-rg">◆36協定設定</h1>
                <span class="float-sm-right font-size-sm">特別条項を適用しない場合にかっこ内の数値内で上限時間を設定します。</span>
              </div>
              <!-- /.panel header -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-12 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-230"
                      id="basic-addon1"
                    >１ヶ月累計（月45時間以内）</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="number"
                      title="月45時間"
                      max="45.00"
                      min="00.00"
                      step="0.01"
                      class="form-control"
                      :value="valueoneMonthTotal"
                      @change="oneMonthTotalChanges"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedataoneMonthTotal" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-12 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-230"
                      id="basic-addon1"
                    >２ヶ月累計（81時間以内）</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="number"
                      title="２ヶ月累計（81時間以内）"
                      max="81.00"
                      min="00.00"
                      step="0.01"
                      class="form-control"
                      :value="valuetwoMonthTotal"
                      @change="twoMonthTotalChanges"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedatatwoMonthTotal" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-12 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-230"
                      id="basic-addon1"
                    >３ヶ月累計（120時間以内）</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="number"
                      title="３ヶ月累計（120時間以内）"
                      max="120.00"
                      min="00.00"
                      step="0.01"
                      class="form-control"
                      :value="valuethreeMonthTotal"
                      @change="threeMonthTotalChanges"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedatathreeMonthTotal" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-12 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-230"
                      id="basic-addon1"
                    >１年累計（年360時間以内）</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="number"
                      title="年360時間"
                      max="360.00"
                      min="00.00"
                      step="0.01"
                      class="form-control"
                      v-model="form.yearTotal"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedatayearTotal" v-bind:message-class="'warning'"></message-data>
              </div>
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- panel header -->
              <div class="card-header col-12 bg-transparent pb-2 border-0">
                <h1 class="float-sm-left font-size-rg">◆36協定特別条項設定</h1>
                <span class="float-sm-right font-size-sm">特別条項を適用する場合にかっこ内の数値内で上限時間または回数を設定します。</span>
              </div>
              <!-- /.panel header -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-12 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-230"
                      id="basic-addon1"
                    >１ヶ月累計（100時間未満）</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="number"
                      title="月100時間未満"
                      max="100.00"
                      min="00.00"
                      step="0.01"
                      class="form-control"
                      :value="valuesp_oneMonthTotal"
                      @change="oneSpMonthTotalChanges"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedatasponeMonthTotal" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-12 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-230"
                      id="basic-addon1"
                    >１年累計（720時間以内）</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="number"
                      title="年720時間以内"
                      max="720.00"
                      min="00.00"
                      step="0.01"
                      class="form-control"
                      v-model="form.sp_yearTotal"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedataspyearTotal" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- .col -->
              <div class="col-12 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-230"
                      id="basic-addon1"
                    >2～6ヵ月の平均時間 （80時間以内）</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="number"
                      title="平均80時間以内"
                      max="80.00"
                      min="00.00"
                      step="0.01"
                      class="form-control"
                      v-model="form.sp_ave_2_6"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedataspave_2_6" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-12 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-230"
                      id="basic-addon1"
                    >特別条項の回数 （年6回以内）</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="number"
                      title="年6回"
                      max="6"
                      min="0"
                      step="1"
                      class="form-control"
                      v-model="form.sp_count"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedataspcount" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- panel header -->
              <div class="card-header col-12 bg-transparent pb-2 border-0">
                <h1 class="float-sm-left font-size-rg">◆働き方改革</h1>
                <span class="float-sm-right font-size-sm">次の勤務と見なすまでの時間インターバルを設定します</span>
              </div>
              <!-- /.panel header -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-230"
                      id="basic-addon1"
                    >勤務間インターバル</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="number"
                      title="勤務間インターバル"
                      max="24.00"
                      min="00.00"
                      step="0.25"
                      class="form-control"
                      v-model="form.sp_interval"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedataspinterval" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- panel header -->
              <div class="card-header col-12 bg-transparent pb-2 border-0">
                <h1 class="float-sm-left font-size-rg">◆集計自動起動設定</h1>
                <span class="float-sm-right font-size-sm">設定した時刻に当月度の間集計を自動で行います。</span>
              </div>
              <!-- /.panel header -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <input-time
                  v-bind:item-title="'自動起動時刻'"
                  v-bind:item-required="true"
                  v-bind:value-data="valuecalcauto"
                  v-bind:classData="'input-group-text font-size-sm line-height-xs label-width-230'"
                  v-on:change-event="autotimeChanges">
                ></input-time>
                <message-data v-bind:message-datas="messagedatavaluecalcauto" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.panel contents -->
            <!-- /.panel contents -->
          </div>
          <div class="card-body mb-3 p-0 border-top">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between px-3">
              <!-- panel header -->
              <div class="card-header col-12 bg-transparent pb-2 border-0">
                <h1 class="float-sm-left font-size-rg">◆月度別設定</h1>
                <span class="float-sm-right font-size-sm">月度ごとに締日と時間に関する設定ができます</span>
              </div>
              <!-- /.panel header -->
              <message-data v-bind:message-datas="messagedataclosing" v-bind:message-class="'warning'"></message-data>
              <message-data v-bind:message-datas="messagedataupTime" v-bind:message-class="'warning'"></message-data>
              <message-data v-bind:message-datas="messagedatatimeunit" v-bind:message-class="'warning'"></message-data>
              <message-data v-bind:message-datas="messagedatatimeround" v-bind:message-class="'warning'"></message-data>
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <div class="col-12 p-0">
                    <table class="table table-striped border-bottom font-size-sm text-nowrap">
                      <thead>
                        <tr>
                          <td class="text-center align-middle w-10">月度<span class="color-red">[必須]</span></td>
                          <td class="text-center align-middle w-20 mw-rem-10">締日<span class="color-red">[必須]</span></td>
                          <td class="text-center align-middle w-20 mw-rem-10"
                            data-toggle="tooltip"
                            data-placement="top"
                            v-bind:title="edtString"
                            @mouseover="edttooltips('36協定特別条項適用しない場合は36協定設定の１ヶ月累計時間以内、適用の場合は36協定特別条項設定の１ヶ月累計時間以内','')"
                          >上限残業時間<span class="color-red">[必須]</span></td>
                          <td class="text-center align-middle w-20 mw-rem-15">時間単位<span class="color-red">[必須]</span></td>
                          <td class="text-center align-middle w-20 mw-rem-15">時間の丸め<span class="color-red">[必須]</span></td>
                          <td class="text-center align-middle"></td>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(n,index) in 12" :key="index">
                          <td class="text-center align-middle">{{ n }}月</td>
                          <td class="text-center align-middle">
                            <div class="input-group">
                              <select class="form-control" v-model="form.closingDate[index]" @change="days_maxChanges(index)">
                                <option value></option>
                                <option
                                  v-for="n in get_Days(index)"
                                  :value="n"
                                  v-bind:key="n"
                                >{{ n }} 日</option>
                              </select>
                            </div>
                          </td>
                          <td class="text-center align-middle">
                            <div class="input-group">
                              <input
                                title="36協定特別条項適用しない場合は36協定設定の１ヶ月累計時間以内、適用の場合は36協定特別条項設定の１ヶ月累計時間以内"
                                type="number"
                                v-bind:max="limit_valueoneMonthTotal"
                                min="0.00"
                                step="0.50"
                                class="form-control"
                                v-model="form.upTime[index]"
                                @change="upTimeChanges(index)"
                              />
                            </div>
                          </td>
                          <td class="text-center align-middle">
                            <div class="input-group">
                              <select class="form-control" v-model="form.timeunit[index]" @change="timeunitChanges(index)">
                                <option value></option>
                                <option
                                  v-for="tulist in get_c009"
                                  :value="tulist.code"
                                  v-bind:key="tulist.code"
                                >{{ tulist.code_name }}</option>
                              </select>
                            </div>
                          </td>
                          <td class="text-center align-middle">
                            <div class="btn-group d-flex">
                              <select class="form-control" v-model="form.timeround[index]" @change="timeroundChanges(index)">
                                <option value></option>
                                <option
                                  v-for="trlist in get_c010"
                                  :value="trlist.code"
                                  v-bind:key="trlist.code"
                                >{{ trlist.code_name }}</option>
                              </select>
                            </div>
                          </td>
                          <td class="text-center align-middle">
                            <input type="hidden" v-model="hidden" value />
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.row -->
            <!-- ----------- ボタン部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:storeclick-event="storeclick"
                  v-bind:btn-mode="'store'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /panel body -->
        </div>
      </div>
      <!-- /.panel -->
    </div>
    <!-- /main contentns row -->
  </div>
</template>
<script>
import toasted from "vue-toasted";
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';
// CONST
const CONST_C009 = "C009";
const CONST_C010 = "C010";

export default {
  name: "SettingCalc",
  mixins: [ dialogable, checkable, requestable ],
  props: {
    authusers: {
      type: Array,
      default: []
    },
    isexistdownload: {
      type: String,
      default: ""
    },
    settingcompanies: {
      type: String,
      default: ""
    },
    settingdepartments: {
      type: String,
      default: ""
    },
    settingsettings: {
      type: String,
      default: ""
    },
    settingworkingtimetables: {
      type: String,
      default: ""
    },
    settingcalendarsettinginformations: {
      type: String,
      default: ""
    },
    settingusers: {
      type: String,
      default: ""
    },
    const_generaldatas: {
      type: Array,
      default: []
    }
  },
  data() {
    return {
      defaultDate: new Date(),
      year: "",
      bMonth: "",
      valueymd: "",
      valueoneMonthTotal: "",
      valuetwoMonthTotal: "",
      valuethreeMonthTotal: "",
      valuesp_oneMonthTotal: "",
      before_valueoneMonthTotal: "",
      before_valuesp_oneMonthTotal: "",
      limit_valueoneMonthTotal: 100,
      valuecalcauto: "",
      form: {
        year: "",
        biginningMonth: "",
        calc_auto_time: "",
        oneMonthTotal: "",
        twoMonthTotal: "",
        threeMonthTotal: "",
        yearTotal: "",
        sp_oneMonthTotal: "",
        sp_yearTotal: "",
        sp_ave_2_6: "",
        sp_count: "",
        sp_interval: "",
        closingDate: [{}],
        upTime: [{}],
        timeunit: [{}],
        timeround: [{}]
      },
      edtString: "",
      days_max: [],
      baseYear: "",
      details: [],
      hidden: "",
      testList: [],
      messagedatayear: [],
      messagedatabiginningMonth: [],
      messagedataoneMonthTotal: [],
      messagedatatwoMonthTotal: [],
      messagedatathreeMonthTotal: [],
      messagedatayearTotal: [],
      messagedatasponeMonthTotal: [],
      messagedataspyearTotal: [],
      messagedataspave_2_6: [],
      messagedataspcount: [],
      messagedataspinterval: [],
      messagedatavaluecalcauto: [],
      messagedataclosing: [],
      messagedataupTime: [],
      messagedatatimeunit: [],
      messagedatatimeround: [],
      messagedatastore: [],
      const_C009_data: [],
      const_C010_data: [],
      storeisPush: false
    };
  },
  computed: {
    get_c009: function() {
      if (this.const_C009_data.length == 0) {
        var i = 0;
        let $this = this;
        this.const_generaldatas.forEach(function(item) {
          if (item.identification_id == CONST_C009) {
            $this.const_C009_data.push($this.const_generaldatas[i]);
          }
          i++;
        });
      }
      return this.const_C009_data;
    },
    get_c010: function() {
      if (this.const_C010_data.length == 0) {
        var i = 0;
        let $this = this;
        this.const_generaldatas.forEach(function(item) {
          if (item.identification_id == CONST_C010) {
            $this.const_C010_data.push($this.const_generaldatas[i]);
          }
          i++;
        });
      }
      return this.const_C010_data;
    },
    // 月の月末リスト作成
    get_Maxdays: function() {
      if (this.days_max.length == 0) {
        for (let index = 0; index < 12; index++) {
          var month = index + 1;
          this.days_max[index] = new Date(this.form.year, month, 0).getDate();
        }
      }
      return this.days_max;
    },
    // 月の月末取得
    get_Days: function() {
      if (this.days_max.length == 0) {
        this.get_Maxdays;
      }
      let self = this;
      return function (index) {
        return self.days_max[index];
      }
    }
  },
  // マウント時
  mounted() {
    var date = new Date();
    this.valueymd = moment(this.defaultDate).format("YYYY");
    this.year = moment(this.valueymd).format("YYYY");
    this.inputClear();
    this.messageClear();
    // this.baseYear = date.getFullYear();
    this.getItem();
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション
    checkForm: function() {
      var flag = true;
      this.messageClear();

      if (this.form.year == "" || this.form.year == null) {
        this.messagedatayear.push("年度を入力してください");
      }
      if (this.messagedatayear.length > 0) {
        if (this.messagedatastore.length == 0) {
          this.messagedatastore = this.messagedatayear;
        } else {
          this.messagedatastore = this.messagedatastore.concat(this.messagedatayear);
        }
      }
      if (this.form.biginningMonth == "" || this.form.biginningMonth == null) {
        this.messagedatabiginningMonth.push("期首月を入力してください");
      } else if (parseInt(this.form.biginningMonth) < 1 || parseInt(this.form.biginningMonth) > 12) {
          this.messagedatabiginningMonth.push("1~12の範囲で入力してください");
      } else {
        this.form.biginningMonth = parseInt(this.form.biginningMonth);
      }
      if (this.messagedatabiginningMonth.length > 0) {
        if (this.messagedatastore.length == 0) {
          this.messagedatastore = this.messagedatabiginningMonth;
        } else {
          this.messagedatastore = this.messagedatastore.concat(this.messagedatabiginningMonth);
        }
      }
      if (this.form.oneMonthTotal != "" && this.form.oneMonthTotal != null) {
        if (parseFloat(this.form.oneMonthTotal) < 1 || parseFloat(this.form.oneMonthTotal) > 45) {
          this.messagedataoneMonthTotal.push("1~45の範囲で入力してください");
        }
      }
      if (this.messagedataoneMonthTotal.length > 0) {
        if (this.messagedatastore.length == 0) {
          this.messagedatastore = this.messagedataoneMonthTotal;
        } else {
          this.messagedatastore = this.messagedatastore.concat(this.messagedataoneMonthTotal);
        }
      }
      if (this.form.twoMonthTotal != "" && this.form.twoMonthTotal != null) {
        if (parseFloat(this.form.twoMonthTotal) < 1 || parseFloat(this.form.twoMonthTotal) > 81) {
          this.messagedatatwoMonthTotal.push("1~81の範囲で入力してください");
        }
      }
      if (this.messagedatatwoMonthTotal.length > 0) {
        if (this.messagedatastore.length == 0) {
          this.messagedatastore = this.messagedatatwoMonthTotal;
        } else {
          this.messagedatastore = this.messagedatastore.concat(this.messagedatatwoMonthTotal);
        }
      }
      if (this.form.threeMonthTotal != "" && this.form.threeMonthTotal != null) {
        if (parseFloat(this.form.threeMonthTotal) < 1 || parseFloat(this.form.threeMonthTotal) > 120) {
          this.messagedatathreeMonthTotal.push("1~120の範囲で入力してください");
        }
      }
      if (this.messagedatathreeMonthTotal.length > 0) {
        if (this.messagedatastore.length == 0) {
          this.messagedatastore = this.messagedatathreeMonthTotal;
        } else {
          this.messagedatastore = this.messagedatastore.concat(this.messagedatathreeMonthTotal);
        }
      }
      if (this.form.yearTotal != "" && this.form.yearTotal != null) {
        if (parseFloat(this.form.yearTotal) < 1 || parseFloat(this.form.yearTotal) > 360) {
          this.messagedatayearTotal.push("1~360の範囲で入力してください");
        }
      }
      if (this.messagedatayearTotal.length > 0) {
        if (this.messagedatastore.length == 0) {
          this.messagedatastore = this.messagedatayearTotal;
        } else {
          this.messagedatastore = this.messagedatastore.concat(this.messagedatayearTotal);
        }
      }
      if (this.form.sp_oneMonthTotal != "" && this.form.sp_oneMonthTotal != null) {
        if (parseFloat(this.form.sp_oneMonthTotal) < 1 || parseFloat(this.form.sp_oneMonthTotal) > 100) {
          this.messagedatasponeMonthTotal.push("1~100の範囲で入力してください");
        }
      }
      if (this.messagedatasponeMonthTotal.length > 0) {
        if (this.messagedatastore.length == 0) {
          this.messagedatastore = this.messagedatasponeMonthTotal;
        } else {
          this.messagedatastore = this.messagedatastore.concat(this.messagedatasponeMonthTotal);
        }
      }
      if (this.form.sp_yearTotal != "" && this.form.sp_yearTotal != null) {
        if (parseFloat(this.form.sp_yearTotal) < 1 || parseFloat(this.form.sp_yearTotal) > 720) {
          this.messagedataspyearTotal.push("1~720の範囲で入力してください");
        }
      }
      if (this.messagedataspyearTotal.length > 0) {
        if (this.messagedatastore.length == 0) {
          this.messagedatastore = this.messagedataspyearTotal;
        } else {
          this.messagedatastore = this.messagedatastore.concat(this.messagedataspyearTotal);
        }
      }
      if (this.form.sp_ave_2_6 != "" && this.form.sp_ave_2_6 != null) {
        if (parseFloat(this.form.sp_ave_2_6) < 1 || parseFloat(this.form.sp_ave_2_6) > 80) {
          this.messagedataspave_2_6.push("1~80の範囲で入力してください");
        }
      }
      if (this.messagedataspave_2_6.length > 0) {
        if (this.messagedatastore.length == 0) {
          this.messagedatastore = this.messagedataspave_2_6;
        } else {
          this.messagedatastore = this.messagedatastore.concat(this.messagedataspave_2_6);
        }
      }
      if (this.form.sp_count != "" && this.form.sp_count == null) {
        if (parseInt(this.form.sp_count) < 1 || parseInt(this.form.sp_count) > 6) {
          this.messagedataspcount.push("1~6の範囲で入力してください");
        } else {
          this.form.sp_count = parseInt(this.form.sp_count);
        }
      }
      if (this.messagedataspcount.length > 0) {
        if (this.messagedatastore.length == 0) {
          this.messagedatastore = this.messagedataspcount;
        } else {
          this.messagedatastore = this.messagedatastore.concat(this.messagedataspcount);
        }
      }
      if (this.form.sp_interval != "" && this.form.sp_interval != null) {
        if (parseFloat(this.form.sp_interval) < 0 || parseFloat(this.form.sp_interval) > 8) {
          this.messagedataspinterval.push("0~8の範囲で入力してください");
        }
      }
      if (this.messagedataspinterval.length > 0) {
        if (this.messagedatastore.length == 0) {
          this.messagedatastore = this.messagedataspinterval;
        } else {
          this.messagedatastore = this.messagedatastore.concat(this.messagedataspinterval);
        }
      }
      if (this.form.calc_auto_time == "" || this.form.calc_auto_time == null) {
        this.messagedatavaluecalcauto.push("自動起動時刻を入力してください");
      }
      if (this.messagedatavaluecalcauto.length > 0) {
        if (this.messagedatastore.length == 0) {
          this.messagedatastore = this.messagedatavaluecalcauto;
        } else {
          this.messagedatastore = this.messagedatastore.concat(this.messagedatavaluecalcauto);
        }
      }
      for (let index = 0; index < this.form.closingDate.length; index++) {
        if (this.form.closingDate[index] == "" || this.form.closingDate[index] == null) {
          this.messagedataclosing.push(index+1 + "月の締日を入力してください");
        }
      }
      if (this.messagedataclosing.length > 0) {
        if (this.messagedatastore.length == 0) {
          this.messagedatastore = this.messagedataclosing;
        } else {
          this.messagedatastore = this.messagedatastore.concat(this.messagedataclosing);
        }
      }
      for (let index = 0; index < this.form.upTime.length; index++) {
        if (this.form.upTime[index] == "" || this.form.upTime[index] == null) {
          this.messagedataupTime.push(index+1 + "月の上限残業時間を入力してください");
        } else {
          if (this.form.sp_oneMonthTotal != "" && this.form.sp_oneMonthTotal != null) {
            if (parseFloat(this.form.upTime[index]) < 1 || parseFloat(this.form.upTime[index]) > parseFloat(this.form.sp_oneMonthTotal)) {
              this.messagedataupTime.push(index+1 + "月の上限残業時間は1~" + this.form.sp_oneMonthTotal + "の範囲で入力してください");
            }
          } else {
            if (this.form.oneMonthTotal != "" && this.form.oneMonthTotal != null) {
              if (parseFloat(this.form.upTime[index]) < 1 || parseFloat(this.form.upTime[index])> parseFloat(this.form.oneMonthTotal)) {
                this.messagedataupTime.push(index+1 + "月の上限残業時間は1~" + this.form.oneMonthTotal + "の範囲で入力してください");
              }
            }
          }
        }
      }
      if (this.messagedataupTime.length > 0) {
        if (this.messagedatastore.length == 0) {
          this.messagedatastore = this.messagedataupTime;
        } else {
          this.messagedatastore = this.messagedatastore.concat(this.messagedataupTime);
        }
      }
      for (let index = 0; index < this.form.timeunit.length; index++) {
        if (this.form.timeunit[index] == "" || this.form.timeunit[index] == null) {
          this.messagedatatimeunit.push(index+1 + "月の時間単位を入力してください");
        }
      }
      if (this.messagedatatimeunit.length > 0) {
        if (this.messagedatastore.length == 0) {
          this.messagedatastore = this.messagedatatimeunit;
        } else {
          this.messagedatastore = this.messagedatastore.concat(this.messagedatatimeunit);
        }
      }
      for (let index = 0; index < this.form.timeround.length; index++) {
        if (this.form.timeround[index] == "" || this.form.timeround[index] == null) {
          this.messagedatatimeround.push(index+1 + "月の時間の丸めを入力してください");
        }
      }
      if (this.messagedatatimeround.length > 0) {
        if (this.messagedatastore.length == 0) {
          this.messagedatastore = this.messagedatatimeround;
        } else {
          this.messagedatastore = this.messagedatastore.concat(this.messagedatatimeround);
        }
      }
      if (this.messagedatastore.length > 0) {
        flag  = false;
      }
      return flag;
    },
    // ------------------------ イベント処理 ------------------------------------
    // 指定年月が変更された場合の処理
    fromdateChanges: function(value) {
      this.valueymd = value;
      this.year = moment(this.valueymd).format("YYYY");
      this.getItem();
    },
    // 指定日付がクリアされた場合の処理
    fromdateCleared: function() {
      this.valueymd = ""
      this.year = "";
      this.defaultDate = "";
      this.inputClear();
      this.messageClear();
    },
    // 期首月が変更された場合の処理
    basemonthChanges: function(value) {
      this.valueymd = value;
      this.bYMD = moment(this.valueymd);
      this.bMonth = moment(this.valueymd).format("MM");
    },
    // 月45時間以内が変更された場合の処理
    oneMonthTotalChanges: function(event) {
      this.before_valueoneMonthTotal = this.valueoneMonthTotal;
      this.valueoneMonthTotal = event.target.value;
      this.form.oneMonthTotal = this.valueoneMonthTotal;
      if (this.valueoneMonthTotal > 0) {
        if (this.valuesp_oneMonthTotal == "" || this.valuesp_oneMonthTotal == 0) {
          this.limit_valueoneMonthTotal = this.valueoneMonthTotal;
          for (let index = 0; index < this.form.upTime.length; index++) {
            if (this.form.upTime[index] == "" || this.form.upTime[index] > this.valueoneMonthTotal) {
              this.form.upTime[index] = this.valueoneMonthTotal;
            }
          }
        }
      } else if(this.valueoneMonthTotal == "" || this.valueoneMonthTotal == 0) {
        if (this.valuesp_oneMonthTotal == "" || this.valuesp_oneMonthTotal == 0) {
          this.limit_valueoneMonthTotal = 0;
          for (let index = 0; index < this.form.upTime.length; index++) {
            if (this.form.upTime[index] == this.before_valueoneMonthTotal) {
              this.form.upTime[index] = "";
            }
          }
        }
      }
    },
    // 36協定設定１ヶ月累計が変更された場合の処理
    twoMonthTotalChanges: function(event) {
      this.valuetwoMonthTotal = event.target.value;
      this.form.twoMonthTotal = this.valuetwoMonthTotal;
    },
    // 36協定設定２ヶ月累計が変更された場合の処理
    threeMonthTotalChanges: function(event) {
      this.valuethreeMonthTotal = event.target.value;
      this.form.threeMonthTotal = this.valuethreeMonthTotal;
    },
    // 36協定特別条項設定１ヶ月累計が変更された場合の処理
    oneSpMonthTotalChanges: function(event) {
      this.before_valuesp_oneMonthTotal = this.valuesp_oneMonthTotal;
      this.valuesp_oneMonthTotal = event.target.value;
      this.form.sp_oneMonthTotal = this.valuesp_oneMonthTotal;
      if (this.valuesp_oneMonthTotal > 0) {
        this.limit_valueoneMonthTotal = this.valuesp_oneMonthTotal;
        for (let index = 0; index < this.form.upTime.length; index++) {
          if (this.form.upTime[index] == "" || this.form.upTime[index] > this.valuesp_oneMonthTotal) {
            this.form.upTime[index] = this.valuesp_oneMonthTotal;
          }
        }
      } else if(this.valuesp_oneMonthTotal == "" || this.valuesp_oneMonthTotal == 0) {
        if (this.valueoneMonthTotal == "" || this.valueoneMonthTotal == 0) {
          this.limit_valueoneMonthTotal = 0;
          for (let index = 0; index < this.form.upTime.length; index++) {
            if (this.form.upTime[index] == this.before_valuesp_oneMonthTotal) {
              this.form.upTime[index] = "";
            }
          }
        }
      }
    },
    // 自動起動時刻が変更された場合の処理
    autotimeChanges: function(value) {
      this.valuecalcauto = value;
      this.form.calc_auto_time = this.valuecalcauto;
    },
    // 締日が変更された場合の処理
    days_maxChanges: function(index) {
      if (index < 11) {
        if (this.form.closingDate[index+1] == "" || this.form.closingDate[index+1] == null) {
          for ( var i=index+1; i<12; i++ ) {
            if (this.form.closingDate[i] != "" && this.form.closingDate[i] != null) {
              i = 12;
            } else {
              this.form.closingDate[i] = this.form.closingDate[index];
            }
          }
        }
      }
    },
    // 上限残業時間が変更された場合の処理
    upTimeChanges: function(index) {
      if (index < 11) {
        if (this.form.upTime[index+1] == "" || this.form.upTime[index+1] == null) {
          for ( var i=index+1; i<12; i++ ) {
            if (this.form.upTime[i] != "" && this.form.upTime[i] != null) {
              i = 12;
            } else {
              this.form.upTime[i] = this.form.upTime[index];
            }
          }
        }
      }
    },
    // 時間単位が変更された場合の処理
    timeunitChanges: function(index) {
      if (index < 11) {
        if (this.form.timeunit[index+1] == "" || this.form.timeunit[index+1] == null) {
          for ( var i=index+1; i<12; i++ ) {
            if (this.form.timeunit[i] != "" && this.form.timeunit[i] != null) {
              i = 12;
            } else {
              this.form.timeunit[i] = this.form.timeunit[index];
            }
          }
        }
      }
    },
    // 時間の丸めが変更された場合の処理
    timeroundChanges: function(index) {
      if (index < 11) {
        if (this.form.timeround[index+1] == "" || this.form.timeround[index+1] == null) {
          for ( var i=index+1; i<12; i++ ) {
            if (this.form.timeround[i] != "" && this.form.timeround[i] != null) {
              i = 12;
            } else {
              this.form.timeround[i] = this.form.timeround[index];
            }
          }
        }
      }
    },
    // -------------------- サーバー処理 ----------------------------
    // 労働時間基本設定取得処理
    getItem() {
      this.inputClear();
      this.messageClear();
      this.form.year = this.year;
      var arrayParams = { year : this.year };
      this.postRequest("/setting_calc/get", arrayParams)
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("取得");
        });
    },
    storeclick() {
      if (this.checkForm()) {
        var arrayParams = { form : this.form };
        this.postRequest("/setting_calc/store", arrayParams)
          .then(response  => {
            this.putThenHead(response, "登録");
          })
          .catch(reason => {
            this.serverCatch("登録");
          });
      } else {
        this.countswal("エラー", this.messagedatastore, "error", true, false, true)
          .then(result  => {
            if (result) {
            }
        });
      }
    },
    // -------------------- 共通 ----------------------------
    // 取得正常処理
    getThen(response) {
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        this.count = this.details.length;
        this.before_count = this.count;
        if ( this.details.length > 0) {
          this.form.year = this.details[0].fiscal_year;
          this.form.biginningMonth = this.details[0].beginning_month;
          this.form.sp_count = this.details[0].count_sp;
          if (this.details[0].calc_auto_time != null) {
            this.form.calc_auto_time = this.details[0].calc_auto_time.toString();
            this.valuecalcauto = this.form.calc_auto_time;
          }
          if (this.details[0].max_1month_total != null) {
            this.form.oneMonthTotal = this.details[0].max_1month_total.toString();
            this.valueoneMonthTotal = this.details[0].max_1month_total.toString();
            this.limit_valueoneMonthTotal = this.details[0].max_1month_total;
          }
          if (this.details[0].max_2month_total != null) {
            this.form.twoMonthTotal = this.details[0].max_2month_total.toString();
            this.valuetwoMonthTotal = this.details[0].max_2month_total.toString();
          }
          if (this.details[0].max_3month_total != null) {
            this.form.threeMonthTotal = this.details[0].max_3month_total.toString();
            this.valuethreeMonthTotal = this.details[0].max_3month_total.toString();
          }
          if (this.details[0].max_12month_total != null) {
            this.form.yearTotal = this.details[0].max_12month_total.toString();
          }
          if (this.details[0].max_1month_total_sp != null) {
            this.form.sp_oneMonthTotal = this.details[0].max_1month_total_sp.toString();
            this.valuesp_oneMonthTotal = this.details[0].max_1month_total_sp.toString();
            this.limit_valueoneMonthTotal = this.details[0].max_1month_total_sp;
          }
          if (this.details[0].ave_2_6_time_sp != null) {
            this.form.sp_ave_2_6 = this.details[0].ave_2_6_time_sp.toString();
          }
          if (this.details[0].max_12month_total_sp != null) {
            this.form.sp_yearTotal = this.details[0].max_12month_total_sp.toString();
          }
          if (this.details[0].interval != null) {
            this.form.sp_interval = this.details[0].interval.toString();
          }
          this.details.forEach((detail, i) => {
            if (detail.closing != null) {
              this.form.closingDate[i] = detail.closing.toString();
            } else {
              this.form.closingDate[i] = "";
            }
            if (detail.uplimit_time != null) {
              this.form.upTime[i] = detail.uplimit_time.toString();
            } else {
              this.form.upTime[i] = "";
            }
            if (detail.time_unit != null) {
              this.form.timeunit[i] = detail.time_unit.toString();
            } else {
              this.form.timeunit[i] = "";
            }
            if (detail.time_rounding != null) {
              this.form.timeround[i] = detail.time_rounding.toString();
            } else {
              this.form.timeround[i] = "";
            }
            this.bMonth = detail.beginning_month;
          });
        }
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("取得");
        }
      }
    },
    // 更新系正常処理
    putThenHead(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.$toasted.show("労働時間基本設定を" + eventtext + "しました");
        this.getNotSetting();
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    // 異常処理
    serverCatch(eventtext) {
      var messages = [];
      messages.push("労働時間基本設定" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    
    // 設定要否取得処理
    getNotSetting() {
      if (this.settingworkingtimetables == 0) {
        this.getThenWorkingtimetables();
      } else if (this.settingusers == 0) {
        this.getThenUsers();
      } else if (this.settingcalendarsettinginformations == 0) {
        this.getThenCalendarSettingInfos();
      } else if (this.isexistdownload == 0) {
        this.getThenDownload();
      }
    },
    // 項目クリア
    inputClear() {
      this.details = [];
      this.count = 0;
      this.before_count = 0;
      this.bMonth = this.form.biginningMonth;
      this.form.year = "";
      this.form.biginningMonth = "";
      this.form.calc_auto_time = "";
      this.form.oneMonthTotal = "";
      this.form.twoMonthTotal = "";
      this.form.threeMonthTotal = "";
      this.valueoneMonthTotal = "";
      this.valuetwoMonthTotal = "";
      this.valuethreeMonthTotal = "";
      this.form.yearTotal = "";
      this.form.sp_oneMonthTotal = "";
      this.valuesp_oneMonthTotal = "";
      this.form.sp_yearTotal = "";
      this.form.sp_ave_2_6 = "";
      this.form.sp_interval = "";
      this.form.sp_count = "";
      this.valuecalcauto = "";
      for (let index = 0; index < 12; index++) {
        this.form.closingDate[index] = "";
        this.form.upTime[index] = "";
        this.form.timeunit[index] = "";
        this.form.timeround[index] = "";
      }
    },
    // メッセージ項目クリア
    messageClear() {
      this.messagedatayear = [];
      this.messagedatabiginningMonth = [];
      this.messagedataoneMonthTotal = [];
      this.messagedatatwoMonthTotal = [];
      this.messagedatathreeMonthTotal = [];
      this.messagedatayearTotal = [];
      this.messagedatasponeMonthTotal = [];
      this.messagedataspyearTotal = [];
      this.messagedataspave_2_6 = [];
      this.messagedataspcount = [];
      this.messagedataspinterval = [];
      this.messagedatavaluecalcauto = [];
      this.messagedataclosing = [];
      this.messagedataupTime = [];
      this.messagedatatimeunit = [];
      this.messagedatatimeround = [];
      this.messagedatastore = [];
    },
    // tooltips文字列生成
    edttooltips: function(value1, value2) {
      if (value1.length > 0) {
        this.edtString = value1;
      }
      if (value2.length > 0) {
        this.edtString = this.edtString + '\n' + value2;
      }
    }
  }
};
</script>
