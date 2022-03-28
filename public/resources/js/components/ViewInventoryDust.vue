<template>
<div>
  <!-- main contentns  -->
  <div id="">

    <div v-if="selectMode=='LINEACTIVE'">
      <div id="top_cnt">
        <h2>ゴミ箱一覧</h2>
      </div>

      <div id="msg_cnt" v-if="actionmsgArr.length">
          <ul class="errorred color1">
            <li v-for="(actionmsg,index) in actionmsgArr" v-bind:key="index">{{ actionmsg }}</li>
          </ul>
      </div>


      <div id="subtitle"><h4 class="bcc1">預かり</h4></div>
      <div id="tbl_1">
        <table>
          <thead>
            <tr>
              <th class="gc3">担当</th>
              <th class="gc3">受注番号</th>
              <th class="gc3">会社名</th>
              <th class="gc3">商品名</th>
              <th class="gc3">単位</th>
              <th class="gc3">入数</th>
              <th class="gc3">入庫日</th>
              <th class="gc3">発注数</th>
              <th class="gc3">入庫数</th>
              <th class="gc3">出庫日</th>
              <th class="gc3">出庫数</th>
              <th class="gc3">現在在庫</th>
              <th class="gc3">箱数</th>
              <th class="gc3">出庫No.</th>
              <th class="gc3">残りNo.</th>
              <th class="gc3">発送先</th>
              <th class="gc3">備考</th>
              <th class="gc3">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item,rowIndex) in details" :key="rowIndex">
              <td>{{ item['charge'] }}</td>
              <td>{{ item['order_no'] }}</td>
              <td>{{ item['company_name'] }}</td>
              <td>{{ item['product_name'] }}</td>
              <td class="nbr">{{ item['unit'] }}</td>
              <td class="style1">{{ item['quantity'] }}</td>
              <td class="nbr">{{ item['receipt_day'] }}</td>
              <td class="style1">{{ item['order_quantity'] }}</td>
              <td class="style1">{{ item['receipt'] }}</td>
              <td class="nbr">{{ item['delivery_day'] }}</td>
              <td class="style1">{{ item['delivery'] }}</td>
              <td class="style1">{{ item['now_inventory'] }}</td>
              <td class="style1">{{ item['nbox'] }}</td>
              <td>{{ item['dnum'] }}</td>
              <td>{{ item['rnum'] }}</td>
              <td>{{ item['shipping_address'] }}</td>
              <td>{{ item['remarks'] }}</td>
              <td class="style1 w1">
                <input type="hidden" v-model="details[rowIndex].id" name="id" />
                <button type="button" class="" @click="StatusBtn(rowIndex, item['id'], item['product_name'], item['order_info'] , 0)">
                元に戻す
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="" v-if="details == ''">該当するデータがありません</div>
      </div><!-- end tbl_1 -->

      <div id="subtitle"><h4 class="margin bcc2">在庫</h4></div>
      <div id="tbl_1">
        <table>
          <thead>
            <tr>
              <th class="gc3">担当</th>
              <th class="gc3">受注番号</th>
              <th class="gc3">会社名</th>
              <th class="gc3">商品名</th>
              <th class="gc3">単位</th>
              <th class="gc3">入数</th>
              <th class="gc3">納入日</th>
              <th class="gc3">納入数</th>
              <th class="gc3">発注日</th>
              <th class="gc3">発注数</th>
              <th class="gc3">現在在庫</th>
              <th class="gc3">箱数</th>
              <th class="gc3">発注先</th>
              <th class="gc3">単価</th>
              <th class="gc3">合計</th>
              <th class="gc3">備考</th>
              <th class="gc3">メモ/ノート</th>
              <th class="gc3">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item,rowIndex) in details2" :key="rowIndex">
              <td>{{ item['charge'] }}</td>
              <td>{{ item['order_no'] }}</td>
              <td>{{ item['company_name'] }}</td>
              <td>{{ item['product_name'] }}</td>
              <td class="nbr">{{ item['unit'] }}</td>
              <td class="style1">{{ item['quantity'] }}</td>
              <td class="nbr">{{ item['supply_day'] }}</td>
              <td class="style1">{{ item['supply_quantity'] }}</td>
              <td class="nbr">{{ item['order_day'] }}</td>
              <td class="style1">{{ item['order_quantity'] }}</td>
              <td class="style1">{{ item['now_inventory'] }}</td>
              <td class="style1">{{ item['nbox'] }}</td>
              <td>{{ item['order_address'] }}</td>
              <td>{{ item['unit_price'] }}</td>
              <td>{{ item['total'] }}</td>
              <td>{{ item['remarks'] }}</td>
              <td>{{ item['note'] }}</td>
              <td class="style1 w1">
                <input type="hidden" v-model="details2[rowIndex].id" name="id" />
                <button type="button" class="" @click="StatusBtn(rowIndex, item['id'], details2[rowIndex].product_name, item['order_info'], 0)">
                元に戻す
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="" v-if="details2 == ''">該当するデータがありません</div>
      </div><!-- end tbl_1 -->

    </div><!--end selectMode=='LINEACTIVE'-->




    <div id="input_area_1" v-if="selectMode=='COMPLETE'">
      <div>
        <h2>ゴミ箱 / {{ acttitle }} 完了</h2>
      </div>

      <div class="" v-if="actionmsgArr.length">
          <ul class="error-red color_red">
            <li v-for="(actionmsg,index) in actionmsgArr" v-bind:key="index">{{ actionmsg }}</li>
          </ul>
      </div>
      <div id="tbl_1">
        <table>
          <thead>
            <tr>
              <th class="gc3">担当</th>
              <th class="gc3">受注番号</th>
              <th class="gc3">会社名</th>
              <th class="gc3">商品名</th>
              <th class="gc3">単位</th>
              <th class="gc3">入数</th>
              <th class="gc3">入庫日</th>
              <th class="gc3">発注数</th>
              <th class="gc3">入庫数</th>
              <th class="gc3">出庫日</th>
              <th class="gc3">出庫数</th>
              <th class="gc3">現在在庫</th>
              <th class="gc3">箱数</th>
              <th class="gc3">出庫No.</th>
              <th class="gc3">残りNo.</th>
              <th class="gc3">発送先</th>
              <th class="gc3">備考</th>
              <th class="gc3">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item,rowIndex) in details" :key="rowIndex" v-bind:class="classObj1">
              <td >{{ item['charge'] }}</td>
              <td>{{ item['order_no'] }}</td>
              <td>{{ item['company_name'] }}</td>
              <td>{{ item['product_name'] }}</td>
              <td class="nbr">{{ item['unit'] }}</td>
              <td class="style1">{{ item['quantity'] }}</td>
              <td class="nbr">{{ item['receipt_day'] }}</td>
              <td class="style1" v-bind:class="(item['order_quantity'] === 0) ? 'color3' : ''">{{ item['order_quantity'] }}</td>
              <td class="style1" v-bind:class="(item['receipt'] === 0) ? 'color3' : ''">{{ item['receipt'] }}</td>
              <td class="nbr">{{ item['delivery_day'] }}</td>
              <td class="style1" v-bind:class="(item['delivery'] === 0) ? 'color3' : ''">{{ item['delivery'] }}</td>
              <td class="style1" v-bind:style="(item['now_inventory'] === 0) ? 'color:red' : ''">{{ item['now_inventory'] }}</td>
              <td class="style1">{{ item['nbox'] }}</td>
              <td>{{ item['dnum'] }}</td>
              <td>{{ item['rnum'] }}</td>
              <td>{{ item['shipping_address'] }}</td>
              <td>{{ item['remarks'] }}</td>
              <td> id={{ item['id'] }} re_id={{ re_id }}              
                <button type="button" class="" @click="EditBtn(item['id'], item['product_id'], details[rowIndex].product_name)">
                編集
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div><!-- end tbl_1 -->
    </div><!--end v-if="selectMode=='COMPLETE'-->


  </div>
  <!-- /main contentns  -->
</div>
</template>
<script>
//import toasted from "vue-toasted";
//import moment from "moment";
//import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  mixins: [ requestable , checkable ],
  props: {
  },
  data() {
    return {
      form: {
        id: "",
        charge: "",
        order_no: "",
        company_name: "",
        company_id: "",
        product_name: "",
        product_id: "",
        unit: "",
        quantity: "",
        receipt_day: "",
        place_order: "",
        receipt: "",
        delivery_day: "",
        delivery: "",
        now_inventory: "",
        nbox: "",
        dnum: "",
        rnum: "",
        shipping_address: "",
        status: "",
        order_info: "",
        other1: "",
        marks: "",
        created_user: "",
        updated_user: "",
        created_at: "",
        updated_at: ""
      },
      messagevalidatesNew: [],
      settingmessage: [],
      details: [],
      details2: [],
      edit_id: "",
      product_id: "",
      product_title: "",
      selectMode: "LINEACTIVE",
      actionmsgArr: [],
      acttitle: "",
      classObj1: ""
    };
  },
  // マウント時
  mounted() {
      this.getItem();
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション
    checkFormStore: function() {
      this.messagevalidatesNew = [];
      var chkArray = [];
      var flag = true;
      // 会社名
      var required = true;
      var equalength = 0;
      var maxlength = 20;
      var itemname = '会社名';
      chkArray = 
        this.checkHeader(this.form.company_name, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // 商品名
      required = true;
      equalength = 0;
      maxlength = 40;
      itemname = '商品名';
      chkArray = 
        this.checkHeader(this.form.product_name, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }

      if (this.messagevalidatesNew.length > 0) {
        flag = false;
      }
      return flag;
    },
    // ------------------------ イベント処理 ------------------------------------
    StatusBtn(index,eid,pname,orderinfo,mode) {
      console.log("StatusBtn orderinfo = " + orderinfo);
      var uk = "";
      var motion_msg = "";
        if (mode == 0) {
          motion_msg = 'ゴミ箱から移動';
          uk = 5;

        }

      var messages = [];
      if(orderinfo == 'a')  {
        var arrayParams = { details : this.details[index] , edit_id : eid , upkind : uk };
        var msg1 = '預かり';
      }
      if(orderinfo == 'z')  {
        var arrayParams = { details : this.details2[index] , edit_id : eid , upkind : uk };
        var msg1 = '在庫';
      }
      this.postRequest("/view_inventory_dust/update_" + orderinfo, arrayParams)
        .then(response  => {
          var res = response.data;
          //console.log(res);
          if (res.result) {
            //this.details = res.details;
            //this.details2 = res.details2;
            this.product_title = res.product_name;
            this.$toasted.show(this.product_title + " " + motion_msg + "しました");
            this.actionmsgArr.push(this.product_title + " を " + msg1 + " へ移動しました。");

          }
          this.getItem();
        })
        .catch(reason => {
          this.serverCatch(motion_msg);
        });


    },
    // -------------------- サーバー処理 ----------------------------
    // 取得処理
    getItem() {
      this.inputClear();
      //console.log("getitem in = ");
      var arrayParams = {  status : 'del' };
      this.postRequest("/view_inventory_dust/get",  arrayParams)
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("取得");
        });
    },
    // ゴミ箱処理
    dataDel(index,k) {
      if (this.checkFormStore()) {
        this.product_title = this.form.product_name;
        this.classObj1 = "bgcolor4";
        this.acttitle = "ゴミ箱移動";
        var messages = [];
        var arrayParams = { details : this.details[index] , upkind : k };
        this.postRequest("/view_inventory_a/update", arrayParams)
          .then(response  => {
            this.putThenDel(response, "ゴミ箱へ移動");
          })
          .catch(reason => {
            this.serverCatch("ゴミ箱移動");
          });
      }
    },
    // 編集変更処理
    dataUpdate(index,k) {
      if (this.checkFormStore()) {
        //console.log("dataUpdate in edit_id = " + this.edit_id);
        //console.log("dataUpdate in product_id = " + this.product_id);
        var messages = [];
        var arrayParams = { details : this.details[index] , upkind : k };
        var motion_msg = "";
        if (k == 0) motion_msg = '修正';
        if (k == 1) motion_msg = '在庫を更新';
        if (k == 2) motion_msg = '新しい商品追加';
        this.postRequest("/view_inventory_a/update", arrayParams)
          .then(response  => {
            this.putThenHead(response, motion_msg);
          })
          .catch(reason => {
            this.serverCatch(motion_msg);
          });
      }
    },
    // -------------------- 共通 ----------------------------
    // 取得正常処理
    getThen(response) {
      var res = response.data;
      //console.log(res);
      if (res.result) {
        this.details = res.details;
        this.details2 = res.details2;

      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("取得");
        }
      }
    },
    // 更新系正常処理
    putThenHead(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        if(res.id) {
          this.edit_id = res.id;
          //console.log("putThenHead in res.pid = " + res.product_id);
          this.product_id = res.product_id;
          this.product_title = res.product_name;
        }
        this.$toasted.show(this.product_title + " " + eventtext + "しました");
        //console.log("putThenHead in edit_id = " + this.edit_id);
        //console.log("putThenHead in product_id = " + this.product_id);
        this.getItemOne(this.edit_id,this.product_id,this.product_title);
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    // 削除系正常処理
    putThenDel(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.re_id = res.id;

        this.getItemOne(this.re_id,this.product_id,this.product_title);
        this.$toasted.show(this.product_title + "を" + eventtext + "しました");
        this.actionmsgArr.push(this.product_title + "ゴミ箱へ移動しました。","");
        this.selectMode = 'COMPLETE';

      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    // 異常処理
    serverCatch(eventtext) {
      var messages = [];
      //messages.push("ゴミ箱内" + eventtext + "に失敗しました");
      //this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    
    inputClear() {
      this.details = [];
      this.details2 = [];
    }





  }
};
</script>
