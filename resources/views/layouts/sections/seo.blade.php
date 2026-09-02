@php
    $seo = [
        'title' => config('app.name')  . ' | The #1 Website to make money online',
        'description' => 'Complete tasks, play games or take online surveys for money. Want to know how to get free Robux, or how to get free V Bucks. Join ' . config('app.name') . ' today.',
        'thumbnail' => asset('assets/img/thumbnail.jpg'),
    ]
@endphp

<script type='application/ld+json'>
    {
        "@context": "http://www.schema.org",
        "@type": "WebSite",
        "name": "{{ config('app.name') }}",
            "alternateName": "coupons, cash back, cash app, Paid Online Surveys, Free Gift Cards, promo codes, offers, discounts, deals, coupon codes, timebucks, swagbucks, earn by click, paid by click, referral earn money online, earn money watching videos, click to pay earn money, make money signing up, easy earn, withdraw paypal, withdraw bitcoin, fast pay, easy offers, free game card, freefire gift, pubg gift, free gift card, fast earn, minimun withdraw, payperclick, surveys, make money online, CPA, CPL, CPV, ways to earn money, earn cash online,take surveys and make money,free online survey jobs,money surveys,earn survey,best online survey sites,best surveys to earn money,take surveys for money,get paid surveys,paid surveys scams,get paid for surveys,take surveys for cash,take surveys,survey sites to make money,cash for surveys,online survey for money,paid survey online,get paid for online surveys,paid to take surveys,best online surveys to make money,online survey rewards,best surveys for money,pay me for surveys,the best online surveys for money,which online surveys pay the most,top survey sites to earn money",
            "url": "{{ config('app.url') }}"
        }
</script>

<link rel="alternate" hreflang="en" href="{{ config('app.url') }}">
<link rel="canonical" href="{{ config('app.url') }}">
<meta name="robots" content="index, follow">
<meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="keywords"
      content="coupons, cash back, cash app, Paid Online Surveys, Free Gift Cards, promo codes, offers, discounts, deals, coupon codes, timebucks, swagbucks, earn by click, paid by click, referral earn money online, earn money watching videos, click to pay earn money, make money signing up, easy earn, withdraw paypal, withdraw bitcoin, fast pay, easy offers, free game card, freefire gift, pubg gift, free gift card, fast earn, minimun withdraw, payperclick, surveys, make money online, CPA, CPL, CPV, ways to earn money, earn cash online,take surveys and make money,free online survey jobs,money surveys,earn survey,best online survey sites,best surveys to earn money,take surveys for money,get paid surveys,paid surveys scams,get paid for surveys,take surveys for cash,take surveys,survey sites to make money,cash for surveys,online survey for money,paid survey online,get paid for online surveys,paid to take surveys,best online surveys to make money,online survey rewards,best surveys for money,pay me for surveys,the best online surveys for money,which online surveys pay the most,top survey sites to earn money">
<meta name="description" content="{{ $seo['description'] }}">
<meta property="og:locale" content="en_US">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $seo['title'] }}">
<meta property="og:description" content="{{ $seo['description'] }}">
<meta property="og:url" content="{{ config('app.url') }}">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:creator" content="{{ '@' . config('app.name') }}">
<meta name="twitter:site" content="{{'@' . config('app.name') }}">
<meta property="og:image" content="{{ $seo['thumbnail'] }}">
<meta property="og:image:width" content="1500">
<meta property="og:image:height" content="500">
<meta property="og:image:type" content="image/png">
