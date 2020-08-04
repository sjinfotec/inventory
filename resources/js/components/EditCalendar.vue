<template>
<div>
  <!-- main contentns row -->
  <div class="row justify-content-between">
    <!-- .panel -->
    <div class="col-md pt-3">
      <div class="card shadow-pl">
        <!-- panel header -->
        <div class="card-header bg-transparent pb-0 border-0">
          <h1 class="float-sm-left font-size-rg">年月を指定してカレンダーを表示する</h1>
          <span class="float-sm-right font-size-sm">カレンダー設定で登録したスケジュールを表示できます</span>
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
                  <span class="input-group-text font-size-sm line-height-xs label-width-90" id="basic-addon1">年</span>
                </div>
                <select class="form-control" v-model="year">
                  <option v-for="n in 20" :value="n + baseYear -1">{{ n + baseYear -1 }}年</option>
                </select>
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-90" id="basic-addon1">月</span>
                </div>
                <select class="form-control" v-model="month">
                  <option v-for="n in 12" :value="n">{{ n }}月</option>
                </select>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <div class="btn-group d-flex">
                <button type="button" class="btn btn-primary btn-lg font-size-rg w-100" @click="display()">この条件で表示する</button>
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
  </div>
  <!-- /main contentns row -->
  <!-- main contentns row -->
  <div class="row justify-content-between" v-if="details.length ">
    <!-- .panel -->
    <div class="col-md pt-3 align-self-stretch">
      <div class="card shadow-pl">
        <!-- panel header -->
        <div class="card-header bg-transparent pt-3 border-0">
          <h1 class="float-sm-left font-size-rg">設定済みカレンダー一覧</h1>
          <span class="float-sm-right font-size-sm">登録済みのスケジュールを編集できます</span>
        </div>
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
                      <td class="text-center align-middle w-30">日付</td>
                      <td class="text-center align-middle w-35 mw-rem-10">営業日区分</td>
                      <td class="text-center align-middle w-35 mw-rem-10">休暇区分</td>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item,index) in details" v-bind:key="item.date">
                      <td class="text-center align-middle">{{item.date}}</td>
                      <td class="text-center align-middle">
                        <select class="custom-select" v-model="business[index]">
                          <option value></option>
                          <option v-for="blist in BusinessDayList" :value="blist.code">{{ blist.code_name }}</option>
                        </select>
                      </td>
                      <td class="text-center align-middle">
                        <select class="custom-select" v-model="holiday[index]">
                          <option value></option>
                          <option v-for=" hlist in HoliDayList" :value="hlist.code">{{ hlist.code_name }}</option>
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
          <div class="row justify-content-between px-3">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <div class="btn-group d-flex">
                <button type="button" class="btn btn-success btn-lg font-size-rg w-100" @click="store()">この内容で入力する</button>
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
  </div>
  <!-- /main contentns row -->
</div>
</template>
<script>

export default {
  name: "EditCalendar",
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
      this.valueBusinessDay = value;
    },
    holiDayChanges: function(value) {
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
