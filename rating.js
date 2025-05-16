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
