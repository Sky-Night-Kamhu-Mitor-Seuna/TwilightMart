document.addEventListener('DOMContentLoaded', function () {
    var element = document.getElementById('siteName');
    if (element) {
        if (element.textContent.length > 10) {
            element.style.fontSize = '24px';
        }else if (element.textContent.length > 12) {
            element.style.fontSize = '12px';
        }
    }
});