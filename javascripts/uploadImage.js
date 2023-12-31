function uploadImage(action, id) {
  let inputFile = document.getElementById(id);
  let file = inputFile.files[0];
  let formData = new FormData();
  formData.append("action", action);
  formData.append("id", id);
  formData.append("uploadfile", file);

  $.ajax({
    url: "/api/api.uploadfile.php",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (res) {
      console.log(res);
      updateImage(res[0], action, id);
      location.reload()
    },
    error: function (err) {
      console.log(err);
    },
  });
}

function updateImage(path, action, id) {
  // 新增資料到資料庫
  $.ajax({
    url: "/api/api.updateImage.php",
    method: "POST",
    data: {
      path: path,
      action: action,
      id: id
    },
    success: function (res) {
        console.log(res);
    },
    error: function (err) {
      console.log(err);
    },
  });
}
