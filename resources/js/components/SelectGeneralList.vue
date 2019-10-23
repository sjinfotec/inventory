<template>
  <select class="form-control" v-model="selectedvalue" v-on:change="selChanges(selectedvalue)">
    <option selected v-if="this.blankData" value="" disabled selected>＜{{ placeholderData }}＞</option>
    <option v-for="(generallists, index) in generalList" v-bind:value="generallists.code">
      {{ generallists.code_name }}
    </option>
  </select>
</template>
<script>

export default {
  name: "SelectGeneralList",
  props: {
    identificationId: {
        type: String,
        default: ''
    },
    placeholderData: {
        type: String,
        default: ''
    },
    blankData: {
        type: Boolean,
        default: false
    }
  },
  data() {
    return {
      selectedvalue: '',
      selectedname: '',
      generalList: []
    };
  },
  // マウント時
  mounted() {
    this.getGeneralList();
  },
  methods: {
    getGeneralList(){
      this.$axios
        .get("/get_general_list", {
          params: {
            identificationid: this.identificationId
          }
        })
        .then(response => {
          this.generalList = response.data;
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
      this.generalList.forEach(function (item) {
        if (item.code === value) {
          name = item.code_name;
          return name;
        }
      });
      return name;
    }

  }
};
</script>
