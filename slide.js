let slideIndex = 1;

function changeSlide(n) {
  showSlides(slideIndex += n);
}

function showSlides(n) {
  const slides = document.getElementsByClassName("slideshow")[0].getElementsByTagName("img");
  if (n > slides.length) {
    slideIndex = slides.length;
  }
  if (n < 1) {
    slideIndex = 1;
  }
  for (let i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  slides[slideIndex - 1].style.display = "block";

  const bestUserRank = document.querySelector('.bestUser-rank');
  
  switch(slideIndex) {
    case 1:
      bestUserRank.classList.remove('medaille2', 'medaille3');
      bestUserRank.classList.add('medaille1');
      break;
    case 2:
      bestUserRank.classList.remove('medaille1', 'medaille3');
      bestUserRank.classList.add('medaille2');
      break;
    case 3:
      bestUserRank.classList.remove('medaille1', 'medaille2');
      bestUserRank.classList.add('medaille3');
      break;
    default:
      bestUserRank.classList.remove('medaille1', 'medaille2', 'medaille3');
  }
}


