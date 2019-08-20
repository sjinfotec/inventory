<template>
  <p class="form-control"><input type="month" name="inputym" v-model="inputValue" v-on:change="selChanges(inputValue)" required></p>
</template>

<script>

export default {
  name: "inputDateYm",
  data() {
    return {
      inputValue:''
    };
  },
  // マウント時
  mounted() {
    console.log("inputDateYm Component mounted.");
  },
  methods: {
    // 入力が変更された場合、親コンポーネントに選択値を返却
    selChanges : function(value) {

        this.$emit('change-event', value);

    }
  }

};
</script>
