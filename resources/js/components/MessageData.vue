<template v-if="messageDatas.length">
  <p  v-if="messageClass === 'info'">
    <ul>
      <li v-for="(messagedata,index) in messageDatas" v-bind:key="index">{{ messagedata }}</li>
    </ul>
  </p>
  <p  v-else>
    <ul class="error-red color-red">
      <li v-for="(messagedata,index) in messageDatas" v-bind:key="index">{{ messagedata }}</li>
    </ul>
  </p>
</template>
<script>

export default {
  name: "messageData",
  props: {
    messageDatas: {
        type: Array,
        default: () => []
    },
    messageClass: {
        type: String,
        default: "info"
    }
  }
};
</script>
