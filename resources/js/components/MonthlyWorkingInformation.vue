<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between print-none">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'年月を指定して集計を表示する'"
            v-bind:header-text2="'雇用形態や所属部署でフィルタリングして表示できます'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
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
                    >指定年月<span class="color-red">[*]</span></span>
                  </div>
                  <input-datepicker
                    v-bind:default-date="defaultDate"
                    v-bind:date-format="'yyyy年MM月'"
                    v-on:change-event="fromdateChanges"
                  ></input-datepicker>
                </div>
                <message-data v-bind:message-datas="messagedatasfromdate" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-90"
                      for="inputGroupSelect01"
                    >集計区分<span class="color-red">[*]</span></label>
                  </div>
                  <general-list
                    v-bind:identification-id="'C016'"
                    v-bind:placeholder-data="'集計区分を選択してください'"
                    v-bind:blank-data="false"
                    v-on:change-event="displayChange"
                  ></general-list>
                </div>
                <message-data v-bind:message-datas="messagedatadisplay" v-bind:message-class="'warning'"></message-data>
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
                  <select-department
                    ref="selectdepartment"
                    v-bind:blank-data="true"
                    v-on:change-event="departmentChanges"
                  ></select-department>
                </div>
                <message-data v-bind:message-datas="messagedatadepartment" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-90"
                      for="inputGroupSelect01"
                    >氏 名</label>
                  </div>
                  <select-user
                    ref="selectuser"
                    v-bind:blank-data="true"
                    v-bind:get-Do="'1'"
                    v-bind:date-value="fromdate"
                    v-on:change-event="userChanges"
                  ></select-user>
                  <message-data v-bind:message-datas="messagedatauser" v-bind:message-class="'warning'"></message-data>
                </div>
              </div>
              <div class="col-md-6 pb-2">
                <message-data-server v-bind:message-datas="messagedatasserver" v-bind:message-class="'warning'"></message-data-server>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:searchclick-event="searchclick"
                  v-bind:btn-mode="'search'"
                  v-bind:is-push="issearchbutton">
                </btn-work-time>
                <message-waiting v-bind:is-message-show="messageshowsearch"></message-waiting>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div v-if="isswitchvisible" class="col-md-12 pb-2">
                <btn-work-time
                  v-on:switchclick-event="switchclick"
                  v-bind:btn-mode="btnmodeswitch"
                  v-bind:is-push="isswitchbutton">
                </btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:updateclick-event="updateclick"
                  v-bind:btn-mode="'update'"
                  v-bind:is-push="isupdatebutton">
                </btn-work-time>
                <message-waiting v-bind:is-message-show="messageshowupdate"></message-waiting>
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
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3 align-self-stretch">
        <div class="card shadow-pl">
          <!-- panel header -->
          <div class="card-header bg-transparent pt-3 border-0 print-none">
            <daily-working-information-panel-header
              v-bind:header-text1="stringtext"
              v-bind:header-text2="'虫眼鏡アイコンをクリックするとタイムカードが表示されます'"
            ></daily-working-information-panel-header>
            <daily-working-information-panel-header
              v-bind:header-text1="''"
              v-bind:header-text2="'タイムカードを印刷する場合は Ctrl+P で印刷してください。'"
            ></daily-working-information-panel-header>
          </div>
          <!-- /.panel header -->
          <!-- panel body -->
          <div class="card-body mb-3 py-0 pt-4 border-top print-none">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <div class="btn-group d-flex">
                  <btn-csv-download
                    v-bind:csv-data="calcresults"
                    v-bind:is-csvbutton="iscsvbutton"
                    v-bind:csv-date="datejaFormat"
                  >
                  </btn-csv-download>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.panel contents -->
          </div>
          <!-- /panel body -->
          <!-- panel body -->
          <div class="card-body mb-3 py-0 pt-4 border-top print-none">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <h1 class="float-md-left font-size-rg">{{ company_name }}</h1>
                <span class="float-md-right font-size-sm">{{ datejaFormat }}</span>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.panel contents -->
          </div>
          <!-- /panel body -->
          <!-- panel body -->
          <div
            v-for="(calclist,index) in calcresults"
            :key="calclist.user_code"
          >
            <!-- panel contents -->
            <!-- .row -->
            <div class="row">
              <div class="card-body mb-3 py-0 pt-4 border-top print-only print-space">
                <!-- panel contents -->
                <!-- .row -->
                <div class="row">
                  <!-- col -->
                  <div class="col-md-12 pb-2">
                    <h1 class="float-md-left font-size-rg">{{ company_name }}</h1>
                    <span class="float-md-right font-size-sm">{{ datejaFormat }}</span>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
                <!-- /.panel contents -->
              </div>
            </div>
            <div class="row">
              <!-- col -->
              <div class="col-sm-2 col-md-2 col-lg-2 pb-2 align-self-stretch">
                <a
                  class="float-left mr-2 px-2 py-2 font-size-rg btn btn-primary btn-lg print-none"
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
              <div class="col-sm-6 col-md-6 col-lg-6 pb-2 align-self-stretch">
                <working-chart
                  v-on:label-data="'残業時間＋深夜残業時間'"
                  v-on:chart-data="'36.5'"
                >
                </working-chart>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-sm-4 col-md-4 col-lg-4 pb-2 align-self-stretch">
                <h1 class="font-size-sm m-0 mb-1 text-sm-right">所属部署</h1>
                <p class="font-size-rg m-0 text-sm-right">{{ calclist.department }}</p>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <monthly-working-info-table
              v-bind:detail-or-total="'detail'"
              v-bind:calc-lists="calclist"
              v-bind:btn-mode="btnmodeswitch"
            ></monthly-working-info-table>
            <!-- /.row -->
            <!-- /.panel contents -->
            <!-- /panel body -->
            <!-- collapse -->
            <div class="collapse page-break-after" v-bind:id="'collapseUser' + index">
              <!-- panel body -->
              <div class="card-body mb-3 p-0 border-top">
                <!-- panel contents -->
                <!-- .row -->
                <div class="row">
                  <div class="col-12">
                    <div class="table-responsive">
                      <div class="col-12 p-0">
                        <table class="table table-striped border-bottom font-size-sm text-nowrap">
                          <div v-if="calclist.date.length">
                            <thead>
                              <tr>
                                <td class="text-center align-middle w-15">日付</td>
                                <td class="text-center align-middle w-15">出勤時間</td>
                                <td class="text-center align-middle w-15">退勤時間</td>
                                <td class="text-center align-middle w-15">実働時間</td>
                                <td class="text-center align-middle w-15">所定時間</td>
                                <td class="text-center align-middle w-15">残業時間</td>
                                <td class="text-center align-middle w-15">深夜時間</td>
                                <td class="text-center align-middle mw-rem-20">備考</td>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="calclisttimedate in calclist.date">
                                <td
                                  class="text-center align-middle"
                                >{{ calclisttimedate.workingdate }}</td>
                                <td
                                  v-if="calclisttimedate.attendance != '00:00' || calclisttimedate.leaving != '00:00'"
                                  class="text-center align-middle"
                                >{{ calclisttimedate.attendance }}</td>
                                <td
                                  v-if="calclisttimedate.attendance != '00:00' || calclisttimedate.leaving != '00:00'"
                                  class="text-center align-middle"
                                >{{ calclisttimedate.leaving }}</td>
                                <td
                                  class="text-center align-middle"
                                >{{ calclisttimedate.total_working_times }}</td>
                                <td
                                  class="text-center align-middle"
                                >{{ calclisttimedate.regular_working_times }}</td>
                                <td
                                  class="text-center align-middle"
                                >{{ calclisttimedate.off_hours_working_hours }}</td>
                                <td
                                  class="text-center align-middle"
                                >{{ calclisttimedate.late_night_overtime_hours }}</td>
                                <td
                                  class="text-left align-middle"
                                >{{ calclisttimedate.remark_holiday_name }}</td>
                              </tr>
                            </tbody>
                          </div>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.row -->
                <!-- /.panel contents -->
              </div>
              <!-- /panel body -->
            </div>
          </div>
          <!-- /collapse -->
        </div>
      </div>
      <!-- /.panel -->
    </div>
    <!-- /main contentns row -->
    <!-- main contentns row -->
    <div class="row justify-content-between print-none">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'合計'"
            v-bind:header-text2="'集計月の合計が表示されます'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <monthly-working-info-table
              v-bind:detail-or-total="'total'"
              v-bind:calc-lists="sumresults"
              v-bind:btn-mode="btnmodeswitch"
            ></monthly-working-info-table>
            <!-- /.row -->
            <!-- /panel contents -->
          </div>
          <!-- /collapse -->
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
  data: function() {
    return {
      placeholderdata: "",
      company_name: "",
      valueym: "",
      valuedisplay: "",
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
      serchorupdate: "",
      messagedatasserver: [],
      messagedatasfromdate: [],
      messagedatastodate: [],
      messagedatadisplay: [],
      messagedatadepartment: [],
      messagedatauser: [],
      messageshowsearch: false,
      messageshowupdate: false,
      issearchbutton: false,
      isupdatebutton: false,
      iscsvbutton: true,
      btnmodeswitch: "basicswitch",
      isswitchbutton: false,
      isswitchvisible: false,
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
      this.validate = true;
      this.messagedatasserver = [];
      this.messagedatasfromdate = [];
      this.messagedatadisplay = [];
      this.messagedatadepartment = [];
      this.messagedatauser = [];
      if (!this.valueym) {
        this.messagedatasfromdate.push("指定年月は必ず入力してください。");
        this.validate = false;
      }
      if (!this.valuedisplay) {
        this.messagedatadisplay.push("集計区分は必ず入力してください。");
        this.validate = false;
      }
      if (this.serchorupdate == "update") {
        if (!this.valueuser) {
          if (!this.valuedepartment) {
            this.messagedatadepartment.push("指定年月締め一括集計の場合は所属部署は必ず入力してください。");
            this.validate = false;
          }
        }
        if (this.userrole < "8") {
          if (!this.valueuser) {
            this.messagedatauser.push("氏名は必ず入力してください。");
            this.validate = false;
          }
        }
      } else {
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
      }

      if (this.validate) {
        return this.validate;
      }

      e.preventDefault();
    },
    // ログインユーザーの権限を取得
    getUserRole: function() {
      this.$axios
        .get("/get_login_user_role", {})
        .then(response => {
          this.userrole = response.data;
        })
        .catch(reason => {
          alert("ログインユーザー権限取得エラー");
        });
    },
    // 指定年月が変更された場合の処理
    fromdateChanges: function(value) {
      console.log("fromdateChanges value = " + value);
      this.valueym = value;
      // パネルに表示
      this.setPanelHeader();
      // 再取得
      this.fromdate = "";
      if (this.valuefromdate) {
        this.fromdate = moment(this.valuefromdate).format("YYYYMMDD");
      }
      this.$refs.selectdepartment.getDepartmentList(this.fromdate);
      this.getUserSelected();
    },
    // 表示区分が変更された場合の処理
    displayChange: function(value, name) {
      this.valuedisplay = value;
      this.setPanelHeader();
    },
    // 雇用形態が変更された場合の処理
    employmentChanges: function(value) {
      this.valueemploymentstatus = value;
      this.getDo = 1;
      // ユーザー選択コンポーネントの取得メソッドを実行
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
    // 表示ボタンがクリックされた場合の処理
    searchclick: function(e) {
      this.serchorupdate = "search";
      this.isswitchvisible = false;
      this.validate = this.checkForm(e);
      if (this.validate) {
        this.issearchbutton = true;
        this.isupdatebutton = true;
        this.iscsvbutton = true;
        this.messageshowsearch = true;
        this.itemClear();
        this.$axios
          .get("/monthly/show", {
            params: {
              datefrom: moment(this.valuefromdate).format("YYYYMM"),
              displaykbn: this.valuedisplay,
              employmentstatus: this.valueemploymentstatus,
              departmentcode: this.valuedepartment,
              usercode: this.valueuser
            }
          })
          .then(response => {
            this.resresults = response.data;
            if (this.resresults.calcresults != null) {
              this.calcresults = this.resresults.calcresults;
              if (Object.keys(this.calcresults).length > 0) {
                this.iscsvbutton = false;
                this.isswitchvisible = true;
              }
            }
            if (this.resresults.sumresults != null) {
              this.sumresults = this.resresults.sumresults;
            }
            this.company_name = this.resresults.company_name;
            if (this.resresults.messagedata != null) {
              this.messagedatasserver = this.resresults.messagedata;
            }
            console.log("calcresults" + Object.keys(this.calcresults).length);
            console.log("sumresults" + Object.keys(this.sumresults).length);
            console.log("sumresults" + Object.keys(this.messagedatasserver).length);
            console.log("company_name" + this.company_name);
            this.messageshowsearch = false;
            this.issearchbutton = false;
            this.isupdatebutton = false;
            this.$forceUpdate();
          })
          .catch(reason => {
            this.messageshowsearch = false;
            this.issearchbutton = false;
            this.isupdatebutton = false;
            this.iscsvbutton = true;
            alert("月次集計エラー");
          });
      }
    },
    // 詳細表示ボタンがクリックされた場合の処理
    switchclick: function() {
      if (this.btnmodeswitch == "basicswitch") {
        this.btnmodeswitch = "detailswitch";
      } else {
        this.btnmodeswitch = "basicswitch";
      }

    },
    // 最新更新開始ボタンがクリックされた場合の処理
    updateclick: function(e) {
      this.infoDialog(e, 'update','確認','集計してよろしいですか？');
    },
    // 最新更新開始ボタンがクリックされた場合の処理
    updNew: function(e) {
      this.serchorupdate = "update";
      this.validate = this.checkForm(e);
      console.log("validate = " + this.validate);
      if (this.validate) {
        this.issearchbutton = true;
        this.isupdatebutton = true;
        this.iscsvbutton = true;
        this.messageshowupdate = true;
        this.itemClear();
        this.$axios
          .get("/monthly/calc", {
            params: {
              datefrom: moment(this.valuefromdate).format("YYYYMM"),
              displaykbn: this.valuedisplay,
              employmentstatus: this.valueemploymentstatus,
              departmentcode: this.valuedepartment,
              usercode: this.valueuser
            }
          })
          .then(response => {
            this.resresults = response.data;
            if (this.resresults.calcresults != null) {
              this.calcresults = this.resresults.calcresults;
            }
            if (this.resresults.sumresults != null) {
              this.sumresults = this.resresults.sumresults;
            }
            this.company_name = this.resresults.company_name;
            if (this.resresults.messagedata != null) {
              this.messagedatasserver = this.resresults.messagedata;
            }
            this.messageshowupdate = false;
            this.issearchbutton = false;
            this.isupdatebutton = false;
            this.$forceUpdate();
          })
          .catch(reason => {
            this.messageshowupdate = false;
            this.issearchbutton = false;
            this.isupdatebutton = false;
            this.iscsvbutton = true;
            alert("月次集計最新更新エラー");
          });
      }
    },

    // ----------------- 共通メソッド ----------------------------------
    // 確認ダイアログ処理
    infoDialog: function(e, value, title, text) {
      this.$swal({
        title: title,
        text: text,
        icon: 'info',
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          if (value === 'update') {
            this.itemClear();
            this.updNew(e);
          }
        }
      });
    },
    // クリアメソッド
    itemClear: function() {
      this.resresults = [];
      this.calcresults = [];
      this.sumresults = [];
      this.serchorupdate = "";
      this.messagedatasserver = [];
      this.messagedatasfromdate = [];
      this.messagedatastodate = [];
      this.messagedatadisplay = [];
      this.messagedatadepartment = [];
      this.messagedatauser = [];
    },
    // ユーザー選択コンポーネント取得メソッド
    getUserSelected: function() {
      this.valueuser = "";
      this.getDo = 1;
      this.fromdate = "";
      if (this.valuefromdate) {
        this.fromdate = moment(this.valuefromdate).format("YYYYMMDD");
      }
      if (this.valueemploymentstatus == "") {
        if (this.valuedepartment == "") {
          this.$refs.selectuser.getUserList(this.getDo, this.valueuser, this.fromdate);
        } else {
          this.$refs.selectuser.getUserListByDepartment(
            this.getDo,
            this.valuedepartment,
            this.valueuser,
            this.fromdate
          );
        }
      } else {
        if (this.valuedepartment == "") {
          this.$refs.selectuser.getUserListByEmployment(
            this.getDo,
            this.valueemploymentstatus,
            this.valueuser,
            this.fromdate
          );
        } else {
          this.$refs.selectuser.getUserListByDepartmentEmployment(
            this.getDo,
            this.valuedepartment,
            this.valueemploymentstatus,
            this.valueuser,
            this.fromdate
          );
        }
      }
    },

    // 集計パネルヘッダ文字列編集処理
    setPanelHeader: function() {
      moment.locale("ja");
      if (this.valueym == null || this.valueym == "") {
        this.stringtext = "";
      } else {
        if (this.valuedisplay == null || this.valuedisplay == "") {
          this.stringtext = "";
        } else {
          this.valuefromdate = this.valueym;
          if (
            moment(this.valuefromdate).format("YYYYMM") !=
            moment().format("YYYYMM")
          ) {
            this.valuefromdate = moment(this.valuefromdate)
              .endOf("month")
              .format("YYYYMMDD");
          } else {
            this.valuefromdate = moment().format("YYYYMMDD");
          }
          this.datejaFormat = moment(this.valuefromdate).format("YYYY年MM月");
          if (this.valuedisplay == "1") {
            this.stringtext =
              "月次集計 " + this.datejaFormat + "分を〆日で集計";
          } else {
            if (this.valuedisplay == "2") {
              this.stringtext =
                "月次集計 " + this.datejaFormat + "分を1日から月末で集計";
            } else {
              this.stringtext = "";
            }
          }
        }
      }
    }
  }
};
</script>
