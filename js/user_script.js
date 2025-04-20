let profile = document.querySelector('.header .flex .profile');
let searchForm = document.querySelector('.header .flex .search-form');
let navbar = document.querySelector('.header .flex .navbar');

document.querySelector('#user-btn').onclick = () => {
  profile.classList.toggle('active');
  searchForm.classList.remove('active');
  navbar.classList.remove('active');
};

// Toggle Search Form
document.querySelector('#search-btn').onclick = () => {
  searchForm.classList.toggle('active');
  profile.classList.remove('active');
  navbar.classList.remove('active');
};

// Toggle Navbar Menu
document.querySelector('#menu-btn').onclick = () => {
  navbar.classList.toggle('active');
  profile.classList.remove('active');
  searchForm.classList.remove('active');
};


/***********************tab-teachers********************/
const tabsContainer = document.querySelector('.teacher-tabs');
const teacherSection = document.querySelector('.teacher-section');

tabsContainer.addEventListener('click', (e) => {
  const clicked = e.target;
  if (!clicked.classList.contains('tab-item')) return;
  // deactivate old tab and content
  tabsContainer.querySelector('.tab-item.active')?.classList.remove('active');
  teacherSection.querySelector('.tab-content.active')?.classList.remove('active');
  // activate clicked tab and its panel
  clicked.classList.add('active');
  const panel = teacherSection.querySelector(clicked.getAttribute('data-target'));
  panel?.classList.add('active');
});

