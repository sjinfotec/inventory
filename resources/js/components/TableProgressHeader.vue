<template>
  <div>
    <!-- ----------- テーブル部 START ---------------- -->
    <!-- main contentns row -->
    <div class="card-body" v-if="details_length > 0">
      <!-- .row -->
      <div class="row">
        <div class="card w-100" v-if="details_length">
          <div class="card-body table-responsive bg-color-exists">
            <table class="table table-striped border-bottom font-size-sm text-nowrap">
              <!-- <table class="table"> -->
              <thead class="thead-dark">
                <tr>
                  <td class="text-center align-middle mw-rem-8">納期</td>
                  <td class="text-center align-middle mw-rem-20">客先</td>
                  <td class="text-center align-middle mw-rem-20">受注番号</td>
                  <td class="text-center align-middle mw-rem-10">図面番号</td>
                  <td class="text-center align-middle mw-rem-10">個数</td>
                  <td class="text-center align-middle mw-rem-10">型式／型番</td>
                  <td class="text-center align-middle mw-rem-10">品名</td>
                  <td colspan="2" class="text-center align-middle mw-rem-10">操作</td>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item,rowIndex) in details" :key="rowIndex">
                  <td class="text-left align-middle mw-rem-8">{{ item['supply_date_name'] }}</td>
                  <td class="text-left align-middle mw-rem-20">{{ item['customer_name'] }}</td>
                  <td class="text-left align-middle mw-rem-20">{{ item['order_no'] }}</td>
                  <td class="text-left align-middle mw-rem-10">{{ item['drawing_no'] }}</td>
                  <td class="text-left align-middle mw-rem-10">{{ item['order_count'] }}</td>
                  <td class="text-left align-middle mw-rem-10">{{ item['model_number'] }}</td>
                  <td class="text-left align-middle mw-rem-10">{{ item['product_name'] }}</td>
                  <td class="text-center align-middle mw-rem-10">
                    <a :href="'/edit_work_order/home'" class="btn btn-primary">編集</a>
                  </td>
                  <td class="text-center align-middle mw-rem-5">
                    <button type="button" class="btn btn-danger mb-1" @click="delClick(rowIndex)">
                      削除
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div v-else class="card w-100">
          <div class="card-body table-responsive bg-color-exists">
            <table class="table table-striped border-bottom font-size-sm text-nowrap">
              <!-- <table class="table"> -->
              <thead class="thead-dark">
                <tr>
                  <td class="text-center align-middle mw-rem-8">納期</td>
                  <td class="text-center align-middle mw-rem-20">客先</td>
                  <td class="text-center align-middle mw-rem-30">受注番号</td>
                  <td class="text-center align-middle mw-rem-10">図面番号</td>
                  <td class="text-center align-middle mw-rem-10">個数</td>
                  <td colspan="2" class="text-center align-middle mw-rem-10">操作</td>
                </tr>
              </thead>
              <tbody>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tbody>
            </table>
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

// const
const C_USER_INDEX = 26; // 編集者表示
const C_USER_CON_INDEX = 27; // 条件付き編集者表示
const C_EDIT_USER = "23"; // 三条編集者ID

export default {
  name: "TableWorkingStatus",
  mixins: [dialogable, checkable, requestable],
  props: {
    targetFromDate: {
      type: String,
      default: ""
    },
    targetToDate: {
      type: String,
      default: ""
    },
    officeCode: {
      type: String,
      default: ""
    },
    customerCode: {
      type: Array,
      default: []
    }
  },
  computed: {
    userindex: function() {
      return C_USER_INDEX;
    },
    userconindex: function() {
      return C_USER_CON_INDEX;
    },
    edit_user_id: function() {
      return C_EDIT_USER;
    }
  },
  data() {
    return {
      targetFromYmd: "",
      targetToYmd: "",
      details: [],
      details_length: 0,
      defaultYmd: new Date(),
      dialogVisible: false,
      longitude: "",
      latitude: "",
      record_time: "",
      user_name: "",
      mode_name: ""
    };
  },
  // マウント時
  mounted() {
    if (this.targetFromDate == null || this.targetFromDate == "") {
      this.targetFromYmd = null;
    } else {
      this.targetFromYmd = moment(this.targetFromDate).format("YYYYMMDD");
    }
    if (this.targetFromDate == null || this.targetFromDate == "") {
      this.targetToYmd = null;
    } else {
      this.targetToYmd = moment(this.targetFromDate).format("YYYYMMDD");
    }
    this.officeCode = null;
    this.customerCode = null;
    this.getItem();
  },
  methods: {
    // ------------------------ イベント処理 ------------------------------------
    // 編集ボタン押下された場合の処理
    edtClick: function(index) {
      this.$router.push({ path: '/edit_work_order/home' })
    },
    // 削除ボタン押下された場合の処理
    delClick: function(index) {
      var messages = [];
      messages.push("準備中");
      this.htmlMessageSwal("通知", messages, "info", true, false);
    },
    // ------------------------ サーバー処理 ------------------------------------
    //
    getItem() {
      var arrayParams = {
        target_from_date: this.targetFromYmd,
        target_to_date: this.targetToYmd,
        office_code: this.officeCode,
        customer_code: this.customerCode
      };
      this.postRequest("/get_progress_header", arrayParams)
        .then(response => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("加工指示書／工程管理書", "取得");
        });
    },
    // ------------------------ 共通処理 ------------------------------------
    // 取得正常処理（ユーザー）
    getThen(response) {
      this.details = [];
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        this.details_length = Object.keys(this.details).length;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("加工指示書／工程管理書", "取得");
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
thead,
tbody {
  display: block !important;
}

tbody {
  overflow-x: hidden !important;
  overflow-y: scroll !important;
  height: 300px !important;
}

.table th,
.table td {
  padding: 0rem !important;
  border-style: solid dashed !important;
  border-width: 1px !important;
  border-color: #95c5ed #dee2e6 !important;
}
.bg-color-exists {
  background-color: aliceblue;
}
.bg-color-not-exists {
  background-color: floralwhite;
}

.mw-rem-6 {
  min-width: 8rem;
}

.mw-rem-8 {
  min-width: 8rem;
}
.mw-rem-30 {
    min-width: 30rem;
}
</style>
