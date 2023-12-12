function setProductStatus(product_id, status) {
  $.ajax({
    url: "/api/api.updateProduct.php",
    method: "POST",
    data: {
      action: "change_status",
      product_id: product_id,
      product_status: status ^ 1,
    },
    success: function (res) {
      console.log(res);
    },
    error: function (err) {
      console.log(err);
    },
  });
}
function addProduct(product_id, status) {
  $.ajax({
    url: "/api/api.updateProduct.php",
    method: "POST",
    data: {
      product_name: "add_product",
      product_price: product_id,
      product_quantity: status ^ 1,
    },
    success: function (res) {
      console.log(res);
      location.reload()
    },
    error: function (err) {
      console.log(err);
    },
  });
}
