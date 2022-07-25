<template>
<div>
  <!-- main contentns  -->
  <div id="">

    <div v-if="selectMode=='HOME'">
      <div id="btn_top">
          <button type="button" class="" @click="SelectContentsBtn('a')">
            抹消 / 在庫 1F
          </button>
          <button type="button" class="" @click="SelectContentsBtn('b')">
            抹消 / 在庫 2F
          </button>
          <button type="button" class="" @click="SelectContentsBtn('c')">
            抹消 / 在庫 3F
          </button>
      </div>
    </div>


    <div v-if="selectMode=='LINEACTIVE'">
      <div id="top_cnt">
        <h2 class="h2gc1" v-if="selectCnt=='a'">抹消一覧 1F</h2>
        <h2 class="h2gc2" v-if="selectCnt=='b'">抹消一覧 2F</h2>
        <h2 class="h2gc3" v-if="selectCnt=='c'">抹消一覧 3F</h2>
      </div>

      <div id="msg_cnt" v-if="actionmsgArr.length">
          <ul class="errorred color1">
            <li v-for="(actionmsg,index) in actionmsgArr" v-bind:key="index">{{ actionmsg }}</li>
          </ul>
      </div>


      <div id="subtitle"><h4 class="bcc1">抹消在庫</h4></div>
      <div id="tbl_1">
        <table>
          <thead>
            <tr>
              <th class="gc4">日付</th>
              <th class="gc4">部署</th>
              <th class="gc4">担当</th>
              <th class="gc4">商品名</th>
              <th class="gc4">商品コード</th>
              <th class="gc4">単位</th>
              <th class="gc4">入庫数</th>
              <th class="gc4">出庫数</th>
              <th class="gc4">現在在庫</th>
              <th class="gc4">備考</th>
              <th class="gc4">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item,rowIndex) in details" :key="rowIndex">
              <td class="nbr">{{ item['mdate'] }}</td>
              <td>{{ item['department'] }}</td>
              <td>{{ item['charge'] }}</td>
              <td>{{ item['product_name'] }}</td>
              <td>{{ item['product_number'] }}</td>
              <td class="nbr">{{ item['unit'] }}</td>
              <td class="style1">{{ item['receipt'] }}</td>
              <td class="style1">{{ item['delivery'] }}</td>
              <td class="style1">{{ item['now_inventory'] }}</td>
              <td>{{ item['remarks'] }}</td>
              <td class="style1 w1">
                <input type="hidden" v-model="details[rowIndex].id" name="id" />
                <button type="button" class="" @click="StatusBtn(rowIndex, item['id'], item['product_name'], item['marks'] , 0)">
                元に戻す
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="" v-if="details == ''">該当するデータがありません</div>
      </div><!-- end tbl_1 -->


    </div><!--end selectMode=='LINEACTIVE'-->


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
      selectMode: "HOME",
      actionmsgArr: [],
      acttitle: "",
      classObj1: "",
      selectCnt: "",

    };
  },
  // マウント時
  mounted() {
      //this.getItem();
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
    SelectContentsBtn(sc) {
      this.selectMode = "LINEACTIVE";
      this.getItem(sc);
      this.selectCnt = sc;
    },
    StatusBtn(index,eid,pname,marks,mode) {
      console.log("StatusBtn marks = " + marks);
      var uk = "";
      var motion_msg = "";
        if (mode == 0) {
          motion_msg = '抹消から移動';
          uk = 5;

        }

      var messages = [];
      if(marks === 'a')  {
        var arrayParams = { details : this.details[index] , edit_id : eid , upkind : uk };
        var msg1 = '在庫 1F';
      }
      if(marks === 'b')  {
        var arrayParams = { details : this.details[index] , edit_id : eid , upkind : uk };
        var msg1 = '在庫 2F';
      }
      if(marks === 'c')  {
        var arrayParams = { details : this.details[index] , edit_id : eid , upkind : uk };
        var msg1 = '在庫 3F';
      }
      
      this.postRequest("/mmdust/update" , arrayParams)
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
          this.getItem(marks);
        })
        .catch(reason => {
          this.serverCatch(motion_msg);
        });
        


    },
    // -------------------- サーバー処理 ----------------------------
    // 取得処理
    getItem(sc) {
      this.inputClear();
      //console.log("getitem in this.selectCnt = " + sc);
      var arrayParams = {   marks : sc , is_deleted : 1};
      this.postRequest("/mmdust/get",  arrayParams)
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
