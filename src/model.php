<?php

defined (' IVYSTAVKA ' ) or die (' Access denied ');

/* ==== Каталог - отримання масиву === */
function catalog () {
    $query = " SELECT * FROM vys_firmas ORDER BY parent_id , vys_firma_name " ;
    $res = mysql_query ($query ) or die ( mysql_query ( )) ;
    
    // масив категорій
    $cat = array ();
    while ($row = mysql_fetch_assoc ($res )) {
        if (! $row [' parent_id '] ) {
            $cat [$row [' vys_firma_id '] ] [ ] = $row [' vys_firma_name '] ;
        } else {
            $cat [$row [' parent_id '] ] [' sub '] [$row [' vys_firma_id '] ] = $row [' vys_firma_name '] ;
        }
    }
    return $cat ;
}
/* ==== Каталог - отримання масиву === */

/* === Сторінки === */
function pages () {
    $query = " SELECT page_id , title FROM pages ORDER BY position " ;
    $res = mysql_query ($query ) ;
    
    $pages = array ();
    while ($row = mysql_fetch_assoc ($res )) {
        $pages [] = $row ;
    }
    return $pages ;
}
/* === Сторінки === */

/* === Окрема сторінка === */
function get_page ($page_id ) {
    $query = " SELECT title , keywords , description , text FROM pages WHERE page_id = $page_id " ;
    $res = mysql_query ($query ) ;
    
    $get_page = array ();
    $get_page = mysql_fetch_assoc ($res ) ;
    return $get_page ;
}
/* === Окрема сторінка === */

/* === Назви новин === */
function get_title_news () {
    $query = " SELECT news_id , title , date FROM news ORDER BY date DESC LIMIT 2";
    $res = mysql_query ($query ) ;
    
    $news = array ();
    while ($row = mysql_fetch_assoc ($res )) {
        $news [] = $row ;
    }
    return $news ;
}
/* === Назви новин === */

/* === Окрема новина === */
function get_news_text ($news_id ) {
    $query = " SELECT title , text , date FROM news WHERE news_id = $news_id " ;
    $res = mysql_query ($query ) ;
    
    $news_text = array ();
    $news_text = mysql_fetch_assoc ($res ) ;
    return $news_text ;
}
/* === Окрема новина === */

/* === Архів новин === */
function get_all_news ($start_pos , $perpage ) {
    $query = " SELECT news_id , title , anons , date FROM news ORDER BY date DESC LIMIT $start_pos , $perpage " ;
    $res = mysql_query ($query ) ;
    
    $all_news = array ();
    while ($row = mysql_fetch_assoc ($res )) {
        $all_news [] = $row ;
    }
    return $all_news ;
}
/* === Архів новин === */

/* === Кількість новин === */
function count_news () {
    $query = " SELECT COUNT ( news_id ) FROM news " ;
    $res = mysql_query ($query ) ;
    
    $count_news = mysql_fetch_row ($res ) ;
    return $count_news [0];
}
/* === Кількість новин === */

/* === Інформери - отримання масиву === */
function informer () {
    $query = " SELECT * FROM links
                INNER JOIN informers ON
                    links.parent_informer = informers.informer_id
                        ORDER BY informer_position , links_position " ;
    $res = mysql_query ($query ) or die ( mysql_query ( )) ;
    
    $informers = array ();
    $name = '' ; // Прапор імені інформера
    while ($row = mysql_fetch_assoc ($res )) {
        if ($row [' informer_name '] != $name ) { // якщо такого інформера в масиві ще немає
            $informers [$row [' informer_id '] ] [ ] = $row [' informer_name '] ; // Додаємо інформер в масив
            $name = $row [' informer_name '] ;
        }
        $informers [$row [' parent_informer '] ] [' sub '] [$row [' link_id '] ] = $row [' link_name '] ; // Заносимо сторінки в інформер
    }
    return $informers ;
}
/* === Інформери - отримання масиву === */

/* === Отримання тексту інформера === */
function get_text_informer ($informer_id ) {
    $query = " SELECT link_id , link_name , text , informers.informer_id , informers.informer_name
                FROM links
                    LEFT JOIN informers ON informers.informer_id = links.parent_informer
                        WHERE link_id = $informer_id " ;
    $res = mysql_query ($query ) ;
    
    $text_informer = array ();
    $text_informer = mysql_fetch_assoc ($res ) ;
    return $text_informer ;
}
/* === Отримання тексту інформера === */
function get_text_informer2 ($informer_id ) {
    $query = " SELECT vys_firma_id , vys_firma_name FROM vys_firmas WHERE vys_firma_id = $category ";
    $res = mysql_query ($query ) ;
    $vys_firma_name = array ();
    while ($row = mysql_fetch_assoc ($res )) {
        $vys_firma_name [] = $row ;
    }
    return $vys_firma_name ;
}
/* === Отримання назв === */

/* === Вибір за параметрами === */
function filter ($category , $startquality , $endquality ) {
    $vys_posluga = array ();
    if ($category OR $endquality ) {
        $predicat1 = " visible = '1 '" ;
        if ($category ) {
            $predicat1 . = " AND docs_vys_firmaid IN ($category )";
            $predicat2 = " UNION
                        ( SELECT docs_id , name , img , quality , hits , new , sale
                        FROM docs
                            WHERE docs_vys_firmaid IN
                            (
                                SELECT vys_firma_id FROM vys_firmas WHERE parent_id IN ($category )
                            ) AND visible = '1 '" ;
            if ($endquality ) $predicat2 . = " AND quality BETWEEN $startquality AND $endquality " ;
            $predicat2 . = " )";
        }
        if ($endquality ) {
            $predicat1 . = " AND quality BETWEEN $startquality AND $endquality " ;
        }
        
        $query = " ( SELECT docs_id , name , img , quality , hits , new , sale
                    FROM docs
                        WHERE $predicat1 )
                         $predicat2 ORDER BY name " ;
        $res = mysql_query ($query ) or die ( mysql_error ( )) ;
        if ( mysql_num_rows ($res )> 0 ) {
            while ($row = mysql_fetch_assoc ($res )) {
                $vys_posluga [] = $row ;
            }
        } else {
            $vys_posluga [' notfound '] = " <div class='error’> За вказаними параметрами нічого не знайдено </ div > " ;
        }
    } else {
        $vys_posluga [' notfound '] = " <div class='error’> Ви не вказали параметри підбору </ div > " ;
    }
    return $vys_posluga ;
}
function total_sum ($docs ) {
    $total_sum = 0 ;
    
    $str_docs = implode (' , ' , array_keys ($docs )) ;
    
    $query = " SELECT docs_id , name , quality , img
                FROM docs
                    WHERE docs_id IN ($str_docs ) ";
    $res = mysql_query ($query ) or die ( mysql_error ( )) ;
    
    while ($row = mysql_fetch_assoc ($res )) {
        $_SESSION [' Bloknot '] [$row [' docs_id '] ] [' name '] = $row [' name '] ;
        $_SESSION [' Bloknot '] [$row [' docs_id '] ] [' quality '] = $row [' quality '] ;
        $_SESSION [' Bloknot '] [$row [' docs_id '] ] [' img '] = $row [' img '] ;
        $total_sum + = $_SESSION [' bloknot '] [$row [' docs_id '] ] [' qty '] * $row [' quality '] ;
    }
    return $total_sum ;
}
function registration () {
    $error = '' ; / / Прапор перевірки порожніх полів
    
    $login = trim ($_POST [' login '] ) ;
    $pass = trim ($_POST [' pass '] ) ;
    $name = trim ($_POST [' name '] ) ;
    $email = trim ($_POST [' email '] ) ;
    $phone = trim ($_POST [' phone '] ) ;
    $address = trim ($_POST [' address '] ) ;
    
    if ( empty ($login ) ) $error . = ' <li> Не вказаний логін </ li >' ;
    if ( empty ($pass ) ) $error . = ' <li> Не вказаний пароль </ li >' ;
    if ( empty ($name ) ) $error . = ' <li> Не вказано ПІБ </ li >' ;
    if ( empty ($email ) ) $error . = ' <li> Не вказаний Email </ li >' ;
    if ( empty ($phone ) ) $error . = ' <li> Не вказаний телефон </ li >' ;
    if ( empty ($address ) ) $error . = ' <li> Не вказано адреси </ li >' ;
    
    if ( empty ($error )) {
        / / Якщо всі поля заповнені
        / / Перевіряємо чи немає такого користувача в БД
        $query = " SELECT customer_id FROM customers WHERE login = '" . clear ($login ) . " ' LIMIT 1 " ;
        $res = mysql_query ($query ) or die ( mysql_error ( )) ;
        $row = mysql_num_rows ($res ) ; / / 1 - такий юзер є, 0 - немає
        if ($row ) {
            / / Якщо такий логін вже є
            $_SESSION [' Reg '] [' res '] = " <div class='error’> Користувач з таким логіном вже зареєстрований на сайті. Введіть інший логін . </ Div > " ;
            $_SESSION [' Reg '] [' name '] = $name ;
            $_SESSION [' Reg '] [' email '] = $email ;
            $_SESSION [' Reg '] [' phone '] = $phone ;
            $_SESSION [' Reg '] [' addres '] = $address ;
        } else {
            / / Якщо все ок - реєструємо
            $login = clear ($login ) ;
            $name = clear ($name ) ;
            $email = clear ($email ) ;
            $phone = clear ($phone ) ;
            $address = clear ($address ) ;
            $pass = md5 ($pass ) ;
            $query = " INSERT INTO customers ( name , email , phone , address , login , password )
                        VALUES ( '$name ' , '$email ' , '$phone ' , '$address ' , '$login ' , '$pass ') " ;
            $res = mysql_query ($query ) or die ( mysql_error ( )) ;
            if ( mysql_affected_rows ( )> 0 ) {
                / / Якщо запис додана
                $_SESSION [' Reg '] [' res '] = " <div class='success’> Реєстрація пройшла успішно. </ Div > " ;
                $_SESSION [' Auth '] [' user '] = $_POST [' name '] ;
                $_SESSION [' Auth '] [' customer_id '] = mysql_insert_id ();
                $_SESSION [' Auth '] [' email '] = $email ;
            } else {
                $_SESSION [' Reg '] [' res '] = " <div class='error’> Помилка ! </ Div > " ;
                $_SESSION [' Reg '] [' login '] = $login ;
                $_SESSION [' Reg '] [' name '] = $name ;
                $_SESSION [' Reg '] [' email '] = $email ;
                $_SESSION [' Reg '] [' phone '] = $phone ;
                $_SESSION [' Reg '] [' addres '] = $address ;
            }
        }
    } else {
        / / Якщо не заповнені обов’язкові поля
        $_SESSION [' Reg '] [' res '] = " <div class='error’> Чи не заповнені обов’язкові поля: <ul> $error </ ul > </ div > " ;
        $_SESSION [' Reg '] [' login '] = $login ;
        $_SESSION [' Reg '] [' name '] = $name ;
        $_SESSION [' Reg '] [' email '] = $email ;
        $_SESSION [' Reg '] [' phone '] = $phone ;
        $_SESSION [' Reg '] [' addres '] = $address ;
    }
}
/* === Реєстрація === */

/* === Авторизація === */
function authorization () {
    $login = mysql_real_escape_string ( trim ($_POST [' login '] )) ;
    $pass = trim ($_POST [' pass '] ) ;
    
    if ( empty ($login ) OR empty ($pass )) {
        / / Якщо порожні поля логін / пароль
        $_SESSION [' Auth '] [' error '] = " Поля логін / пароль повинні бути заповнені ! " ;
    } else {
        / / Якщо отримані дані з полів логін / пароль
        $pass = md5 ($pass ) ;
        
        $query = " SELECT customer_id , name , email FROM customers WHERE login = '$login ' AND password = '$pass ' LIMIT 1 " ;
        $res = mysql_query ($query ) or die ( mysql_error ( )) ;
        if ( mysql_num_rows ($res ) == 1 ) {
            / / Якщо авторизація успішна
            $row = mysql_fetch_row ($res ) ;
            $_SESSION [' Auth '] [' customer_id '] = $row [0];
            $_SESSION [' Auth '] [' user '] = $row [ 1 ] ;
            $_SESSION [' Auth '] [' email '] = $row [ 2 ] ;
        } else {
            / / Якщо невірний логін / пароль
            $_SESSION [' Auth '] [' error '] = " Логін / пароль введені невірно ! " ;
        }
    }
}
/* === Авторизація === */
