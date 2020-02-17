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
            okbtn,
            cancelbtn,
            dangerMode
        ) {
            this.messagedata = this.arrayTostring(arrayMessage);
            let self = this;
            return new Promise(function(resolve, reject) {
                self.$swal({
                    title: title,
                    text: self.messagedata,
                    icon: icon,
                    buttons: {
                        cancel: cancelbtn,
                        ok: okbtn
                    },
                    allowOutsideClick: false, //枠外をクリックしても画面を閉じない
                    grow: "fullscreen",
                    dangerMode: dangerMode
                }).then(result => {
                    resolve(result);
                });
            });
        },
        // メッセージ表示
        htmlMessageSwal: function(
            title,
            html,
            icon,
            okbtn,
            cancelbtn,
            dangerMode
        ) {
            let self = this;
            return new Promise(function(resolve, reject) {
                self.$swal({
                    title: title,
                    text: html,
                    icon: icon,
                    buttons: {
                        cancel: cancelbtn,
                        ok: okbtn
                    },
                    allowOutsideClick: false, //枠外をクリックしても画面を閉じない
                    grow: "fullscreen",
                    dangerMode: dangerMode,
                    html: true
                }).then(result => {
                    resolve(result);
                });
            });
        },
        // エラー個数メッセージ表示
        countswal(title, arrayMessage, icon, okbtn, cancelbtn, dangerMode) {
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
                    buttons: {
                        cancel: cancelbtn,
                        ok: okbtn
                    },
                    allowOutsideClick: false, //枠外をクリックしても画面を閉じない
                    grow: "fullscreen",
                    dangerMode: dangerMode
                }).then(result => {
                    resolve(result);
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
                    text: self.messagedata,
                    icon: icon,
                    allowOutsideClick: false, //枠外をクリックしても画面を閉じない
                    showConfirmButton: false,
                    grow: "fullscreen",
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    }
                });
            });
        },
        // -------------------------- private メソッド ------------------------
        // 配列→String改行
        arrayTostring(arrayMessage) {
            var stringdata = arrayMessage[0];
            for (var i = 1; i < arrayMessage.length; i++) {
                stringdata += "\n" + arrayMessage[i];
            }
            return stringdata;
        }
    }
};
