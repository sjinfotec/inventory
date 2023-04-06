<template>
  <div>
    <div v-if="dataarr['selecthtml'] == 'select_cnt'">
      {{ dataarr['selecthtml'] }}
      <div>
        <ul>
          <li><a href="/">在庫管理システム －預かり・在庫－</a></li>
          <li><a href="/mm">在庫管理システム －資材－</a></li>
        </ul>

      </div>
    </div>
    <div v-else>
      <div id="tips_cnt">
          <h3 class="">在庫管理システム／使い方</h3>
          <ul class="lst1">
          <li>預かり・在庫商品を更新編集する準備
            <ol class="lst2">
            <li>メニューより預かり又は在庫をクリックし、一覧を表示させる</li>
            <li>更新したい商品列の右端にある『編集』をクリックし、詳細を表示させる</li>
            </ol>
          </li>
          <li>預かり・在庫商品を更新（入庫出庫）するには
            <ol class="lst2">
            <li>入出庫の数字を半角で入力</li>
            <li>商品名や備考なども変更可能<br>商品名を変更しても同じ商品として管理されます</li>
            <li>『在庫の更新』をクリックすると更新されます</li>
            </ol>
          </li>
          <li>預かり・在庫商品の修正
            <ol class="lst2">
            <li>『この内容に修正する』をクリックすることで編集箇所（表示されている）の内容で上書き保存できます</li>
            <li>入出庫はないが商品名等の変更がある場合に</li>
            </ol>
          </li>
          <li>同じ会社に新たな商品の預かり・在庫を登録するには
            <ol class="lst2">
            <li>預かり・在庫の一覧から登録したい会社名の『編集』をクリック<br>会社名が同じであればどの商品名でも可能</li>
            <li>受注番号や商品名など各項目を入力する</li>
            <li>『この会社名で新しい商品を登録する』をクリックすると商品名が新しい管理で登録されます</li>
            <li>預かりから編集すると預かりへ登録され、在庫から編集すると在庫へ登録されます</li>
            </ol>
          </li>
          <li>商品の預かり・在庫を抹消するには
            <ol class="lst2">
            <li>抹消したい商品列の右端にある『編集』をクリックし、詳細を表示させる</li>
            <li>『ゴミ箱へ移す』をクリックすると抹消されます</li>
            <li>抹消された商品はメニューのゴミ箱の中へ移動されます</li>
            <li>いつでも元の登録場所（預かり又は在庫）へ戻すことができます</li>
            <li>戻すと『在庫の更新』など編集作業ができます</li>
            </ol>
          </li>
          <li>新たに会社名も商品名も登録するには
            <ol class="lst2">
            <li>メニューより預かり又は在庫をクリックし一覧表示</li>
            <li>一覧と書かれたタイトルの右端にある『新規登録』をクリックすると全て空欄の登録画面が表示されます</li>
            <li>受注番号や会社名、商品名など各項目を入力する<br>会社名と商品名は入力必須になっています</li>
            <li>『この内容で新規登録する』をクリックすると会社名、商品名が新しい管理で登録されます</li>
            <li>預かり一覧から新規登録すると預かりへ登録され、在庫一覧から新規登録すると在庫へ登録されます</li>
            </ol>
          </li>
          </ul>
      </div>
    </div>
  </div>
</template>
<script>
//import moment from "moment";
import { dialogable } from "../mixins/dialogable.js";
import { checkable } from "../mixins/checkable.js";
import { requestable } from "../mixins/requestable.js";

// CONST

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
    dataarr: {
      type: Object,
      default: []
    }
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
