// core version + navigation, pagination modules:
import Swiper, { Navigation } from 'swiper';
// import Swiper and modules styles
import 'swiper/css';
import 'swiper/css/navigation';

// init Swiper:
const companyCarousel = new Swiper('.company-carousel', {
    modules: [Navigation],
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    slidesPerView: 1,
    spaceBetween: 10,
    breakpoints: {
        600: {
            slidesPerView: 2,
        },
        992: {
            slidesPerView: 3,
        },
        1024: {
            slidesPerView: 4,
        }
    }
});
