/****************************************************************************************
 * 
 * 購物車
 * 
 ****************************************************************************************/
// 更新購物車
$(function(){
	// $('div.card[data-id]').on('click','button.btn',function(){
	// 	let productId = $(this).parents('.card').data('id');
	// 	let productName = $(this).parents('.card').find('.card-title').text();
	// 	updateCartItem(productId, productName, 1);
	// 	updateCartItemView();
	// });
	
	$('#CartView').on('click',function(){
		$('#floatingShoppingList').toggleClass('d-none');
		
		if(!$('#floatingShoppingList').is(':hidden')){
			updateCartItemView();
		}
	});
});
function updateCartItemView(){
	$('#floatingShoppingList .list').empty();
	$('#floatingShoppingList #productsCount').empty();
	//從資料庫查詢資料
	$.ajax({
	  url: '/api/api.shopping_cart.php',
	  method: 'POST',
	  data: {
		action: 'VIEW'
	  },
	  success:function(res){
			// 測試用
			// let arr = (localStorage.getItem('cart'))?JSON.parse(localStorage.getItem('cart')):{};
			console.log(res);
			amount = 0;
			for(let o in res){
				$('#floatingShoppingList .list').append(
					`<!-- ${res[o].product_id} -->
					<div class="productItem" style="background-color: rgba(255,255,255,.9);color: #000">
					<div class="productImage" >
						<a href=""><img src="/assets/images/4156_G_1598386817236.jpg" style="max-width: 100px;"></a> <!-- 第一欄 -->
					</div>
					<div style="width: 65%;text-align: center;">
						<!-- #商品名稱# / #尺寸# / #顏色# -->
						${res[o].name} <!--/ ${res[o].specification} /${res[o].color}-->
					</div>
					<div style="width: 20%;text-align: center;"> 
						${res[o].price}
					</div>
					<div style="width: 10%;text-align: center;">
						${res[o].quantity}
						<!-- <form style="text-align: center;">
						<input onchange="updateCartItem('${o}', '${res[o].name}', this.value, true);" type="number" value="${res[o].amount}" />
						</form> -->
					</div>
					<button class="delete material-icons" style="width: 50px;" onclick="deleteCartItem('${o}');">&#xe872</button>
					</div>`
				);
				amount += res[o].price * res[o].quantity;
			}
			if(amount > 0) $('#floatingShoppingList #productsCount').append(`總價: `+amount);
			else $('#floatingShoppingList #productsCount').append(`目前沒有任何商品`);
			
		},
	  error:function(err){
		console.log(err)
	  },
	});
}

function updateCartItem(productId, productQuantity, productSpecification, productColor){
	productQuantity = parseInt(productQuantity);
	// productId = parseInt(productId);
	// 新增資料到資料庫
	$.ajax({
	  url: '/api/api.shopping_cart.php',
	  method: 'POST',
	  data: {
		action: 'UPDATE',
		product_id: productId,
		product_quantity: productQuantity,
		product_specification: productSpecification,
		product_color: productColor,
	  },
	  success:function(res){
			// console.log(res);
			// 測試用
			// let cartId = Object.keys(res)[0];
			// let arr = (localStorage.getItem('cart'))?JSON.parse(localStorage.getItem('cart')):{};
			// arr[cartId] = {
			// 	name: res[cartId]['name'],
			// 	spec: res[cartId]['specification'],
			// 	color: res[cartId]['color'],
			// 	// amount: (arr[productId]?.amount && !isUpdating)?arr[productId].amount+amount:amount
			// 	amount:  res[cartId]['quantity']
			// };
			updateCartItemView();
			// localStorage.setItem('cart', JSON.stringify(arr));
		},
	  error:function(err){
		console.log(err)
	  },
	});
};

function deleteCartItem(cartId){
	// 新增資料到資料庫
	$.ajax({
	  url: '/api/api.shopping_cart.php',
	  method: 'POST',
	  data: {
		action: 'DEL',
		cart_id: cartId
	  },
	  success:function(res){
			// 測試用
			// let arr = (localStorage.getItem('cart'))?JSON.parse(localStorage.getItem('cart')):{};
			// delete arr[cartId];
			// localStorage.setItem('cart', JSON.stringify(arr));
			updateCartItemView();
		},
	  error:function(err){
		console.log(err)
	  },
	});
};