

document.addEventListener("DOMContentLoaded", function(e){
    // 모바일 메뉴 토글
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
            this.classList.toggle('active');
        });
        
        // 모바일 메뉴 링크 클릭 시 메뉴 닫기
        const mobileMenuLinks = mobileMenu.querySelectorAll('a');
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
                mobileMenuBtn.classList.remove('active');
            });
        });
        
        // 외부 클릭 시 메뉴 닫기
        document.addEventListener('click', function(e) {
            if (mobileMenuBtn && mobileMenu && 
                !mobileMenuBtn.contains(e.target) && 
                !mobileMenu.contains(e.target)) {
                mobileMenu.classList.remove('active');
                mobileMenuBtn.classList.remove('active');
            }
        });
    }
    
    // 개인정보처리방침 팝업
    const privacyLink = document.querySelector('.privacy-link');
    const privacyPopup = document.getElementById('privacyPopup');
    const privacyPopupClose = privacyPopup ? privacyPopup.querySelector('.closeBtn') : null;
    
    if (privacyLink && privacyPopup) {
        privacyLink.addEventListener('click', function(e) {
            e.preventDefault();
            privacyPopup.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    
    function closePrivacyPopup() {
        if (privacyPopup) {
            privacyPopup.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
    
    if (privacyPopupClose) {
        privacyPopupClose.addEventListener('click', function(e) {
            e.stopPropagation();
            closePrivacyPopup();
        });
    }
    
    if (privacyPopup) {
        privacyPopup.addEventListener('click', function(e) {
            if (e.target === privacyPopup) {
                closePrivacyPopup();
            }
        });
        
        const contentWrap = privacyPopup.querySelector('.contentWrap');
        if (contentWrap) {
            contentWrap.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    }
    
    // ESC 키로 팝업 닫기
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && privacyPopup && privacyPopup.classList.contains('active')) {
            closePrivacyPopup();
        }
    });
    
    // 섹션1 Swiper 초기화
    const section1Swiper = document.querySelector('.main .section1 .section1-swiper');
    if (section1Swiper && typeof Swiper !== 'undefined') {
        // 실제 슬라이드 개수 계산 (loop 모드에서 복제된 슬라이드 제외)
        const slideElements = document.querySelectorAll('.main .section1 .swiper-slide');
        const totalSlidesCount = slideElements ? slideElements.length : 0;
        
        if (totalSlidesCount > 0) {
            const swiperInstance = new Swiper('.section1-swiper', {
                slidesPerView: 1,
                spaceBetween: 56,
                loop: true,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false
                },
                navigation: {
                    nextEl: '.section1-next',
                    prevEl: '.section1-prev',
                },
                pagination: {
                    el: '.section1-pagination',
                    type: 'progressbar',
                },
                breakpoints: {
                    650: {
                        spaceBetween: 30
                    }
                },
                on: {
                    slideChange: function() {
                        updateSlideCounter(this, totalSlidesCount);
                        updateSlideTitle(this);
                    },
                    init: function() {
                        updateSlideCounter(this, totalSlidesCount);
                        updateSlideTitle(this);
                    }
                }
            });

            function updateSlideCounter(swiper, totalSlides) {
                if (!swiper || !totalSlides) return;
                
                const currentSlide = swiper.realIndex + 1;
                const currentSlideElements = document.querySelectorAll('.main .section1 .current-slide');
                const totalSlidesElements = document.querySelectorAll('.main .section1 .total-slides');
                
                if (currentSlideElements && currentSlideElements.length > 0) {
                    currentSlideElements.forEach(el => {
                        if (el) {
                            el.textContent = String(currentSlide).padStart(2, '0');
                        }
                    });
                }
                
                if (totalSlidesElements && totalSlidesElements.length > 0) {
                    totalSlidesElements.forEach(el => {
                        if (el) {
                            el.textContent = String(totalSlides).padStart(2, '0');
                        }
                    });
                }
            }

            function updateSlideTitle(swiper) {
                if (!swiper || !swiper.slides || swiper.activeIndex === undefined) return;
                
                const activeSlide = swiper.slides[swiper.activeIndex];
                if (!activeSlide) return;
                
                const slideItem = activeSlide.querySelector('.slide-item');
                const slideTitle = document.querySelector('.main .section1 .slide-title');
                
                if (slideItem && slideItem.dataset && slideItem.dataset.slidename && slideTitle) {
                    slideTitle.textContent = slideItem.dataset.slidename;
                }
            }
        }
    }
})


