<template>
  <select class="form-control" v-model="selectedUser" v-on:change="selChanges(selectedUser)" placeholder="社員を選択してください">
    <option v-for="users in userList" v-bind:value="users.code">
      {{ users.name }}
    </option>
  </select>
</template>
<script>

export default {
  name: "selectUser",
  props: {
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
    console.log("selectedUser Component mounted.");
    this.getUserList(this.getDo, '');
  },
  methods: {
    getUserList(getdovalue, value){
    console.log("getdovalue = " + getdovalue);
      this.$axios
        .get("/get_user_list", {
          params: {
            getdo: getdovalue,
            code: value
          }
        })
        .then(response => {
          this.userList = response.data;
          console.log("ユーザーリスト取得");
        })
        .catch(reason => {
          alert("error");
        });
    },
    // 選択が変更された場合、親コンポーネントに選択値を返却
    selChanges : function(value) {

        console.log("selecteduser = ["+ value + ']');
        this.$emit('change-event', value);

    }

  }
};
</script>
