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

class Json extends JsonGeneralResponse
{

    #[NoReturn] public static function Unexpected(
        string $varName = '',
        array|string $result = [],
        string $moreInfo = '',
        int|string $line = ''

    ): void
    {
        self::ErrorWithHeader400(
            9000,
            $varName,
            $result,
            $moreInfo,
            $line
        );
    }

    #[NoReturn] public static function Missing(
        string $varName,
        string $moreInfo = '',
        int|string $line = ''
    ): void
    {
        self::PostErrorHandler($varName, 1000, $moreInfo, $line);
    }

    #[NoReturn] public static function Incorrect(
        string $varName,
        string $moreInfo = '',
        int|string $line = ''
    ): void
    {
        self::PostErrorHandler($varName, 2000, $moreInfo, $line);
    }

    #[NoReturn] public static function Exist(string $varName, string $moreInfo = '', int|string $line = ''): void
    {
        self::PostErrorHandler($varName, 3000, $moreInfo, $line);
    }

    #[NoReturn] public static function Invalid(
        string $varName,
        string $moreInfo = '',
        int|string $line = ''
    ): void
    {
        self::PostErrorHandler($varName, 4000, $moreInfo, $line);
    }

    #[NoReturn] public static function NotVerified(string $varName, string $moreInfo = '', int|string $line = ''): void
    {
        self::PostErrorHandler($varName, 5000, $moreInfo, $line);
    }

    #[NoReturn] public static function NotExist(string $varName, string $moreInfo = '', int|string $line = ''): void
    {
        self::PostErrorHandler($varName, 6000, $moreInfo, $line);
    }

    #[NoReturn] public static function NotAllowedToUse(string $varName, string $moreInfo = '', int|string $line = ''): void
    {
        self::PostErrorHandler($varName, 7000, $moreInfo, $line);
    }

    #[NoReturn] public static function InUse(string $varName, string $moreInfo = '', int|string $line = ''): void
    {
        self::PostErrorHandler($varName, 8000, $moreInfo, $line);
    }

    #[NoReturn] public static function Success(array $result = [], string $description = '', string $more_info = '', int|string $line = ''): void
    {
        self::HeaderResponseJson([
            'success'       => true,
            'response'      => 200,
            'result'        => $result,
            'description'   => $description,
            'more_info'     => $more_info,
            'error_details' => GeneralFunctions::CurrentPageError($line ? : debug_backtrace()[0]['line']),
        ]);
    }

    #[NoReturn] private static function PostErrorHandler(
        string $varName,
        int $CodeRange,
        string $moreInfo = '',
        int|string $line = ''
    ): void
    {
        self::ErrorWithHeader400(
            $CodeRange,
            $varName,
            self::ErrorDescription($varName, $CodeRange),
            $moreInfo,
            $line
        );
    }

    private static function ErrorDescription(
        string $varName,
        int $CodeRange
    ): string
    {
        $formattedVarName = ucwords(str_replace('_', ' ', $varName));

        return match ($CodeRange) {
            1000 => "MISSING $formattedVarName",
            2000 => "Incorrect $formattedVarName",
            3000 => "$formattedVarName is already exist",
            4000 => "INVALID $formattedVarName",
            5000 => "$formattedVarName is Not verified",
            6000 => "$formattedVarName is Not exist",
            7000 => "$formattedVarName is Not Allowed To Use",
            8000 => "$formattedVarName In Use",
            9000 => "$formattedVarName Unexpected",
            default => '',
        };
    }

    #[NoReturn] public static function DbError(int|string $line = 0): void
    {
        http_response_code(500);
        self::HeaderResponseJson([
            'success'     => false,
            'response'    => 500,
            'description' => 'Internal Server Error',
            'more_info'   => $line ? : '',
        ]);
    }

    public static function JsonFormat($array): string
    {
        return (string)str_replace(array("\r", "\n"), '', json_encode($array, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_UNESCAPED_SLASHES));
    }

}