<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <div class="card-body mb-3 p-0 border-top">
            <!-- panel contents -->
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <div class="col-12 p-0">
                    <table class="table table-striped border-bottom font-size-sm text-nowrap">
                      <tr v-for="(n,index) in form_count " :key="index">
                        <td class="text-center align-middle">{{ form.item_name[index] }}</td>
                        <td class="text-center align-middle">{{ form.item_data[index] }}</td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.row -->
            <!-- ----------- ボタン部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between print-none">
              <!-- col -->
              <div class="col-md-6 pb-2">
                <btn-work-time
                  v-on:okclick-event="okclick"
                  v-bind:btn-mode="'ok'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <!-- /.col -->
              <!-- col -->
              <!-- <div class="col-md-6 pb-2">
                <btn-work-time
                  v-on:cancelclick-event="cancelclick"
                  v-bind:btn-mode="'cancel'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div> -->
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
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
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';
// CONST
const C_SUPPLY_DATE_NAME = "納期";
const C_ORDER_NO_NAME = "受注番号";
const C_ROW_SEQ_NAME = "行";
const C_DRAWING_NO_NAME = "図面番号";
const C_ORDER_COUNT_NAME = "個数";
const C_MODEL_NUMBER_NAME = "形式／型番";
const C_PRODUCT_NAME = "品名";
const C_OUTLINE_NAME = "明細摘要";
const C_DEVICE_NAME = "機器名";
const C_USER_NAME = "作業者";
const C_KIND_START = "1";
const C_KIND_START_NAME = "作業開始";
const C_KIND_END = "2";
const C_KIND_END_NAME = "作業終了";
const C_KIND_STOP = "3";
const C_KIND_STOP_NAME = "作業中止";
const C_KIND_COMPLETE = "9";
const C_KIND_COMPLETE_NAME = "作業完了";

export default {
  name: "ProcessInfo",
  mixins: [ dialogable, checkable, requestable ],
  props: {
    order_no: {
      type: String,
      default: ""
    },
    seq: {
      type: Number,
      default: 0
    },
    kind: {
      type: String,
      default: ""
    },
    device: {
      type: String,
      default: ""
    },
    user_code: {
      type: String,
      default: ""
    }
  },
  data() {
    return {
      form: {
        order_no: "",
        seq: 0,
        kind: "",
        device_code: "",
        user_code: "",
        row_seq: "",
        progress_no: "",
        item_name: [{}],
        item_data: [{}]
      },
      count: 0,
      before_count: 0,
      form_count: 0,
      details: []
    };
  },
  computed: {
  },
  // マウント時
  mounted() {
    console.log('ProcessInfo mounted ');
    this.getItem();
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション
    // ------------------------ イベント処理 ------------------------------------
    // 確認OK
    okclick() {
      this.storeData();
    },
    // キャンセル
    cancelclick() {
      window.close();
    },
    // -------------------- サーバー処理 ----------------------------
    // 指示書／管理書取得
    getItem() {
      console.log('ProcessInfo getItem order_no = ' + this.order_no);
      console.log('ProcessInfo getItem seq = ' + this.seq);
      console.log('ProcessInfo getItem kind = ' + this.kind);
      console.log('ProcessInfo getItem device = ' + this.device);
      var arrayParams = { 
        order_no : this.order_no ,
        seq : this.seq,
        kind : this.kind,
        device : this.device,
        user_code : this.user_code
        };
      this.postRequest("/process_info/get", arrayParams)
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("取得");
        });
    },
    // 登録処理
    storeData() {
      var arrayParams = { form : this.form };
      this.postRequest("/process_history/put", arrayParams)
        .then(response => {
          this.putThen(response, "登録");
        })
        .catch(reason => {
          this.serverCatch("作業工程", "登録");
        });
    },
    // -------------------- 共通 ----------------------------
    // 取得正常処理
    getThen(response) {
      var res = response.data;
      var kind_data = "";
      if (res.result) {
        switch (this.kind) {
          case C_KIND_START:
            kind_data = C_KIND_START_NAME;
            break;
          case C_KIND_END:
            kind_data = C_KIND_END_NAME;
            break;
          case C_KIND_STOP:
            kind_data = C_KIND_STOP_NAME;
            break;
          case C_KIND_COMPLETE:
            kind_data = C_KIND_COMPLETE_NAME;
            break;
          default:
            break;
        }
        console.log('getThen in kind_data = ' + kind_data);
        this.details = res.details;
        this.count = this.details.length;
        if ( this.details.length > 0) {
          this.form.kind = this.kind;
          this.form.device_code = this.device;
          this.form.user_code = this.user_code;
          this.form.order_no = this.order_no;
          this.form.seq = this.seq;
          var set_index = 0;
          let $this = this;
          this.details.forEach((detail, i) => {
            $this.form.row_seq = detail.row_seq;
            // progress_noは廃止する方向
            $this.form.progress_no = null;
            $this.form.item_name[set_index] = C_SUPPLY_DATE_NAME;
            $this.form.item_data[set_index] = detail.supply_date_name;
            console.log('getThen detail.supply_date_name = ' + $this.form.item_data[set_index]);
            set_index = set_index + 1;
            $this.form.item_name[set_index] = C_ORDER_NO_NAME;
            $this.form.item_data[set_index] = detail.order_no;
            set_index = set_index + 1;
            $this.form.item_name[set_index] = C_ROW_SEQ_NAME;
            $this.form.item_data[set_index] = detail.row_seq;
            set_index = set_index + 1;
            $this.form.item_name[set_index] = C_DRAWING_NO_NAME;
            $this.form.item_data[set_index] = detail.drawing_no;
            set_index = set_index + 1;
            $this.form.item_name[set_index] = C_ORDER_COUNT_NAME;
            $this.form.item_data[set_index] = detail.order_count;
            set_index = set_index + 1;
            $this.form.item_name[set_index] = C_MODEL_NUMBER_NAME;
            $this.form.item_data[set_index] = detail.model_number;
            set_index = set_index + 1;
            $this.form.item_name[set_index] = C_PRODUCT_NAME;
            $this.form.item_data[set_index] = detail.back_order_product_name;
            set_index = set_index + 1;
            $this.form.item_name[set_index] = C_OUTLINE_NAME;
            $this.form.item_data[set_index] = detail.outline_name;
            set_index = set_index + 1;
            $this.form.item_name[set_index] = C_DEVICE_NAME;
            $this.form.item_data[set_index] = detail.device_name;
            set_index = set_index + 1;
            $this.form.item_name[set_index] = C_USER_NAME;
            $this.form.item_data[set_index] = detail.user_name;
            set_index = set_index + 1;
            $this.form.item_name[set_index] = "QRコードの種類";
            $this.form.item_data[set_index] = kind_data;
          });
        }
        this.form_count = set_index + 1;
        console.log('getThen in form_count = ' + this.form_count);
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("取得");
        }
      }
    },
    // 更新系正常処理
    putThen(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        messages.push("作業工程を" + eventtext + "しました。");
        this.htmlMessageSwal(eventtext + "完了", messages, "info", true, false);
        this.getItem();
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch("作業工程", eventtext);
        }
      }
    },
    // 異常処理
    serverCatch(eventtext) {
      var messages = [];
      messages.push("" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
  }
};
</script>
<style scoped>
.mw-rem-2 {
    min-width: 2rem;
}
</style>
