<template>
<div>
  <!-- main contentns  -->
  <div id="">

    <div v-if="selectMode=='LINEACTIVE'">
      <div id="top_cnt">
        <h2>預かり一覧</h2>
        <form id="form1" name="form2">
          <input type="text" class="form_style bc1 w6e" v-model="s_company_name" maxlength="30" name="s_company_name">
          <button type="button" class="" @click="searchBtn2()">
            会社名 検索
          </button>
        </form>
        <form id="form1" name="form1">
          <input type="text" class="form_style bc1 w4e" v-model="s_order_no" maxlength="30" name="s_order_no">
          <button type="button" class="" @click="searchBtn()">
            受注番号 検索
          </button>
          <input type="text" class="form_style bc1 w8e" v-model="s_product_name" maxlength="30" name="s_product_name">
          <button type="button" class="" @click="searchBtn()">
            商品 検索
          </button>
        </form>
        <button type="button" class="" @click="NewBtn()">
          新規登録
        </button>
      </div>

      <div id="tbl_1">
        <table>
          <thead>
            <tr>
              <th class="gc1">担当</th>
              <th class="gc1">受注番号 <a href="./view_inventory?order_info=1&order_no=1">▲</a> <a href="./view_inventory?order_info=1&order_no=2">▼</a></th>
              <th class="gc1">会社名 <a href="./view_inventory?order_info=1&company_id=1">▲</a> <a href="./view_inventory?order_info=1&company_id=2">▼</a></th>
              <th class="gc1">商品名 <a href="./view_inventory?order_info=1&product_id2=1">▲</a> <a href="./view_inventory?order_info=1&product_id2=2">▼</a></th>
              <th class="gc1">単位</th>
              <th class="gc1">入数</th>
              <th class="gc1">入庫日 <a href="./view_inventory?order_info=1&receipt_day=1">▲</a> <a href="./view_inventory?order_info=1&receipt_day=2">▼</a></th>
              <th class="gc1">発注数</th>
              <th class="gc1">入庫数</th>
              <th class="gc1">出庫日 <a href="./view_inventory?order_info=1&delivery_day=1">▲</a> <a href="./view_inventory?order_info=1&delivery_day=2">▼</a></th>
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
            <!--
            <tr>
              <td>charge</td>
              <td>order_no</td>
              <td>company_name</td>
              <td>product_name</td>
              <td>unit</td>
              <td>quantity</td>
              <td>receipt_day</td>
              <td>order_quantity</td>
              <td>receipt</td>
              <td>delivery_day</td>
              <td>delivery</td>
              <td>now_inventory</td>
              <td>nbox</td>
              <td>dnum</td>
              <td>rnum</td>
              <td>shipping_address</td>
              <td>remarks</td>
              <td>button</td>
            </tr>
            -->
            <tr v-for="(item,rowIndex) in details" :key="rowIndex">
              <td>{{ item['charge'] }}</td>
              <td>{{ item['order_no'] }}</td>
              <td>{{ item['company_name'] }}</td>
              <td>{{ item['product_name'] }}</td>
              <td class="nbr">{{ item['unit'] }}</td>
              <td class="style1">{{ item['quantity'] }}</td>
              <td class="nbr">{{ item['receipt_day'] }}</td>
              <td class="style1">{{ item['order_quantity'] }}</td>
              <td class="style1">{{ item['receipt'] }}</td>
              <td class="nbr">{{ item['delivery_day'] }}</td>
              <td class="style1">{{ item['delivery'] }}</td>
              <td class="style1">{{ item['now_inventory'] }}</td>
              <td class="style1">{{ item['nbox'] }}</td>
              <td>{{ item['dnum'] }}</td>
              <td>{{ item['rnum'] }}</td>
              <td>{{ item['shipping_address'] }}</td>
              <td>{{ item['remarks'] }}</td>
              <td class="nbr">
                <button type="button" class="style1" @click="EditBtn(item['id'], item['product_id'], details[rowIndex].product_name, 'update', rowIndex)">
                更新
                </button>
                <button type="button" class="style2" @click="EditBtn(item['id'], item['product_id'], details[rowIndex].product_name, 'fix', rowIndex)">
                修正
                </button>
                <!--
                <button type="button" class="" @click="EditBtn(item['id'],item['product_id'],item['product_name'])">
                編集
                </button>
                -->
              </td>
            </tr>
          </tbody>
        </table>
      </div><!-- end tbl_1 -->
    </div><!--end selectMode=='LINEACTIVE'-->








    <div id="input_area_1" v-if="selectMode=='NEW'">
      <div id="top_cnt">
        <h2>預かり / 新規登録</h2>
        <button type="button" class="customize" @click="viewBtn(2)">
          管理者
        </button>
      </div>

      <div class="" v-if="messagevalidatesNew.length">
          <ul class="error-red color_red">
            <li v-for="(messagevalidate,index) in messagevalidatesNew" v-bind:key="index">{{ messagevalidate }}</li>
          </ul>
      </div>

      <div id="cnt1">
        <div class="inputgroup w1">
          <div class="cate gc1">担当</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style bc1"
              v-model="form.charge"
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
              v-model="form.order_no"
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
              v-model="form.company_name"
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
              v-model="form.product_name"
              maxlength="100"
              name="product_name"
            />
          </div>
        </div>
      </div><!--#### end id="cnt1" ####-->

      <div id="cnt1">
        <div class="inputgroup w1">
          <div class="cate gc1">単位</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style bc1"
              v-model="form.unit"
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
              v-model="form.quantity"
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
              v-model="form.receipt_day"
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
              v-model="form.order_quantity"
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
              v-model="form.receipt"
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
              v-model="form.delivery_day"
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
              v-model="form.delivery"
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
              v-model="form.now_inventory"
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
              v-model="form.nbox"
              maxlength="16"
              name="nbox"
            />
          </div>
        </div>
      </div><!--#### end id="cnt1" ####-->

      <div id="cnt1">
        <div class="inputgroup w2">
          <div class="cate gc1">出庫No</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style bc1"
              v-model="form.dnum"
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
              v-model="form.rnum"
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
              v-model="form.other1"
              maxlength="10"
              name="other1"
            />
          </div>
        </div>
        <div class="inputgroup w2">
          <div class="cate gc1">発送先</div>
          <div class="inputzone">
            <textarea class="form_style_t bc1" v-model="form.shipping_address" maxlength="100" name="shipping_address" rows="3"></textarea>
          </div>
        </div>
        <div class="inputgroup w2">
          <div class="cate gc1">備考</div>
          <div class="inputzone">
            <textarea class="form_style_t bc1" v-model="form.remarks" maxlength="191" name="remarks" rows="3"></textarea>
          </div>
        </div>
      </div><!--#### end id="cnt1" ####-->



      <div id="cnt1" v-if="view_switch=='on'">
        <div class="inputgroup w1">
          <div class="cate">グループ</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style"
              v-model="form.marks"
              maxlength="20"
              name="marks"
            />
          </div>
        </div>
        <div class="inputgroup w1">
          <div class="cate">作成ユーザー</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style"
              v-model="form.created_user"
              maxlength="20"
              name="created_user"
            />
          </div>
        </div>
        <div class="inputgroup w1">
          <div class="cate">修正ユーザー</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style"
              v-model="form.updated_user"
              maxlength="20"
              name="updated_user"
            />
          </div>
        </div>
        <div class="inputgroup w2">
          <div class="cate">作成日時</div>
          <div class="inputzone">
            <input
              type="datetime"
              class="form_style"
              v-model="form.created_at"
              maxlength="16"
              name="created_at"
            />
          </div>
        </div>
        <div class="inputgroup w2">
          <div class="cate">修正日時</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style"
              v-model="form.updated_at"
              maxlength="16"
              name="updated_at"
            />
          </div>
        </div>

        <div class="inputgroup w1">
          <div class="cate">会社ID</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style"
              v-model="form.company_id"
              maxlength="11"
              name="company_id"
            />
          </div>
        </div>
        <div class="inputgroup w1">
          <div class="cate">商品ID</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style"
              v-model="form.product_id"
              maxlength="11"
              name="product_id"
            />
          </div>
        </div>
        <div class="inputgroup w1">
          <div class="cate">ステータス</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style"
              v-model="form.status"
              maxlength="20"
              name="status"
            />
          </div>
        </div>
        <div class="inputgroup w1">
          <div class="cate">発注情報</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style"
              v-model="form.order_info"
              maxlength="20"
              name="order_info"
            />
          </div>
        </div>
        <div class="inputgroup w1">
          <div class="cate">削除フラグ</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style"
              v-model="form.is_deleted"
              maxlength="20"
              name="is_deleted"
            />
          </div>
        </div>
      </div><!--#### end id="cnt1" ####-->

      <div id="button1">
          <div class="btnstyle">
            <button type="button" class="style1" @click="dataStore()">この内容で新規登録する</button>
          </div>
      </div>

    </div><!--end selectMode=='NEW'-->


    <div id="input_area_1" v-if="selectMode=='COMPLETE'">
      <div>
        <h2>預かり / {{ acttitle }} 完了</h2>
      </div>

      <div>
        <button type="button" onClick="window.location=''">一覧へ</button>
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
              <td class="nbr">
                <!--
                id={{ item['id'] }} re_id={{ re_id }}              
                -->
                <div v-if="item['status']=='newest'">
                  <button type="button" class="style1" @click="EditBtn(item['id'], item['product_id'], details[rowIndex].product_name, 'update', rowIndex)">
                  更新
                  </button>
                  <button type="button" class="style2" @click="EditBtn(item['id'], item['product_id'], details[rowIndex].product_name, 'fix', rowIndex)">
                  修正
                  </button>
                </div>
                <div v-else>
                  <button type="button" class="style2" @click="EditBtn(item['id'], item['product_id'], details[rowIndex].product_name, 'fix', rowIndex)">
                  修正
                  </button>
                </div>
                <!--
                <button v-if="btnMode=='1'" type="button" class="" @click="EditBtn(item['id'], item['product_id'], details[rowIndex].product_name)">
                編集
                </button>
                -->
              </td>
            </tr>
          </tbody>
        </table>
      </div><!-- end tbl_1 -->
    </div><!--end v-if-->



    <div id="input_area_1" v-if="selectMode=='EDT'">

      <div id="top_cnt">
        <h2 v-if="btnMode=='update'">預かり / 更新</h2>
        <h2 v-if="btnMode=='fix'">預かり / 修正</h2>
        <button type="button" class="customize" @click="viewBtn(2)">
          管理者
        </button>
      </div>

      <div v-for="(item,index) in details" v-bind:key="item.id">

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
                v-bind:disabled="isDisabled"
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
                v-bind:disabled="isDisabled"
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
                v-bind:disabled="isDisabled"
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
                v-bind:disabled="isDisabled"
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
                v-bind:disabled="isDisabled"
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
                v-bind:disabled="isDisabled"
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
                type="text"
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
                type="text"
                class="form_style"
                v-model="details[index].updated_user"
                maxlength="20"
                name="updated_user"
              />
              
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
            <div class="cate">ID</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].id"
                maxlength="11"
                name="id"
              />
              <span>{{ details[index].id }}</span>
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
                type="text"
                class="form_style"
                v-model="details[index].status"
                maxlength="20"
                name="status"
              />
            </div>
          </div>
          <div class="inputgroup">
            <div class="cate">発注情報</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style"
                v-model="details[index].order_info"
                maxlength="20"
                name="order_info"
              />
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

          <div id="button1">
            <button type="button" class="" @click="recordDel(index,'all')">この商品（履歴含む）を削除</button>
            <button type="button" class="" @click="recordDel(index,'one')">この登録（レコード）を削除</button>
          </div>
        </div><!--end v-if="view_switch=='on'-->


        <div id="button1">
          <div>
            <div class="btnstyle" v-if="btnMode==='update'">
              <button type="button" class="style1" @click="dataUpdate(index,1)">在庫の更新</button>
            </div>
            <div class="btnstyle" v-if="btnMode==='fix'">
              <button type="button" class="style2" @click="dataUpdate(index,0)">在庫の修正</button>
            </div>
          </div>
          <div>
            <!--
            <div class="btnstyle">
              <button type="button" class="" @click="dataUpdate(index,2)"><span>{{ item['company_name'] }} </span><span>の新しい商品を登録する</span></button>
            </div>
            -->
            <div class="btnstyle">
              <button type="button" class="" @click="backLine()">一覧へ</button>
            </div>
            <div class="btnstyle">
              <button type="button" class="" @click="resultLine()">検索一覧へ</button>
            </div>
            <div class="btnstyle" v-if="btnMode==='fix'">
              <button type="button" class="" @click="dataDel(index,4)">ゴミ箱へ移す</button>
            </div>
            <div class="btnstyle" v-if="btnMode==='great'">
              <button type="button" class="" @click="recordDel(index,'one')">この登録を削除</button>
            </div>
          </div>
        </div>




        <!--
        <div id="button1">
          <div>
            <div class="btnstyle">
              <button type="button" class="style1" @click="dataUpdate(index,1)">在庫の更新</button>
            </div>
            <div class="btnstyle">
              <button type="button" class="style2" @click="dataUpdate(index,0)">在庫の修正</button>
            </div>
          </div>
          <div>
            
            <div class="btnstyle">
              <button type="button" class="" @click="dataUpdate(index,2)"><span>{{ item['company_name'] }} </span><span>の新しい商品を登録する</span></button>
            </div>
            
            <div class="btnstyle">
              <button type="button" class="" @click="dataDel(index,4)">ゴミ箱へ移す</button>
            </div>
          </div>
        </div>
        -->

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
              <td class="nbr">
                <!-- id={{ item['id'] }} edit_id={{ edit_id }} product_id= {{ product_id }} -->
                <div v-if="item['status']=='newest'">
                  <button type="button" class="style1" @click="EditBtn(item['id'], item['product_id'], details2[rowIndex].product_name, 'update', rowIndex)">
                  更新
                  </button>
                  <button type="button" class="style2" @click="EditBtn(item['id'], item['product_id'], details2[rowIndex].product_name, 'fix', rowIndex)">
                  修正
                  </button>
                </div>
                <div v-else>
                  <button type="button" class="style2" @click="EditBtn(item['id'], item['product_id'], details2[rowIndex].product_name, 'fix', rowIndex)">
                  修正
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div><!-- end tbl_1 -->

    </div><!--end selectMode=='EDT'-->

    <div id="page_top"><a href="#"></a></div>
  </div>
  <!-- /main contentns  -->
</div>
</template>
<script>
//import toasted from "vue-toasted";
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  mixins: [ requestable , checkable , dialogable],
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
    get_NewDateYMD: function() {
      return moment(new Date()).format("YYYYMMDD");
    },
    get_UpDate: function() {
      return moment(details[index].updated_at).format( 'YYYY年MM月DD日' );
    },
    get_CrDate: function() {
      return moment(details[index].created_at).format( 'YYYY年MM月DD日' );
    }
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
      edit_id: "",
      product_id: "",
      product_title: "",
      selectMode: "LINEACTIVE",
      actionmsgArr: [],
      acttitle: "",
      classObj1: "",
      view_switch: "off",
      i: 2,
      s_order_no: "",
      s_company_name: "",
      s_product_name: "",
      btnMode: 0,
      isDisabled: "",
    };
  },
  // マウント時
  mounted() {
      this.getItem();
      //this.todayset();
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
        this.checkHeader(this.form.company_name, required, equalength, maxlength, itemname);
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
        this.checkHeader(this.form.product_name, required, equalength, maxlength, itemname);
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
    EditBtnxxxx(eid,pid,pname) {
      //var edit_id = eid;
      //console.log("EditBtn in");
      //console.log(edit_id);
      this.selectMode = 'EDT';
      this.getItemOne(eid,pid,pname);
    },
    EditBtn(eid,pid,pname,smode,index) {
      //var edit_id = eid;
      //console.log("EditBtn in");
      //console.log(edit_id);
      this.selectMode = 'EDT';
      this.btnMode = smode;
      this.getItemOne(eid,pid,pname,smode);
      if(smode === 'fix') {
        this.isDisabled = false;
      }
      else if(smode === 'update') {
        this.isDisabled = true;
      }
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
    searchBtn2() {
      this.details = [];
      this.searchItem2();
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
    backLine() {
      this.selectMode = "LINEACTIVE";
      const sc = this.selectCnt;
      this.getItem(sc);

    },
    resultLine() {
      this.details = [];
      this.searchItem();
      this.selectMode = "COMPLETE";
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
      //console.log("getitem in arrayParams['delivery_day'] = " + arrayParams['delivery_day']);
      this.postRequest("/view_inventory_a/get", arrayParams)
        .then(response  => {
          //console.log(response);
          this.getThen(response);
        })
        .catch(reason => {
          //console.log("getitem reason");
          this.serverCatch("取得");
        });
    },
    // 取得処理(単)
    getItemOne(e,p,pn,md) {
      this.inputClear();
      //console.log("getitem one in edit_id = " + e);
      //console.log("getitem one in product_id = " + p);
      this.edit_id = e;
      this.product_id = p;
      this.product_title = pn;
      var arrayParams = {  edit_id : e , product_id : p};
      this.postRequest("/view_inventory_a/getone", arrayParams)
        .then(response  => {
          //console.log(response);
          this.getThen(response, md);
        })
        .catch(reason => {
          //console.log("getitem reason");
          this.serverCatch("取得");
        });
    },
    // 検索処理
    searchItem() {
        //console.log("searchItem in s_order_no = " + this.s_order_no);
        this.s_order_no = this.s_order_no;
        //this.classObj1 = "bgcolor3";
        this.acttitle = "検索";
        var motion_msg = "検索";
        var messages = [];
        var arrayParams = { s_order_no : this.s_order_no , s_company_name : this.s_company_name , s_product_name : this.s_product_name , order_info : 'a'};
        this.postRequest("/view_inventory_a/search", arrayParams)
          .then(response  => {
            this.putThenSearch(response, motion_msg);
            this.btnMode = 1;
          })
          .catch(reason => {
            this.serverCatch("検索");
          });
    },
    searchItem2() {
        console.log("searchItem2 in s_company_name = " + this.s_company_name);
        //this.classObj1 = "bgcolor3";
        this.acttitle = "検索";
        var motion_msg = "検索";
        var messages = [];
        var arrayParams = { s_company_name : this.s_company_name , order_info : 'a'};
        this.postRequest("/view_inventory_a/search", arrayParams)
          .then(response  => {
            this.putThenSearch(response, motion_msg);
            this.btnMode = 1;
          })
          .catch(reason => {
            this.serverCatch("検索");
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
    // レコード削除処理
    recordDel(index,dk) {
        var messages = [];
        messages.push("この登録を削除しますか？");
        this.htmlMessageSwal(this.details[index].product_name, messages, "info", true, true).then(
          result => {
            if (result) {
              //this.storeData();
              var arrayParams = { details : this.details[index] , delkind : dk  };
              this.postRequest("/view_inventory_a/delete", arrayParams)
                .then(response  => {
                  this.putThenRecordDel(response, "削除");
                })
                .catch(reason => {
                  this.serverCatch("削除");
                });

            }
          }
        );

    },
    // 編集変更処理
    dataUpdate(index,k) {
      if (this.checkFormStore()) {
        //console.log("dataUpdate in edit_id = " + this.edit_id);
        //console.log("dataUpdate in product_id = " + this.product_id);
        var messages = [];
        var arrayParams = { details : this.details[index] , upkind : k };
        var motion_msg = "";
        if (k == 0) motion_msg = '修正';
        if (k == 1) motion_msg = '在庫を更新';
        if (k == 2) motion_msg = '新しい商品追加';
        this.postRequest("/view_inventory_a/update", arrayParams)
          .then(response  => {
            this.putThenHead(response, motion_msg, k);
          })
          .catch(reason => {
            this.serverCatch(motion_msg);
          });
      }
    },
    // -------------------- 共通 ----------------------------
    // 取得正常処理
    getThen(response) {
      var res = response.data;
      //console.log('getthen in res = ' + res);
      if (res.result) {
        this.details = res.details;
        this.details2 = res.details2;
        //this.count = this.details.length;
        //this.before_count = this.count;
        if ( this.details.length > 0) {
          this.form.id = this.details[0].id;
          this.form.charge = this.details[0].charge;
          this.form.order_no = this.details[0].order_no;
          this.form.company_name = this.details[0].company_name;
          this.form.company_id = this.details[0].company_id;
          this.form.product_name = this.details[0].product_name;
          this.form.product_id = this.details[0].product_id;
          this.form.unit = this.details[0].unit;
          this.form.quantity = this.details[0].quantity;
          this.form.receipt_day = this.details[0].receipt_day;
          this.form.order_quantity = this.details[0].order_quantity;
          this.form.receipt = this.details[0].receipt;
          this.form.delivery_day = this.details[0].delivery_day;
          this.form.delivery = this.details[0].delivery;
          this.form.now_inventory = this.details[0].now_inventory;
          this.form.nbox = this.details[0].nbox;
          this.form.dnum = this.details[0].dnum;
          this.form.rnum = this.details[0].rnum;
          this.form.shipping_address = this.details[0].shipping_address;
          this.form.remarks = this.details[0].remarks;
          this.form.status = this.details[0].status;
          this.form.order_info = this.details[0].order_info;
          this.form.other1 = this.details[0].other1;
          this.form.marks = this.details[0].marks;
          this.form.created_user = this.details[0].created_user;
          this.form.updated_user = this.details[0].updated_user;
          this.form.created_at = this.details[0].created_at;
          this.form.updated_at = this.details[0].updated_at;

        } else {
          
        }
      } else {
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
          this.product_title = res.s_order_no + res.s_company_name;
          console.log("putThenSearch in res.s_order_no = " + res.s_order_no);
          this.$toasted.show(this.product_title + " " + eventtext + "しました");
          this.actionmsgArr.push(this.product_title + " を検索しました。","");
      } else {
          this.actionmsgArr.push(this.s_order_no + this.s_company_name + " が見つかりませんでした。","");
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    // 更新系正常処理
    putThenHead(response, eventtext, k) {
      let object_mode = {0: 'fix', 1: 'update', 2: 'new'};
      console.log('key: ' + k + 'value: ' + object_mode[k]);
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
        this.getItemOne(this.edit_id,this.product_id,this.product_title,object_mode[k]);
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
      if (res.result) {
        this.re_id = res.id;

        this.getItemOne(this.re_id,this.product_id,this.product_title);
        this.$toasted.show(this.product_title + "を" + eventtext + "しました");
        this.actionmsgArr.push(this.product_title + "を新規登録しました。","");
        this.selectMode = 'COMPLETE';

      } else {
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
    // レコード削除正常処理
    putThenRecordDel(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.re_id = res.id;
        this.acttitle = "削除";
        this.getItemOne(this.re_id,this.product_id,this.product_title);
        this.$toasted.show(this.product_title + "を" + eventtext + "しました");
        this.actionmsgArr.push(this.product_title + " を削除しました。");
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
      this.form.is_deleted = "";
    },

    todayset()  {
      var date_obj = new Date();
      console.log('todayset = ' + date_obj);
      this.today_year  = date_obj.getFullYear(); // 西暦年取得
      this.today_month = date_obj.getMonth();    // 月取得
      this.today_day   = date_obj.getDate();     // 日取得
      console.log('todayset y = ' + this.today_year);
      console.log('todayset m = ' + this.today_month);
      console.log('todayset d = ' + this.today_day);

      // 文字列として連結
      var today_format = ('0000' + this.today_year).slice(-4) 
                      + '/' 
                      + ('00' + (this.today_month + 1)).slice(-2) 
                      + '/' 
                      + ('00' + this.today_day).slice(-2);
      console.log(today_format);
      this.days_max = new Date(this.today_year, this.today_month, 0).getDate();

      return today_format;
    },

    get_days: function () {
      this.days_max = new Date(this.year, this.month, 0).getDate();
    },
    get_todays (y,m) {
      this.days_max = new Date(y, m, 0).getDate();
    }




  }
};
</script>
