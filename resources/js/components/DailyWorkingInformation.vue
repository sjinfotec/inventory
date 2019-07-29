<template>
  <!-- main contentns row -->
  <div>
  <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
          <div class="card bg-secondary text-white pt-2 border-0 shadow-pl">
              <!-- panel header -->
              <div class="card-header bg-transparent pt-2 border-0">
                  <h1 class="float-left font-size-xl line-height-rg"><img class="icon-size-rg mr-3" src="/images/round-notifications-none-w.svg" alt="">日次集計</h1>
              </div>
              <!-- /.panel header -->
          </div>
      </div>
      <!-- /.panel -->
  </div>
  <!-- main contentns row -->
  <div class="row justify-content-between">
    <!-- .panel -->
    <div class="col-md pt-3">
      <div class="card shadow-pl">
        <!-- panel header -->
        <daily-working-information-panel-header
          v-bind:headertext1="'日付を指定して集計を表示する'" v-bind:headertext2="'雇用形態や所属部署でフィルタリングして表示できます'">
        </daily-working-information-panel-header>
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
                  <span class="input-group-text font-size-sm line-height-xs label-width-90" for="target_fromdate">指定日付</span>
                </div>
                <input-datepicker v-bind:default-Date="defaultDate" v-on:change-event="fromdateChanges"></input-datepicker>
                <message-data v-bind:messagedatas="messagedatasfromdate"></message-data>
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <label class="input-group-text font-size-sm line-height-xs label-width-90" for="target_employmentstatus">雇用形態</label>
                </div>
                <select-employmentstatus v-bind:blank-data="true" v-on:change-event="employmentChanges"></select-employmentstatus>
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <label class="input-group-text font-size-sm line-height-xs label-width-90" for="target_department">所属部署</label>
                </div>
                <select-department v-bind:blank-data="true" v-on:change-event="departmentChanges"></select-department>
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <label class="input-group-text font-size-sm line-height-xs label-width-90" for="target_users">氏　　名</label>
                </div>
                <select-user ref="selectuser" v-bind:blank-data="true" v-bind:get-Do="getDo" v-on:change-event="userChanges"></select-user>
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
          v-bind:headertext1="stringtext" v-bind:headertext2="'虫眼鏡アイコンをクリックすると集計結果が表示されます'">
        </daily-working-information-panel-header>
        <!-- /.panel header -->
        <!-- panel body -->
        <div class="card-body mb-3 py-0 pt-4 border-top">
          <!-- panel contents --> 
          <!-- .row -->
          <div v-for="(calclist,index) in calcresults">
          <div class="row">
            <!-- col -->
            <div class="col-sm-12 col-md-12 col-sm-6 col-lg-4 pb-2 align-self-stretch">
              <a class="float-left mr-2 px-2 py-2 font-size-rg btn btn-primary btn-lg" data-toggle="collapse" href="#collapseUser" role="button" aria-expanded="true" aria-controls="collapseUser1"><img class="icon-size-rg" src="/images/round-search-w.svg" alt=""></a>
              <h1 class="font-size-sm m-0 mb-1">氏名</h1>
              <p class="font-size-rg font-weight-bold m-0">{{ calclist.user_name }}</p>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-12 col-md-12 col-sm-6 col-lg-4 pb-2 align-self-stretch">
              <span class="float-left mr-2 py-2 px-2 font-size-lg text-white bg-success rounded"><img class="icon-size-lg" src="/images/round-flag-w.svg" alt=""></span>
              <h1 class="font-size-sm m-0 mb-1">部署</h1>
              <p class="font-size-rg m-0">{{ calclist.department_name }}</p>
            </div>
            <!-- /.col -->
            <!-- col -->
            <col-attendance
              v-bind:attendancetime="calclist.attendance_time_1" v-bind:leavingtime="calclist.leaving_time_1" v-bind:displaynone="true">
            </col-attendance>
            <col-attendance
              v-bind:attendancetime="calclist.attendance_time_2" v-bind:leavingtime="calclist.leaving_time_2" v-bind:displaynone="false">
            </col-attendance>
            <col-attendance
              v-bind:attendancetime="calclist.attendance_time_3" v-bind:leavingtime="calclist.leaving_time_3" v-bind:displaynone="false">
            </col-attendance>
            <col-attendance
              v-bind:attendancetime="calclist.attendance_time_4" v-bind:leavingtime="calclist.leaving_time_4" v-bind:displaynone="false">
            </col-attendance>
            <col-attendance 
              v-bind:attendancetime="calclist.attendance_time_5" v-bind:leavingtime="calclist.leaving_time_5" v-bind:displaynone="false">
            </col-attendance>
            <!-- /.col -->
            <!-- col -->
            <col-missingmiddle
              v-bind:missingmiddletime="calclist.missing_middle_time_1" v-bind:missingmiddlereturntime="calclist.missing_middle_return_time_1" v-bind:displaynone="true">
            </col-missingmiddle>
            <col-missingmiddle
              v-bind:missingmiddletime="calclist.missing_middle_time_2" v-bind:missingmiddlereturntime="calclist.missing_middle_return_time_2" v-bind:displaynone="false">
            </col-missingmiddle>
            <col-missingmiddle
              v-bind:missingmiddletime="calclist.missing_middle_time_3" v-bind:missingmiddlereturntime="calclist.missing_middle_return_time_3" v-bind:displaynone="false">
            </col-missingmiddle>
            <col-missingmiddle
              v-bind:missingmiddletime="calclist.missing_middle_time_4" v-bind:missingmiddlereturntime="calclist.missing_middle_return_time_4" v-bind:displaynone="false">
            </col-missingmiddle>
            <col-missingmiddle
              v-bind:missingmiddletime="calclist.missing_middle_time_5" v-bind:missingmiddlereturntime="calclist.missing_middle_return_time_5" v-bind:displaynone="false">
            </col-missingmiddle>
          </div>
          <!-- /.row -->
          <!-- collapse -->
          <div class="collapse" id="collapseUser1">
            <!-- .row -->
            <div class="row mt-2">
              <!-- col -->
              <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
                <div class="card text-white bg-secondary border-0">
                  <div class="card-body px-3 py-2">
                    <h1 class="font-size-sm m-0 mb-1">雇用形態</h1>
                    <p class="font-size-rg m-0">正社員</p>
                  </div>
                </div>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
                <div class="card text-white bg-secondary border-0">
                  <div class="card-body px-3 py-2">
                    <h1 class="font-size-sm m-0 mb-1">勤務時間</h1>
                    <p class="font-size-lg m-0">08:13</p>
                  </div>
                </div>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
                <div class="card text-white bg-secondary border-0">
                  <div class="card-body px-3 py-2">
                    <h1 class="font-size-sm m-0 mb-1">勤務状態</h1>
                    <p class="font-size-rg m-0">退勤</p>
                  </div>
                </div>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
                <div class="card text-white bg-secondary border-0">
                  <div class="card-body px-3 py-2">
                    <h1 class="font-size-sm m-0 mb-1">勤務シフト</h1>
                    <p class="font-size-rg m-0">通常</p>
                  </div>
                </div>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
                <div class="card text-white bg-primary border-0">
                  <div class="card-body px-3 py-2">
                    <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-access-time-w.svg" alt=""></span>
                    <h1 class="font-size-sm m-0 mb-1">所定労働時間</h1>
                    <p class="font-size-lg m-0">08:00</p>
                  </div>
                </div>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
                <div class="card text-white bg-primary border-0">
                  <div class="card-body px-3 py-2">
                    <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-access-time-w.svg" alt=""></span>
                    <h1 class="font-size-sm m-0 mb-1">所定外労働時間</h1>
                    <p class="font-size-lg m-0">08:00</p>
                  </div>
                </div>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
                <div class="card text-white bg-success border-0">
                  <div class="card-body px-3 py-2">
                    <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-w.svg" alt=""></span>
                    <h1 class="font-size-sm m-0 mb-1">残業時間</h1>
                    <p class="font-size-lg m-0">00:13</p>
                  </div>
                </div>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
                <div class="card text-white bg-success border-0">
                  <div class="card-body px-3 py-2">
                    <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-w.svg" alt=""></span>
                    <h1 class="font-size-sm m-0 mb-1">深夜残業時間</h1>
                    <p class="font-size-lg m-0">00:00</p>
                  </div>
                </div>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
                <div class="card text-white bg-success border-0">
                  <div class="card-body px-3 py-2">
                    <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-w.svg" alt=""></span>
                    <h1 class="font-size-sm m-0 mb-1">法定労働時間</h1>
                    <p class="font-size-lg m-0">00:00</p>
                  </div>
                </div>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
                <div class="card text-white bg-success border-0">
                  <div class="card-body px-3 py-2">
                    <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-w.svg" alt=""></span>
                    <h1 class="font-size-sm m-0 mb-1">法定外労働時間</h1>
                    <p class="font-size-lg m-0">00:00</p>
                  </div>
                </div>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
                <div class="card text-white bg-warning border-0">
                  <div class="card-body px-3 py-2">
                    <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-w.svg" alt=""></span>
                    <h1 class="font-size-sm m-0 mb-1">未就労時間</h1>
                    <p class="font-size-lg m-0">00:00</p>
                  </div>
                </div>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
                <div class="card text-white bg-warning border-0">
                  <div class="card-body px-3 py-2">
                    <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-w.svg" alt=""></span>
                    <h1 class="font-size-sm m-0 mb-1">時間外労働</h1>
                    <p class="font-size-lg m-0">00:00</p>
                  </div>
                </div>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-12 pb-2 align-self-stretch">
                <div class="card text-white bg-danger border-0">
                  <div class="card-body px-3 py-2">
                    <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-error-w.svg" alt=""></span>
                    <h1 class="font-size-sm m-0">お知らせ</h1>
                    <p class="font-size-sm my-2">※打刻時間が不正です。</p>
                  </div>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          </div>
          <!-- /collapse -->
          <!-- /.panel contents -->
        </div>
        <!-- /panel body -->
      </div>
    </div>
    <!-- /.panel -->
  </div>
  <!-- /main contentns row -->
  <!-- main contentns row -->
  <div class="row justify-content-between">
    <!-- .panel -->
    <div class="col-md p-3">
      <div class="text-center">
        <small>© 2019 One Door</small>
      </div>
    </div>
    <!-- /.panel -->
  </div>
  <!-- /main contentns row -->
  <!-- main contentns row -->
  </div>
</template>

<script>

import toasted from "vue-toasted";
import moment from 'moment';

export default {
  name: "dailyworkingtime",
  data: function() {
    return {
      valuedepartment: '',
      valueemploymentstatus: '',
      getDo: 0,
      valueuser: '',
      valuefromdate: '',
      defaultDate: new Date(),
      stringtext: '',
      datejaFormat: '',
      resresults: [],
      calcresults: [],
      messagedatasserver: [],
      messagedatasfromdate: [],
      messagedatastodate: [],
      validate: false,
      initialized: false
    }
  },
  methods: {
    // バリデーション
    checkForm: function (e) {
      console.log("checkForm in ");
      this.validate = true;
      this.messagedatasserver = [];
      this.messagedatasfromdate = [];
      this.messagedatastodate = [];
      if (!this.valuefromdate) {
        this.messagedatasfromdate.push("指定日付は必ず入力してください。");
        this.validate = false;
      }

      if (this.validate) {return this.validate;}

      e.preventDefault();
      
    },
    // 雇用形態が変更された場合の処理
    employmentChanges: function(value){
      this.valueemploymentstatus = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      if(this.valuedepartment == ''){
        this.$refs.selectuser.getUserList(this.getDo, value);
      } else {
        this.$refs.selectuser.getUserListByEmployment(this.getDo, this.valuedepartment, value);
      }
    },
    // 部署選択が変更された場合の処理
    departmentChanges: function(value){
      this.valuedepartment = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      if(this.valueemploymentstatus == ''){
        this.$refs.selectuser.getUserList(this.getDo, value);
      } else {
        this.$refs.selectuser.getUserListByEmployment(this.getDo, value, this.valueemploymentstatus);
      }
    },
    // ユーザー選択が変更された場合の処理
    userChanges: function(value){
      this.valueuser = value;
    },
    // 指定日付が変更された場合の処理
    fromdateChanges: function(value){
      moment.locale("ja");
      this.valuefromdate = value;
      if(this.valuefromdate == null || this.valuefromdate == ''){
        this.stringtext = "";
      } else {
        this.datejaFormat =  moment(this.valuefromdate).format('YYYY年MM月DD日 (ddd)');
        this.stringtext = "日次集計 " + this.datejaFormat;
      }
    },
    // 集計開始ボタンがクリックされた場合の処理
    searchclick: function(e){
      this.validate = this.checkForm(e);
      if(this.validate){
        this.$axios
          .get("/daily/calc", {
            params: {
              datefrom: moment(this.valuefromdate).format('YYYYMMDD'),
              dateto: moment(this.valuefromdate).format('YYYYMMDD'),
              employmentstatus: this.valueemploymentstatus,
              departmentcode: this.valuedepartment,
              usercode: this.valueuser
            }
          })
          .then(response => {
            this.resresults = response.data;
            this.calcresults = this.resresults.calcresults;
            this.dispmessage(this.resresults.massegedata);
            console.log("集計時間取得"+Object.keys(this.resresults).length);
          })
          .catch(reason => {
            alert("error");
          });
      }
    },
    // メッセージ処理
    dispmessage: function(items){
      items.forEach(function( value ) {
        this.messagedatasserver.push(value);
      });
    }
  }
};
</script>
