<?php

namespace Controller;

use Model\Model;
use stdClass;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

/**
 * Class AdminPage
 * @package Controller
 */
abstract class AdminPage
{
    /**
     * @param string $page_name
     *
     * @return bool|string|AdminPage|stdClass
     */
    public static function getCurrentModule(string $page_name)
    {
        if (!empty($page_name)) {
            $mAR = new Model('module_admin_routes');
            $mAR = $mAR->getOneResult('url', $page_name);
            if ($mAR) {
                $admins_permissions = new Model('admins_permissions');
                $admins_permissions->admin = AdminController::getCurrentUser()->id;
                $admins_permissions->permission = $mAR->permission;
                if (count($admins_permissions->get())) {
                    $class = 'Module\\' . $mAR->modules->name . '\\Admin\\AdminPage';
                    if (class_exists($class)) {
                        return new $class();
                    } else {
                        return self::getTwigPage($mAR->modules->name);
                    }
                }
            }
        }

        return false;
    }

    public static function getTwigPage($moduleName)
    {
        $json_file = _APP_DIR_ . 'Module/' . $moduleName . '/Admin/admin.json';
        if (file_exists($json_file)) {
            $loader = new FilesystemLoader(
                _APP_DIR_ . 'Module/' . $moduleName . '/Admin/'
            );
            $twig = new Environment($loader, [
                'cache' => _APP_DIR_ . 'cache/templates',
            ]);
            $twig->addFunction(new TwigFunction('__', '__'));
            $page = new class {
                public function output()
                {
                    $output = json_decode(file_get_contents($this->json_file));
                    if (isset($output->_wildcards_)) {
                        unset($output->_wildcards_);
                    }
                    foreach (get_object_vars($output) as $key => $value) {
                        $this->$key = $value;
                    }
                    $output->content = $this->twig->render('admin.twig', get_object_vars($this));

                    return $output;
                }

                public function __get(string $name)
                {
                    return (property_exists($this, $name))?$this->$name:null;
                }

                public function __set(string $name, $value)
                {
                    $this->$name = $value;

                    return $this;
                }
            };
            $page->json_file = $json_file;
            $page->twig = $twig;

            return $page;
        }

        return false;
    }

    /**
     * @param array $link
     * @param string $page_name
     *
     * @return string
     */
    public static function createLink(array $link, string $page_name): string
    {
        $arrayClass = ['nav-item'];
        $linkClass = ['nav-link'];
        $href = $link['href'];
        if ($link['href'] == $page_name || arrayKeyExists('submenu', $link)) {
            $href = '#';
            if ($link['href'] == $page_name) {
                $linkClass[] = "active";
            }
            if (arrayKeyExists('submenu', $link)) {
                foreach ($link['submenu'] as $submenu) {
                    if ($submenu['href'] == $page_name) {
                        $linkClass[] = "active";
                        $arrayClass[] = "menu-open";
                    }
                }
            }
        }
        $html_submenu = '';
        if (arrayKeyExists('submenu', $link)) {
            $menuLinkClass = ['nav-link'];
            $hrefClass = $link['class'];
            if ($link['href'] == $page_name) {
                $menuLinkClass[] = "active";
                $arrayClass[] = "menu-open";
            }
            $html_submenu .= '<ul class="nav nav-treeview">' . PHP_EOL;
            $html_submenu .= '<li class="nav-item"><a href="' . $link['href'] . '" class="' . join(
                    " ",
                    $menuLinkClass
                ) . '" title="' . $link['text'] . '"><i class="' . $hrefClass . '"></i> ' . $link['text'] . '</a>' . PHP_EOL;
            foreach ($link['submenu'] as $submenu) {
                $submenuLinkClass = ['nav-link'];
                $hrefClass = arrayKeyExists('class', $submenu)?$submenu['class']:$link['class'];
                if ($submenu['href'] == $page_name) {
                    $submenuLinkClass[] = "active";
                    $arrayClass[] = "menu-open";
                }
                $html_submenu .= '<li class="nav-item"><a href="' . $submenu['href'] . '" class="' . join(
                        " ",
                        $submenuLinkClass
                    ) . '" title="' . $submenu['text'] . '"><i class="' . $hrefClass . '"></i> ' . $submenu['text'] . '</a>' . PHP_EOL;
            }
            $html_submenu .= '</ul>' . PHP_EOL;
        }

        return '<li class="' . join(" ", $arrayClass) . '">
                <a href="' . $href . '" class="
                    ' . join(" ", $linkClass) . '" title="' . $link['text'] . '">
                    <i class="' . $link['class'] . '"></i>
                    <p>
                        ' . $link['text'] . (!empty($html_submenu)?'<i class="fa fa-angle-left right"></i>':'') . '
                    </p>
                </a>' . $html_submenu . '
            </li>' . PHP_EOL;
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->$key = $value;
    }
}