<template>
  <!-- panel body -->
  <div class="panel-body">
    <div class="row">
      <div class="form-group col-md-5">
        <label for="business_kubun" class>年</label>
        <select class="form-control" v-model="year">
          <option v-for="n in 20" :value="n + baseYear -1">{{ n + baseYear -1 }}年</option>
        </select>
      </div>
      <div class="form-group col-md-5">
        <label for="business_kubun" class>月</label>
        <select class="form-control" v-model="month">
          <option v-for="n in 12" :value="n">{{ n }}月</option>
        </select>
      </div>
      <div class="form-group col-md-2 margin-set-mid">
        <button class="btn btn-primary" @click="display()">表示</button>
      </div>
    </div>
    <div class="margin-set-mid" v-if="details.length ">
      設定済みカレンダー一覧
      <table class="table">
        <thead>
          <tr>
            <th>日付</th>
            <th>営業日区分</th>
            <th>休暇区分</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item,index) in details" v-bind:key="item.date">
            <td>{{item.date}}</td>
            <td>
              <select class="form-control" v-model="business[index]">
                <option value></option>
                <option v-for="blist in BusinessDayList" :value="blist.code">{{ blist.code_name }}</option>
              </select>
            </td>
            <td>
              <select class="form-control" v-model="holiday[index]">
                <option value></option>
                <option v-for=" hlist in HoliDayList" :value="hlist.code">{{ hlist.code_name }}</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <button class="btn btn-success" @click="store()">編集確定</button>
  </div>
</template>
<script>
import toasted from "vue-toasted";

export default {
  name: "EditWorkTimes",
  data() {
    return {
      dates: new Date(),
      valueBusinessDay: "",
      valueholiDay: "",
      year: "",
      month: "",
      selectMonth: "",
      baseYear: "",
      BusinessDayList: [],
      HoliDayList: [],
      details: [],
      business: [{}],
      holiday: [{}]
    };
  },
  // マウント時
  mounted() {
    // this.getTimeTableList();
    var date = new Date();
    this.baseYear = date.getFullYear();
  },
  // セレクトボックス変更時
  watch: {
    valueBusinessDay: function(val, oldVal) {
      // console.log(val + " " + oldVal);
    },
    valueholiDay: function(val, oldVal) {
      // console.log(val + " " + oldVal);
    },
    details: function(val, oldVal) {
      this.details.forEach((detail, i) => {
        this.business[i] = detail.business_kubun;
        this.holiday[i] = detail.holiday_kubun;
      });
    },
    month: function(val, oldVal) {
      this.selectMonth = this.zeroPadding(val, 2);
    }
  },
  methods: {
    getDetail() {
      this.$axios
        .get("/edit_calendar/get", {
          params: {
            year: this.year,
            month: this.month,
            business_kubun: this.valueBusinessDay,
            holiday_kubun: this.valueholiDay
          }
        })
        .then(response => {
          this.details = response.data;
          this.getBusinessList();
          this.getHoliDayList();
        })
        .catch(reason => {
          alert("error");
        });
    },
    getBusinessList() {
      this.$axios
        .get("/get_business_day_list")
        .then(response => {
          this.BusinessDayList = response.data;
          console.log("営業区分リスト取得");
        })
        .catch(reason => {
          alert("error");
        });
    },
    getHoliDayList() {
      this.$axios
        .get("/get_holi_day_list", {})
        .then(response => {
          this.HoliDayList = response.data;
          console.log("休暇区分リスト取得");
        })
        .catch(reason => {
          alert("error");
        });
    },
    businessDayChanges: function(value) {
      console.log("businessDayChanges = " + value);
      this.valueBusinessDay = value;
    },
    holiDayChanges: function(value) {
      console.log("holiDayChanges = " + value);
      this.valueholiDay = value;
    },
    store() {
      this.$axios
        .post("/edit_calendar/store", {
          details: this.details,
          businessdays: this.business,
          holidays: this.holiday
        })
        .then(response => {
          var res = response.data;
          if (res.result == 0) {
            this.$toasted.show("登録しました");
          } else {
            var options = {
              position: "bottom-center",
              duration: 2000,
              fullWidth: false,
              type: "error"
            };
            this.$toasted.show("登録に失敗しました", options);
          }
        })
        .catch(reason => {});
    },
    display() {
      this.getDetail();
    },
    // ゼロ埋め
    zeroPadding(num, length) {
      return ("0000000000" + num).slice(-length);
    }
  }
};
</script>
<style scoped>
.margin-set-mid {
  margin-top: 30px;
}
</style>
