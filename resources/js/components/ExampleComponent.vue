<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Example Component</div>

                    <div class="card-body">
                        I'm an example component.
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-between">
        <!-- .panel -->
        <div class="col-md pt-3">
            <div class="card shadow-pl">
                <div class="card-body pt-2">
                    <!-- panel contents -->
                    <!-- .row -->
                    <div class="row justify-content-between">
                        <!-- .col -->
                        <div class="col-md-6 pb-2">
                            <div class="input-group">
                            <div class="input-group-prepend">
                                <span
                                class="input-group-text font-size-sm line-height-xs label-width-150"
                                id="basic-addon1"
                                >所属部署</span>
                            </div>
                            <select-departmentlist v-if="showdepartmentlist"
                                ref="selectdepartmentlist"
                                v-bind:blank-data="true"
                                v-bind:placeholder-data="'部署を選択してください'"
                                v-bind:selected-department="selectedDepartmentValue"
                                v-bind:add-new="false"
                                v-bind:date-value="''"
                                v-bind:kill-value="valueDepartmentkillcheck"
                                v-bind:row-index=0
                                v-on:change-event="departmentChanges"
                            ></select-departmentlist>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                    <!-- .row -->
                    <div class="row justify-content-between">
                        <!-- .col -->
                        <div class="col-md-6 pb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label
                                    class="input-group-text font-size-sm line-height-xs label-width-150"
                                    for="target_users"
                                    >氏 　名</label>
                                </div>
                                <select-userlist v-if="showuserlist"
                                    ref="selectuserlist"
                                    v-bind:blank-data="false"
                                    v-bind:placeholder-data="'氏名を選択すると編集モードになります'"
                                    v-bind:selected-value="selectedUserValue"
                                    v-bind:add-new="true"
                                    v-bind:get-do="1"
                                    v-bind:date-value="applytermdate"
                                    v-bind:kill-value="valueUserkillcheck"
                                    v-bind:row-index=0
                                    v-bind:department-value="selectedDepartmentValue"
                                    v-bind:employment-value="''"
                                    v-on:change-event="userChanges"
                                ></select-userlist>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                    <!-- .row -->
                    <div class="row justify-content-between">
                        <!-- .col -->
                        <div class="col-md-12 pb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span
                                    class="input-group-text font-size-sm line-height-xs label-width-180"
                                    id="basic-addon1"
                                    >タイムテーブル選択<span class="color-red">[必須]</span></span>
                                </div>
                                <select-timetablelist v-if="showlist"
                                    ref="selecttimetablelist"
                                    v-bind:blank-data="false"
                                    v-bind:placeholder-data="'タイムテーブルを選択すると編集モードになります'"
                                    v-bind:setting-value="selectedValue"
                                    v-bind:add-new="true"
                                    v-bind:date-value="''"
                                    v-bind:kill-value="valuekillcheck"
                                    v-bind:row-index=0
                                    v-on:change-event="itemChanges"
                                ></select-timetablelist>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                    <!-- .row -->
                    <div class="row justify-content-between">
                        <!-- .col -->
                        <div class="col-md-6 pb-2">
                            <div class="input-group">
                            <div class="input-group-prepend">
                                <label
                                class="input-group-text font-size-sm line-height-xs label-width-120"
                                for="target_employmentstatus"
                                >雇用形態</label>
                            </div>
                            <select-employmentstatuslist
                                ref="selectemploymentstatuslist"
                                v-bind:blank-data="true"
                                v-bind:placeholder-data="'雇用形態を選択してください'"
                                v-bind:selected-value="valueemploymentstatus"
                                v-on:change-event="employmentChanges"
                            ></select-employmentstatuslist>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                    <!-- .row -->
                    <div class="row justify-content-between">
                        <!-- .col -->
                        <div class="col-md-6 pb-2">
                            <div class="input-group">
                            <div class="input-group-prepend">
                                <label
                                class="input-group-text font-size-sm line-height-xs label-width-120"
                                for="target_employmentstatus"
                                >集計区分</label>
                            </div>
                            <select-generallist
                                v-bind:blank-data="false"
                                v-bind:placeholder-data="'集計区分を選択してください'"
                                v-bind:selected-value="selecteTallyvalue"
                                v-bind:add-new="false"
                                v-bind:get-do="'1'"
                                v-bind:date-value="''"
                                v-bind:kill-value="false"
                                v-bind:row-index="'0'"
                                v-bind:identification-id="'C016'"
                                v-on:change-event="displayChange"
                            ></select-generallist>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                    <!-- .row -->
                    <div class="row justify-content-between">
                        <!-- .col -->
                        <div class="col-md-12 pb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span
                                    class="input-group-text font-size-sm line-height-xs label-width-180"
                                    id="basic-addon1"
                                    >ＸＸＸＸＸＸ選択<span class="color-red">[必須]</span></span>
                                </div>
                                <select-generallist v-if="showxxxxxxlist"
                                    ref="selectxxxxxxlist"
                                    v-bind:blank-data="false"
                                    v-bind:placeholder-data="'ＸＸＸＸＸＸを選択すると編集モードになります'"
                                    v-bind:setting-value="selectedXXXXXXValue"
                                    v-bind:add-new="true"
                                    v-bind:date-value="''"
                                    v-bind:kill-value="valueXXXXXXkillcheck"
                                    v-bind:row-index=0
                                    v-on:change-event="XXXXXXChanges"
                                ></select-generallist>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                    <!-- /panel contents -->
                </div>
            </div>
        </div>
        <!-- ./panel -->
    </div>
</template>

<script>
    import toasted from "vue-toasted";
    import moment from "moment";
    import {dialogable} from '../mixins/dialogable.js';
    import {checkable} from '../mixins/checkable.js';
    import {requestable} from '../mixins/requestable.js';
    export default {
        name: "UserAdd",
        mixins: [ dialogable, checkable, requestable ],
        data() {
            return {
                getDo : 1,
                selectedDepartmentValue : "",
                valueDepartmentkillcheck : false,
                showdepartmentlist: true,
                selectedTimetableValue : "",
                valueTimetablekillcheck : false,
                valuefromdate: "",
                applytermdate: "",
                getDo : 1,
                departmentcode : null,
                employmentcode : null
            };
        },
        // マウント時
        mounted() {
            // 氏名リスト取得
            this.getUserList('');
            // 部署リスト取得
            this.getDepartmentList('');
            // タイムテーブルリスト取得
            this.getTimeTableList('');
        },
        methods: {
            // 指定日付が変更された場合の処理
            fromdateChanges: function(value) {
            moment.locale("ja");
            this.valuefromdate = value;
            // 再取得
            this.applytermdate = ""
            if (this.valuefromdate) {
                this.applytermdate = moment(this.valuefromdate).format("YYYYMMDD");
            }
            this.$refs.selectdepartment.getList(this.applytermdate);
            this.getUserSelected();
            },
            // 部署選択が変更された場合の処理
            departmentChanges: function(value, arrayitem) {
                this.selectedDepartmentValue = value;
            },
            // タイムテーブル選択が変更された場合の処理
            departmentChanges: function(value, arrayitem) {
                this.selectedTimetableValue = value;
            },
            // 氏名選択リスト取得処理
            getUserList(targetdate){
                if (targetdate == '') {
                    targetdate = moment(new Date()).format("YYYYMMDD");
                }
                this.postRequest("/get_user_list",
                    { targetdate: targetdate,
                        killvalue: this.valueDepartmentkillcheck,
                        getDo : this.getDo,
                        departmentValue : this.selectedDepartmentValue,
                        employmentcode : this.employmentValue
                    })
                    .then(response  => {
                        this.getThen(response);
                    })
                    .catch(reason => {
                        var messages = [];
                        messages.push("氏名選択リスト作成エラー");
                        this.messageswal("エラー", messages, "error", true, false, true);
                    });
            },
            // 部署選択リスト取得処理
            getDepartmentList(targetdate){
                if (targetdate == '') {
                    targetdate = moment(new Date()).format("YYYYMMDD");
                }
                this.postRequest("/get_departments_list",
                    { targetdate: targetdate,
                        killvalue: this.valueDepartmentkillcheck
                    })
                    .then(response  => {
                        this.getThen(response);
                    })
                    .catch(reason => {
                        this.serverCatch(response);
                    });
            },
            // タイムテーブル選択リスト取得処理
            getTimeTableList(targetdate) {
                if (targetdate == '') {
                    targetdate = moment(new Date()).format("YYYYMMDD");
                }
                this.postRequest("/get_time_table_list",
                    { targetdate: targetdate,
                        killvalue: this.killValue
                    })
                    .then(response  => {
                        this.getThen(response);
                    })
                    .catch(reason => {
                        this.serverCatch("取得");
                    });
            },
            // コード選択リスト取得処理
            getGeneralList(value) {
                var arrayParams = { identificationid : value };
                this.postRequest("/get_general_list", arrayParams)
                    .then(response  => {
                        if (value == "C009") {
                            this.getThentimeUnit(response, "時間単位");
                        }
                        if (value == "C010") {
                            this.getThentimeRounding(response, "時間の丸め");
                        }
                    })
                    .catch(reason => {
                        if (value == "C009") {
                            this.serverCatch("時間単位", "取得");
                        }
                        if (value == "C010") {
                            this.serverCatch("時間の丸め", "取得");
                        }
                    });
            },
            // 取得正常処理
            getThen(response) {
                this.details = [];
                var res = response.data;
                if (res.result) {
                    // 固有処理 START
                    this.details = res.details;
                    this.count = this.details.length;
                    this.before_count = this.count;
                    // 固有処理 end
                } else {
                    if (res.messagedata.length > 0) {
                        this.messageswal("エラー", res.messagedata, "error", true, false, true);
                    } else {
                        this.serverCatch("取得");
                    }
                }
            },
            // 更新系正常処理
            putThenHead(response, eventtext) {
                var messages = [];
                var res = response.data;
                if (res.result) {
                    // 固有処理 START
                    messages.push("部署を" + eventtext + "しました");
                    this.messageswal(eventtext + "完了", messages, "success", true, false, true);
                    this.refreshItemList();
                    this.form.code = res.code;
                    // 固有処理 end
                } else {
                    if (res.messagedata.length > 0) {
                        this.messageswal("警告", res.messagedata, "warning", true, false, true);
                    } else {
                        this.serverCatch(eventtext);
                    }
                }
            },
            // 更新系正常処理（明細）
            putThenDetail(response, eventtext) {
                var messages = [];
                var res = response.data;
                if (res.result) {
                    // 固有処理 START
                    messages.push("部署を" + eventtext + "しました");
                    this.messageswal(eventtext + "完了", messages, "success", true, false, true);
                    this.refreshItemList();
                    this.getItem();
                    this.count = this.details.length;
                    this.before_count = this.count;
                    // 固有処理 end
                } else {
                    if (res.messagedata.length > 0) {
                        this.messageswal("警告", res.messagedata, "warning", true, false, true);
                    } else {
                        this.serverCatch(eventtext);
                    }
                }
            },
            // 異常処理
            serverCatch(eventtext) {
                var messages = [];
                messages.push("部署情報" + eventtext + "に失敗しました");
                this.messageswal("エラー", messages, "error", true, false, true);
            },
            refreshtDepartmentList() {
                // 最新リストの表示
                this.showdepartmentlist = false;
                this.$nextTick(() => (this.showdepartmentlist = true));
            }
        }
</script>
