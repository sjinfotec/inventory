<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between print-none">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'期首月または１月（検索区分）から指定年月の間でアラートを表示'"
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
                    <span class="input-group-text font-size-sm line-height-xs label-width-90" id="basic-addon1">指定年月<span class="color-red">[*]</span></span>
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
                    <label class="input-group-text font-size-sm line-height-xs label-width-90" for="inputGroupSelect01">検索区分<span class="color-red">[*]</span></label>
                  </div>
                  <general-list
                    v-bind:identification-id="'C022'"
                    v-bind:placeholder-data="'検索区分を選択してください'"
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
                  <message-data v-bind:message-datas="messagedatadepartment" v-bind:message-class="'warning'"></message-data>
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
                  <message-data v-bind:message-datas="messagedatauser"></message-data>
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
                <btn-work-time v-bind:btn-mode="'search'" v-on:searchclick-event="searchclick"></btn-work-time>
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
              v-bind:header-text2="'各月の時間は残業時間合計（深夜含む）です'"
            ></daily-working-information-panel-header>
          </div>
          <!-- /.panel header -->
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
          <!-- panel contents -->
          <!-- .row -->
          <div
            v-for="(timeitem,index) in timeitems"
            :key="timeitem.user_code"
            class="col-12 p-0"
          >
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
                >
                  <img class="icon-size-rg" src="/images/round-search-w.svg" alt />
                </a>
                <h1 class="font-size-sm m-0 mb-1">氏名</h1>
                <p class="font-size-rg font-weight-bold m-0">{{ timeitem.user_name }}</p>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-sm-6 col-md-6 col-lg-6 pb-2 align-self-stretch">
                <h1 class="font-size-sm m-0 mb-1 text-sm-right">所属部署</h1>
                <p class="font-size-rg m-0 text-sm-right">{{ timeitem.department_name }}</p>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <monthly-working-alert-table
              v-bind:time-items="timeitem"
            ></monthly-working-alert-table>
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
      timeitems: [],
      timevalues: [],
      warningitems: [],
      warningvalues: [],
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
    // 検索開始ボタンがクリックされた場合の処理
    searchclick: function(e) {
      this.serchorupdate = "search";
      this.validate = this.checkForm(e);
      if (this.validate) {
        this.itemClear();
        this.$axios
          .get("/monthly_alert/show", {
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
            if (this.resresults.alert_result == true) {
              if (this.resresults.timeitems != null) {
                this.timeitems = this.resresults.timeitems;
              }
            }
            if (this.resresults.messagedata != null) {
              this.messagedatasserver = this.resresults.messagedata;
            }
            console.log("timeitems" + Object.keys(this.timeitems).length);
            console.log("messagedatasserver" + Object.keys(this.messagedatasserver).length);
            this.$forceUpdate();
          })
          .catch(reason => {
            alert("月次警告通知エラー");
          });
      }
    },

    // ----------------- 共通メソッド ----------------------------------
    // クリアメソッド
    itemClear: function() {
      this.resresults = [];
      this.timeitems = [];
      this.timevalues = [];
      this.warningitems = [];
      this.warningvalues = [];
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
            this.stringtext = this.datejaFormat + "のアラートを締日集計で検索";
          } else {
            if (this.valuedisplay == "2") {
              this.stringtext = this.datejaFormat + "のアラートを月初集計で検索";
            } else {
              if (this.valuedisplay == "3") {
                this.stringtext = this.datejaFormat + "のアラートを締日集計で検索";
              } else {
                if (this.valuedisplay == "4") {
                  this.stringtext = this.datejaFormat + "のアラートを月初集計で検索";
                } else {
                  this.stringtext = "";
                }
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
    }
  }

};
</script>
