let currentPopup = null;
function showpopup(popupId) {
    if (currentPopup) {
        currentPopup.classList.remove('show');
        document.getElementById('background-blur').classList.remove('show');
        setTimeout(() => {
            document.getElementById(popupId).classList.add('show');
            document.getElementById('background-blur').classList.add('show');
            currentPopup = document.getElementById(popupId);
        }, 300); // transition duration
    } else {
        document.getElementById(popupId).classList.add('show');
        document.getElementById('background-blur').classList.add('show');
        currentPopup = document.getElementById(popupId);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Change the height of the second form
    document.getElementById('popup-2').style.height = '470px';
});
