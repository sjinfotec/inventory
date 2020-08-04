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
      if (value == null) { value = ""; }
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
    // チェック（明細No）
    checkDetail(value, required, equalength, maxlength, itemname, rowindex) {
      this.checkmessagedata = [];
      if (value == null) { value = ""; }
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
    },
    // チェック（明細No以外）
    checkDetailtext(value, required, equalength, maxlength, itemname, strtext) {
      this.checkmessagedata = [];
      if (value == null) { value = ""; }
      if (required) {
        if (value == "") {
          this.checkmessagedata.push(strtext + "の" + itemname + "を入力してください");
        }
      }
      if (equalength > 0) {
        if (value.length != equalength) {
          this.checkmessagedata.push(strtext + "の" + itemname + "は" + equalength + "桁で入力してください");
        }
      }
      if (maxlength > 0) {
        if (value.length > maxlength) {
          this.checkmessagedata.push(strtext + "の" + itemname + "は" + maxlength + "文字数以内で入力してください");
        }
      }
      return this.checkmessagedata;
    },
    // 入力不可チェック（ヘッダ）
    checkHeaderNotEnter(value, itemname, casename) {
      this.checkmessagedata = [];
      if (value != null && value != "") {
        this.checkmessagedata.push(itemname + "は" + casename + "入力できません。");
      }
      return this.checkmessagedata;
    },
    // 入力不可チェック（明細No）
    checkDetailNotEnter(value, itemname, casename, rowindex) {
      this.checkmessagedata = [];
      if (value != null && value != "") {
        this.checkmessagedata.push("No." + rowindex + "の" + itemname + "は" + casename + "入力できません。");
      }
      return this.checkmessagedata;
    }

    // -------------------------- private メソッド ------------------------
  }
}
