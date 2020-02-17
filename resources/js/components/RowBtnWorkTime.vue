<template>
  <td v-if="btnMode ==='rowdel'" class="text-center align-middle">
    <div class="btn-group">
      <button
        type="button"
        class="btn btn-danger btn-lg font-size-rg"
        :disabled="isPush"
        v-on:click="rowdelclickBtn()"
      >削除</button>
    </div>
  </td>
</template>
<script>

export default {
  name: "rowbtnworktime",
  props: {
      btnMode: {
          default: "search",
          type: String
      },
      isPush: {
          default: false,
          type: Boolean
      }
  },
  // マウント時
  mounted() {
    console.log("rowbtnworktime Component mounted.");
  },
  data: function() {
    return {
      edtString: ""
    };
  },
  methods: {
    rowdelclickBtn: function(state, id, index) {
      this.$emit('rowdelclick-event',event);
    }
  }
};
</script>
