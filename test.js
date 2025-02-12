$(document).ready(function(){
    //jquery for toggle sub menus
  $('.sub-btn').click(function(){
    $(this).next('.sub-menu').slideToggle();
    $(this).find('.dropdown').toggleClass('rotate');
  });
});

// Profile Dropdown
const profileDropdownList = document.querySelector(".profile-dropdown-list");
const profileBtn = document.querySelector(".profile-dropdown-btn");

const toggleProfile = () => profileDropdownList.classList.toggle("active");

profileBtn.addEventListener("click", toggleProfile);

window.addEventListener("click", function (e) {
  if (!profileBtn.contains(e.target)) {
    profileDropdownList.classList.remove("active");
  }
});

// Notification Dropdown
const notificationDropdownList = document.querySelector(".notification_dropdown-list");
const notificationBtn = document.querySelector(".notification_dropdown-btn");

const toggleNotification = () => notificationDropdownList.classList.toggle("active");

notificationBtn.addEventListener("click", toggleNotification);

window.addEventListener("click", function (e) {
  if (!notificationBtn.contains(e.target)) {
    notificationDropdownList.classList.remove("active");
  }
});


  //jquery for search menus
const searchInput = document.getElementById('search-input');
const suggestionsBox = document.getElementById('suggestions');

searchInput.addEventListener('input', () => {
  const query = searchInput.value;

  if (query.length > 0) {
    fetch(`suggest.php?query=${encodeURIComponent(query)}`)
    .then(response => response.json())
    .then(data => {
      suggestionsBox.innerHTML = '';
      data.forEach(item => {
        const suggestionItem = document.createElement('li');
        suggestionItem.textContent = item;
        suggestionItem.className = 'list-group-item';
        suggestionItem.style.cursor = 'pointer';

        suggestionItem.addEventListener('click', () => {
          searchInput.value = item;
          suggestionsBox.innerHTML = '';
        });

        suggestionsBox.appendChild(suggestionItem);
      });
    });
  } else {
    suggestionsBox.innerHTML = '';
  }
});

document.addEventListener('click', (event) => {
  if (!searchInput.contains(event.target)) {
    suggestionsBox.innerHTML = '';
  }
});




