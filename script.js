// === Statistic Animation ===
const counters = document.querySelectorAll('.counter');
const speed = 200; // Semakin kecil, semakin cepat
counters.forEach(counter => {
  const updateCount = () => {
    const target = +counter.getAttribute('data-target');
    const count = +counter.innerText;
    const increment = target / speed;
    if (count < target) {
      counter.innerText = Math.ceil(count + increment);
      setTimeout(updateCount, 10);
    } else {
      counter.innerText = target;
    }
  };
  updateCount();
});

const stars = document.querySelectorAll('.star');
const ratingInput = document.getElementById('ratingInput');
let selectedRating = 0;

stars.forEach((star, index) => {
  star.addEventListener('mouseover', () => highlightStars(index + 1));
  star.addEventListener('mouseout', () => highlightStars(selectedRating));
  star.addEventListener('click', () => {
    selectedRating = index + 1;
    ratingInput.value = selectedRating;
    highlightStars(selectedRating);
  });
});

function highlightStars(rating) {
  stars.forEach((star, index) => {
    star.classList.toggle('hover', index < rating);
    star.classList.toggle('selected', index < rating);
  });
}

