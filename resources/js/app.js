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
import VCalendar from "v-calendar";
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
Vue.use(VCalendar);
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

Vue.component(Dialog.name, Dialog);
Vue.component(Select.name, Select);
Vue.component(Option.name, Option);
Vue.component(TimePicker.name, TimePicker);
Vue.component(Button.name, Button);

// ホーム画面
Vue.component(
    "home-component",
    require("./components/HomeComponent.vue").default
);

// 日次集計
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
    "daily-working-info-time-table",
    require("./components/DailyWorkingInfoTimeTable.vue").default
);
Vue.component(
    "daily-working-info-table-print",
    require("./components/DailyWorkingInfoTablePrint.vue").default
);
Vue.component(
    "daily-working-info-sum-table",
    require("./components/DailyWorkingInfoSumTable.vue").default
);
// 日次警告
Vue.component(
    "daily-working-alert",
    require("./components/DailyWorkingAlert.vue").default
);

Vue.component(
    "daily-working-alert-table",
    require("./components/DailyWorkingAlertTable.vue").default
);

// 月次集計
Vue.component(
    "monthly-working-information",
    require("./components/MonthlyWorkingInformation.vue").default
);

Vue.component(
    "monthly-working-info-table",
    require("./components/MonthlyWorkingInfoTable.vue").default
);

// 月次警告
Vue.component(
    "monthly-working-alert",
    require("./components/MonthlyWorkingAlert.vue").default
);

Vue.component(
    "monthly-working-alert-table",
    require("./components/MonthlyWorkingAlertTable.vue").default
);

// 勤怠ログ
Vue.component(
    "attendance-log",
    require("./components/AttendanceLog.vue").default
);

// -------------------------- 編集 ----------------------------------------------
// シフト編集
// Vue.component(
//     "setting-shift-time",
//     require("./components/SettingShiftTime.vue").default
// );
Vue.component(
    "edit-shift-time",
    require("./components/EditShiftTime.vue").default
);
Vue.component(
    "table-shift-time",
    require("./components/TableShiftTime.vue").default
);
// 未使用
// Vue.component(
//     "create-shift-time",
//     require("./components/CreateShiftTime.vue").default
// );
// 勤怠編集
Vue.component(
    "edit-work-times",
    require("./components/EditWorkTimes.vue").default
);
// 勤怠編集テーブル
Vue.component(
    "edit-work-times-table",
    require("./components/EditWorkTimesTable.vue").default
);

// -------------------------- 設定 ----------------------------------------------
// 会社設定
Vue.component(
    "create-company-information",
    require("./components/CreateCompanyInformation.vue").default
);
// 組織設定
Vue.component(
    "create-department",
    require("./components/CreateDepartment.vue").default
);
// 労働時間基本設定
Vue.component("setting-calc", require("./components/SettingCalc.vue").default);
// タイムテーブル設定
Vue.component(
    "create-time-table",
    require("./components/CreateTimeTable.vue").default
);
// カレンダー設定
Vue.component(
    "setting-calendar",
    require("./components/SettingCalendar.vue").default
);
Vue.component(
    "init-calendar",
    require("./components/InitCalendar.vue").default
);
// 未使用
// Vue.component(
//     "edit-calendar",
//     require("./components/EditCalendar.vue").default
// );
// ユーザー情報設定
Vue.component("edit-user", require("./components/EditUser.vue").default);
// 未使用
// Vue.component("user-add", require("./components/UserAdd.vue").default);

// -------------------------- 操作 ----------------------------------------------
// パスワード変更
Vue.component("user-pass", require("./components/UserPass.vue").default);
// ダウンロード
Vue.component(
    "file-download",
    require("./components/FileDownload.vue").default
);

// -------------------------- 共通 ----------------------------------------------
// ボタン類
Vue.component("btn-work-time", require("./components/BtnWorkTime.vue").default);

Vue.component(
    "input-datepicker",
    require("./components/InputDatepicker.vue").default
);

Vue.component(
    "input-datepicker-disabled",
    require("./components/InputDatepickerDisabled.vue").default
);

Vue.component(
    "input-eltimepicker",
    require("./components/InputElTimepicker.vue").default
);

Vue.component("input-ym", require("./components/InputDateYm.vue").default);

Vue.component("input-time", require("./components/InputTime.vue").default);

Vue.component(
    "input-time-disabled",
    require("./components/InputTimeDisabled.vue").default
);

Vue.component("edit-user", require("./components/EditUser.vue").default);

Vue.component(
    "create-department",
    require("./components/CreateDepartment.vue").default
);

Vue.component(
    "create-time-table",
    require("./components/CreateTimeTable.vue").default
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

// 勤怠編集
Vue.component(
    "edit-work-times",
    require("./components/EditWorkTimes.vue").default
);
// 勤怠編集テーブル
Vue.component(
    "edit-work-times-table",
    require("./components/EditWorkTimesTable.vue").default
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

// CSV ダウンロードボタン
Vue.component(
    "btn-csv-download",
    require("./components/BtnCsvDownload.vue").default
);

Vue.component(
    "message-waiting",
    require("./components/MessageWaiting.vue").default
);

Vue.component(
    "message-data-server",
    require("./components/MessageDataServer.vue").default
);
// 申請
//  承認ルート作成
Vue.component(
    "create-approvalroot",
    require("./components/CreateApprovalRouteNo.vue").default
);
Vue.component("setting-root", require("./components/SettingRoot.vue").default); // 削除予定

Vue.component("make-demand", require("./components/MakeDemand.vue").default);

Vue.component(
    "make-approval",
    require("./components/MakeApproval.vue").default
);

Vue.component(
    "rowbtn-work-time",
    require("./components/RowBtnWorkTime.vue").default
);

Vue.component(
    "working-chart",
    require("./components/WorkingChart.vue").default
);

Vue.component("company-set", require("./components/CompanySet.vue").default);

Vue.component(
    "department-set",
    require("./components/DepartmentSet.vue").default
);

Vue.component(
    "input-timetablepicker",
    require("./components/InputTimeTableElTimepicker.vue").default
);
// テーブル関係
Vue.component(
    "table-calendarmonth",
    require("./components/TableCalendarMonth.vue").default
);

// 選択リスト
Vue.component(
    "select-departmentlist",
    require("./components/SelectDepartmentList.vue").default
);
Vue.component(
    "select-departmentlist-detail",
    require("./components/SelectDepartmentListDetail.vue").default
);

Vue.component(
    "select-timetablelist",
    require("./components/SelectTimetableList.vue").default
);

Vue.component("select-user", require("./components/SelectUser.vue").default);

Vue.component(
    "select-userlist",
    require("./components/SelectUserList.vue").default
);

Vue.component(
    "select-userlist-detail",
    require("./components/SelectUserListDetail.vue").default
);

Vue.component(
    "select-businessdaylist",
    require("./components/SelectBusinessDayList.vue").default
);

Vue.component(
    "select-employmentstatuslist",
    require("./components/SelectEmploymentStatusList.vue").default
);

Vue.component(
    "select-holi-day",
    require("./components/SelectHoliDay.vue").default
);

Vue.component(
    "select-demandno",
    require("./components/SelectDemandNo.vue").default
);

Vue.component(
    "select-comfirm",
    require("./components/SelectConfirm.vue").default
);

Vue.component(
    "select-generallist",
    require("./components/SelectGeneralList.vue").default
);

Vue.component(
    "select-approvalrouteList",
    require("./components/SelectApprovalRouteList.vue").default
);

// 使えないコントロールなので途中で断念した（sizeとか）。使用不可
// Vue.component(
//     "select-elcommonlist",
//     require("./components/SelectElCommonList.vue").default
// );

// 勤怠ログ
// 勤怠ログ登録
Vue.component(
    "store_attendance-log",
    require("./components/StoreAttendanceLog.vue").default
);
// 勤怠ログ編集
Vue.component(
    "edit_attendance-log",
    require("./components/EditAttendanceLog.vue").default
);

// 勤務状況テーブル
Vue.component(
    "table-working-status",
    require("./components/TableWorkingStatus.vue").default
);

// 有給設定
Vue.component(
    "setting-paid-holiday",
    require("./components/SettingPaidHoliday.vue").default
);
// 地図Map
Vue.component(
    "show-map-dialog",
    require("./components/ShowMapDialog.vue").default
);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: "#app",
    // render: h => h(App)
    router // ルーティングの定義を読み込む
    // components: { App }, // ルートコンポーネントの使用を宣言する
    // template: "<App />" // ルートコンポーネントを描画する
});
