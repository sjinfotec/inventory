<template>
<div>
  <!-- main contentns  -->
  <div id="">

    <div v-if="selectMode=='HOME'">
      <div id="btn_top">
          <button type="button" class="" @click="SelectContentsBtn('a')">
            在庫 1F
          </button>
          <button type="button" class="" @click="SelectContentsBtn('b')">
            在庫 2F
          </button>
          <button type="button" class="" @click="SelectContentsBtn('c')">
            在庫 3F
          </button>
      </div>
    </div>




    <div v-if="selectMode=='LINEACTIVE'">
      <div id="top_cnt">
        <h2 class="h2gc1" v-if="selectCnt=='a'">資材在庫一覧 1F</h2>
        <h2 class="h2gc2" v-if="selectCnt=='b'">資材在庫一覧 2F</h2>
        <h2 class="h2gc3" v-if="selectCnt=='c'">資材在庫一覧 3F</h2>
        <form id="form1" name="form2">
          <input type="text" class="form_style bc1" v-model="s_charge" maxlength="30" name="s_charge">
          <button type="button" class="" @click="searchBtn()">
            担当 検索
          </button>
        </form>
        <form id="form1" name="form1">
          <input type="text" class="form_style bc1" v-model="s_product_name" maxlength="30" name="s_product_name">
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
              <th class="gc2">日付 <a href="./material_management?mdate=1">▲</a> <a href="./material_management?mdate=2">▼</a></th>
              <th class="gc2">部署<!-- <a href="./material_management?department=1">▲</a> <a href="./material_management?department=2">▼</a>--></th>
              <th class="gc2">担当 <a href="./material_management?charge=1">▲</a> <a href="./material_management?charge=2">▼</a></th>
              <th class="gc2">商品名 <a href="./material_management?product_name=1">▲</a> <a href="./material_management?product_name=2">▼</a></th>
              <th class="gc2">単位</th>
              <th class="gc2">入庫数</th>
              <th class="gc2">出庫数</th>
              <th class="gc2">現在在庫</th>
              <!--
              <th class="gc2">箱数</th>
              <th class="gc2">発注先</th>
              <th class="gc2">単価</th>
              <th class="gc2">合計</th>
              -->
              <th class="gc2">備考</th>
              <!--<th class="gc2">メモ/ノート</th>-->
              <th class="gc2">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item,rowIndex) in details" :key="rowIndex">
              <td class="nbr">{{ item['mdate'] }}</td>
              <td>{{ item['department'] }}</td>
              <td>{{ item['charge'] }}</td>
              <td>{{ item['product_name'] }}</td>
              <td class="nbr">{{ item['unit'] }}</td>
              <!--<td class="style1">{{ item['quantity'] }}</td>-->
              <td class="style1">{{ item['receipt'] }}</td>
              <td class="style1">{{ item['delivery'] }}</td>
              <td class="style1">{{ item['now_inventory'] }}</td>
              <!--
              <td class="style1">{{ item['nbox'] }}</td>
              <td>{{ item['order_address'] }}</td>
              <td>{{ item['unit_price'] }}</td>
              <td>{{ item['total'] }}</td>
              -->
              <td>{{ item['remarks'] }}</td>
              <!--<td>{{ item['note'] }}</td>-->
              <td class="nbr">
                <button type="button" class="style1" @click="EditBtn(item['id'], item['product_code'], details[rowIndex].product_name, 'update', rowIndex)">
                更新
                </button>
                <button type="button" class="style2" @click="EditBtn(item['id'], item['product_code'], details[rowIndex].product_name, 'fix', rowIndex)">
                修正
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="" v-if="details == ''">該当するデータがありません</div>
      </div><!-- end tbl_1 -->
    </div><!--end selectMode=='LINEACTIVE'-->




    <div id="input_area_1" v-if="selectMode=='NEW'">
      <div id="top_cnt">
        <h2>資材在庫 / 新規登録</h2>
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
          <div class="cate gc2">日付 本日{{itsdate}}</div>
          <div class="inputzone">
            <input
              type="date"
              class="form_style bc2"
              v-model="form.mdate"
              maxlength="12"
              name="mdate"
            />
          </div>
        </div>
        <div class="inputgroup w1">
          <div class="cate gc2">部署</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style bc2"
              v-model="form.department"
              maxlength="20"
              name="department"
            />
          </div>
        </div>
        <div class="inputgroup w2">
          <div class="cate gc2">担当</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style bc2"
              v-model="form.charge"
              maxlength="20"
              name="charge"
            />
          </div>
        </div>
      </div><!--## end id="cnt1" ##-->

      <div id="cnt1">
        <div class="inputgroup w3">
          <div class="cate gc2">商品名</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style bc2"
              v-model="form.product_name"
              maxlength="100"
              name="product_name"
            />
          </div>
        </div>
        <div class="inputgroup w1">
          <div class="cate gc2">Code Number</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style bc2"
              v-model="form.product_number"
              maxlength="30"
              name="product_number"
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
              v-model="form.unit"
              maxlength="10"
              name="unit"
            />
          </div>
        </div>
        <!--
        <div class="inputgroup w1">
          <div class="cate gc2">入数</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style bc2"
              v-model="form.quantity"
              maxlength="11"
              name="quantity"
            />
          </div>
        </div>
        -->
        <div class="inputgroup w1">
          <div class="cate gc2">入庫数</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style bc2"
              v-model="form.receipt"
              maxlength="11"
              name="receipt"
            />
          </div>
        </div>
        <div class="inputgroup w1">
          <div class="cate gc2">出庫数</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style bc2"
              v-model="form.delivery"
              maxlength="11"
              name="delivery"
            />
          </div>
        </div>

        <div class="inputgroup w1">
          <div class="cate gc2">現在在庫</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style bc2"
              v-model="form.now_inventory"
              maxlength="11"
              name="now_inventory"
            />
          </div>
        </div>
        <!--
        <div class="inputgroup w1">
          <div class="cate gc2">箱数</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style bc2"
              v-model="form.nbox"
              maxlength="16"
              name="nbox"
            />
          </div>
        </div>
        -->
      </div><!--## end id="cnt1" ##-->

      <div id="cnt1">
        <!--
        <div class="inputgroup w2">
          <div class="cate gc2">発注先</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style bc2"
              v-model="form.order_address"
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
              v-model="form.unit_price"
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
              v-model="form.total"
              maxlength="100"
              name="total"
            />
          </div>
        </div>
        -->
        <div class="inputgroup w4">
          <div class="cate gc2">備考</div>
          <div class="inputzone">
            <textarea class="form_style_t bc2" v-model="form.remarks" maxlength="191" name="remarks" rows="3"></textarea>
          </div>
        </div>
        <!--
        <div class="inputgroup w2">
          <div class="cate gc2">メモ/ノート</div>
          <div class="inputzone">
            <textarea class="form_style_t bc2" v-model="form.note" maxlength="191" name="note" rows="3"></textarea>
          </div>
        </div>
        -->
      </div><!--## end id="cnt1" ##-->

      <div id="cnt1" v-if="view_switch=='on'">
        <div class="inputgroup w1">
          <div class="cate">グループ</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style"
              v-model="form.marks"
              maxlength="10"
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
              type="text"
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
          <div class="cate">商品CODE</div>
          <div class="inputzone">
            <input
              type="text"
              class="form_style"
              v-model="form.product_code"
              maxlength="11"
              name="product_code"
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
      </div><!--## end id="cnt1" ##-->

      <div id="button1">
          <div class="btnstyle">
            <button type="button" class="style1" @click="dataStore()">この内容で新規登録する</button>
          </div>
      </div>

    </div><!--end selectMode=='NEW'-->


    <div id="input_area_1" v-if="selectMode=='COMPLETE'">
      <div>
        <h2>在庫 / {{ acttitle }} 完了</h2>
      </div>
            <div id="btn_cnt2">
              <button type="button" class="" @click="backLine()">一覧へ</button>
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
              <th class="gc2">日付</th>
              <th class="gc2">部署</th>
              <th class="gc2">担当</th>
              <th class="gc2">商品名</th>
              <th class="gc2">単位</th>
              <!--<th class="gc2">入数</th>-->
              <th class="gc2">入庫数</th>
              <th class="gc2">出庫数</th>
              <th class="gc2">在庫</th>
              <!--
              <th class="gc2">箱数</th>
              <th class="gc2">発注先</th>
              <th class="gc2">単価</th>
              <th class="gc2">合計</th>
              -->
              <th class="gc2">備考</th>
              <!--
              <th class="gc2">メモ/ノート</th>
              -->
              <th class="gc2">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item,rowIndex) in details" :key="rowIndex" v-bind:class="classObj1">
              <td class="nbr">{{ item['mdate'] }}</td>
              <td>{{ item['department'] }}</td>
              <td>{{ item['charge'] }}</td>
              <td v-bind:class="(item['status'] == 'newest') ? 'bgcolor5' : ''">{{ item['product_name'] }}</td>
              <td class="nbr">{{ item['unit'] }}</td>
              <!--<td class="style1">{{ item['quantity'] }}</td>-->
              <td class="style1" v-bind:class="(item['receipt'] === 0) ? 'color3' : ''">{{ item['receipt'] }}</td>
              <td class="style1" v-bind:class="(item['delivery'] === 0) ? 'color3' : ''">{{ item['delivery'] }}</td>
              <td class="style1" v-bind:style="(item['now_inventory'] === 0) ? 'color:red' : ''">{{ item['now_inventory'] }}</td>
              <!--
              <td class="style1">{{ item['nbox'] }}</td>
              <td>{{ item['order_address'] }}</td>
              <td>{{ item['unit_price'] }}</td>
              <td>{{ item['total'] }}</td>
              -->
              <td>{{ item['remarks'] }}</td>
              <!--
              <td>{{ item['note'] }}</td>
              -->
              <td>
                <!--
                id={{ item['id'] }} re_id={{ re_id }}
                -->
                <div v-if="btnMode === 'off'"></div>
                <div v-else></div>
                <div v-if="item['status']=='newest'">
                  <button type="button" class="style1" @click="EditBtn(item['id'], item['product_code'], details[rowIndex].product_name, 'update', rowIndex)">
                  更新
                  </button>
                  <button type="button" class="style2" @click="EditBtn(item['id'], item['product_code'], details[rowIndex].product_name, 'fix', rowIndex)">
                  修正
                  </button>
                </div>
                <div v-else>
                  <button type="button" class="style2" @click="EditBtn(item['id'], item['product_code'], details[rowIndex].product_name, 'fix', rowIndex)">
                  修正
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div><!-- end tbl_1 -->
    </div><!--end v-if-->






    <div id="input_area_1" v-if="selectMode=='EDT'">

      <div id="top_cnt">
        <h2 v-if="btnMode==='update'">在庫 / 更新</h2>
        <h2 v-if="btnMode==='fix'">在庫 / 修正</h2>
        <button type="button" class="customize" @click="viewBtn(2)">
          追加情報
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
            <div class="cate gc2">日付</div>
            <div class="inputzone">
              <input
                type="date"
                class="form_style bc2"
                v-model="details[index].mdate"
                maxlength="12"
                name="mdate"
              />
            </div>
          </div>
          <div class="inputgroup w1">
            <div class="cate gc2">部署</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc2"
                v-model="details[index].department"
                maxlength="40"
                name="department"
              />
            </div>
          </div>
          <div class="inputgroup w2">
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
        </div>
        <div id="cnt1">
          <div class="inputgroup w3">
            <div class="cate gc2">商品名</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc2"
                v-model="details[index].product_name"
                maxlength="100"
                name="product_name"
                v-bind:disabled="isDisabled"
              />
            </div>
          </div>
          <div class="inputgroup w1">
            <div class="cate gc2">Code Number</div>
            <div class="inputzone">
              <input
                type="text"
                class="form_style bc2"
                v-model="details[index].product_number"
                maxlength="100"
                name="product_number"
                v-bind:disabled="isDisabled"
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
                v-bind:disabled="isDisabled"
              />
            </div>
          </div>
          <!--
          <div class="inputgroup w1">
            <div class="cate gc2">入数</div>
            <div class="inputzone">
              <input
                type="number"
                class="form_style bc2"
                v-model.number="details[index].quantity"
                maxlength="11"
                name="quantity"
              />
            </div>
          </div>
          -->
          <div class="inputgroup w1">
            <div class="cate gc2">入庫数</div>
            <div class="inputzone">
              <input
                type="number"
                min="0"
                class="form_style bc2"
                v-model.number="details[index].receipt"
                maxlength="11"
                name="receipt"
              />
            </div>
          </div>
          <div class="inputgroup w1">
            <div class="cate gc2">出庫数</div>
            <div class="inputzone">
              <input
                type="number"
                min="0"
                class="form_style bc2"
                v-model.number="details[index].delivery"
                maxlength="11"
                name="delivery"
              />
            </div>
          </div>
          <div class="inputgroup w1">
            <div class="cate gc2">在庫</div>
            <div class="inputzone" v-if="btnMode ==='update'">
              <input
                type="number"
                class="form_style bc2"
                v-model.number="details[index].now_inventory + details[index].receipt - details[index].delivery"
                maxlength="11"
                name="now_inventory"
                v-bind:disabled="isDisabled"
              />
            </div>
            <div class="inputzone" v-else>
              <input
                type="number"
                class="form_style bc2"
                v-model.number="details[index].now_inventory"
                maxlength="11"
                name="now_inventory"
                v-bind:disabled="isDisabled"
              />
            </div>
          </div>
          <!--v-bind:disabled="isDisabled"
          v-bind:readonly="isDisabled"
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
          -->
        </div>
        <div id="cnt1">
          <!--
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
          -->
          <div class="inputgroup w4">
            <div class="cate gc2">備考</div>
            <div class="inputzone">
              <textarea class="form_style_t bc2" v-model="details[index].remarks" maxlength="191" name="remarks" rows="3"></textarea>
            </div>
          </div>
          <!--
          <div class="inputgroup w2">
            <div class="cate gc2">メモ/ノート</div>
            <div class="inputzone">
              <textarea class="form_style_t bc2" v-model="details[index].note" maxlength="191" name="note" rows="3"></textarea>
            </div>
          </div>
          -->
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
            <div class="cate">商品CODE</div>
            <div class="inputzone">
              <input
                type="hidden"
                class="form_style"
                v-model="details[index].product_code"
                maxlength="11"
                name="product_code"
              />
              <span>{{ details[index].product_code }}</span>
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

        </div>
        <div id="cnt1" v-if="view_switch=='on'">
        </div>


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
              <button type="button" class="" @click="dataDel(index,4)">抹消</button>
            </div>
            <div class="btnstyle" v-if="btnMode==='great'">
              <button type="button" class="" @click="recordDel(index,'one')">この登録を削除</button>
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

              <th class="gc2">日付</th>
              <th class="gc2">部署</th>
              <th class="gc2">担当</th>
              <th class="gc2">商品名</th>
              <th class="gc2">単位</th>
              <th class="gc2">入庫数</th>
              <th class="gc2">出庫数</th>
              <th class="gc2">現在在庫</th>
              <!--
              <th class="gc2">箱数</th>
              <th class="gc2">発注先</th>
              <th class="gc2">単価</th>
              <th class="gc2">合計</th>
              -->
              <th class="gc2">備考</th>
              <!--<th class="gc2">&nbsp;</th>-->
            </tr>
          </thead>
          <tbody>
            <tr  v-for="(item,rowIndex) in details2" :key="rowIndex" v-bind:class="(item['id'] == edit_id) ? 'bgcolor3' : ''"">
              <td>{{ item['mdate'] }}</td>
              <td>{{ item['department'] }}</td>
              <td>{{ item['charge'] }}</td>
              <td>{{ item['product_name'] }}</td>
              <td class="nbr">{{ item['unit'] }}</td>
              <!--<td class="style1">{{ item['quantity'] }}</td>-->
              <td class="style1" v-bind:class="(item['receipt'] === 0) ? 'color3' : ''">{{ item['receipt'] }}</td>
              <td class="style1" v-bind:class="(item['delivery'] === 0) ? 'color3' : ''">{{ item['delivery'] }}</td>
              <td class="style1" v-bind:style="(item['now_inventory'] === 0) ? 'color:red' : ''">{{ item['now_inventory'] }}</td>
              <!--
              <td class="style1">{{ item['nbox'] }}</td>
              <td>{{ item['order_address'] }}</td>
              <td>{{ item['unit_price'] }}</td>
              <td>{{ item['total'] }}</td>
              -->
              <td>{{ item['remarks'] }}</td>
              <!--<td>{{ item['note'] }}</td>-->
              <!--<td>id={{ item['id'] }} edit_id={{ edit_id }}</td>-->
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
    charge: {
      type: [String, Number],
      //type: String,
      default: ""
    },
    product_name: {
      type: [String, Number],
      default: ""
    },
    orderfr: {
      type: [String, Number],
      default: ""
    }
  },
  data() {
    return {
      form: {
        id: "",
        mdate: "",
        department: "",
        charge: "",
        product_name: "",
        product_code: "",
        product_number: "",
        unit: "",
        quantity: "",
        receipt: "",
        delivery: "",
        now_inventory: "",
        nbox: "",
        order_address: "",
        unit_price: "",
        total: "",
        remarks: "",
        note: "",
        status: "",
        marks: "",
        created_user: "",
        updated_user: "",
        created_at: "",
        updated_at: "",
        is_deleted: ""
      },
      messagevalidatesNew: [],
      settingmessage: [],
      details: [],
      details2: [],
      edit_id: "",
      product_code: "",
      product_title: "",
      mdate: "",
      selectMode: "HOME",
      selectCnt: "",
      actionmsgArr: [],
      acttitle: "",
      classObj1: "",
      view_switch: "off",
      i: 2,
      s_charge: "",
      s_product_name: "",
      btnMode: 0,
      calc_now_inventory: "",
      calc_nbox: "",
      btn_select: "",
      isDisabled: "",
      smode: "",
      itsdate: "",
    };
  },
  // マウント時
  mounted() {
      //this.getItem();
      this.dateset();
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション
    checkFormStore: function() {
      this.messagevalidatesNew = [];
      var chkArray = [];
      var flag = true;
      // 担当
      var required = true;
      var equalength = 0;
      var maxlength = 20;
      var itemname = '担当者';
      chkArray = 
        this.checkHeader(this.form.charge, required, equalength, maxlength, itemname);
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
    SelectContentsBtn(sc) {

      this.selectMode = "LINEACTIVE";
      this.getItem(sc);
      this.selectCnt = sc;


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
      }
    },
    NewBtn()  {
      this.inputClear();
      this.form.mdate = this.itsdate;
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
    //console.log("viewBtn amari = " + amari);
    if(amari == 0){
      //console.log("viewBtn amari 0");
      this.view_switch = 'on';
    } else {
      this.view_switch = 'off';
    }
    this.i = this.i + 1;
    //console.log("viewBtn i = " + this.i);

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
    getItem(sc) {
      this.inputClear();
      var arrayParams = { 
        charge : this.charge,
        product_name : this.product_name,
        mdate : this.mdate,
        orderfr : this.orderfr,
        marks : sc,
        is_deleted : 0
      };
      //console.log("getitem in arrayParams['mdate'] = " + arrayParams['mdate']);
      this.postRequest("/material_management/get", arrayParams)
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
      //console.log("getitem one in product_code = " + p);
      //console.log("getitem one in p_name = " + pn);
      this.edit_id = e;
      this.product_code = p;
      this.product_title = pn;
      //console.log("getitem one in product_title = " + this.product_title);
      var arrayParams = {  edit_id : e , product_code : p};
      this.postRequest("/material_management/getone", arrayParams)
        .then(response  => {
          this.getThen(response);
          if(md === 'update') {
            this.details[0].mdate = this.itsdate;
            this.details[0].receipt = "";
            this.details[0].delivery = "";
          }
        })
        .catch(reason => {
          //console.log("getitem reason");
          this.serverCatch("取得");
        });
    },
    // 検索処理
    searchItem() {
        //console.log("searchItem in s_ = " + this.s_order_no);
        //this.s_order_no = this.s_order_no;
        this.classObj1 = "";
        this.acttitle = "検索";
        var motion_msg = "検索";
        var messages = [];
        var arrayParams = { s_charge : this.s_charge , s_product_name : this.s_product_name , marks : 'a'};
        this.postRequest("/material_management/search", arrayParams)
          .then(response  => {
            this.putThenSearch(response, motion_msg);
            //this.btnMode = "1";
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
        var arrayParams = { form : this.form , marks : this.selectCnt};
        this.postRequest("/material_management/insert", arrayParams)
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
        this.acttitle = "抹消へ移動";
        var messages = [];
        var arrayParams = { details : this.details[index] , upkind : k };
        this.postRequest("/material_management/update", arrayParams)
          .then(response  => {
            this.putThenDel(response, "抹消へ移動");
          })
          .catch(reason => {
            this.serverCatch("抹消移動");
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
              this.postRequest("/material_management/delete", arrayParams)
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
        var messages = [];
        if (k == 1) this.details[index].now_inventory = this.details[index].now_inventory + this.details[index].receipt - this.details[index].delivery
        var arrayParams = { details : this.details[index] , upkind : k };
        var motion_msg = "";
        if (k == 0) motion_msg = '修正';
        if (k == 1) motion_msg = '在庫を更新';
        if (k == 2) motion_msg = '新しい商品追加';
        this.postRequest("/material_management/update", arrayParams)
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
        this.count = this.details.length;
        this.before_count = this.count;
        if ( this.details.length > 0) {
          this.form.id = this.details[0].id;
          this.form.mdate = this.details[0].mdate;
          this.form.department = this.details[0].department;
          this.form.charge = this.details[0].charge;
          this.form.product_name = this.details[0].product_name;
          this.form.product_code = this.details[0].product_code;
          this.form.product_number = this.details[0].product_number;
          this.form.unit = this.details[0].unit;
          this.form.quantity = this.details[0].quantity;
          this.form.receipt = this.details[0].receipt;
          this.form.delivery = this.details[0].delivery;
          this.form.now_inventory = this.details[0].now_inventory;
          this.form.nbox = this.details[0].nbox;
          this.form.order_address = this.details[0].order_address;
          this.form.unit_price = this.details[0].unit_price;
          this.form.total = this.details[0].total;
          this.form.remarks = this.details[0].remarks;
          this.form.note = this.details[0].note;
          this.form.status = this.details[0].status;
          this.form.marks = this.details[0].marks;
          this.form.created_user = this.details[0].created_user;
          this.form.updated_user = this.details[0].updated_user;
          this.form.created_at = this.details[0].created_at;
          this.form.updated_at = this.details[0].updated_at;
          this.form.is_deleted = this.details[0].is_deleted;

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
          this.product_title = res.s_charge + res.s_product_name;
          //console.log("putThenSearch in res.s_product_name = " + res.s_product_name);
          this.$toasted.show(this.product_title + " " + eventtext + "しました");
          this.actionmsgArr.push(this.product_title + " を検索しました。");
      } else {
          this.actionmsgArr.push(this.s_charge + this.s_product_name + " が見つかりませんでした。","");
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
          //this.acttitle = "更新";
          this.edit_id = res.id;
          //console.log("putThenHead in res.pid = " + res.product_code);
          this.product_code = res.product_code;
          this.product_title = res.product_name;
        }
        this.$toasted.show(this.product_title + "を" + eventtext + "しました");
        this.getItemOne(this.edit_id,this.product_code,this.product_title,object_mode[k]);
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
        this.acttitle = "登録";
        this.getItemOne(this.re_id,this.product_code,this.product_title);
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
        this.acttitle = "移動";
        this.getItemOne(this.re_id,this.product_code,this.product_title);
        this.$toasted.show(this.product_title + "を" + eventtext + "しました");
        this.actionmsgArr.push(this.product_title + " を抹消へ移動しました。","");
        this.btnMode = 'off';
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
        this.getItemOne(this.re_id,this.product_code,this.product_title);
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
      this.form.mdate = "";
      this.form.department = "";
      this.form.charge = "";
      this.form.product_name = "";
      this.form.product_code = "";
      this.form.product_number = "";
      this.form.unit = "";
      this.form.quantity = "";
      this.form.receipt = "";
      this.form.delivery = "";
      this.form.now_inventory = "";
      this.form.nbox = "";
      this.form.order_address = "";
      this.form.unit_price = "";
      this.form.total = "";
      this.form.remarks = "";
      this.form.note = "";
      this.form.status = "";
      this.form.marks = "";
      this.form.created_user = "";
      this.form.updated_user = "";
      this.form.created_at = "";
      this.form.updated_at = "";
      this.form.is_deleted = "";

    },
    dateset: function()  {
      var date_obj = new Date();
      //console.log('todayset = ' + date_obj);
      this.today_year  = date_obj.getFullYear(); // 西暦年取得
      this.today_month = date_obj.getMonth();    // 月取得
      this.today_day = date_obj.getDate();    // 日取得
      // 文字列として連結month_format
      this.itsdate = ('0000' + this.today_year).slice(-4) 
                      + '-' 
                      + ('00' + (this.today_month + 1)).slice(-2) 
                      + '-' 
                      + ('00' + (this.today_day)).slice(-2) 
    },


  }
};
</script>
