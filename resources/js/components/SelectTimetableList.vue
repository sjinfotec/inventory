<template>
  <select class="form-control" v-model="selectedvalue" v-on:change="selChanges(selectedvalue)">
    <option disabled selected style="display:none;" v-if="this.placeholderData" value="">＜{{ placeholderData }}＞</option>
    <option selected v-if="this.blankData" value=""></option>
    <option v-for="(item, index) in itemList" v-bind:value="item.no">
      {{ item.name }}
    </option>
  </select>
</template>
<script>

export default {
  name: "SelectCommonlList",
  props: {
    selectedValue: {
        type: Number,
        default: ''
    },
    placeholderData: {
        type: String,
        default: ''
    },
    blankData: {
        type: Boolean,
        default: true
    }
  },
  data() {
    return {
      selectedvalue: 0,
      selectedname: '',
      itemList: []
    };
  },
    // マウント時
  mounted() {
    this.selectedvalue = this.selectedValue;
    this.getList();
  },
  methods: {
    getList(){
      this.$axios
        .get("/get_time_table_list", {
          params: {
          }
        })
        .then(response => {
          this.itemList = response.data;
          if (this.itemList.length != 0) {
            this.object = { apply_term_from: "", name: "新規にタイムテーブルを登録する", no: "" };
          } else {
            this.placeholderData = "はじめに「所定就業時間の登録」を選択してください"
            this.object = { apply_term_from: "", name: "所定就業時間の登録", no: "" };
          }
          this.itemList.unshift(this.object);
        })
        .catch(reason => {
          alert("リスト作成に失敗しました。");
        });
    },
    // 選択が変更された場合、親コンポーネントに選択値を返却
    selChanges : function(value) {
      this.selectedname = this.getText(value);
      this.$emit('change-event', value, this.selectedname);

    },
    // 選択テキスト取得
    getText : function(value) {
      name = "";
      this.itemList.forEach(function (item) {
        if (item.no == value) {
          name = item.name;
          return name;
        }
      });
      return name;
    }

  }
};
</script>
