// CONST
// const C_ROOT_URL = "http://onefr.onedawn.net/";
const C_ROOT_URL = "http://192.168.0.47/";

export const dialogable = {

    data() {
        return {
            messagedata: ""
        };
    },


    methods: {
        // -------------------------- public メソッド ------------------------
        // メッセージ表示
        messageswal: function(
            title,
            arrayMessage,
            icon,
            showConfirmButton,
            showCancelButton
        ) {
            this.messagedata = this.arrayTostring(arrayMessage);
            let self = this;
            return new Promise(function(resolve, reject) {
                self.$swal({
                    title: title,
                    text: self.messagedata,
                    icon: icon,
                    showCancelButton: showCancelButton,
                    showConfirmButton: showConfirmButton,
                    allowOutsideClick: false //枠外をクリックしても画面を閉じない
                }).then(result => {
                    resolve(result.value);
                });
            });
        },
        // メッセージ表示
        htmlMessageSwal: function(
            title,
            arrayMessage,
            icon,
            showConfirmButton,
            showCancelButton
        ) {
            this.messagedata = this.arrayTostring(arrayMessage);
            let self = this;
            // html: '<p style="text-align: left">' + self.messagedata + '</p>',
            return new Promise(function(resolve, reject) {
                self.$swal({
                    title: title,
                    html: self.messagedata,
                    icon: icon,
                    showCancelButton: showCancelButton,
                    showConfirmButton: showConfirmButton,
                    width: "42rem",
                    allowOutsideClick: false, //枠外をクリックしても画面を閉じない
                }).then(result => {
                    resolve(result.value);
                });
            });
        },
        // メッセージ表示
        htmlMessageSwalLink: function(
            title,
            arrayMessage,
            icon,
            showConfirmButton,
            showCancelButton,
            tourl
        ) {
            this.messagedata = this.arrayTostring(arrayMessage);
            let self = this;
            // html: '<p style="text-align: left">' + self.messagedata + '</p>',
            return new Promise(function(resolve, reject) {
                self.$swal({
                    title: title,
                    html: self.messagedata,
                    icon: icon,
                    showCancelButton: showCancelButton,
                    showConfirmButton: showConfirmButton,
                    width: "42rem",
                    allowOutsideClick: false, //枠外をクリックしても画面を閉じない
                    footer: tourl,
                }).then(result => {
                    resolve(result.value);
                });
            });
        },
        // エラー個数メッセージ表示
        countswal(title, arrayMessage, icon, showConfirmButton, showCancelButton, dangerMode) {
            if (icon == "warning") {
                this.messagedata =
                    arrayMessage.length + "個の警告メッセージがあります。";
            } else if (icon == "error") {
                this.messagedata =
                    arrayMessage.length + "個のエラーメッセージがあります。";
            } else if (icon == "success") {
                this.messagedata =
                    arrayMessage.length + "個のメッセージがあります。";
            } else if (icon == "info") {
                this.messagedata =
                    arrayMessage.length + "個のメッセージがあります。";
            }
            let self = this;
            return new Promise(function(resolve, reject) {
                self.$swal({
                    title: title,
                    text: self.messagedata,
                    icon: icon,
                    showCancelButton: showCancelButton,
                    showConfirmButton: showConfirmButton,
                    allowOutsideClick: false //枠外をクリックしても画面を閉じない
                }).then(result => {
                    resolve(result.value);
                });
            });
        },
        // 処理中メッセージ表示
        waitswal: function(title, arrayMessage, icon) {
            this.messagedata = this.arrayTostring(arrayMessage);
            let self = this;
            return new Promise(function(resolve, reject) {
                self.$swal({
                    title: title,
                    html: self.messagedata,
                    allowOutsideClick: false, //枠外をクリックしても画面を閉じない
                    showConfirmButton: false,
                    onBeforeOpen: () => {
                        self.$swal.showLoading();
                    }
                });
                // self.$swal({
                //     title: title,
                //     text: self.messagedata,
                //     icon: icon,
                //     allowOutsideClick: false, //枠外をクリックしても画面を閉じない
                //     showConfirmButton: false,
                //     grow: "fullscreen",
                //     onBeforeOpen: () => {
                //         Swal.showLoading();
                //     }
                // });
            });
        },
        // -------正常処理（インストールダウンロード処理）
        getThenDownload: function() {
            var settingmessage = [];
            settingmessage.push(
            "打刻端末の打刻プログラムをダウンロードしてインストールします。"
            );
            this.htmlMessageSwalLink("通知",
            settingmessage,
            "info",
            false,
            true,
            '<a href=' + C_ROOT_URL + 'file_download' + ">インストールする</a>")
        },
        // 取得正常処理（会社情報処理）
        getThenCompany: function(
            settingdepartments,
            settingsettings,
            settingworkingtimetables,
            settingusers,
            settingcalendarsettinginformations
        ) {
            var settingmessage = [];
            settingmessage.push(
            "会社情報を設定する必要がありますので会社を設定します。"
            );
            this.htmlMessageSwalLink("通知",
            settingmessage,
            "info",
            false,
            true,
            '<a href=' + C_ROOT_URL + 'create_company_information' + ">会社を設定する</a>")
            .then(result  => {
                if (!result) {
                    if (settingdepartments == 0) {
                        this.getThenDepartment();
                    } else if (settingsettings == 0) {
                        this.getThenSetting();
                    } else if (settingworkingtimetables == 0) {
                        this.getThenWorkingtimetables();
                    } else if (settingusers == 0) {
                        this.getThenUsers();
                    } else if (settingcalendarsettinginformations == 0) {
                        this.getThenCalendarSettingInfos();
                    }
                }
            });
        },
        // 部署取得正常処理
        getThenDepartment: function(
            settingsettings,
            settingworkingtimetables,
            settingusers,
            settingcalendarsettinginformations
        ) {
            var settingmessage = [];
            settingmessage.push(
                "部署情報を設定する必要がありますので部署を設定します。"
            );
            this.htmlMessageSwalLink("通知",
            settingmessage,
            "info",
            false,
            true,
            '<a href=' + C_ROOT_URL + 'create_department' + ">部署を設定する</a>")
            .then(result  => {
                if (!result) {
                    if (settingsettings == 0) {
                        this.getThenSetting();
                    } else if (settingworkingtimetables == 0) {
                        this.getThenWorkingtimetables();
                    } else if (settingusers == 0) {
                        this.getThenUsers();
                    } else if (settingcalendarsettinginformations == 0) {
                        this.getThenCalendarSettingInfos();
                    }
                }
            });
        },
        // 設定情報正常処理
        getThenSetting: function(
            settingworkingtimetables,
            settingusers,
            settingcalendarsettinginformations
        ) {
            var settingmessage = [];
            settingmessage.push(
            "労働時間の基本設定する必要がありますので基本設定します。"
            );
            this.htmlMessageSwalLink("通知",
            settingmessage,
            "info",
            false,
            true,
            '<a href=' + C_ROOT_URL + 'setting_calc' + ">労働時間基本を設定する</a>")
            .then(result  => {
                if (!result) {
                    if (settingworkingtimetables == 0) {
                        this.getThenWorkingtimetables();
                    } else if (settingusers == 0) {
                        this.getThenUsers();
                    } else if (settingcalendarsettinginformations == 0) {
                        this.getThenCalendarSettingInfos();
                    }
                }
            });
        },
        // 設定情報正常処理
        getThenWorkingtimetables: function(
            settingusers,
            settingcalendarsettinginformations
        ) {
            var settingmessage = [];
            settingmessage.push(
            "勤務帯の時間設定する必要がありますので設定します。"
            );
            this.htmlMessageSwalLink("通知",
            settingmessage,
            "info",
            false,
            true,
            '<a href=' + C_ROOT_URL + 'create_time_table' + ">勤務帯時間を設定する</a>")
            .then(result  => {
                if (!result) {
                    if (settingusers == 0) {
                        this.getThenUsers();
                    } else if (settingcalendarsettinginformations == 0) {
                        this.getThenCalendarSettingInfos();
                    }
                }
            });
        },
        // ユーザー情報正常処理
        getThenUsers: function(
            settingcalendarsettinginformations
        ) {
            var settingmessage = [];
            settingmessage.push(
            "ユーザー情報の設定する必要がありますので設定します。"
            );
            this.htmlMessageSwalLink("通知",
            settingmessage,
            "info",
            false,
            true,
            '<a href=' + C_ROOT_URL + 'edit_user' + ">ユーザー情報を設定する</a>")
            .then(result  => {
                if (!result) {
                    if (settingcalendarsettinginformations == 0) {
                        this.getThenCalendarSettingInfos();
                    }
                }
            });
        },
        // カレンダー情報正常処理
        getThenCalendarSettingInfos: function(
        ) {
            var settingmessage = [];
            settingmessage.push(
            "ユーザーのカレンダーを設定する必要がありますので設定します。"
            );
            this.htmlMessageSwalLink("通知",
            settingmessage,
            "info",
            false,
            true,
            '<a href=' + C_ROOT_URL + 'setting_calendar' + ">カレンダー情報を設定する</a>")
            ;
        },
        // -------------------------- private メソッド ------------------------
        // 配列→String改行
        arrayTostring(arrayMessage) {
            var stringdata = arrayMessage[0].replace(/\n/g, '<br/>');
            for (var i = 1; i < arrayMessage.length; i++) {
                stringdata += "<br>" + arrayMessage[i].replace(/\n/g, '<br/>');
            }
            return stringdata;
        }
    }
};
