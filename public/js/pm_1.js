

/*
<div id="btn_cnt1" class="print-none">
  <div class="btn_col_1">
    <div id="green">作業開始</div>
  </div>
  <div class="btn_col_1">
    <div id="red">作業中断</div>
  </div>
  <div class="btn_col_1">
    <div id="blue">作業完了</div>
  </div>
</div>
*/

/*
#green, #red, #blue {
  flex: 0 0 30%;
	height: 30px;
	margin: 10px 0;
	border-radius: 10px;
	color: #FFF;
	font-weight: bold;
	transition: 0.5s;
	cursor: pointer;
  padding: 1.3rem 1rem;

	display: flex;
	align-items: center;
	justify-content: center;
}
#green {
	background: #80dd60;
}
#red {
	background: #dd6060;
}
#blue {
	background: #6cb2eb;
}
#green:hover, #red:hover, #blue:hover, #cancelbtn:hover{
  color: yellow;
}
#cancelbtn  {
  flex: 0 0 30%;
	height: 30px;
	margin: 10px 0;
	border-radius: 10px;
	color: #FFF;
	font-weight: bold;
	transition: 0.5s;
	cursor: pointer;
  padding: 1.3rem 1rem;
	background: #6cb2eb;

	display: flex;
	align-items: center;
	justify-content: center;

}

#target {
  width: 100px;
  height: 100px;
  margin: 10px 0;
  align-items: center;
  justify-content: center;
  background: #9E9E9E;
  border-radius: 50%;
  color: #FFF;
  font-weight: bold;
}

*/



//-------------ボタン操作----------
//要素を指定
const green = document.getElementById('green');
const red = document.getElementById('red');
const blue = document.getElementById('blue');
const target = document.getElementById('target');
const cancelbtn = document.getElementById('cancelbtn');

//greenボタンが押された時
green.addEventListener('click', function () {

	target.style.background = '#80dd60';
    table_cnt3.style.color = '#FFF';
    btn_cnt2.style.display = 'flex';
    btn_cnt1.style.display = 'none';
}, false);
//redボタンが押された時
red.addEventListener('click', function () {

	target.style.background = '#dd6060';
    table_cnt3.style.color = '#FFF';
    btn_cnt2.style.display = 'flex';
    //this.style.display = 'none';
    btn_cnt1.style.display = 'none';

}, false);
//blueボタンが押された時
blue.addEventListener('click', function () {

	target.style.background = '#6cb2eb';
    table_cnt3.style.color = '#FFF';
    btn_cnt2.style.display = 'flex';
    btn_cnt1.style.display = 'none';

}, false);
//canselボタンが押された時
cancelbtn.addEventListener('click', function () {

	target.style.background = '#FFF';
    table_cnt3.style.color = '#212529';
    btn_cnt2.style.display = 'none';
    btn_cnt1.style.display = 'flex';

}, false);
