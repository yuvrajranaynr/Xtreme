let body = document.body;
let profile = document.querySelector('header.flex .profile');
let searchForm = document.querySelector('.header.flex .search-form');
let sidebar = document.querySelector('.side-bar');

// Ensure the sidebar is hidden by default
sidebar.style.display = 'none';

document.querySelector('#user-btn').onclick = () => {
   profile.classList.toggle('active');
   searchForm.classList.remove('active');
};

document.querySelector('#search-btn').onclick = () => {
   profile.classList.remove('active');
   searchForm.classList.toggle('active');
};

// Add an event listener to the menu button to toggle the sidebar visibility
const menuBtn = document.querySelector('#menu-btn');
menuBtn.addEventListener('click', () => {
    if (sidebar.style.display === 'none') {
        sidebar.style.display = 'block';
    } else {
        sidebar.style.display = 'none';
    }
});

window.onscroll = () => {
   profile.classList.remove('active');
   searchForm.classList.remove('active');

   if (window.innerWidth < 1200) {
      sidebar.classList.remove('active');
      body.classList.remove('active');
   }
};

/************************counter************************/
// (() => {
//     const counters = document.querySelectorAll('.counter');

//     counters.forEach((item) => {
//         const target = parseInt(item.textContent.trim(), 10); // Convert & clean
//         item.textContent = '0';

//         let count = 0;
//         const speed = parseInt(item.dataset.speed, 10) || 2000;
//         const increment = Math.ceil(target / 100); // Fine-tuned increment
//         const intervalTime = speed / (target / increment);

//         const stop = setInterval(() => {
//             count += increment;
//             if (count >= target) {
//                 item.textContent = target;
//                 clearInterval(stop);
//             } else {
//                 item.textContent = count;
//             }
//         }, intervalTime);
//     });
// })();




