function announcementSwitch(evt, tabName) {
    $('.announcementContent').hide();
    $('.announcementTab a').removeClass('announcementActive');
    $('#' + tabName).show();
    evt.currentTarget.className += ' announcementActive';
}