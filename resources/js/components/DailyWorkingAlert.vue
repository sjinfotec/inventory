<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:headertext1="'指定日付から過去１週間以内の警告を表示'"
            v-bind:headertext2="'雇用形態や所属部署でフィルタリングして表示できます'"
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
                      for="target_fromdate"
                    >指定日付<span class="color-red">＊</span></span>
                  </div>
                  <input-datepicker
                    v-bind:default-Date="defaultDate"
                    v-on:change-event="fromdateChanges"
                  ></input-datepicker>
                  <message-data
                    v-bind:message-datas="messagedatasfromdate"
                    v-bind:message-class="'warning'">
                  </message-data>
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
                  <message-data v-bind:message-datas="messagedatadepartment"></message-data>
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
                  <message-data v-bind:message-datas="messagedatauser"></message-data>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <message-data-server v-bind:message-datas="messagedatasserver" v-bind:message-class="'warning'"></message-data-server>
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
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3 align-self-stretch">
        <div class="card shadow-pl">
          <!-- panel body -->
          <daily-working-alert-table
            v-bind:alert-lists="alertresults"
          ></daily-working-alert-table>
          <!-- /panel body -->
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
  name: "dailyworkingalert",
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
      datejaFormat: "",
      hrefindex: "",
      resresults: [],
      alertresults: [],
      messagedatasserver: [],
      messagedatasfromdate: [],
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
      console.log("this.valuefromdate" + this.valuefromdate);
      console.log("this.valueemploymentstatus" + this.valueemploymentstatus);
      console.log("this.valuedepartment" + this.valuedepartment);
      console.log("this.valueuser" + this.valueuser);
      if (this.validate) {
        this.itemClear();
        this.$axios
          .get("/daily_alert/show", {
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
            if (this.resresults.alertresults != null) {
              this.alertresults = this.resresults.alertresults;
            }
            if (this.resresults.messagedata != null) {
              this.messagedatasserver = this.resresults.messagedata;
            }
            console.log("alertresults" + Object.keys(this.alertresults).length);
            console.log("messagedatasserver" + Object.keys(this.messagedatasserver).length);
            this.$forceUpdate();
          })
          .catch(reason => {
            alert("error");
          });
      }
    },

    // ----------------- 共通メソッド ----------------------------------

    // ユーザー選択コンポーネント取得メソッド
    getUserSelected: function() {
      this.fromdate = "";
      this.valueuser = "";
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
    // クリアメソッド
    itemClear: function() {
      this.resresults = [];
      this.alertresults = [];
      this.messagedatasserver = [];
      this.messagedatasfromdate = [];
      this.messagedatastodate = [];
      this.messagedatadepartment = [];
      this.messagedatauser = [];
    }
  }
};
</script>
