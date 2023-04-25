<template>
<div>
  <!-- main contentns  -->
  <div id="">


    <div v-if="selectMode=='DEFAULT'">
      <div id="top_cnt">
        <h2>棚卸 / 預かり・在庫</h2>
      </div>

      <div id="cnt3">
          <div>
            <button type="button" class="" @click="RestartStock()">
              棚卸 再開
            </button>
          </div>
          <div id="form3" class="mg1">
            <input type="month" class="form_style bc4" v-model="stock_month" name="stock_month">
            <button type="button" class="" @click="NewStock()">
              棚卸 新規開始
            </button>
          </div>
          <!--<div>stock month {{ stock_month }}</div>-->

      </div>


      <div class="" v-if="messagevalidatesNew.length">
          <ul class="error-red color_red">
            <li v-for="(messagevalidate,index) in messagevalidatesNew" v-bind:key="index">{{ messagevalidate }}</li>
          </ul>
      </div>


    </div><!--end v-if="selectMode=='DEFAULT'"-->



    <div v-if="selectMode=='LINEACTIVE'">
      <div id="top_cnt">
        <h2>棚卸 / 預かり・在庫</h2>
        <form name="moveform">
          <div id="btn_cnt1">
            <div class="btn_col_1">
              <input type="text" name="urlname" class="form_style bc1">
              <input type="text" name="dummy" style="display:none;">
            </div>
            <div class="btn_col_2">
              <input type="button" value="受注番号 移動" class="transition2 btn1" 
              onclick="location.hash = document.moveform.urlname.value; return false;">
            </div>
          </div>
        </form>
        <!--
        <form id="form1" name="form1">
          <input type="text" class="form_style bc1" v-model="s_order_no" maxlength="30" name="s_order_no">
          <button type="button" class="" @click="searchBtn()">
            受注番号 検索
          </button>
        </form>
        -->
        <!--
        <button type="button" class="" @click="NewBtn()">
          新規登録
        </button>
        -->
      </div>

      <div class="" v-if="actionmsgArr.length">
          <ul class="error-red color_red">
            <li v-for="(actionmsg,index) in actionmsgArr" v-bind:key="index">{{ actionmsg }}</li>
          </ul>
      </div>

      <div id="tbl_1">
        <table>
          <thead>
            <tr>
              <th class="gc4">受注番号 <button type="button" class="" @click="ForwardReverse('order_no',1)">▲</button> <button type="button" class="" @click="ForwardReverse('order_no',2)">▼</button></th>
              <th class="gc4">会社名 <button type="button" class="" @click="ForwardReverse('company_name',1)">▲</button> <button type="button" class="" @click="ForwardReverse('company_name',2)">▼</button></th>
              <th class="gc4">商品名 <button type="button" class="" @click="ForwardReverse('product_name',1)">▲</button> <button type="button" class="" @click="ForwardReverse('product_name',2)">▼</button></th>
              <th class="gc4">単位</th>
              <th class="gc4">入数</th>
              <th class="gc4">在庫</th>
              <th class="gc4">箱数</th>
              <th class="gc4">在庫結果</th>
              <th class="gc4">箱数結果</th>
              <th class="gc4">棚卸在庫</th>
              <th class="gc4">棚卸箱数</th>
              <th class="gc4">発注情報</th>
              <th class="gc4">&nbsp;</th>
              <th class="gc4">単価<span>（在庫のみ）</span></th>
              <th class="gc4">合計金額<span>（在庫のみ）</span></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item,rowIndex) in details3" :key="rowIndex"  v-bind:class="(item['cal_now_inventory'] !== 0 || item['cal_nbox'] !== 0 ) ? 'bgcolor6' : ''">
              <td class="posi_r1"><span class="posi_a1" v-bind:id="item['order_no']"></span>{{ item['order_no'] }}</td>
              <td>{{ item['company_name'] }}</td>
              <td>{{ item['product_name'] }}</td>
              <td class="nbr">{{ item['unit'] }}</td>
              <td class="style1">{{ item['quantity'] }}</td>
              <td class="style1">{{ item['now_inventory'] }}</td>
              <td class="style1">{{ item['nbox'] }}</td>
              <td class="style1"><span class="color1" v-if="item['cal_now_inventory'] === 0">&#10004;</span><span class="color2 bold" v-else-if="item['cal_now_inventory'] !== 0">{{ item['cal_now_inventory'] }}</span></td>
              <td class="style1"><span class="color1" v-if="item['cal_nbox'] === 0">&#10004;</span><span class="color2 bold" v-else-if="item['cal_nbox'] !== 0">{{ Number(item['cal_nbox']) }}</span></td>
              <td class="style1" v-bind:class="(item['status'] === 'stockup') ? 'bgcolor4' : ''"><input type="text" class="form_style bc1" v-model="details3[rowIndex].stock_now_inventory" maxlength="11" name="now_inventory"></td>
              <td class="style1" v-bind:class="(item['status'] === 'stockup') ? 'bgcolor4' : ''"><input type="text" class="form_style bc1" v-model="details3[rowIndex].stock_nbox" maxlength="16" name="nbox"></td>
              <td class="nbr"><span v-if="item['order_info'] == 'a'">預かり</span><span v-if="item['order_info'] == 'z'">在庫</span></td>
              <td>
                <input type="hidden" v-model="stock_month" name="stock_month">
                <div id="btn_cnt1">
                  <button type="button" class="style1 mg_r" @click="stockUpdate(rowIndex,6)">
                  棚卸更新
                  </button>
                  <!--
                  <button type="button" class="" @click="StockBtn(details3[rowIndex].inv_id,details3[rowIndex].stock_now_inventory,details3[rowIndex].stock_nbox,details3[rowIndex])">
                  登録
                  </button>
                  -->
                  <!--
                  <button type="button" class="" @click="InvBtn(details3[rowIndex].inv_id,details3[rowIndex].product_id,details3[rowIndex].product_name,details3[rowIndex].order_info)">
                  編集
                  </button>
                  -->
                </div>
              </td>
              <td class="style1">{{ item['unit_price'] }}</td>
              <td class="style1">{{ item['cal_total_price'] }}</td>
            </tr>
            <tr class="border1">
              <td colspan="3" class="style1">{{ this.details3.length }} 件</td>
              <td colspan="11" class="style1">合計金額</td>
              <td class="style1">{{ Number(totals) | numberFormat }}</td>
              <!--<td colspan="2" class="style1 font1"> ※合計金額に履歴は含まれていません</td>-->
            </tr>
          </tbody>
        </table>
      </div><!-- end tbl_1 -->
    </div><!--end selectMode=='LINEACTIVE'-->



    <div id="input_area_1" v-if="selectMode=='COMPLETE'">
      <div>
        <h2>{{ acttitle }} 完了</h2>
      </div>

      <div class="" v-if="actionmsgArr.length">
          <ul class="error-red color_red">
            <li v-for="(actionmsg,index) in actionmsgArr" v-bind:key="index">{{ actionmsg }}</li>
          </ul>
      </div>
      <div id="tbl_1">
        <table>
          <thead>
            <tr>
              <th class="gc1">担当</th>
              <th class="gc1">受注番号</th>
              <th class="gc1">会社名</th>
              <th class="gc1">商品名</th>
              <th class="gc1">単位</th>
              <th class="gc1">入数</th>
              <th class="gc1">入庫日</th>
              <th class="gc1">発注数</th>
              <th class="gc1">入庫数</th>
              <th class="gc1">出庫日</th>
              <th class="gc1">出庫数</th>
              <th class="gc1">現在在庫</th>
              <th class="gc1">箱数</th>
              <th class="gc1">出庫No.</th>
              <th class="gc1">残りNo.</th>
              <th class="gc1">発送先</th>
              <th class="gc1">備考</th>
              <th class="gc1">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item,rowIndex) in details" :key="rowIndex" v-bind:class="classObj1">
              <td>{{ item['charge'] }}</td>
              <td v-bind:class="(item['status'] == 'newest') ? 'bgcolor5' : ''">{{ item['order_no'] }}</td>
              <td>{{ item['company_name'] }}</td>
              <td>{{ item['product_name'] }}</td>
              <td class="nbr">{{ item['unit'] }}</td>
              <td class="style1">{{ item['quantity'] }}</td>
              <td class="nbr">{{ item['receipt_day'] }}</td>
              <td class="style1" v-bind:class="(item['order_quantity'] === 0) ? 'color3' : ''">{{ item['order_quantity'] }}</td>
              <td class="style1" v-bind:class="(item['receipt'] === 0) ? 'color3' : ''">{{ item['receipt'] }}</td>
              <td class="nbr">{{ item['delivery_day'] }}</td>
              <td class="style1" v-bind:class="(item['delivery'] === 0) ? 'color3' : ''">{{ item['delivery'] }}</td>
              <td class="style1" v-bind:style="(item['now_inventory'] === 0) ? 'color:red' : ''">{{ item['now_inventory'] }}</td>
              <td class="style1">{{ item['nbox'] }}</td>
              <td>{{ item['dnum'] }}</td>
              <td>{{ item['rnum'] }}</td>
              <td>{{ item['shipping_address'] }}</td>
              <td>{{ item['remarks'] }}</td>
              <td>
                <!--
                id={{ item['id'] }} re_id={{ re_id }}              
                -->
                <button v-if="btnMode=='1'" type="button" class="" @click="EditBtn(item['id'], item['product_id'], details[rowIndex].product_name)">
                編集
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div><!-- end tbl_1 -->
    </div><!--end v-if-->





    <div id="input_area_1" v-if="selectMode=='EDT'">
        <!--<div v-if="orinfo">order_info =  {{ orinfo }}</div>-->
        <!--
        <EditModeA v-if="orinfo === 'a'"
          v-bind:deatils="details[index]"
          v-bind:deatils2="details2"
          v-bind:index="index"
        ></EditModeA>
        <EditModeZ v-if="orinfo === 'z'"></EditModeZ>
        -->

      <div v-if="orinfo === 'a'">
      <div v-for="(item,index) in details" v-bind:key="item.id">
        <div id="top_cnt">
          <h2>預かり / 更新-追加-修正</h2>
          <button v-if="stock_month" type="button" class="" @click="RestartStock()">
            戻る
          </button>
          <button type="button" class="customize" @click="viewBtn(2)">
            追加情報
          </button>
        </div>

        <div class="" v-if="messagevalidatesNew.length">
            <ul class="error-red color_red">
              <li v-for="(messagevalidate,index) in messagevalidatesNew" v-bind:key="index">{{ messagevalidate }}</li>
            </ul>
        </div>

        <input type="hidden" v-model="details[index].id" name="id" />
        <div id="cnt1">
          <div class="inputgroup w1">
            <div class="cate gc1">担当</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc1"
                v-model="details[index].charge"
                maxlength="20"
                name="charge"
              />
            </div>
          </div>
          <div class="inputgroup w2">
            <div class="cate gc1">受注番号</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc1"
                v-model="details[index].order_no"
                maxlength="30"
                name="order_no"
              />
            </div>
          </div>
          <div class="inputgroup w3">
            <div class="cate gc1">会社名</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc1"
                v-model="details[index].company_name"
                maxlength="40"
                name="company_name"
              />
            </div>
          </div>
          <div class="inputgroup w4">
            <div class="cate gc1">商品名</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc1"
                v-model="details[index].product_name"
                maxlength="100"
                name="product_name"
              />
            </div>
          </div>
        </div>

        <div id="cnt1">
          <div class="inputgroup w1">
            <div class="cate gc1">単位</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc1"
                v-model="details[index].unit"
                maxlength="10"
                name="unit"
              />
            </div>
          </div>
          <div class="inputgroup w1">
            <div class="cate gc1">入数</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc1"
                v-model="details[index].quantity"
                maxlength="11"
                name="quantity"
              />
            </div>
          </div>
          <div class="inputgroup w2">
            <div class="cate gc1">入庫日</div>
            <div class="inputzone">
              <input
                type="date"
                class="form_style bc1"
                v-model="details[index].receipt_day"
                maxlength="12"
                name="receipt_day"
              />
            </div>
          </div>
          <div class="inputgroup w1">
            <div class="cate gc1">発注数</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc1"
                v-model="details[index].order_quantity"
                maxlength="11"
                name="order_quantity"
              />
            </div>
          </div>
          <div class="inputgroup w1">
            <div class="cate gc1">入庫数</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc1"
                v-model="details[index].receipt"
                maxlength="11"
                name="receipt"
              />
            </div>
          </div>
          <div class="inputgroup w2">
            <div class="cate gc1">出庫日</div>
            <div class="inputzone">
              <input
                type="date"
                class="form_style bc1"
                v-model="details[index].delivery_day"
                maxlength="12"
                name="delivery_day"
              />
            </div>
          </div>
          <div class="inputgroup w1">
            <div class="cate gc1">出庫数</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc1"
                v-model="details[index].delivery"
                maxlength="11"
                name="delivery"
              />
            </div>
          </div>
          <div class="inputgroup w1">
            <div class="cate gc1">現在在庫</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc1"
                v-model="details[index].now_inventory"
                maxlength="11"
                name="now_inventory"
              />
            </div>
          </div>
          <div class="inputgroup w1">
            <div class="cate gc1">箱数</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc1"
                v-model="details[index].nbox"
                maxlength="16"
                name="nbox"
              />
            </div>
          </div>
        </div>

        <div id="cnt1">
          <div class="inputgroup w2">
            <div class="cate gc1">出庫No</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc1"
                v-model="details[index].dnum"
                maxlength="40"
                name="dnum"
              />
            </div>
          </div>
          <div class="inputgroup w2">
            <div class="cate gc1">残りNo</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc1"
                v-model="details[index].rnum"
                maxlength="40"
                name="rnum"
              />
            </div>
          </div>
          <div class="inputgroup w1">
            <div class="cate gc1">種別</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc1"
                v-model="details[index].other1"
                maxlength="10"
                name="other1"
              />
            </div>
          </div>
          <div class="inputgroup w2">
            <div class="cate gc1">発送先</div>
            <div class="inputzone">
              <textarea class="form_style_t bc1" v-model="details[index].shipping_address" maxlength="100" name="shipping_address" rows="3"></textarea>
            </div>
          </div>
          <div class="inputgroup w2">
            <div class="cate gc1">備考</div>
            <div class="inputzone">
              <textarea class="form_style_t bc1" v-model="details[index].remarks" maxlength="191" name="remarks" rows="3"></textarea>
            </div>
          </div>
        </div>

        <div id="cnt1" v-if="view_switch=='on'">
          <div class="inputgroup">
            <div class="cate">グループ</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].marks"
                maxlength="10"
                name="marks"
              />
              <span>{{ details[index].marks }}</span>
            </div>
          </div>
          <div class="inputgroup">
            <div class="cate">作成ユーザー</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].created_user"
                maxlength="20"
                name="created_user"
              />
              <span>{{ details[index].created_user }}</span>
            </div>
          </div>
          <div class="inputgroup">
            <div class="cate">修正ユーザー</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].updated_user"
                maxlength="20"
                name="updated_user"
              />
              <span>{{ details[index].updated_user }}</span>
            </div>
          </div>
          <div class="inputgroup">
            <div class="cate">作成日時</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].created_at"
                maxlength="16"
                name="created_at"
              />
              <span>{{ details[index].created_at }}</span>
            </div>
          </div>
          <div class="inputgroup">
            <div class="cate">修正日時</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].updated_at"
                maxlength="16"
                name="updated_at"
              />
              <span>{{ details[index].updated_at }}</span>
            </div>
          </div>

          <div class="inputgroup">
            <div class="cate">会社ID</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].company_id"
                maxlength="11"
                name="company_id"
              />
              <span>{{ details[index].company_id }}</span>
            </div>
          </div>
          <div class="inputgroup">
            <div class="cate">商品ID</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].product_id"
                maxlength="11"
                name="product_id"
              />
              <span>{{ details[index].product_id }}</span>
            </div>
          </div>
          <div class="inputgroup">
            <div class="cate">ステータス</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].status"
                maxlength="20"
                name="status"
              />
              <span>{{ details[index].status }}</span>
            </div>
          </div>
          <div class="inputgroup">
            <div class="cate">発注情報</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].order_info"
                maxlength="20"
                name="order_info"
              />
              <span>{{ details[index].order_info }}</span>
            </div>
          </div>
          <div class="inputgroup">
            <div class="cate">削除フラグ</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].is_deleted"
                maxlength="20"
                name="is_deleted"
              />
              <span>{{ details[index].is_deleted }}</span>
            </div>
          </div>
        </div>

        <div id="button1">
          <div>
            <div class="btnstyle">
              <button type="button" class="style1" @click="dataUpdate(index,1,details[index].order_info)">在庫の更新</button>
            </div>
            <div class="btnstyle">
              <button type="button" class="style2" @click="dataUpdate(index,0,details[index].order_info)">在庫の修正</button>
            </div>
          </div>
          <div>
            <div class="btnstyle">
              <button type="button" class="" @click="dataUpdate(index,2,details[index].order_info)"><span>{{ item['company_name'] }} </span><span>の新しい商品を登録する</span></button>
            </div>
            <div class="btnstyle">
              <button type="button" class="" @click="dataDel(index,4,details[index].order_info)">ゴミ箱へ移す</button>
            </div>
          </div>
        </div>

      </div><!--end v-for-->


      <div id="cnt2" v-for="(item2,index2) in details" v-bind:key="index2">
        <div>
          <!--<h4>{{ details[index2].product_name }} {{ item2['product_name'] }} 履歴一覧</h4>-->
          <h4>{{ product_title }}<span style="margin-left:20px;">履歴一覧</span></h4>
        </div>
      </div><!--end v-for-->

      <div id="tbl_1">
        <table>
          <thead>
            <tr>
              <th class="gc1">担当</th>
              <th class="gc1">受注番号</th>
              <th class="gc1">会社名</th>
              <th class="gc1">商品名</th>
              <th class="gc1">単位</th>
              <th class="gc1">入数</th>
              <th class="gc1">入庫日</th>
              <th class="gc1">発注数</th>
              <th class="gc1">入庫数</th>
              <th class="gc1">出庫日</th>
              <th class="gc1">出庫数</th>
              <th class="gc1">現在在庫</th>
              <th class="gc1">箱数</th>
              <th class="gc1">出庫No.</th>
              <th class="gc1">残りNo.</th>
              <th class="gc1">発送先</th>
              <th class="gc1">備考</th>
              <th class="gc1">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item,rowIndex) in details2" :key="rowIndex" v-bind:class="(item['id'] == edit_id) ? 'bgcolor3' : ''">
              <td >{{ item['charge'] }}</td>
              <td>{{ item['order_no'] }}</td>
              <td>{{ item['company_name'] }}</td>
              <td>{{ item['product_name'] }}</td>
              <td class="nbr">{{ item['unit'] }}</td>
              <td class="style1">{{ item['quantity'] }}</td>
              <td class="nbr">{{ item['receipt_day'] }}</td>
              <td class="style1" v-bind:class="(item['order_quantity'] === 0) ? 'color3' : ''">{{ item['order_quantity'] }}</td>
              <td class="style1" v-bind:class="(item['receipt'] === 0) ? 'color3' : ''">{{ item['receipt'] }}</td>
              <td class="nbr">{{ item['delivery_day'] }}</td>
              <td class="style1" v-bind:class="(item['delivery'] === 0) ? 'color3' : ''">{{ item['delivery'] }}</td>
              <td class="style1" v-bind:style="(item['now_inventory'] === 0) ? 'color:red' : ''">{{ item['now_inventory'] }}</td>
              <td class="style1">{{ item['nbox'] }}</td>
              <td>{{ item['dnum'] }}</td>
              <td>{{ item['rnum'] }}</td>
              <td>{{ item['shipping_address'] }}</td>
              <td>{{ item['remarks'] }}</td>
              <td><!-- id={{ item['id'] }} edit_id={{ edit_id }} product_id= {{ product_id }} --></td>
            </tr>
          </tbody>
        </table>
      </div><!-- end tbl_1 -->
      </div><!--end v-if="orinfo === 'a'" -->






      <div v-if="orinfo === 'z'">
      <div v-for="(item,index) in details" v-bind:key="item.id">

        <div id="top_cnt">
          <h2>在庫 / 更新-追加-修正</h2>
          <button v-if="stock_month" type="button" class="" @click="RestartStock()">
            戻る
          </button>
          <button type="button" class="customize" @click="viewBtn(2)">
            追加情報
          </button>
        </div>

        <div class="" v-if="messagevalidatesNew.length">
            <ul class="error-red color_red">
              <li v-for="(messagevalidate,index) in messagevalidatesNew" v-bind:key="index">{{ messagevalidate }}</li>
            </ul>
        </div>

        <input type="hidden" v-model="details[index].id" name="id" />
        <div id="cnt1">
          <div class="inputgroup w1">
            <div class="cate gc2">担当</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc2"
                v-model="details[index].charge"
                maxlength="20"
                name="charge"
              />
            </div>
          </div>
          <div class="inputgroup w2">
            <div class="cate gc2">受注番号</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc2"
                v-model="details[index].order_no"
                maxlength="30"
                name="order_no"
              />
            </div>
          </div>
          <div class="inputgroup w3">
            <div class="cate gc2">会社名</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc2"
                v-model="details[index].company_name"
                maxlength="40"
                name="company_name"
              />
            </div>
          </div>
          <div class="inputgroup w4">
            <div class="cate gc2">商品名</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc2"
                v-model="details[index].product_name"
                maxlength="100"
                name="product_name"
              />
            </div>
          </div>
        </div>
        <div id="cnt1">
          <div class="inputgroup w1">
            <div class="cate gc2">単位</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc2"
                v-model="details[index].unit"
                maxlength="10"
                name="unit"
              />
            </div>
          </div>
          <div class="inputgroup w1">
            <div class="cate gc2">入数</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc2"
                v-model="details[index].quantity"
                maxlength="11"
                name="quantity"
              />
            </div>
          </div>
          <div class="inputgroup w2">
            <div class="cate gc2">納入日</div>
            <div class="inputzone">
              <input
                type="date"
                class="form_style bc2"
                v-model="details[index].supply_day"
                maxlength="12"
                name="supply_day"
              />
            </div>
          </div>
          <div class="inputgroup w1">
            <div class="cate gc2">納入数</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc2"
                v-model="details[index].supply_quantity"
                maxlength="11"
                name="supply_quantity"
              />
            </div>
          </div>
          <div class="inputgroup w2">
            <div class="cate gc2">発注日</div>
            <div class="inputzone">
              <input
                type="date"
                class="form_style bc2"
                v-model="details[index].order_day"
                maxlength="12"
                name="order_day"
              />
            </div>
          </div>
          <div class="inputgroup w1">
            <div class="cate gc2">発注数</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc2"
                v-model="details[index].order_quantity"
                maxlength="11"
                name="order_quantity"
              />
            </div>
          </div>
          <div class="inputgroup w1">
            <div class="cate gc2">現在在庫</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc2"
                v-model="details[index].now_inventory"
                maxlength="11"
                name="now_inventory"
              />
            </div>
          </div>
          <div class="inputgroup w1">
            <div class="cate gc2">箱数</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc2"
                v-model="details[index].nbox"
                maxlength="16"
                name="nbox"
              />
            </div>
          </div>
        </div>
        <div id="cnt1">
          <div class="inputgroup w2">
            <div class="cate gc2">発注先</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc2"
                v-model="details[index].order_address"
                maxlength="40"
                name="order_address"
              />
            </div>
          </div>
          <div class="inputgroup w1">
            <div class="cate gc2">単価</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc2"
                v-model="details[index].unit_price"
                maxlength="40"
                name="unit_price"
              />
            </div>
          </div>
          <div class="inputgroup w1">
            <div class="cate gc2">合計</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc2"
                v-model="details[index].total"
                maxlength="100"
                name="total"
              />
            </div>
          </div>
          <div class="inputgroup w1">
            <div class="cate gc2">種別</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc2"
                v-model="details[index].other1"
                maxlength="10"
                name="other1"
              />
            </div>
          </div>
          <div class="inputgroup w2">
            <div class="cate gc2">備考</div>
            <div class="inputzone">
              <textarea class="form_style_t bc2" v-model="details[index].remarks" maxlength="191" name="remarks" rows="3"></textarea>
            </div>
          </div>
          <div class="inputgroup w2">
            <div class="cate gc2">メモ/ノート</div>
            <div class="inputzone">
              <textarea class="form_style_t bc2" v-model="details[index].note" maxlength="191" name="note" rows="3"></textarea>
            </div>
          </div>
        </div>

        <div id="cnt1" v-if="view_switch=='on'">
          <div class="inputgroup">
            <div class="cate">グループ</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].marks"
                maxlength="10"
                name="marks"
              />
              <span>{{ details[index].marks }}</span>
            </div>
          </div>
          <div class="inputgroup">
            <div class="cate">作成ユーザー</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].created_user"
                maxlength="20"
                name="created_user"
              />
              <span>{{ details[index].created_user }}</span>
            </div>
          </div>
          <div class="inputgroup">
            <div class="cate">修正ユーザー</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].updated_user"
                maxlength="20"
                name="updated_user"
              />
              <span>{{ details[index].updated_user }}</span>
            </div>
          </div>
          <div class="inputgroup">
            <div class="cate">作成日時</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].created_at"
                maxlength="16"
                name="created_at"
              />
              <span>{{ details[index].created_at }}</span>
            </div>
          </div>
          <div class="inputgroup">
            <div class="cate">修正日時</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].updated_at"
                maxlength="16"
                name="updated_at"
              />
              <span>{{ details[index].updated_at }}</span>
            </div>
          </div>

          <div class="inputgroup">
            <div class="cate">会社ID</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].company_id"
                maxlength="11"
                name="company_id"
              />
              <span>{{ details[index].company_id }}</span>
            </div>
          </div>
          <div class="inputgroup">
            <div class="cate">商品ID</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].product_id"
                maxlength="11"
                name="product_id"
              />
              <span>{{ details[index].product_id }}</span>
            </div>
          </div>
          <div class="inputgroup">
            <div class="cate">ステータス</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].status"
                maxlength="20"
                name="status"
              />
              <span>{{ details[index].status }}</span>
            </div>
          </div>
          <div class="inputgroup">
            <div class="cate">発注情報</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].order_info"
                maxlength="20"
                name="order_info"
              />
              <span>{{ details[index].order_info }}</span>
            </div>
          </div>
          <div class="inputgroup">
            <div class="cate">削除フラグ</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].is_deleted"
                maxlength="20"
                name="is_deleted"
              />
              <span>{{ details[index].is_deleted }}</span>
            </div>
          </div>
        </div>

        <div id="button1">
          <div>
            <div class="btnstyle">
              <button type="button" class="style1" @click="dataUpdate(index,1,details[index].order_info)">在庫の更新</button>
            </div>
            <div class="btnstyle">
              <button type="button" class="style2" @click="dataUpdate(index,0,details[index].order_info)">在庫の修正</button>
            </div>
          </div>
          <div>
            <div class="btnstyle">
              <button type="button" class="" @click="dataUpdate(index,2,details[index].order_info)"><span>{{ item['company_name'] }} </span><span>の新しい商品を登録する</span></button>
            </div>
            <div class="btnstyle">
              <button type="button" class="" @click="dataDel(index,4,details[index].order_info)">ゴミ箱へ移す</button>
            </div>
          </div>
        </div>

      </div><!--end v-for-->


      <div id="cnt2" v-for="(item2,index2) in details" v-bind:key="index2">
        <div>
          <h4>{{ product_title }}<span style="margin-left:20px;">履歴一覧</span></h4>
        </div>
      </div><!--end v-for-->

      <div id="tbl_1">
        <table>
          <thead>
            <tr>
              <th class="gc2">担当</th>
              <th class="gc2">受注番号</th>
              <th class="gc2">会社名</th>
              <th class="gc2">商品名</th>
              <th class="gc2">単位</th>
              <th class="gc2">入数</th>
              <th class="gc2">納入日</th>
              <th class="gc2">納入数</th>
              <th class="gc2">発注日</th>
              <th class="gc2">発注数</th>
              <th class="gc2">現在在庫</th>
              <th class="gc2">箱数</th>
              <th class="gc2">発注先</th>
              <th class="gc2">単価</th>
              <th class="gc2">合計</th>
              <th class="gc2">備考</th>
              <th class="gc2">メモ/ノート</th>
              <th class="gc2">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            <tr  v-for="(item,rowIndex) in details2" :key="rowIndex" v-bind:class="(item['id'] == edit_id) ? 'bgcolor3' : ''">
              <td>{{ item['charge'] }}</td>
              <td>{{ item['order_no'] }}</td>
              <td>{{ item['company_name'] }}</td>
              <td>{{ item['product_name'] }}</td>
              <td class="nbr">{{ item['unit'] }}</td>
              <td class="style1">{{ item['quantity'] }}</td>
              <td class="nbr">{{ item['supply_day'] }}</td>
              <td class="style1" v-bind:class="(item['supply_quantity'] === 0) ? 'color3' : ''">{{ item['supply_quantity'] }}</td>
              <td class="nbr">{{ item['order_day'] }}</td>
              <td class="style1" v-bind:class="(item['order_quantity'] === 0) ? 'color3' : ''">{{ item['order_quantity'] }}</td>
              <td class="style1" v-bind:style="(item['now_inventory'] === 0) ? 'color:red' : ''">{{ item['now_inventory'] }}</td>
              <td class="style1">{{ item['nbox'] }}</td>
              <td>{{ item['order_address'] }}</td>
              <td>{{ item['unit_price'] }}</td>
              <td>{{ item['total'] }}</td>
              <td>{{ item['remarks'] }}</td>
              <td>{{ item['note'] }}</td>
              <td><!-- id={{ item['id'] }} edit_id={{ edit_id }} --></td>
            </tr>
          </tbody>
        </table>
      </div><!-- end tbl_1 -->
      </div><!--end v-if="orinfo === 'z'"-->

    </div><!--end selectMode=='EDT'-->




    <!--<div class="gotop"><a href="#">▲</a></div>-->
    <div id="page_top"><a href="#"></a></div>
  </div>
  <!-- /main contentns  -->
</div>
</template>
<script>
import { provide } from 'vue';
//import toasted from "vue-toasted";
import moment from "moment";
//import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';
import { useRouter } from 'vue-router';
//import EditModeA from './TemplateEditModeA.vue';
//import EditModeZ from './TemplateEditModeZ.vue';

export default {
 components: {
    //EditModeA,
    //EditModeZ

  },
  mixins: [ requestable , checkable ],
  props: {
    order_info: {
      type: [String, Number],
      default: ""
    },
    order_no: {
      type: [String, Number],
      default: ""
    },
    company_id: {
      type: [String, Number],
      //type: String,
      default: ""
    },
    product_id2: {
      type: [String, Number],
      default: ""
    },
    receipt_day: {
      type: [String, Number],
      default: ""
    },
    delivery_day: {
      type: [String, Number],
      default: ""
    },
    orderfr: {
      type: [String, Number],
      default: ""
    }
  },
  computed: {
  },
  data() {
    return {
      form: {
        id: "",
        charge: "",
        order_no: "",
        company_name: "",
        company_id: "",
        product_name: "",
        product_id: "",
        unit: "",
        quantity: "",
        receipt_day: "",
        place_order: "",
        receipt: "",
        delivery_day: "",
        delivery: "",
        now_inventory: "",
        nbox: "",
        dnum: "",
        rnum: "",
        shipping_address: "",
        status: "",
        order_info: "",
        other1: "",
        marks: "",
        created_user: "",
        updated_user: "",
        created_at: "",
        updated_at: ""
      },
      messagevalidatesNew: [],
      settingmessage: [],
      details: [],
      details2: [],
      details3: [],
      edit_id: "",
      product_id: "",
      product_title: "",
      selectMode: "DEFAULT",
      actionmsgArr: [],
      acttitle: "",
      classObj1: "",
      view_switch: "off",
      i: 2,
      s_order_no: "",
      btnMode: 0,
      stock_month: "",
      totals: "",
      
    };
  },
  provide() {
    var smonth = this.stock_month;
    return {
      details: () => this.details,
      details2: () => this.details2,
      strs: smonth
      //details: this.details,
      //details2: this.details2
    }
  },
  setup() {
      /*
      provide('location', 'North Pole');
      provide('geolocation', {
        longitude: 90,
        latitude: 135
      });
      */
      //provide('strs', this.stock_month);
      //provide('details', this.details);
      //provide('details2', this.details2);

  },
  filters:{ 
    numberFormat: function(num){
      return num.toLocaleString();
    }
  },
  // マウント時
  mounted() {
      this.monthset();
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション
    checkFormStore: function() {
      this.messagevalidatesNew = [];
      var chkArray = [];
      var flag = true;
      // 会社名
      var required = true;
      var equalength = 0;
      var maxlength = 40;
      var itemname = '会社名';
      chkArray = 
        this.checkHeader(this.details[0].company_name, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // 商品名
      required = true;
      equalength = 0;
      maxlength = 80;
      itemname = '商品名';
      chkArray = 
        this.checkHeader(this.details[0].product_name, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }

      if (this.messagevalidatesNew.length > 0) {
        flag = false;
      }
      return flag;
    },
    checkFormInput: function() {
      this.messagevalidatesNew = [];
      var chkArray = [];
      var flag = true;
      // 月
      var required = true;
      var equalength = 0;
      var maxlength = 20;
      var itemname = '日付';
      chkArray = 
        this.checkHeader(this.stock_month, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }

      if (this.messagevalidatesNew.length > 0) {
        flag = false;
      }
      return flag;
    },
    // ------------------------ イベント処理 ------------------------------------
    NewStock()  {
      if (this.checkFormInput()) {
        this.getItem();
        setTimeout(this.AllStore, 3000);
        this.actionmsgArr.push(this.product_title + "登録データ作成中です...","");
        //console.log("NewStock in details = " + this.details);
        //this.AllStore();
        this.selectMode = 'LINEACTIVE';
      }

    },
    RestartStock()  {
      if (this.checkFormInput()) {
        this.actionmsgArr = [];
        this.getItemStock();
        this.actionmsgArr.push("" + this.stock_month + " 棚卸を開始します。");
        //console.log("NewStock in details = " + this.details);
        this.selectMode = 'LINEACTIVE';
      }
    },
    ForwardReverse(arraykey,q1) {
      //console.log("ForwardReverse in  = " + q1);
      //console.log("ForwardReverse in  = " + arraykey);
      this.sort_k = arraykey;
      this.sort_q = q1;

      var sort_target = arraykey; //ソート対象を変数で設定
      //if(q1 == 1) this.details3.sort((a, b) => a[sort_target] - b[sort_target]);
      //if(q1 == 2) this.details3.sort((a, b) => b[sort_target] - a[sort_target]);
      //console.log("ForwardReverse in details3 = " + this.details3);

      if(q1 == 1) {
        this.details3.sort(function(a,b){
          if(a[sort_target] > b[sort_target]) {
            return 1;
          }
          if(a[sort_target] < b[sort_target]) {
            return -1;
          }
          return 0;
        });
      }
      if(q1 == 2) {
        this.details3.sort(function(a,b){
          if(a[sort_target] > b[sort_target]) {
            return -1;
          }
          if(a[sort_target] < b[sort_target]) {
            return 1;
          }
          return 0;
        });
      }
      //console.log(this.details3);
    },
    StockBtn(eid,inv,nbox,details) {
      //var edit_id = eid;
      //console.log(edit_id);
      this.actionmsgArr.push("-------------------------------",eid + " = ID",inv + " = stock_now_inventory",nbox + " = stock_nbox",details);
      //this.selectMode = 'EDT';
      //this.getItemOne(eid,pid,pname);
    },
    InvBtn(eid,pid,pname,orderinfo) {
      var edit_id = eid;
      console.log("InvBtn in edit_id = " + edit_id);
      //this.actionmsgArr.push(eid + " = ID",pid + " = product_id",pname + " = product_name",orderinfo + " = order_info");
      this.selectMode = 'EDT';
      this.getItemOne(eid,pid,pname,orderinfo);
    },
    NewBtn()  {
      this.inputClear();
      this.selectMode = 'NEW';
    },
    searchBtn() {
      this.details = [];
      this.searchItem();
      this.selectMode = 'COMPLETE';
    },
    viewBtn(go) {
    var amari = this.i % go;
    if(amari == 0){
      this.view_switch = 'on';
    } else {
      this.view_switch = 'off';
    }
    this.i = this.i + 1;
    },
    // -------------------- サーバー処理 ----------------------------
    // 取得処理
    getItem() {
      this.inputClear();
      //console.log("getitem in");
      var arrayParams = { 
        order_info : this.order_info ,
        order_no : this.order_no,
        company_id : this.company_id,
        product_id2 : this.product_id2,
        receipt_day : this.receipt_day,
        delivery_day : this.delivery_day,
        orderfr : this.orderfr
        };
      //console.log("getitem in arrayParams['receipt_day'] = " + arrayParams['receipt_day']);
      this.postRequest("/stock_a/invget", arrayParams)
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          //console.log("getitem reason");
          this.serverCatch("取得");
        });
    },
    getItemStock() {
      this.inputClear();
      var sort_q = this.sort_q;
      //console.log('getItemStock in sort_q = ' + sort_q);
      var arrayParams = { 
        stock_month : this.stock_month,
        order_info : this.order_info,
        order_no : this.order_no,
        company_id : this.company_id,
        product_id2 : this.product_id2,
        receipt_day : this.receipt_day,
        delivery_day : this.delivery_day,
        orderfr : this.orderfr
        };
      //console.log("getitem in arrayParams['receipt_day'] = " + arrayParams['receipt_day']);
      this.postRequest("/stock_a/stockget", arrayParams)
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          //console.log("getItemStock reason");
          if(sort_q > 0 ) {
            //console.log('getthen in this.sort_q = ' + this.sort_k);
            //console.log('getthen in this.details3 = ' + this.details3);
            this.ForwardReverse(this.sort_k,this.sort_q);
          }
          this.serverCatch("取得");
        });
    },
    // 取得処理(単)
    getItemOne(e,p,pn,oi) {
      this.inputClear();
      console.log("getitem one in edit_id = " + e);
      console.log("getitem one in product_id = " + p);
      console.log("getitem one in order_info = " + oi);
      this.edit_id = e;
      this.product_id = p;
      this.product_title = pn;
      this.orinfo = oi;

      var arrayParams = {  edit_id : e , product_id : p};
      this.postRequest("/view_inventory_" + oi + "/getone", arrayParams)
        .then(response  => {
          //console.log(response);
          this.getThen(response);
        })
        .catch(reason => {
          //console.log("getitem reason");
          this.serverCatch("取得");
        });
    },
    // 検索処理
    searchItem() {
        console.log("searchItem in s_order_no = " + this.s_order_no);
        this.s_order_no = this.s_order_no;
        //this.classObj1 = "bgcolor3";
        this.acttitle = "検索";
        var motion_msg = "検索";
        var messages = [];
        var arrayParams = { s_order_no : this.s_order_no , order_info : 'a'};
        this.postRequest("/view_inventory_a/search", arrayParams)
          .then(response  => {
            this.putThenSearch(response, motion_msg);
            this.btnMode = 1;
          })
          .catch(reason => {
            this.serverCatch("検索");
          });
    },
    // ALLインサート処理
    AllStore(k) {
        //console.log("AllStore in details = " + this.details);
        console.log("AllStore in stock_month = " + this.stock_month);
        //this.product_title = this.form.product_name;
        this.classObj1 = "bgcolor3";
        this.acttitle = "棚卸データ登録";
        var messages = [];
        var arrayParams = {
          details : this.details,
          stock_month : this.stock_month,
          upkind : k,
        };
        this.postRequest("/stock_a/insert", arrayParams)
          .then(response  => {
            this.putThenStore(response, "棚卸データ登録");
          })
          .catch(reason => {
            this.serverCatch("登録");
          });
    },
    // 登録インサート処理
    dataStore() {
      if (this.checkFormStore()) {
        this.product_title = this.form.product_name;
        this.classObj1 = "bgcolor3";
        this.acttitle = "新規登録";
        var messages = [];
        var arrayParams = { form : this.form };
        this.postRequest("/view_inventory_a/insert", arrayParams)
          .then(response  => {
            this.putThenStore(response, "新規登録");
          })
          .catch(reason => {
            this.serverCatch("登録");
          });
      }
    },
    // ゴミ箱処理
    dataDel(index,k) {
      if (this.checkFormStore()) {
        this.product_title = this.form.product_name;
        this.classObj1 = "bgcolor4";
        this.acttitle = "ゴミ箱移動";
        var messages = [];
        var arrayParams = { details : this.details[index] , upkind : k };
        this.postRequest("/view_inventory_a/update", arrayParams)
          .then(response  => {
            this.putThenDel(response, "ゴミ箱へ移動");
          })
          .catch(reason => {
            this.serverCatch("ゴミ箱移動");
          });
      }
    },
    // 棚卸在庫登録処理
    stockUpdate(index,k) {
        //console.log("stockUpdate in edit_id = " + this.edit_id);
        //console.log("stockUpdate in product_id = " + this.product_id);
        var messages = [];
        var arrayParams = { details : this.details3[index] , upkind : k };
        var motion_msg = "";
        if (k == 6) motion_msg = '在庫カウント';
        this.postRequest("/stock_a/update", arrayParams)
          .then(response  => {
            this.putThenUpdate(response, motion_msg);
          })
          .catch(reason => {
            this.serverCatch(motion_msg);
          });
    },
    // 編集変更処理
    dataUpdate(index,k,oi) {
      if (this.checkFormStore()) {
        //console.log("dataUpdate in edit_id = " + this.edit_id);
        //console.log("dataUpdate in product_id = " + this.product_id);
        var messages = [];
        var arrayParams = { details : this.details[index] , upkind : k };
        var motion_msg = "";
        if (k == 0) motion_msg = '修正';
        if (k == 1) motion_msg = '在庫を更新';
        if (k == 2) motion_msg = '新しい商品追加';
        this.postRequest("/view_inventory_" + oi + "/update", arrayParams)
          .then(response  => {
            this.putThenHead(response, motion_msg, oi);
          })
          .catch(reason => {
            this.serverCatch(motion_msg);
          });
      }
    },
    // -------------------- 共通 ----------------------------
    // 取得正常処理
    getThen(response, eventtext) {
      //console.log('getthen in ' + this.sort_q);
      var sort_q = this.sort_q;
      var res = response.data;
      if (res.result) {
        console.log('getThen in res.result = ' + res.result);
        this.details = res.details;
        this.details2 = res.details2;
        this.details3 = res.details3;
        var pt = 0;
        //this.product_title = res.details[0].product_name;
        if (typeof this.details3 !== 'undefined') {
          console.log('getthen in this.details3.length = ' + this.details3.length); 
          this.details3.forEach(function(element, index) {
            console.log('getThen in details3.forEach = ' + element.cal_total_price);
            pt = pt + Number(element.cal_total_price);
            //if((typeof element.cal_total_price == 'string')) {
            //  pt = pt + Number(element.cal_total_price);
            //  console.log('getThen in typeof element.cal_total_price = ' + pt);
            //}
          });
          this.totals = pt;

          if (this.details3.length == 0) this.actionmsgArr.push("" + this.stock_month + " 該当データがありません");
        }
        //console.log('getthen in sort_q = ' + sort_q);
        if(sort_q > 10 ) {
          //console.log('getthen in this.sort_q = ' + this.sort_k);
          //console.log('getthen in this.details3 = ' + this.details3);
          this.ForwardReverse(this.sort_k,this.sort_q);
        }
        if(eventtext.length > 0) {
          this.$toasted.show(this.product_title + " " + eventtext + "しました");
        }

      }
      else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("取得");
        }
      }
    },
    // 検索系正常処理
    putThenSearch(response, eventtext) {
      var messages = [];
      var res = response.data;
      //if (res.result) {
      if (res.details.length > 0) {
          this.details = res.details;
          //this.classObj1 = (this.details[0].status == 'newest') ? 'bgcolor3' : '';
          //if(this.details[0].status == 'newest') this.classObj1 = "bgcolor5";
          this.product_title = "受注番号 " + res.s_order_no;
          console.log("putThenSearch in res.s_order_no = " + res.s_order_no);
          this.$toasted.show(this.product_title + " " + eventtext + "しました");
          this.actionmsgArr.push(this.product_title + " を検索しました。","");
      } else {
          this.actionmsgArr.push(this.s_order_no + " が見つかりませんでした。","");
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    // 更新系正常処理
    putThenHead(response, eventtext, oi) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        if(res.id) {
          this.edit_id = res.id;
          //console.log("putThenHead in res.pid = " + res.product_id);
          this.product_id = res.product_id;
          this.product_title = res.product_name;
        }
        this.$toasted.show(this.product_title + " " + eventtext + "しました");
        //console.log("putThenHead in edit_id = " + this.edit_id);
        //console.log("putThenHead in product_id = " + this.product_id);
        this.getItemOne(this.edit_id,this.product_id,this.product_title,oi);
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    // 棚卸更新系正常処理
    putThenUpdate(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        if(res.id) {
          this.edit_id = res.id;
          //console.log("putThenUpdate in res.pid = " + res.product_id);
          this.product_id = res.product_id;
          this.product_title = res.product_name;
        }
        this.$toasted.show(this.product_title + " " + eventtext + "しました");
        //console.log("putThenHead in edit_id = " + this.edit_id);
        //console.log("putThenHead in product_id = " + this.product_id);
        this.getItemStock();
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    // 新規系正常処理
    putThenStore(response, eventtext) {
      var messages = [];
      var res = response.data;
      this.product_title = this.stock_month + "";
      this.actionmsgArr = [];
      if (res.result) {
        this.re_id = res.id;
        this.getItemStock();
        this.$toasted.show(this.product_title + "を" + eventtext + "しました");
        this.actionmsgArr.push(this.stock_month + " 新しい月で棚卸の準備ができました。");

        //this.selectMode = 'COMPLETE';
        this.selectMode = 'LINEACTIVE';

      } else {
        this.details = [];
        this.details2 = [];
        this.details3 = [];
        if (res.update_num == 0) this.actionmsgArr.push(this.product_title + " は既に登録されている年月です。","違う年月を登録してください。");

        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    // 削除系正常処理
    putThenDel(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.re_id = res.id;

        this.getItemOne(this.re_id,this.product_id,this.product_title);
        this.$toasted.show(this.product_title + "を" + eventtext + "しました");
        this.actionmsgArr.push(this.product_title + "ゴミ箱へ移動しました。","");
        this.selectMode = 'COMPLETE';

      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    // 異常処理
    serverCatch(eventtext) {
      var messages = [];
      //messages.push("在庫管理" + eventtext + "に失敗しました");
      //this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    
    inputClear() {
      this.details = [];
      this.form.id = "";
      this.form.charge = "";
      this.form.order_no = "";
      this.form.company_name = "";
      this.form.company_id = "";
      this.form.product_name = "";
      this.form.product_id = "";
      this.form.unit = "";
      this.form.quantity = "";
      this.form.receipt_day = "";
      this.form.order_quantity = "";
      this.form.receipt = "";
      this.form.delivery_day = "";
      this.form.delivery = "";
      this.form.now_inventory = "";
      this.form.nbox = "";
      this.form.dnum = "";
      this.form.rnum = "";
      this.form.shipping_address = "";
      this.form.remarks = "";
      this.form.status = "";
      this.form.order_info = "";
      this.form.other1 = "";
      this.form.marks = "";
      this.form.created_user = "";
      this.form.updated_user = "";
      this.form.created_at = "";
      this.form.updated_at = "";
    },
    monthset: function()  {
      var date_obj = new Date();
      //console.log('todayset = ' + date_obj);
      this.today_year  = date_obj.getFullYear(); // 西暦年取得
      this.today_month = date_obj.getMonth();    // 月取得
      //console.log('todayset y = ' + this.today_year);
      //console.log('todayset m = ' + this.today_month);
      // 文字列として連結month_format
      this.stock_month = ('0000' + this.today_year).slice(-4) 
                      + '-' 
                      + ('00' + (this.today_month + 1)).slice(-2) 
      //console.log(this.stock_month);
      //return stock_month;
    },





  },

};


</script>
