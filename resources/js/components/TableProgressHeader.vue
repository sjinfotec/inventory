<template>
  <div>
    <!-- ----------- テーブル部 START ---------------- -->
    <!-- main contentns row -->
    <div class="card-body">
      <!-- .row -->
      <div id="search_btn_cnt">

        <!-- .col -->
        <div class="flex_width print-none">
          <div id="input-area_2">
            <div class="input-area-prepend w_cate">
              <span
                class="input-area-text"
                for="target_fromdate"
              >
                納期
              </span>
            </div>
            <input-datepicker
              v-bind:default-date="valuesupplydate"
              v-bind:date-format="DatePickerFormat"
              v-bind:place-holder="'日付を選択してください'"
              v-on:change-event="supplydateChanges"
              v-on:clear-event="supplydateCleared"
            ></input-datepicker>
          </div>
          <message-data
            v-bind:message-datas="messagedatasfromdate"
            v-bind:message-class="'warning'"
          ></message-data>
        </div>
        <!-- /.col -->

        <!-- .col -->
        <div class="flex_width print-none">
          <div id="input-area_2">
            <div class="input-area-prepend w_cate">
              <span
                class="input-area-text"
                for="target_customer"
              >営業所</span>
            </div>
            <div class="form-control p-0">
            <select-officelist
              ref="selectofficelist"
              v-bind:blank-data="true"
              v-bind:placeholder-data="'選択'"
              v-bind:selected-value="selectedOfficeValue"
              v-bind:add-new="false"
              v-bind:row-index="0"
              v-on:change-event="officeChanges"
            ></select-officelist>
            </div>
          </div>
          <message-data
            v-bind:message-datas="messagedataoffice"
            v-bind:message-class="'warning'"
          ></message-data>
        </div>
        <!-- /.col -->
        <!-- .col -->
        <div class="flex_width print-none">
          <div id="input-area_2">
            <div class="input-area-prepend w_cate">
              <span
                class="input-area-text"
                for="target_customer"
              >客先</span>
            </div>
            <div class="form-control p-0">
            <select-customerlist
              ref="selectcustomerlist"
              v-bind:blank-data="true"
              v-bind:placeholder-data="'客先を選択してください'"
              v-bind:selected-value="selectedCustomerValue"
              v-bind:add-new="false"
              v-bind:office-code="selectedOfficeValue"
              v-bind:row-index="0"
              v-on:change-event="customerChanges"
            ></select-customerlist>
            </div>
          </div>
          <message-data
            v-bind:message-datas="messagedatacustomer"
            v-bind:message-class="'warning'"
          ></message-data>
        </div>
        <!-- /.col -->
      </div>

      <!-- .row -->
      <div id="search_btn_cnt">
        <!-- .col -->
        <div class="flex_width print-none">
          <div id="input-area_2">
            <div class="input-area-prepend w_cate">
              <span
                class="input-area-text"
                id="basic-addon1"
              >受注番号
              </span>
            </div>
            <div class="form-control p-0">
              <input
                type="text"
                title="受注番号"
                class="form-control"
                v-model="value_order_no"
                @change="ordernoChanges()"
              />
            </div>
          </div>
          <message-data v-bind:message-datas="messagedataorderno" v-bind:message-class="'warning'"></message-data>
        </div>
        <!-- /.col -->

        <!-- .col -->
        <div class="flex_width print-none">
          <div id="input-area_2">
            <div class="input-area-prepend w_cate">
              <span
                class="input-area-text"
                id="basic-addon1"
              >図面番号</span>
            </div>
            <div class="form-control p-0">
              <input
                type="text"
                title="図面番号"
                class="form-control"
                v-model="value_drawing_no"
                @change="drawingnoChanges()"
              />
            </div>
          </div>
          <message-data v-bind:message-datas="messagedatadrawingno" v-bind:message-class="'warning'"></message-data>
        </div>
        <!-- /.col -->

        <!-- .col -->
        <!-- <div class="flex_width print-none">
          <div id="input-area_2">
            <div class="input-area-prepend w_cate">
              <span
                class="input-area-text"
                id="basic-addon1"
              >品名</span>
            </div>
            <div class="form-control p-0">
              <select-productlist
                ref="selectproductlist"
                v-bind:blank-data="true"
                v-bind:placeholder-data="'品名を選択してください'"
                v-bind:selected-value="selectedProductsValue"
                v-bind:add-new="false"
                v-bind:row-index="0"
                v-on:change-event="productsnameChanges"
              ></select-productlist>
            </div>
          </div>
          <message-data v-bind:message-datas="messagedataproductsname" v-bind:message-class="'warning'"></message-data>
        </div> -->
        <!-- /.col -->

      </div>
      <!-- /.row -->

      <!-- .row -->
      <!-- ----------- ボタン部 START ---------------- -->
      <div id="btn_cnt3" class="print-none">
        <div class="btn_col_1">
          <btn-work-time
            v-on:searchclick-event="searchClick1"
            v-bind:btn-mode="'startsearchgo'"
            v-bind:is-push="false"
          ></btn-work-time>
        </div>
      </div>
      <!-- /.row -->

      <!-- .row -->
      <div class="row">
        <div class="card w-100" v-if="details_length">
          <div class="card-body table-responsive bg-color-exists">
            <table id="table_cnt5" class="table table-striped border-bottom font-size-sm text-nowrap">
              <!-- <table class="table"> -->
              <thead class="thead-dark">
                <tr>
                  <td class="text-center align-middle w1">No.</td>
                  <td class="text-center align-middle w2">納期</td>
                  <td class="text-center align-middle w5">客先</td>
                  <td class="text-center align-middle w3">受注番号</td>
                  <td class="text-center align-middle w4">行</td>
                  <td class="text-center align-middle mw-rem-15">図面番号</td>
                  <td class="text-center align-middle w1">個数</td>
                  <td class="text-center align-middle mw-rem-15">型式／型番</td>
                  <td class="text-center align-middle mw-rem-15">品名</td>
                  <td colspan="2" class="text-center align-middle mw-rem-10">操作</td>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item,rowIndex) in details" :key="rowIndex">
                  <td class="text-right align-middle w1">{{ rowIndex + 1 }}</td>
                  <td class="text-left align-middle w2">{{ item['supply_date_name'] }}</td>
                  <td class="text-left align-middle w5">{{ item['customer_name'] }}</td>
                  <td class="text-left align-middle w3">{{ item['order_no'] }}</td>
                  <td class="text-left align-middle w4">{{ item['row_seq'] }}</td>
                  <td class="text-left align-middle mw-rem-15">{{ item['drawing_no'] }}</td>
                  <td class="text-left align-middle w1">{{ item['order_count'] }}</td>
                  <td class="text-left align-middle mw-rem-15">{{ item['model_number'] }}</td>
                  <td class="text-left align-middle mw-rem-15">{{ item['product_name'] }}</td>
                  <td class="text-center align-middle mw-rem-5">
                    <a :href="edtUrl(item['order_no'],item['row_seq'] )" class="btn btn-primary">編集</a>
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
                  <td class="text-center align-middle mw-rem-10">客先</td>
                  <td class="text-center align-middle mw-rem-5">受注番号</td>
                  <td class="text-center align-middle mw-rem-5">行</td>
                  <td class="text-center align-middle mw-rem-15">図面番号</td>
                  <td class="text-center align-middle mw-rem-5">個数</td>
                  <td class="text-center align-middle mw-rem-15">型式／型番</td>
                  <td class="text-center align-middle mw-rem-15">品名</td>
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
      type: String,
      default: ""
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
    },
    edtUrl: function() {
      return function(orderno,row_seq) {
        return "/edit_work_order/home?order_no='" + orderno + "'&row_seq= '" + row_seq + "'";
      }
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
      mode_name: "",
      messagedatasfromdate: "",
      valuesupplydate: "",
      defaultDate: new Date(),
      DatePickerFormat: "yyyy年MM月dd日",
      fromdateChanges: "",
      fromdateCleared: "",
      selectedOfficeValue: "",
      selectedCustomerValue: "",
      value_order_no: "",
      value_drawing_no: "",

      messagedataoffice: "",
      messagedatacustomer: "",
      messagedataorderno: "",
      messagedatadrawingno: "",
      messagedataproductsname: "",

    };
  },
  // マウント時
  mounted() {
    //this.valuesupplydate = this.defaultDate;
    this.valuesupplydate = null;
    console.log('mounted defaultDate = ' + this.defaultDate);
    console.log('mounted valuesupplydate = ' + this.valuesupplydate);
    var date = new Date();

    if (this.targetFromDate == null || this.targetFromDate == "") {
      this.targetFromYmd = null;
    } else {
      this.valuesupplydate = this.targetFromDate;
      this.targetFromYmd = moment(this.targetFromDate).format("YYYYMMDD");
    }
    console.log('mounted targetFromYmd = ' + this.targetFromYmd);
    if (this.targetToDate == null || this.targetToDate == "") {
      this.targetToYmd = null;
    } else {
      this.targetToYmd = moment(this.targetToDate).format("YYYYMMDD");
    }
    this.selectedOfficeValue = this.officeCode ;
    this.selectedCustomerValue = this.customerCode ;
    this.value_order_no = null;
    this.value_drawing_no = null;
    this.getItem();
    console.log('mounted valuesupplydate = ' + this.valuesupplydate);
    if (this.valuesupplydate == null || this.valuesupplydate == "") {
    this.targetFromYmd = null;
    } else {
    this.targetFromYmd= moment(this.valuesupplydate).format("YYYYMMDD");
    }
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
    // 指定日付が変更された場合の処理
    supplydateChanges: function(value) {
      moment.locale("ja");
      this.valuesupplydate = value;
      this.targetFromYmd = moment(value).format("YYYYMMDD");
      this.targetToYmd = null;
    },
    // 指定日付がクリアされた場合の処理
    supplydateCleared: function() {
      this.valuesupplydate = "";
      this.targetFromYmd = null;
    },
      // 営業所選択が変更された場合の処理
    officeChanges: function(value, arrayitem) {
      console.log('officeChanges value = ' + value);
      this.selectedOfficeValue = value;
      // 客先選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      console.log('officeChanges this.selectedOfficeValue = ' + this.selectedOfficeValue);
      this.getCustomerSelected(this.selectedOfficeValue);
    },
    // 客先選択が変更された場合の処理
    customerChanges: function(value, arrayitem) {
      this.selectedCustomerValue = value;
    },
    // 受注番号選択が変更された場合の処理
    ordernoChanges: function() {
    },
    // 図面番号選択が変更された場合の処理
    drawingnoChanges: function() {
    },
    // 検索（表示）ボタン押下された場合の処理
    searchClick1() {
      this.getItem();
    },

    // ------------------------ サーバー処理 ------------------------------------
    //
    getItem() {
      var value_targetFromYmd = null;
      var value_targetToYmd = null;
      value_targetFromYmd = this.targetFromYmd;
      value_targetToYmd = this.targetToYmd;
      console.log('getItem value_targetFromYmd = ' + value_targetFromYmd);
      console.log('getItem value_targetToYmd = ' + value_targetToYmd);
      console.log('getItem this.selectedOfficeValue = ' + this.selectedOfficeValue);
      console.log('getItem this.selectedCustomerValue = ' + this.selectedCustomerValue);
      console.log('getItem this.value_order_no = ' + this.value_order_no);
      console.log('getItem this.value_drawing_no = ' + this.value_drawing_no);
      var arrayParams = {
        target_from_date: value_targetFromYmd,
        target_to_date: value_targetToYmd,
        office_code: this.selectedOfficeValue,
        customer_code: this.selectedCustomerValue,
        order_no: this.value_order_no,
        drawing_no: this.value_drawing_no
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
    // 営業所選択コンポーネント取得メソッド
    getOfficeSelected: function(value) {
      this.$refs.selectofficelist.getList(value);
    },
    // 客先選択コンポーネント取得メソッド
    getCustomerSelected: function(value) {
      console.log('getCustomerSelected value = ' + value);
      this.$refs.selectcustomerlist.getList(value);
    },
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
