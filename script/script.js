

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
    const privacyLinks = document.querySelectorAll('.privacy-link');
    const privacyPopup = document.getElementById('privacyPopup');
    const privacyPopupClose = privacyPopup ? privacyPopup.querySelector('.closeBtn') : null;
    
    if (privacyLinks.length > 0 && privacyPopup) {
        privacyLinks.forEach(privacyLink => {
            privacyLink.addEventListener('click', function(e) {
                e.preventDefault();
                privacyPopup.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
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
    
    // Section11 Swiper 초기화
    const section11Swiper = document.querySelector('.main .section11-swiper');
    if (section11Swiper && typeof Swiper !== 'undefined') {
        new Swiper('.section11-swiper', {
            slidesPerView: 3,
            spaceBetween: 24,
            loop: true,
            centeredSlides: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false
            },
            breakpoints: {
                0: {
                    slidesPerView: 1.1,
                    spaceBetween: 16
                },
                650: {
                    slidesPerView: 1.5,
                    spaceBetween: 16
                },
                900: {
                    slidesPerView: 2,
                    spaceBetween: 20
                },
                1250: {
                    slidesPerView: 3,
                    spaceBetween: 24
                }
            }
        });
    }
    
    // Section14 FAQ 토글 기능
    const faqQuestions = document.querySelectorAll('.section14-faq-question');
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const faqItem = this.closest('.section14-faq-item');
            const isActive = faqItem.classList.contains('active');
            
            // 다른 모든 FAQ 항목 닫기
            document.querySelectorAll('.section14-faq-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // 클릭한 항목이 닫혀있었다면 열기
            if (!isActive) {
                faqItem.classList.add('active');
            }
        });
    });
    
    // 부드러운 스크롤 기능
    const smoothScrollLinks = document.querySelectorAll('a[href^="#"]');
    smoothScrollLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#' || href === '') return;
            
            const targetId = href.substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                e.preventDefault();
                
                // 헤더 높이 고려
                const header = document.getElementById('header');
                const headerHeight = header ? header.offsetHeight : 0;
                
                // 모바일 메뉴가 열려있으면 닫기
                if (mobileMenu && mobileMenu.classList.contains('active')) {
                    mobileMenu.classList.remove('active');
                    if (mobileMenuBtn) {
                        mobileMenuBtn.classList.remove('active');
                    }
                }
                
                // 부드러운 스크롤
                const targetPosition = targetElement.offsetTop - headerHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Section3 아이템 자동 호버 해제 기능
    const section3Items = document.querySelectorAll('.section3-item');
    let activeTimer = null;
    const hoverDuration = 3000; // 3초
    
    section3Items.forEach(item => {
        // 마우스/터치 진입 시
        item.addEventListener('mouseenter', function() {
            clearActiveItems();
            this.classList.add('active');
            resetTimer(this);
        });
        
        item.addEventListener('touchstart', function(e) {
            e.preventDefault();
            clearActiveItems();
            this.classList.add('active');
            resetTimer(this);
        });
        
        // 마우스/터치 나갈 때
        item.addEventListener('mouseleave', function() {
            resetTimer(this);
        });
        
        item.addEventListener('touchend', function() {
            resetTimer(this);
        });
    });
    
    function clearActiveItems() {
        section3Items.forEach(item => {
            item.classList.remove('active');
        });
        if (activeTimer) {
            clearTimeout(activeTimer);
            activeTimer = null;
        }
    }
    
    function resetTimer(item) {
        if (activeTimer) {
            clearTimeout(activeTimer);
        }
        activeTimer = setTimeout(function() {
            item.classList.remove('active');
            activeTimer = null;
        }, hoverDuration);
    }
    
    // Section5 원형 아이템 스크롤 애니메이션
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
        
        const section5Items = document.querySelectorAll('.section5-circle-item');
        
        section5Items.forEach((item, index) => {
            gsap.fromTo(item, 
                {
                    opacity: 0,
                    y: 30
                },
                {
                    opacity: 1,
                    y: 0,
                    duration: 0.6,
                    delay: index * 0.15,
                    ease: 'power2.out',
                    scrollTrigger: {
                        trigger: item,
                        start: 'top 90%',
                        toggleActions: 'play reverse play reverse'
                    }
                }
            );
        });
    } else {
        // GSAP가 없는 경우 Intersection Observer 사용
        const section5Items = document.querySelectorAll('.section5-circle-item');
        
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -150px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const index = Array.from(section5Items).indexOf(entry.target);
                    setTimeout(() => {
                        entry.target.classList.add('animate');
                    }, index * 150);
                } else {
                    entry.target.classList.remove('animate');
                }
            });
        }, observerOptions);
        
        section5Items.forEach(item => {
            observer.observe(item);
        });
    }
    
    // Section6 아이템 스크롤 애니메이션
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        const section6Items = document.querySelectorAll('.section6-right-item');
        
        section6Items.forEach((item, index) => {
            gsap.fromTo(item, 
                {
                    opacity: 0,
                    y: 30
                },
                {
                    opacity: 1,
                    y: 0,
                    duration: 0.6,
                    delay: index * 0.15,
                    ease: 'power2.out',
                    scrollTrigger: {
                        trigger: item,
                        start: 'top 90%',
                        toggleActions: 'play reverse play reverse'
                    }
                }
            );
        });
    } else {
        // GSAP가 없는 경우 Intersection Observer 사용
        const section6Items = document.querySelectorAll('.section6-right-item');
        
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -150px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const index = Array.from(section6Items).indexOf(entry.target);
                    setTimeout(() => {
                        entry.target.classList.add('animate');
                    }, index * 150);
                } else {
                    entry.target.classList.remove('animate');
                }
            });
        }, observerOptions);
        
        section6Items.forEach(item => {
            observer.observe(item);
        });
    }
    
    // Section7 이미지 스크롤 애니메이션
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        const section7Item1 = document.querySelector('.section7-item1');
        const section7Item2 = document.querySelector('.section7-item2');
        
        if (section7Item1) {
            gsap.fromTo(section7Item1, 
                {
                    opacity: 0,
                    y: 30
                },
                {
                    opacity: 1,
                    y: 0,
                    duration: 0.6,
                    ease: 'power2.out',
                    scrollTrigger: {
                        trigger: section7Item1,
                        start: 'top 90%',
                        toggleActions: 'play reverse play reverse'
                    }
                }
            );
        }
        
        if (section7Item2) {
            gsap.fromTo(section7Item2, 
                {
                    opacity: 0,
                    y: 30
                },
                {
                    opacity: 1,
                    y: 0,
                    duration: 0.6,
                    delay: 0.15,
                    ease: 'power2.out',
                    scrollTrigger: {
                        trigger: section7Item2,
                        start: 'top 90%',
                        toggleActions: 'play reverse play reverse'
                    }
                }
            );
        }
    } else {
        // GSAP가 없는 경우 Intersection Observer 사용
        const section7Items = document.querySelectorAll('.section7-item1, .section7-item2');
        
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -150px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const index = Array.from(section7Items).indexOf(entry.target);
                    setTimeout(() => {
                        entry.target.classList.add('animate');
                    }, index * 150);
                } else {
                    entry.target.classList.remove('animate');
                }
            });
        }, observerOptions);
        
        section7Items.forEach(item => {
            observer.observe(item);
        });
    }
    
    // Section8 퍼센트 카운팅 애니메이션
    function animateCounter(element, target, duration = 2000) {
        const start = 0;
        const increment = target / (duration / 16); // 60fps 기준
        let current = start;
        const percentElement = element.querySelector('.section8-item-percent');
        const percentHTML = percentElement ? percentElement.outerHTML : '<span class="section8-item-percent">%</span>';
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.innerHTML = Math.floor(current) + percentHTML;
        }, 16);
    }
    
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        const section8Numbers = document.querySelectorAll('.section8-item-number');
        
        section8Numbers.forEach((numberEl) => {
            const target = parseInt(numberEl.getAttribute('data-target'));
            const section8Item = numberEl.closest('.section8-item');
            
            ScrollTrigger.create({
                trigger: section8Item,
                start: 'top 90%',
                once: true,
                onEnter: () => {
                    animateCounter(numberEl, target);
                }
            });
        });
    } else {
        // GSAP가 없는 경우 Intersection Observer 사용
        const section8Numbers = document.querySelectorAll('.section8-item-number');
        const section8Items = document.querySelectorAll('.section8-item');
        
        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px 0px -100px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const numberEl = entry.target.querySelector('.section8-item-number');
                    if (numberEl && !numberEl.classList.contains('counted')) {
                        const target = parseInt(numberEl.getAttribute('data-target'));
                        numberEl.classList.add('counted');
                        animateCounter(numberEl, target);
                    }
                }
            });
        }, observerOptions);
        
        section8Items.forEach(item => {
            observer.observe(item);
        });
    }
    
    // Section12 이미지 스크롤 애니메이션
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        const section12Images = document.querySelectorAll('.section12 .content-image');
        
        section12Images.forEach((image, index) => {
            gsap.fromTo(image, 
                {
                    opacity: 0,
                    y: 30
                },
                {
                    opacity: 1,
                    y: 0,
                    duration: 0.6,
                    delay: index * 0.1,
                    ease: 'power2.out',
                    scrollTrigger: {
                        trigger: image,
                        start: 'top 90%',
                        toggleActions: 'play reverse play reverse'
                    }
                }
            );
        });
    } else {
        // GSAP가 없는 경우 Intersection Observer 사용
        const section12Images = document.querySelectorAll('.section12 .content-image');
        
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -150px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const index = Array.from(section12Images).indexOf(entry.target);
                    setTimeout(() => {
                        entry.target.classList.add('animate');
                    }, index * 100);
                } else {
                    entry.target.classList.remove('animate');
                }
            });
        }, observerOptions);
        
        section12Images.forEach(image => {
            observer.observe(image);
        });
    }
    
    // 전체 섹션 스크롤 애니메이션
    function initScrollAnimation(selector, options = {}) {
        const {
            delay = 0,
            duration = 0.6,
            y = 30,
            start = 'top 90%',
            stagger = 0.1
        } = options;
        
        const elements = document.querySelectorAll(selector);
        if (elements.length === 0) return;
        
        if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
            elements.forEach((element, index) => {
                gsap.fromTo(element, 
                    {
                        opacity: 0,
                        y: y
                    },
                    {
                        opacity: 1,
                        y: 0,
                        duration: duration,
                        delay: delay + (index * stagger),
                        ease: 'power2.out',
                        scrollTrigger: {
                            trigger: element,
                            start: start,
                            toggleActions: 'play reverse play reverse'
                        }
                    }
                );
            });
        } else {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -150px 0px'
            };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        const index = Array.from(elements).indexOf(entry.target);
                        setTimeout(() => {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }, delay + (index * stagger * 1000));
                        observer.unobserve(entry.target);
                    } else {
                        entry.target.style.opacity = '0';
                        entry.target.style.transform = `translateY(${y}px)`;
                    }
                });
            }, observerOptions);
            
            elements.forEach(element => {
                element.style.opacity = '0';
                element.style.transform = `translateY(${y}px)`;
                element.style.transition = `opacity ${duration}s ease, transform ${duration}s ease`;
                observer.observe(element);
            });
        }
    }
    
    // 각 섹션별 애니메이션 적용 (이미 애니메이션이 있는 요소 제외)
    initScrollAnimation('.section1-title, .section1-subtitle');
    initScrollAnimation('.section3-subtitle, .section3-title, .section3-description');
    initScrollAnimation('.section3-item', { stagger: 0.1 });
    initScrollAnimation('.section4-title');
    initScrollAnimation('.section8-title, .section8-text');
    // section8-item-number는 카운팅 애니메이션이 있어서 제외
    initScrollAnimation('.section9-title, .section9-text');
    initScrollAnimation('.section9-tab', { stagger: 0.05 });
    initScrollAnimation('.section9-item', { stagger: 0.1 });
    initScrollAnimation('.section10-item', { stagger: 0.1 });
    initScrollAnimation('.section11-title, .section11-text');
    initScrollAnimation('.section12-title, .section12-text');
    // section12 .content-image는 이미 애니메이션 있음
    initScrollAnimation('.section13-item', { stagger: 0.1 });
    initScrollAnimation('.section14-title');
    initScrollAnimation('.section15-title');
    // section15-item은 이미 애니메이션 있음
    // section16 (문의폼) - 왼쪽과 오른쪽을 순차적으로
    initScrollAnimation('.section16-left', { delay: 0 });
    initScrollAnimation('.section16-right', { delay: 0.2 });
})


