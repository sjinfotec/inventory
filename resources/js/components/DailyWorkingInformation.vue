<template>
  <span>
    <div class="form-group col-md-6">
      <label for="target_department" class>部署選択</label>
      <select-department v-bind:selectdepartment="valuedepartment" v-on:change-event="departmentChanges"></select-department>&nbsp;
    </div>
    <div class="form-group col-md-6">
      <label for="target_users" class>ユーザー選択</label>
      <select-user ref="selectuser" v-bind:get-do="getDo" v-on:change-event="userChanges"></select-user>&nbsp;
    </div>
    <div class="form-group col-md-6">
      <label for="target_fromdate" class>計算開始日付入力</label>
    </div>
  </span>
</template>

<script>

import toasted from "vue-toasted";
import Datepicker from "vuejs-datepicker";

export default {
  name: "dailyworkingtime",
  data: function() {
      return {
          valuedepartment: '',
          getDo: 0,
          initialized: false
      }
  },
  methods: {
    // 部署選択が変更された場合の処理
    departmentChanges: function(value){
      console.log("departmentChanges = " + value);
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.$refs.selectuser.getUserList(this.getDo, value);
    },
    // ユーザー選択が変更された場合の処理
    userChanges: function(value){
      console.log("userChanges = " + value);
    }
  }
};
</script>
