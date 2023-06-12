<?php

defined (' IVYSTAVKA ' ) or die (' Access denied ');

/* === Роздруківка масиву === */
function print_arr ($arr ) {
    echo " <pre> " ;
    print_r ($arr ) ;
    echo " </ pre > " ;
}
/* === Роздруківка масиву === */

/* === Фільтрація вхідних даних === */
function clear ($var ) {
    $var = mysql_real_escape_string ( strip_tags ($var )) ;
    return $var ;
}
/* === Фільтрація вхідних даних === */

/* === Редірект === */
function redirect ($http = false ) {
    if ($http ) $redirect = $http ;
        else $redirect = isset ($_SERVER [' HTTP_REFERER '] ) ? $_SERVER [' HTTP_REFERER '] : PATH ;
    header ( "Location : $redirect " ) ;
    exit ;
}
/* === Редірект === */

/* === Вихід користувача === */
function logout () {
    unset ($_SESSION [' auth '] ) ;
}
/* === Вихід користувача === */

/* === Додавання запиту === */
function addtobloknot ($docs_id , $qty = 1 ) {
    if ( isset ($_SESSION [' bloknot '] [$docs_id ])) {
        // Якщо в масиві bloknot вже є запит
        $_SESSION [' Bloknot '] [$docs_id ] [' qty '] += $qty ;
        return $_SESSION [' bloknot '] ;
    } else {
        $_SESSION [' Bloknot '] [$docs_id ] [' qty '] = $qty ;
        return $_SESSION [' bloknot '] ;
    }
}
function delete_from_bloknot ($id ) {
    if ($_SESSION [' bloknot '] ) {
        if ( array_key_exists ($id , $_SESSION [' bloknot '] )) {
            $_SESSION [' Total_quantity '] -= $_SESSION [' bloknot '] [$id ] [' qty '] ;
            $_SESSION [' Total_sum '] -= $_SESSION [' bloknot '] [$id ] [' qty '] * $_SESSION [' bloknot '] [$id ] [' quality '] ;
            unset ($_SESSION [' bloknot '] [$id ] ) ;
        }
    }
}
function total_quantity () {
    $_SESSION [' Total_quantity '] = 0 ;
    foreach ($_SESSION [' bloknot '] as $key => $value ) {
        if ( isset ($value [' quality '] )) {
            $_SESSION [' Total_quantity '] += $value [' qty '] ;
        } else {
            // Інакше - видаляємо такий ID із сесій
            unset ($_SESSION [' bloknot '] [$key ] ) ;
        }
    }
}
/* === Посторінкова навігація === */
function pagination ($page , $pages_count , $modrew = 1 ) {
    if ($modrew == 0 ) {
        // Якщо функція викликається на сторінці 
        if ($_SERVER [' QUERY_STRING '] ) {// якщо є параметри у запиті
            $uri = " ? " ;
            foreach ($_GET as $key => $value ) {
                // Формуємо рядок параметрів без номера сторінки ... номер передається параметром функції
               if ($key != ' page ' ) $uri .= " {$key } = {$value } & " ;
            }
        }
    } else {
        // Якщо функція викликана на сторінці 
        $uri = $_SERVER [' REQUEST_URI '] ;
        $params = explode ( "/" , $uri ) ; ;
        $uri = null ;
        foreach ($params as $param ) {
            if (! empty ($param ) AND ! preg_match ( "# page = # " , $param )) {
                $uri .= " / $param " ;
            }
        }
        $uri .= "/" ;
    }
    
    
    // Формування посилань
    $back = '' ; // Посилання НАЗАД
    $forward = '' ; // Посилання ВПЕРЕД
    $startpage = '' ; // Посилання У ПОЧАТОК
    $endpage = '' ; // Посилання У КІНЕЦЬ
    $page2left = '' ; // Друга сторінка зліва
    $page1left = '' ; // Перша сторінка зліва
    $page2right = '' ; // Друга сторінка праворуч
    $page1right = '' ; // Перша сторінка праворуч
    
    if ($page > 1 ) {
        $back = " < a class = ' nav_link ' href = ' {$uri } page =" . ($page -1). " '> << / a > " ;
    }
    if ($page <$pages_count ) {
        $forward = " < a class = ' nav_link ' href = ' {$uri } page =" . ($page +1). " ' >> </ a > " ;
    }
    if ($page > 3 ) {
        $startpage = " <a class='nav_link' href='{$uri}page=1'> « < / a > " ;
    }
    if ($page < ($pages_count - 2 )) {
        $endpage = " <a class='nav_link' href='{$uri}page={$pages_count}'> » </ a > " ;
    }
    if ($page - 2 > 0 ) {
        $page2left = " < a class = ' nav_link ' href = ' {$uri } page =" . ($page -2). " '> " . ($Page -2). " </ a > " ;
    }
    if ($page - 1> 0 ) {
        $page1left = " < a class = ' nav_link ' href = ' {$uri } page =" . ($page -1). " '> " . ($Page -1). " </ a > " ;
    }
    if ($page + 2 <= $pages_count ) {
        $page2right = " < a class = ' nav_link ' href = ' {$uri } page =" . ($page +2 ) . " '> " . ($Page +2 ) . " </ a > " ;
    }
    if ($page + 1 <= $pages_count ) {
        $page1right = " < a class = ' nav_link ' href = ' {$uri } page =" . ($page +1). " '> " . ($Page +1). " </ a > " ;
    }
    
    // Формуємо навігації
    echo ' <div class="pagination"> ' . $startpage . $back . $page2left . $page1left . ' <a class="nav_active"> ' . $page . ' </ a >' . $page1right . $page2right . $forward . $endpage . '< / div >' ;
}
/* === Посторінкова навігація === */
