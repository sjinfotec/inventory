<template>
  <div class="card">
    <div class="card-header">カレンダー初期設定</div>
    <div class="card-body">
      <!-- .row -->
      <div class="row justify-content-between">
        <!-- .panel -->
        <div class="col-md pt-3">
          <div class="card shadow-pl">
            <!-- panel header -->
            <div class="card-header bg-transparent pb-0 border-0">
              <daily-working-information-panel-header
                v-bind:header-text1="'１年のカレンダーを初期設定します。'"
                v-bind:header-text2="'期首月からまたは１月から１年分を設定します。'"
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
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-90"
                        id="basic-addon1"
                      >指定年<span class="color-red">＊</span></span>
                    </div>
                    <input-datepicker
                      v-bind:default-date="defaultDate"
                      v-bind:date-format="'yyyy年'"
                      v-on:change-event="fromdateChanges"
                    ></input-datepicker>
                  </div>
                  <message-data v-bind:message-datas="messagedatasfromdate" v-bind:message-class="'warning'"></message-data>
                </div>
                <!-- .col -->
                <div class="col-md-6 pb-2">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <label
                        class="input-group-text font-size-sm line-height-xs label-width-90"
                        for="inputGroupSelect01"
                      >設定区分<span class="color-red">＊</span></label>
                    </div>
                    <general-list
                      v-bind:identification-id="'C024'"
                      v-bind:placeholder-data="'設定区分を選択してください'"
                      v-bind:blank-data="false"
                      v-on:change-event="displayChange"
                    ></general-list>
                  </div>
                  <message-data v-bind:message-datas="messagedatadisplay" v-bind:message-class="'warning'"></message-data>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <div class="col-md-12 pb-2">
                <div class="btn-group d-flex" v-on:click="initclick">
                  <button type="button" class="btn btn-outline-primary btn-lg font-size-rg w-100"><img class="icon-size-sm mr-2 pb-1" src="/images/round-notifications-b.svg" alt="">
                    初期設定する</button>
                </div>
              </div>
              <!-- .row -->
              <!-- /.row -->
              <div class="col-md-12 pb-2">
                <div class="btn-group d-flex" v-on:click="backclick">
                  <button type="button" class="btn btn-outline-secondary btn-lg font-size-rg w-100"><img class="icon-size-sm mr-2 pb-1" src="/images/round-outlined-flag-w.svg" alt="">戻る</button>
                </div>
              </div>
              <!-- .row -->
            </div>
          </div>
          <!-- /.panel contents -->
        </div>
      </div>
      <div class="row justify-content-between">
        <!-- .panel -->
        <div class="col-md pt-3 align-self-stretch">
          <div class="card shadow-pl">
            <!-- panel header -->
            <div class="card-header bg-transparent pt-3 border-0 print-none">
              <daily-working-information-panel-header
                v-bind:header-text1="stringtext"
                v-bind:header-text2="''"
              ></daily-working-information-panel-header>
              <message-waiting v-bind:is-message-show="ismessageshowsearch"></message-waiting>
            </div>
            <!-- /.row -->
          </div>
          <!-- /.panel contents -->
        </div>
      </div>
    </div>
    <!-- /.panel -->
  </div>
  <!-- /main contentns row -->
</template>
<script>
import toasted from "vue-toasted";
import moment from "moment";

export default {
  name: "InitCalendar",
  data() {
    return {
      fromdate: "",
      valueym: "",
      valuey: "",
      valuefromdate: "",
      defaultDate: new Date(),
      valuedisplay: "",
      stringtext: "",
      datejaFormat: "",
      ismessageshowsearch: false,
      messagedatasserver: [],
      messagedatasfromdate: [],
      messagedatadisplay: [],
      resresults: [],
      validate: false
    };
  },
  methods: {
    // バリデーション
    checkForm: function(e) {
      this.validate = true;
      this.messagedatasserver = [];
      this.messagedatasfromdate = [];
      this.messagedatadisplay = [];
      if (!this.valueym) {
        this.messagedatasfromdate.push("指定年は必ず入力してください。");
        this.validate = false;
      }
      if (!this.valuedisplay) {
        this.messagedatadisplay.push("設定区分は必ず入力してください。");
        this.validate = false;
      }

      if (this.validate) {
        return this.validate;
      }

      e.preventDefault();
    },
    // 指定年月が変更された場合の処理
    fromdateChanges: function(value) {
      this.valueym = value;
      // パネルに表示
      this.setPanelHeader();
      // 再取得
      this.fromdate = "";
      if (this.valuefromdate) {
        this.fromdate = moment(this.valuefromdate).format("YYYYMMDD");
      }
    },
    // 設定区分が変更された場合の処理
    displayChange: function(value) {
      this.valuedisplay = value;
      this.setPanelHeader();
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
          this.datejaFormat = moment(this.valuefromdate).format("YYYY年");
          if (this.valuedisplay == "1") {
            this.stringtext =
              this.datejaFormat + "のカレンダーを期首月から１年分初期設定";
          } else {
            if (this.valuedisplay == "2") {
              this.stringtext =
                this.datejaFormat + "のカレンダーを1月から１年分初期設定";
            } else {
              this.stringtext = "";
            }
          }
        }
      }
    },
    // 設定ボタンがクリックされた場合の処理
    initclick: function(e) {
      this.$swal({
        title: "確認",
        text: "指定年に登録しているデータは消えますが、初期設定してもよろしいですか？",
        icon: 'info',
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.initProc(e);
        } else {
        }
      });
    },
    // 戻るボタンがクリックされた場合の処理
    backclick: function(e) {
      console.log("init backclickBtn click");
      this.$emit('backclick-event',event);
    },
    // 設定ボタンがクリックされた場合の処理
    initProc: function(e) {
      this.validate = this.checkForm(e);
      this.valuey = moment(this.valuefromdate).format("YYYY");
      if (this.validate) {
        this.ismessageshowsearch = true;
        this.$axios
          .post("/create_calendar/init", {
            datefrom: this.valuey,
            displaykbn: this.valuedisplay
          })
          .then(response => {
            this.resresults = response.data;
            if (this.resresults.result == true) {
              this.$toasted.show("カレンダー初期設定しました。");
            } else {
              this.$toasted.show("カレンダー初期設定に失敗しました。");
            }
            if (this.resresults.messagedata != null) {
              this.messagedatasserver = this.resresults.messagedata;
            }
            this.ismessageshowsearch = false;
            this.$forceUpdate();
          })
          .catch(reason => {
            this.ismessageshowsearch = false;
            this.alert("error", "カレンダー初期設定に失敗しました", "エラー");
          });
      }
    }
  }
};
</script>
