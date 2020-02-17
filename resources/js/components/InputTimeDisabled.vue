<template>
  <div>
    <div class="input-group">
      <div class="input-group-prepend">
        <span
          :class="classData"
          id="basic-addon1"
        >{{ itemTitle }}
        </span>
      </div>
      <input
        type="time"
        class="form-control"
        name="inputtime"
        v-model="valueData"
        disabled
      ></input>
    </div>
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
      valueData: {
          default: '',
          type: String
      },
      classData: {
          default: 'input-group-text font-size-sm line-height-xs label-width-180',
          type: String
      }
  }

};
</script>
