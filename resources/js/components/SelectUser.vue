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
        default: 0
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
    this.getUserList(this.getDo, '');
  },
  methods: {
    getUserList(getdovalue, value){
      this.$axios
        .get("/get_user_list", {
          params: {
            getdo: getdovalue,
            code: value
          }
        })
        .then(response => {
          this.userList = response.data;
        })
        .catch(reason => {
          alert("社員選択リスト作成エラー");
        });
    },
    getUserListByEmployment(getdovalue, depvalue, empvalue){
      this.$axios
        .get("/get_user_list", {
          params: {
            getdo: getdovalue,
            code: depvalue,
            employment: empvalue
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

        console.log("selectuser = ["+ value + ']');
        this.$emit('change-event', value);

    }

  }
};
</script>
