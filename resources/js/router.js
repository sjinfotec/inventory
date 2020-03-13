import Vue from "vue";
import VueRouter from "vue-router";
import axios from "axios";
// VueRouterプラグインを使用する
// これによって<RouterView />コンポーネントなどを使うことができる
Vue.use(VueRouter);

// パスとコンポーネントのマッピング 今後vue-routerで画面遷移する場合使用
// const routes = [
//     {
//         path: "/",
//         component: DailyWorkingInformation
//     },
//     {
//         path: "/monthly",
//         component: MonthlyWorkingInformation
//     }
// ];

// VueRouterインスタンスを作成する
const router = new VueRouter({
    mode: "history"
    // routes
});

// 全てのaxiosでのエラーをcatchします
// 419 : ユーザー認証エラー(セッション切れ等)
axios.interceptors.response.use(null, error => {
    if (error.response.status == 419) {
        document.location = "/login";
    }
    // return Promise.reject(error);
});

// VueRouterインスタンスをエクスポートする
// app.jsでインポートするため
export default router;
