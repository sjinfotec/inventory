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
      <div>
        <span>{{ details[0].user_name }}</span>
        <span>{{details[0].d_name}}</span>
      </div>
      <table class="table">
        <thead>
          <tr>
            <th>日付</th>
            <th>時間</th>
            <th>モード</th>
            <th>備考</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item,index) in details" v-bind:key="item.id">
            <td>{{item.date}}</td>
            <td>{{ item.time}}</td>
            <td>
              <select class="form-control" v-model="details[index].mode">
                <option value></option>
                <option v-for="mode in modeList" :value="mode.code">{{ mode.code_name }}</option>
              </select>
            </td>
            <td v-if="index==0">
              <select class="form-control" v-model="kbn[index]">
                <option value></option>
                <option v-for="list in userLeaveKbnList" :value="list.code">{{ list.code_name }}</option>
              </select>
            </td>
            <td v-else-if="item.kbn_flag == 1">
              <select class="form-control" v-model="kbn[index]">
                <option value></option>
                <option v-for="list in userLeaveKbnList" :value="list.code">{{ list.code_name }}</option>
              </select>
            </td>
            <td v-else></td>
            <td>
              <button class="btn btn-danger" @click="del">削除</button>
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
      userLeaveKbnList: [],
      details: [],
      modeList: [],
      kbn: [{}],
      mode: [{}]
    };
  },
  // マウント時
  mounted() {
    // this.getTimeTableList();
    var date = new Date();
    var baseDate = new Date("2018/01/01 8:00:00");
    this.baseYear = baseDate.getFullYear();
    this.getUserLeaveKbnList();
    this.getModeList();
    // this.baseYear = baseDate;
  },
  // セレクトボックス変更時
  watch: {
    details: function(val, oldVal) {
      this.details.forEach((detail, i) => {
        this.mode[i] = detail.mode;
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
    getUserLeaveKbnList() {
      this.$axios
        .get("/get_user_leave_kbn")
        .then(response => {
          this.userLeaveKbnList = response.data;
          console.log("個人休暇区分取得");
        })
        .catch(reason => {
          alert("error");
        });
    },
    getModeList() {
      this.$axios
        .get("/get_mode_list")
        .then(response => {
          this.modeList = response.data;
          console.log("モード取得");
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
        if (this.valueemploymentstatus == "") {
          this.$refs.selectuser.getUserList(this.getDo);
        } else {
          this.$refs.selectuser.getUserListByEmployment(
            this.getDo,
            this.valueemploymentstatus
          );
        }
      } else {
        if (this.valueemploymentstatus == "") {
          this.$refs.selectuser.getUserListByDepartment(
            this.getDo,
            this.valuedepartment
          );
        } else {
          this.$refs.selectuser.getUserListByDepartmentEmployment(
            this.getDo,
            this.valuedepartment,
            this.valueemploymentstatus
          );
        }
      }
    },
    // 部署選択が変更された場合の処理
    departmentChanges: function(value) {
      this.valuedepartment = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      if (this.valueemploymentstatus == "") {
        if (this.valuedepartment == "") {
          this.$refs.selectuser.getUserList(this.getDo);
        } else {
          this.$refs.selectuser.getUserListByDepartment(
            this.getDo,
            this.valuedepartment
          );
        }
      } else {
        if (this.valuedepartment == "") {
          this.$refs.selectuser.getUserListByEmployment(
            this.getDo,
            this.valueemploymentstatus
          );
        } else {
          this.$refs.selectuser.getUserListByDepartmentEmployment(
            this.getDo,
            this.valuedepartment,
            this.valueemploymentstatus
          );
        }
      }
    },
    // ユーザー選択が変更された場合の処理
    userChanges: function(value) {
      this.valueuser = value;
    },
    del: function() {
      // this.valueuser = value;
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
