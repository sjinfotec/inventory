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
            return new Promise(function(resolve, reject) {
                self.$swal({
                    title: title,
                    html: self.messagedata,
                    icon: icon,
                    showCancelButton: showCancelButton,
                    showConfirmButton: showConfirmButton,
                    allowOutsideClick: false, //枠外をクリックしても画面を閉じない
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
        // -------------------------- private メソッド ------------------------
        // 配列→String改行
        arrayTostring(arrayMessage) {
            var stringdata = arrayMessage[0];
            for (var i = 1; i < arrayMessage.length; i++) {
                stringdata += "<br>" + arrayMessage[i];
            }
            return stringdata;
        }
    }
};
