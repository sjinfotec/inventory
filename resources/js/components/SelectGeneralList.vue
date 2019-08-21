<template>
  <select class="form-control" v-model="selectGenerallist" v-on:change="selChanges(selectGenerallist)" placeholder="placeholderData">
    <option v-if="this.blankData" value=""></option>
    <option v-for="generallists in generalList" v-bind:value="generallists.code">
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
      selectGenerallist: '',
      generalList: []
    };
  },
  // マウント時
  mounted() {
    console.log("SelectGeneralList mounted ");
    this.getGeneralList();
  },
  methods: {
    getGeneralList(){
      console.log("getGeneralList in ");
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

        this.$emit('change-event', value);

    }

  }
};
</script>
