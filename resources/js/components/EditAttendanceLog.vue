<template>
  <div>
	  <!-- main contentns row -->
    <div class="row justify-content-between print-none">
      <!-- ========================== 検索部 START ========================== -->
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'日付範囲を指定して勤怠ログの編集を行います'"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- ----------- 選択リスト START ---------------- -->
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >開始日付<span class="color-red">[必須]</span></span>
                  </div>
                  <input-datepicker
                    v-bind:default-date="selectFromdateValue"
                    v-bind:date-format="DatePickerFormat"
                    v-bind:place-holder="'開始日付を選択してください'"
                    v-on:change-event="fromdateChanges"
                    v-on:clear-event="fromdateCleared"
                  ></input-datepicker>
                </div>
                <message-data v-bind:message-datas="messagedatasfromdate" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >終了日付<span class="color-red">[必須]</span></span>
                  </div>
                  <input-datepicker
                    v-bind:default-date="selectTodateValue"
                    v-bind:date-format="DatePickerFormat"
                    v-bind:place-holder="'終了日付を選択してください'"
                    v-on:change-event="fromdateChanges"
                    v-on:clear-event="fromdateCleared"
                  ></input-datepicker>
                </div>
                <message-data v-bind:message-datas="messagedatasfromdate" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >所属部署</span>
                  </div>
                  <select-departmentlist
                    v-if="showdepartmentlist"
                    ref="selectdepartmentlist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'部署を選択してください'"
                    v-bind:selected-department="selectedDepartmentValue"
                    v-bind:add-new="false"
                    v-bind:date-value="''"
                    v-bind:kill-value="valueDepartmentkillcheck"
                    v-bind:row-index="0"
                    v-on:change-event="departmentChanges"
                  ></select-departmentlist>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      for="target_users"
                    >氏 名</label>
                  </div>
                  <select-userlist
                    v-if="showuserlist"
                    ref="selectuserlist"
                    v-bind:blank-data="false"
                    v-bind:placeholder-data="'氏名を選択すると編集モードになります'"
                    v-bind:selected-value="selectedUserValue"
                    v-bind:add-new="true"
                    v-bind:get-do="getDo"
                    v-bind:date-value="applytermdate"
                    v-bind:kill-value="valueUserkillcheck"
                    v-bind:row-index="0"
                    v-bind:department-value="selectedDepartmentValue"
                    v-bind:employment-value="''"
                    v-bind:management-value="'99'"
                    v-on:change-event="userChanges"
                  ></select-userlist>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- 選択リスト END ---------------- -->
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <div class="col-md-6 pb-2">
                <input type="file" class="file_input" name="wmi" @change="onFileChange" accept="text/plain,text/csv" />
              </div>
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
              v-bind:header-text1="stringtext2"
              v-bind:header-text2="'タイムカードを印刷する場合は Ctrl+P で印刷してください。'"
            ></daily-working-information-panel-header>
          </div>
          <!-- /.panel header -->
          <!-- panel body -->
          <div class="card-body mb-3 py-0 pt-4 border-top print-none">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row">
              <div class="col-md-12 pb-2">
                <!-- col -->
                <btn-csv-download
                  v-bind:btn-mode="'csvcalc'"
                  v-bind:csv-data="calcresults"
                  v-bind:is-csvbutton="iscsvbutton"
                  v-bind:csv-date="datejaFormat"
                >
                </btn-csv-download>
                <!-- /.col -->
              </div>
            </div>
            <!-- /.row -->
            <div class="row">
              <div class="col-md-12 pb-2">
                <!-- col -->
                <btn-csv-download
                  v-bind:btn-mode="'csvsalary'"
                  v-bind:csv-data="calcresults"
                  v-bind:is-csvbutton="iscsvbutton"
                  v-bind:csv-date="datejaFormat"
                >
                </btn-csv-download>
                <!-- /.col -->
              </div>
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
                                <td class="text-center align-middle w-15">出勤時刻</td>
                                <td class="text-center align-middle w-15">退勤時刻</td>
                                <td class="text-center align-middle w-15">PC起動時刻</td>
                                <td class="text-center align-middle w-15">PC終了時刻</td>
                                <td class="text-center align-middle mw-rem-20">差異の理由</td>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="calclisttimedate in calclist.date">
                                <td
                                  class="text-center align-middle"
                                >{{ calclisttimedate.workingdatename }}</td>
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
<script language="VBScript">
  Function msgVb()
    Const CONVERT_TO_LOCAL_TIME = True
    Set dtmStartDate = CreateObject("WbemScripting.SWbemDateTime") 
    Set dtmEndDate = CreateObject("WbemScripting.SWbemDateTime") 
    DateToCheck = Date
    dtmStartDate.SetVarDate DateToCheck - 7, CONVERT_TO_LOCAL_TIME
    dtmEndDate.SetVarDate DateToCheck + 1, CONVERT_TO_LOCAL_TIME
    
    strComputer = "."
    Set objWMIService = GetObject("winmgmts:" _ 
        & "{impersonationLevel=impersonate}!\\" & strComputer & "\root\cimv2") 
    Set colEvents = objWMIService.ExecQuery _ 
        ("Select * from Win32_NTLogEvent Where TimeWritten >= '" _ 
            & dtmStartDate & "' and TimeWritten < '" _ 
            & dtmEndDate & "' AND (EventCode = 7001 OR EventCode = 7002)")
    
    Dim msg
    For each objEvent in colEvents

        Set tempDate = CreateObject("WbemScripting.SWbemDateTime") 
        tempDate.Value = objEvent.TimeWritten
    
    If objEvent.EventCode = 7001 then
        msg = msg & "起動 " & tempDate.GetVarDate(True) & vbCrLf
    ElseIf objEvent.EventCode = 7002 then
        msg = msg & "終了 " & tempDate.GetVarDate(True) & vbCrLf
    End If
    Next
    
    Wscript.Echo msg
    msgVb = msg
  End Function
</script>
<script>
import toasted from "vue-toasted";
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "monthlyworkingtime",
  mixins: [ dialogable, checkable, requestable ],
  data: function() {
    return {
      selectFromdateValue: "",
      selectTodateValue: "",
      messagevalidates: [],

      selectedDepartmentValue : "",
      valueDepartmentkillcheck : false,
      showdepartmentlist: true,
      selectedUserValue : "",
      showuserlist: true,
      valueUserkillcheck : false,
      selectedEmploymentValue: "",
      company_name: "",
      selecteTallyvalue: "",
      getDo: 1,
      applytermdate: "",

      valuefromdate: "",
      userrole: "",
      DatePickerFormat: "yyyy年MM月",
      defaultDate: new Date(),
      stringtext: "",
      stringtext2: "",
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
    this.selectFromdateValue = this.defaultDate;
    this.selectTodateValue = this.defaultDate;
    this.selectFromdateValue = this.defaultDate;
    this.getUserRole();
    this.applytermdate = ""
    if (this.valuefromdate) {
      this.applytermdate = moment(this.valuefromdate).format("YYYYMMDD");
    }
    this.$refs.selectdepartmentlist.getList(this.applytermdate);
    this.getUserSelected();
  },
  methods: {
    // バリデーション
    checkForm: function(e) {
      this.messagevalidates = [];
      var chkArray = [];
      var flag = true;
      // 開始日付
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = "開始日付";
      chkArray = this.checkHeader(
        this.selectFromdateValue,
        required,
        equalength,
        maxlength,
        itemname
      );
      if (chkArray.length > 0) {
        if (this.messagevalidates.length == 0) {
          this.messagevalidates = chkArray;
        } else {
          this.messagevalidates = this.messagevalidates.concat(chkArray);
        }
      }
      // 終了日付
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = "終了日付";
      chkArray = this.checkHeader(
        this.selectTodateValue,
        required,
        equalength,
        maxlength,
        itemname
      );
      if (chkArray.length > 0) {
        if (this.messagevalidates.length == 0) {
          this.messagevalidates = chkArray;
        } else {
          this.messagevalidates = this.messagevalidates.concat(chkArray);
        }
      }

      if (this.messagevalidates.length > 0) {
        flag = false;
      }
      return flag;
    },
    // 指定年月が変更された場合の処理
    fromdateChanges: function(value) {
      this.selectFromdateValue = value;
      // パネルに表示
      this.setPanelHeader();
      // 再取得
      this.applytermdate = ""
      if (this.valuefromdate) {
          this.applytermdate = moment(this.valuefromdate).format("YYYYMMDD");
      }
      this.$refs.selectdepartmentlist.getList(this.applytermdate);
      this.getUserSelected();
    },
    // 指定日付がクリアされた場合の処理
    fromdateCleared: function() {
      this.selectFromdateValue = ""
      // パネルに表示
      this.setPanelHeader();
      this.applytermdate = "";
      this.valuefromdate = "";
    },
    // 表示区分が変更された場合の処理
    displayChange: function(value, name) {
      this.selecteTallyvalue = value;
      this.setPanelHeader();
    },
    // 雇用形態が変更された場合の処理
    employmentChanges: function(value) {
      this.selectedEmploymentValue = value;
      this.getDo = 1;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getUserSelected();
    },
    // 部署選択が変更された場合の処理
    departmentChanges: function(value, arrayitem) {
      this.selectedDepartmentValue = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getUserSelected();
    },
    // ユーザー選択が変更された場合の処理
    userChanges: function(value) {
      this.selectedUserValue = value;
    },
    // ファイル選択が変更された場合の処理
    onFileChange: function(e) {
      this.handleFileSelect(e)
    },
    
    // 表示ボタンがクリックされた場合の処理
    searchclick: function(e) {
      execScript("MsgBox('VBscript!')", 'VBScript');
      var os = require('os');
      console.log(os.uptime());
      // // 結果
      // var result = [];
      // try {
      //     // 「SWbemLocator」オブジェクト取得
      //     var locator = new ActiveXObject("WbemScripting.SWbemLocator");
      //     // 「SWbemServices」オブジェクト取得（ローカルコンピュータ、名前空間「root\CIMV2」）
      //     var services = locator.ConnectServer(null, "root\\CIMV2");
      //     // クエリ実行、「SWbemObjectSet」オブジェクト取得
      //     var set = services.ExecQuery("Select * from Win32_NTLogEvent Where EventCode = 7001 OR EventCode = 7002");
      //     // 「SWbemObjectSet」をJScriptで扱えるように「Enumerator」に変換
      //     var enumSet = new Enumerator(set);
      //     // 「SWbemObjectSet」の最後までループ
      //     while (!enumSet.atEnd()) {
      //         // 要素「SWbemObject」を取得
      //         var item = enumSet.item();
      //         // プロパティ「SWbemPropertySet」取得
      //         var props = item.Properties_;
      //         // 「SWbemPropertySet」をJScriptで扱えるように「Enumerator」に変換
      //         var enumProps = new Enumerator(props);
      //         // アイテム変数
      //         var item = {};
      //         // プロパティ分ループ
      //         while (!enumProps.atEnd()) {
      //             var val = null;
      //             // プロパティ取得
      //             var prop = enumProps.item();
      //             // nullチェック
      //             if (prop.Value != null) {
      //                 // 配列判定
      //                 if (prop.IsArray) {
      //                     // 配列化
      //                     val = new VBArray(prop.Value).toArray();
      //                 } else {
      //                     val = prop.Value;
      //                 }
      //             }
      //             // アイテム設定
      //             item[prop.Name] = val;
      //             // 次のプロパティへ移動
      //             enumProps.moveNext();
      //         }
      //         // アイテム設定
      //         result.push(item);
      //         // 次の要素へ移動
      //         enumSet.moveNext();
      //     }
      // } catch (e) {
      //     // エラーの場合
      //     throw e;
      // }
      // for (var $i=0;$i<result.length;$i++) {
      //   console.log(" windows log = " + result[$i]);
      // }
    },
    // 表示ボタンがクリックされた場合の処理
    tmp_searchclick: function(e) {
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
              displaykbn: this.selecteTallyvalue,
              employmentstatus: this.selectedEmploymentValue,
              departmentcode: this.selectedDepartmentValue,
              usercode: this.selectedUserValue
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
              displaykbn: this.selecteTallyvalue,
              employmentstatus: this.selectedEmploymentValue,
              departmentcode: this.selectedDepartmentValue,
              usercode: this.selectedUserValue
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
    // ------------------------ サーバー処理 ----------------------------
    // ログインユーザーの権限を取得
    getUserRole: function() {
      var arrayParams = [];
      this.postRequest("/get_login_user_role", arrayParams)
        .then(response  => {
          this.getThenrole(response);
        })
        .catch(reason => {
          this.serverCatch("ユーザー権限", "取得");
        });
    },

    // ----------------- 共通メソッド ----------------------------------
    // イベントログファイル操作
    handleFileSelect: function(e) {
      console.log( e.target.files[0] );
      var file_data = e.target.files[0];
      // 読み込み
      var reader = new FileReader();
      // 読み込んだファイルの中身を取得する
      reader.readAsText( file_data );
      //ファイルの中身を取得後に処理を行う
      reader.addEventListener( 'load', function() {
        //CSVの各データ毎に読み込む
        console.log( reader.result.split('\r\n') );
      });
    },
    // ユーザー選択コンポーネント取得メソッド
    getUserSelected: function() {
      // 再取得
      this.applytermdate = ""
      if (this.valuefromdate) {
          this.applytermdate = moment(this.valuefromdate).format("YYYYMMDD");
      }
      this.$refs.selectuserlist.getList(
        this.applytermdate,
        this.valueUserkillcheck,
        this.getDo,
        this.selectedDepartmentValue,
        this.selectedEmploymentValue
      );
    },
    // 取得正常処理（ユーザー権限）
    getThenrole(response) {
      var res = response.data;
      if (res.result) {
        this.userrole = res.role;
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("エラー", res.messagedata, "error", true, false, true);
        } else {
          this.serverCatch("ユーザー権限", "取得");
        }
      }
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + "情報" + eventtext + "に失敗しました");
      this.messageswal("エラー", messages, "error", true, false, true);
    },
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
    // 集計パネルヘッダ文字列編集処理
    setPanelHeader: function() {
      moment.locale("ja");
      if (this.selectFromdateValue == null || this.selectFromdateValue == "") {
        this.stringtext = "";
      } else {
        this.valuefromdate = this.selectFromdateValue;
        if (this.selecteTallyvalue == null || this.selecteTallyvalue == "") {
          this.stringtext = "";
        } else {
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
          if (this.selecteTallyvalue == "1") {
            this.stringtext =
              "月次集計 " + this.datejaFormat + "分を〆日で集計";
          } else {
            if (this.selecteTallyvalue == "2") {
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
