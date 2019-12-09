<template>
  <div>
    <div class="input-group">
      <div class="input-group-prepend">
        <span
          :class="classData"
          id="basic-addon1"
        >{{ itemTitle }}
        <span v-if="itemRequired" class="color-red">[必須]</span>
        </span>
      </div>
      <input
        type="time"
        class="form-control"
        name="inputtime"
        v-model="valueData"
        v-on:change="selChanges(valueData)"
      ></input>
    </div>
    <p class="rf-mini rf-mt-5">{{ placeHolder }}</p>
  </div>
</template>

<script>

export default {
  name: "inputTime",
  props: {
      itemTitle: {
          default: '時刻',
          type: String
      },
      itemRequired: {
          default: false,
          type: Boolean
      },
      placeHolder: {
          default: '時刻を半角で入力します（例 13:00）',
          type: String
      },
      valueData: {
          default: '',
          type: String
      },
      classData: {
          default: 'input-group-text font-size-sm line-height-xs label-width-180',
          type: String
      }
  },
  // マウント時
  mounted() {
    console.log("inputTime Component mounted.");
  },
  methods: {
    // 入力が変更された場合、親コンポーネントに選択値を返却
    selChanges : function(value) {

        this.$emit('change-event', value);

    }
  }

};
</script>
