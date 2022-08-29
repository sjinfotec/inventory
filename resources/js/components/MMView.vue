<template>
<div>
  <!-- main contentns  -->
  <div id="">

    <div v-if="selectMode=='HOME'">
      <div id="btn_top">
          <button type="button" class="" @click="SelectContentsBtn('a')">
            在庫 印刷1
          </button>
          <button type="button" class="" @click="SelectContentsBtn('b')">
            在庫 印刷2
          </button>
          <button type="button" class="" @click="SelectContentsBtn('c')">
            在庫 加工1
          </button>
          <button type="button" class="" @click="SelectContentsBtn('d')">
            在庫 加工2
          </button>
          <button type="button" class="" @click="SelectContentsBtn('e')">
            在庫 制作
          </button>
          <button type="button" class="" @click="SelectContentsBtn('f')">
            在庫 情報処理
          </button>
      </div>
    </div>




    <div v-if="selectMode=='LINEACTIVE'">
      <div id="top_cnt">
        <h2 class="h2gc1 ilb" v-if="selectCnt=='a'"><span>資材在庫一覧</span><span>印刷1</span></h2>
        <h2 class="h2gc2 ilb" v-if="selectCnt=='b'"><span>資材在庫一覧</span><span>印刷2</span></h2>
        <h2 class="h2gc1 ilb" v-if="selectCnt=='c'"><span>資材在庫一覧</span><span>加工1</span></h2>
        <h2 class="h2gc2 ilb" v-if="selectCnt=='d'"><span>資材在庫一覧</span><span>加工2</span></h2>
        <h2 class="h2gc3 ilb" v-if="selectCnt=='e'"><span>資材在庫一覧</span><span>制作</span></h2>
        <h2 class="h2gc3 ilb" v-if="selectCnt=='f'"><span>資材在庫一覧</span><span>情報処理</span></h2>
        <form id="form1" name="form2">
          <input type="text" class="form_style bc1" v-model="s_charge" maxlength="30" name="s_charge">
          <button type="button" class="" @click="searchBtn()">
            担当 検索
          </button>
        </form>
        <form id="form1" name="form1">
          <input type="text" class="form_style bc1" v-model="s_product_name" maxlength="30" name="s_product_name">
          <button type="button" class="" @click="searchBtn()">
            商品 検索
          </button>
        </form>
      </div>

      <div id="tbl_1">
        <table>
          <thead>
            <tr>
              <th class="gc2">日付 <button type="button" class="" @click="ForwardReverse('mdate',1)">▲</button> <button type="button" class="" @click="ForwardReverse('mdate',2)">▼</button><!-- <a href="./material_management?mdate=1">▲</a> <a href="./material_management?mdate=2">▼</a>--></th>
              <th class="gc2">部署 <button type="button" class="" @click="ForwardReverse('department',1)">▲</button> <button type="button" class="" @click="ForwardReverse('department',2)">▼</button><!-- <a href="./material_management?department=1">▲</a> <a href="./material_management?department=2">▼</a>--></th>
              <th class="gc2">担当 <button type="button" class="" @click="ForwardReverse('charge',1)">▲</button> <button type="button" class="" @click="ForwardReverse('charge',2)">▼</button><!-- <a href="./material_management?charge=1">▲</a> <a href="./material_management?charge=2">▼</a>--></th>
              <th class="gc2">商品名 <button type="button" class="" @click="ForwardReverse('product_name',1)">▲</button> <button type="button" class="" @click="ForwardReverse('product_name',2)">▼</button><!-- <a href="./material_management?product_name=1">▲</a> <a href="./material_management?product_name=2">▼</a>--></th>
              <th class="gc2">商品コード</th>
              <th class="gc2">発注先</th>
              <th class="gc2">単位</th>
              <th class="gc2">入庫数</th>
              <th class="gc2">出庫数</th>
              <th class="gc2">現在在庫</th>
              <th class="gc2">単価</th>
              <th class="gc2">合計金額</th>
              <th class="gc2">備考</th>
              <!--<th class="gc2">メモ/ノート</th>-->
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item,rowIndex) in details" :key="rowIndex">
              <td class="nbr">{{ item['mdate'] }}</td>
              <td>{{ item['department'] }}</td>
              <td>{{ item['charge'] }}</td>
              <td>{{ item['product_name'] }}</td>
              <td>{{ item['product_number'] }}</td>
              <td>{{ item['order_address'] }}</td>
              <td class="nbr">{{ item['unit'] }}</td>
              <td class="style1">{{ item['receipt'] }}</td>
              <td class="style1">{{ item['delivery'] }}</td>
              <td class="style1">{{ Number(item.now_inventory) | numberFormat }}</td>
              <td class="style1">{{ Number(item.unit_price) | numberFormat }}</td>
              <td class="style1">
                <div v-if="item['total'] !== null">{{ Number(item['total']) | numberFormat }}</div>
                <div v-else-if="item['total'] === null">{{ Number(item['now_inventory'] * item['unit_price']) | numberFormat }}</div>
              </td>
              <td>{{ item['remarks'] }} </td>
              <!--<td>{{ item['note'] }}</td>-->
            </tr>
            <tr class="border1">
              <td colspan="11" class="style1">総合計金額</td>
              <td class="style1">{{ Number(totalItem(details)) | numberFormat }}</td>
              <td><!--※強制計算無し {{ Number(totals) | numberFormat }}--></td>
            </tr>
          </tbody>
        </table>
        <div class="" v-if="details == ''">該当するデータがありません</div>
      </div><!-- end tbl_1 -->
    </div><!--end selectMode=='LINEACTIVE'-->

    <div id="input_area_1" v-if="selectMode=='COMPLETE'">
      <div>
        <h2>在庫 / {{ acttitle }} 完了</h2>
      </div>
            <div id="btn_cnt2">
              <button type="button" class="" @click="backLine()">一覧へ</button>
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
              <th class="gc2">日付</th>
              <th class="gc2">部署</th>
              <th class="gc2">担当</th>
              <th class="gc2">商品名</th>
              <th class="gc2">商品コード</th>
              <th class="gc2">発注先</th>
              <th class="gc2">単位</th>
              <th class="gc2">入庫数</th>
              <th class="gc2">出庫数</th>
              <th class="gc2">現在在庫</th>
              <th class="gc2">単価</th>
              <th class="gc2">合計金額</th>
              <th class="gc2">備考</th>
              <!--
              <th class="gc2">メモ/ノート</th>
              -->
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item,rowIndex) in details" :key="rowIndex" v-bind:class="classObj1">
              <td class="nbr">{{ item['mdate'] }}</td>
              <td>{{ item['department'] }}</td>
              <td>{{ item['charge'] }}</td>
              <td v-bind:class="(item['status'] == 'newest') ? 'bgcolor5' : ''">{{ item['product_name'] }}</td>
              <td>{{ item['product_number'] }}</td>
              <td>{{ item['order_address'] }}</td>
              <td class="nbr">{{ item['unit'] }}</td>
              <td class="style1" v-bind:class="(item['receipt'] === 0) ? 'color3' : ''">{{ item['receipt'] }}</td>
              <td class="style1" v-bind:class="(item['delivery'] === 0) ? 'color3' : ''">{{ item['delivery'] }}</td>
              <td class="style1" v-bind:style="(item['now_inventory'] === 0) ? 'color:red' : ''">{{ item['now_inventory'] }}</td>
              <td class="style1">{{ item['unit_price'] }}</td>
              <td class="style1">{{ item['total'] }}</td>
              <td>{{ item['remarks'] }}</td>
              <!--
              <td>{{ item['note'] }}</td>
              -->
            </tr>
          </tbody>
        </table>
      </div><!-- end tbl_1 -->
    </div><!--end v-if-->

    <div id="page_top"><a href="#"></a></div>
  </div>
  <!-- /main contentns  -->
</div>
</template>
<script>
//import toasted from "vue-toasted";
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  mixins: [ requestable , checkable , dialogable],
  data() {
    return {
      form: {
        id: "",
        mdate: "",
        department: "",
        charge: "",
        product_name: "",
        product_code: "",
        product_number: "",
        unit: "",
        quantity: "",
        receipt: "",
        delivery: "",
        now_inventory: "",
        nbox: "",
        order_address: "",
        unit_price: "",
        total: "",
        remarks: "",
        note: "",
        status: "",
        marks: "",
        created_user: "",
        updated_user: "",
        created_at: "",
        updated_at: "",
        is_deleted: ""
      },
      messagevalidatesNew: [],
      settingmessage: [],
      details: [],
      details2: [],
      edit_id: "",
      product_code: "",
      product_title: "",
      mdate: "",
      selectMode: "HOME",
      selectCnt: "",
      actionmsgArr: [],
      acttitle: "",
      classObj1: "",
      view_switch: "off",
      i: 2,
      s_charge: "",
      s_product_name: "",
      btnMode: 0,
      calc_now_inventory: "",
      calc_nbox: "",
      btn_select: "",
      isDisabled: "",
      smode: "",
      itsdate: "",
      totals: "",
      ttl: 0,
      ttls: 0,
    };
  },
  computed: {
  },
  // マウント時
  mounted() {
      //this.getItem();
      this.dateset();
  },
  filters: {
    numberFormat: function(num){
      return num.toLocaleString();
    }
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // ------------------------ イベント処理 ------------------------------------
    SelectContentsBtn(sc) {

      this.selectMode = "LINEACTIVE";
      this.getItem(sc);
      this.selectCnt = sc;


    },
    searchBtn() {
      this.details = [];
      this.searchItem();
      this.selectMode = 'COMPLETE';
    },
    backLine() {
      this.selectMode = "LINEACTIVE";
      const sc = this.selectCnt;
      this.getItem(sc);

    },
    resultLine() {
      this.details = [];
      this.searchItem();
      this.selectMode = "COMPLETE";
    },
    ForwardReverse(arraykey,q1) {
      this.sort_k = arraykey;
      this.sort_q = q1;
      var sort_target = arraykey; //ソート対象を変数で設定
      //if(q1 == 1) this.details.sort((a, b) => a[sort_target] - b[sort_target]);
      //if(q1 == 2) this.details.sort((a, b) => b[sort_target] - a[sort_target]);
      //console.log("ForwardReverse in details = " + this.details);

      if(q1 == 1) {
        this.details.sort(function(a,b){
          if(a[sort_target] > b[sort_target]) {
            return 1;
          }
          if(a[sort_target] < b[sort_target]) {
            return -1;
          }
          return 0;
        });
      }
      if(q1 == 2) {
        this.details.sort(function(a,b){
          if(a[sort_target] > b[sort_target]) {
            return -1;
          }
          if(a[sort_target] < b[sort_target]) {
            return 1;
          }
          return 0;
        });
      }
      //console.log("ForwardReverse in details sort result = " + this.details);
    },
    totalItem: function(details){
      let sum = 0;
      for(let i = 0; i < this.details.length; i++){
        sum += ((this.details[i].now_inventory) * (this.details[i].unit_price));
      }

     return sum;
    },


    // -------------------- サーバー処理 ----------------------------
        // 取得処理
    getItem(sc) {
      this.inputClear();
      var arrayParams = { 
        charge : this.charge,
        product_name : this.product_name,
        mdate : this.mdate,
        orderfr : this.orderfr,
        marks : sc,
        is_deleted : 0
      };
      //console.log("getitem in arrayParams['mdate'] = " + arrayParams['mdate']);
      this.postRequest("/material_management/get", arrayParams)
        .then(response  => {
          //console.log(response);
          this.getThen(response);
        })
        .catch(reason => {
          //console.log("getitem reason");
          this.serverCatch("取得");
        });
    },
    // 取得処理(単)
    getItemOne(e,p,pn,md) {
      this.inputClear();
      //console.log("getitem one in edit_id = " + e);
      //console.log("getitem one in product_code = " + p);
      //console.log("getitem one in p_name = " + pn);
      this.edit_id = e;
      this.product_code = p;
      this.product_title = pn;
      //console.log("getitem one in product_title = " + this.product_title);
      var arrayParams = {  edit_id : e , product_code : p};
      this.postRequest("/material_management/getone", arrayParams)
        .then(response  => {
          this.getThen(response);
          if(md === 'update') {
            this.details[0].mdate = this.itsdate;
            this.details[0].receipt = "";
            this.details[0].delivery = "";
          }
        })
        .catch(reason => {
          //console.log("getitem reason");
          this.serverCatch("取得");
        });
    },
    // 検索処理
    searchItem() {
        //console.log("searchItem in s_ = " + this.s_order_no);
        //this.s_order_no = this.s_order_no;
        this.classObj1 = "";
        this.acttitle = "検索";
        var motion_msg = "検索";
        var messages = [];
        var arrayParams = { s_charge : this.s_charge , s_product_name : this.s_product_name , marks : 'a'};
        this.postRequest("/material_management/search", arrayParams)
          .then(response  => {
            this.putThenSearch(response, motion_msg);
            //this.btnMode = "1";
          })
          .catch(reason => {
            this.serverCatch("検索");
          });
    },
    // -------------------- 共通 ----------------------------
    // 取得正常処理
    getThen(response) {
      var res = response.data;
      //console.log('getthen in res = ' + res);
      if (res.result) {
        this.details = res.details;
        this.details2 = res.details2;
        this.count = this.details.length;
        this.before_count = this.count;
        this.totals = res.totals[0].totals;
        if ( this.details.length > 0) {
          this.form.id = this.details[0].id;
          this.form.mdate = this.details[0].mdate;
          this.form.department = this.details[0].department;
          this.form.charge = this.details[0].charge;
          this.form.product_name = this.details[0].product_name;
          this.form.product_code = this.details[0].product_code;
          this.form.product_number = this.details[0].product_number;
          this.form.unit = this.details[0].unit;
          this.form.quantity = this.details[0].quantity;
          this.form.receipt = this.details[0].receipt;
          this.form.delivery = this.details[0].delivery;
          this.form.now_inventory = this.details[0].now_inventory;
          this.form.nbox = this.details[0].nbox;
          this.form.order_address = this.details[0].order_address;
          this.form.unit_price = this.details[0].unit_price;
          this.form.total = this.details[0].total;
          this.form.remarks = this.details[0].remarks;
          this.form.note = this.details[0].note;
          this.form.status = this.details[0].status;
          this.form.marks = this.details[0].marks;
          this.form.created_user = this.details[0].created_user;
          this.form.updated_user = this.details[0].updated_user;
          this.form.created_at = this.details[0].created_at;
          this.form.updated_at = this.details[0].updated_at;
          this.form.is_deleted = this.details[0].is_deleted;

        } else {
          
        }
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("取得");
        }
      }
    },
    // 検索系正常処理
    putThenSearch(response, eventtext) {
      var messages = [];
      var res = response.data;
      //if (res.result) {
      if (res.details.length > 0) {
          this.details = res.details;
          //this.classObj1 = (this.details[0].status == 'newest') ? 'bgcolor3' : '';
          this.product_title = res.s_charge + res.s_product_name;
          //console.log("putThenSearch in res.s_product_name = " + res.s_product_name);
          this.$toasted.show(this.product_title + " " + eventtext + "しました");
          this.actionmsgArr.push(this.product_title + " を検索しました。");
      } else {
          this.actionmsgArr.push(this.s_charge + this.s_product_name + " が見つかりませんでした。","");
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
      //messages.push("在庫管理" + eventtext + "に失敗しました");
      //this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    
    inputClear() {
      this.details = [];

      this.form.id = "";
      this.form.mdate = "";
      this.form.department = "";
      this.form.charge = "";
      this.form.product_name = "";
      this.form.product_code = "";
      this.form.product_number = "";
      this.form.unit = "";
      this.form.quantity = "";
      this.form.receipt = "";
      this.form.delivery = "";
      this.form.now_inventory = "";
      this.form.nbox = "";
      this.form.order_address = "";
      this.form.unit_price = "";
      this.form.total = "";
      this.form.remarks = "";
      this.form.note = "";
      this.form.status = "";
      this.form.marks = "";
      this.form.created_user = "";
      this.form.updated_user = "";
      this.form.created_at = "";
      this.form.updated_at = "";
      this.form.is_deleted = "";

    },
    dateset: function()  {
      var date_obj = new Date();
      //console.log('todayset = ' + date_obj);
      this.today_year  = date_obj.getFullYear(); // 西暦年取得
      this.today_month = date_obj.getMonth();    // 月取得
      this.today_day = date_obj.getDate();    // 日取得
      // 文字列として連結month_format
      this.itsdate = ('0000' + this.today_year).slice(-4) 
                      + '-' 
                      + ('00' + (this.today_month + 1)).slice(-2) 
                      + '-' 
                      + ('00' + (this.today_day)).slice(-2) 
    },


  }
};
</script>
