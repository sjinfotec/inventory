<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <div class="card-header bg-transparent pb-0 border-0">
            <daily-working-information-panel-header
              v-bind:header-text1="'年月を指定してカレンダーを設定する'"
              v-bind:header-text2="'初期設定は期首月または１月から１年分を設定します。'"
            ></daily-working-information-panel-header>
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
                      class="input-group-text font-size-sm line-height-xs label-width-90"
                      id="basic-addon1"
                    >指定年月<span class="color-red">＊</span></span>
                  </div>
                  <input-datepicker
                    v-bind:default-date="defaultDate"
                    v-bind:date-format="'yyyy年MM月'"
                    v-on:change-event="fromdateChanges"
                  ></input-datepicker>
                </div>
                <message-data v-bind:message-datas="messagedatasfromdate" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- .col -->
            </div>
            <!-- .row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:searchclick-event="searchclick"
                  v-bind:btn-mode="'search'"
                  v-bind:is-push="issearchbutton">
                </btn-work-time>
                <message-waiting v-bind:is-message-show="messageshowsearch"></message-waiting>
              </div>
              <!-- /.col -->
            </div>
            <!-- .modal -->
            <modal name="setting-calendar_work" :width="800" :height="600">
              <init-calendar v-on:backclick-event="backclick"></init-calendar>
            </modal>
            <!-- /.modal -->
          </div>
          <!-- /.panel contents -->
        </div>
      </div>
    </div>
    <!-- /.panel -->
    <!-- /main contentns row -->
    <!-- main contentns row -->
    <div class="row justify-content-between" v-if="details.length ">
      <!-- .panel -->
      <div class="col-md pt-3 align-self-stretch">
        <div class="card shadow-pl">
          <!-- panel header -->
          <div class="card-header bg-transparent pb-0 border-0">
            <daily-working-information-panel-header
              v-bind:header-text1="'設定済みカレンダー一覧'"
              v-bind:header-text2="'設定済みのスケジュールを編集できます。'"
            ></daily-working-information-panel-header>
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
                        <td class="text-center align-middle">{{item.date_name}}</td>
                        <td class="text-center align-middle">
                          <select class="custom-select" v-model="business[index]" v-on:change="businessDayChanges(business[index], index)">
                            <option value></option>
                            <option v-for="blist in BusinessDayList" :value="blist.code">{{ blist.code_name }}</option>
                          </select>
                        </td>
                        <td class="text-center align-middle">
                          <select class="custom-select" v-model="holiday[index]" v-on:change="holiDayChanges(holiday[index], index)">
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
                <btn-work-time
                  v-on:storeclick-event="storeclick"
                  v-bind:btn-mode="'store'"
                  v-bind:is-push="isstorebutton">
                </btn-work-time>
                <message-waiting v-bind:is-message-show="messageshowsearch"></message-waiting>
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
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- /.row -->
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
          </div>
        </div>
      </div>
    </div>
    <!-- /main contentns row -->
  </div>
</template>
<script>
import toasted from "vue-toasted";
import moment from "moment";

export default {
  name: "SettingCalendar",
  data() {
    return {
      valueym: "",
      valuefromdate: "",
      defaultDate: new Date(),
      issearchbutton: false,
      isinitbutton: false,
      isstorebutton: false,
      datejaFormat: "",
      ismessageshowinit: false,
      messageshowsearch: false,
      year: "",
      month: "",
      valueBusinessDay: "",
      BusinessDayList: [],
      business: [{}],
      valueholiDay: "",
      HoliDayList: [],
      holiday: [{}],
      details: [],
      messagedatasserver: [],
      messagedatasfromdate: [],
      validate: true
    };
  },
  // セレクトボックス変更時
  watch: {
    valueholiDay: function(val, oldVal) {
      console.log(val + " " + oldVal);
    },
    details: function(val, oldVal) {
      this.details.forEach((detail, i) => {
        this.business[i] = detail.business_kubun;
        this.holiday[i] = detail.holiday_kubun;
      });
    }
  },
  methods: {
    checkForm: function() {
      this.validate = true;
      this.messagedatasserver = [];
      this.messagedatasfromdate = [];
      if (!this.valueym) {
        this.messagedatasfromdate.push("指定年月は必ず入力してください。");
        this.validate = false;
      }

      if (this.validate) {
        return this.validate;
      }

      e.preventDefault();
    },
    // 指定年月が変更された場合の処理
    fromdateChanges: function(value) {
      this.valueym = value;
      this.year = moment(this.valueym).format("YYYY");
      this.month = moment(this.valueym).format("MM");
    },
    searchclick() {
      this.getDetail();
    },
    // 初期設定ボタンの処理
    initclick: function(e) {
      this.$modal.show("setting-calendar_work");
    },
    backclick: function() {
      this.$modal.hide("setting-calendar_work");
      this.searchclick();
    },
    storeclick() {
      this.$axios
        .post("/edit_calendar/store", {
          details: this.details,
          businessdays: this.business,
          holidays: this.holiday
        })
        .then(response => {
          var res = response.data;
          if (res.result == 0) {
            this.$toasted.show("登録しました。");
            this.searchclick();
          } else {
            var options = {
              position: "bottom-center",
              duration: 2000,
              fullWidth: false,
              type: "error"
            };
            this.$toasted.show("登録に失敗しました。", options);
          }
        })
        .catch(reason => {
          this.messageshowsearch = false;
          alert("登録に失敗しました。");
        });
    },
    inputClear() {
      // this.dates.length = 0;
      this.dates = [];
    },
    gotoedit() {
      location.href = "/edit_calendar";
    },
    getDetail() {
      this.messageshowsearch = true;
      this.$axios
        .get("/edit_calendar/get", {
          params: {
            year: this.year,
            month: this.month
          }
        })
        .then(response => {
          this.details = response.data;
          this.messageshowsearch = false;
          this.getBusinessList();
          this.getHoliDayList();
        })
        .catch(reason => {
          this.messageshowsearch = false;
          alert("カレンダー情報の取得に失敗しました。");
        });
    },
    getBusinessList() {
      this.$axios
        .get("/get_business_day_list")
        .then(response => {
          this.BusinessDayList = response.data;
        })
        .catch(reason => {
          alert("営業区分リストの取得に失敗しました。");
        });
    },
    getHoliDayList() {
      this.$axios
        .get("/get_holi_day_list", {})
        .then(response => {
          this.HoliDayList = response.data;
        })
        .catch(reason => {
          alert("休暇区分リストの取得に失敗しました。");
        });
    },
    businessDayChanges: function(value, index) {
      if (value < 2) {
        this.holiday[index] = null;
      }
    },
    holiDayChanges: function(value, index) {
      if (value < 1) {
        this.business[index] = 1;
      } else {
        this.business[index] = 3;
      }
    }
  }
};
</script>
