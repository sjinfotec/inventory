<template>
  <span>
    <message-data v-bind:messagedatas="messagedatasserver"></message-data>
    <div class="form-group col-md-6">
      <label for="target_fromdate" class>計算開始日付入力</label>
      <input-datepicker v-bind:default-Date="defaultDate" v-on:change-event="fromdateChanges"></input-datepicker>
      <message-data v-bind:messagedatas="messagedatasfromdate"></message-data>
    </div>
    <div class="form-group col-md-6">
      <label for="target_fromdate" class>計算終了日付入力</label>
      <input-datepicker v-bind:default-Date="defaultDate" v-on:change-event="todateChanges"></input-datepicker>
      <message-data v-bind:messagedatas="messagedatastodate"></message-data>
    </div>
    <div class="form-group col-md-6">
      <label for="target_employmentstatus" class>雇用形態選択</label>
      <select-employmentstatus v-bind:blank-data="true" v-on:change-event="employmentChanges"></select-employmentstatus>
    </div>
    <div class="form-group col-md-6">
      <label for="target_department" class>部署選択</label>
      <select-department v-bind:blank-data="true" v-on:change-event="departmentChanges"></select-department>
    </div>
    <div class="form-group col-md-6">
      <label for="target_users" class>ユーザー選択</label>
      <select-user ref="selectuser" v-bind:blank-data="true" v-bind:get-Do="getDo" v-on:change-event="userChanges"></select-user>
    </div>
    <div class="form-group col-md-6">
      <search-workingtimebutton v-on:searchclick-event="searchclick"></search-workingtimebutton>
    </div>
    <div class="form-group col-md-6" v-for="result in results.calc_results">
      <p>{{ result.name }}</p>
    </div>
    <div class="form-group col-md-6">
      <worktime-day></worktime-day>
    </div>
  </span>
</template>

<script>

import toasted from "vue-toasted";
import moment from 'moment';

export default {
  name: "dailyworkingtime",
  data: function() {
    return {
      valuedepartment: '',
      valueemploymentstatus: '',
      getDo: 0,
      valueuser: '',
      valuefromdate: '',
      valuetodate: '',
      defaultDate: new Date(),
      results: [],
      messagedatasserver: [],
      messagedatasfromdate: [],
      messagedatastodate: [],
      validate: false,
      initialized: false
    }
  },
  methods: {
    // バリデーション
    checkForm: function (e) {
      console.log("checkForm in ");
      this.validate = true;
      this.messagedatasserver = [];
      this.messagedatasfromdate = [];
      this.messagedatastodate = [];

      console.log("checkForm start " + (moment(this.valuefromdate).format('YYYYMMDD')));
      if (!this.valuefromdate) {
        this.messagedatasfromdate.push("計算開始日付は必ず入力してください。");
        this.validate = false;
      }
      if (!this.valuetodate) {
        this.messagedatastodate.push("計算終了日付は必ず入力してください。");
        this.validate = false;
      }
      if (moment(this.valuefromdate).isAfter(this.valuetodate)) {
        this.messagedatasfromdate.push("計算開始日付が計算終了日付より未来の日付になっています。");
        this.messagedatastodate.push("計算開始日付が計算終了日付より未来の日付になっています。");
        this.validate = false;
      }

      if (this.validate) {return this.validate;}

      e.preventDefault();
      
    },
    // 雇用形態が変更された場合の処理
    employmentChanges: function(value){
      this.valueemploymentstatus = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      if(this.valuedepartment == ''){
        this.$refs.selectuser.getUserList(this.getDo, value);
      } else {
        this.$refs.selectuser.getUserListByEmployment(this.getDo, this.valuedepartment, value);
      }
    },
    // 部署選択が変更された場合の処理
    departmentChanges: function(value){
      this.valuedepartment = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      if(this.valueemploymentstatus == ''){
        this.$refs.selectuser.getUserList(this.getDo, value);
      } else {
        this.$refs.selectuser.getUserListByEmployment(this.getDo, value, this.valueemploymentstatus);
      }
    },
    // ユーザー選択が変更された場合の処理
    userChanges: function(value){
      this.valueuser = value;
    },
    // 計算開始日付が変更された場合の処理
    fromdateChanges: function(value){
      this.valuefromdate = value;
    },
    // 計算終了日付が変更された場合の処理
    todateChanges: function(value){
      this.valuetodate = value;
    },
    // 集計開始ボタンがクリックされた場合の処理
    searchclick: function(e){
      this.validate = this.checkForm(e);
      if(this.validate){
        this.$axios
          .get("/daily/calc", {
            params: {
              datefrom: moment(this.valuefromdate).format('YYYYMMDD'),
              dateto: moment(this.valuetodate).format('YYYYMMDD'),
              employmentstatus: this.valueemploymentstatus,
              departmentcode: this.valuedepartment,
              usercode: this.valueuser
            }
          })
          .then(response => {
            this.results = response.data;
            this.dispmessage(this.results.massegedata);
            console.log("集計時間取得"+Object.keys(this.results).length);
          })
          .catch(reason => {
            alert("error");
          });
      }
    },
    // メッセージ処理
    dispmessage: function(items){
      items.forEach(function( value ) {
        this.messagedatasserver.push(value);
      });
    }
  }
};
</script>
