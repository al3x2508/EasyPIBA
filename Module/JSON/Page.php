<?php

namespace Module\JSON;

use Model\Model;
use Module\Users\Controller;

class Page
{
    public static function output()
    {
        if (arrayKeyExists('action', $_REQUEST)) {
            switch ($_REQUEST['action']) {
                case 'resend':
                    $user = new Model('users');
                    $user = $user->getOneResult('email', $_REQUEST['email']);
                    if ($user) {
                        $send = Controller::sendActivationEmail($user, false);
                        if ($send) {
                            $response = array(
                                'message' => __('Confirmation code was sent to your email address'),
                                'redirect' => _ADDRESS_ . _FOLDER_URL_ . 'email_confirm'
                            );
                        } else {
                            $response = array('message' => __('Your account is already confirmed'));
                        }
                    } else {
                        $response = array('message' => __('No account registered with this email address'));
                    }
                    $response = json_encode($response);
                    if (arrayKeyExists('callback', $_GET)) {
                        $response = $_GET['callback'] . '(' . $response . ')';
                    }
                    return $response;
                case 'date_firma':
                    header("Access-Control-Allow-Origin: http://ifn.bsprojects.ro/");
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, _OA_URL_ . preg_replace('/[^0-9.]/', '', $_GET['cif']));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('x-api-key: ' . _OA_KEY_));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    if($response === false) $response = json_encode(['']);
                    elseif ($json = json_decode($response, true)) {
                        $json['an'] = preg_replace('/^.*\/([\d]+)$/', '$1', $json['numar_reg_com']);
                        $response = json_encode($json);
                    } else {
                        $response = json_encode(['']);
                    }
                    curl_close($ch);
                    if (arrayKeyExists('callback', $_GET)) {
                        $response = $_GET['callback'] . '(' . $response . ')';
                    }
                    return $response;
                default:
                    return '';
            }
        }
        return '';
    }
}