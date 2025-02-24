<?php

/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2022-12-12
 * Time: 1:36 AM
 */

namespace Maatify\Json;

use JetBrains\PhpStorm\NoReturn;
use Maatify\Functions\GeneralFunctions;
use Maatify\Logger\Logger;

abstract class FunJson
{
    public static function PostLogger(): void
    {
        $arr = $_POST;
        if (! empty($_POST['password'])) {
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

    public static function LoggerResponseAndPost(array $json_array): void
    {
        $arr = $_POST;

        if (! empty($arr['password'])) {
            $arr['password'] = '*******';
        }

        if (! empty($arr['base64_file'])) {
            $arr['base64_file'] = (base64_encode(base64_decode($arr['base64_file'], true)) === $arr['base64_file'])
                ? 'Valid Base64'
                : 'Not Valid Base64';
        }

        unset($json_array['result']['base64']);

        // Handle Logger APP Folder
        $urlParts = explode('/', ltrim($_SERVER['REQUEST_URI'], '/'));
        $app_folder_logger = ($urlParts[0] ?? '') === 'apps'
            ? ($urlParts[0] . '-' . ($urlParts[1] ?? ''))
            : ($urlParts[0] ?? '');

        Logger::RecordLog([
            'Response'    => $json_array,
            'posted_data' => $arr ?: '',
            'agent'       => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'ip'          => $_SERVER['REMOTE_ADDR'] ?? '',
            'real_ip'     => $_SERVER['REMOTE_ADDR'] ?? '',
            'forward_ip'  => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '',
            'server_ip'   => $_SERVER['SERVER_ADDR'] ?? '',
            'referer'     => $_SERVER['HTTP_REFERER'] ?? '',
            'uri'         => $_SERVER['REQUEST_URI'] ?? '',
            'page'        => basename($_SERVER['PHP_SELF']) ?? '',
        ], 'post/' . ($app_folder_logger ? "$app_folder_logger/" : '') . (basename($_SERVER["PHP_SELF"], '.php') ?? 'posted') . '_response');
    }

    #[NoReturn] public static function HeaderError($line = ''): void
    {
        self::ErrorWithHeader400(1000001,
            '',
            'Error: ' . GeneralFunctions::CurrentPageError($line),
            line: $line);
    }

    #[NoReturn] public static function HeaderResponseError($code, $desc, $more = '', $line = ''): void
    {
        self::HeaderResponseJson(['success'       => false,
                                  'response'      => $code,
                                  'description'   => $desc,
                                  'more_info'     => $more,
                                  'error_details' => GeneralFunctions::CurrentPageError($line),]);
    }

    #[NoReturn] public static function headerResponseSuccess($arr): void
    {
        $result = [
            'success'     => true,
            'response'    => 200,
            'description' => '',
        ];

        if (isset($arr['token'])) {
            $result['token'] = $arr['token'];
            unset($arr['token']);
        }

        self::HeaderResponseJson($result);
    }

    #[NoReturn] public static function HeaderResponseJson($json_array): void
    {
        if (! empty($_ENV['JSON_POST_LOG'])) {
            self::LoggerResponseAndPost($json_array);
        }
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($json_array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }

    #[NoReturn] public static function ErrorWithHeader400(int $responseCode,
        string $varName,
        array|string $description = '',
        string $moreInfo = '',
        int|string $line = ''
    ): void
    {
        http_response_code(400);
        self::HeaderResponseJson([
            'success'       => false,
            'response'      => $responseCode,
            'var'           => $varName,
            'description'   => $description,
            'more_info'     => $moreInfo,
            'error_details' => GeneralFunctions::CurrentPageError($line),
        ]);
    }
}