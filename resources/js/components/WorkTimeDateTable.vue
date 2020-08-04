<template>
  <div v-if="workingtimedates.length ">
    <table class="table table-striped table-bordered text-nowrap">
      <thead>
        <tr>
          <th>部署</th>
          <th>社員</th>
          <th>雇用形態</th>
          <th>出勤</th>
          <th>退勤</th>
          <th>中抜</th>
          <th>戻り</th>
          <th>所定時間</th>
          <th>所定外時間</th>
          <th>所定外深夜時間</th>
          <th>所定外時間</th>
          <th>不就労時間</th>
          <th>タイムテーブル</th>
          <th>所定外累計（月）</th>
          <th>残所定外（月）</th>
          <th>休暇</th>
          <th>編集</th>
          <th>確定</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in workingtimedates">
          <input type="hidden" v-model="item.id"></input>
          <td>{{item.department_name}}</td>
          <td>{{item.user_name}}</td>
          <td>{{item.working_time_style_name}}</td>
          <td>{{item.attendance_time}}</td>
          <td>{{item.leaving_time}}</td>
          <td>{{item.missing_middle_time}}</td>
          <td>{{item.missing_middle_return_time}}</td>
          <td>{{item.regular_working_times}}</td>
          <td>{{item.out_of_regular_working_times}}</td>
          <td>{{item.out_of_regular_night_working_times}}</td>
          <td>{{item.out_of_regular_total_working_times}}</td>
          <td>{{item.not_employment_time}}</td>
          <td>{{item.working_timetable_name}}</td>
          <td>{{item.prescribed_outside_total_month}}</td>
          <td>{{item.remaining_prescribed_outside_month}}</td>
          <td>{{item.leave_item_code}}</td>
          <td>{{item.leave_item_name}}</td>
          <td><button class="btn btn-danger" @click="editClick(item.id)">編集</button></td>
          @if ({{item.fixedtime}} == '1')
            <td><button class="btn btn-danger" @click="fixedClick(item.fixedtime)">解除</button></td>
          @else
            <td><button class="btn btn-danger" @click="fixedClick(item.fixedtime)">確定</button></td>
          @endif
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>

export default {
  name: "worktimedatetable",
  props: {
      depCode: {
          type: String,
          default: ''
      }
  },
  data() {
    return {
      workingtimedates:[]
    };
  },
  // マウント時
  mounted() {
    console.log("worktimedatetable Component mounted.");
  },
  methods: {
    editClick : function(value) {
      console.log("editClick = " +value);

    },
    fixedClick : function(value) {
      console.log("fixedClick = " +value);

    }

  }
};
</script>
