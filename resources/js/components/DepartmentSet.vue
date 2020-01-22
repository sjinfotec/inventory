<template>
  <div><li class="pr-3 py-1 text-white font-size-sm d-block">{{ department_name }}</li></div>
</template>
<script>
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "DepartmentSet",
  mixins: [ dialogable, checkable, requestable ],
  data() {
    return {
      department_name: "",
      details: []
    };
  },
  // マウント時
  mounted() {
    this.getDepartmentItem();
  },
  methods: {
    // ------------------------ イベント処理 ------------------------------------
    // -------------------- サーバー処理 ----------------------------
    // 会社情報を取得
    getDepartmentItem: function() {
      this.postRequest("/get_login_user_department",  [])
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
            this.department_name = this.details[key]['department_name'];
            break;
          };
        }
      }
    },
    // 異常処理
    serverCatch(eventtext) {
      messages.push("部署名" + eventtext + "に失敗しました");
      this.messageswal("エラー", messages, "error", true, false, true);
    }
  }
};
</script>

