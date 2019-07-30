/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

window.Vue = require("vue");
import axios from "axios";
import toasted from "vue-toasted";
import VCalendar from "v-calendar";
import VModal from "vue-js-modal";

var options = {
    position: "bottom-center",
    duration: 2000,
    fullWidth: false,
    type: "info"
};
Vue.use(toasted, options);
Vue.use(VCalendar);
Vue.use(VModal);

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
    "daily-working-information-panel-header",
    require("./components/DailyWorkingInformationPanelHeader.vue").default
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

Vue.component(
    "setting-shift-time",
    require("./components/SettingShiftTime.vue").default
);

Vue.component(
    "search-workingtimebutton",
    require("./components/WorkTimeSearchButton.vue").default
);

Vue.component(
    "select-department",
    require("./components/SelectDepartment.vue").default
);

Vue.component(
    "select-business-day",
    require("./components/SelectBusinessDay.vue").default
);

Vue.component(
    "select-holi-day",
    require("./components/SelectHoliDay.vue").default
);

Vue.component("select-user", require("./components/SelectUser.vue").default);

Vue.component(
    "input-datepicker",
    require("./components/InputDatepicker.vue").default
);

Vue.component("user-add", require("./components/UserAdd.vue").default);

Vue.component(
    "create-department",
    require("./components/CreateDepartment.vue").default
);

Vue.component(
    "create-time-table",
    require("./components/CreateTimeTable.vue").default
);

Vue.component(
    "create-calendar",
    require("./components/CreateCalendar.vue").default
);

Vue.component(
    "edit-calendar",
    require("./components/EditCalendar.vue").default
);

Vue.component(
    "edit-work-times",
    require("./components/EditWorkTimes.vue").default
);

Vue.component(
    "create-company-information",
    require("./components/CreateCompanyInformation.vue").default
);

Vue.component("setting-calc", require("./components/SettingCalc.vue").default);

Vue.component("message-data", require("./components/MessageData.vue").default);

Vue.component(
    "worktime-day",
    require("./components/WorkTimeDateTable.vue").default
);

Vue.component(
    "select-employmentstatus",
    require("./components/SelectEmploymentStatus.vue").default
);

Vue.component(
    "col-attendance",
    require("./components/ColAttendance.vue").default
);

Vue.component(
    "col-missingmiddle",
    require("./components/ColMissingMiddle.vue").default
);

Vue.component(
    "col-missingmiddle",
    require("./components/ColMissingMiddle.vue").default
);

// CSV ダウンロードボタン
Vue.component(
    "btn-csv-download",
    require("./components/BtnCsvDownload.vue").default
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
