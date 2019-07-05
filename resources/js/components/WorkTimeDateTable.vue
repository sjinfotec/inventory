<template>
  <table class="table">
    <thead>
      <tr>
        <th>部署</th>
        <th>社員</th>
        <th>雇用形態</th>
        <th>編集</th>
        <th>出勤</th>
        <th>退勤</th>
        <th>中抜</th>
        <th>戻り</th>
        <th>所定時間</th>
        <th>所定外時間</th>
        <th>所定外深夜時間</th>
        <th>所定外時間</th>
        <th>不就労時間</th>
        <th>所定外累計（月）</th>
        <th>残所定外（月）</th>
        <th>労働形態</th>
        <th>休暇</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="item in workingtimedates">
        <input type="hidden" v-model="item.id"></input>
        <td>{{item.target_date}}</td>
        <td>{{item.shift_start_time}}</td>
        <td>{{item.shift_end_time}}</td>
        <td><button class="btn btn-danger" @click="delShiftTimes(item.id)">削除</button></td>
      </tr>
    </tbody>
  </table>
</template>

<script>

export default {
  name: "selectUser",
  props: {
      depCode: {
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
    console.log("selectedUser Component mounted.");
    this.getUserList();
  },
  methods: {
    getUserList(value){
          console.log("depCode = " +value);
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
    selChanges : function(value) {

        console.log("selecteduser = ["+ value + ']');
        this.$emit('change-event', value);

    }

  }
};
</script>
