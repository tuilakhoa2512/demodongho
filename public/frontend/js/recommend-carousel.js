document.addEventListener('DOMContentLoaded', function () {

    const track = document.querySelector('.recommend-track');
    const items = document.querySelectorAll('.recommend-item');
    const prevBtn = document.querySelector('.recommend-btn.prev');
    const nextBtn = document.querySelector('.recommend-btn.next');

    if (!track || items.length === 0) return;

    const itemsPerView = 3;
    const totalItems = items.length;
    const maxIndex = Math.ceil(totalItems / itemsPerView) - 1;

    let currentIndex = 0;

    function updateSlide() {
        const translateX = -(currentIndex * 100);
        track.style.transform = `translateX(${translateX}%)`;
    }

    nextBtn.addEventListener('click', () => {
        if (currentIndex < maxIndex) {
            currentIndex++;
            updateSlide();
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateSlide();
        }
    });

});
