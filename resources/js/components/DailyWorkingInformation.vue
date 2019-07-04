<template>
  <span>
    <div class="form-group col-md-6">
      <label for="target_department" class>部署選択</label>
      <select-department v-bind:selectdepartment="valuedepartment" v-on:change-event="departmentChanges"></select-department>
    </div>
    <div class="form-group col-md-6">
      <label for="target_users" class>ユーザー選択</label>
      <select-user ref="selectuser" v-bind:get-do="getDo" v-on:change-event="userChanges"></select-user>
    </div>
    <div class="form-group col-md-6">
      <label for="target_fromdate" class>計算開始日付入力</label>
      <input-datepicker v-on:change-event="fromdateChanges"></input-datepicker>
    </div>
    <div class="form-group col-md-6">
      <label for="target_fromdate" class>計算終了日付入力</label>
      <input-datepicker v-on:change-event="todateChanges"></input-datepicker>
    </div>
    <div class="form-group col-md-6">
      <search-workingtimebutton v-on:searchclick-event="searchclick"></search-workingtimebutton>
    </div>
  </span>
</template>

<script>

import toasted from "vue-toasted";

export default {
  name: "dailyworkingtime",
  data: function() {
    return {
      valuedepartment: '',
      getDo: 0,
      valueuser: '',
      valuefromdate: '',
      valuetodate: '',
      results: [],
      initialized: false
    },
    validation {
        title: false,
        description: false,
        date: false,
        location: false
    }
  },
  methods: {
    // 部署選択が変更された場合の処理
    departmentChanges: function(value){
      console.log("departmentChanges = " + value);
      this.valuedepartment = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.$refs.selectuser.getUserList(this.getDo, value);
    },
    // ユーザー選択が変更された場合の処理
    userChanges: function(value){
      console.log("userChanges = " + value);
      this.valueuser = value;
    },
    // 計算開始日付が変更された場合の処理
    fromdateChanges: function(value){
      console.log("fromdateChanges = " + value);
      this.valuefromdate = value;
    },
    // 計算終了日付が変更された場合の処理
    todateChanges: function(value){
      console.log("todateChanges = " + value);
      this.valuetodate = value;
    },
    // 集計開始ボタンがクリックされた場合の処理
    searchclick: function(){
      console.log("searchclick 1 ");
      this.$axios
        .get("/daily/calc", {
          params: {
            departmentcodefrom: this.valuedepartment,
            departmentcodeto: this.valuedepartment,
            usercodefrom: this.valueuser,
            usercodeto: this.valueuser,
            datefrom: this.valuefromdate,
            dateto: this.valuetodate
          }
        })
        .then(response => {
          this.results = response.data;
          console.log("集計時間取得");
        })
        .catch(reason => {
          alert("error");
        });
    }
  }
};
</script>
