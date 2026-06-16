<!doctype html>
<html lang="zxx">

<head>
  <!-- Meta -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, maximum-scale=1" />
  <meta name="description" content="" />
  <meta name="keywords" content="" />
  <meta name="author" content="" />
  <!-- Page Title -->
  <title>Life Foundation || Home</title>

  <!-- Header-links Start -->
  <?php include 'inc/header-links.php'; ?>
  <!-- Header-links Start -->

  <!-- Mouse Cursor Css File -->
  <link rel="stylesheet" href="css/mousecursor.css">


</head>

<body>
  <!-- Header Start -->
  <?php include 'inc/header.php'; ?>
  <!-- Header End -->

  <style>
     /* ----- variables (matching original) ----- */
        :root {
            --white-color: #ffffff;
            --accent-color: #f5b342;
            --dark-divider-color: rgba(255, 255, 255, 0.15);
            --divider-color: rgba(255, 255, 255, 0.06);
        }

        /* ----- hero slider container ----- */
        .hero-slider-wrapper {
            position: relative;
            width: 100%;
            height: 100vh;
            min-height: 600px;
            overflow: hidden;
            background: #0b1e1a;
        }

        /* slides container */
        .hero-slides {
            display: flex;
            width: 100%;
            height: 100%;
            transition: transform 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            will-change: transform;
        }

        /* each slide = full area with background image + overlay */
        .hero-slide {
            flex: 0 0 100%;
            height: 100%;
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            position: relative;
            display: flex;
            align-items: center;
        }

        /* dark overlay (like original gradient) */
        .hero-slide::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(270deg, transparent 27.97%, rgba(3, 53, 44, 0.90) 68.07%);
            z-index: 1;
            pointer-events: none;
        }

        /* container inside slide (z-index above overlay) */
        .hero-slide .container {
            position: relative;
            z-index: 2;
            width: 100%;
            padding: 0 30px;
            max-width: 1280px;
            margin: 0 auto;
        }

        /* ----- original hero content (reused) ----- */
        .hero-sub-heading {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background-color: var(--dark-divider-color);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-radius: 100px;
            padding: 5px 20px 5px 5px;
            margin-bottom: 15px;
        }
        .satisfy-client-images {
            display: flex;
            align-items: center;
        }
        .satisfy-client-image {
            position: relative;
            display: inline-block;
            margin-left: -15px;
            border: 1px solid var(--white-color);
            border-radius: 50%;
            overflow: hidden;
            z-index: 1;
        }
        .satisfy-client-image:first-child {
            margin: 0;
        }
        .satisfy-client-image figure img {
            width: 100%;
            max-width: 40px;
            border-radius: 50%;
        }
        .hero-sub-heading .satisfy-client-image {
            margin-left: -10px;
        }
        .hero-sub-heading .satisfy-client-image:first-child {
            margin-left: 0;
        }
        .hero-sub-heading .satisfy-client-image figure img {
            max-width: 28px;
        }
        .satisfy-client-content p {
            margin: 0;
            font-size: 14px;
            color: var(--white-color);
        }

        .section-title h1 {
            font-size: clamp(2rem, 6vw, 4.2rem);
            font-weight: 700;
            line-height: 1.2;
            color: var(--white-color);
            max-width: 900px;
            margin: 40px 0 20px 0;
            letter-spacing: -0.02em;
        }

        .hero-body {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 30px;
            margin-top: 80px;
        }
        .hero-body-content {
            max-width: 560px;
        }
        .hero-content p {
            font-size: 18px;
            color: var(--white-color);
            margin: 0;
            line-height: 1.6;
            opacity: 0.9;
        }
        .hero-body-btn {
            margin-top: 30px;
        }
        .btn-default {
            display: inline-block;
            background: var(--accent-color);
            color: #0b1e1a;
            font-weight: 600;
            padding: 14px 42px;
            border-radius: 60px;
            font-size: 16px;
            letter-spacing: 0.3px;
            transition: 0.2s;
            border: 1px solid transparent;
        }
        .btn-default:hover {
            background: #e09e3a;
            transform: scale(1.02);
            box-shadow: 0 8px 20px rgba(245, 179, 66, 0.3);
        }

        .hero-counter-box {
            max-width: 335px;
            border: 1px solid var(--dark-divider-color);
            background: var(--divider-color);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 40px;
            color: white;
        }
        .hero-counter-box h2 {
            font-size: 40px;
            font-weight: 600;
            line-height: 1em;
            color: var(--white-color);
        }
        .hero-counter-box h2 sup {
            color: var(--accent-color);
        }
        .hero-counter-box h3 {
            font-size: 20px;
            color: var(--white-color);
            margin: 15px 0 0;
        }
        .hero-counter-box p {
            color: var(--white-color);
            border-top: 1px solid var(--dark-divider-color);
            padding-top: 20px;
            margin: 40px 0 0;
            font-size: 15px;
            opacity: 0.8;
        }

        /* ----- slider navigation: arrows on sides, dots at bottom ----- */
        .slider-controls {
            position: absolute;
            bottom: 40px;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            z-index: 10;
            pointer-events: none;
        }
        .slider-controls > * {
            pointer-events: auto;
        }
        .slider-dots {
            display: flex;
            gap: 12px;
        }
        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            border: 1px solid rgba(255,255,255,0.1);
            cursor: pointer;
            transition: 0.3s;
        }
        .dot.active {
            background: var(--accent-color);
            transform: scale(1.25);
            border-color: var(--accent-color);
        }

        /* side arrows - positioned left and right */
        .slider-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255,255,255,0.15);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s;
            z-index: 15;
            pointer-events: auto;
        }
        .slider-arrow:hover {
            background: var(--accent-color);
            color: #0b1e1a;
            border-color: var(--accent-color);
        }
        .slider-arrow.prev {
            left: 20px;
        }
        .slider-arrow.next {
            right: 20px;
        }

        /* hide side arrows on very small screens if needed, but keep them */
        @media (max-width: 480px) {
            .slider-arrow {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
            .slider-arrow.prev {
                left: 10px;
            }
            .slider-arrow.next {
                right: 10px;
            }
        }

        /* small screens */
        @media (max-width: 768px) {
            .hero-slider-wrapper {
                min-height: 700px;
                height: auto;
            }
            .hero-slide {
                min-height: 700px;
                height: auto;
                padding: 60px 0 100px;
            }
            .hero-body {
                flex-direction: column;
                align-items: flex-start;
                margin-top: 40px;
            }
            .hero-counter-box {
                max-width: 100%;
                width: 100%;
                padding: 30px;
            }
            .section-title h1 {
                font-size: 2.2rem;
            }
            .slider-controls {
                bottom: 20px;
                gap: 10px;
                flex-wrap: wrap;
            }
            .hero-sub-heading {
                padding: 4px 14px 4px 4px;
            }
            .hero-body-content {
                max-width: 100%;
            }
            .hero-counter-box h2 {
                font-size: 32px;
            }
        }
        @media (max-width: 480px) {
            .hero-slide {
                min-height: 650px;
                padding: 40px 0 80px;
            }
            .section-title h1 {
                font-size: 1.8rem;
            }
            .hero-content p {
                font-size: 16px;
            }
            .btn-default {
                padding: 12px 30px;
                font-size: 14px;
            }
        }

        /* image-anime helper (original) */
        .image-anime {
            display: block;
            border-radius: 50%;
            overflow: hidden;
        }

        /* utility */
        .wow {
            visibility: visible;
        }
        .hero-ban {
            width: 100%;
        }
        .hero-row {
            display: flex;
            flex-wrap: wrap;
        }
        .hero-col, .hero-col-2 {
            width: 100%;
        }
        @media (min-width: 1200px) {
            .hero-col {
                width: 83.333%;
            }
        }
  </style>

  <!-- Hero Section Start -->
  <div class="hero-slider-wrapper" id="heroSliderWrapper">

        <!-- SLIDES CONTAINER -->
        <div class="hero-slides" id="heroSlides">
            <!-- slide 1 -->
            <div class="hero-slide" style="background-image: url('images/banner.jpeg');">
                <div class="container hero-ban">
                    <div class="row hero-row">
                        <div class="col-xl-10 hero-col" >
                            <div class="hero-content-wrap">
                                <!-- <div class="hero-sub-heading">
                                    <div class="satisfy-client-images">
                                        <div class="satisfy-client-image"><figure class="image-anime"><img src="https://randomuser.me/api/portraits/women/44.jpg" alt="volunteer" /></figure></div>
                                        <div class="satisfy-client-image"><figure class="image-anime"><img src="https://randomuser.me/api/portraits/men/32.jpg" alt="volunteer" /></figure></div>
                                        <div class="satisfy-client-image"><figure class="image-anime"><img src="https://randomuser.me/api/portraits/women/68.jpg" alt="volunteer" /></figure></div>
                                        <div class="satisfy-client-image"><figure class="image-anime"><img src="https://randomuser.me/api/portraits/men/75.jpg" alt="volunteer" /></figure></div>
                                    </div>
                                    <div class="satisfy-client-content"><p>Driving Positive Change Worldwide</p></div>
                                </div> -->
                                <div class="section-title">
                                    <h1>Empowering Communities, Transforming Lives</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 hero-col-2">
                            <div class="hero-body">
                                <div class="hero-body-content">
                                    <div class="hero-content">
                                        <p>Life Foundation is committed to building an inclusive, compassionate, and sustainable society through education, healthcare, empowerment, and humanitarian service.</p>
                                    </div>
                                    <div class="hero-body-btn">
                                        <a href="#" class="btn-default">Donate now</a>
                                    </div>
                                </div>
                                <div class="hero-counter-box">
                                    <h2><span class="counter">180</span><sup>+</sup></h2>
                                    <h3>Active Volunteers</h3>
                                    <p>A passionate network volunteers working on the ground.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- slide 2 -->
            <div class="hero-slide" style="background-image: url('images/banner-2.jpeg');">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-10">
                            <div class="hero-content-wrap">
                                <!-- <div class="hero-sub-heading">
                                    <div class="satisfy-client-images">
                                        <div class="satisfy-client-image"><figure class="image-anime"><img src="https://randomuser.me/api/portraits/men/45.jpg" alt="volunteer" /></figure></div>
                                        <div class="satisfy-client-image"><figure class="image-anime"><img src="https://randomuser.me/api/portraits/women/22.jpg" alt="volunteer" /></figure></div>
                                        <div class="satisfy-client-image"><figure class="image-anime"><img src="https://randomuser.me/api/portraits/men/67.jpg" alt="volunteer" /></figure></div>
                                        <div class="satisfy-client-image"><figure class="image-anime"><img src="https://randomuser.me/api/portraits/women/90.jpg" alt="volunteer" /></figure></div>
                                    </div>
                                    <div class="satisfy-client-content"><p>Education for every child</p></div>
                                </div> -->
                                <div class="section-title">
                                    <h1>Building Brighter Futures Through Learning</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="hero-body">
                                <div class="hero-body-content">
                                    <div class="hero-content">
                                        <p>Our educational programs reach remote villages, providing quality learning resources and teacher training to uplift entire communities.</p>
                                    </div>
                                    <div class="hero-body-btn">
                                        <a href="#" class="btn-default">Learn more</a>
                                    </div>
                                </div>
                                <div class="hero-counter-box">
                                    <h2><span class="counter">340</span><sup>+</sup></h2>
                                    <h3>Schools Supported</h3>
                                    <p>Empowering young minds with knowledge and skills.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- slide 3 -->
            <div class="hero-slide" style="background-image: url('images/banner-3.jpeg');">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-10">
                            <div class="hero-content-wrap">
                                <!-- <div class="hero-sub-heading">
                                    <div class="satisfy-client-images">
                                        <div class="satisfy-client-image"><figure class="image-anime"><img src="https://randomuser.me/api/portraits/women/33.jpg" alt="volunteer" /></figure></div>
                                        <div class="satisfy-client-image"><figure class="image-anime"><img src="https://randomuser.me/api/portraits/men/42.jpg" alt="volunteer" /></figure></div>
                                        <div class="satisfy-client-image"><figure class="image-anime"><img src="https://randomuser.me/api/portraits/women/11.jpg" alt="volunteer" /></figure></div>
                                        <div class="satisfy-client-image"><figure class="image-anime"><img src="https://randomuser.me/api/portraits/men/88.jpg" alt="volunteer" /></figure></div>
                                    </div>
                                    <div class="satisfy-client-content"><p>Healthcare for all</p></div>
                                </div> -->
                                <div class="section-title">
                                    <h1>Wellness & Dignity: Health Access for All</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="hero-body">
                                <div class="hero-body-content">
                                    <div class="hero-content">
                                        <p>Mobile health clinics, mental health support, and maternal care programs that reach underserved regions, ensuring no one is left behind.</p>
                                    </div>
                                    <div class="hero-body-btn">
                                        <a href="#" class="btn-default">Get involved</a>
                                    </div>
                                </div>
                                <div class="hero-counter-box">
                                    <h2><span class="counter">12</span><sup>k+</sup></h2>
                                    <h3>Patients Treated</h3>
                                    <p>Free medical camps and health awareness drives.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- controls: side arrows + bottom dots -->
        <button class="slider-arrow prev" id="prevSlide" aria-label="Previous slide"><i class="fas fa-chevron-left"></i></button>
        <button class="slider-arrow next" id="nextSlide" aria-label="Next slide"><i class="fas fa-chevron-right"></i></button>

        <div class="slider-controls">
            <div class="slider-dots" id="sliderDots"></div>
        </div>
    </div>
  <!-- Hero Section End -->

  <script>
    (function() {
            const slidesWrapper = document.getElementById('heroSlides');
            const slides = slidesWrapper.querySelectorAll('.hero-slide');
            const totalSlides = slides.length;
            let currentIndex = 0;
            let autoPlayInterval = null;
            const delay = 6000;

            const dotsContainer = document.getElementById('sliderDots');
            const prevBtn = document.getElementById('prevSlide');
            const nextBtn = document.getElementById('nextSlide');

            // build dots
            for (let i = 0; i < totalSlides; i++) {
                const dot = document.createElement('span');
                dot.className = 'dot' + (i === 0 ? ' active' : '');
                dot.dataset.index = i;
                dot.addEventListener('click', function() {
                    goTo(parseInt(this.dataset.index));
                });
                dotsContainer.appendChild(dot);
            }
            const dots = dotsContainer.querySelectorAll('.dot');

            function updateSlide(index) {
                if (index < 0) index = totalSlides - 1;
                if (index >= totalSlides) index = 0;
                currentIndex = index;
                slidesWrapper.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';
                dots.forEach((dot, i) => {
                    dot.classList.toggle('active', i === currentIndex);
                });
            }

            function goTo(index) {
                if (index === currentIndex) return;
                updateSlide(index);
                resetAutoPlay();
            }

            function nextSlide() {
                goTo(currentIndex + 1);
            }

            function prevSlide() {
                goTo(currentIndex - 1);
            }

            function startAutoPlay() {
                if (autoPlayInterval) clearInterval(autoPlayInterval);
                autoPlayInterval = setInterval(() => {
                    nextSlide();
                }, delay);
            }

            function resetAutoPlay() {
                if (autoPlayInterval) {
                    clearInterval(autoPlayInterval);
                    autoPlayInterval = null;
                }
                startAutoPlay();
            }

            nextBtn.addEventListener('click', nextSlide);
            prevBtn.addEventListener('click', prevSlide);

            // touch / swipe
            let touchStartX = 0;
            let touchEndX = 0;
            const wrapper = document.getElementById('heroSliderWrapper');
            wrapper.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });
            wrapper.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                const diff = touchStartX - touchEndX;
                if (Math.abs(diff) > 50) {
                    if (diff > 0) nextSlide();
                    else prevSlide();
                }
            }, { passive: true });

            // keyboard arrows
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') prevSlide();
                else if (e.key === 'ArrowRight') nextSlide();
            });

            // pause on hover
            wrapper.addEventListener('mouseenter', () => {
                if (autoPlayInterval) {
                    clearInterval(autoPlayInterval);
                    autoPlayInterval = null;
                }
            });
            wrapper.addEventListener('mouseleave', () => {
                startAutoPlay();
            });

            updateSlide(0);
            startAutoPlay();
        })();
  </script>
  

  <!-- Our Scrolling Ticker Section Start -->
  <div class="our-scrolling-ticker">
    <!-- Scrolling Ticker Box Start -->
    <div class="scrolling-ticker-box">
      <!-- Scrolling Content Start -->
      <div class="scrolling-content">
        <span><img src="images/icon-asterisk.svg" alt="" />Community
          Support</span>
        <span><img src="images/icon-asterisk.svg" alt="" />Health Support</span>
        <span><img src="images/icon-asterisk.svg" alt="" />Volunteer Impact</span>
        <span><img src="images/icon-asterisk.svg" alt="" />Future Ready</span>
        <span><img src="images/icon-asterisk.svg" alt="" />Community
          Support</span>
        <span><img src="images/icon-asterisk.svg" alt="" />Health Support</span>
        <span><img src="images/icon-asterisk.svg" alt="" />Volunteer Impact</span>
        <span><img src="images/icon-asterisk.svg" alt="" />Future Ready</span>
      </div>
      <!-- Scrolling Content End -->

      <!-- Scrolling Content Start -->
      <div class="scrolling-content">
        <span><img src="images/icon-asterisk.svg" alt="" />Community
          Support</span>
        <span><img src="images/icon-asterisk.svg" alt="" />Health Support</span>
        <span><img src="images/icon-asterisk.svg" alt="" />Volunteer Impact</span>
        <span><img src="images/icon-asterisk.svg" alt="" />Future Ready</span>
        <span><img src="images/icon-asterisk.svg" alt="" />Community
          Support</span>
        <span><img src="images/icon-asterisk.svg" alt="" />Health Support</span>
        <span><img src="images/icon-asterisk.svg" alt="" />Volunteer Impact</span>
        <span><img src="images/icon-asterisk.svg" alt="" />Future Ready</span>
      </div>
      <!-- Scrolling Content End -->
    </div>
    <!-- Scrolling Ticker Box End -->
  </div>
  <!-- Our Scrolling Ticker Section End -->

  <!-- About US Section Start -->
  <div class="about-us">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-xl-6">
          <!-- About Us Image Box Start -->
          <div class="about-us-image-box wow fadeInUp">
            <!-- About Us Image Box 1 Start -->
            <div class="about-us-image-box-1">
              <!-- About Us Image Start -->
              <div class="about-us-image">
                <figure class="image-anime">
                  <img src="images/about1.jpeg" alt="" />
                </figure>
              </div>
              <!-- About Us Image End -->

              <!-- About Us Counter Box Start -->
              <div class="about-us-counter-box">
                <h2><span class="counter">15</span>+</h2>
                <p>Years Of Service</p>
              </div>
              <!-- About Us Counter Box End -->
            </div>
            <!-- About Us Image Box 1 End -->

            <!-- About Us Image Box 2 Start -->
            <div class="about-us-image-box-2">
              <!-- About Us Image Start -->
              <div class="about-us-image">
                <figure class="image-anime">
                  <img src="images/about2.jpeg" alt="" />
                </figure>
              </div>
              <!-- About Us Image End -->
            </div>
            <!-- About Us Image Box 2 End -->
          </div>
          <!-- About Us Image Box End -->
        </div>

        <div class="col-xl-6">
          <!-- About Us Content Start -->
          <div class="about-us-content">
            <!-- Section Title Start -->
            <div class="section-title">
              <span class="section-sub-title wow fadeInUp">ABOUT LIFE FOUNDATION</span>
              <h2 class="text-anime-style-3" data-cursor="-opaque">
                Dedicated to Humanity, Development & Social Justice
              </h2>
              <p class="wow fadeInUp" data-wow-delay="0.2s">
                Life Foundation is a non-profit humanitarian organization established in 2010 with a mission to empower women, children, youth, and marginalized communities through sustainable development initiatives. Registered under the Societies Registration Act, 1860, the organization works across education, healthcare, livelihood, environment, disaster relief, child protection, disability inclusion, and community development.
              </p>
              <p>Guided by compassion, integrity, and service to humanity, Life Foundation continuously strives to create positive social transformation and inclusive growth.</p>
            </div>
            <!-- Section Title End -->

            <!-- About Us Body Start -->
            <div class="about-us-body wow fadeInUp" data-wow-delay="0.4s">
              <!-- About Body Item Start -->
              <div class="about-body-item">
                <div class="icon-box">
                  <img src="images/icon-about-us-item-1.svg" alt="" />
                </div>
                <div class="about-body-item-content">
                  <h3>Empowering Communities</h3>
                  <ul>
                    <li>
                      Women & Child Welfare
                    </li>
                    <li>Education & Skill Development</li>
                    <li>Health & Nutrition</li>
                    <li>Rural & Community
                      Development</li>
                    <li>Environmental Sustainability</li>
                    <li>Relief & Rehabilitation</li>
                  </ul>
                </div>
              </div>
              <!-- About Body Item End -->

              <!-- About Body Image Start -->
              <div class="about-body-image">
                <figure class="image-anime">
                  <img src="images/about3.jpeg" alt="" />
                </figure>
              </div>
              <!-- About Body Image Start -->
            </div>
            <!-- About Us Body End -->

            <!-- About Us Footer Start -->
            <div class="about-us-footer wow fadeInUp" data-wow-delay="0.6s">
              <!-- About Us Button Start -->
              <div class="about-us-btn">
                <a href="about.php" class="btn-default">More About Us</a>
              </div>
              <!-- About Us Button End -->

              <!-- Video Play Button Start -->
              <!-- <div class="video-play-button">
                <a
                  href="https://www.youtube.com/watch?v=Y-x0efG1seA"
                  class="popup-video bg-effect" 
                  data-cursor-text="Play">
                  <i class="fa-solid fa-play"></i>
                </a>
                <p>Watch Our Video</p>
              </div> -->
              <!-- Video Play Button End -->
            </div>
            <!-- About Us Footer End -->
          </div>
          <!-- About Us Content End -->
        </div>
      </div>
    </div>
  </div>
  <!-- About US Section End -->

  <!-- Why Choosse Us Section Start -->
  <div class="why-choose-us">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-xl-6">
          <!-- Why Choosse Us Content Start -->
          <div class="why-choose-us-content">
            <!-- Section Title Start -->
            <div class="section-title">
              <span class="section-sub-title wow fadeInUp">Why Life Foundation?</span>
              <h2 class="text-anime-style-3" data-cursor="-opaque">What Makes our Impact Strong</h2>
              <p class="wow fadeInUp" data-wow-delay="0.2s">We approach every initiative with compassion, operate with full transparency, and focus on creating sustainable impact that improves lives.</p>
            </div>
            <!-- Section Title End -->

            <!-- Why Choosse Item List Start -->
            <div class="why-choose-item-list wow fadeInUp" data-wow-delay="0.4s">
              <!-- Why Choosse Item Start -->
              <div class="why-choose-item">
                <div class="icon-box">
                  <img src="images/icon-why-choose-us-item-1.svg" alt="">
                </div>
                <div class="why-choose-item-content">
                  <p>Grassroots Community Engagement</p>
                </div>
              </div>
              <!-- Why Choosse Item End -->

              <!-- Why Choosse Item Start -->
              <div class="why-choose-item">
                <div class="icon-box">
                  <img src="images/icon-why-choose-us-item-2.svg" alt="">
                </div>
                <div class="why-choose-item-content">
                  <p>Transparent & Accountable Operations</p>
                </div>
              </div>
              <!-- Why Choosse Item End -->

              <!-- Why Choosse Item Start -->
              <div class="why-choose-item">
                <div class="icon-box">
                  <img src="images/icon-why-choose-us-item-3.svg" alt="">
                </div>
                <div class="why-choose-item-content">
                  <p>Inclusive Development Approach</p>
                </div>
              </div>
              <!-- Why Choosse Item End -->

              <div class="why-choose-item">
                <div class="icon-box">
                  <img src="images/icon-why-choose-us-item-3.svg" alt="">
                </div>
                <div class="why-choose-item-content">
                  <p>Experienced Social Workers</p>
                </div>
              </div>

              <div class="why-choose-item">
                <div class="icon-box">
                  <img src="images/icon-why-choose-us-item-3.svg" alt="">
                </div>
                <div class="why-choose-item-content">
                  <p>Sustainable Impact Programs</p>
                </div>
              </div>

              <div class="why-choose-item">
                <div class="icon-box">
                  <img src="images/icon-why-choose-us-item-3.svg" alt="">
                </div>
                <div class="why-choose-item-content">
                  <p>Compassion-Driven Mission</p>
                </div>
              </div>

            </div>
            <!-- Why Choosse Item List End -->
          </div>
          <!-- Why Choosse Us Content End -->
        </div>

        <div class="col-xl-6">
          <!-- Why Choosse Us Image Box Start -->
          <div class="why-choose-us-image-box wow fadeInUp">
            <!-- Why Choosse Image Box 1 Start -->
            <div class="why-choose-image-box-1">
              <!-- Why Choosse Image Start -->
              <div class="why-choose-image">
                <figure class="image-anime">
                  <img src="images/why1.jpeg" alt="">
                </figure>
              </div>
              <!-- Why Choosse Image End -->
            </div>
            <!-- Why Choosse Image Box 1 End -->

            <!-- Why Choosse Image Box 2 Start -->
            <div class="why-choose-image-box-2">
              <!-- Why Choosse Image Start -->
              <div class="why-choose-image">
                <figure class="image-anime">
                  <img src="images/why2.jpeg" alt="">
                </figure>
              </div>
              <!-- Why Choosse Image Start -->

              <!-- Why Choosse Contact Box Start -->
              <div class="why-choose-contact-box">
                <div class="icon-box">
                  <img src="images/icon-headphone-primary.svg" alt="">
                </div>
                <div class="why-choose-contact-content">
                  <h3>Call Us</h3>
                  <p><a href="tel:+919862059664">+91 98620 59664</a></p>
                </div>
              </div>
              <!-- Why Choosse Contact Box End -->
            </div>
            <!-- Why Choosse Image Box 2 End -->
          </div>
          <!-- Why Choosse Us Image Box End -->
        </div>
      </div>
    </div>
  </div>
  <!-- Why Choosse Us Section End -->

  <!-- Our Approach Section Start -->
  <div class="our-approach">
    <div class="container">
      <div class="row section-row align-items-center">
        <div class="col-xl-6">
          <!-- Section Title Start -->
          <div class="section-title">
            <span class="section-sub-title wow fadeInUp">Our Approach</span>
            <h2 class="text-anime-style-3" data-cursor="-opaque">From Understanding to Meaningful Action</h2>
          </div>
          <!-- Section Title End -->
        </div>

        <div class="col-xl-6">
          <!-- Section Content Btn Start -->
          <div class="section-content-btn">
            <!-- Section Title Content Start -->
            <div class="section-title-content wow fadeInUp" data-wow-delay="0.2s">
              <p>Through careful planning, collaboration, and transparent execution, we turn insights into practical initiatives that create lasting, positive impact where it matters most.</p>
            </div>
            <!-- Section Title Content End -->

            <!-- Section Button Start -->
            <div class="section-btn wow fadeInUp" data-wow-delay="0.4s">
              <a class="btn-default" href="contact.php">Contact Us</a>
            </div>
            <!-- Section Button End -->
          </div>
          <!-- Section Content Btn End -->
        </div>
      </div>

      <div class="row">
        <div class="col-xl-4 col-md-6">
          <!-- Our Approach Item Start -->
          <div class="approach-item box-1 wow fadeInUp">
            <div class="approach-item-header">
              <div class="icon-box">
                <img src="images/icon-our-approach-1.svg" alt="">
              </div>
              <div class="approach-item-title">
                <h3>Our Mission</h3>
              </div>
            </div>
            <div class="approach-item-content">
              <p>To promote inclusive development through education, healthcare, empowerment, sustainable livelihood, environmental stewardship, and humanitarian action.</p>

            </div>
          </div>
          <!-- Our Approach Item End -->
        </div>

        <div class="col-xl-4 col-md-6">
          <!-- Our Approach Item Start -->
          <div class="approach-item box-2 wow fadeInUp" data-wow-delay="0.2s">
            <div class="approach-item-header">
              <div class="icon-box">
                <img src="images/icon-our-approach-2.svg" alt="">
              </div>
              <div class="approach-item-title">
                <h3>Our Vision</h3>
              </div>
            </div>
            <div class="approach-item-content">
              <p>To build a just, inclusive, and compassionate society where every individual can live with dignity, equality, and opportunity.</p>

            </div>
          </div>
          <!-- Our Approach Item End -->
        </div>

        <div class="col-xl-4 col-md-6">
          <!-- Our Approach Item Start -->
          <div class="approach-item box-3 wow fadeInUp" data-wow-delay="0.4s">
            <div class="approach-item-header">
              <div class="icon-box">
                <img src="images/icon-our-approach-3.svg" alt="">
              </div>
              <div class="approach-item-title">
                <h3>Our Values</h3>
              </div>
            </div>
            <div class="approach-item-content">
              <p>We work with honesty, transparency, and accountability, building trust with the communities we serve and our partners.</p>

            </div>
          </div>
          <!-- Our Approach Item End -->
        </div>

        <div class="col-lg-12">
          <!-- Section Footer Text Start -->
          <div class="section-footer-text section-satisfy-img wow fadeInUp" data-wow-delay="0.6s">
            <!-- Satisfy Client Images Start -->
            <div class="satisfy-client-images">
              <div class="satisfy-client-image">
                <figure class="image-anime">
                  <img src="images/author-1.jpg" alt="">
                </figure>
              </div>
              <div class="satisfy-client-image add-more">
                <img src="images/icon-phone-primary.svg" alt="">
              </div>
            </div>
            <!-- Satisfy Client Images End -->
          </div>
          <!-- Section Footer Text End -->
        </div>
      </div>
    </div>
  </div>
  <!-- Our Approach Section End -->

  <!-- Our Programs Section Start -->
  <div class="our-program">
    <div class="container">
      <div class="row section-row">
        <div class="col-lg-12">
          <!-- Section Title Section Start -->
          <div class="section-title section-title-center">
            <span class="section-sub-title wow fadeInUp">Our Programs</span>
            <h2 class="text-anime-style-3" data-cursor="-opaque">
              Initiatives that Empower
            </h2>
            <p class="wow fadeInUp" data-wow-delay="0.2s">
              Our programs are thoughtfully designed to address real community
              needs, promote self-reliance, and create sustainable
              opportunities in education, etc.
            </p>
          </div>
          <!-- Section Title Section End -->
        </div>
      </div>

      <div class="row">

        <div class="col-xl-4 col-md-6">
          <!-- Programs Item Start -->
          <div class="program-item wow fadeInUp" data-wow-delay="0.4s">
            <div class="program-item-image">
              <a href="programs.php" data-cursor-text="View">
                <figure>
                  <img src="images/pro1.jpeg" alt="" />
                </figure>
              </a>
            </div>
            <div class="program-item-content">
              <ul>
                <li>Crisis Support</li>
              </ul>
              <h2>
                <a href="programs.php">Community service activities</a>
              </h2>
              <p></p>
            </div>
          </div>
          <!-- Programs Item End -->
        </div>

        <div class="col-xl-4 col-md-6">
          <!-- Programs Item Start -->
          <div class="program-item wow fadeInUp" data-wow-delay="0.4s">
            <div class="program-item-image">
              <a href="programs.php" data-cursor-text="View">
                <figure>
                  <img src="images/pro2.jpeg" alt="" />
                </figure>
              </a>
            </div>
            <div class="program-item-content">
              <ul>
                <li>Crisis Support</li>
              </ul>
              <h2>
                <a href="">Children education visuals</a>
              </h2>
              <p></p>
            </div>
          </div>
          <!-- Programs Item End -->
        </div>

        <div class="col-xl-4 col-md-6">
          <!-- Programs Item Start -->
          <div class="program-item wow fadeInUp" data-wow-delay="0.4s">
            <div class="program-item-image">
              <a href="programs.php" data-cursor-text="View">
                <figure>
                  <img src="images/pro4.jpeg" alt="" />
                </figure>
              </a>
            </div>
            <div class="program-item-content">
              <ul>
                <li>Crisis Support</li>
              </ul>
              <h2>
                <a href="">Women Empowerment</a>
              </h2>
              <p></p>
            </div>
          </div>
          <!-- Programs Item End -->
        </div>

        <div class="col-xl-4 col-md-6">
          <!-- Programs Item Start -->
          <div class="program-item wow fadeInUp">
            <div class="program-item-image">
              <a href="programs.php" data-cursor-text="View">
                <figure>
                  <img src="images/pro3.jpeg" alt="" />
                </figure>
              </a>
            </div>
            <div class="program-item-content">
              <ul>
                <li>Community Health</li>
              </ul>
              <h2>
                <a href="">Child Welfare</a>
                <!-- <a href="program-single.html">Health & Wellness Outreach</a> -->
              </h2>

            </div>
          </div>
          <!-- Programs Item End -->
        </div>

        <div class="col-xl-4 col-md-6">
          <!-- Programs Item Start -->
          <div class="program-item wow fadeInUp" data-wow-delay="0.2s">
            <div class="program-item-image">
              <a href="programs.php" data-cursor-text="View">
                <figure>
                  <img src="images/pro5.jpeg" alt="" />
                </figure>
              </a>
            </div>
            <div class="program-item-content">
              <ul>
                <li>Legal Awareness</li>
              </ul>
              <h2><a href="">Healthcare & Awareness</a></h2>
            </div>
          </div>
          <!-- Programs Item End -->
        </div>

      </div>
    </div>
  </div>
  <!-- Our Programs Section End -->

  <!-- Our Impact Section Start -->
  <div class="our-impact">
    <div class="container">
      <div class="row">
        <div class="col-xl-6">
          <!-- Our Impact Images Box Start -->
          <div class="our-impact-images-box wow fadeInUp">
            <!-- Our Impact Images Start -->
            <div class="our-impact-image-1">
              <figure class="image-anime">
                <img src="images/imp1.jpeg" alt="">
              </figure>
            </div>
            <!-- Our Impact Images End -->

            <!-- Our Impact Images Start -->
            <div class="our-impact-image-2">
              <figure class="image-anime">
                <img src="images/imp2.jpeg" alt="">
              </figure>
            </div>
            <!-- Our Impact Images End -->
          </div>
          <!-- Our Impact Images Box End -->
        </div>

        <div class="col-xl-6">
          <!-- Our Impact Content Start -->
          <div class="our-impact-content">
            <!-- Section title Start -->
            <div class="section-title">
              <span class="section-sub-title wow fadeInUp">Our Impact</span>
              <h2 class="text-anime-style-3" data-cursor="-opaque">Creating Change That Truly Matters</h2>
              <p class="wow fadeInUp" data-wow-delay="0.2s">Through dedicated programs, transparent processes, & long-term commitment, we ensure every effort leads to meaningful and lasting impact.</p>
            </div>
            <!-- Section title End -->

            <!-- Our Impact Item List Start -->
            <div class="our-impact-item-list">
              <!-- Our Impact Item Start -->
              <div class="our-impact-item wow fadeInUp">
                <div class="our-impact-item-header">
                  <h2><span class="counter">15</span><sup>+</sup></h2>
                </div>
                <div class="our-impact-item-body">
                  <h3>Years of Service</h3>
                </div>
              </div>
              <!-- Our Impact Item Start -->

              <!-- Our Impact Item Start -->
              <div class="our-impact-item wow fadeInUp" data-wow-delay="0.2s">
                <div class="our-impact-item-header">
                  <h2><span class="counter">10,000</span><sup>+</sup></h2>
                </div>
                <div class="our-impact-item-body">
                  <h3>Lives Impacted</h3>
                </div>
              </div>

              <!-- Our Impact Item Start -->
              <div class="our-impact-item wow fadeInUp">
                <div class="our-impact-item-header">
                  <h2><span class="counter">20</span><sup>+</sup></h2>
                </div>
                <div class="our-impact-item-body">
                  <h3>Awareness Programs</h3>
                </div>
              </div>
              <!-- Our Impact Item Start -->

              <!-- Our Impact Item Start -->
              <div class="our-impact-item wow fadeInUp" data-wow-delay="0.2s">
                <div class="our-impact-item-header">
                  <h2><span class="counter">10</span><sup>+</sup></h2>
                </div>
                <div class="our-impact-item-body">
                  <h3>Villages Reached</h3>
                </div>
              </div>
              <!-- Our Impact Item Start -->
            </div>
            <!-- Our Impact Item List End -->
          </div>
          <!-- Our Impact Content End -->
        </div>
      </div>
    </div>
  </div>
  <!-- Our Impact Section End -->

  <!-- Our Blog Section Start -->
  <div class="our-blog">
    <div class="container">
      <div class="row section-row">
        <div class="col-lg-12">
          <!-- Section Title Start -->
          <div class="section-title section-title-center">
            <span class="section-sub-title wow fadeInUp">Latest Blogs</span>
            <h2 class="text-anime-style-3" data-cursor="-opaque">
              Insights & Stories
            </h2>
            <p class="wow fadeInUp" data-wow-delay="0.2s">
              Read real stories from the field, community experiences, and
              thought-provoking perspectives that reflect our mission and
              impact.
            </p>
          </div>
          <!-- Section Title End -->
        </div>
      </div>

      <div class="row">
        <div class="col-xl-4 col-md-6">
          <!-- Post Item Start -->
          <div class="post-item wow fadeInUp">
            <!-- Post Item image Start -->
            <div class="post-item-image">
              <a href="" data-cursor-text="View">
                <figure>
                  <img src="images/blo-1.jpeg" alt="" />
                </figure>
              </a>
            </div>
            <!-- Post Item image End -->

            <!-- Post Item Body Start -->
            <div class="post-item-body">
              <!-- Post Item Tag Start -->
              <div class="post-item-tag">
                <a href=" ">International Women’s Day Celebration</a>
              </div>
              <!-- Post Item Tag End -->

              <!-- Post Item Body Content Start -->
              <div class="post-item-body-content">
                <!-- Post Item Content Start -->
                <div class="post-item-content">
                  <h2>
                    <a href=" ">Celebrating the strength, achievements, and empowerment of women.</a>
                  </h2>
                </div>
                <!-- Post Item Content End -->

                <!-- Post Item Button Start -->
                <div class="post-item-btn">
                  <!-- <a href=" " class="readmore-btn">Read More</a> -->
                  <a href="" class="readmore-btn">Read More</a>
                </div>
                <!-- Post Item Button End -->
              </div>
              <!-- Post Item Body Content End -->
            </div>
            <!-- Post Item Body End -->
          </div>
          <!-- Post Item End -->
        </div>

        <div class="col-xl-4 col-md-6">
          <!-- Post Item Start -->
          <div class="post-item wow fadeInUp" data-wow-delay="0.2s">
            <!-- Post Item image Start -->
            <div class="post-item-image">
              <a href="" data-cursor-text="View">
                <figure>
                  <img src="images/blo3-a.jpeg" alt="" />
                </figure>
              </a>
            </div>
            <!-- Post Item image End -->

            <!-- Post Item Body Start -->
            <div class="post-item-body">
              <!-- Post Item Tag Start -->
              <div class="post-item-tag">
                <a href="">Free Health Camp Organized</a>
              </div>
              <!-- Post Item Tag End -->

              <!-- Post Item Body Content Start -->
              <div class="post-item-body-content">
                <!-- Post Item Content Start -->
                <div class="post-item-content">
                  <h2>
                    <a href="">Providing free healthcare access for a healthier community.</a>
                  </h2>
                </div>
                <!-- Post Item Content End -->

                <!-- Post Item Button Start -->
                <div class="post-item-btn">
                  <a href="" class="readmore-btn">Read More</a>
                </div>
                <!-- Post Item Button End -->
              </div>
              <!-- Post Item Body Content End -->
            </div>
            <!-- Post Item Body End -->
          </div>
          <!-- Post Item End -->
        </div>

        <div class="col-xl-4 col-md-6">
          <!-- Post Item Start -->
          <div class="post-item wow fadeInUp" data-wow-delay="0.4s">
            <!-- Post Item image Start -->
            <div class="post-item-image">
              <a href="" data-cursor-text="View">
                <figure>
                  <img src="images/blo2.jpeg" alt="" />
                </figure>
              </a>
            </div>
            <!-- Post Item image End -->

            <!-- Post Item Body Start -->
            <div class="post-item-body">
              <!-- Post Item Tag Start -->
              <div class="post-item-tag">
                <a href="">Educational Support Drive</a>
              </div>
              <!-- Post Item Tag End -->

              <!-- Post Item Body Content Start -->
              <div class="post-item-body-content">
                <!-- Post Item Content Start -->
                <div class="post-item-content">
                  <h2>
                    <a href="">Empowering lives through education, one child at a time.</a>
                  </h2>
                </div>
                <!-- Post Item Content End -->

                <!-- Post Item Button Start -->
                <div class="post-item-btn">
                  <a href="" class="readmore-btn">Read More</a>
                </div>
                <!-- Post Item Button End -->
              </div>
              <!-- Post Item Body Content End -->
            </div>
            <!-- Post Item Body End -->
          </div>
          <!-- Post Item End -->
        </div>
      </div>
    </div>
  </div>
  <!-- Our Blog Section End -->

  <!-- Our Testimonials Section Start -->
  <div class="our-testimonials">
    <div class="container">
      <div class="row section-row">
        <div class="col-lg-12">
          <!-- Section Title Start -->
          <div class="section-title section-title-center">
            <span class="section-sub-title wow fadeInUp">Our Testimonials</span>
            <h2 class="text-anime-style-3" data-cursor="-opaque">
              Voices of Real Peoples
            </h2>
            <p class="wow fadeInUp" data-wow-delay="0.2s">
              Hear directly from the people, volunteers, and partners whose
              lives have been touched by our work and who continue to believe
              in our mission.
            </p>
          </div>
          <!-- Section Title End -->
        </div>
      </div>

      <div class="row">
        <div class="col-lg-12">
          <div class="testimonial-slider wow fadeInUp">
            <div class="swiper">
              <div class="swiper-wrapper" data-cursor-text="Drag">
                <!-- Testimonial Slide Start -->
                <div class="swiper-slide">
                  <!-- Testimonial Item Start -->
                  <div class="testimonial-item">
                    <div class="testimonial-item-header">
                      <div class="testimonial-item-rating">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                      </div>
                      <div class="testimonial-item-content">
                        <p>
                          “Being part of this organization has been truly life
                          change. The team's compassion, transparency &
                          commitment to real impact made me feel proud to
                          contribute my time and skills.”
                        </p>
                      </div>
                    </div>
                    <div class="testimonial-item-body">
                      <div class="testimonial-author-content">
                        <h2>Annette Black</h2>
                        <p>Community Volunteer</p>
                      </div>
                      <div class="testimonial-item-image-box">
                        <div class="testimonial-author-image">
                          <figure class="image-anime">
                            <img src="images/author-1.jpg" alt="" />
                          </figure>
                        </div>
                        <div class="testimonial-item-quote">
                          <img src="images/testimonial-quote.svg" alt="" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Testimonial Item End -->
                </div>
                <!-- Testimonial Slide End -->

                <!-- Testimonial Slide Start -->
                <div class="swiper-slide">
                  <!-- Testimonial Item Start -->
                  <div class="testimonial-item">
                    <div class="testimonial-item-header">
                      <div class="testimonial-item-rating">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                      </div>
                      <div class="testimonial-item-content">
                        <p>
                          “Working with this organization has been life
                          changing. Their commit-ment to transparency &
                          community development is genuine, and the impact
                          they create is visible at every level.”
                        </p>
                      </div>
                    </div>
                    <div class="testimonial-item-body">
                      <div class="testimonial-author-content">
                        <h2>Jane Cooper</h2>
                        <p>Long-Term Supporter</p>
                      </div>
                      <div class="testimonial-item-image-box">
                        <div class="testimonial-author-image">
                          <figure class="image-anime">
                            <img src="images/author-2.jpg" alt="" />
                          </figure>
                        </div>
                        <div class="testimonial-item-quote">
                          <img src="images/testimonial-quote.svg" alt="" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Testimonial Item End -->
                </div>
                <!-- Testimonial Slide End -->

                <!-- Testimonial Slide Start -->
                <div class="swiper-slide">
                  <!-- Testimonial Item Start -->
                  <div class="testimonial-item">
                    <div class="testimonial-item-header">
                      <div class="testimonial-item-rating">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                      </div>
                      <div class="testimonial-item-content">
                        <p>
                          “From education programs to women empowerment
                          initiatives, every project is thoughtfully planned
                          and responsibly executed. It's inspiring to be part
                          of something so meaningful.”
                        </p>
                      </div>
                    </div>
                    <div class="testimonial-item-body">
                      <div class="testimonial-author-content">
                        <h2>Joseph Willison</h2>
                        <p>Local Partner</p>
                      </div>
                      <div class="testimonial-item-image-box">
                        <div class="testimonial-author-image">
                          <figure class="image-anime">
                            <img src="images/author-3.jpg" alt="" />
                          </figure>
                        </div>
                        <div class="testimonial-item-quote">
                          <img src="images/testimonial-quote.svg" alt="" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Testimonial Item End -->
                </div>
                <!-- Testimonial Slide End -->

                <!-- Testimonial Slide Start -->
                <div class="swiper-slide">
                  <!-- Testimonial Item Start -->
                  <div class="testimonial-item">
                    <div class="testimonial-item-header">
                      <div class="testimonial-item-rating">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                      </div>
                      <div class="testimonial-item-content">
                        <p>
                          “When this organization started working in our area,
                          we honestly expect change. The education & health
                          programs have helped children stay in school & our
                          families stay healthy.”
                        </p>
                      </div>
                    </div>
                    <div class="testimonial-item-body">
                      <div class="testimonial-author-content">
                        <h2>Bessie Cooper</h2>
                        <p>Local Partner</p>
                      </div>
                      <div class="testimonial-item-image-box">
                        <div class="testimonial-author-image">
                          <figure class="image-anime">
                            <img src="images/author-4.jpg" alt="" />
                          </figure>
                        </div>
                        <div class="testimonial-item-quote">
                          <img src="images/testimonial-quote.svg" alt="" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Testimonial Item End -->
                </div>
                <!-- Testimonial Slide End -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Our Testimonials Section End -->


  <!-- Our Partners Section Start -->
  <div class="our-testimonials">
    <div class="container">
      <div class="row section-row">
        <div class="col-lg-12">
          <!-- Section Title Start -->
          <div class="section-title section-title-center">
            <span class="section-sub-title wow fadeInUp">Our Partners</span>
            <h2 class="text-anime-style-3" data-cursor="-opaque">
              Trusted Partners & Supporters
            </h2>
            <p class="wow fadeInUp" data-wow-delay="0.2s">
              We proudly collaborate with organizations, institutions, and supporters who
              share our vision of creating positive social impact and empowering communities.
            </p>
          </div>
          <!-- Section Title End -->
        </div>
      </div>

      <div class="row">
        <div class="col-lg-12">
          <div class="testimonial-slider wow fadeInUp">
            <div class="swiper">
              <div class="swiper-wrapper" data-cursor-text="Drag">
                <!-- Testimonial Slide Start -->
                <div class="swiper-slide">
                  <!-- Testimonial Item Start -->
                  <div class="team-item wow fadeInUp">
                    <div class="team-item-image">
                      <a href=" " data-cursor-text="View">
                        <figure>
                          <img src="images/t-1.jpeg" alt="">
                        </figure>
                      </a>
                    </div>

                    <div class="team-item-content">
                      <!-- <ul>
                        <li>Program Director</li>
                      </ul> -->
                      <h2><a href=" ">FCRA Partner</a></h2>
                    </div>
                  </div>
                  <!-- Testimonial Item End -->
                </div>
                <!-- Testimonial Slide End -->

                <!-- Testimonial Slide Start -->
                <div class="swiper-slide">
                  <!-- Testimonial Item Start -->
                  <div class="team-item wow fadeInUp">
                    <div class="team-item-image">
                      <a href=" " data-cursor-text="View">
                        <figure>
                          <img src="images/t-2.jpeg" alt="">
                        </figure>
                      </a>
                    </div>

                    <div class="team-item-content">
                      <!-- <ul>
                        <li>Program Director</li>
                      </ul> -->
                      <h2><a href=" ">FCRA Collaborative Partner </a></h2>
                    </div>
                  </div>
                  <!-- Testimonial Item End -->
                </div>
                <!-- Testimonial Slide End -->

                <!-- Testimonial Slide Start -->
                <div class="swiper-slide">
                  <!-- Testimonial Item Start -->
                  <div class="team-item wow fadeInUp">
                    <div class="team-item-image">
                      <a href=" " data-cursor-text="View">
                        <figure>
                          <img src="images/t-3.jpeg" alt="">
                        </figure>
                      </a>
                    </div>

                    <div class="team-item-content">
                      <!-- <ul>
                        <li>Program Director</li>
                      </ul> -->
                      <h2><a href=" ">CSR Partner</a></h2>
                    </div>
                  </div>
                  <!-- Testimonial Item End -->
                </div>
                <!-- Testimonial Slide End -->

                <div class="swiper-slide">
                  <!-- Testimonial Item Start -->
                  <div class="team-item wow fadeInUp">
                    <div class="team-item-image">
                      <a href=" " data-cursor-text="View">
                        <figure>
                          <img src="images/t-4.jpeg" alt="">
                        </figure>
                      </a>
                    </div>

                    <div class="team-item-content">
                      <!-- <ul>
                        <li>Program Director</li>
                      </ul> -->
                      <h2><a href=" ">Govt. Department</a></h2>
                    </div>
                  </div>
                  <!-- Testimonial Item End -->
                </div>

                <div class="swiper-slide">
                  <!-- Testimonial Item Start -->
                  <div class="team-item wow fadeInUp">
                    <div class="team-item-image">
                      <a href=" " data-cursor-text="View">
                        <figure>
                          <img src="images/t-5.jpeg" alt="">
                        </figure>
                      </a>
                    </div>

                    <div class="team-item-content">
                      <!-- <ul>
                        <li>Program Director</li>
                      </ul> -->
                      <h2><a href=" ">Donor /Local Institution</a></h2>
                    </div>
                  </div>
                  <!-- Testimonial Item End -->
                </div>

                <div class="swiper-slide">
                  <div class="team-item wow fadeInUp">
                    <div class="team-item-image">
                      <a href=" " data-cursor-text="View">
                        <figure>
                          <img src="images/t-6.jpeg" alt="">
                        </figure>
                      </a>
                    </div>
                    <div class="team-item-content">
                      <h2><a href=" ">Supporter</a></h2>
                    </div>
                  </div>
                </div>

                <div class="swiper-slide">
                  <div class="team-item wow fadeInUp">
                    <div class="team-item-image">
                      <a href=" " data-cursor-text="View">
                        <figure>
                          <img src="images/t-6.jpeg" alt="">
                        </figure>
                      </a>
                    </div>
                    <div class="team-item-content">
                      <h2><a href=" ">Supporter</a></h2>
                    </div>
                  </div>
                </div>

                <div class="swiper-slide">
                  <div class="team-item wow fadeInUp">
                    <div class="team-item-image">
                      <a href=" " data-cursor-text="View">
                        <figure>
                          <img src="images/t-7.jpeg" alt="">
                        </figure>
                      </a>
                    </div>
                    <div class="team-item-content">
                      <h2><a href=" ">Supporter</a></h2>
                    </div>
                  </div>
                </div>

                <div class="swiper-slide">
                  <div class="team-item wow fadeInUp">
                    <div class="team-item-image">
                      <a href=" " data-cursor-text="View">
                        <figure>
                          <img src="images/t-8.jpeg" alt="">
                        </figure>
                      </a>
                    </div>
                    <div class="team-item-content">
                      <h2><a href=" ">Supporter</a></h2>
                    </div>
                  </div>
                </div>

                <div class="swiper-slide">
                  <div class="team-item wow fadeInUp">
                    <div class="team-item-image">
                      <a href=" " data-cursor-text="View">
                        <figure>
                          <img src="images/t-9.jpeg" alt="">
                        </figure>
                      </a>
                    </div>
                    <div class="team-item-content">
                      <h2><a href=" ">Govt.Supporter</a></h2>
                    </div>
                  </div>
                </div>

                <div class="swiper-slide">
                  <div class="team-item wow fadeInUp">
                    <div class="team-item-image">
                      <a href=" " data-cursor-text="View">
                        <figure>
                          <img src="images/t-10.jpeg" alt="">
                        </figure>
                      </a>
                    </div>
                    <div class="team-item-content">
                      <h2><a href=" "> Our Partners & Supporters</a></h2>
                    </div>
                  </div>
                </div>

                <div class="swiper-slide">
                  <div class="team-item wow fadeInUp">
                    <div class="team-item-image">
                      <a href=" " data-cursor-text="View">
                        <figure>
                          <img src="images/t-9.jpeg" alt="">
                        </figure>
                      </a>
                    </div>
                    <div class="team-item-content">
                      <h2><a href=" ">Govt.Supporter</a></h2>
                    </div>
                  </div>
                </div>

                <div class="swiper-slide">
                  <div class="team-item wow fadeInUp">
                    <div class="team-item-image">
                      <a href=" " data-cursor-text="View">
                        <figure>
                          <img src="images/t-9.jpeg" alt="">
                        </figure>
                      </a>
                    </div>
                    <div class="team-item-content">
                      <h2><a href=" ">Govt.Supporter</a></h2>
                    </div>
                  </div>
                </div>

                <div class="swiper-slide">
                  <div class="team-item wow fadeInUp">
                    <div class="team-item-image">
                      <a href=" " data-cursor-text="View">
                        <figure>
                          <img src="images/t-9.jpeg" alt="">
                        </figure>
                      </a>
                    </div>
                    <div class="team-item-content">
                      <h2><a href=" ">Govt.Supporter</a></h2>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Our Partners Section End -->



  <!-- Footer Start -->
  <?php include 'inc/footer.php'; ?>
  <!-- Footer End -->



  <!-- Jquery Library File -->
  <script src="js/jquery-3.7.1.min.js"></script>
  <!-- Bootstrap js file -->
  <script src="js/bootstrap.min.js"></script>
  <!-- Validator js file -->
  <script src="js/validator.min.js"></script>
  <!-- SlickNav js file -->
  <script src="js/jquery.slicknav.js"></script>
  <!-- Swiper js file -->
  <script src="js/swiper-bundle.min.js"></script>
  <!-- Counter js file -->
  <script src="js/jquery.waypoints.min.js"></script>
  <script src="js/jquery.counterup.min.js"></script>
  <!-- Magnific js file -->
  <script src="js/jquery.magnific-popup.min.js"></script>
  <!-- SmoothScroll -->
  <script src="js/SmoothScroll.js"></script>
  <!-- Parallax js -->
  <script src="js/parallaxie.js"></script>
  <!-- MagicCursor js file -->
  <script src="js/gsap.min.js"></script>
  <script src="js/magiccursor.js"></script>
  <!-- Text Effect js file -->
  <script src="js/SplitText.min.js"></script>
  <script src="js/ScrollTrigger.min.js"></script>
  <!-- YTPlayer js File -->
  <script src="js/jquery.mb.YTPlayer.min.js"></script>
  <!-- Wow js file -->
  <script src="js/wow.min.js"></script>
  <!-- Main Custom js file -->
  <script src="js/function.js"></script>





</body>



</html>