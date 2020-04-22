<template>
  <div>
    <!-- ----------- テーブル部 START ---------------- -->
    <!-- main contentns row -->
    <div class="card-body" v-if="ondetails_length > 0 || offdetails_length > 0">
      <!-- .row -->
      <div class="row">
        <div class="col-6" v-if="ondetails_length">
          <p>在席者数：{{ ondetails_length}}名</p>
          <div class="table-responsive">
            <table class="table table-striped border-bottom font-size-sm text-nowrap">
            <!-- <table class="table"> -->
              <thead>
                <tr>
                  <td class="text-center align-middle mw-rem-10">部署</td>
                  <td class="text-center align-middle mw-rem-10">氏名</td>
                  <td class="text-center align-middle mw-rem-8">打刻時刻</td>
                  <td class="text-center align-middle mw-rem-5">モード</td>
                  <td class="text-center align-middle mw-rem-10">勤務状況</td>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item,onrowIndex) in ondetails" v-bind:key="item['user_code']">
                  <td class="text-left align-middle mw-rem-10">{{ item['department_name'] }}</td>
                  <td class="text-left align-middle mw-rem-10">{{ item['user_name'] }}</td>
                  <td class="text-center align-middle mw-rem-8">{{ item['record_time_name'] }}</td>
                  <td class="text-center align-middle mw-rem-5">{{ item['mode_name'] }}</td>
                  <td
                    class="text-center align-middle mw-rem-10"
                    v-if="item['holiday_kubun_name']"
                  >{{ item['holiday_kubun_name'] }}</td>
                  <td
                    class="text-center align-middle mw-rem-10"
                    v-else
                  >{{ item['mode_sub_name'] }}</td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-6" v-if="offdetails_length">
          <p>離席者数：{{ offdetails_length}}名</p>
          <div class="table-responsive">
            <table class="table table-striped border-bottom font-size-sm text-nowrap">
            <!-- <table class="table"> -->
              <thead>
                <tr>
                  <td class="text-center align-middle mw-rem-10">部署</td>
                  <td class="text-center align-middle mw-rem-10">氏名</td>
                  <td class="text-center align-middle mw-rem-8">打刻時刻</td>
                  <td class="text-center align-middle mw-rem-5">モード</td>
                  <td class="text-center align-middle mw-rem-10">勤務状況</td>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item,offrowIndex) in offdetails" v-bind:key="item['user_code']">
                  <td class="text-left align-middle mw-rem-10">{{ item['department_name'] }}</td>
                  <td class="text-left align-middle mw-rem-10">{{ item['user_name'] }}</td>
                  <td class="text-center align-middle mw-rem-8">{{ item['record_time_name'] }}</td>
                  <td class="text-center align-middle mw-rem-5">{{ item['mode_name'] }}</td>
                  <td
                    class="text-center align-middle mw-rem-10"
                    v-if="item['holiday_kubun_name']"
                  >{{ item['holiday_kubun_name'] }}</td>
                  <td
                    class="text-center align-middle mw-rem-10"
                    v-else
                  >{{ item['mode_sub_name'] }}</td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- /.row -->
    </div>
    <div class="card-body" v-else>
      <!-- .row -->
      <div class="row">
        <div class="col-12">
          <div class="table-responsive">
              本日の出勤者はいません
          </div>
        </div>
      </div>
      <!-- /.row -->
    </div>
    <!-- ----------- テーブル部 END ---------------- -->
  </div>
</template>
<script>
import moment from "moment";
import { dialogable } from "../mixins/dialogable.js";
import { checkable } from "../mixins/checkable.js";
import { requestable } from "../mixins/requestable.js";

export default {
  name: "TableWorkingStatus",
  mixins: [dialogable, checkable, requestable],
  props: {
    targetDate: {
      type: String,
      default: ""
    }
  },
  data() {
    return {
      ondetails: [],
      offdetails: [],
      ondetails_length: 0,
      offdetails_length: 0,
      defaultYmd: new Date()
    };
  },
  // マウント時
  mounted() {
    if (this.targetDate == null || this.targetDate == "") {
      this.targetYmd = moment(this.defaultYmd).format("YYYYMMDD");
    } else {
      this.targetYmd = moment(this.targetDate).format("YYYYMMDD");
    }
    this.getItem();
  },
  methods: {
    // ------------------------ サーバー処理 ------------------------------------
    // 
    getItem() {
      var arrayParams = {
        target_date: this.targetYmd
      };
      this.postRequest("/get_working_status/get", arrayParams)
        .then(response => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("勤務状況", "取得");
        });
    },
    // ------------------------ 共通処理 ------------------------------------
    // 取得正常処理（ユーザー）
    getThen(response) {
      this.ondetails = [];
      this.offdetails = [];
      var res = response.data;
      if (res.result) {
        this.ondetails = res.ondetails;
        this.offdetails = res.offdetails;
        this.ondetails_length = Object.keys(this.ondetails).length;
        this.offdetails_length = Object.keys(this.offdetails).length;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("氏名", "取得");
        }
      }
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + "情報" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    }
  }
};

</script>
<style scoped>

thead, tbody {
  display: block !important;
}

tbody {
  overflow-x: hidden !important;
  overflow-y: scroll !important;
  height: 300px !important;
}

.table th, .table td {
    padding: 0rem !important;
    border-style: solid dashed !important;
    border-width: 1px !important;
    border-color: #95c5ed #dee2e6 !important;
}

.mw-rem-8 {
  min-width: 8rem;
}

</style>
