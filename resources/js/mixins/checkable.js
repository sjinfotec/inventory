export const checkable =  {
  data() {
    return {
      checkmessagedata: []
    };
  },
  methods:{
    // -------------------------- public メソッド ------------------------
    // チェック（ヘッダ）
    checkHeader(value, required, equalength, maxlength, itemname) {
      this.checkmessagedata = [];
      if (required) {
        if (value == "") {
          this.checkmessagedata.push(itemname + "を入力してください");
        }
      }
      if (equalength > 0) {
        if (value.length != equalength) {
          this.checkmessagedata.push(itemname + "は" + equalength + "桁で入力してください");
        }
      }
      if (maxlength > 0) {
        if (value.length > maxlength) {
          this.checkmessagedata.push(itemname + "は" + maxlength + "文字数以内で入力してください");
        }
      }
      return this.checkmessagedata;
    },
    // チェック（明細）
    checkDetail(value, required, equalength, maxlength, itemname, rowindex) {
      this.checkmessagedata = [];
      if (required) {
        if (value == "") {
          this.checkmessagedata.push("No." + rowindex + "の" + itemname + "を入力してください");
        }
      }
      if (equalength > 0) {
        if (value.length != equalength) {
          this.checkmessagedata.push("No." + rowindex + "の" + itemname + "は" + equalength + "桁で入力してください");
        }
      }
      if (maxlength > 0) {
        if (value.length > maxlength) {
          this.checkmessagedata.push("No." + rowindex + "の" + itemname + "は" + maxlength + "文字数以内で入力してください");
        }
      }
      return this.checkmessagedata;
    }
    // -------------------------- private メソッド ------------------------
  }
}
