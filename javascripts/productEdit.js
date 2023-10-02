function changeProduct(sendvalue) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        // console.log("readyState: " + this.readyState + ", status: " + this.status + ", responseText: " + this.responseText);
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
        }
    };
    xmlhttp.open("POST", "./?page=member&action=productEdit", true);
    xmlhttp.setRequestHeader(
        "Content-type",
        "application/x-www-form-urlencoded"
    );
    xmlhttp.send(sendvalue);
    return true;
}
function deleteProduct(pid) {
    var sendvalue="pid=" + pid + "&status=0";
    if (confirm("確定要刪除此商品嗎？")) {
        changeProduct(sendvalue);
        alert("已成功刪除");
    } else {
        return false;
    }
}
function setStatusProduct(pid,switchBtn) {
    if (switchBtn.checked) {
        var sendvalue="pid=" + pid + "&status=1";
    } else {
        var sendvalue="pid=" + pid + "&status=2";
    }
    changeProduct(sendvalue);
    alert("已成功變更商品狀態");
}
function editProduct(pid) {
    // var productName = document.getElementById("pName").value;
    // var productPrice = document.getElementById("pPrice").value;
    // var productDescription = document.getElementById("pDesc").value;
    // var sendvalue="pid=" + pid + "&pName=" + productName + "&pPrice=" + productPrice + "&pDesc=" + productDescription + "&edit=1";
    if (confirm("確定要編輯此商品嗎？")) {
        // changeProduct(sendvalue);
        alert("已成功編輯");
    } else {
        return false;
    }
}
