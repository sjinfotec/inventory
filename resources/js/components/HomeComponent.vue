<template>
  <div class="col-xl-12">
    <!-- main contentns row -->
    <div class="d-flex flex-row flex-wrap align-content-between">
      <!-- 日次集計 -->
      <div class="p-4" v-if="menudatas[get_c038[1]['sort_seq'] - 1]['is_select']">
        <a class href="/daily">
          <img width="100" height="100" class src="/images/icon02.svg" alt />
        </a>
      </div>
      <!-- 月次集計 -->
      <div class="p-4" v-if="menudatas[get_c038[2]['sort_seq'] - 1]['is_select']">
        <a class href="/monthly">
          <img width="100" height="100" class src="/images/icon01.svg" alt />
        </a>
      </div>
      <!-- 日次警告 -->
      <div class="p-4" v-if="menudatas[get_c038[4]['sort_seq'] - 1]['is_select']">
        <a class href="/daily_alert">
          <img width="100" height="100" class src="/images/icon04.svg" alt />
        </a>
      </div>
      <!-- 月次警告 -->
      <div class="p-4" v-if="menudatas[get_c038[5]['sort_seq'] - 1]['is_select']">
        <a class href="/monthly_alert">
          <img width="100" height="100" class src="/images/icon03.svg" alt />
        </a>
      </div>
      <!-- 勤怠履歴登録 -->
      <div class="p-4" v-if="menudatas[get_c038[7]['sort_seq'] - 1]['is_select']">
        <a class href="/store_attendancelog">
          <img width="100" height="100" class src="/images/icon19.svg" alt />
        </a>
      </div>
      <!-- 勤怠履歴編集 -->
      <div class="p-4" v-if="menudatas[get_c038[8]['sort_seq'] - 1]['is_select']">
        <a class href="/edit_attendancelog">
          <img width="100" height="100" class src="/images/icon10.svg" alt />
        </a>
      </div>
      <!-- シフト編集 -->
      <div
        class="p-4"
        v-if="menudatas[get_c038[10]['sort_seq'] - 1]['is_select'] && login_user_role === get_c025[2]['code']"
      >
        <a class href="/setting_shift_time">
          <img width="100" height="100" class src="/images/icon08.svg" alt />
        </a>
      </div>
      <!-- 勤怠編集 -->
      <div
        class="p-4"
        v-if="menudatas[get_c038[11]['sort_seq'] - 1]['is_select'] && login_user_role === get_c025[2]['code']"
      >
        <a class href="/edit_work_times">
          <img width="100" height="100" class src="/images/icon09.svg" alt />
        </a>
      </div>
      <!-- 各種申請作成 -->
      <div class="p-4" v-if="menudatas[get_c038[13]['sort_seq'] - 1]['is_select']">
        <a class href="/demand">
          <img width="100" height="100" class src="/images/icon05.svg" alt />
        </a>
      </div>
      <!-- 各種申請承認 -->
      <div
        class="p-4"
        v-if="menudatas[get_c038[14]['sort_seq'] - 1]['is_select'] && login_user_role >= get_c025[1]['code']"
      >
        <a class href="/approval">
          <img width="100" height="100" class src="/images/icon06.svg" alt />
        </a>
      </div>
      <!-- 承認者ルート設定 -->
      <div
        class="p-4"
        v-if="menudatas[get_c038[15]['sort_seq'] - 1]['is_select'] && login_user_role >= get_c025[1]['code']"
      >
        <a class href="/confirm">
          <img width="100" height="100" class src="/images/icon07.svg" alt />
        </a>
      </div>
      <!-- 会社設定 -->
      <div
        class="p-4"
        v-if="menudatas[get_c038[17]['sort_seq'] - 1]['is_select'] && login_user_role === get_c025[2]['code']"
      >
        <a class href="/create_company_information">
          <img width="100" height="100" class src="/images/icon13.svg" alt />
        </a>
      </div>
      <!-- 組織設定 -->
      <div
        class="p-4"
        v-if="menudatas[get_c038[18]['sort_seq'] - 1]['is_select'] && login_user_role === get_c025[2]['code']"
      >
        <a class href="/create_department">
          <img width="100" height="100" class src="/images/icon14.svg" alt />
        </a>
      </div>
      <!-- 労働時間基本設定 -->
      <div
        class="p-4"
        v-if="menudatas[get_c038[19]['sort_seq'] - 1]['is_select'] && login_user_role === get_c025[2]['code']"
      >
        <a class href="/setting_calc">
          <img width="100" height="100" class src="/images/icon15.svg" alt />
        </a>
      </div>
      <!-- 勤務帯時間設定 -->
      <div
        class="p-4"
        v-if="menudatas[get_c038[20]['sort_seq'] - 1]['is_select'] && login_user_role === get_c025[2]['code']"
      >
        <a class href="/create_time_table">
          <img width="100" height="100" class src="/images/icon16.svg" alt />
        </a>
      </div>
      <!-- カレンダー設定 -->
      <div
        class="p-4"
        v-if="menudatas[get_c038[21]['sort_seq'] - 1]['is_select'] && login_user_role === get_c025[2]['code']"
      >
        <a class href="/setting_calendar">
          <img width="100" height="100" class src="/images/icon17.svg" alt />
        </a>
      </div>
      <!-- ユーザー情報設定 -->
      <div
        class="p-4"
        v-if="menudatas[get_c038[22]['sort_seq'] - 1]['is_select'] && login_user_role === get_c025[2]['code']"
      >
        <a class href="/edit_user">
          <img width="100" height="100" class src="/images/icon18.svg" alt />
        </a>
      </div>
      <!-- パスワード変更 -->
      <div class="p-4" v-if="menudatas[get_c038[24]['sort_seq'] - 1]['is_select'] ">
        <a class href="/user_pass">
          <img width="100" height="100" class src="/images/icon11.svg" alt />
        </a>
      </div>
      <!-- ダウンロード -->
      <div class="p-4" v-if="menudatas[get_c038[25]['sort_seq'] - 1]['is_select'] ">
        <a class href="/file_download">
          <img width="100" height="100" class src="/images/icon12.svg" alt />
        </a>
      </div>
    </div>
    <div class="d-flex align-content-between">
      <div class="card w-50">
        <div class="card-header bg-color alert-head">
          <!-- <img class="icon-size-sm svg_img orange600" src="/images/info-32.png" alt />打刻エラー -->
          <!-- <i class="fa fa-exclamation-triangle my-red fa-lg fa-fw" aria-hidden="true"></i> -->
          <!-- <img class="con-size-lg svg_img" src="/images/round-info-b.svg" alt /> -->
          <i class="fa fa-exclamation-circle fa-lg my-orange fa-fw" aria-hidden="true"></i>
          <span class="font-weight-bolder">【通知事項】</span>
        </div>
        <div class="card-body">
          <!-- ----------- waitメッセージ部 START ---------------- -->
          <!-- .row -->
          <message-waiting v-bind:is-message-show="messageshowsearch"></message-waiting>
          <!-- /.row -->
          <!-- ----------- waitメッセージ部 END ---------------- -->
          <div class="row justify-content-between print-none" v-if="infomationmessage.length">
            <!-- col -->
            <!-- <div class="col-md-12"> -->
            <div v-if="login_user_role === get_c025[2]['code']">
              <a
                class
                href="/daily_alert/home"
                v-for="(messagevalidate,index) in infomationmessage"
                v-bind:key="index"
              >{{ messagevalidate }}</a>
            </div>
            <div v-else>
              <span style="color: #808080;">通知事項はありません</span>
            </div>
            <!-- </div> -->
            <!-- /.col -->
          </div>
          <div class="row justify-content-between print-none" v-else>
            <!-- col -->
            <span style="color: #808080;">通知事項はありません</span>
            <!-- /.col -->
          </div>
        </div>
      </div>
      <div class="card w-50 ml-2">
        <div class="card-header bg-color alert-head">
          <i class="fa fa-bullhorn fa-lg my-orange fa-fw" aria-hidden="true"></i>
          <span class="font-weight-bolder">お知らせ</span>
        </div>
        <div class="card-body">
          <div v-if="informations" v-for="(item,index) in informations">
            <span v-html="item.content"></span>
          </div>
        </div>
      </div>
    </div>
    <!-- ========================== 勤務状況部 START ========================== -->
    <div class="d-flex align-content-between mt-3">
      <div class="card w-100">
        <div class="card-header bg-color alert-head">
          <i class="fa fa-user-circle-o fa-lg my-orange fa-fw" aria-hidden="true"></i>
          <span class="font-weight-bolder">出勤状況確認</span>
        </div>
        <table-working-status
          v-if="showeditworktimestable"
          ref="refeditworktimestable"
          v-bind:target-date="''"
          v-bind:login-user="authusers['code']"
          v-bind:login-role="authusers['role']"
          v-bind:account-data="accountdatas['account_id']"
          v-bind:menu-data="menudatas"
        ></table-working-status>
      </div>
      <!-- /.panel -->
    </div>
    <!-- ========================== 勤務状況部 END =========================== -->

    <!-- <div class="card flex-fill margin-left-small">
        <div class="card-header bg-color">
          <i class="fa fa-bullhorn fa-lg my-orange fa-fw" aria-hidden="true"></i>
          <span class="font-weight-bolder">お知らせ</span>
        </div>
        <div class="card-body">
          <div v-if="informations" v-for="(item,index) in informations">
            <span v-html="item.content"></span>
          </div>
          <div class="float-right">
            <button class="btn btn-primary" @click="makeInformation()">作成</button>
          </div>
        </div>
    </div>-->
    <!-- </div> -->
    <!-- <el-dialog custom-class v-bind:title="'三条印刷からのお知らせ'" :visible.sync="dialogVisible" width="80%">
      <div class="card">
        <div class="card-header">
          <i class="fa fa-edit my-orange fa-lg fa-fw" aria-hidden="true"></i>お知らせ入力
        </div>
        <div class="card-body">
          <textarea
            id="textContents"
            type="text"
            name="content"
            v-model="content"
            cols="50"
            rows="10"
            wrap="hard"
            placeholder="html形式でお知らせ内容を入力してください"
            class="form-control"
          ></textarea>
          <div>
            <table class="table">
              <thead>
                <tr>
                  <th>内容</th>
                  <th>日時</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in informations">
                  <td v-html="row.content"></td>
                  <td>{{row.created_at}}</td>
                  <td>
                    <el-button type="danger" @click="delPostInformations(row.id)">削除</el-button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="card-footer text-align-right padding-dis">
            <el-button type="danger" @click="dialogVisible = false">閉じる</el-button>
    <!-- 下記ボタンは三条印刷ユーザーのみ表示-->
    <!-- <el-button type="primary" @click="postInformation()">作成する</el-button>
          </div>
        </div>
    </div>-->
    <!-- </el-dialog> -->
  </div>
</template>
<script>
import moment from "moment";
import { dialogable } from "../mixins/dialogable.js";
import { checkable } from "../mixins/checkable.js";
import { requestable } from "../mixins/requestable.js";

// CONST
const CONST_C025 = "C025";
const CONST_C038 = "C038";

export default {
  name: "Home",
  mixins: [dialogable, checkable, requestable],
  props: {
    authusers: {
      type: Array,
      default: []
    },
    accountdatas: {
      type: Array,
      default: []
    },
    menudatas: {
      type: Array,
      default: []
    },
    const_generaldatas: {
      type: Array,
      default: []
    }
  },
  data() {
    return {
      details: [],
      informations: [],
      content: "",
      login_user_code: 0,
      login_user_role: 0,
      dialogVisible: false,
      messageshowsearch: false,
      infomationmessage: [],
      showeditworktimestable: true,
      const_C025_data: [],
      const_C038_data: []
    };
  },
  computed: {
    get_c025: function() {
      var i = 0;
      let $this = this;
      this.const_generaldatas.forEach(function(item) {
        if (item.identification_id == CONST_C025) {
          $this.const_C025_data.push($this.const_generaldatas[i]);
        }
        i++;
      });
      return this.const_C025_data;
    },
    get_c038: function() {
      var i = 0;
      let $this = this;
      this.const_generaldatas.forEach(function(item) {
        if (item.identification_id == CONST_C038) {
          $this.const_C038_data.push($this.const_generaldatas[i]);
        }
        i++;
      });
      return this.const_C038_data;
    }
  },
  // マウント時
  mounted() {
    this.login_user_code = this.authusers["code"];
    this.login_user_role = this.authusers["role"];
    this.getDayAlert();
    this.getPostInformations();
  },
  methods: {
    // ------------------------ サーバー処理 ----------------------------
    // 日次警告取得処理
    getDayAlert() {
      // 処理中メッセージ表示
      this.infomationmessage = [];
      this.messageshowsearch = true;
      var arrayParams = {
        alert_form_date: moment(new Date()).format("YYYYMMDD"),
        employmentstatus: null,
        departmentcode: null,
        usercode: null
      };
      this.postRequest("/daily_alert/show", arrayParams)
        .then(response => {
          this.getThen(response);
        })
        .catch(reason => {
          this.$swal.close();
          this.serverCatch("日次警告", "取得");
        });
      this.messageshowsearch = false;
    },
    // お知らせ取得
    getPostInformations() {
      this.$axios
        .get("/get_post_informations")
        .then(response => {
          this.informations = response.data;
        })
        .catch(reason => {
          console.log(reason);
          alert("error");
        });
    },
    // お知らせ登録
    postInformation() {
      axios
        .post("/insert_post_informations", {
          content: this.content
        })
        .then(res => {
          this.dialogVisible = false;
          this.$toasted.show("お知らせ登録しました。");
          this.getPostInformations();
        })
        .catch(err => {
          //例外処理を行う
          console.log(err);
          this.dialogVisible = false;
        });
    },
    // お知らせ削除
    delPostInformations(id) {
      axios
        .post("/del_post_informations", {
          id: id
        })
        .then(res => {
          this.$toasted.show("削除しました。");
          this.getPostInformations();
        })
        .catch(err => {
          //例外処理を行う
          console.log(err);
          this.dialogVisible = false;
        });
    },
    // -------------------- 共通 ----------------------------
    // 取得正常処理（アラート）
    getThen(response) {
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        if (this.details.length > 0) {
          this.infomationmessage.push(
            "直近1週間に打刻警告が" + this.details.length + "件あります"
          );
        }
        this.dateName = res.datename;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("日次警告", "取得");
        }
      }
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + "情報" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    makeInformation() {
      this.dialogVisible = true;
    },
    hide: function() {
      this.$modal.hide("hello-world");
    },
    show: function() {
      this.$modal.show("hello-world");
    }
  }
};
</script>
<style scoped>
.font-color-black {
  color: black;
}
.margin-top-regular {
  margin-top: 30px;
}
.margin-left-small {
  margin-left: 15px;
}
.my-red {
  color: red;
}
.my-skyblue {
  color: skyblue;
}
.my-orange {
  color: #fecb81;
}
.bg-color {
  background-color: floralwhite;
}
.padding-dis {
  padding: 0.75rem 0rem !important;
}
.alert-head {
  background-color: #0d3867;
  color: white;
}
</style>
