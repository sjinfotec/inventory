<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:headertext1="'日付を指定して集計を表示する'"
            v-bind:headertext2="'雇用形態や所属部署でフィルタリングして表示できます'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <message-data v-bind:messagedatas="messagedatasserver"></message-data>
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-90"
                      for="target_fromdate"
                    >指定日付</span>
                  </div>
                  <input-datepicker
                    v-bind:default-Date="defaultDate"
                    v-on:change-event="fromdateChanges"
                  ></input-datepicker>
                  <message-data v-bind:messagedatas="messagedatasfromdate"></message-data>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-90"
                      for="target_employmentstatus"
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
                      for="target_department"
                    >所属部署</label>
                  </div>
                  <select-department
                    ref="selectdepartment"
                    v-bind:blank-data="true" v-on:change-event="departmentChanges"
                  ></select-department>
                  <message-data v-bind:messagedatas="messagedatadepartment"></message-data>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-90"
                      for="target_users"
                    >氏 名</label>
                  </div>
                  <select-user
                    ref="selectuser"
                    v-bind:blank-data="true"
                    v-bind:get-do="getDo"
                    v-bind:date-value="fromdate"
                    v-on:change-event="userChanges"
                  ></select-user>
                  <message-data v-bind:messagedatas="messagedatauser"></message-data>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <search-workingtimebutton v-on:searchclick-event="searchclick"></search-workingtimebutton>
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
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3 align-self-stretch">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:headertext1="stringtext"
            v-bind:headertext2="'虫眼鏡アイコンをクリックすると集計結果が表示されます'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- panel body -->
          <div
            v-for="(calclist,index) in calcresults"
            :key="calclist.user_code"
            class="card-body mb-3 py-0 pt-4 border-top"
          >
            <!-- panel contents -->
            <!-- .row -->
            <div class="row">
              <!-- col -->
              <div class="col-sm-6 col-md-6 col-sm-6 col-lg-4 pb-2 align-self-stretch">
                <a
                  class="float-left mr-2 px-2 py-2 font-size-rg btn btn-primary btn-lg"
                  data-toggle="collapse"
                  v-bind:href="'#collapseUser' + index"
                  role="button"
                  aria-expanded="true"
                  v-bind:aria-controls="'collapseUser' + index"
                >
                  <img class="icon-size-rg" src="/images/round-search-w.svg" alt />
                </a>
                <h1 class="font-size-sm m-0 mb-1">氏名</h1>
                <p class="font-size-rg font-weight-bold m-0">{{ calclist.user_name }}</p>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-sm-6 col-md-6 col-sm-6 col-lg-4 pb-2 align-self-stretch">
                <span class="float-left mr-2 py-2 px-2 font-size-lg text-white bg-success rounded">
                  <img class="icon-size-lg" src="/images/round-flag-w.svg" alt />
                </span>
                <h1 class="font-size-sm m-0 mb-1">部署</h1>
                <p class="font-size-rg m-0">{{ calclist.department_name }}</p>
              </div>
              <!-- /.col -->
              <!-- col 出勤 退勤 -->
              <col-attendance
                v-bind:attendancetime="calclist.attendance_time_1"
                v-bind:leavingtime="calclist.leaving_time_1"
                v-bind:displaynone="true"
                class="col-12 col-lg-4 p-0 align-self-stretch"
              ></col-attendance>
              <col-attendance
                v-bind:attendancetime="calclist.attendance_time_2"
                v-bind:leavingtime="calclist.leaving_time_2"
                v-bind:displaynone="false"
                class="col-12 col-lg-4 p-0 align-self-stretch"
              ></col-attendance>
              <col-attendance
                v-bind:attendancetime="calclist.attendance_time_3"
                v-bind:leavingtime="calclist.leaving_time_3"
                v-bind:displaynone="false"
                class="col-12 col-lg-4 p-0 align-self-stretch"
              ></col-attendance>
              <col-attendance
                v-bind:attendancetime="calclist.attendance_time_4"
                v-bind:leavingtime="calclist.leaving_time_4"
                v-bind:displaynone="false"
                class="col-12 col-lg-4 p-0 align-self-stretch"
              ></col-attendance>
              <col-attendance
                v-bind:attendancetime="calclist.attendance_time_5"
                v-bind:leavingtime="calclist.leaving_time_5"
                v-bind:displaynone="false"
                class="col-12 col-lg-4 p-0 align-self-stretch"
              ></col-attendance>
              <!-- /.col -->
              <!-- col 中抜け 戻り -->
              <col-missingmiddle
                v-bind:missingmiddletime="calclist.missing_middle_time_1"
                v-bind:missingmiddlereturntime="calclist.missing_middle_return_time_1"
                v-bind:displaynone="false"
                class="col-12 col-lg-4 p-0 align-self-stretch"
              ></col-missingmiddle>
              <col-missingmiddle
                v-bind:missingmiddletime="calclist.missing_middle_time_2"
                v-bind:missingmiddlereturntime="calclist.missing_middle_return_time_2"
                v-bind:displaynone="false"
                class="col-12 col-lg-4 p-0 align-self-stretch"
              ></col-missingmiddle>
              <col-missingmiddle
                v-bind:missingmiddletime="calclist.missing_middle_time_3"
                v-bind:missingmiddlereturntime="calclist.missing_middle_return_time_3"
                v-bind:displaynone="false"
                class="col-12 col-lg-4 p-0 align-self-stretch"
              ></col-missingmiddle>
              <col-missingmiddle
                v-bind:missingmiddletime="calclist.missing_middle_time_4"
                v-bind:missingmiddlereturntime="calclist.missing_middle_return_time_4"
                v-bind:displaynone="false"
                class="col-12 col-lg-4 p-0 align-self-stretch"
              ></col-missingmiddle>
              <col-missingmiddle
                v-bind:missingmiddletime="calclist.missing_middle_time_5"
                v-bind:missingmiddlereturntime="calclist.missing_middle_return_time_5"
                v-bind:displaynone="false"
                class="col-12 col-lg-4 p-0 align-self-stretch"
              ></col-missingmiddle>
            </div>
            <!-- /.row -->
            <!-- collapse -->
            <div class="collapse" v-bind:id="'collapseUser' + index">
              <!-- .row -->
              <div class="row mt-2">
                <!-- col  雇用形態 勤務時間 勤務シフト-->
                <col-employmentstatus
                  v-bind:item-name="'雇用形態'"
                  v-bind:item-value="calclist.employment_status_name"
                ></col-employmentstatus>
                <col-employmentstatus
                  v-bind:item-name="'勤務状態'"
                  v-bind:item-value="calclist.working_status_name"
                  v-bind:itemsecound-value="calclist.holiday_name"
                ></col-employmentstatus>
                <col-regularworking
                  v-bind:item-name="'勤務時間'"
                  v-bind:item-value="calclist.total_working_times"
                ></col-regularworking>
                <col-employmentstatus
                  v-bind:item-name="'勤務シフト'"
                  v-bind:item-value="calclist.working_timetable_name"
                ></col-employmentstatus>
                <!-- col  所定労働時間 所定外労働時間-->
                <col-regularworking
                  v-bind:item-name="'所定労働時間'"
                  v-bind:item-value="calclist.regular_working_times"
                ></col-regularworking>
                <col-regularworking
                  v-bind:item-name="'所定外労働時間'"
                  v-bind:item-value="calclist.out_of_regular_working_times"
                ></col-regularworking>
                <!-- /.col -->
                <!-- col -->
                <!-- col  残業時間 深夜残業時間 法定労働時間 法定外労働時間-->
                <col-overtimehours
                  v-bind:item-name="'残業時間'"
                  v-bind:item-value="calclist.overtime_hours"
                ></col-overtimehours>
                <col-overtimehours
                  v-bind:item-name="'深夜残業時間'"
                  v-bind:item-value="calclist.late_night_overtime_hours"
                ></col-overtimehours>
                <col-overtimehours
                  v-bind:item-name="'法定労働時間'"
                  v-bind:item-value="calclist.legal_working_times"
                ></col-overtimehours>
                <col-overtimehours
                  v-bind:item-name="'法定外労働時間'"
                  v-bind:item-value="calclist.out_of_legal_working_times"
                ></col-overtimehours>
                <!-- /.col -->
                <!-- col  未就労時間 時間外労働-->
                <col-notemploymentworking
                  v-bind:item-name="'未就労時間'"
                  v-bind:item-value="calclist.not_employment_working_hours"
                ></col-notemploymentworking>
                <col-notemploymentworking
                  v-bind:item-name="'時間外労働'"
                  v-bind:item-value="calclist.off_hours_working_hours"
                ></col-notemploymentworking>
                <!-- /.col -->
                <!-- col -->
                <col-note v-bind:item-name="'備考：'" v-bind:item-note="calclist.note" v-bind:item-late="calclist.late" v-bind:item-leaveearly="calclist.leave_early"></col-note>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- /collapse -->
          </div>
          <!-- /panel body -->
        </div>
      </div>
      <!-- /.panel -->
    </div>
    <!-- /main contentns row -->
    <!-- main contentns row -->
    <div v-for="sumresult in sumresults" class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:headertext1="'合計'"
            v-bind:headertext2="'集計日の合計が表示されます'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row">
              <col-regularworking
                v-bind:item-name="'勤務時間'"
                v-bind:item-value="sumresult.total_working_times"
              ></col-regularworking>
              <col-regularworking
                v-bind:item-name="'所定労働時間'"
                v-bind:item-value="sumresult.regular_working_times"
              ></col-regularworking>
              <col-regularworking
                v-bind:item-name="'所定外労働時間'"
                v-bind:item-value="sumresult.out_of_regular_working_times"
              ></col-regularworking>
              <col-overtimehours
                v-bind:item-name="'残業時間'"
                v-bind:item-value="sumresult.overtime_hours"
              ></col-overtimehours>
              <col-overtimehours
                v-bind:item-name="'深夜残業時間'"
                v-bind:item-value="sumresult.late_night_overtime_hours"
              ></col-overtimehours>
              <col-overtimehours
                v-bind:item-name="'法定労働時間'"
                v-bind:item-value="sumresult.legal_working_times"
              ></col-overtimehours>
              <col-overtimehours
                v-bind:item-name="'法定外労働時間'"
                v-bind:item-value="sumresult.out_of_legal_working_times"
              ></col-overtimehours>
              <col-notemploymentworking
                v-bind:item-name="'未就労時間'"
                v-bind:item-value="sumresult.not_employment_working_hours"
              ></col-notemploymentworking>
              <col-notemploymentworking
                v-bind:item-name="'時間外労働'"
                v-bind:item-value="sumresult.off_hours_working_hours"
              ></col-notemploymentworking>
              <col-notemploymentworking
                v-bind:item-name="'出勤者数'"
                v-bind:item-value="sumresult.total_working_status"
              ></col-notemploymentworking>
              <col-notemploymentworking
                v-bind:item-name="'外出者数'"
                v-bind:item-value="sumresult.total_go_out"
              ></col-notemploymentworking>
              <col-notemploymentworking
                v-bind:item-name="'休暇者数'"
                v-bind:item-value="sumresult.total_holiday_kubun"
              ></col-notemploymentworking>
            </div>
            <!-- /.row -->
            <!-- /panel contents -->
          </div>
        </div>
        <!-- /panel -->
      </div>
      <!-- /main contentns row -->
    </div>
  </div>
</template>

<script>
import toasted from "vue-toasted";
import moment from "moment";

export default {
  name: "dailyworkingtime",
  data: function() {
    return {
      valuedepartment: "",
      valueemploymentstatus: "",
      getDo: 1,
      fromdate: "",
      valueuser: "",
      valuefromdate: "",
      userrole: "",
      defaultDate: new Date(),
      stringtext: "",
      datejaFormat: "",
      hrefindex: "",
      resresults: [],
      calcresults: [],
      sumresults: [],
      messagedatasserver: [],
      messagedatasfromdate: [],
      messagedatastodate: [],
      messagedatadepartment: [],
      messagedatauser: [],
      validate: false,
      initialized: false
    };
  },
  // マウント時
  mounted() {
    this.getUserRole();
  },
  methods: {
    // バリデーション
    checkForm: function(e) {
      console.log("checkForm in ");
      this.validate = true;
      this.messagedatasserver = [];
      this.messagedatasfromdate = [];
      this.messagedatastodate = [];
      this.messagedatadepartment = [];
      this.messagedatauser = [];
      if (!this.valuefromdate) {
        this.messagedatasfromdate.push("指定日付は必ず入力してください。");
        this.validate = false;
      }
      if (this.userrole < "8") {
        if (!this.valuedepartment) {
          this.messagedatadepartment.push("所属部署は必ず入力してください。");
          this.validate = false;
        }
        if (!this.valueuser) {
          this.messagedatauser.push("氏名は必ず入力してください。");
          this.validate = false;
        }
      }
      console.log("validate = true");

      if (this.validate) {
        return this.validate;
      }

      e.preventDefault();
    },
    // ログインユーザーの権限を取得
    getUserRole: function() {
      this.$axios
        .get("/get_login_user_role", {
        })
        .then(response => {
          this.userrole = response.data;
        })
        .catch(reason => {
          alert("ログインユーザー権限取得エラー");
        });
    },
    // 指定日付が変更された場合の処理
    fromdateChanges: function(value) {
      moment.locale("ja");
      this.valuefromdate = value;
      if (this.valuefromdate == null || this.valuefromdate == "") {
        this.stringtext = "";
      } else {
        this.datejaFormat = moment(this.valuefromdate).format(
          "YYYY年MM月DD日 (ddd)"
        );
        this.stringtext = "日次集計 " + this.datejaFormat;
      }
      // 再取得
      this.fromdate = ""
      if (this.valuefromdate) {
        this.fromdate = moment(this.valuefromdate).format("YYYYMMDD");
      }
      this.$refs.selectdepartment.getDepartmentList(this.fromdate);
      this.getUserSelected();
    },
    // 雇用形態が変更された場合の処理
    employmentChanges: function(value) {
      this.valueemploymentstatus = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getUserSelected();
    },
    // 部署選択が変更された場合の処理
    departmentChanges: function(value) {
      this.valuedepartment = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getUserSelected();
    },
    // ユーザー選択が変更された場合の処理
    userChanges: function(value) {
      this.valueuser = value;
    },
    // 集計開始ボタンがクリックされた場合の処理
    searchclick: function(e) {
      this.validate = this.checkForm(e);
      if (this.validate) {
        this.$axios
          .get("/daily/calc", {
            params: {
              datefrom: moment(this.valuefromdate).format("YYYYMMDD"),
              dateto: moment(this.valuefromdate).format("YYYYMMDD"),
              employmentstatus: this.valueemploymentstatus,
              departmentcode: this.valuedepartment,
              usercode: this.valueuser
            }
          })
          .then(response => {
            this.resresults = response.data;
            this.calcresults = this.resresults.calcresults;
            this.sumresults = this.resresults.sumresults;
            this.dispmessage(this.resresults.massegedata);
            this.messagedatasserver.length = 0;
            this.$forceUpdate();
            console.log("sumresults" + Object.keys(this.sumresults).length);
          })
          .catch(reason => {
            alert("error");
          });
      }
    },

    // ----------------- 共通メソッド ----------------------------------

    // ユーザー選択コンポーネント取得メソッド
    getUserSelected: function() {
      this.fromdate = ""
      if (this.valuefromdate) {
        this.fromdate = moment(this.valuefromdate).format("YYYYMMDD");
      }
      if (this.valueemploymentstatus == "") {
        if (this.valuedepartment == "") {
          this.$refs.selectuser.getUserList(this.getDo, this.fromdate);
        } else {
          this.$refs.selectuser.getUserListByDepartment(
            this.getDo,
            this.valuedepartment,
            this.fromdate
          );
        }
      } else {
        if (this.valuedepartment == "") {
          this.$refs.selectuser.getUserListByEmployment(
            this.getDo,
            this.valueemploymentstatus,
            this.fromdate
          );
        } else {
          this.$refs.selectuser.getUserListByDepartmentEmployment(
            this.getDo,
            this.valuedepartment,
            this.valueemploymentstatus,
            this.fromdate
          );
        }
      }
    },
    // メッセージ処理
    dispmessage: function(items) {
      items.forEach(function(value) {
        this.messagedatasserver.push(value);
      });
    },
    // メッセージ処理
    dispmessagevalue: function(value) {
      this.messagedatasserver.push(value);
    },
    // メッセージ処理
    dispmessage1: function(items) {
      console.log("dispmessage1 " + Object.keys(items).length);
      Object.keys(items).forEach(function (value) {
        console.log(value.messagedata + "はと鳴いた！");
      });
    }
  }
};
</script>
