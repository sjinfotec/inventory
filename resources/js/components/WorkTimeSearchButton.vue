<template>
  <button class="btn btn-success"  v-on:click="searchclickBtn()">検索</button>
</template>
<script>

export default {
  name: "WorkTimeSearchButton",
  props: {
    department_code_from: {
      type: String,
      default: ''
    }
    department_code_to: {
      type: String,
      default: ''
    }
    user_code_from: {
      type: String,
      default: ''
    }
    user_code_to: {
      type: String,
      default: ''
    }
    date_from: {
      type: Date
    }
    date_to: {
      type: Date
    }
  },
  data() {
    return {
      paramsList:[]
    };
  },
  // マウント時
  mounted() {
    console.log("selectedUser Component mounted.");
    this.setParams();
  },
  methods: {
    setParams(){
      console.log("setParams start ");
      this.$set(this.paramsList, "department_code_from", this.department_code_from);
      this.$set(this.paramsList, "department_code_to", this.department_code_to);
      this.$set(this.paramsList, "user_code_from", this.user_code_from);
      this.$set(this.paramsList, "user_code_to", this.user_code_to);
      this.$set(this.paramsList, "date_from", this.date_from);
      this.$set(this.paramsList, "date_to", this.date_to);
    },
    searchBtn(){
      console.log("setParams start ");
      this.$axios
        .get("/get_user_list", {
          params: {
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
    searchclickBtn : function(value) {
      console.log("searchclickBtn start ");
      this.$axios
        .get("/get_user_list", {
          params: {
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

    }

  }
};
</script>
