<template>
  <!-- panel body -->
  <div class="panel-body">
    <div class="row">
      <div class="form-group col-md-6">
        <label for="business_kubun" class>指定年</label>
        <select class="form-control" v-model="year">
          <option v-for="n in 20" :value="n + baseYear -1">{{ n + baseYear -1 }}年</option>
        </select>
      </div>
      <div class="form-group col-md-6">
        <label for="business_kubun" class>指定月</label>
        <select class="form-control" v-model="month">
          <option v-for="n in 12" :value="n">{{ n }}月</option>
        </select>
      </div>
    </div>
    <div class="row">
      <div class="form-group col-md-6">
        <label for="business_kubun" class>雇用形態</label>
        <select-employmentstatus v-bind:blank-data="true" v-on:change-event="employmentChanges"></select-employmentstatus>
      </div>
      <div class="form-group col-md-6">
        <label for="business_kubun" class>所属部署</label>
        <select-department v-bind:blank-data="true" v-on:change-event="departmentChanges"></select-department>
      </div>
      <div class="form-group col-md-6">
        <label for="business_kubun" class>氏名</label>
        <select-user
          ref="selectuser"
          v-bind:blank-data="true"
          v-bind:get-Do="getDo"
          v-on:change-event="userChanges"
        ></select-user>
      </div>
      <div class="form-group col-md-12">
        <button class="btn btn-primary" @click="getDetail()">この条件で表示する</button>
      </div>
    </div>
    <div class="margin-set-mid" v-if="details.length ">
      {{ year }}年 {{ month }} 月 〆日から表示
      <table class="table">
        <thead>
          <tr>
            <th>日付</th>
            <th>出勤時間</th>
            <th>退勤時間</th>
            <th>中抜け開始</th>
            <th>中抜け終了</th>
            <th>備考</th>
            <th>操作</th>
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
  </div>
</template>
<script>
import toasted from "vue-toasted";

export default {
  name: "EditWorkTimes",
  data() {
    return {
      dates: new Date(),
      valuedepartment: "",
      valueemploymentstatus: "",
      getDo: 0,
      valueuser: "",
      valueBusinessDay: "",
      valueholiDay: "",
      year: "",
      month: "",
      selectMonth: "",
      baseYear: "",
      details: []
    };
  },
  // マウント時
  mounted() {
    // this.getTimeTableList();
    var date = new Date();
    var baseDate = new Date("2018/01/01 8:00:00");
    this.baseYear = baseDate.getFullYear();
    // this.baseYear = baseDate;
  },
  // セレクトボックス変更時
  watch: {
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
        .get("/edit_work_times/get", {
          params: {
            year: this.year,
            month: this.month,
            code: this.valueuser
          }
        })
        .then(response => {
          this.details = response.data;
        })
        .catch(reason => {
          alert("error");
        });
    },
    // 雇用形態が変更された場合の処理
    employmentChanges: function(value) {
      this.valueemploymentstatus = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      if (this.valuedepartment == "") {
        this.$refs.selectuser.getUserList(this.getDo, value);
      } else {
        this.$refs.selectuser.getUserListByEmployment(
          this.getDo,
          this.valuedepartment,
          value
        );
      }
    },
    // 部署選択が変更された場合の処理
    departmentChanges: function(value) {
      this.valuedepartment = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      if (this.valueemploymentstatus == "") {
        this.$refs.selectuser.getUserList(this.getDo, value);
      } else {
        this.$refs.selectuser.getUserListByEmployment(
          this.getDo,
          value,
          this.valueemploymentstatus
        );
      }
    },
    // ユーザー選択が変更された場合の処理
    userChanges: function(value) {
      this.valueuser = value;
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
