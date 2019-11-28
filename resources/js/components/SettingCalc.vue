<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <div class="card-header clearfix bg-transparent pb-0 border-0">
            <h1 class="float-sm-left font-size-rg">◆年度指定</h1>
            <span class="float-sm-right font-size-sm">設定する年度と期首月を指定します</span>
          </div>
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
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >年度指定<span class="color-red">[*]</span></span>
                  </div>
                  <input-datepicker
                    v-bind:default-date="defaultDate"
                    v-bind:date-format="'yyyy年'"
                    v-bind:place-holder="'年度を選択してください'"
                    v-on:change-event="fromdateChanges"
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
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >期首月<span class="color-red">[*]</span></span>
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
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >勤務間インターバル<span class="color-red">[*]</span></span>
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
                <span class="float-sm-right font-size-sm">設定した時刻にその時刻までの時間集計を自動で行います。</span>
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
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >自動起動時刻<span class="color-red">[*]</span></span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="time"
                      class="form-control"
                      v-model="form.calc_auto_time"
                    />
                  </div>
                </div>
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
                          <td class="text-center align-middle w-10">月度<span class="color-red">[*]</span></td>
                          <td class="text-center align-middle w-20 mw-rem-10">締日<span class="color-red">[*]</span></td>
                          <td class="text-center align-middle w-20 mw-rem-10"
                            data-toggle="tooltip"
                            data-placement="top"
                            v-bind:title="edtString"
                            @mouseover="edttooltips('36協定特別条項適用しない場合は36協定設定の１ヶ月累計時間以内、適用の場合は36協定特別条項設定の１ヶ月累計時間以内','')"
                          >上限残業時間<span class="color-red">[*]</span></td>
                          <td class="text-center align-middle w-20 mw-rem-15">時間単位<span class="color-red">[*]</span></td>
                          <td class="text-center align-middle w-20 mw-rem-15">時間の丸め<span class="color-red">[*]</span></td>
                          <td class="text-center align-middle"></td>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(n,index) in 12" :key="index">
                          <td class="text-center align-middle">{{ n }}月</td>
                          <td class="text-center align-middle">
                            <div class="input-group">
                              <select class="form-control" v-model="form.closingDate[index]">
                                <option value></option>
                                <option
                                  v-for="n in days_max[index]"
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
                              />
                            </div>
                          </td>
                          <td class="text-center align-middle">
                            <div class="input-group">
                              <select class="form-control" v-model="form.timeunit[index]">
                                <option value></option>
                                <option
                                  v-for="tulist in TimeUnitList"
                                  :value="tulist.code"
                                  v-bind:key="tulist.code"
                                >{{ tulist.code_name }}</option>
                              </select>
                            </div>
                          </td>
                          <td class="text-center align-middle">
                            <div class="btn-group d-flex">
                              <select class="form-control" v-model="form.timeround[index]">
                                <option value></option>
                                <option
                                  v-for="trlist in TimeRoundingList"
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
          </div>
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:storeclick-event="storeclick"
                  v-bind:btn-mode="'store'"
                  v-bind:is-push="storeisPush">
                </btn-work-time>
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
import {
  FvlForm,
  FvlInput,
  FvlSearchSelect,
  FvlSelect,
  FvlSubmit
} from "formvuelar";

export default {
  name: "SettingCalc",
  components: {
    FvlForm,
    FvlInput,
    FvlSearchSelect,
    FvlSelect,
    FvlSubmit
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
      TimeUnitList: [],
      TimeRoundingList: [],
      days_max: [{}],
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
      messagedataclosing: [],
      messagedataupTime: [],
      messagedatatimeunit: [],
      messagedatatimeround: [],
      storeisPush: true
    };
  },
  // マウント時
  mounted() {
    var date = new Date();
    this.inputClear();
    this.baseYear = date.getFullYear();
    this.getTimeUnitList();
    this.getTimeRoundingList();
    this.get_days();
  },
  watch: {
    year: function(val, oldVal) {
      this.form.year = this.year;
      this.getDetail(this.form.year);
    },
    bMonth: function(val, oldVal) {
      this.form.biginningMonth = this.bMonth;
      this.hidden = "GET";
    },
    details: function(val, oldVal) {
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
      this.hidden = "GET";
    }
  },
  methods: {
    // バリデーション
    checkForm: function() {
      var flag = true;
      this.messagedatayear = [];
      this.messagedatabiginningMonth = [];
      this.messagedataoneMonthTotal = [];
      this.messagedatayearTotal = [];
      this.messagedatasponeMonthTotal = [];
      this.messagedatasptwoMonthTotal = [];
      this.messagedataspthreeMonthTotal = [];
      this.messagedataspyearTotal = [];
      this.messagedataspave_2_6 = [];
      this.messagedataspcount = [];
      this.messagedataspinterval = [];
      this.messagedataclosing = [];
      this.messagedataupTime = [];
      this.messagedatatimeunit = [];
      this.messagedatatimeround = [];

      if (this.form.year == "") {
        this.messagedatayear.push("年度を入力してください");
        flag = false;
      }
      if (this.form.biginningMonth == "") {
        this.messagedatabiginningMonth.push("期首月を入力してください");
        flag = false;
      } else if (parseInt(this.form.biginningMonth) < 1 || parseInt(this.form.biginningMonth) > 12) {
          this.messagedatabiginningMonth.push("1~12の範囲で入力してください");
          flag = false;
      } else {
        this.form.biginningMonth = parseInt(this.form.biginningMonth);
      }
      if (this.form.oneMonthTotal != "") {
        if (parseFloat(this.form.oneMonthTotal) < 1 || parseFloat(this.form.oneMonthTotal) > 45) {
          this.messagedataoneMonthTotal.push("1~45の範囲で入力してください");
          flag = false;
        }
      }
      if (this.form.twoMonthTotal != "") {
        if (parseFloat(this.form.twoMonthTotal) < 1 || parseFloat(this.form.twoMonthTotal) > 81) {
          this.messagedatatwoMonthTotal.push("1~81の範囲で入力してください");
          flag = false;
        }
      }
      if (this.form.threeMonthTotal != "") {
        if (parseFloat(this.form.threeMonthTotal) < 1 || parseFloat(this.form.threeMonthTotal) > 120) {
          this.messagedatathreeMonthTotal.push("1~120の範囲で入力してください");
          flag = false;
        }
      }
      if (this.form.yearTotal != "") {
        if (parseFloat(this.form.yearTotal) < 1 || parseFloat(this.form.yearTotal) > 360) {
          this.messagedatayearTotal.push("1~360の範囲で入力してください");
          flag = false;
        }
      }
      if (this.form.sp_oneMonthTotal != "") {
        if (parseFloat(this.form.sp_oneMonthTotal) < 1 || parseFloat(this.form.sp_oneMonthTotal) > 100) {
          this.messagedatasponeMonthTotal.push("1~100の範囲で入力してください");
          flag = false;
        }
      }
      if (this.form.sp_yearTotal != "") {
        if (parseFloat(this.form.sp_yearTotal) < 1 || parseFloat(this.form.sp_yearTotal) > 720) {
          this.messagedataspyearTotal.push("1~720の範囲で入力してください");
          flag = false;
        }
      }
      if (this.form.sp_ave_2_6 != "") {
        if (parseFloat(this.form.sp_ave_2_6) < 1 || parseFloat(this.form.sp_ave_2_6) > 80) {
          this.messagedataspave_2_6.push("1~80の範囲で入力してください");
          flag = false;
        }
      }
      if (this.form.sp_count == "") {
        this.messagedataspcount.push("特別条項の回数を入力してください");
        flag = false;
      } else if (parseInt(this.form.sp_count) < 1 || parseInt(this.form.sp_count) > 6) {
          this.messagedataspcount.push("1~6の範囲で入力してください");
          flag = false;
      } else {
        this.form.sp_count = parseInt(this.form.sp_count);
      }
      if (this.form.sp_interval != "") {
        if (parseFloat(this.form.sp_interval) < 1 || parseFloat(this.form.sp_interval) > 8) {
          this.messagedataspinterval.push("1~8の範囲で入力してください");
          flag = false;
        }
      }
      for (let index = 0; index < this.form.closingDate.length; index++) {
        if (this.form.closingDate[index] == "") {
          this.messagedataclosing.push(index+1 + "月の締日を入力してください");
          flag = false;
        }
      }
      for (let index = 0; index < this.form.upTime.length; index++) {
        if (this.form.upTime[index] == "") {
          this.messagedataupTime.push(index+1 + "月の上限残業時間を入力してください");
          flag = false;
        } else {
          if (this.form.sp_oneMonthTotal != "") {
            if (parseFloat(this.form.upTime[index]) < 1 || parseFloat(this.form.upTime[index]) > parseFloat(this.form.sp_oneMonthTotal)) {
              this.messagedataupTime.push(index+1 + "月の上限残業時間は1~" + this.form.sp_oneMonthTotal + "の範囲で入力してください");
              flag = false;
            }
          } else {
            if (this.form.oneMonthTotal != "") {
              if (parseFloat(this.form.upTime[index]) < 1 || parseFloat(this.form.upTime[index])> parseFloat(this.form.oneMonthTotal)) {
                this.messagedataupTime.push(index+1 + "月の上限残業時間は1~" + this.form.oneMonthTotal + "の範囲で入力してください");
                flag = false;
              }
            }
          }
        }
      }
      for (let index = 0; index < this.form.timeunit.length; index++) {
        if (this.form.timeunit[index] == "") {
          this.messagedatatimeunit.push(index+1 + "月の時間単位を入力してください");
          flag = false;
        }
      }
      for (let index = 0; index < this.form.timeround.length; index++) {
        if (this.form.timeround[index] == "") {
          this.messagedatatimeround.push(index+1 + "月の時間の丸めを入力してください");
          flag = false;
        }
      }
      return flag;
    },
    // 指定年月が変更された場合の処理
    fromdateChanges: function(value) {
      this.valueymd = value;
      this.year = moment(this.valueymd).format("YYYY");
    },
    // 期首月が変更された場合の処理
    basemonthChanges: function(value) {
      this.valueymd = value;
      this.bYMD = moment(this.valueymd);
      this.bMonth = moment(this.valueymd).format("MM");
    },
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
    twoMonthTotalChanges: function(event) {
      this.valuetwoMonthTotal = event.target.value;
      this.form.twoMonthTotal = this.valuetwoMonthTotal;
    },
    threeMonthTotalChanges: function(event) {
      this.valuethreeMonthTotal = event.target.value;
      this.form.threeMonthTotal = this.valuethreeMonthTotal;
    },
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
    // 登録の処理
    storeclick: function() {
      if (this.checkForm()) {
        this.$axios
          .post("/setting_calc/store", {
            settings: this.form
          })
          .then(response => {
            var res = response.data;
            this.alert("success", "登録しました", "登録完了");
            this.getDetail(this.form.year);
          })
          .catch(reason => {
            this.alert("error", "登録に失敗しました", "エラー");
          });
      }
    },
    get_days: function() {
      for (let index = 0; index < 12; index++) {
        var month = index + 1;
        this.days_max[index] = new Date(this.form.year, month, 0).getDate();
      }
    },
    getDetail(year) {
      this.inputClear();
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
      this.messagedataclosing = [];
      this.messagedataupTime = [];
      this.messagedatatimeunit = [];
      this.messagedatatimeround = [];
      this.$axios
        .get("/setting_calc/get", {
          params: {
            year: year
          }
        })
        .then(response => {
          if (response.data.length > 0) {
            this.details = response.data;
            this.form.year = this.details[0].fiscal_year;
            this.form.biginningMonth = this.details[0].beginning_month;
            this.form.sp_count = this.details[0].count_sp;
            if (this.details[0].calc_auto_time != null) {
              this.form.calc_auto_time = this.details[0].calc_auto_time.toString();
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
            this.form.sp_interval = this.details[0].interval.toString();
          }
        })
        .catch(reason => {
          alert("詳細取得エラー");
        });
    },
    alert: function(state, message, title) {
      this.$swal(title, message, state);
    },
    addSuccess() {
      // ここで会社情報呼び出す
      this.$toasted.show("登録しました");
    },
    getTimeUnitList() {
      this.$axios
        .get("/get_time_unit_list")
        .then(response => {
          this.TimeUnitList = response.data;
        })
        .catch(reason => {
          alert("error");
        });
    },
    getTimeRoundingList() {
      this.$axios
        .get("/get_time_rounding_list", {})
        .then(response => {
          this.TimeRoundingList = response.data;
        })
        .catch(reason => {
          alert("error");
        });
    },
    error() {
      this.alert("error", "登録に失敗しました", "エラー");
    },
    inputClear() {
      // alert("clear");
      this.bMonth = this.form.biginningMonth;
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
      for (let index = 0; index < 12; index++) {
        this.form.closingDate[index] = "";
        this.form.upTime[index] = "";
        this.form.timeunit[index] = "";
        this.form.timeround[index] = "";
      }
    },
    // tooltips
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
<style scoped>
.width15 {
  width: 15%;
}
</style>
