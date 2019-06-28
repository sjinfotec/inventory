/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

window.Vue = require("vue");
import axios from "axios";
import toasted from "vue-toasted";
Vue.use(toasted);

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

Vue.component(
  "daily-working-information",
  require("./components/DailyWorkingInformation.vue").default
);
Vue.component(
  "monthly-working-information",
  require("./components/MonthlyWorkingInformation.vue").default
);
Vue.component("app-component", require("./components/App.vue").default);

Vue.component(
  "create-shift-time",
  require("./components/CreateShiftTime.vue").default
);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
  el: "#app"
  // router, // ルーティングの定義を読み込む
  // components: { App }, // ルートコンポーネントの使用を宣言する
  // template: "<App />" // ルートコンポーネントを描画する
});
