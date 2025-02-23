<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-21
 * Time: 2:27 PM
 */

namespace Maatify\Json;

use JetBrains\PhpStorm\NoReturn;
use Maatify\Functions\GeneralFunctions;

abstract class JsonGeneralResponse extends FunJson
{

    public static function ErrorNoUpdate(int|string $line = 0): void
    {
        self::ErrorWithHeader400(40001, 'There is no date to update', line: $line);
    }

    public static function Unauthorized(string $line = ''): void
    {
        header('HTTP/1.1 401 Unauthorized');
        header('Content-type: application/json; charset=utf-8');
        echo(json_encode(['line'=>$line],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES));
        exit;
    }

    public static function ServiceUnavailable(string $line = ''): void
    {
        header('HTTP/1.1 503 Maintenance');
        header('Content-type: application/json; charset=utf-8');
        echo(json_encode(['line' => GeneralFunctions::CurrentPageError($line)],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES));
        exit;
    }

    public static function ReLogin(int|string $line, string|array $more_info = ''): void
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        self::GoToMethod('Login',
            'Please Re-Login',
            more_info: $more_info,
            line: $line);
    }

    public static function GoToMethod(string $method, string $description = 'Go Back', string|array $more_info = '', int|string $line = 0, string $token = ''): void
    {
        self::HeaderResponseJson([
            'success'     => false,
            'response'    => 405000,
            'result' => ['token' => $token],
            'description' => $description,
            'more_info'   => $more_info,
            'action'   => $method,
            'error_details'   => GeneralFunctions::CurrentPageError($line ?: debug_backtrace()[0]['line']),

        ]);
    }

    public static function IpIsBlocked(): void
    {
        self::ErrorWithHeader400(403019, 'IP Blocked');
    }

    public static function MissingMethod(): void
    {
        header('HTTP/1.1 405 Method Not Allowed');
        exit;
    }

    public static function RenewalRequired(): void
    {
        header('HTTP/1.1 402 Payment Required, Expired account ');
        exit;
    }

    public static function UnauthorizedBlock(): void
    {
        header('HTTP/1.1 401 Unauthorized');
        exit;
    }

    public static function Forbidden(int|string $line = 0): void
    {
        header('HTTP/1.1 403 Forbidden');
        self::HeaderResponseJson([
            'success'     => false,
            'error_details'   => GeneralFunctions::CurrentPageError($line),
        ]);
        exit;
    }

    public static function DisabledAccount(): void
    {
        header('HTTP/1.1 409 Disabled account ');
        exit;
    }

    public static function IncorrectCredentials(int|string $line = 0): void
    {
        self::ErrorWithHeader400(2025, 'credentials', 'Incorrect Credentials', line: $line ?: debug_backtrace()[0]['line']);
    }

    public static function SuspendedAccount(int|string $line = ''): void
    {
        self::HeaderResponseError(403022, 'Suspended Account' , line: $line ?: debug_backtrace()[0]['line']);
    }

    public static function ApprovalPendingAccount(int|string $line = 0): void
    {
        self::HeaderResponseError(405022, 'Approval Pending Account' , line: $line ?: debug_backtrace()[0]['line']);
    }

    public static function PhoneAlreadyVerified(): void
    {
        self::ErrorWithHeader400(501601, 'phone', 'Already Verified');
    }

    public static function EmailAlreadyVerified(int|string $line = 0): void
    {
        self::ErrorWithHeader400(503030, 'email', 'Email Already Verified' , line: $line ?: debug_backtrace()[0]['line']);
    }

    public static function DeviceIsBlocked(): void
    {
        self::ErrorWithHeader400(403015, 'device', 'Device Blocked');
    }

    public static function DeviceIsPending(): void
    {
        self::ErrorWithHeader400(405015, 'device', 'Device Need Approve, Please Call Customer Support');
    }

    public static function InsufficientBalance(): void
    {
        self::ErrorWithHeader400(405106, 'balance', 'Insufficient Balance');
    }

    public static function GoBackStep(string $description = 'Go Back', int|string $line = 0): void
    {
        self::ErrorWithHeader400(30400, '', $description, (string)$line);
    }

    public static function TryAgain(int|string $line = 0): void
    {
        self::ErrorWithHeader400(30500, '', 'Please try again', line: $line ?: debug_backtrace()[0]['line']);
    }

    public static function captchaInvalid(array|string $description = '', string $more_info = '', int|string $line = 0): void
    {
        if(empty($more_info)){
            $more_info = 'Invalid Validation';
        }
        self::ErrorWithHeader400(40002, 'captcha', $description, $more_info, line: $line ?: debug_backtrace()[0]['line']);
    }

    public static function PlatformNotAllowedToUse(string $description = '', int|string  $line = 0): void
    {
        self::ErrorWithHeader400(7012, 'app_type_id', $description, line: $line ?: debug_backtrace()[0]['line']);

    }

    public static function OTPError(int $error_code = 0, array|string $description = '', array|string $moreInfo = '', int|string  $line = 0): void
    {
        self::ErrorWithHeader400((4110*1000)+$error_code, 'code', $description, $moreInfo, line: $line ?: debug_backtrace()[0]['line']);

    }

    public static function CodeExpired(array|string $description = 'code expired', array|string $moreInfo = '', int|string  $line = 0): void
    {
        self::ErrorWithHeader400(401701, 'code', $description, $moreInfo, line: $line ?: debug_backtrace()[0]['line']);

    }
}