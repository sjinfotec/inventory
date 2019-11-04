<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between print-none">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <div class="card-header bg-transparent pb-0 border-0">
            <h1 class="float-sm-left font-size-rg">年月を指定して集計を表示する</h1>
            <span class="float-sm-right font-size-sm">雇用形態や所属部署でフィルタリングして表示できます</span>
          </div>
          <!-- /.panel header -->
          <!-- panel body -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-90"
                      id="basic-addon1"
                    >指定年<span class="color-red">＊</span></span>
                  </div>
                  <select class="form-control" v-model="year">
                    <option
                      v-for="n in 20"
                      :value="n + baseYear -1"
                      v-bind:key="n"
                    >{{ n + baseYear -1 }}年</option>
                  </select>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-90"
                      id="basic-addon1"
                    >指定月<span class="color-red">＊</span></span>
                  </div>
                  <select class="form-control" v-model="month">
                    <option v-for="n in 12" :value="n" v-bind:key="n">{{ n }}月</option>
                  </select>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-90"
                      for="inputGroupSelect01"
                    >雇用形態</label>
                  </div>
                  <select-employmentstatus
                    v-bind:blank-data="true"
                    v-on:change-event="employmentChanges"
                  ></select-employmentstatus>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-90"
                      for="inputGroupSelect01"
                    >所属部署</label>
                  </div>
                  <select-department v-bind:blank-data="true" v-on:change-event="departmentChanges"></select-department>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-90"
                      for="inputGroupSelect01"
                    >氏名<span class="color-red">＊</span></label>
                  </div>
                  <select-user
                    ref="selectuser"
                    v-bind:blank-data="true"
                    v-bind:get-Do="getDo"
                    v-on:change-event="userChanges"
                  ></select-user>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <div class="btn-group d-flex">
                  <button class="btn btn-primary btn-lg font-size-rg w-100" @click="getDetail()">
                    <img class="icon-size-sm mr-2 pb-1" src="/images/round-search-w.svg" alt />この条件で表示する
                  </button>
                </div>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-md-12 pb-2" v-if="valueuser != ''">
                <div class="btn-group d-flex">
                  <button class="btn btn-success btn-lg font-size-rg w-100" v-on:click="show">
                    <img class="icon-size-sm mr-2 pb-1" src="/images/round-search-w.svg" alt />勤務時間を追加
                  </button>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.panel contents -->
          </div>
        </div>
      </div>
      <!-- /.panel -->
    </div>
    <!-- /main contentns row -->
    <!-- main contentns row -->
    <div class="row justify-content-between" v-if="details.length ">
      <!-- .panel -->
      <div class="col-md pt-3 align-self-stretch">
        <div class="card shadow-pl">
          <!-- panel header -->
          <div class="card-header bg-transparent pt-3 border-0 print-none">
            <h1 class="float-md-left font-size-rg">{{ year }}年 {{ month }} 月 〆分を表示</h1>
            <span class="float-md-right font-size-sm">勤務時間や休暇区分などを変更できます</span>
          </div>
          <!-- /.panel header -->
          <!-- panel body -->
          <div class="card-body mb-3 py-0 pt-4 border-top print-space">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row">
              <!-- col -->
              <div class="col-sm-6 col-md-6 col-lg-6 pb-2 align-self-stretch">
                <h1 class="font-size-sm m-0 mb-1">氏名</h1>
                <p class="font-size-rg font-weight-bold m-0">{{ details[0].user_name }}</p>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-sm-6 col-md-6 col-lg-6 pb-2 align-self-stretch">
                <h1 class="font-size-sm m-0 mb-1 text-sm-right">所属部署</h1>
                <p class="font-size-rg m-0 text-sm-right">{{details[0].d_name}}</p>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.panel contents -->
          </div>
          <!-- /panel body -->
          <!-- panel body -->
          <div class="card-body mb-3 p-0 border-top">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <div class="col-12 p-0">
                    <table class="table table-striped border-bottom font-size-sm text-nowrap">
                      <thead>
                        <tr>
                          <td class="text-center align-middle w-15">日付</td>
                          <td class="text-center align-middle w-15">時間</td>
                          <td class="text-center align-middle w-15">区分</td>
                          <td class="text-center align-middle w-35 mw-rem-20">備考</td>
                          <td class="text-center align-middle mw-rem-15">操作</td>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(item,index) in details" v-bind:key="item.id">
                          <td class="text-center align-middle">{{item.date}}</td>
                          <td class="text-center align-middle">
                            <div class="input-group">
                              <input type="time" class="form-control" v-model="details[index].time" />
                            </div>
                          </td>
                          <td class="text-center align-middle">
                            <div class="input-group">
                              <select class="form-control" v-model="details[index].mode">
                                <option value></option>
                                <option
                                  v-for="mode in modeList"
                                  :value="mode.code"
                                >{{ mode.code_name }}</option>
                              </select>
                            </div>
                          </td>
                          <td class="text-center align-middle" v-if="index==0">
                            <div class="input-group">
                              <div class="input-group-prepend">
                                <label
                                  class="input-group-text font-size-sm line-height-xs label-width-90"
                                  for="inputGroupSelect01"
                                >休暇区分</label>
                              </div>
                              <select
                                class="form-control"
                                v-model="details[index].user_holiday_kbn"
                              >
                                <option value></option>
                                <option
                                  v-for="list in userLeaveKbnList"
                                  :value="list.code"
                                >{{ list.code_name }}</option>
                              </select>
                            </div>
                          </td>
                          <td class="text-center align-middle" v-else-if="item.kbn_flag == 1">
                            <div class="input-group">
                              <div class="input-group-prepend">
                                <label
                                  class="input-group-text font-size-sm line-height-xs label-width-90"
                                  for="inputGroupSelect01"
                                >休暇区分</label>
                              </div>
                              <select
                                class="form-control"
                                v-model="details[index].user_holiday_kbn"
                              >
                                <option value></option>
                                <option
                                  v-for="list in userLeaveKbnList"
                                  :value="list.code"
                                >{{ list.code_name }}</option>
                              </select>
                            </div>
                          </td>
                          <td v-else></td>
                          <td class="text-center align-middle">
                            <div class="btn-group d-flex">
                              <button
                                type="button"
                                class="btn btn-danger btn-lg font-size-rg w-100"
                                @click="alertDelConf('warning',item.id)"
                              >削除</button>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between px-3">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <div class="btn-group d-flex">
                  <button
                    type="button"
                    class="btn btn-success btn-lg font-size-rg w-100"
                    @click="alertStoreConf('info')"
                  >この内容で編集を確定する</button>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.panel contents -->
          </div>
          <!-- /panel body -->
        </div>
      </div>
      <!-- /.panel -->
    </div>
    <!-- /main contentns row -->
    <!-- modal -->
    <modal name="add-work_time" :width="800" :height="600" v-model="valueuser">
      <div class="card">
        <div class="card-header">勤怠情報追加</div>
        <div class="card-body">
          <!-- .row -->
          <div class="row justify-content-between" v-if="errors.length">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <ul class="error-red color-red">
                <li v-for="(error,index) in errors" v-bind:key="index">{{ error }}</li>
              </ul>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span
                    class="input-group-text font-size-sm line-height-xs label-width-120"
                    for="shift_end"
                  >日付</span>
                </div>
                <datepicker
                  :language="ja"
                  :value="this.default"
                  :format="DatePickerFormat"
                  v-model="addDate"
                ></datepicker>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-md-12 pb-2" v-if="addKbn == ''">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span
                    class="input-group-text font-size-sm line-height-xs label-width-120"
                    for="shift_end"
                  >時間</span>
                </div>
                <input type="time" class="form-control" v-model="addTime" />
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-md-12 pb-2" v-if="addKbn == ''">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span
                    class="input-group-text font-size-sm line-height-xs label-width-120"
                    for="shift_end"
                  >勤務区分</span>
                </div>
                <select class="form-control" v-model="addMode">
                  <option value></option>
                  <option
                    v-for="mode in modeList"
                    :value="mode.code"
                    v-bind:key="mode.code"
                  >{{ mode.code_name }}</option>
                </select>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-md-12 pb-2" v-if="addMode == ''">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span
                    class="input-group-text font-size-sm line-height-xs label-width-120"
                    for="holiday_kbn"
                  >休暇区分</span>
                </div>
                <select class="form-control" v-model="addKbn">
                  <option value></option>
                  <option
                    v-for="list in userLeaveKbnList"
                    :value="list.code"
                    v-bind:key="list.code"
                  >{{ list.code_name }}</option>
                </select>
              </div>
            </div>

            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- col -->
            <div class="col-md-12 py-4"></div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <div class="btn-group d-flex">
                <button
                  type="button"
                  class="btn btn-success btn-lg font-size-rg w-100"
                  v-on:click="alertAddConf('info')"
                >この条件で登録</button>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-md-12 pb-2">
              <div class="btn-group d-flex">
                <button
                  type="button"
                  class="btn btn-warning btn-lg font-size-rg w-100"
                  v-on:click="hide"
                >キャンセル</button>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
      </div>
    </modal>
    <!-- /modal -->
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
      validate: false,
      errors: [],
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
    },
    addMode: function(val, oldVal) {
      this.addKbn = "";
    },
    addKbn: function(val, oldVal) {
      this.addMode = "";
      this.addTime = "";
    }
  },
  methods: {
    // バリデーション
    checkForm: function() {
      var flag = false;
      this.errors = [];
      if (this.addDate && this.addKbn) {
        flag = true;
        return flag;
      } else {
        if (this.addDate && this.addTime && this.addMode) {
          flag = true;
          return flag;
        } else {
          if (!this.addDate) {
            flag = false;
            this.errors.push("登録する日付を選択してください");
          }
          if (this.addKbn == "") {
            if (!this.addTime) {
              flag = false;
              this.errors.push("時間を入力してください");
            }
            if (!this.addMode) {
              flag = false;
              this.errors.push("モードを選択してください");
            }
          }
          return flag;
        }
      }
    },
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
      this.validate = this.checkForm();
      if (this.validate) {
        this.$swal({
          title: "確認",
          text: "登録してもよろしいですか？",
          icon: state,
          buttons: true,
          dangerMode: true
        }).then(willDelete => {
          if (willDelete) {
            this.inputClear();
            this.addWorkTime();
          } else {
          }
        });
      } else {
      }
    },
    alertDelConf: function(state, value) {
      this.$swal({
        title: "確認",
        text: "削除してもよろしいですか？",
        icon: state,
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.del(value);
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
      this.$axios
        .post("/edit_work_times/add", {
          date: this.addDate,
          user_code: this.valueuser,
          time: this.addTime,
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
          alert("error", "削除でエラーが発生しました", "エラー");
        });
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
    // ゼロ埋め
    zeroPadding(num, length) {
      return ("0000000000" + num).slice(-length);
    },
    inputClear() {
      this.errors = [];
    }
  }
};
</script>
<style scoped>
.margin-set-mid {
  margin-top: 30px;
}
</style>
