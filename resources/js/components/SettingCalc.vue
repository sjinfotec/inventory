<template>
  <!-- panel body -->
  <div class="panel-body">
    <fvl-form
      method="post"
      :data="form"
      url="/setting_calc/store"
      @success="addSuccess()"
      @error="error()"
    >
      <div class="row">
        <div class="form-group col-md-6">
        <fvl-search-select
          :selected.sync="year"
          label="年度"
          name="year"
          :options="yearList"
          placeholder="年度を選択してください"
          :allowEmpty="true"
          :search-keys="['name']"
          option-key="name"
          option-value="name"
        />
        </div>
        <div class="form-group col-md-6">
          <fvl-search-select
            :selected.sync="form.biginningMonth"
            label="期首月"
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
      <fvl-input :value.sync="form.threeMonthTotal" label="３ヶ月累計" name="threeMonthTotal" />
      <fvl-input :value.sync="form.sixMonthTotal" label="６ヶ月累計" name="sixMonthTotal" />
      <fvl-input :value.sync="form.yaerTotal" label="１２ヶ月累計" name="yaerTotal" />
      <fvl-input :value.sync="form.interval" label="勤務間インターバル" name="interval" />
      <table class="table">
        <thead>
          <tr>
            <th class="width15">月</th>
            <th class="width15">締日</th>
            <th class="width15">上限残業</th>
            <th>時間単位</th>
            <th>時間の丸め</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(n,index) in 12" :key="index">
            <input type="hidden" ></input>
            <td>{{ n }}月</td>
            <td>
              <!-- <input class="form-control" v-model="closingDate[index]"></input> -->
              <select class="form-control" v-model="form.closingDate[index]">
                <option v-for="n in days_max[index]" :value="n" v-bind:key="n">
                  {{ n }} 日
                </option>
              </select>
            </td>
            <td>
              <input class="form-control" v-model="form.upTime[index]"></input>
            </td>
            <td>
              <select class="form-control" v-model="form.timeunit[index]">
                  <option value></option>
                  <option v-for="tulist in TimeUnitList" :value="tulist.code" v-bind:key="tulist.code">{{ tulist.code_name }}</option>
                </select>
            </td>
            <td>
              <select class="form-control" v-model="form.timeround[index]">
                  <option value></option>
                  <option v-for="trlist in TimeRoundingList" :value="trlist.code" v-bind:key="trlist.code">{{ trlist.code_name }}</option>
                </select>
            </td>
            <td><input type="hidden" v-model="hidden" value=""></input></td>
          </tr>
        </tbody>
      </table>
      <fvl-submit>登録</fvl-submit>
    </fvl-form>
  </div>
</template>
<script>
import toasted from "vue-toasted";
import { FvlForm, FvlInput, FvlSearchSelect,FvlSelect,FvlSubmit } from "formvuelar";

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
      year:"",
      form: {
        year:"",
        threeMonthTotal: "",
        sixMonthTotal: "",
        yaerTotal: "",
        interval: "",
        closingDate: [{}],
        upTime: [{}],
        timeunit: [{}],
        timeround: [{}],
      },
      TimeUnitList: [],
      TimeRoundingList: [],
      days_max: [{}],
      yearList: [{}],
      monthList: [{}],
      baseYear:"",
      details: [],
      hidden:"",
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
        if(detail.closing != null){
          this.form.closingDate[i] = detail.closing.toString();
        }else{
          this.form.closingDate[i] = "";
        }
        if(detail.uplimit_time != null){
          this.form.upTime[i] = detail.uplimit_time.toString();
        }else{
          this.form.upTime[i] = "";
        }
        if(detail.time_unit != null){
          this.form.timeunit[i] = detail.time_unit.toString();
        }else{
          this.form.timeunit[i] = "";
        }
        if(detail.time_rounding != null){
          this.form.timeround[i] = detail.time_rounding.toString();
        }else{
          this.form.timeround[i] = "";
        }
      });
      this.hidden = "GET";
    },
  },
  methods: {
    get_days: function () {
      for (let index = 0; index < 12; index++) {
        var month = index + 1;
        this.days_max[index] = new Date(this.form.year, month, 0).getDate();
      }
    },
    get_month: function () {
      for (let index = 0; index < 12; index++) {
        var code = index +1;
        var target_month = index+1;
        this.monthList[index] = { code: code, name: target_month };
      }
      console.log("年度更新");
    },
    get_years: function () {
      for (let index = 0; index < 30; index++) {
        var code = index +1;
        var target_year = this.baseYear + index;
        this.yearList[index] = { code: code, name: target_year };
      }
      console.log("年度更新");
    },
    getDetail(){
        this.$axios
        .get("/setting_calc/get",{
          params: {
            year: this.year
          }
        })
        .then(response => {
          this.details = response.data;
          // this.form.year = this.details[0].year;
          if(this.details.length > 0){
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
      var options = {
        position: "bottom-center",
        duration: 2000,
        fullWidth: false,
        type: "error"
      };
      this.$toasted.show("登録に失敗しました", options);
    },
    inputClear() {}
  }
};
</script>
<style scoped>
.width15{
  width: 15%;
}
</style>
