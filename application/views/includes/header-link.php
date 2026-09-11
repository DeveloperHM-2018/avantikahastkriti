<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    <meta name="google-site-verification" content="xwXO0O_13v9dUYv-Gri4r84L7HesIH23V0KaNkEon-I" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Meta Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1485414235840295');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=1485414235840295&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
    <title><?= isset($metaTitle) && $metaTitle != '' ? $metaTitle : (isset($title) ? $title . ' - ' . $this->projectName : $this->projectName) ?></title>
    <meta name="description" content="<?= !empty($metaDescription) ? $metaDescription : 'Shop trendy ethnic wear for women online at ' . $this->projectName . ' - sarees, kurtas, and lehengas suited for weddings, festivals and more.' ?>">
    <meta name="keywords" content="<?= !empty($metaKeywords) ? $metaKeywords : 'ethnic wear, ethnic wear for women, traditional indian wear, sarees, kurtas, lehengas' ?>">
    <meta name="author" content="<?= $this->projectName ?>">
    <link rel="canonical" href="<?= current_url() ?>">
    <meta name="base-url" content="<?= base_url() ?>">
    <link rel="shortcut icon" href="<?= base_url() ?>assets/img/logo.jpg" type="image/x-icon" />

    <link rel="stylesheet" href="<?= base_url() ?>assets/css/swiper-bundle.min.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/style.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/dist/output-scss.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/dist/output-tailwind.css" />

    <?php if (isset($customStyles)):
        foreach ($customStyles as $style):
            echo '<link rel="stylesheet" href="' . base_url($style) . '" />';
        endforeach;
    endif; ?>


    <!-- Facebook Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= isset($title) ? $title : 'Ecommerce' ?>">
    <meta property="og:description" content="">
    <meta property="og:url" content="<?= base_url() ?>">
    <meta property="og:image" content="<?= base_url() ?>assets/img/logo.jpg">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= isset($title) ? $title : 'Ecommerce' ?>">
    <meta name="twitter:description" content="">
    <meta name="twitter:image" content="<?= base_url() ?>assets/img/logo.jpg">
    <meta name="twitter:site" content="#">

    <!-- LinkedIn Cards -->
    <meta property="og:type" content="Website">
    <meta property="og:title" content="<?= isset($title) ? $title : '' ?>">
    <meta property="og:description" content="">
    <meta property="og:image" content="<?= base_url() ?>assets/img/logo.jpg">
    <meta property="og:url" content="<?= base_url() ?>">
    <meta property="og:site_name" content="<?= $this->projectName ?>">

</head>

<body>