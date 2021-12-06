<template>
  <div class="col-xl-12">
    <!-- main contentns row -->
    <!-- ========================== 【加工指示書／工程管理】部 START ========================== -->
    <div class="d-flex align-content-between mt-3">
      <div class="card w-100">
        <div class="card-header bg-color alert-head">
          <i class="fa fa-user-circle-o fa-lg my-orange fa-fw" aria-hidden="true"></i>
          <span class="font-weight-bolder">【加工指示書／工程管理】</span>
        </div>
        <table-progress-header
          ref="refeditprogresstable"
          v-bind:target-from-date="null"
          v-bind:target-to-date="null"
          v-bind:office-code="null"
          v-bind:customer-code="null"
        ></table-progress-header>
      </div>
      <!-- /.panel -->
    </div>
    <!-- ========================== 【加工指示書／工程管理】部 END =========================== -->
  </div>
</template>
<script>
import moment from "moment";
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
    authusers: {
      type: Array,
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
    this.login_user_code = this.authusers["code"];
    this.login_user_role = this.authusers["role"];
  },
  methods: {
    // ------------------------ サーバー処理 ----------------------------
    // -------------------- 共通 ----------------------------
  }
};
</script>
<style scoped>
.font-color-black {
  color: black;
}
.margin-top-regular {
  margin-top: 30px;
}
.margin-left-small {
  margin-left: 15px;
}
.my-red {
  color: red;
}
.my-skyblue {
  color: skyblue;
}
.my-orange {
  color: #fecb81;
}
.bg-color {
  background-color: floralwhite;
}
.padding-dis {
  padding: 0.75rem 0rem !important;
}
.alert-head {
  background-color: #0d3867;
  color: white;
}
</style>
