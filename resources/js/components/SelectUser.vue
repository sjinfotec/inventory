<template>
  <select class="form-control" v-model="selectedusercode" v-on:change="selChanges(selectedusercode,rowIndex)">
    <option disabled selected style="display:none;" v-if="this.placeholderData" value="">＜{{ placeholderData }}＞</option>
    <option v-if="this.blankData" value=""></option>
    <option v-for="users in userList" v-bind:value="users.code">
      {{ users.name }}
    </option>
  </select>
</template>
<script>
import moment from "moment";

export default {
  name: "selectUser",
  props: {
    blankData: {
        type: Boolean,
        default: false
    },
    placeholderData: {
        type: String,
        default: '氏名を選択してください'
    },
    addNew: {
        type: Boolean,
        default: false
    },
    getDo: {
        type: Number,
        default: 1
    },
    selectedUser: {
        type: String,
        default: ''
    },
    selectedDepartment: {
        type: String,
        default: ''
    },
    rowIndex: {
        type: Number,
        default: 0
    },
    dateValue: {
        type: String,
        default: ''
    }
  },
  data() {
    return {
      selectedusercode: '',
      dateApllyValue: '',
      userList:[]
    };
  },
  // マウント時
  mounted() {
    this.selectedusercode = this.selectedUser;
    if (this.dateValue == '') {
      this.dateApllyValue = moment(new Date()).format("YYYYMMDD");
    } else {
      this.dateApllyValue = moment(this.dateValue).format("YYYYMMDD");
    }
    if (this.selectedDepartment == '') {
      this.getUserList(this.getDo, this.selectedusercode, this.dateApllyValue);
    } else {
      this.getUserListByDepartment(this.getDo, this.selectedDepartment, this.selectedusercode, this.dateApllyValue);
    }
  },
  methods: {
    getUserList(getdovalue, value, datevalue){
      this.selectedusercode = value;
      this.rowIndex = 0;
      this.userList = [];
      this.$axios
        .get("/get_user_list", {
          params: {
            getdo: getdovalue,
            targetdate: datevalue
          }
        })
        .then(response => {
          this.userList = response.data;
          if (this.addNew) {
            this.object = { name: "新規にユーザー登録する", code: "" };
            this.userList.unshift(this.object);
          }
        })
        .catch(reason => {
          alert("社員選択リスト作成エラー");
        });
    },
    getUserListByDepartment(getdovalue, value, uservalue, datevalue){
      this.selectedusercode = uservalue;
      this.rowIndex = 0;
      this.userList = [];
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
          if (this.addNew) {
            this.object = { name: "新規にユーザー登録する", code: "" };
            this.userList.unshift(this.object);
          }
        })
        .catch(reason => {
          alert("社員選択リスト作成エラー");
        });
    },
    getUserListByEmployment(getdovalue, empvalue, uservalue, datevalue){
      this.selectedusercode = uservalue;
      this.rowIndex = 0;
      this.userList = [];
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
          if (this.addNew) {
            this.object = { name: "新規にユーザー登録する", code: "" };
            this.userList.unshift(this.object);
          }
        })
        .catch(reason => {
          alert("社員選択リスト作成エラー");
        });
    },
    getUserListByDepartmentEmployment(getdovalue, value, empvalue, uservalue, datevalue){
      this.selectedusercode = uservalue;
      this.rowIndex = 0;
      this.userList = [];
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
          if (this.addNew) {
            this.object = { name: "新規にユーザー登録する", code: "" };
            this.userList.unshift(this.object);
          }
        })
        .catch(reason => {
          alert("社員選択リスト作成エラー");
        });
    },
    // 選択が変更された場合、親コンポーネントに選択値を返却
    selChanges : function(value, index) {
      this.$emit('change-event', value, index);

    }

  }
};
</script>
