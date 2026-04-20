/* =====================================
Header 8 Content - Embedded Header HTML
This file contains the header HTML as a JavaScript string
to avoid AJAX loading issues with file:// protocol
======================================*/

var HEADER_4_CONTENT = `
        <style>
            @media only screen and (max-width: 767px) {
                .top-bar { padding: 5px 0; }
                .e-p-bx li { display: block; padding-right: 0; text-align: center; font-size: 11px; margin-bottom: 2px; }
                .social-bx { float: none !important; text-align: center; margin: 5px 0 0 0 !important; }
                .social-bx li { padding: 0 4px; }
                .logo-header { height: 80px !important; width: 200px !important; }
                .logo-header img { max-height: 50px; }
                
                /* Hero Slider Mobile Friendly Fixes */
                .tp-caption[id^="slide-"][id$="-layer-1"] { 
                    font-size: 28px !important; 
                    line-height: 34px !important;
                    width: 90% !important;
                    left: 5% !important;
                    text-align: center !important;
                }
                .tp-caption[id^="slide-"][id$="-layer-2"] { 
                    font-size: 14px !important; 
                    line-height: 20px !important;
                    width: 90% !important;
                    left: 5% !important;
                    text-align: center !important;
                    display: -webkit-box !important;
                    -webkit-line-clamp: 3;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
                .tp-caption[id^="slide-"][id$="-layer-3"] { 
                    left: 50% !important;
                    transform: translateX(-50%) !important;
                }
            }
        </style>
        <!-- HEADER START -->
        <header class="site-header header-style-3 mobile-sider-drawer-menu">
        
            <div class="top-bar bg-secondry">
                <div class="container">
                    <div class="wt-topbar-right">
                        <ul class="list-unstyled e-p-bx">
                            <li><i class="fa fa-map-marker"></i>2750 E W.T. Harris Blvd, Charlotte, NC 28213</li>
                            <li><i class="fa fa-phone"></i>(980) 222-1633</li>
                        </ul>
                        <ul class="social-bx list-inline">
                            <li><a href="https://www.facebook.com/people/Wu-Spa/61581691872363/" class="fa fa-facebook" target="_blank"></a></li>
                            <li><a href="https://www.instagram.com/thewooroom2025/" class="fa fa-instagram" target="_blank"></a></li>
                            <li><a href="https://www.pinterest.com/wuspacharlotte/" class="fa fa-pinterest" target="_blank"></a></li>
                            <li><a href="https://medium.com/@wuspacharlotte" class="fa fa-medium" target="_blank"></a></li>
                            <li><a href="https://www.reddit.com/user/wuspacharlotte/" class="fa fa-reddit" target="_blank"></a></li>
                            <li><a href="https://g.page/r/CZRVraXLBEK_EBM/" class="fa fa-google" target="_blank"></a></li>
                            <li><a href="https://solo.to/wuspa" class="fa fa-link" target="_blank"></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="sticky-header main-bar-wraper">
                <div class="main-bar" style="background-color: #ffffff;">
                    <div class="container">
                        
                        <div class="logo-header mostion">
                            <a href="index.html">
                                <img src="images/logo.png" width="216" height="37" alt="Wu Spa" >
                            </a>
                        </div>

                        <!-- MAIN Nav -->
                        <div class="header-nav navbar-collapse collapse ">
                            <ul class=" nav navbar-nav">
                                <li>
                                    <a href="index.html">Home</a>
                                </li>
                                <li>
                                    <a href="about.html">About us</a>
                                </li>
                                <li class="has-child">
                                    <a href="services.html">Services<i class="fa fa-chevron-down"></i></a>
                                    <ul class="sub-menu">
                                        <li><a href="service-massage-services.html">Massage Services</a></li>
                                        <li><a href="service-facial.html">Facial</a></li>
                                        <li><a href="service-facial-peel.html">Facial Peel</a></li>
                                        <li><a href="service-facial-massage.html">Facial Massage</a></li>
                                        <li><a href="service-spa.html">SPA</a></li>
                                        <li><a href="service-body-rub.html">Body Rub</a></li>
                                        <li><a href="service-skin-care.html">Skin Care</a></li>
                                        <li><a href="service-rejuvenating-facial-rituals.html">Rejuvenating Facial Rituals</a></li>
                                        <li><a href="service-signature-body-treatments.html">Signature Body Treatments</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="gallery-grid-1.html">Gallery</a>
                                </li>
                                <li class="has-child">
                                    <a href="product.html">Shop <i class="fa fa-chevron-down"></i></a>
                                    <ul class="sub-menu">
                                        <li><a href="product.html">All Products</a></li>
                                        <li><a href="shopping-cart.html"><i class="fa fa-shopping-cart" style="margin-right:5px;"></i>View Cart</a></li>
                                    </ul>
                                </li>
                                <li class="has-child">
                                    <a href="javascript:;">Area<i class="fa fa-chevron-down"></i></a>
                                    <ul class="sub-menu">
                                        <li><a href="area-mt-holly-nc.html">Mt Holly, NC</a></li>
                                        <li><a href="area-lake-wylie-nc.html">Lake Wylie, NC</a></li>
                                        <li><a href="area-weddington-nc.html">Weddington, NC</a></li>
                                        <li><a href="area-mint-hill-nc.html">Mint Hill, NC</a></li>
                                        <li><a href="area-allen-nc.html">Allen, North Carolina</a></li>
                                        <li><a href="area-matthews-nc.html">Matthews, NC</a></li>
                                        <li><a href="area-harrisburg-nc.html">Spa in Harrisburg, NC</a></li>
                                        <li><a href="area-steele-creek-charlotte-nc.html">Steele Creek, Charlotte, NC</a></li>
                                        <li><a href="area-belmont-nc.html">Belmont, North Carolina</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="contact-1.html">Contact US</a>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- ETRA Nav -->
                        <div class="extra-nav">
                            <div class="extra-cell">
                                <a href="#search" class="site-search-btn"><i class="fa fa-search"></i></a>
                            </div>
                            <div class="extra-cell">
                                <a href="shopping-cart.html" class="wt-cart cart-btn" title="Your Cart">
                                    <span class="link-inner">
                                        <span class="woo-cart-total"> </span>
                                        <span class="woo-cart-count">
                                            <span class="shopping-bag wcmenucart-count">0</span>
                                        </span>
                                    </span>
                                </a>
                                
                                <div class="cart-dropdown-item-wraper clearfix">
                                    <div class="nav-cart-content">
                                        
                                        <div class="nav-cart-items p-a15">
                                            <div class="nav-cart-item clearfix">
                                                <div class="nav-cart-item-image">
                                                    <a href="#"><img src="images/cart/pic-1.jpg" alt="p-1"></a>
                                                </div>
                                                <div class="nav-cart-item-desc">
                                                    <a href="#">Safety helmet</a>
                                                    <span class="nav-cart-item-price"><strong>2</strong> x $19.99</span>
                                                    <a href="#" class="nav-cart-item-quantity">x</a>
                                                </div>
                                            </div>
                                            <div class="nav-cart-item clearfix">
                                                <div class="nav-cart-item-image">
                                                    <a href="#"><img src="images/cart/pic-2.jpg" alt="p-2"></a>
                                                </div>
                                                <div class="nav-cart-item-desc">
                                                    <a href="#">Hammer drill machine</a>
                                                    <span class="nav-cart-item-price"><strong>1</strong> x $24.99</span>
                                                    <a href="#" class="nav-cart-item-quantity">x</a>
                                                </div>
                                            </div>
                                            <div class="nav-cart-item clearfix">
                                                <div class="nav-cart-item-image">
                                                    <a href="#"><img src="images/cart/pic-3.jpg" alt="p-1"></a>
                                                </div>
                                                <div class="nav-cart-item-desc">
                                                    <a href="#">Safety helmet</a>
                                                    <span class="nav-cart-item-price"><strong>2</strong> x $19.99</span>
                                                    <a href="#" class="nav-cart-item-quantity">x</a>
                                                </div>
                                            </div>
                                            <div class="nav-cart-item clearfix">
                                                <div class="nav-cart-item-image">
                                                    <a href="#"><img src="images/cart/pic-4.jpg" alt="p-2"></a>
                                                </div>
                                                <div class="nav-cart-item-desc">
                                                    <a href="#">Hammer drill machine</a>
                                                    <span class="nav-cart-item-price"><strong>1</strong> x $24.99</span>
                                                    <a href="#" class="nav-cart-item-quantity">x</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="nav-cart-title p-tb10 p-lr15 clearfix">
                                            <h4  class="pull-left m-a0">Subtotal:</h4>
                                            <h5 class="pull-right m-a0">$114.95</h5>
                                        </div>
                                        <div class="nav-cart-action p-a15 clearfix">
                                            <a href="shopping-cart.html" class="site-button btn-block m-b15" style="display:block;text-align:center;"><i class="fa fa-shopping-cart m-r5"></i>View Cart</a>
                                            <a href="checkout.html" class="site-button btn-block" style="display:block;text-align:center;"><i class="fa fa-lock m-r5"></i>Checkout</a>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                        <!-- NAV Toggle Button -->
                        <button id="mobile-side-drawer" data-target=".header-nav" data-toggle="collapse" type="button" class="navbar-toggler collapsed">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar icon-bar-first"></span>
                            <span class="icon-bar icon-bar-two"></span>
                            <span class="icon-bar icon-bar-three"></span>
                        </button> 
                        <!-- SITE Search -->
                        <div id="search"> 
                            <span class="close"></span>
                            <form role="search" id="searchform" action="/search" method="get" class="radius-xl">
                                <div class="input-group">
                                    <input value="" name="q" type="search" placeholder="Type to search">
                                    <span class="input-group-btn"><button type="button" class="search-btn"><i class="fa fa-search"></i></button></span>
                                </div>   
                            </form>
                        </div>
                        
                        
                    </div>
                </div>
            </div>
            
        </header>
        <!--HEADER END-- >
    `;
