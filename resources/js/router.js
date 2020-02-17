import Vue from "vue";
import VueRouter from "vue-router";

// ページコンポーネントをインポートする
import DailyWorkingInformation from "./pages/DailyWorkingInformation.vue";
import MonthlyWorkingInformation from "./pages/MonthlyWorkingInformation.vue";

// VueRouterプラグインを使用する
// これによって<RouterView />コンポーネントなどを使うことができる
Vue.use(VueRouter);

// パスとコンポーネントのマッピング
const routes = [
    {
        path: "/",
        component: DailyWorkingInformation
    },
    {
        path: "/monthly",
        component: MonthlyWorkingInformation
    }
];

// VueRouterインスタンスを作成する
const router = new VueRouter({
    mode: "history", // ★ 追加
    routes
});

// VueRouterインスタンスをエクスポートする
// app.jsでインポートするため
export default router;
