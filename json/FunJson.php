<?php

/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2022-12-12
 * Time: 1:36 AM
 */
namespace Maatify\Json;

use Maatify\Functions\GeneralFunctions;
use Maatify\Logger\Logger;

abstract class FunJson
{
    public static function PostLogger(): void
    {
        $arr = $_POST;
        if (isset($_POST['password']) & ! empty($_POST['password'])) {
            $arr['password'] = '*********';
        }
        Logger::RecordLog(['posted_data' => ($arr ?? ''),
                           'agent'       => ($_SERVER['HTTP_USER_AGENT']) ?? '',
                           'ip'          => $_SERVER['REMOTE_ADDR'] ?? '',
                           'forward_ip'  => ($_SERVER['HTTP_X_FORWARDED_FOR'] ??
                                             ''),
                           'server_ip'   => ($_SERVER['SERVER_ADDR'] ?? ''),
                           'referer'     => ($_SERVER['HTTP_REFERER'] ?? ''),
                           'uri'         => ($_SERVER['REQUEST_URI'] ?? ''),
                           'page'        => (basename($_SERVER['PHP_SELF']) ??
                                             ''),
        ], 'post/' . (basename($_SERVER["PHP_SELF"], '.php') ?? 'posted'));
    }

    public static function LoggerResponseAndPost($json_array): void
    {
        $arr = $_POST;
        if (isset($_POST['password']) & ! empty($_POST['password'])) {
            $arr['password'] = '*******';
        }
        Logger::RecordLog(['Response'    => $json_array,
                           'posted_data' => ($arr ?? ''),
                           'agent'       => $_SERVER['HTTP_USER_AGENT'],
                           'ip'          => $_SERVER['REMOTE_ADDR'] ?? '',
                           'real_ip'     => $_SERVER['REMOTE_ADDR'],
                           'forward_ip'  => ($_SERVER['HTTP_X_FORWARDED_FOR'] ??
                                             ''),
                           'server_ip'   => ($_SERVER['SERVER_ADDR'] ?? ''),
                           'referer'     => ($_SERVER['HTTP_REFERER'] ?? ''),
                           'uri'         => ($_SERVER['REQUEST_URI'] ?? ''),
                           'page'        => (basename($_SERVER['PHP_SELF']) ??
                                             ''),
        ],
            'post/' . (basename($_SERVER["PHP_SELF"], '.php') ?? 'posted')
            . '_response');
    }


    public static function HeaderError($line = ''): void
    {
        self::ErrorWithHeader400(1000001,'',
            'Error: ' . GeneralFunctions::CurrentPageError($line), line: $line);
    }

    public static function HeaderResponseError($code, $desc, $more = '', $line = ''): void
    {
        self::HeaderResponseJson(['success'     => false,
                                  'response'    => $code,
                                  'description' => $desc,
                                  'more_info'   => $more,
                                  'error_details'   => GeneralFunctions::CurrentPageError($line),]);
    }

    public static function headerResponseSuccess($arr): void
    {

        $result['success'] = true;
        $result['response'] = 200;
        if (isset($arr['token'])) {
            $result['token'] = $arr['token'];
            unset($arr['token']);
        }
        foreach ($arr as $key => $value) {
            $result[$key] = $value;
        }
        $result['description'] = '';
        self::HeaderResponseJson($result);
    }

    public static function HeaderResponseJson($json_array): void
    {
        if (! empty($_ENV['JSON_POST_LOG'])) {
            self::LoggerResponseAndPost($json_array);
        }
        header('Content-type: application/json; charset=utf-8');
        echo(json_encode($json_array,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES));
        exit();
    }

    public static function ErrorWithHeader400(int $responseCode,
        string $varName,
        string $description = '',
        string $moreInfo = '',
        int|string $line = ''
    ): void
    {
        http_response_code(400);
        self::HeaderResponseJson([
            'success'     => false,
            'response'    => $responseCode,
            'var'    => $varName,
            'description' => $description,
            'more_info'   => $moreInfo,
            'error_details'   => GeneralFunctions::CurrentPageError($line),
        ]);
    }
}