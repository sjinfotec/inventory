<template>
  <div>
    <!-- ----------- テーブル部 START ---------------- -->
    <!-- main contentns row -->
    <div class="card-body pt-2">
      <!-- .row -->
      <div class="row">
        <div class="col-12">
          <div class="table-responsive">
            <table class="table table-striped border-bottom font-size-sm text-nowrap">
            <!-- <table class="table"> -->
              <thead>
                <tr>
                  <td class="text-center align-middle mw-rem-10">部署</td>
                  <td class="text-center align-middle mw-rem-10">雇用形態</td>
                  <td class="text-center align-middle mw-rem-10">氏名</td>
                  <td class="text-center align-middle mw-rem-2-6" v-if="isEdtbutton">操作</td>
                  <td v-for="(item,rowIndex) in detailDates" v-bind:key="item.date"
                    class="text-center align-middle mw-rem-5">{{ item['date_name'] }}</td>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item1,rowIndex1) in details" v-bind:key="item1['user_code']">
                  <td class="text-left align-middle mw-rem-10">{{ item1['department_name'] }}</td>
                  <td class="text-left align-middle mw-rem-10">{{ item1['employment_name'] }}</td>
                  <td class="text-left align-middle mw-rem-10">{{ item1['user_name'] }}</td>
                  <td class="text-center align-middle mw-rem-2-6" style="text-align:center" v-if="isEdtbutton">
                    <input type="image" src="images/btn01.svg" v-on:click="detailEdtClick(rowIndex1)" alt />
                  </td>
                  <td v-for="(item2,rowIndex2) in item1['array_user_date_data']" v-bind:key="item2['date']"
                    class="text-center align-middle mw-rem-5">
                    {{ item2['business_kubun_name'] }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- /.row -->
    </div>
    <!-- ----------- テーブル部 END ---------------- -->
  </div>
</template>
<script>

export default {
  name: "CalendarMonthTable",
  props: {
    detailDates: {
      type: Array,
      default: []
    },
    details: {
      type: Array,
      default: []
    },
    isEdtbutton: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    // ------------------------ イベント処理 ------------------------------------
    
    // 明細編集ボタンクリックされた場合の処理
    detailEdtClick: function(index) {
      var arrayData = {'rowIndex' : index};
      this.$emit('detaileditclick-event',event, arrayData);
    }
  }
};
</script>
<style scoped>

thead, tbody {
  display: block !important;
}

tbody {
  overflow-x: hidden !important;
  overflow-y: scroll !important;
  height: 360px !important;
}

.table th, .table td {
    padding: 0rem !important;
    border-style: solid dashed !important;
    border-width: 1px !important;
    border-color: #95c5ed #dee2e6 !important;
}

.mw-rem-2-6 {
  min-width: 2.6rem;
}

<<<<<<< HEAD
=======

input {
    vertical-align : middle;
}
>>>>>>> develop
</style>
