<template>
<div>
  <!-- main contentns row -->
  <div class="row justify-content-between print-none">
    <!-- .panel -->
    <div class="col-md pt-3">
      <div class="card shadow-pl">
        <!-- panel header -->
        <div class="card-header bg-transparent pb-0 border-0">
          <daily-working-information-panel-header
            v-bind:headertext1="'年月を指定して集計を表示する'"
            v-bind:headertext2="'雇用形態や所属部署でフィルタリングして表示できます'"
          ></daily-working-information-panel-header>
        </div>
        <!-- /.panel header -->
        <div class="card-body pt-2">
          <!-- panel contents -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-90" id="basic-addon1">指定年月</span>
                </div>
                <input-ym v-on:change-event="fromdateChanges"></input-ym>
              </div>
              <message-data v-bind:messagedatas="messagedatasfromdate"></message-data>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <label class="input-group-text font-size-sm line-height-xs label-width-90" for="inputGroupSelect01">表示区分</label>
                </div>
                <general-list
                  v-bind:identification-id="'C016'"
                  v-bind:placeholder-data="'表示区分を選択してください'"
                  v-bind:blank-data="false"
                  v-on:change-event="displayChange"
                ></general-list>
              </div>
              <message-data v-bind:messagedatas="messagedatadisplay"></message-data>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <label class="input-group-text font-size-sm line-height-xs label-width-90" for="inputGroupSelect01">雇用形態</label>
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
                  <label class="input-group-text font-size-sm line-height-xs label-width-90" for="inputGroupSelect01">所属部署</label>
                </div>
                <select-department
                  ref="selectdepartment"
                  v-bind:blank-data="true"
                  v-on:change-event="departmentChanges"
                ></select-department>
                <message-data v-bind:messagedatas="messagedatadepartment"></message-data>
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <label class="input-group-text font-size-sm line-height-xs label-width-90" for="inputGroupSelect01">氏　　名</label>
                </div>
                <select-user
                  ref="selectuser"
                  v-bind:blank-data="true"
                  v-bind:get-Do="'1'"
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
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <update-workingtimebutton v-on:updateclick-event="updateclick"></update-workingtimebutton>
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
            v-bind:headertext1="stringtext"
            v-bind:headertext2="'虫眼鏡アイコンをクリックするとタイムカードが表示されます'"
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
                <button type="button" class="btn btn-success btn-lg font-size-rg w-100"><img class="icon-size-sm mr-2 pb-1" src="/images/round-get-app-w.svg" alt="">集計結果をCSVファイルに出力する</button>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- /.panel contents -->
        </div>
        <!-- /panel body -->
        <!-- panel body -->
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
        <!-- /panel body -->
        <!-- panel body -->
        <div
          v-for="(calclist,index) in calcresults"
          :key="calclist.user_code"
          class="card-body mb-3 py-0 pt-4 border-top print-space"
        >
          <!-- panel contents -->
          <!-- .row -->
          <div class="row">
            <!-- col -->
            <div class="col-sm-6 col-md-6 col-lg-6 pb-2 align-self-stretch">
              <a
                class="float-left mr-2 px-2 py-2 font-size-rg btn btn-primary btn-lg print-none"
                data-toggle="collapse"
                v-bind:href="'#collapseUser' + index"
                role="button"
                aria-expanded="true"
                v-bind:aria-controls="'collapseUser' + index"
              ><img class="icon-size-rg" src="/images/round-search-w.svg" alt=""></a>
              <h1 class="font-size-sm m-0 mb-1">氏名</h1>
              <p class="font-size-rg font-weight-bold m-0">{{ calclist.user_name }}</p>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-6 col-md-6 col-lg-6 pb-2 align-self-stretch">
              <h1 class="font-size-sm m-0 mb-1 text-sm-right">所属部署</h1>
              <p class="font-size-rg m-0 text-sm-right">{{ calclist.department }}</p>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row mt-2 print-none">
            <!-- col  雇用形態 勤務時間 勤務シフト-->
            <col-employmentstatus
              v-bind:item-name="'雇用形態'"
              v-bind:item-value="calclist.employment"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'勤務時間'"
              v-bind:item-value="calclist.total_working_times"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'所定労働時間'"
              v-bind:item-value="calclist.regular_working_times"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'所定外労働時間'"
              v-bind:item-value="calclist.out_of_regular_working_times"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'残業時間'"
              v-bind:item-value="calclist.overtime_hours"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'深夜残業時間'"
              v-bind:item-value="calclist.late_night_overtime_hours"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'法定労働時間'"
              v-bind:item-value="calclist.legal_working_times"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'法定外労働時間'"
              v-bind:item-value="calclist.out_of_legal_working_times"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'公用外出時間'"
              v-bind:item-value="calclist.not_employment_working_hours"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'私用外出時間'"
              v-bind:item-value="calclist.not_employment_working_hours"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'未就労時間'"
              v-bind:item-value="calclist.not_employment_working_hours"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'法定休日勤務時間'"
              v-bind:item-value="'00:00'"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'法定休日残業時間'"
              v-bind:item-value="'00:00'"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'法定休日深夜残業時間'"
              v-bind:item-value="'00:00'"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'法定外休日勤務時間'"
              v-bind:item-value="'00:00'"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'法定外休日残業時間'"
              v-bind:item-value="'00:00'"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'法定外休日深夜残業時間'"
              v-bind:item-value="'00:00'"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'遅刻日数'"
              v-bind:item-value="calclist.total_working_status + '日'"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'早退日数'"
              v-bind:item-value="calclist.total_go_out + '日'"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'休暇日数'"
              v-bind:item-value="calclist.total_holiday_kubun + '日'"
            ></col-employmentstatus>
            <col-employmentstatus
              v-bind:item-name="'有給休暇日数'"
              v-bind:item-value="'0日'"
            ></col-employmentstatus>
            <!-- /.col -->
          </div>
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
                              <td class="text-center align-middle w-20">日付</td>
                              <td class="text-center align-middle w-20">出勤時間</td>
                              <td class="text-center align-middle w-20">退勤時間</td>
                              <td class="text-center align-middle mw-rem-20">備考</td>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="calclisttimedate in calclist.date">
                              <td class="text-center align-middle">{{ calclisttimedate.workingdate }}</td>
                              <td v-if="calclisttimedate.attendance1 != '00:00' || calclisttimedate.leaving1 != '00:00'" class="text-center align-middle">{{ calclisttimedate.attendance1 }}</td>
                              <td v-if="calclisttimedate.attendance1 != '00:00' || calclisttimedate.leaving1 != '00:00'"  class="text-center align-middle">{{ calclisttimedate.leaving1 }}</td>
                              <td class="text-center align-middle"> </td>
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
        this.messagedatadisplay.push("表示区分は必ず入力してください。");
        this.validate = false;
      }
      if (this.serchorupdate == "update") {
        if (!this.valuedepartment) {
          this.messagedatadepartment.push("所属部署は必ず入力してください。");
          this.validate = false;
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
    // 指定年月が変更された場合の処理
    fromdateChanges: function(value) {
      console.log("fromdateChanges value = " + value);
      this.valueym = value;
      // パネルに表示
      this.setPanelHeader();
      // 再取得
      this.fromdate = ""
      if (this.valuefromdate) {
        this.fromdate = moment(this.valuefromdate).format("YYYYMMDD");
      }
      this.$refs.selectdepartment.getDepartmentList(this.fromdate);
      this.getUserSelected();
    },
    // 表示区分が変更された場合の処理
    displayChange: function(value) {
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
    // 集計開始ボタンがクリックされた場合の処理
    searchclick: function(e) {
      this.serchorupdate = "search";
      this.validate = this.checkForm(e);
      if (this.validate) {
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
            this.calcresults = this.resresults.calcresults;
            this.sumresults = this.resresults.sumresults;
            this.company_name = this.resresults.company_name;
            this.dispmessage(this.resresults.massegedata);
            this.messagedatasserver.length = 0;
            this.$forceUpdate();
            console.log("calcresults" + Object.keys(this.calcresults).length);
            console.log("sumresults" + Object.keys(this.sumresults).length);
            console.log("company_name" + this.company_name);
          })
          .catch(reason => {
            alert("月次集計エラー");
          });
      }
    },
    // 最新更新開始ボタンがクリックされた場合の処理
    updateclick: function(e) {
      this.serchorupdate = "update";
      this.validate = this.checkForm(e);
      if (this.validate) {
        this.itemClear();
        this.$toasted.show("更新を開始しました");
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
            this.calcresults = this.resresults.calcresults;
            this.sumresults = this.resresults.sumresults;
            this.company_name = this.resresults.company_name;
            this.dispmessage(this.resresults.massegedata);
            this.messagedatasserver.length = 0;
            this.$forceUpdate();
            this.$toasted.show("集計を最新に更新しました");
          })
          .catch(reason => {
            alert("月次集計エラー");
          });
      }
    },

    // ----------------- 共通メソッド ----------------------------------
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
      this.getDo = 1;
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

    // 集計パネルヘッダ文字列編集処理
    setPanelHeader: function() {
      console.log("setPanelHeader in "+this.valueym);
      moment.locale("ja");
      if (this.valueym == null || this.valueym == "") {
        this.stringtext = "";
      } else {
        if (this.valuedisplay == null || this.valuedisplay == "") {
          this.stringtext = "";
        } else {
          if (this.valuedisplay == null || this.valuedisplay == "") {
            this.stringtext = "";
          } else {
            this.valuefromdate = this.valueym + '-01';
            if (moment(this.valuefromdate).format("YYYYMM") != moment().format("YYYYMM")) {
              this.valuefromdate = moment(this.valuefromdate).endOf('month').format("YYYYMMDD");
            } else {
              this.valuefromdate = moment().format("YYYYMMDD");
            }
            this.datejaFormat = moment(this.valuefromdate).format("YYYY年MM月");
            if (this.valuedisplay == "1") {
              this.stringtext = "月次集計 " + this.datejaFormat + "を〆日で集計";
            } else {
              if (this.valuedisplay == "2") {
                this.stringtext = "月次集計 " + this.datejaFormat + "1日から月末で集計";
              } else {
                this.stringtext = "";
              }
            }
          }
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
