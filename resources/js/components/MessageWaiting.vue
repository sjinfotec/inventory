<template>
  <!-- <div id="messagewaiting" :class="{'is-open': isMessageShow}">
  </div> -->
  <transition id="waiting" name="slide-fade">
    <p v-if="isMessageShow">
    <img id="loading"src="/images/ajax-loader.gif">&nbsp;<span v-text="messageText"></span>
    </p>
  </transition>
</template>
<script>

export default {
  name: "waiting",
  props: {
      messageText: {
          default: '処　理　中...',
          type: String
      },
      isMessageShow: {
          default: false,
          type: Boolean
      }
  },
  // マウント時
  mounted() {
    console.log("messagewaiting Component mounted.");
  }
};
</script>
<style>
  #modal {
      margin: 3vw;
  }
</style>