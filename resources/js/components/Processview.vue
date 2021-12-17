<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <div class="card-body mb-3 p-0 border-top">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between px-3">
              <!-- panel header -->
              <div class="card-header col-12 bg-transparent pb-2 border-0">
                <h1 class="float-sm-left font-size-rg">{{ today_date }}</h1>
                <span class="float-sm-right font-size-sm"></span>
              </div>
              <!-- /.panel header -->
            </div>
            <!-- .row -->
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <div class="col-12 p-0">
                    <table id="table_cnt4" class="table table-striped border-bottom font-size-sm text-nowrap">
                      <thead>
                        <tr>
                          <td class="style1 text-center align-middle">納期</td>
                          <td class="style1 text-center align-middle">客先</td>
                          <td class="style1 text-center align-middle">受注番号</td>
                          <!--<td class="style1 text-center align-middle">行</td>-->
                          <td class="style1 text-center align-middle">品名</td>
                          <td class="style1 text-center align-middle">機器名</td>
                          <td class="style1 text-center align-middle">作業者名</td>
                          <td class="style1 text-center align-middle">状況</td>
                          <!--
                          <td class="style1 text-center align-middle">作業時刻</td>
                          <td class="style1 text-center align-middle">作業時間</td>
                          -->
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(item,index) in details " :key="index">
                          <td class="text-center align-middle w1" v-bind:class="{ 'td_active': isActiveKind }">{{ item.supply_date_name }}</td>
                          <td class="text-center align-middle w2">{{ item.back_order_customer_name }}</td>
                          <td class="text-center align-middle w3">{{ item.order_no }}</td>
                          <!--<td class="text-center align-middle w4">{{ item.row_seq }}</td>-->
                          <td class="text-center align-middle w5 textwrap">{{ item.back_order_product_name }}</td>
                          <td class="text-center align-middle w6 textwrap">{{ item.device_name }}</td>
                          <td class="text-center align-middle w7">{{ item.user_name }}</td>
                          <td class="text-center align-middle w8">{{ item.work_kind_name }}</td>
                          <!--
                          <td class="text-center align-middle">{{ item.process_history_time_name }}</td>
                          <div v-if="item.process_time_h">
                            <td class="text-center align-middle">{{ item.process_time_h }}時間{{ item.process_time_m }}分</td>
                          </div>
                          <div v-else>
                            <td class="text-center align-middle"></td>
                          </div>
                          <td class="text-center align-middle"></td>
                          -->
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
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
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';
// CONST

export default {
  name: "ProcessView",
  mixins: [ dialogable, checkable, requestable ],
  props: {
  },
  data() {
    return {
      today_date: "",
      count: 0,
      before_count: 0,
      details: [],
      isActiveKind: false
    };
  },
  computed: {
  },
  // マウント時
  mounted() {
    console.log('ProcessView mounted ');
    moment.locale("ja", {
      weekdays: ["日曜日", "月曜日", "火曜日", "水曜日", "木曜日", "金曜日", "土曜日"],
      weekdaysShort: ["日", "月", "火", "水", "木", "金", "土"]
    });
    this.today_date = moment().format('YYYY年MM月DD日(dddd)');
    this.getItem();
},
  // Vueインスタンスに変化があったら発動する
  updated() {

    // setTimeoutで10000ms後にshowをfalseにする
    setTimeout(() => {
      this.getItem();}
      ,10000
    )
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション
    // ------------------------ イベント処理 ------------------------------------
    // -------------------- サーバー処理 ----------------------------
    // 指示書／管理書取得
    getItem() {
      console.log('getItem in ')
      var arrayParams = { 
        order_no : ''
        };
      this.postRequest("/process_view/get", arrayParams)
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("取得");
        });
    },
    // -------------------- 共通 ----------------------------
    // 取得正常処理
    getThen(response) {
      var res = response.data;
      if (res.result) {
        this.details = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("取得");
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
