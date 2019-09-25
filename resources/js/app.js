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
import Vue from "vue";
import VueSwal from "vue-swal";

var options = {
    position: "bottom-center",
    duration: 2000,
    fullWidth: false,
    type: "info"
};
Vue.use(toasted, options);
Vue.use(VCalendar);
Vue.use(VModal);
Vue.use(VueSwal);

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
    "daily-working-info-table",
    require("./components/DailyWorkingInfoTable.vue").default
);

Vue.component(
    "daily-working-alert",
    require("./components/DailyWorkingAlert.vue").default
);

Vue.component(
    "monthly-working-information",
    require("./components/MonthlyWorkingInformation.vue").default
);

Vue.component(
    "monthly-working-info-table",
    require("./components/MonthlyWorkingInfoTable.vue").default
);

Vue.component(
    "monthly-working-alert",
    require("./components/MonthlyWorkingAlert.vue").default
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
    "btn-work-time",
    require("./components/BtnWorkTime.vue").default
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

Vue.component("input-ym", require("./components/InputDateYm.vue").default);

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
    "setting-calendar",
    require("./components/SettingCalendar.vue").default
);

Vue.component(
    "init-calendar",
    require("./components/InitCalendar.vue").default
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
    "col-employmentstatus",
    require("./components/ColEmploymentStatus.vue").default
);

Vue.component(
    "col-regularworking",
    require("./components/ColRegularWorking.vue").default
);

Vue.component(
    "col-overtimehours",
    require("./components/ColOvertimeHours.vue").default
);

Vue.component(
    "col-notemploymentworking",
    require("./components/ColNotEmploymentWorking.vue").default
);

Vue.component("col-note", require("./components/ColNote.vue").default);

Vue.component(
    "general-list",
    require("./components/SelectGeneralList.vue").default
);

// CSV ダウンロードボタン
Vue.component(
    "btn-csv-download",
    require("./components/BtnCsvDownload.vue").default
);

Vue.component(
    "daily-working-alert-table",
    require("./components/DailyWorkingAlertTable.vue").default
);

Vue.component(
    "monthly-working-alert-table",
    require("./components/MonthlyWorkingAlertTable.vue").default
);

Vue.component(
    "message-waiting",
    require("./components/MessageWaiting.vue").default
);

Vue.component(
    "message-data-server",
    require("./components/MessageDataServer.vue").default
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
