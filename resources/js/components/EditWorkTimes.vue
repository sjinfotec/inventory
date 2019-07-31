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
    </div>
    <div class="row">
      <div class="form-group col-md-6">
        <button class="btn btn-primary" @click="getDetail()">この条件で表示する</button>
      </div>
      <div class="form-group col-md-6" v-if="valueuser != ''">
        <button class="btn btn-info" v-on:click="show">+ 勤務時間を追加</button>
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
            <td>
              <input type="time" class="form-control" v-model="details[index].time" />
            </td>
            <td>
              <select class="form-control" v-model="details[index].mode">
                <option value></option>
                <option v-for="mode in modeList" :value="mode.code">{{ mode.code_name }}</option>
              </select>
            </td>
            <td v-if="index==0">
              <select class="form-control" v-model="details[index].user_holiday_kbn">
                <option value></option>
                <option v-for="list in userLeaveKbnList" :value="list.code">{{ list.code_name }}</option>
              </select>
            </td>
            <td v-else-if="item.kbn_flag == 1">
              <select class="form-control" v-model="details[index].user_holiday_kbn">
                <option value></option>
                <option v-for="list in userLeaveKbnList" :value="list.code">{{ list.code_name }}</option>
              </select>
            </td>
            <td v-else></td>
            <td>
              <button class="btn btn-danger" @click="del(item.id)">削除</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <button class="btn btn-success" @click="alertStoreConf('info')">編集確定</button>
    <!-- modal -->
    <modal name="add-work_time" v-model="valueuser">
      <div class="card">
        <div class="card-header">勤怠情報追加</div>
        <div class="card-body">
          <div class="row">
            <div class="form-group col-md-6">
              <label for="shift_end" class>日付</label>
              <datepicker
                :language="ja"
                :value="this.default"
                :format="DatePickerFormat"
                v-model="addDate"
              ></datepicker>
            </div>
            <div class="form-group col-md-6">
              <label for="shift_end" class>時間</label>
              <input type="time" class="form-control" v-model="addTime" />
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label for="shift_end" class>モード</label>
              <select class="form-control" v-model="addMode">
                <option value></option>
                <option v-for="mode in modeList" :value="mode.code">{{ mode.code_name }}</option>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label for="shift_end" class>休暇区分</label>
              <select class="form-control" v-model="addKbn">
                <option value></option>
                <option v-for="list in userLeaveKbnList" :value="list.code">{{ list.code_name }}</option>
              </select>
            </div>
          </div>
          <button class="btn btn-success" v-on:click="addWorkTime">登録</button>
          <button class="btn btn-warning" v-on:click="hide">キャンセル</button>
        </div>
      </div>
    </modal>
  </div>
</template>
<script>
import toasted from "vue-toasted";
import Datepicker from "vuejs-datepicker";
import { ja } from "vuejs-datepicker/dist/locale";

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
      addDate: "",
      addTime: "",
      addMode: "",
      addKbn: "",
      ja: ja,
      default: "2019/10/24",
      DatePickerFormat: "yyyy/MM/dd",
      modeList: []
    };
  },
  components: {
    Datepicker
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
    month: function(val, oldVal) {
      this.selectMonth = this.zeroPadding(val, 2);
    }
  },
  methods: {
    alert: function(state, message, title) {
      this.$swal(title, message, state);
    },
    alertStoreConf: function(state) {
      this.$swal({
        title: "確認",
        text: "登録してもよろしいですか？",
        icon: state,
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.store();
        } else {
        }
      });
    },
    alertAddConf: function(state) {
      this.$swal({
        title: "確認",
        text: "登録してもよろしいですか？",
        icon: state,
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.addWorkTime();
        } else {
        }
      });
    },
    show: function() {
      this.$modal.show("add-work_time");
    },
    hide: function() {
      this.$modal.hide("add-work_time");
    },
    addWorkTime: function() {
      // パスワード変更
      this.$axios
        .post("/edit_work_times/add", {
          date: this.addDate,
          user_code: this.valueuser,
          mode: this.addMode,
          holiday_kbn: this.addKbn
        })
        .then(response => {
          var res = response.data;
          if (res.result == 0) {
            // this.$toasted.show("勤怠情報を登録しました");
            this.alert("success", "登録しました", "登録成功");
            this.hide();
          } else {
            this.alert("error", "登録に失敗しました", "エラー");
          }
        })
        .catch(reason => {});
    },
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
    // 削除
    del: function(value) {
      var confirm = window.confirm("選択したレコードを削除しますか？");
      if (confirm) {
        this.$axios
          .post("/edit_work_times/del", {
            id: value
          })
          .then(response => {
            var res = response.data;
            if (res.result == 0) {
              this.$toasted.show("選択したレコードを削除しました");
              this.getDetail();
            } else {
            }
          })
          .catch(reason => {
            alert("削除でエラーが発生しました");
          });
      } else {
      }
    },
    store() {
      this.$axios
        .post("/edit_work_times/store", {
          details: this.details
        })
        .then(response => {
          var res = response.data;
          if (res.result == 0) {
            this.alert("success", "登録しました", "登録成功");
            this.getDetail();
          } else {
            this.alert("error", "登録に失敗しました", "エラー");
          }
        })
        .catch(reason => {});
    },
    display() {
      this.getDetail();
    },
    // レコード新規追加
    addRecord() {},
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
