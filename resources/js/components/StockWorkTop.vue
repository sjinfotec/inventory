<template>
  <div>
      <div id="tips_cnt">
          <h3 class="">在庫管理システム／棚卸編</h3>
          <ul class="lst1">
          <li>棚卸する準備
            <ol class="lst2">
            <li>サブメニューより棚卸 / 預かり・在庫をクリック（メニューの棚卸をクリックでも同じ）</li>
            <li>棚卸の年月を選択し、『棚卸 新規開始』をクリックします</li>
            <li>※既に登録されている年月の場合は「違う年月を登録してください。」と表示されるので、違う年月を選択します。</li>
            <li>棚卸用のデータを読み込み、および作成され一覧で表示されます</li>
            <li>預かり・在庫が同時に表示されます</li>
            <li>受注番号がある場合には受注番号移動ボックスに受注番号を入力しクリックすると、その受注番号の欄まで移動表示できます</li>
            </ol>
          </li>
          <li>棚卸を更新するには
            <ol class="lst2">
            <li>棚卸在庫と棚卸箱数に数字を半角で入力</li>
            <li>『棚卸更新』をクリックすると棚卸在庫・棚卸箱数が更新され、在庫結果と箱数結果に数があっているかの結果が表示されます</li>
            <li>実在庫と当システム登録在庫があっているとチェック（合致）マークが付き、あっていないと過不足が表示されます</li>
            </ol>
          </li>
          <li>預かり・在庫商品の修正、更新
            <ol class="lst2">
            <li>過不足のある商品の右端にある『編集』をクリックすると編集画面にかわります</li>
            <li>修正または更新することにより、棚卸画面の在庫数に反映されます</li>
            </ol>
          </li>
          <li>棚卸の再開
            <ol class="lst2">
            <li>画面を閉じても再開可能です</li>
            <li>棚卸の年月を選択し、『棚卸 再開』をクリックします</li>
            <li>※まだ棚卸用データが作成していない年月を選んで再開すると「該当データがありません」と表示されます</li>
            </ol>
          </li>
          </ul>
      </div>
  </div>
</template>
<script>
//import moment from "moment";
import { dialogable } from "../mixins/dialogable.js";
import { checkable } from "../mixins/checkable.js";
import { requestable } from "../mixins/requestable.js";

// CONST
const CONST_C025 = "C025";
const CONST_C038 = "C038";

export default {
  name: "Home",
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
    };
  },
  // マウント時
  mounted() {
    //this.login_user_code = this.authusers["code"];
    //this.login_user_role = this.authusers["role"];
  },
  methods: {
    // ------------------------ サーバー処理 ----------------------------
    // -------------------- 共通 ----------------------------
  }
};
</script>
