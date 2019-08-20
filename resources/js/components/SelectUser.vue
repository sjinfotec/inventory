<template>
  <select class="form-control" v-model="selectedUser" v-on:change="selChanges(selectedUser)" placeholder="社員を選択してください">
    <option v-if="this.blankData" value=""></option>
    <option v-for="users in userList" v-bind:value="users.code">
      {{ users.name }}
    </option>
  </select>
</template>
<script>

export default {
  name: "selectUser",
  props: {
    blankData: {
        type: Boolean,
        default: false
    },
    getDo: {
        type: Number,
        default: 1
    },
    dateValue: {
        type: String,
        default: ''
    }
  },
  data() {
    return {
      selectedUser:'',
      userList:[]
    };
  },
  // マウント時
  mounted() {
    console.log("UserList マウント ");
    this.getUserList(this.getDo, '');
  },
  methods: {
    getUserList(getdovalue, datevalue){
      this.$axios
        .get("/get_user_list", {
          params: {
            getdo: getdovalue,
            targetdate: datevalue
          }
        })
        .then(response => {
          this.userList = response.data;
        })
        .catch(reason => {
          alert("社員選択リスト作成エラー");
        });
    },
    getUserListByDepartment(getdovalue, value, datevalue){
      this.$axios
        .get("/get_user_list", {
          params: {
            getdo: getdovalue,
            code: value,
            targetdate: datevalue
          }
        })
        .then(response => {
          this.userList = response.data;
        })
        .catch(reason => {
          alert("社員選択リスト作成エラー");
        });
    },
    getUserListByEmployment(getdovalue, empvalue, datevalue){
      this.$axios
        .get("/get_user_list", {
          params: {
            getdo: getdovalue,
            employment: empvalue,
            targetdate: datevalue
          }
        })
        .then(response => {
          this.userList = response.data;
        })
        .catch(reason => {
          alert("社員選択リスト作成エラー");
        });
    },
    getUserListByDepartmentEmployment(getdovalue, value, empvalue, datevalue){
      this.$axios
        .get("/get_user_list", {
          params: {
            getdo: getdovalue,
            code: value,
            employment: empvalue,
            targetdate: datevalue
          }
        })
        .then(response => {
          this.userList = response.data;
        })
        .catch(reason => {
          alert("社員選択リスト作成エラー");
        });
    },
    // 選択が変更された場合、親コンポーネントに選択値を返却
    selChanges : function(value) {

        this.$emit('change-event', value);

    }

  }
};
</script>
