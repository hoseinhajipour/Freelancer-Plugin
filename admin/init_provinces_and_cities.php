<?php

function init_default_provinces_and_cities()
{
    /*
    // حذف ترم‌های قبلی از taxonomy استان‌ها و شهرها
    $provinces = get_terms(['taxonomy' => 'province', 'hide_empty' => false]);
    foreach ($provinces as $province) {
        wp_delete_term($province->term_id, 'province');
    }

    $cities = get_terms(['taxonomy' => 'city', 'hide_empty' => false]);
    foreach ($cities as $city) {
        wp_delete_term($city->term_id, 'city');
    }
    */
    // بررسی اینکه آیا استان‌ها قبلاً اضافه شده‌اند یا خیر
    $existing_provinces = get_terms([
        'taxonomy' => 'province',
        'hide_empty' => false,
    ]);

    if (!empty($existing_provinces) && !is_wp_error($existing_provinces)) {
        // اگر استان‌ها قبلاً اضافه شده‌اند، عملیات متوقف می‌شود
        return;
    }

    // آرایه استان‌ها و شهرها
    $iran_provinces_and_cities = [
        'آذربایجان شرقی' => ['تبریز', 'مراغه', 'مرند', 'میانه', 'اهر', 'بناب', 'سراب', 'شبستر', 'اسکو'],
        'آذربایجان غربی' => ['ارومیه', 'خوی', 'مهاباد', 'میاندوآب', 'بوکان', 'سلماس', 'نقده', 'پیرانشهر'],
        'اردبیل' => ['اردبیل', 'مشگین‌شهر', 'خلخال', 'پارس‌آباد', 'گرمی', 'بیله‌سوار', 'نمین', 'نیر'],
        'اصفهان' => ['اصفهان', 'کاشان', 'خمینی‌شهر', 'شاهین‌شهر', 'نجف‌آباد', 'فولادشهر', 'گلپایگان', 'فلاورجان'],
        'البرز' => ['کرج', 'نظرآباد', 'هشتگرد', 'فردیس', 'ماهدشت', 'اشتهارد', 'محمدشهر', 'کوهسار'],
        'ایلام' => ['ایلام', 'دهلران', 'ایوان', 'آبدانان', 'سرابله', 'مهران', 'دره‌شهر', 'بدره'],
        'بوشهر' => ['بوشهر', 'برازجان', 'گناوه', 'دشتستان', 'دشتی', 'کنگان', 'دیلم', 'عسلویه'],
        'تهران' => ['تهران', 'ری', 'اسلامشهر', 'شهریار', 'قدس', 'ملارد', 'دماوند', 'ورامین'],
        'چهارمحال و بختیاری' => ['شهرکرد', 'بروجن', 'فارسان', 'لردگان', 'کوهرنگ', 'اردل', 'سامان', 'کیار'],
        'خراسان جنوبی' => ['بیرجند', 'قائنات', 'نهبندان', 'سربیشه', 'فردوس', 'طبس', 'درمیان', 'بشرویه'],
        'خراسان رضوی' => ['مشهد', 'نیشابور', 'سبزوار', 'تربت‌حیدریه', 'کاشمر', 'گناباد', 'قوچان', 'چناران'],
        'خراسان شمالی' => ['بجنورد', 'شیروان', 'اسفراین', 'جاجرم', 'آشخانه', 'فاروج', 'گرمه'],
        'خوزستان' => ['اهواز', 'آبادان', 'خرمشهر', 'دزفول', 'شوش', 'ماهشهر', 'بندر امام خمینی', 'بهبهان'],
        'زنجان' => ['زنجان', 'ابهر', 'خرمدره', 'قیدار', 'ماهنشان', 'سلطانیه', 'طارم'],
        'سمنان' => ['سمنان', 'شاهرود', 'دامغان', 'گرمسار', 'مهدیشهر', 'آرادان', 'سرخه'],
        'سیستان و بلوچستان' => ['زاهدان', 'زابل', 'ایرانشهر', 'چابهار', 'خاش', 'سراوان', 'نیک‌شهر', 'کنارک'],
        'فارس' => ['شیراز', 'کازرون', 'مرودشت', 'جهرم', 'فسا', 'لار', 'داراب', 'نی‌ریز'],
        'قزوین' => ['قزوین', 'تاکستان', 'آبیک', 'الوند', 'بوئین‌زهرا', 'محمدیه', 'اقبالیه'],
        'قم' => ['قم'],
        'کردستان' => ['سنندج', 'سقز', 'مریوان', 'بانه', 'قروه', 'بیجار', 'کامیاران', 'دهگلان'],
        'کرمان' => ['کرمان', 'رفسنجان', 'جیرفت', 'سیرجان', 'زرند', 'بافت', 'عنبرآباد', 'بردسیر'],
        'کرمانشاه' => ['کرمانشاه', 'اسلام‌آباد غرب', 'هرسین', 'سنقر', 'صحنه', 'جوانرود', 'کنگاور', 'سرپل‌ذهاب'],
        'کهگیلویه و بویراحمد' => ['یاسوج', 'گچساران', 'دهدشت', 'سی‌سخت', 'باشت', 'لیکک'],
        'گلستان' => ['گرگان', 'گنبد کاووس', 'آق‌قلا', 'علی‌آباد کتول', 'کردکوی', 'مینودشت', 'بندر ترکمن', 'کلاله'],
        'گیلان' => ['رشت', 'انزلی', 'لاهیجان', 'آستانه اشرفیه', 'صومعه‌سرا', 'رودسر', 'رودبار', 'فومن'],
        'لرستان' => ['خرم‌آباد', 'بروجرد', 'دورود', 'الیگودرز', 'نورآباد', 'کوهدشت', 'الشتر', 'پلدختر'],
        'مازندران' => ['ساری', 'بابل', 'آمل', 'قائم‌شهر', 'تنکابن', 'نکا', 'چالوس', 'بهشهر'],
        'مرکزی' => ['اراک', 'ساوه', 'خمین', 'محلات', 'دلیجان', 'شازند', 'آشتیان', 'تفرش'],
        'هرمزگان' => ['بندرعباس', 'قشم', 'میناب', 'بندر لنگه', 'کیش', 'حاجی‌آباد', 'رودان', 'جاسک'],
        'همدان' => ['همدان', 'ملایر', 'نهاوند', 'اسدآباد', 'تویسرکان', 'بهار', 'کبودرآهنگ', 'رزن'],
        'یزد' => ['یزد', 'میبد', 'اردکان', 'بافق', 'مهریز', 'ابرکوه', 'اشکذر', 'تفت'],
    ];

    // افزودن استان‌ها و شهرها
    foreach ($iran_provinces_and_cities as $province => $cities) {
        // افزودن استان
        $province_term = wp_insert_term($province, 'province');
        if (!is_wp_error($province_term)) {
            $province_id = $province_term['term_id']; // شناسه استان

            // افزودن شهرها به استان
            foreach ($cities as $city) {
                $city_term = wp_insert_term($city, 'city');
                if (!is_wp_error($city_term)) {
                    $city_id = $city_term['term_id'];

                    // افزودن متادیتای استان به شهر
                    update_term_meta($city_id, 'province_id', $province_id);
                }
            }
        }
    }
}

// هوک فعال‌سازی پلاگین
//add_action('init', 'init_default_provinces_and_cities');

