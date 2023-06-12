<?php

defined (' IVYSTAVKA ') or die ('Access denied');

session_start ();

// Підключення моделі
require_once MODEL ;

// Підключення бібліотеки функцій
require_once 'functions / functions.php';

// Отримання масиву каталогу
$cat = catalog ();

// Отримання масиву інформерів
$informers = informer ();

// Одержанн масиву сторінок
$pages = pages ();

// Отримання назви новин
$news = get_title_news ();

// Реєстрація
if ($_POST ['reg'] ) {
    registration ();
    redirect ();
}
// Авторизація
if ($_POST ['auth'] ) {
    authorization ();
    if ($_SESSION ['auth'] ['user'] ) {
        // Якщо користувач авторизувався
        echo " <p> Ласкаво просимо , {$_SESSION ['auth'] ['user'] } </ p > " ;
        exit ;
    } else {
        // Якщо авторизація невдала
        echo $_SESSION ['auth'] ['error'] ;
        unset ($_SESSION ['auth'] ) ;
        exit ;
    }
}

// Вихід користувача
if ($_GET ['do'] == 'logout ') {
    logout ();
    redirect ();
}
// Масив метаданих
$meta = array ();

// Отримання динамічною частини шаблону # content
$view = empty ($_GET ['view'] ) ? 'hits ': $_GET ['view'] ;

switch ($view ) {
    case ('hits ') :
        // Лідери 
        $eyestoppers = eyestopper ('hits');
        $meta ['title'] = TITLE ;
        $meta ['description'] = TITLE ;
        $meta ['keywords'] = TITLE . " , Запити ,Самбір " ;
    break ;
    
    case ('new ') :
        // Новини
        $eyestoppers = eyestopper ('new');
        $meta ['title'] = " Новини | " . TITLE ;
        $meta ['description'] = " Новини | " . TITLE ;
    break ;
    
  
    case ('page ') :
        // Окрема сторінка
        $page_id = abs ( ( int ) $_GET ['page_id'] ) ;
        $get_page = get_page ($page_id ) ;
        $meta ['title'] = " {$get_page ['title'] } | " . TITLE ;
        $meta ['description'] = " {$get_page ['description'] } | " . TITLE ;
    break ;
    
    case ('news ') :
        // Окрема новина
        $news_id = abs ( ( int ) $_GET ['news_id'] ) ;
        $news_text = get_news_text ($news_id ) ;
    break ;
    
    case ('archive ') :
        // Всі новини ( архів новин )
        // Параметри для навігації
        $perpage = 2 ; // Кількість документів на сторінку
        if ( isset ($_GET ['page'] )) {
            $page = ( int ) $_GET ['page'] ;
            if ($page < 1 ) $page = 1 ;
        } else {
            $page = 1 ;
        }
        $count_rows = count_news (); // Загальне у новин
        $pages_count = ceil ($count_rows / $perpage ) ; // К -ть сторінок
        if (! $pages_count ) $pages_count = 1 ; // Мінімум 1 сторінка
        if ($page > $pages_count ) $page = $pages_count ; // Якщо запитана сторінка більше максимуму
        $start_pos = ($page - 1 ) * $perpage ; // Початкова позиція для запиту
        
        $all_news = get_all_news ($start_pos , $perpage ) ;
    break ;
    
    case ('informer ') :
        // Текст інформера
        $informer_id = abs ( ( int ) $_GET ['informer_id'] ) ;
        $text_informer = get_text_informer ($informer_id ) ;
    break ;
    
    case ('cat ') :
        // Категорії
        $category = abs ( ( int ) $_GET ['category'] ) ;
        
        /* ===== Сортування ===== */
        // Масив параметрів сортування
        // Ключі - те, що передаємо GET - параметром
        // Значення - те, що показуємо користувачеві і частина SQL -запиту , який передаємо в модель
        $order_p = array (
                        ’ datea '=> array ('за датою додавання - до останніх ', 'date ASC ') ,
                        ’ dated '=> array ('за датою додавання - з останніх ', 'date DESC ') ,
                        ’ namea '=> array ('від А до Я ', 'name ASC ') ,
                        ’ named '=> array ('від Я до А ', 'name DESC ')
                        ) ;
        $order_get = clear ($_GET ['order'] ) ; // Отримуємо можливий параметр сортування
        if ( array_key_exists ($order_get , $order_p )) {
            $order = $order_p [$order_get ] [ 0 ] ;
            $order_db = $order_p [$order_get ] [ 1 ] ;
        } else {
            // За замовчуванням сортування за першому елементу масиву order_p
            $order = $order_p ['namea'] [0];
            $order_db = $order_p ['namea'] [ 1 ] ;
        }
        /* ===== Сортування ===== */
        
        // Параметри для навігації
        $perpage = 3 ; // Кількість на сторінку
        if ( isset ($_GET ['page'] )) {
            $page = ( int ) $_GET ['page'] ;
            if ($page < 1 ) $page = 1 ;
        } else {
            $page = 1 ;
        }
        $count_rows = count_rows ($category ) ; // Загальна
        $pages_count = ceil ($count_rows / $perpage ) ; // К -ть сторінок
        if (! $pages_count ) $pages_count = 1 ; // Мінімум 1 сторінка
        if ($page > $pages_count ) $page = $pages_count ; // Якщо запитана сторінка більше максимуму
        $start_pos = ($page - 1 ) * $perpage ; // Початкова позиція для запиту
        
        $vys_firma_name = vys_firma_name ($category ) ; //
        $vys_posluga = vys_posluga ($category , $order_db , $start_pos , $perpage ) ; // Отримуємо масив з моделі
        $meta ['title'] = $vys_firma_name [ 0 ] ['vys_firma_name'] ;
        if ($vys_firma_name [ 1 ] ) $meta ['title'] . = " - {$vys_firma_name [ 1 ] ['vys_firma_name'] } " ;
        $meta ['title'] . = "|" . TITLE ;
        $meta ['description'] = " {$vys_firma_name [ 0 ] ['vys_firma_name'] } , {$vys_firma_name [ 1 ] ['vys_firma_name'] } " ;
    break ;
    
    case ('addtobloknot ') :
        // Додавання 
        $docs_id = abs ( ( int ) $_GET ['docs_id'] ) ;
        addtobloknot ($docs_id ) ;
        
        $_SESSION ['Total_sum'] = total_sum ($_SESSION ['bloknot'] ) ;
        
        total_quantity ();
        redirect ();
    break ;
    
    case ('bloknot ') :
        // Отримання способів
        $consultation = get_consultation ();
        
        if ( isset ($_GET ['id'] , $_GET ['qty'] )) {
            $docs_id = abs ( ( int ) $_GET ['id'] ) ;
            $qty = abs ( ( int ) $_GET ['qty'] ) ;
            
            $qty = $qty - $_SESSION ['bloknot'] [$docs_id ] ['qty'] ;
            addtobloknot ($docs_id , $qty ) ;
            
          
        }
        // Видалення запиту
        if ( isset ($_GET ['delete'] )) {
            $id = abs ( ( int ) $_GET ['delete'] ) ;
            if ($id ) {
                delete_from_bloknot ($id ) ;
            }
            redirect ();
        }
        
        if ($_POST ['order_x'] ) {
            add_order ();
            redirect ();
        }
    break ;
    
    case ('reg ') :
        // Реєстрація
    break ;
    
    case ('search ') :
        // Пошук
        $result_search = search ();
        
        // Параметри для навігації
        $perpage = 9 ; // Кількість на сторінку
        if ( isset ($_GET ['page'] )) {
            $page = ( int ) $_GET ['page'] ;
            if ($page < 1 ) $page = 1 ;
        } else {
            $page = 1 ;
        }
        $count_rows = count ($result_search ) ; // Загальне 
        $pages_count = ceil ($count_rows / $perpage ) ; // К -ть сторінок
        if (! $pages_count ) $pages_count = 1 ; // Мінімум 1 сторінка
        if ($page > $pages_count ) $page = $pages_count ; // Якщо запитана сторінка більше максимуму
        $start_pos = ($page - 1 ) * $perpage ; // Початкова позиція для запиту
        $endpos = $start_pos + $perpage ; // До якого запиту буде висновок на сторінці
        if ($endpos > $count_rows ) $endpos = $count_rows ;
    break ;
    
    case ('filter ') :
        // Вибір за параметрами
        $startquality = ( int ) $_GET ['startquality'] ;
        $endquality = ( int ) $_GET ['endquality'] ;
        $vys_firma = array ();
        
        if ($_GET ['vys_firma'] ) {
            foreach ($_GET ['vys_firma'] as $value ) {
                $value = ( int ) $value ;
                $vys_firma [$value ] = $value ;
            }
        }
        if ($vys_firma ) {
            $category = implode (', ', $vys_firma ) ;
        }
        $vys_posluga = filter ($category , $startquality , $endquality ) ;
    break ;
    
    case ('doc ') :
        // Окремий вид
        $docs_id = abs ( ( int ) $_GET ['docs_id'] ) ;
        if ($docs_id ) {
            $docs = get_docs ($docs_id ) ;
            if ($docs ) $vys_firma_name = vys_firma_name ($docs ['docs_vys_firmaid'] ) ; //        }
    break ;
  
    default :
        // Якщо з адресного рядка отримано ім’я неіснуючого виду
        $view = 'hits';
        $eyestoppers = eyestopper ('hits');
}

// Підключення виду
require_once $_SERVER ['DOCUMENT_ROOT'] . '/ views / ivystavka / index.php'; // Http://doc.com.uaviews/ivystavka/index.php
