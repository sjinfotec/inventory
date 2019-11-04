<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <fvl-form
            method="post"
            :data="form"
            url="/setting_calc/store"
            @success="addSuccess()"
            @error="error()"
          >
            <!-- panel header -->
            <div class="card-header clearfix bg-transparent pb-0 border-0">
              <h1 class="float-sm-left font-size-rg">◆年度指定</h1>
              <span class="float-sm-right font-size-sm">設定する年度を指定できます</span>
            </div>
            <!-- /.panel header -->
            <div class="card-body pt-2">
              <!-- panel contents -->
              <!-- .row -->
              <div class="row justify-content-between">
                <!-- .col -->
                <div class="col-12 pb-2">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >年度指定</span>
                    </div>
                    <fvl-search-select
                      :selected.sync="year"
                      class="p-0"
                      name="year"
                      :options="yearList"
                      placeholder="年度を選択してください"
                      :allowEmpty="true"
                      :search-keys="['name']"
                      option-key="name"
                      option-value="name"
                    />
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <!-- .row -->
              <div class="row justify-content-between">
                <!-- panel header -->
                <div class="card-header col-12 bg-transparent pb-2 border-0">
                  <h1 class="float-sm-left font-size-rg">◆基本設定</h1>
                  <span class="float-sm-right font-size-sm">決算月と次の勤務と見なすまでの時間インターバルを設定できます</span>
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
                      >期首月</span>
                    </div>
                    <fvl-search-select
                      :selected.sync="bMonth"
                      class="p-0"
                      name="biginningMonth"
                      :options="monthList"
                      placeholder="期首月を選択してください"
                      :allowEmpty="true"
                      :search-keys="['name']"
                      option-key="name"
                      option-value="name"
                    />
                  </div>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >勤務間インターバル</span>
                    </div>
                    <fvl-input
                      class="form-control p-0"
                      :value.sync="form.interval"
                      name="interval"
                      type="number"
                      :max="99.99"
                      :step="0.01"
                    />
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <!-- .row -->
              <div class="row justify-content-between">
                <!-- panel header -->
                <div class="card-header col-12 bg-transparent pb-2 border-0">
                  <h1 class="float-sm-left font-size-rg">◆集計自動起動設定</h1>
                  <span class="float-sm-right font-size-sm">設定した時刻に自動でその時刻までの時間集計を行います。</span>
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
                      >自動起動時刻</span>
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
              <!-- .row -->
              <div class="row justify-content-between">
                <!-- panel header -->
                <div class="card-header col-12 bg-transparent pb-2 border-0">
                  <h1 class="float-sm-left font-size-rg">◆36協定特別条項設定</h1>
                  <span class="float-sm-right font-size-sm">特別条項を適用する場合は時間を設定します。</span>
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
                        class="input-group-text font-size-sm line-height-xs label-width-180"
                        id="basic-addon1"
                      >１ヶ月累計（100時間未満）　　　　</span>
                    </div>
                    <div class="form-control p-0">
                      <input
                        type="number"
                        title="100時間未満"
                        max="100.00"
                        min="00.00"
                        step="0.50"
                        class="form-control"
                        v-model="form.oneMonthTotal"
                      />
                    </div>
                  </div>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-12 pb-2">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-180"
                        id="basic-addon1"
                      >１年累計（720時間以内）　　　　　</span>
                    </div>
                    <div class="form-control p-0">
                      <input
                        type="number"
                        title="100時間未満"
                        max="720.00"
                        min="00.50"
                        step="0.01"
                        class="form-control"
                        v-model="form.yearTotal"
                      />
                    </div>
                  </div>
                </div>
                <!-- .col -->
                <div class="col-12 pb-2">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-180"
                        id="basic-addon1"
                      >2～6ヵ月の平均時間 （80時間以内）</span>
                    </div>
                    <div class="form-control p-0">
                      <input
                        type="number"
                        title="80時間以内"
                        max="80.00"
                        min="00.50"
                        step="0.01"
                        class="form-control"
                        v-model="form.ave_2_6"
                      />
                    </div>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <!-- /.panel contents -->
            </div>
            <div class="card-body mb-3 p-0 border-top">
              <!-- panel contents -->
              <!-- .row -->
              <div class="row justify-content-between px-3">
                <!-- panel header -->
                <div class="card-header col-12 bg-transparent pb-2 border-0">
                  <h1 class="float-sm-left font-size-rg">◆月別設定</h1>
                  <span class="float-sm-right font-size-sm">月ごとに締日と時間に関する設定ができます</span>
                </div>
                <!-- /.panel header -->
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
                            <td class="text-center align-middle w-10">月</td>
                            <td class="text-center align-middle w-20 mw-rem-10">締日</td>
                            <td class="text-center align-middle w-20 mw-rem-10">上限残業時間</td>
                            <td class="text-center align-middle w-20 mw-rem-15">時間単位</td>
                            <td class="text-center align-middle w-20 mw-rem-15">時間の丸め</td>
                            <td class="text-center align-middle"></td>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(n,index) in 12" :key="index">
                            <td class="text-center align-middle">{{ n }}月</td>
                            <td class="text-center align-middle">
                              <div class="input-group">
                                <select class="form-control" v-model="form.closingDate[index]">
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
                                  title="36協定特別条項設定の場合は１ヶ月累計時間未満、設定ない場合は45時間以内"
                                  type="number"
                                  max="99.99"
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
              <!-- .row -->
              <div class="row justify-content-between px-3">
                <!-- col -->
                <div class="col-md-12 pb-2">
                  <div class="btn-group d-flex">
                    <button
                      type="submit"
                      class="btn btn-success btn-lg font-size-rg w-100"
                    >この内容で入力する</button>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <!-- /.panel contents -->
            </div>
          </fvl-form>
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
      year: "",
      bMonth: "",
      form: {
        year: "",
        biginningMonth: "",
        calc_auto_time: "",
        oneMonthTotal: "",
        yearTotal: "",
        ave_2_6: "",
        interval: "",
        closingDate: [{}],
        upTime: [{}],
        timeunit: [{}],
        timeround: [{}]
      },
      TimeUnitList: [],
      TimeRoundingList: [],
      days_max: [{}],
      yearList: [{}],
      monthList: [{}],
      baseYear: "",
      details: [],
      hidden: "",
      testList: []
    };
  },
  // マウント時
  mounted() {
    var date = new Date();
    this.baseYear = date.getFullYear();
    this.getTimeUnitList();
    this.getTimeRoundingList();
    this.form.closingDate[0] = "";
    this.form.upTime[0] = "";
    this.form.timeround[0] = "";
    this.form.timeunit[0] = "";
    this.get_days();
    this.get_month();
    this.get_years();
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
      console.log("各配列振り分け 終了");
    }
  },
  methods: {
    get_days: function() {
      for (let index = 0; index < 12; index++) {
        var month = index + 1;
        this.days_max[index] = new Date(this.form.year, month, 0).getDate();
      }
    },
    get_month: function() {
      for (let index = 0; index < 12; index++) {
        var code = index + 1;
        var target_month = index + 1;
        this.monthList[index] = { code: code, name: target_month };
      }
    },
    get_years: function() {
      for (let index = 0; index < 30; index++) {
        var code = index + 1;
        var target_year = this.baseYear + index;
        this.yearList[index] = { code: code, name: target_year };
      }
    },
    getDetail(year) {
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
            if (this.details[0].calc_auto_time != null) {
              this.form.calc_auto_time = this.details[0].calc_auto_time.toString();
            }
            if (this.details[0].max_1month_total_sp != null) {
              this.form.oneMonthTotal = this.details[0].max_1month_total_sp.toString();
            }
            if (this.details[0].ave_2_6_time_sp != null) {
              this.form.ave_2_6 = this.details[0].ave_2_6_time_sp.toString();
            }
            if (this.details[0].max_12month_total_sp != null) {
              this.form.yearTotal = this.details[0].max_12month_total_sp.toString();
            }
            this.form.interval = this.details[0].interval.toString();
          } else {
            this.inputClear();
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
      this.form.calc_auto_time = "";
      this.form.oneMonthTotal = "";
      this.form.ave_2_6 = "";
      this.form.yearTotal = "";
      this.form.interval = "";
      this.form.biginningMonth = "";
      this.form.closingDate = [];
      this.form.upTime = [];
      this.form.timeunit = [];
      this.form.timeround = [];
      this.bMonth = this.form.biginningMonth;
    }
  }
};
</script>
<style scoped>
.width15 {
  width: 15%;
}
</style>
