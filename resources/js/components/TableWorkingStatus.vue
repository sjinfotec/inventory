<template>
  <div>
    <!-- ----------- テーブル部 START ---------------- -->
    <!-- main contentns row -->
    <div class="card-body pt-2" v-if="details.length">
      <!-- .row -->
      <div class="row">
        <div class="col-12">
          <div class="table-responsive">
            <table class="table table-striped border-bottom font-size-sm text-nowrap">
            <!-- <table class="table"> -->
              <thead>
                <tr>
                  <td class="text-center align-middle mw-rem-20">部署</td>
                  <td class="text-center align-middle mw-rem-20">氏名</td>
                  <td class="text-center align-middle mw-rem-02">勤務状況</td>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item,rowIndex1) in details" v-bind:key="item['user_code']">
                  <td class="text-left align-middle mw-rem-20">{{ item['department_name'] }}</td>
                  <td class="text-left align-middle mw-rem-20">{{ item['user_name'] }}</td>
                  <td class="text-left align-middle mw-rem-20">{{ item['mode_name'] }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- /.row -->
    </div>
    <div class="card-body pt-2" v-else>
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
      details: [],
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
      this.details = [];
      var res = response.data;
      if (res.result) {
        this.details = res.details;
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
  height: 360px !important;
}

.table th, .table td {
    padding: 0rem !important;
    border-style: solid dashed !important;
    border-width: 1px !important;
    border-color: #95c5ed #dee2e6 !important;
}

.mw-rem-2-6 {
  min-width: 2.6rem;
}

</style>
