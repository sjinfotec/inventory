<template>
<div>
  <!-- main contentns  -->
  <div id="">

    <div v-if="selectMode=='HOME'">
      <div id="btn_top">
          <button type="button" class="" @click="SelectContentsBtn('a')">
            棚卸 / 在庫 印刷1
          </button>
          <button type="button" class="" @click="SelectContentsBtn('b')">
            棚卸 / 在庫 印刷2
          </button>
          <button type="button" class="" @click="SelectContentsBtn('c')">
            棚卸 / 在庫 加工1
          </button>
          <button type="button" class="" @click="SelectContentsBtn('d')">
            棚卸 / 在庫 加工2
          </button>
          <button type="button" class="" @click="SelectContentsBtn('e')">
            棚卸 / 在庫 制作
          </button>
          <button type="button" class="" @click="SelectContentsBtn('f')">
            棚卸 / 在庫 情報処理
          </button>
          <button type="button" class="" @click="SelectContentsBtn('s')">
            棚卸 / 在庫 システム
          </button>
      </div>


      <div id="tips_cnt" class="mg_t40">
          <h3 class="">在庫管理システム／棚卸編</h3>
          <ul class="lst1">
          <li>棚卸する準備
            <ol class="lst2">
            <li>各『部署（階数）』をクリック、次の画面で棚卸の年月を選択し、『棚卸 新規開始』をクリックします</li>
            <li>※既に登録されている年月の場合は「違う年月を登録してください。」と表示されるので、違う年月を選択します</li>
            <li>棚卸用データの読み込み、および登録作成され一覧で表示されます</li>
            <!--<li>CODEやNUMBER番号がある場合には番号移動ボックスに番号を入力しクリックすると、その番号の欄まで移動表示できます</li>-->
            </ol>
          </li>
          <li>棚卸を更新するには
            <ol class="lst2">
            <li>棚卸在庫に数字を半角で入力</li>
            <li>『棚卸更新』をクリックすると棚卸在庫が更新され、在庫結果に数があっているかの結果が表示されます</li>
            <li>実在庫と入力在庫があっているとチェック（合致）マークが付き、あっていないと過不足が表示されます</li>
            <li><!--<span class="new">New</span>-->備考欄にコメントを残せます。『棚卸更新』のクリックで在庫入力との同時更新が可能です</li>
            <li>棚卸の更新を行うと合計金額が表示されます。※単価は『棚卸 新規開始』をクリックした時点の値になります。</li>
            <li>過不足があった場合、在庫の本データに在庫結果が自動反映されます。※通常の在庫入出庫更新は棚卸更新を終えた後の操作を推奨します</li>
            </ol>
          </li>
          <li>棚卸の再開
            <ol class="lst2">
            <li>画面を閉じても再開可能です</li>
            <li>『部署』選択後、棚卸の年月を選択し、『棚卸 再開』をクリックします</li>
            <li>※まだ棚卸用データが作成していない年月を選んで再開すると「該当データがありません」と表示されます</li>
            </ol>
          </li>
          </ul>
      </div>

    </div>



    <div v-if="selectMode=='DEFAULT'">
      <div id="top_cnt">
        <h2 class="h2gc1" v-if="selectCnt=='a'">棚卸 / 資材在庫一覧 印刷1</h2>
        <h2 class="h2gc2" v-if="selectCnt=='b'">棚卸 / 資材在庫一覧 印刷2</h2>
        <h2 class="h2gc1" v-if="selectCnt=='c'">棚卸 / 資材在庫一覧 加工1</h2>
        <h2 class="h2gc2" v-if="selectCnt=='d'">棚卸 / 資材在庫一覧 加工2</h2>
        <h2 class="h2gc3" v-if="selectCnt=='e'">棚卸 / 資材在庫一覧 制作</h2>
        <h2 class="h2gc3" v-if="selectCnt=='f'">棚卸 / 資材在庫一覧 情報処理</h2>
        <h2 class="h2gc3" v-if="selectCnt=='s'">棚卸 / 資材在庫一覧 システム</h2>
      </div>

      <div id="cnt3">
          <div>
            <button type="button" class="" @click="RestartStock()">
              棚卸 再開
            </button>
          </div>
          <div id="form3" class="mg1">
            <input type="month" class="form_style bc4" v-model="stock_month" name="stock_month">
            <button type="button" class="" @click="NewStock()">
              棚卸 新規開始
            </button>
          </div>
          <!--<div>stock month {{ stock_month }}</div>-->

      </div>


      <div class="" v-if="messagevalidatesNew.length">
          <ul class="error-red color_red">
            <li v-for="(messagevalidate,index) in messagevalidatesNew" v-bind:key="index">{{ messagevalidate }}</li>
          </ul>
      </div>


    </div><!--end v-if="selectMode=='DEFAULT'"-->



    <div v-if="selectMode=='LINEACTIVE'">
      <div id="top_cnt">
        <h2 class="h2gc1" v-if="selectCnt=='a'">棚卸 / 資材在庫一覧 印刷1</h2>
        <h2 class="h2gc2" v-if="selectCnt=='b'">棚卸 / 資材在庫一覧 印刷2</h2>
        <h2 class="h2gc1" v-if="selectCnt=='c'">棚卸 / 資材在庫一覧 加工1</h2>
        <h2 class="h2gc2" v-if="selectCnt=='d'">棚卸 / 資材在庫一覧 加工2</h2>
        <h2 class="h2gc3" v-if="selectCnt=='e'">棚卸 / 資材在庫一覧 制作</h2>
        <h2 class="h2gc3" v-if="selectCnt=='f'">棚卸 / 資材在庫一覧 情報処理</h2>
        <h2 class="h2gc3" v-if="selectCnt=='s'">棚卸 / 資材在庫一覧 システム</h2>
        <!--
        <form name="moveform">
          <div id="btn_cnt1">
            <div class="btn_col_1">
              <input type="text" name="urlname" class="form_style bc1">
              <input type="text" name="dummy" style="display:none;">
            </div>
            <div class="btn_col_2">
              <input type="button" value="分類" class="transition2 btn1" 
              onclick="location.hash = document.moveform.urlname.value; return false;">
            </div>
          </div>
        </form>
        -->
        <!--
        <form id="form1" name="form1">
          <input type="text" class="form_style bc1" v-model="s_order_no" maxlength="30" name="s_order_no">
          <button type="button" class="" @click="searchBtn()">
            受注番号 検索
          </button>
        </form>
        -->
      </div>

      <div class="" v-if="actionmsgArr.length > 0">
          <ul class="error-red color_red">
            <li v-for="(actionmsg,index) in actionmsgArr" v-bind:key="index">{{ actionmsg }}</li>
          </ul>
      </div>

      <div id="tbl_1">
        <table>
          <thead>
            <tr>
              <th class="gc3">商品名 <button type="button" class="" @click="ForwardReverse('product_name',1)">▲</button> <button type="button" class="" @click="ForwardReverse('product_name',2)">▼</button></th>
              <th class="gc3">分類 <button type="button" class="" @click="ForwardReverse('product_number',1)">▲</button> <button type="button" class="" @click="ForwardReverse('product_number',2)">▼</button></th>
              <th class="gc3">単位</th>
              <th class="gc3">単価</th>
              <th class="gc3">在庫</th>
              <!--<th class="gc4">箱数</th>-->
              <th class="gc3">在庫結果</th>
              <!--<th class="gc4">箱数結果</th>-->
              <th class="gc3">棚卸在庫</th>
              <th class="gc3">合計金額</th>
              <!--<th class="gc4">棚卸箱数</th>-->
              <th class="gc3">&nbsp;</th>
              <th class="gc3">備考</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item,rowIndex) in details3" :key="rowIndex"  v-bind:class="(item['cal_now_inventory'] !== 0 ) ? 'bgcolor6' : ''">
              <!--<td class="posi_r1"></td>-->
              <td ><span class="posi_a1" v-bind:id="item['product_number']"></span>{{ item['product_name'] }}</td>
              <td class="">{{ item['product_number'] }}</td>
              <td class="nbr">{{ item['unit'] }}</td>
              <td class="style1">{{ item['unit_price'] }}</td>
              <td class="style1">{{ item['now_inventory'] }}</td>
              <!--<td class="style1">{{ item['nbox'] }}</td>-->
              <td class="style1"><span class="color1" v-if="item['cal_now_inventory'] == 0">&#10004;</span><span class="color2 bold" v-else-if="item['cal_now_inventory'] !== 0">{{ item['cal_now_inventory'] }}</span></td>
              <!--<td class="style1"><span class="color1" v-if="item['cal_nbox'] === 0">&#10004;</span><span class="color2 bold" v-else-if="item['cal_nbox'] !== 0">{{ item['cal_nbox'] }}</span></td>-->
              <td class="style3" v-bind:class="(item['status'] === 'stockup') ? 'bgcolor4' : ''"><input type="number" class="form_style bc1" v-model="details3[rowIndex].stock_now_inventory" maxlength="11" name="now_inventory" min="0"></td>
              <td class="style1">{{ item['cal_total_price'] }}</td>
              <!--<td class="style1" v-bind:class="(item['status'] === 'stockup') ? 'bgcolor4' : ''"><input type="text" class="form_style bc1" v-model="details3[rowIndex].stock_nbox" maxlength="16" name="nbox"></td>-->
              <!--<td class="nbr"><span v-if="item['marks'] == 'a'">1F</span><span v-if="item['marks'] == 'b'">2F</span></td>-->
              <td>
                <input type="hidden" v-model="details3[rowIndex].stock_month" name="stock_month">
                <div id="btn_cnt1">
                  <button type="button" class="style1 mg_r" @click="stockUpdate(rowIndex,6)">
                  棚卸更新
                  </button>
                </div>
              </td>
              <td class="style3" ><textarea class="form_style2 bc1" v-model="details3[rowIndex].remarks" name="remarks"></textarea></td>
            </tr>
            <tr class="border1">
              <td colspan="4" class="style1">{{ this.d3length }} 件</td>
              <td colspan="3" class="style1">合計金額</td>
              <td class="style1">{{ Number(totals) | numberFormat }}</td>
              <td colspan="2" class="style1 font1"><div v-if="this.str_s_history"> ※合計金額に履歴は含まれていません</div></td>
            </tr>

          </tbody>
        </table>
      </div><!-- end tbl_1 -->
    </div><!--end selectMode=='LINEACTIVE'-->



    <div id="input_area_1" v-if="selectMode=='COMPLETE'">
      <div>
        <h2>{{ acttitle }} 完了</h2>
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
              <th class="gc1">担当</th>
              <th class="gc1">受注番号</th>
              <th class="gc1">会社名</th>
              <th class="gc1">商品名</th>
              <th class="gc1">単位</th>
              <th class="gc1">入数</th>
              <th class="gc1">入庫日</th>
              <th class="gc1">発注数</th>
              <th class="gc1">入庫数</th>
              <th class="gc1">出庫日</th>
              <th class="gc1">出庫数</th>
              <th class="gc1">現在在庫</th>
              <th class="gc1">箱数</th>
              <th class="gc1">出庫No.</th>
              <th class="gc1">残りNo.</th>
              <th class="gc1">発送先</th>
              <th class="gc1">備考</th>
              <th class="gc1">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item,rowIndex) in details" :key="rowIndex" v-bind:class="classObj1">
              <td>{{ item['charge'] }}</td>
              <td v-bind:class="(item['status'] == 'newest') ? 'bgcolor5' : ''">{{ item['order_no'] }}</td>
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
              <td>
                <!--
                id={{ item['id'] }} re_id={{ re_id }}              
                -->
                <button v-if="btnMode=='1'" type="button" class="" @click="EditBtn(item['id'], item['product_code'], details[rowIndex].product_name)">
                編集
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div><!-- end tbl_1 -->
    </div><!--end v-if-->


    <!--<div class="gotop"><a href="#">▲</a></div>-->
    <div id="page_top"><a href="#"></a></div>
  </div>
  <!-- /main contentns  -->
</div>
</template>
<script>
import { provide } from 'vue';
//import toasted from "vue-toasted";
import moment from "moment";
//import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';
import { useRouter } from 'vue-router';

export default {
  components: {

  },
  mixins: [ requestable , checkable ],
  props: {
    marks: {
      type: [String, Number],
      default: ""
    },
    product_name: {
      type: [String, Number],
      default: ""
    },
    orderfr: {
      type: [String, Number],
      default: ""
    }
  },
  computed: {
  },
  filters: {
    numberFormat: function(num){
      return num.toLocaleString();
    }
  },
  data() {
    return {
      form: {
        id: "",
        mdate: "",
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
        dnum: "",
        rnum: "",
        status: "",
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
      details3: [],
      d3length: "",
      edit_id: "",
      product_title: "",
      selectMode: "HOME",
      selectCnt: "",
      actionmsgArr: [],
      acttitle: "",
      classObj1: "",
      view_switch: "off",
      i: 2,
      s_order_no: "",
      btnMode: 0,
      stock_month: "",
      totals: "",
      search_totals: "",
      str_s_history: "",
            
    };
  },
  provide() {
    var smonth = this.stock_month;
    return {
      details: () => this.details,
      details2: () => this.details2,
      strs: smonth
      //details: this.details,
      //details2: this.details2
    }
  },
  setup() {

  },
  // マウント時
  mounted() {
      this.monthset();
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    checkFormInput: function() {
      this.messagevalidatesNew = [];
      var chkArray = [];
      var flag = true;
      // 月
      var required = true;
      var equalength = 0;
      var maxlength = 20;
      var itemname = '日付';
      //console.log("checkFormInput in ");
      chkArray = 
        this.checkHeader(this.stock_month, required, equalength, maxlength, itemname);
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
      this.selectMode = "DEFAULT";
      //this.getItem(sc);
      this.selectCnt = sc;
    },
    NewStock()  {
      if (this.checkFormInput()) {
        this.getItem();
        console.log("NewStock in details = " + this.details);
        setTimeout(this.AllStore, 3000);
        //console.log("NewStock in details = " + this.details);
        //this.AllStore();
        this.selectMode = 'LINEACTIVE';
        this.actionmsgArr.push(this.product_title + "登録データ作成中です...","");
      }

    },
    RestartStock()  {
      if (this.checkFormInput()) {
        this.actionmsgArr = [];
        this.getItemStock();
        this.actionmsgArr.push("" + this.stock_month + " 棚卸を開始します。");
        //console.log("NewStock in details = " + this.details);
        this.selectMode = 'LINEACTIVE';
      }
    },
    ForwardReverse(arraykey,q1) {
      var sort_target = arraykey; //ソート対象を変数で設定

      if(q1 == 1) {
        this.details3 = this.details3.sort(function(x, y) {
          if (x[sort_target] === y[sort_target]) {
            return 0;
          }
          else if (x[sort_target] === null) {
            return 1;
          }
          else if (y[sort_target] === null) {
            return -1;
          }
          else {
            return x[sort_target].localeCompare(y[sort_target], 'ja');
          }
        });
      }
      if(q1 == 2) {
        this.details3 = this.details3.sort(function(x, y) {
          if (x[sort_target] === y[sort_target]) {
            return 0;
          }
          else if (x[sort_target] === null) {
            return 1;
          }
          else if (y[sort_target] === null) {
            return -1;
          }
          else {
            return y[sort_target].localeCompare(x[sort_target], 'ja');
          }
        });
      }

      /*
      if(q1 == 1) {
        this.details3.sort(function(a,b){
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
        this.details3.sort(function(a,b){
          if(a[sort_target] > b[sort_target]) {
            return -1;
          }
          if(a[sort_target] < b[sort_target]) {
            return 1;
          }
          return 0;
        });
      }
      */
      //console.log(this.details3);
    },
    StockBtn(eid,inv,nbox,details) {
      //var edit_id = eid;
      //console.log(edit_id);
      this.actionmsgArr.push("-------------------------------",eid + " = ID",inv + " = stock_now_inventory",nbox + " = stock_nbox",details);
      //this.selectMode = 'EDT';
      //this.getItemOne(eid,pid,pname);
    },
    InvBtn(eid,pid,pname,orderinfo) {
      var edit_id = eid;
      console.log("InvBtn in edit_id = " + edit_id);
      //this.actionmsgArr.push(eid + " = ID",pid + " = product_code",pname + " = product_name",orderinfo + " = order_info");
      this.selectMode = 'EDT';
      this.getItemOne(eid,pid,pname,orderinfo);
    },
    NewBtn()  {
      this.inputClear();
      this.selectMode = 'NEW';
    },
    searchBtn() {
      this.details = [];
      this.searchItem();
      this.selectMode = 'COMPLETE';
    },
    viewBtn(go) {
    var amari = this.i % go;
    if(amari == 0){
      this.view_switch = 'on';
    } else {
      this.view_switch = 'off';
    }
    this.i = this.i + 1;
    },
    // -------------------- サーバー処理 ----------------------------
    // 取得処理
    getItem() {
      this.inputClear();
      //console.log("getitem in");
      var arrayParams = { 
        orderfr : this.orderfr,
        marks : this.selectCnt
        };
      //console.log("getitem in arrayParams['receipt_day'] = " + arrayParams['receipt_day']);
      this.postRequest("/mmstock/invget", arrayParams)
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          //console.log("getitem reason");
          this.serverCatch("取得");
        });
    },
    getItemStock() {
      this.inputClear();
      var sort_q = this.sort_q;
      //console.log('getItemStock in sort_q = ' + sort_q);
      var arrayParams = { 
        stock_month : this.stock_month,
        orderfr : this.orderfr,
        marks : this.selectCnt
        };
      //console.log("getitem in arrayParams['receipt_day'] = " + arrayParams['receipt_day']);
      this.postRequest("/mmstock/stockget", arrayParams)
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          //console.log("getItemStock reason");
          if(sort_q > 0 ) {
            //console.log('getthen in this.sort_q = ' + this.sort_k);
            //console.log('getthen in this.details3 = ' + this.details3);
            this.ForwardReverse(this.sort_k,this.sort_q);
          }
          this.serverCatch("取得");
        });
    },
    // 取得処理(単)
    getItemOne(e,p,pn,oi) {
      this.inputClear();
      console.log("getitem one in edit_id = " + e);
      console.log("getitem one in product_code = " + p);
      console.log("getitem one in order_info = " + oi);
      this.edit_id = e;
      this.product_code = p;
      this.product_title = pn;
      this.orinfo = oi;

      var arrayParams = {  edit_id : e , product_code : p};
      this.postRequest("/view_inventory_" + oi + "/getone", arrayParams)
        .then(response  => {
          //console.log(response);
          this.getThen(response);
        })
        .catch(reason => {
          //console.log("getitem reason");
          this.serverCatch("取得");
        });
    },
    // 検索処理
    searchItem() {
        console.log("searchItem in s_order_no = " + this.s_order_no);
        this.s_order_no = this.s_order_no;
        //this.classObj1 = "bgcolor3";
        this.acttitle = "検索";
        var motion_msg = "検索";
        var messages = [];
        var arrayParams = { s_order_no : this.s_order_no , order_info : 'a'};
        this.postRequest("/view_inventory_a/search", arrayParams)
          .then(response  => {
            this.putThenSearch(response, motion_msg);
            this.btnMode = 1;
          })
          .catch(reason => {
            this.serverCatch("検索");
          });
    },
    // ALLインサート処理
    AllStore(k) {
        //console.log("AllStore in details = " + this.details);
        console.log("AllStore in stock_month = " + this.stock_month);
        //this.product_title = this.form.product_name;
        this.classObj1 = "bgcolor3";
        this.acttitle = "棚卸データ登録";
        if(this.details === "" || this.details === null){
          console.log('AllStore details無')
          this.actionmsgArr.push("登録商品がありません","");
        }
        else{
          console.log('AllStore details 有り')
        
        var messages = [];
        var arrayParams = {
          details : this.details,
          stock_month : this.stock_month,
          upkind : k,
          marks : this.selectCnt
        };
        this.postRequest("/mmstock/insert", arrayParams)
          .then(response  => {
            this.putThenStore(response, "棚卸データ登録");
          })
          .catch(reason => {
            this.serverCatch("登録");
          });
        }
    },
    // 登録インサート処理
    dataStore() {
      if (this.checkFormStore()) {
        this.product_title = this.form.product_name;
        this.classObj1 = "bgcolor3";
        this.acttitle = "新規登録";
        var messages = [];
        var arrayParams = { form : this.form };
        this.postRequest("/view_inventory_a/insert", arrayParams)
          .then(response  => {
            this.putThenStore(response, "新規登録");
          })
          .catch(reason => {
            this.serverCatch("登録");
          });
      }
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
    // 棚卸在庫登録処理
    stockUpdate(index,k) {
        //console.log("stockUpdate in edit_id = " + this.edit_id);
        //console.log("stockUpdate in product_code = " + this.product_code);
        var messages = [];
        var arrayParams = { details : this.details3[index] , upkind : k , marks : this.selectCnt };
        var motion_msg = "";
        if (k == 6) motion_msg = '在庫カウント';
        this.postRequest("/mmstock/update", arrayParams)
          .then(response  => {
            this.putThenUpdate(response, motion_msg);
          })
          .catch(reason => {
            this.serverCatch(motion_msg);
          });
    },
    // 編集変更処理
    dataUpdate(index,k,oi) {
      if (this.checkFormStore()) {
        //console.log("dataUpdate in edit_id = " + this.edit_id);
        //console.log("dataUpdate in product_code = " + this.product_code);
        var messages = [];
        var arrayParams = { details : this.details[index] , upkind : k };
        var motion_msg = "";
        if (k == 0) motion_msg = '修正';
        if (k == 1) motion_msg = '在庫を更新';
        if (k == 2) motion_msg = '新しい商品追加';
        this.postRequest("/view_inventory_" + oi + "/update", arrayParams)
          .then(response  => {
            this.putThenHead(response, motion_msg, oi);
          })
          .catch(reason => {
            this.serverCatch(motion_msg);
          });
      }
    },
    // -------------------- 共通 ----------------------------
    // 取得正常処理
    getThen(response, eventtext) {
      //console.log('getthen in ' + this.sort_q);
      var sort_q = this.sort_q;
      var res = response.data;
      if (res.result) {
        //console.log('getThen in res.result = ' + res.result);
        this.details = res.details;
        this.details2 = res.details2;
        this.details3 = res.details3;
        this.count = this.details3.length;
        var pt = 0;
        
        this.details3.forEach(function(element, index) {
          //console.log('getThen in details3.forEach = ' + element.cal_total_price);
          if((typeof element.cal_total_price == 'string')) {
            pt = pt + Number(element.cal_total_price);
            //console.log('getThen in typeof element.cal_total_price = ' + pt);
          }
        });
        this.totals = pt;
        //this.product_title = res.details[0].product_name;
        if (typeof this.details3 !== 'undefined') {
          //console.log('getthen in this.details3.length = ' + this.details3.length); 
          if (this.details3.length == 0) this.actionmsgArr.push("" + this.stock_month + " 該当データがありません");
        }
        this.d3length = this.details3.length;
        //console.log('getthen in sort_q = ' + sort_q);
        if(sort_q > 10 ) {
          //console.log('getthen in this.sort_q = ' + this.sort_k);
          //console.log('getthen in this.details3 = ' + this.details3);
          this.ForwardReverse(this.sort_k,this.sort_q);
        }
        if(eventtext.length > 0) {
          this.$toasted.show(this.product_title + " " + eventtext + "しました");
        }

      }
      else {
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
          //if(this.details[0].status == 'newest') this.classObj1 = "bgcolor5";
          this.product_title = "受注番号 " + res.s_order_no;
          console.log("putThenSearch in res.s_order_no = " + res.s_order_no);
          this.$toasted.show(this.product_title + " " + eventtext + "しました");
          this.actionmsgArr.push(this.product_title + " を検索しました。","");
      } else {
          this.actionmsgArr.push(this.s_order_no + " が見つかりませんでした。","");
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    // 更新系正常処理
    putThenHead(response, eventtext, oi) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        if(res.id) {
          this.edit_id = res.id;
          //console.log("putThenHead in res.pid = " + res.product_code);
          this.product_code = res.product_code;
          this.product_title = res.product_name;
        }
        this.$toasted.show(this.product_title + " " + eventtext + "しました");
        //console.log("putThenHead in edit_id = " + this.edit_id);
        //console.log("putThenHead in product_code = " + this.product_code);
        this.getItemOne(this.edit_id,this.product_code,this.product_title,oi);
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    // 棚卸更新系正常処理
    putThenUpdate(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        if(res.id) {
          this.edit_id = res.id;
          //console.log("putThenUpdate in res.pid = " + res.product_code);
          this.product_code = res.product_code;
          this.product_title = res.product_name;
        }
        this.$toasted.show(this.product_title + " " + eventtext + "しました");
        //console.log("putThenHead in edit_id = " + this.edit_id);
        //console.log("putThenHead in product_code = " + this.product_code);
        this.getItemStock();
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    // 新規系正常処理
    putThenStore(response, eventtext) {
      console.log("putThenStore in ");
      var messages = [];
      var res = response.data;
      this.product_title = this.stock_month + "";
      this.actionmsgArr = [];
      if (res.result) {
        this.re_id = res.id;
        this.getItemStock();
        this.$toasted.show(this.product_title + "を" + eventtext + "しました");
        this.actionmsgArr.push(this.stock_month + " 新しい月で棚卸の準備ができました。");

        //this.selectMode = 'COMPLETE';
        this.selectMode = 'LINEACTIVE';

      } else {
        this.details = [];
        this.details2 = [];
        this.details3 = [];
        if (res.update_num == 0) this.actionmsgArr.push(this.product_title + " は既に登録されている年月です。","違う年月を登録してください。");

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

        this.getItemOne(this.re_id,this.product_code,this.product_title);
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
      this.form.now_inventory = "";
      this.form.nbox = "";
      this.form.remarks = "";
      this.form.status = "";
      this.form.marks = "";
      this.form.created_user = "";
      this.form.updated_user = "";
      this.form.created_at = "";
      this.form.updated_at = "";
    },
    monthset: function()  {
      var date_obj = new Date();
      //console.log('todayset = ' + date_obj);
      this.today_year  = date_obj.getFullYear(); // 西暦年取得
      this.today_month = date_obj.getMonth();    // 月取得
      // 文字列として連結month_format
      this.stock_month = ('0000' + this.today_year).slice(-4) 
                      + '-' 
                      + ('00' + (this.today_month + 1)).slice(-2) 
    },





  },

};


</script>
