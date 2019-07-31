<template>
<div>
  <!-- main contentns row -->
  <div class="row justify-content-between">
    <fvl-form
      method="post"
      :data="form"
      url="/setting_calc/store"
      @success="addSuccess()"
      @error="error()"
    >
    <!-- .panel -->
    <div class="col-md pt-3">
      <div class="card shadow-pl">
        <!-- panel header -->
        <div class="card-header bg-transparent pb-0 border-0">
          <h1 class="float-sm-left font-size-rg">年度指定</h1>
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
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">年度指定</span>
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
              <h1 class="float-sm-left font-size-rg">基本設定</h1>
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
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">期首月</span>
                </div>
                <fvl-search-select
                  :selected.sync="form.biginningMonth"
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
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">勤務間インターバル</span>
                </div>
                <input class="form-control" :value.sync="form.interval" label="勤務間インターバル" name="interval" type="number" :max="99.99" :step="0.01">
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- panel header -->
            <div class="card-header col-12 bg-transparent pb-2 border-0">
              <h1 class="float-sm-left font-size-rg">累計設定</h1>
              <span class="float-sm-right font-size-sm">アラートを表示する累計時間を設定できます</span>
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
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">３ヶ月累計</span>
                </div>
                <input class="form-control" :value.sync="form.threeMonthTotal" label="３ヶ月累計" name="threeMonthTotal" type="number" :max="99999.99" :step="0.01">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-12 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">６ヶ月累計</span>
                </div>
                <input class="form-control" :value.sync="form.sixMonthTotal" label="６ヶ月累計" name="sixMonthTotal" type="number" :max="99999.99" :step="0.01">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-12 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">１２ヶ月累計</span>
                </div>
                <input class="form-control" :value.sync="form.yaerTotal" label="１２ヶ月累計" name="yaerTotal" type="number" :max="99999.99" :step="0.01">
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
              <h1 class="float-sm-left font-size-rg">月別設定</h1>
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
                            <select class="custom-select" v-model="form.closingDate[index]">
                              <option v-for="n in days_max[index]" :value="n" v-bind:key="n">{{ n }} 日</option>
                            </select>
                          </div>
                        </td>
                        <td class="text-center align-middle">
                          <div class="input-group">
                            <input title="整数４桁少数２桁まで" type="number" max="9999.99" step="0.01" class="form-control" v-model="form.upTime[index]">
                          </div>
                        </td>
                        <td class="text-center align-middle">
                          <div class="input-group">
                            <select class="custom-select" v-model="form.timeunit[index]">
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
                            <select class="custom-select" v-model="form.timeround[index]">
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
                <button type="submit" class="btn btn-success btn-lg font-size-rg w-100">この内容で入力する</button>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- /.panel contents -->
        </div>
        <!-- /panel body -->
      </div>
    </div>
    <!-- /.panel -->
    </fvl-form>
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
      form: {
        year: "",
        threeMonthTotal: "",
        sixMonthTotal: "",
        yaerTotal: "",
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
      this.form.year = val;
      this.getDetail();
      console.log(val + " " + oldVal);
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
      });
      this.hidden = "GET";
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
      console.log("年度更新");
    },
    get_years: function() {
      for (let index = 0; index < 30; index++) {
        var code = index + 1;
        var target_year = this.baseYear + index;
        this.yearList[index] = { code: code, name: target_year };
      }
      console.log("年度更新");
    },
    getDetail() {
      this.$axios
        .get("/setting_calc/get", {
          params: {
            year: this.year
          }
        })
        .then(response => {
          this.details = response.data;
          // this.form.year = this.details[0].year;
          if (this.details.length > 0) {
            this.form.biginningMonth = this.details[0].beginning_month;
            this.form.threeMonthTotal = this.details[0].max_3month_total.toString();
            this.form.sixMonthTotal = this.details[0].max_6month_total.toString();
            this.form.yaerTotal = this.details[0].max_12month_total.toString();
            this.form.interval = this.details[0].interval.toString();
          }
          console.log("詳細取得");
        })
        .catch(reason => {
          alert("error");
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
          console.log("時間単位リスト取得");
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
          console.log("時間の丸めリスト取得");
        })
        .catch(reason => {
          alert("error");
        });
    },
    error() {
      this.alert("error", "登録に失敗しました", "エラー");
    },
    inputClear() {}
  }
};
</script>
<style scoped>
.width15 {
  width: 15%;
}
</style>
