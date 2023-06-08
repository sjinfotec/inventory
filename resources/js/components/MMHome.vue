<template>
  <div>
      <div id="tips_cnt">
          <h3 class="">在庫管理システム／使い方</h3>
          <ul class="lst1">
          <li>在庫を更新編集する準備
            <ol class="lst2">
            <li>メニュー欄より『在庫』をクリック、各『部署（階数）』をクリック、一覧を表示させる</li>
            <li>更新したい商品列の右端にある『更新』をクリックし、詳細を表示させる</li>
            </ol>
          </li>
          <li>在庫を更新（入庫出庫）するには
            <ol class="lst2">
            <li>入出庫の数字を半角で入力</li>
            <!--<li>商品名や備考なども変更可能<br>商品名を変更しても同じ商品として管理されます</li>-->
            <li>『在庫の更新』をクリックすると更新されます</li>
            </ol>
          </li>
          <li>在庫の修正
            <ol class="lst2">
            <li>メニュー欄より『在庫』をクリックし、一覧を表示させる</li>
            <li>修正したい商品列の右端にある『修正』をクリックし、詳細を表示させる</li>
            <li>『在庫の修正』をクリックすることで表示されている内容に上書き保存できます</li>
            <!--<li>入出庫はないが商品名等の変更がある場合に</li>-->
            </ol>
          </li>
          <!--
          <li>同じ会社に新たな商品の預かり・在庫を登録するには
            <ol class="lst2">
            <li>預かり・在庫の一覧から登録したい会社名の『編集』をクリック<br>会社名が同じであればどの商品名でも可能</li>
            <li>受注番号や商品名など各項目を入力する</li>
            <li>『この会社名で新しい商品を登録する』をクリックすると商品名が新しい管理で登録されます</li>
            <li>預かりから編集すると預かりへ登録され、在庫から編集すると在庫へ登録されます</li>
            </ol>
          </li>
          -->
          <li>在庫を抹消するには
            <ol class="lst2">
            <li>抹消したい商品列の右端にある『修正』をクリックし、詳細を表示させる</li>
            <li>『抹消』をクリックすると抹消されます</li>
            <li>抹消された商品はメニューの抹消の中へ移動されます</li>
            <li>いつでも元の登録場所へ戻すことができます</li>
            </ol>
          </li>
          <!--
          <li>在庫の登録を削除するには
            <ol class="lst2">
            <li>削除したい商品列の右端にある『修正』をクリックし、詳細を表示させる</li>
            <li>『この登録（レコード）を削除』をクリックすると削除されます</li>
            <li>該当商品列（画面表示されている登録のみ）は完全に削除されます</li>
            <li>不要な履歴を削除する場合に利用</li>
            <li>過去の履歴は『商品検索』で表示可能</li>
            </ol>
          </li>
          -->
          <li>新たに商品を登録するには
            <ol class="lst2">
            <li>メニュー欄より『在庫』をクリックし一覧表示</li>
            <li>一覧と書かれたタイトルの右端にある『新規登録』をクリックすると全て空欄の登録画面が表示されます</li>
            <li>商品名など各項目を入力する<br>担当と商品名は入力必須になっています</li>
            <li>『新規登録する』をクリックすると商品が新しい管理で登録されます</li>
            </ol>
          </li>
          </ul>
      </div>
      <!--<div id="version_cnt"><button type="button" class="customize" @click="viewBtn(2)">version 1.3</button></div>-->
      <div id="version_cnt"><a @click="viewBtn(2)">version 1.5.1</a></div>
      <div id="tbl_2" v-if="view_switch=='on'">
      <table>
        <thead>
          <tr>
          <th>version</th>
          <th>date</th>
          <th>overview</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>1.5.1</td><td>2023/06/08</td><td>棚卸入力更新の変更（情報処理課のみ）</td></tr>
          <tr><td>1.5</td><td>2023/05/31</td><td>棚卸入力の際、資材在庫更新が一定期間ない場合に自動で棚卸在庫更新（印刷1課のみ）</td></tr>
          <tr><td>1.4</td><td>2023/04/25</td><td>棚卸に在庫金額及び備考欄設置＆棚卸結果で差異がある場合に在庫ＤＢへ反映</td></tr>
          <tr><td>1.3</td><td>2022/10/25</td><td>検索項目追加＆検索結果金額表示</td></tr>
          <tr><td>1.2</td><td>2022/10/01</td><td>検索機能強化＆昇降順の正確性アップ</td></tr>
          <tr><td>1.1</td><td>2022/08/25</td><td>部署項目追加変更＆小型UPDATE</td></tr>
          <tr><td>1.0</td><td>2022/08/01</td><td>初版</td></tr>

        </tbody>
      </table>
      
      </div>
  </div>
</template>
<script>
//import moment from "moment";
import { dialogable } from "../mixins/dialogable.js";
import { checkable } from "../mixins/checkable.js";
import { requestable } from "../mixins/requestable.js";


export default {
  name: "MMHome",
  mixins: [dialogable, checkable, requestable],
  props: {
  /*
    authusers: {
      type: Array,
      default: []
    }
  */
  },
  data() {
    return {
      details: [],
      informations: [],
      content: "",
      login_user_code: 0,
      login_user_role: 0,
      dialogVisible: false,
      messageshowsearch: false,
      infomationmessage: [],
      view_switch: "off",
      i: 2,
    };
  },
  // マウント時
  mounted() {
    //this.login_user_code = this.authusers["code"];
    //this.login_user_role = this.authusers["role"];
  },
  methods: {
    viewBtn(go) {
    var amari = this.i % go;
    console.log("viewBtn amari = " + amari);
    if(amari == 0){
      console.log("viewBtn amari 0");
      this.view_switch = 'on';
    } else {
      this.view_switch = 'off';
    }
    this.i = this.i + 1;
    console.log("viewBtn i = " + this.i);
    },
    // ------------------------ サーバー処理 ----------------------------
    // -------------------- 共通 ----------------------------
  }
};
</script>
