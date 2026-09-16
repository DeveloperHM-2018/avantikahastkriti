<div id="footer" class="footer">
    <div class="footer-main">
        <div class="container">
            <div class="content-footer md:py-[60px] py-10 flex justify-between flex-wrap gap-y-8">
                <div class="company-infor basis-1/4 max-lg:basis-full pr-7">
                    <a href="<?= base_url() ?>" class="logo inline-block">
                        <div class="heading3 w-fit">
                            <img src="<?= base_url() ?>assets/img/logo.jpg" alt="<?= $this->projectName ?>" width="100px">
                        </div>
                    </a>
                    <div class="">
                        <div class="text-button-uppercase">Stay Connected With Us:</div>
                        <div class="caption1 mt-3">Follow Us to Stay Updated on News, Updates, and Insights Across Social Media Platforms</div>
                        <div class="list-social flex items-center gap-6 mt-4">
                            <?php if (!empty($this->socialFacebookUrl)): ?>
                                <a href="<?= $this->socialFacebookUrl ?>" target="_blank" rel="noopener noreferrer">
                                    <div class="icon-facebook text-2xl"></div>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($this->socialInstagramUrl)): ?>
                                <a href="<?= $this->socialInstagramUrl ?>" target="_blank" rel="noopener noreferrer">
                                    <div class="icon-instagram text-2xl"></div>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="right-content flex flex-wrap gap-y-8 basis-3/4 max-lg:basis-full">
                    <div class="list-nav flex justify-between basis-2/3 max-md:basis-full gap-4">
                        <div class="item flex flex-col basis-1/3">
                            <div class="text-button-uppercase pb-3">Information</div>
                            <a class="caption1 has-line-before duration-300 w-fit" href="<?= base_url('about') ?>">About Us </a>
                            <a class="caption1 has-line-before duration-300 w-fit pt-2" href="<?= base_url('contact') ?>">Contact us </a>
                            <a class="caption1 has-line-before duration-300 w-fit pt-2" href="<?= base_url('profile') ?>"> My Account</a>
                            <a class="caption1 has-line-before duration-300 w-fit pt-2" href="<?= base_url('profile#orders') ?>"> Order & Returns</a>
                            <a class="caption1 has-line-before duration-300 w-fit pt-2" href="<?= base_url('vendor/register') ?>">Become a Vendor</a>
                            <a class="caption1 has-line-before duration-300 w-fit pt-2" href="<?= base_url('vendor/login') ?>">Vendor Login</a>
                        </div>
                        <div class="item flex flex-col basis-1/3 footer-quick-menus">
                            <div class="text-button-uppercase pb-3">Quick Shop</div>
                            <?php
                            $footerCategory = $this->CommonModel->getRowByOrderWithLimit('category', ['is_delete' => '1'],  'category_id', 'DESC', '6');
                            foreach ($footerCategory as $cate) {
                            ?>
                                <a class="caption1 has-line-before duration-300 w-fit" href="<?= BASE_URL . 'products/' . url_title($cate['category_name'], '-', true) ?>"><?= $cate['category_name'] ?></a>
                            <?php
                            }
                            ?>
                        </div>
                        <div class="item flex flex-col basis-1/3">
                            <div class="text-button-uppercase pb-3">Customer Services</div>
                            <a class="caption1 has-line-before duration-300 w-fit" href="<?= base_url('faqs') ?>">FAQs </a>
                            <a class="caption1 has-line-before duration-300 w-fit pt-2" href="<?= base_url('policy/terms-and-condition') ?>">Terms & Condition</a>
                            <a class="caption1 has-line-before duration-300 w-fit pt-2" href="<?= base_url('policy/privacy') ?>">Privacy Policy</a>
                            <a class="caption1 has-line-before duration-300 w-fit pt-2" href="<?= base_url('policy/return-and-refund') ?>">Return & Refund</a>
                            <a class="caption1 has-line-before duration-300 w-fit pt-2" href="<?= base_url('policy/shipping-policy') ?>">Shipping Policy</a>
                        </div>
                    </div>

                    <div class="newsletter basis-1/3 pl-7 max-md:basis-full max-md:pl-0">
                        <div class="text-button-uppercase pb-3">Contact</div>
                        <div class="flex gap-3">
                            <div class="flex flex-col">
                                <span class="items-baseline gap-2"><span class="text-button block" style="min-width: 66px;">Email:</span><?= $this->companyEmail ?></span>
                                <span class="flex items-baseline gap-2 mt-[14px]"><span class="text-button block" style="min-width: 66px;">Phone:</span><?= $this->companyContact1 ?></span>
                                <span class="flex items-baseline gap-2 mt-2 pt-1"><span class="text-button block" style="min-width: 66px;">Address:</span><?= $this->companyAddress ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom py-3 flex items-center justify-between gap-5 max-lg:justify-center max-lg:flex-col border-t border-line">
                <div class="left flex items-center gap-8">
                    <div class="copyright caption1">© <?= date('Y') . '<span class=" font-bold"> ' . $this->projectName . '.</span>' ?> All Rights Reserved.</div>
                </div>
                <div class="right flex items-center gap-2">
                    <div class="caption1">Payment:</div>
                    <div class="payment-img">
                        <img src="<?= base_url() ?>assets/images/payment/Frame-0.png" alt="payment" class="w-9" />
                    </div>
                    <div class="payment-img">
                        <img src="<?= base_url() ?>assets/images/payment/Frame-1.png" alt="payment" class="w-9" />
                    </div>
                    <div class="payment-img">
                        <img src="<?= base_url() ?>assets/images/payment/Frame-2.png" alt="payment" class="w-9" />
                    </div>
                    <div class="payment-img">
                        <img src="<?= base_url() ?>assets/images/payment/Frame-3.png" alt="payment" class="w-9" />
                    </div>
                    <div class="payment-img">
                        <img src="<?= base_url() ?>assets/images/payment/Frame-4.png" alt="payment" class="w-9" />
                    </div>
                    <div class="payment-img">
                        <img src="<?= base_url() ?>assets/images/payment/Frame-5.png" alt="payment" class="w-9" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Floating WhatsApp Button */
    .whatsapp-float {
        position: fixed;
        bottom: 100px;
        right: 25px;
        width: 62px;
        height: 62px;
        background: linear-gradient(135deg, #25d366, #1ebe5d);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;

        /* Effects */
        box-shadow:
            0 8px 25px rgba(37, 211, 102, 0.45),
            0 0 0 0 rgba(37, 211, 102, 0.6);

        /* Animation */
        animation: whatsappPulse 2s infinite;

        /* Smooth */
        transition:
            transform 0.3s ease,
            box-shadow 0.3s ease,
            background 0.3s ease;
    }

    /* SVG icon size */
    .whatsapp-float svg {
        width: 30px;
        height: 30px;
    }

    /* Hover Effects */
    .whatsapp-float:hover {
        transform: translateY(-5px) scale(1.08);
        background: linear-gradient(135deg, #20c45a, #17a74d);

        box-shadow:
            0 12px 30px rgba(37, 211, 102, 0.55),
            0 0 25px rgba(37, 211, 102, 0.4);
    }

    /* Pulse Animation */
    @keyframes whatsappPulse {
        0% {
            box-shadow:
                0 8px 25px rgba(37, 211, 102, 0.45),
                0 0 0 0 rgba(37, 211, 102, 0.5);
        }

        70% {
            box-shadow:
                0 8px 25px rgba(37, 211, 102, 0.45),
                0 0 0 18px rgba(37, 211, 102, 0);
        }

        100% {
            box-shadow:
                0 8px 25px rgba(37, 211, 102, 0.45),
                0 0 0 0 rgba(37, 211, 102, 0);
        }
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .whatsapp-float {
            width: 56px;
            height: 56px;
            bottom: 20px;
            right: 15px;
        }

        .whatsapp-float svg {
            width: 26px;
            height: 26px;
        }
    }
</style>

<!-- WhatsApp Floating Button -->
<a
    href="https://wa.me/<?= preg_replace('/\D/', '', $this->companyContact1) ?>"
    target="_blank"
    rel="noopener noreferrer"
    class="whatsapp-float"
>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 32 32"
        fill="white"
        class="w-7 h-7"
    >
        <path
            d="M16.001 3C8.821 3 3 8.821 3 16.001c0 2.823.902 5.438 2.438 7.57L3 29l5.596-2.387A12.94 12.94 0 0 0 16.001 29C23.18 29 29 23.179 29 16.001 29 8.821 23.18 3 16.001 3zm0 23.667a10.6 10.6 0 0 1-5.403-1.48l-.387-.23-3.32 1.416.707-3.434-.25-.352a10.61 10.61 0 1 1 8.653 4.08zm5.816-7.96c-.318-.16-1.88-.928-2.17-1.034-.29-.106-.5-.16-.71.16-.212.318-.818 1.034-1.002 1.246-.185.212-.37.238-.688.08-.318-.16-1.344-.494-2.56-1.576-.946-.842-1.584-1.882-1.77-2.2-.184-.318-.02-.49.14-.65.145-.145.318-.37.476-.556.16-.186.212-.318.318-.53.106-.212.054-.398-.026-.558-.08-.16-.71-1.71-.974-2.34-.258-.62-.52-.536-.71-.546l-.606-.01c-.212 0-.556.08-.846.398-.29.318-1.108 1.082-1.108 2.638s1.134 3.06 1.292 3.272c.16.212 2.23 3.404 5.402 4.772.756.326 1.346.52 1.806.666.758.24 1.448.206 1.994.126.608-.09 1.88-.768 2.144-1.51.266-.742.266-1.378.186-1.51-.08-.132-.29-.212-.608-.37z"
        />
    </svg>
</a>