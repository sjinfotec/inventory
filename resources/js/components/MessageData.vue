<template>
  <p v-if="messagedatas.length">
    <ul>
      <li v-for="messagedata in messagedatas">{{ messagedata }}</li>
    </ul>
  </p>
</template>
<script>

export default {
  name: "messageData",
  props: {
    messagedatas: {
        type: Array,
        default: () => []
    }
  }
};
</script>
