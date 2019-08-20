<?PHP
use Model\Model;
use Module\Books\Styles;
use Utils\Util;

ini_set("zlib.output_compression", 4096);
if (!defined("_APP_NAME_")) {
    require_once(dirname(__FILE__) . '/functions.php');
}
function loadJs($js, $fromCache = true, $return = true)
{
    $cache = Util::getCache();
    $scripts = explode(',', $js);
    $md5Value = md5($js);
    $cacheFile = _APP_DIR_ . 'cache/js/' . $md5Value . '.js';
    $buffer = '';
    /** @var bool|Memcached $cache */
    if (!$cache || !($buffer = $cache->get(_CACHE_PREFIX_ . 'javaScript' . $md5Value))) {
        if (!$cache || empty($buffer)) {
            $buffer = "";
            if ($fromCache && file_exists($cacheFile)) {
                return ($return)?file_get_contents($cacheFile):true;
            }
            if (count($scripts) > 0) {
                foreach ($scripts as $script) {
                    switch ($script) {
                        case 'jquery.min.js':
                            $fileName = _APP_DIR_ . 'vendor/components/jquery/' . $script;
                            break;
                        case 'jquery-ui.min.js':
                            $fileName = _APP_DIR_ . 'vendor/components/jqueryui/' . $script;
                            break;
                        case 'bootstrap.min.js':
                            $fileName = _APP_DIR_ . 'vendor/twbs/bootstrap/dist/js/' . $script;
                            break;
                        default:
                            if (strpos($script, 'Module/') !== 0) {
                                if (strpos($script, '/cachedassets/') === 0) {
                                    $fileName = _APP_DIR_ . 'uploads/h5p/' . $script;
                                } else {
                                    $fileName = _APP_DIR_ . 'assets/js/' . $script;
                                }
                            } else {
                                $fileName = _APP_DIR_ . $script;
                            }
                            break;
                    }
                    if (file_exists($fileName)) {
                        $buffer .= file_get_contents($fileName) . PHP_EOL;
                    }
                }
            }
            $buffer = \JShrink\Minifier::minify($buffer, array('flaggedComments' => false));
            if ($cache) {
                $cache->set(_CACHE_PREFIX_ . 'javaScript' . $md5Value, $buffer);
            }
        }
    }
    if ($fromCache) {
        if (!file_exists(_APP_DIR_ . 'cache/')) {
            mkdir(_APP_DIR_ . 'cache/', 0775, true);
        }
        if (!file_exists(_APP_DIR_ . 'cache/js/')) {
            mkdir(_APP_DIR_ . 'cache/js/', 0775, true);
        }
        if (file_exists($cacheFile)) {
            return ($return)?file_get_contents($cacheFile):true;
        } else {
            file_put_contents($cacheFile, $buffer);
        }
    }
    return ($return)?$buffer:true;
}

function loadCss($css, $fromCache = true, $return = true, $saveFileName = '')
{
    $cache = Util::getCache();
    $scripts = explode(',', $css);
    $md5Value = (empty($saveFileName))?md5($css):$saveFileName;
    $buffer = '';
    $cacheFile = _APP_DIR_ . 'cache/css/' . $md5Value . '.css';
    if (!$cache || !($buffer = $cache->get(_CACHE_PREFIX_ . 'css' . $md5Value))) {
        if (!$cache || empty($buffer)) {
            $buffer = "";
            if ($fromCache && file_exists($cacheFile)) {
                return ($return)?file_get_contents($cacheFile):true;
            }
            if (count($scripts) > 0) {
                foreach ($scripts as $script) {
                    switch ($script) {
                        case 'bootstrap.css':
                            $fileName = _APP_DIR_ . 'vendor/twbs/bootstrap/dist/css/' . $script;
                            break;
                        case 'font-awesome.css':
                            $fileName = _APP_DIR_ . 'vendor/components/font-awesome/css/' . $script;
                            break;
                        default:
                            if (strpos($script, 'Module/') !== 0) {
                                if (strpos($script, '/cachedassets/') === 0) {
                                    $fileName = _APP_DIR_ . 'uploads/h5p/' . $script;
                                } else {
                                    $fileName = _APP_DIR_ . 'assets/css/' . $script;
                                }
                            } else {
                                $fileName = _APP_DIR_ . $script;
                            }
                            break;
                    }
                    if (file_exists($fileName)) {
                        $buffer .= file_get_contents($fileName) . PHP_EOL;
                    }
                }
            }
            // Remove comments
            $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
            // Remove space after colons
            $buffer = str_replace(': ', ':', $buffer);
            // Remove whitespace
            $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
            // Enable GZip encoding.
            if ($cache) {
                $cache->set(_CACHE_PREFIX_ . 'css' . $md5Value, $buffer);
            }
        }
    }
    if ($fromCache) {
        if (!file_exists(_APP_DIR_ . 'cache/')) {
            mkdir(_APP_DIR_ . 'cache/', 0775, true);
        }
        if (!file_exists(_APP_DIR_ . 'cache/css/')) {
            mkdir(_APP_DIR_ . 'cache/css/', 0775, true);
        }
        if (file_exists($cacheFile)) {
            return ($return)?file_get_contents($cacheFile):true;
        } else {
            file_put_contents($cacheFile, $buffer);
        }
    }
    return ($return)?$buffer:true;
}

if (arrayKeyExists('js', $_GET)) {
    header("content-type: text/javascript");
    header('Cache-Control: public');
    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000));
    $buffer = loadJs($_GET['js']);
    echo $buffer;
    exit;
} elseif (arrayKeyExists('css', $_GET)) {
    header("content-type: text/css");
    header('Cache-Control: public');
    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000));
    $buffer = loadCss($_GET['css']);
    echo $buffer;
    exit;
} elseif (isset($page_url)) {
    if ($page_url == 'main.css') {
        header("content-type: text/css");
        header('Cache-Control: public');
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000));
        $buffer = loadCss('bootstrap.css,font-montserrat.css,font-awesome.css,main.css', true, true, 'main');
        echo $buffer;
        exit;
    } elseif (strpos($page_url, 'bookcss') === 0) {
        header("content-type: text/css");
        header('Cache-Control: public');
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000));
        if (!file_exists(_APP_DIR_ . 'cache/booksStyles/')) {
            mkdir(_APP_DIR_ . 'cache/booksStyles/', 0775, true);
        }
        if(strpos($page_url, 'bookcss/bookcss') !== false) {
            $bookId = str_replace('bookcss/bookcss', '', $page_url);
            $cacheFile = _APP_DIR_ . 'cache/booksStyles/book' . $bookId . '.css';
        }
        else {
            $bookId = str_replace('bookcss/editor_bookcss', '', $page_url);
            $cacheFile = _APP_DIR_ . 'cache/booksStyles/editor_book' . $bookId . '.css';
        }
        if (!file_exists($cacheFile)) {
            $book = new Model('books');
            $book = $book->getOneResult('id', $bookId);
            if ($book) {
                Styles::bookStyles($bookId);
                echo file_get_contents($cacheFile);
                exit;
            }
        } else {
            echo file_get_contents($cacheFile) . PHP_EOL;
        }
        exit;
    } elseif (strpos($page_url, 'bookjs') === 0) {
        header("content-type: text/javascript");
        header('Cache-Control: public');
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000));
        if (!file_exists(_APP_DIR_ . 'cache/booksJavascripts/')) {
            mkdir(_APP_DIR_ . 'cache/booksJavascripts/', 0775, true);
        }
        $bookId = str_replace('bookjs/bookjs', '', $page_url);
        $cacheFile = _APP_DIR_ . 'cache/booksJavascripts/book' . $bookId . '.js';
        if (!file_exists($cacheFile)) {
            $book = new Model('books');
            $book = $book->getOneResult('id', $bookId);
            if ($book) {
                $bookW = floor($book->page_width * 7.559056);
                $buffer = /** @lang JavaScript */
                "$(function() {
                    var bookW = {$bookW};
                    if(bookW < window.innerWidth) {
                        function loadApp() {
                            var hash = window.location.hash.substr(1),
                                page_no = hash.replace(/p\=(\d+)/, '$1');
                            // Create the flipbook
                            $('#book').addClass('turn').turn({
                                autoCenter: true,
                                page: page_no?page_no:1,
                                when: {
                                    turning: function(event, page, pageObject) {
                                        location.hash = 'p=' + page;
                                    }
                                }
                            }).on('mousewheel DOMMouseScroll', function(event) {
                                if($(window).height() >= $('body').height()) {
                                    if (event.originalEvent.wheelDelta > 0 || (typeof event.originalEvent.detail == 'number' && event.originalEvent.detail < 0)) {
                                        $('#book').turn('previous');
                                    }
                                    else {
                                        $('#book').turn('next');
                                    }
                                }
                            });
                            $('#sidebar-toc').find('a').click(function(event) {
                                var idElement = $(this).attr('href'),
                                    pageNo = $(idElement).closest('.page-wrapper').attr('page');
                                $('#book').turn('page', pageNo);
                                event.stopPropagation();
                                event.preventDefault();
                                
                            });
                        }
                        // Load the HTML4 version if there's not CSS transform
                        yepnope({
                            test : Modernizr.csstransforms,
                            yep: ['" . _FOLDER_URL_ . "js/turnjs/turn.js'],
                            nope: ['" . _FOLDER_URL_ . "js/turnjs/turn.html4.min.js'],
                            both: ['" . _FOLDER_URL_ . "css/turnjs/basic.css'],
                            complete: loadApp
                        });
                    }
                    else {
                        var pageNo = location.hash.replace('#p=', ''),
                            curPage = currentPage();
                        $('.page').each(function(index) {
                            var pNo = index + 1;
                            $(this).attr('id', 'page' + pNo);
                        });
                        if(pageNo) {
                            goToPage(pageNo);
                            curPage = pageNo;
                        }
                        $('#book').scroll(function() {
                            var currentP = currentPage();
                            if(currentP) {
                                var nowPage = currentP.index() + 1;
                                if(nowPage != curPage) {
                                    curPage = nowPage;
                                    location.hash = 'p=' + nowPage;
                                }
                            }
                        });
                    }
                    $('#toolbar').find('button').click(function() {
                        var btnicon = $(this).children('span');
                        if(btnicon.hasClass('fa-search')) {
                            $('#sidebar-toc').removeClass('toggled');
                            $('#sidebar-search').toggleClass('toggled').parent().toggleClass('toggled');
                        }
                        else if(btnicon.hasClass('fa-list-ul')) {
                            $('#sidebar-search').removeClass('toggled');
                            $('#sidebar-toc').toggleClass('toggled').parent().toggleClass('toggled');
                        }
                        else if(btnicon.hasClass('fa-fast-backward')) {
                            if($('#book').hasClass('turn')) $('#book').turn('page', 1);
                            else goToPage(1);
                        }
                        else if(btnicon.hasClass('fa-chevron-left')) {
                            if($('#book').hasClass('turn')) $('#book').turn('previous');
                            else {
                                var curPage = currentPage();
                                if(curPage && curPage.prev() && curPage.prev().offset()) goToPage(curPage.prev().index() + 1);
                            }
                        }
                        else if(btnicon.hasClass('fa-chevron-right')) {
                            if($('#book').hasClass('turn')) $('#book').turn('next');
                            else {
                                var curPage = currentPage();
                                if(curPage && curPage.next() && curPage.next().offset()) goToPage(curPage.next().index() + 1);
                            }
                        }
                        else if(btnicon.hasClass('fa-fast-forward')) {
                            if($('#book').hasClass('turn')) $('#book').turn('page', $('#book').turn('pages'));
                            else goToPage($('.page').length);
                        }
                        else if(btnicon.hasClass('fa-print')) {
                            window.print();
                        }
                    });
                    $('#sidebar-search').find('input[type=\"search\"]').keyup(function(event) {
                        if(event.which == 13) searchText();
                    });
                    $('#sidebar-search').find('button').click(function() {
                        searchText();
                    });
                    function searchText() {
                        var container = $('#book'),
                            slg = $('#sidebar-search').children('.list-group').eq(0);
                        container.find('mark').contents().unwrap();
                        container[0].normalize();
                        slg.empty();
                        if($('#searchterm').val()) InstantSearch.highlight(container[0], $('#searchterm').val());
                    }
                    function currentPage() {
                        var page = false;
                        $('.page').each(function() {
                            if((($(this).offset().top >= 0 && $(this).offset().top <= $('#book').innerHeight() - 100) || ($(this).offset().top < 0 && $(this).offset().top * -1 <= $('#book').innerHeight() - 100)) && $(this).offset().top < $(this).outerHeight(true) - 100) page = !page?$(this):page;
                        });
                        return page;
                    }
                    function goToPage(pageNo) {
                        $('#book').animate({
                            scrollTop: $('#book').scrollTop() + $('#page' + pageNo).offset().top - $('#book').offset().top
                        }, 300);
                        location.hash = 'p=' + pageNo;
                    }
                    var InstantSearch = {

                        'highlight': function (container, highlightText)
                        {
                            var internalHighlighter = function (options)
                            {
                    
                                var id = {
                                    container: 'container',
                                    tokens: 'tokens',
                                    all: 'all',
                                    token: 'token',
                                    className: 'className',
                                    sensitiveSearch: 'sensitiveSearch'
                                },
                                tokens = options[id.tokens],
                                allClassName = options[id.all][id.className],
                                allSensitiveSearch = options[id.all][id.sensitiveSearch],
                                pages = {};
                    
                    
                                function checkAndReplace(node, tokenArr, classNameAll, sensitiveSearchAll)
                                {
                                    var nodeVal = node.nodeValue, parentNode = node.parentNode,
                                        i, j, curToken, myToken, myClassName, mySensitiveSearch,
                                        finalClassName, finalSensitiveSearch,
                                        foundIndex, begin, matched, end,
                                        textNode, span, isFirst;
                    
                                    for (i = 0, j = tokenArr.length; i < j; i++)
                                    {
                                        curToken = tokenArr[i];
                                        myToken = curToken[id.token];
                                        myClassName = curToken[id.className];
                                        mySensitiveSearch = curToken[id.sensitiveSearch];
                    
                                        finalClassName = (classNameAll ? myClassName + ' ' + classNameAll : myClassName);
                    
                                        finalSensitiveSearch = (typeof sensitiveSearchAll !== 'undefined' ? sensitiveSearchAll : mySensitiveSearch);
                    
                                        isFirst = true;
                                        while (true)
                                        {
                                            if (finalSensitiveSearch)
                                                foundIndex = nodeVal.indexOf(myToken);
                                            else
                                                foundIndex = nodeVal.toLowerCase().indexOf(myToken.toLowerCase());
                    
                                            if (foundIndex < 0)
                                            {
                                                if (isFirst)
                                                    break;
                    
                                                if (nodeVal)
                                                {
                                                    textNode = document.createTextNode(nodeVal);
                                                    parentNode.insertBefore(textNode, node);
                                                } // End if (nodeVal)
                    
                                                parentNode.removeChild(node);
                                                break;
                                            } // End if (foundIndex < 0)
                    
                                            isFirst = false;
                    
                    
                                            begin = nodeVal.substring(0, foundIndex);
                                            matched = nodeVal.substr(foundIndex, myToken.length);
                    
                                            if (begin)
                                            {
                                                textNode = document.createTextNode(begin);
                                                parentNode.insertBefore(textNode, node);
                                            } // End if (begin)
                    
                                            mark = document.createElement('mark');
                                            mark.appendChild(document.createTextNode(matched));
                                            parentNode.insertBefore(mark, node);
                                            
                                            var page = $(parentNode).closest('.page'),
                                                pw = page.closest('.page-wrapper'),
                                                matchedFullString = $(node);
                                            while(matched == matchedFullString.text() && matchedFullString.parent()) {
                                                matchedFullString = matchedFullString.parent();
                                            }
                                            var pageIndex = (pw.length)?pw.attr('page'):($('.page').index(page) + 1);
                                            if(!(pageIndex in pages)) pages[pageIndex] = matchedFullString.text().replace(matched, '<mark>' + matched + '</mark>');
                                            nodeVal = nodeVal.substring(foundIndex + myToken.length);
                                        } // Whend
                                    } // Next i 
                                }; // End Function checkAndReplace 
                                function iterator(p)
                                {
                                    if (p === null) return;
                                    var children = Array.prototype.slice.call(p.childNodes), i, cur;
                                    if (children.length)
                                    {
                                        for (i = 0; i < children.length; i++)
                                        {
                                            cur = children[i];
                                            if (cur.nodeType === 3)
                                            {
                                                checkAndReplace(cur, tokens, allClassName, allSensitiveSearch);
                                            }
                                            else if (cur.nodeType === 1)
                                            {
                                                iterator(cur);
                                            }
                                        }
                                    }
                                }; // End Function iterator
                                iterator(options[id.container]);
                                var slg = $('#sidebar-search').children('.list-group').eq(0);
                                $.each(pages, function(key, value) {
                                    var slgi = $('<div></div>'),
                                        slgia = $('<a></a>');
                                    slgia.attr('href', '#p=' + key).addClass('list-group-item-action').html(value);
                                    slgia.click(function() {
                                        if($('#book').hasClass('turn')) $('#book').turn('page', key);
                                        else goToPage(key);
                                    });
                                    slgi.addClass('list-group-item bg-light').append($('<small></small>').text('Pagina ' + key)).append($('<br />')).append(slgia);
                                    slg.append(slgi);
                                });
                                return pages;
                            } // End Function highlighter
                            ;
                            return internalHighlighter(
                                {
                                    container: container
                                    , all:
                                        {
                                            className: 'highlighter'
                                        }
                                    , tokens: [
                                        {
                                            token: highlightText
                                            , className: 'highlight'
                                            , sensitiveSearch: false
                                        }
                                    ]
                                }
                            ); // End Call internalHighlighter 
                        } // End Function highlight
                    };
                });";
                file_put_contents($cacheFile, $buffer);
                echo $buffer;
                exit;
            }
        } else {
            echo file_get_contents($cacheFile) . PHP_EOL;
        }
        exit;
    }
}