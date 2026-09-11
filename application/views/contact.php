<?php
include('includes/header-link.php');
include('includes/header.php');
?>

<style>
        #charCount {
            font-size: 0.9em;
            color: gray;
        }
        #charCount.warning {
            color: red;
        }
        .formMsg{
            position: static !important;
            transform: inherit !important;
        }
    </style>
<div class="breadcrumb-block style-img">
    <div class="breadcrumb-main bg-linear overflow-hidden">
        <div class="container lg:pt-[60px] pt-5 pb-5 relative">
            <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                <div class="text-content">
                    <div class="heading2 text-center">Contact</div>
                    <div class="link flex items-center justify-center gap-1 caption1 mt-2">
                        <a href="<?= base_url() ?>">Homepage</a>
                        <i class="ph ph-caret-right text-sm text-secondary2"></i>
                        <div class="text-secondary2 capitalize">Contact</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="contact-us md:py-20 py-10">
    <div class="container">
        <div class="flex justify-between max-lg:flex-col gap-y-10">
            <div class="left lg:w-2/3 lg:pr-4">
                <div class="heading3">Drop Us A Line</div>
                <div class="body1 text-secondary2 mt-3">Use the form below to get in touch with the sales team</div>
                <form class="md:mt-6 mt-4" id="contact-form" method="POST">
                <div class="formMsg"></div>
                    <div class="grid sm:grid-cols-2 grid-cols-1 gap-4 gap-y-5">
                        <div class="name">
                            <input class="border-line px-4 py-3 w-full rounded-lg" id="name" name="name" type="text" placeholder="Your Name *" required />
                        </div>
                        <div class="number">
                            <input class="border-line px-4 pt-3 pb-3 w-full rounded-lg" id="phone" name="phone" type="tel" placeholder="Your Phone Number *" required />
                        </div>
                        <div class="email sm:col-span-2">
                            <input class="border-line px-4 pt-3 pb-3 w-full rounded-lg" id="email" name="email" type="email" placeholder="Your Email *" required />
                        </div>
                        <div class="message sm:col-span-2">
                            <textarea class="border-line px-4 pt-3 pb-3 w-full rounded-lg" id="message" name="message" rows="3" placeholder="Your Message *" required></textarea>
                            <div id="charCount">0/500 characters</div>
                        </div>
                    </div>
                    <div class="block-button md:mt-6 mt-4">
                        <button class="button-main">Send message</button>
                    </div>
                </form>
            </div>
            <div class="right lg:w-1/4 lg:pl-4">
                <div class="item">
                    <div class="heading4">Our Store</div>
                    <p class="mt-3">Address: <span class=""><?= $this->companyAddress ?></span></p>
                    <p class="mt-3">Phone: <span class="whitespace-nowrap"><?= $this->companyContact1 ?></span></p>
                    <p class="mt-1">Email: <span class="whitespace-nowrap"><?= $this->companyEmail ?></span></p>
                </div>
                <div class="item mt-10">
                    <div class="heading4">Open Hours</div>
                    <p class="mt-3">Monday - Sunday: <span class="whitespace-nowrap">9:00am - 6:00pm IST</span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="map xl:h-[600px] sm:h-[500px] h-[450px] overflow-hidden">
    <iframe class="w-full h-full" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4717.740814334277!2d78.7974213761876!3d23.862426884464693!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3978d13535ca41e1%3A0x2653492151c174f3!2sAVANTIKA%20HASTKRITI!5e1!3m2!1sen!2sin!4v1759823936977!5m2!1sen!2sin" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>

<?php
include('includes/footer.php');
include('includes/footer-link.php');
?>