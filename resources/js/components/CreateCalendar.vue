<template>
  <!-- panel body -->
  <div class="panel-body">
    <v-date-picker v-model="dates" mode="multiple" is-inline is-expanded />
    <select-business-day v-bind:blank-data="true" v-on:change-event="businessDayChanges"></select-business-day>
    <select-holi-day
      v-if="valueBusinessDay==2"
      v-bind:blank-data="true"
      v-on:change-event="holiDayChanges"
    ></select-holi-day>
    <button class="btn btn-success" @click="store()">登録</button>
  </div>
</template>
<script>
import toasted from "vue-toasted";

export default {
  name: "CreateCalendar",
  data() {
    return {
      dates: new Date(),
      valueBusinessDay: "",
      valueholiDay: "",
      oldId: ""
    };
  },
  // マウント時
  mounted() {
    // this.getTimeTableList();
  },
  // セレクトボックス変更時
  watch: {
    valueBusinessDay: function(val, oldVal) {
      // console.log(val + " " + oldVal);
      if (this.valueholiDay) {
        this.valueholiDay = "";
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
    },
    inputClear() {
      // this.dates.length = 0;
      this.dates = [];
    }
  }
};
</script>
