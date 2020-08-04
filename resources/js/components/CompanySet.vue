<template>
  <div><span class="pr-2 d-none d-md-inline">{{ company_name }}</span></div>
</template>
<script>
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "CompanySet",
  mixins: [ dialogable, checkable, requestable ],
  data() {
    return {
      company_name: "",
      details: []
    };
  },
  // マウント時
  mounted() {
    this.getCompanyItem();
  },
  methods: {
    // ------------------------ イベント処理 ------------------------------------
    // -------------------- サーバー処理 ----------------------------
    // 会社情報を取得
    getCompanyItem: function() {
      this.postRequest("/get_company_info_apply",  [])
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
        if ( this.details.length > 0) {
          for (var key in this.details) {
            this.company_name = this.details[key]['name'];
            break;
          };
        }
      }
    },
    // 異常処理
    serverCatch(eventtext) {
      var messages = [];
      messages.push("会社名" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    }
  }
};
</script>

