let body = document.body;
let profile = document.querySelector('header.flex .profile');
let searchForm = document.querySelector('.header.flex .search-form');
let sidebar = document.querySelector('.side-bar');

document.querySelector('#user-btn').onclick = () => {
   profile.classList.toggle('active');
   searchForm.classList.remove('active');
};

document.querySelector('#search-btn').onclick = () => {
   profile.classList.remove('active');
   searchForm.classList.toggle('active');
};

document.querySelector('#menu-btn').onclick = () => {
   sidebar.classList.toggle('active');
   body.classList.toggle('active');
};

window.onscroll = () => {
   profile.classList.remove('active');
   searchForm.classList.remove('active');

   if (window.innerWidth < 1200) {
      sidebar.classList.remove('active');
      body.classList.remove('active');
   }
};

/************************counter************************/
(() => {

    const counter = document.querySelectorAll('.counter');

    // Convert to array
    const array = Array.from(counter);

    // Select array element
    array.map((item) => {

        // Data layer
        let counterInnerText = item.textContent;
        item.textContent = 0;

        let count = 1;
        let speed = item.dataset.speed / counterInnerText;

        function counterUp() {
            item.textContent = count+100;
            if (counterInnerText < count) {
                clearInterval(stop);
            }
        }

        const stop = setInterval(() => {
            counterUp();
        }, speed);

    });

})();

