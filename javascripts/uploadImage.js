function show(obj) {
    document.getElementById(obj).style.display = "block";
}
function hide(obj) {
    document.getElementById(obj).style.display = "none";
}
function trigger(obj) {
    document.getElementById(obj).click();
}
function uploadImage(obj) {
    let formData = new FormData();
    formData.append("IMAGE_UPLOAD", document.getElementById(obj+"_IMAGE_UPLOAD").files[0]);
    formData.append("IMAGE_TYPE", document.getElementById(obj+"_IMAGE_TYPE").value);
    formData.append("IMAGE_RENAME", document.getElementById(obj+"_IMAGE_RENAME").value);
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "/api/upload/");
    xhr.send(formData);
    xhr.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            if (this.responseText!=null) {
                console.log(this.responseText);
                location.reload();
            }
        }
    };
}