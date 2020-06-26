<template>
  <datepicker 
    :language="ja"
    :format="dateFormat"
    :clear-button="isclearButton"
    v-model="defaultDate"
    v-on:closed="dateclosed()"
    v-on:cleared="datecleared()"
    :placeholder="placeHolder"
    :disabled="isDisable"
    >
  </datepicker>
</template>

<script>
import Datepicker from "vuejs-datepicker";
import {ja} from 'vuejs-datepicker/dist/locale';

export default {
  name: "inputDatePicker",
  props: {
    defaultDate: {
      type: Date
    },
    placeHolder: {
      type: String,
      default: '日付を選択してください'
    },
    dateFormat: {
      type: String,
      default: 'yyyy年MM月dd日'
    },
    isclearButton: {
      type: Boolean,
      default: true
    },
    isDisable: {
      type: Boolean,
      default: false
    }
  },
  // マウント時
  mounted() {
    console.log("inputDatePicker Component mounted.");
  },
  data: function() {
    return {
      ja:ja
    }
  },
  components: {
    Datepicker
  },
  methods: {
    // 親コンポーネントに入力値を返却
    dateclosed : function() {
      this.$emit('change-event', this.defaultDate);
    },
    // 親コンポーネントに入力値を返却
    datecleared : function() {
      this.$emit('clear-event');
    },
  }

};
</script>
