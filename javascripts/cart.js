$(function(){
	$('div.card[data-id]').on('click','button.btn',function(){
		let productId = $(this).parents('.card').data('id');
		let productName = $(this).parents('.card').find('.card-title').text();
		
		updateCartItem(productId, productName, 1);
		updateCartItemView();
	});
	
	$('#CartView').on('click',function(){
		$('#floatingShoppingList').toggleClass('d-none');
		
		if(!$('#floatingShoppingList').is(':hidden')){
			updateCartItemView();
		}
	});
});
function updateCartItemView(){
	$('#floatingShoppingList .card-body').empty();
	//從資料庫查詢資料
	$.ajax({
	  url: 'http://127.0.0.1/',
	  method: 'POST',
	  data: {
		userId: '11123'
	  },
	  success:function(res){
			// 測試用
			let arr = (localStorage.getItem('cart'))?JSON.parse(localStorage.getItem('cart')):{};
			
			for(let o in arr){
				$('#floatingShoppingList .card-body').append(
					`<div><div class="d-inline-block">${arr[o].name}</div> <input onchange="updateCartItem('${o}', '${arr[o].name}', this.value, true);" type="number" value="${arr[o].amount}" /> <button onclick="deleteCartItem('${o}');" class="btn btn-danger">刪除</button></div>`
				);
			}
		},
	  error:function(err){
		console.log(err)
	  },
	});
}

function updateCartItem(productId, productName, amount, isUpdating){
	amount = parseInt(amount);

	// 新增資料到資料庫
	$.ajax({
	  url: 'http://127.0.0.1/',
	  method: 'POST',
	  data: {
		id: productId,
		name: productName,
		amount: amount,
	  },
	  success:function(res){
			// 測試用
			let arr = (localStorage.getItem('cart'))?JSON.parse(localStorage.getItem('cart')):{};
			
			arr[productId] = {
				name: productName,
				amount: (arr[productId]?.amount && !isUpdating)?arr[productId].amount+amount:amount
			};
			
			localStorage.setItem('cart', JSON.stringify(arr));
		},
	  error:function(err){
		console.log(err)
	  },
	});
};

function deleteCartItem(productId){
	// 新增資料到資料庫
	$.ajax({
	  url: 'http://127.0.0.1/',
	  method: 'POST',
	  data: {
		id: productId
	  },
	  success:function(res){
			// 測試用
			let arr = (localStorage.getItem('cart'))?JSON.parse(localStorage.getItem('cart')):{};
			
			delete arr[productId];
			
			localStorage.setItem('cart', JSON.stringify(arr));
			
			updateCartItemView();
		},
	  error:function(err){
		console.log(err)
	  },
	});
};
