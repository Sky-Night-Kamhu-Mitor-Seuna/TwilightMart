function addToCart(id, name, price) {
    var quantity = 1;
    var cart = getCookie("cart");
    var i=0;
    if(cart != null) {
        cart = JSON.parse(cart);
        for(i=0; i<cart.length; i++) {
            if(cart[i].id == id) {
                cart[i].quantity++;
                quantity = cart[i].quantity;
                break;
            }
        }
    } 
    else cart = [];
    if(i == cart.length) {
        cart.push({
            "id": id,
            "name": name,
            "quantity": quantity,
            "price": price
        });
    }
    setCookie("cart", JSON.stringify(cart), 7);
    updateCartUI();
}

function updateCartUI() {
    var cart = getCookie("cart");
    if(cart != null) {
        cart = JSON.parse(cart);
        var total = 0;
        var itemHTML = "";
        for(var i=0; i<cart.length; i++) {
            var totalPrice = cart[i].price * cart[i].quantity;
            total += totalPrice;
            itemHTML += "<tr>";
            itemHTML += "<th scope='row'>" + (i+1) + "</th>";
            itemHTML += "<td>" + cart[i].name + "</td>";
            itemHTML += "<td><input type='number' min=1 class='form-control' value='" + cart[i].quantity + "' onchange='updateQuantity(" + i + ", " + cart[i].price + ", this.value)'/></td>";
            itemHTML += "<td>" + cart[i].price + "</td>";
            itemHTML += "<td>" + totalPrice.toFixed(2) + "</td>";
            itemHTML += `<td><a onclick='removeCartItem("${cart[i].id}")'>X</a>`+ "</td>";
            itemHTML += "</tr>";
        }
        itemHTML += "<tr><td colspan='4'>總共</td><td>" + total.toFixed(2) + "</td></tr>";
        $("#items").html(itemHTML);
        
        if(cart.length > 0) {
            $("#cart").show();
            // $("body").addClass("modal-open");
        } else {
            $("#cart").hide();
            // $("body").removeClass("modal-open");
        }
    }else{closeCart();}
}

function updateQuantity(index, price, quantity) {
    var cart = getCookie("cart");
    if(cart != null) {
        cart = JSON.parse(cart);
        cart[index].quantity = quantity<1 ? 1:quantity;
        setCookie("cart", JSON.stringify(cart), 7);
        updateCartUI();
    }
}

function removeCartItem(id) {
    var cart = getCookie("cart");
    if(cart != null) {
        cart = JSON.parse(cart);
        for(var i=0; i<cart.length; i++) {
            if(cart[i].id == id) {
                cart.splice(i, 1);
                break;
            }
        }
        setCookie("cart", JSON.stringify(cart), 7);
        updateCartUI();
    }
}


function closeCart() {
    $("#cartMask").hide();
    // $("body").removeClass("modal-open");
}

function buy() {
    var cart = getCookie("cart");
    if(cart != null) {
        alert("開發中");
        deleteCookie("cart");
        updateCartUI();
        closeCart();
    }else{
        alert("目前沒有商品");
        closeCart();
    }
}