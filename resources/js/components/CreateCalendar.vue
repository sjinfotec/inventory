<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <div class="card-header bg-transparent pb-0 border-0">
            <h1 class="float-sm-left font-size-rg">日付選択</h1>
            <span class="float-sm-right font-size-sm">複数の日付を選択できます</span>
          </div>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-12 pb-2">
                <v-date-picker v-model="dates" mode="multiple" is-inline is-expanded />
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between px-3">
              <!-- panel header -->
              <div class="card-header col-12 bg-transparent pb-2 border-0 px-0">
                <h1 class="float-sm-left font-size-rg">区分選択</h1>
                <span class="float-sm-right font-size-sm">会社の定めた休日や出勤日を設定できます</span>
              </div>
              <!-- /.panel header -->
              <!-- .row -->
              <div class="row justify-content-between" v-if="errors.length">
                <!-- col -->
                <div class="col-md-12 pb-2">
                  <ul class="error-red color-red">
                    <li v-for="(error,index) in errors" v-bind:key="index">{{ error }}</li>
                  </ul>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-12 pb-2">
                <select-business-day
                  v-bind:blank-data="true"
                  v-on:change-event="businessDayChanges"
                ></select-business-day>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-12 pb-2" v-if="valueBusinessDay==3">
                <select-holi-day v-bind:blank-data="true" v-on:change-event="holiDayChanges"></select-holi-day>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <div class="btn-group d-flex">
                  <button
                    type="button"
                    class="btn btn-success btn-lg font-size-rg w-100"
                    @click="store()"
                  >この条件で登録する</button>
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
                  <button
                    type="button"
                    class="btn btn-primary btn-lg font-size-rg w-100"
                    @click="gotoedit()"
                  >カレンダー編集画面へ</button>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.panel contents -->
        </div>
      </div>
    </div>
    <!-- /.panel -->
  </div>
  <!-- /main contentns row -->
</template>
<script>
import toasted from "vue-toasted";

export default {
  name: "CreateCalendar",
  data() {
    return {
      dates: new Date(),
      errors: [],
      valueBusinessDay: 0,
      valueholiDay: 0,
      oldId: 0
    };
  },
  // マウント時
  mounted() {
    // this.getTimeTableList();
    console.log(this.dates.length);
  },
  // セレクトボックス変更時
  watch: {
    valueBusinessDay: function(val, oldVal) {
      // console.log(val + " " + oldVal);
      if (this.valueholiDay == 0) {
        this.valueholiDay = 0;
      }
    }
  },
  methods: {
    alert: function(state, message, title) {
      this.$swal(title, message, state);
    },
    businessDayChanges: function(value) {
      console.log("businessDayChanges = " + value);
      this.valueBusinessDay = value;
      // this.getDo = 1;
      // this.$refs.selectuser.getUserList(this.getDo, value);
    },
    holiDayChanges: function(value) {
      console.log("holiDayChanges = " + value);
      this.valueholiDay = value;
      // this.getDo = 1;
      // this.$refs.selectuser.getUserList(this.getDo, value);
    },
    store() {
      var varidation = this.checkForm();
      if (varidation) {
        this.$axios
          .post("/create_calendar/store", {
            dates: this.dates,
            businessday_kubun: this.valueBusinessDay,
            holiday_kubun: this.valueholiDay
          })
          .then(response => {
            var res = response.data;
            if (res.result == 0) {
              this.$toasted.show("登録しました");
              this.inputClear();
            } else {
              this.alert("error", "登録に失敗しました", "エラー");
            }
          })
          .catch(reason => {
            this.alert("error", "登録に失敗しました", "エラー");
          });
      } else {
      }
    },
    checkForm: function() {
      var flag = true;
      this.errors = [];

      if (typeof this.dates.length == "undefined" || this.dates.length == 0) {
        flag = false;
        this.errors.push("カレンダー上の日付を選択してください");
      }
      if (this.valueBusinessDay == 0) {
        flag = false;
        this.errors.push("営業日区分を選択してください");
      }
      if (this.valueBusinessDay == 3) {
        if (this.valueholiDay == 0) {
          flag = false;
          this.errors.push("休暇区分を選択してください");
        }
      }
      return flag;
    },
    inputClear() {
      // this.dates.length = 0;
      this.dates = [];
    },
    gotoedit() {
      location.href = "/edit_calendar";
    }
  }
};
</script>
