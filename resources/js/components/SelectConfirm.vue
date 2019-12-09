<template>
  <select class="form-control" v-model="confirm" v-on:change="selChanges(confirm)">
    <option disabled selected style="display:none;" v-if="this.placeholderData" value="">＜{{ placeholderData }}＞</option>
    <option v-if="this.blankData" value=""></option>
    <option v-for="item in confirmList" v-bind:value="item.user_code">
      {{ item.list_name }}
    </option>
  </select>
</template>
<script>
import moment from "moment";

export default {
  name: "selectConfirm",
  props: {
    blankData: {
        type: Boolean,
        default: false
    },
    getDo: {
        type: Number,
        default: 1
    },
    orFinal: {
        type: String,
        default: ""
    },
    mainOrsub: {
        type: String,
        default: "main"
    },
    selectedConfirm: {
        type: String,
        default: ''
    },
    dateValue: {
        type: String,
        default: ''
    },
    placeholderData: {
        type: String,
        default: ''
    }
  },
  data() {
    return {
      confirm: '',
      dateApllyValue: '',
      confirmList: []
    };
  },
  // マウント時
  mounted() {
    this.confirm = this.selectedConfirm;
    if (this.dateValue == '') {
      this.dateApllyValue = moment(new Date()).format("YYYYMMDD");
    }
    this.getConfirmList(this.getDo, this.dateApllyValue, this.confirm, this.orFinal, this.mainOrsub);
  },
  methods: {
    // 承認者一覧取得
    getConfirmList(getdovalue, datevalue, confirmvalue, orFinalvalue, mainorsubvalue){
      this.confirm = confirmvalue;
      this.confirmList = [];
      this.$axios
        .get("get_confirm_list", {
          params: {
            getdo: getdovalue,
            targetdate: datevalue,
            orFinal: orFinalvalue,
            mainorsub: mainorsubvalue
          }
        })
        .then(response => {
          this.confirmList = response.data;
        })
        .catch(reason => {
          this.alert("error", "承認者一覧取得に失敗しました", "エラー");
        });
    },
    // 選択が変更された場合、親コンポーネントに選択値を返却
    selChanges : function(value) {
      this.$emit('change-event', value);

    }

  }
};
</script>
