/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

 require("./bootstrap");

 window.Vue = require("vue");
 import axios from "axios";
 import router from "./router";
 import toasted from "vue-toasted";
 //import VCalendar from "v-calendar";
 import VModal from "vue-js-modal";
 import Vue from "vue";
 // import VueSwal from "vue-swal";
 import VueSweetalert2 from "vue-sweetalert2";
 import "sweetalert2/dist/sweetalert2.min.css";
 //import ElementUI from "element-ui";
 import { Dialog, Select, Option, TimePicker, Button } from "element-ui";
 import "element-ui/lib/theme-chalk/index.css";
 //import locale from "element-ui/lib/locale/lang/ja";
 import lang from "element-ui/lib/locale/lang/ja";
 import locale from "element-ui/lib/locale";
 
 var options = {
     position: "bottom-center",
     duration: 2000,
     fullWidth: false,
     type: "info"
 };
 
 Vue.use(toasted, options);
 //Vue.use(VCalendar);
 Vue.use(VModal);
 // Vue.use(VueSwal);
 locale.use(lang);
 //Vue.use(ElementUI, { locale });
 Vue.use(VueSweetalert2);
 
 Vue.prototype.$axios = axios;
 // Vue.prototype.$toasted = toasted;
 
 /**
  * The following block of code may be used to automatically register your
  * Vue components. It will recursively scan this directory for the Vue
  * components and automatically register them with their "basename".
  *
  * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
  */
 
 // const files = require.context('./', true, /\.vue$/i);
 // files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));
 
 Vue.component("app-component", require("./components/App.vue").default);
 
 
 // ホーム画面
 Vue.component(
     "home-component",
     require("./components/HomeComponent.vue").default
 );

 
 // 預かり一覧
 Vue.component(
    "view-inventory-a",
    require("./components/ViewInventoryA.vue").default
);
 // 在庫一覧
 Vue.component(
    "view-inventory-z",
    require("./components/ViewInventoryZ.vue").default
);
 // ゴミ箱一覧
 Vue.component(
    "view-inventory-dust",
    require("./components/ViewInventoryDust.vue").default
);
 // 棚卸
 Vue.component(
    "stock-work-top",
    require("./components/StockWorkTop.vue").default
);
 Vue.component(
    "stock-work-a",
    require("./components/StockWorkA.vue").default
);



 // -------------------------- 資材 在庫 material management ----------------------------------------------

 // 在庫home
 Vue.component(
    "mm-home",
    require("./components/MMHome.vue").default
);
 // 在庫一覧
 Vue.component(
    "mat-manage",
    require("./components/MatManage.vue").default
);
 // 棚卸
 Vue.component(
    "mm-stock",
    require("./components/MMStock.vue").default
);








 // -------------------------- 設定 ----------------------------------------------
 
 // -------------------------- 操作 ----------------------------------------------
 
 // -------------------------- 共通 ----------------------------------------------
 // ボタン類
 Vue.component("btn-work-time", require("./components/BtnWorkTime.vue").default);
 
 
 
 
 
 
 const app = new Vue({
     el: "#app",
     // render: h => h(App)
     router // ルーティングの定義を読み込む
     // components: { App }, // ルートコンポーネントの使用を宣言する
     // template: "<App />" // ルートコンポーネントを描画する
 });
 