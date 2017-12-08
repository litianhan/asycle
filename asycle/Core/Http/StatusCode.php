<?php

namespace Asycle\Core\http;
/**
 * Date: 2017/9/24
 * Time: 下午9:25
 */
class StatusCode{
    public static function text(int $statusCode){
        $status = [
            100 => 'Continue',
            101 => 'Switching Protocols',

            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',

            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',

            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            422 => 'Unprocessable Entity',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',

            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            511 => 'Network Authentication Required',
        ];
        if( ! isset($status[$statusCode])){
            throw new \InvalidArgumentException('No status text available.');
        }
        return $status[$statusCode];
    }

    const Continue = 100;
    const SwitchingProtocols = 101;


    const OK = 200;
    const Created =201;
    const Accepted = 202;
    const NonAuthoritativeInformation = 203;
    const NoContent = 204;
    const ResetContent = 205;
    const PartialContent = 206;

    const MultipleChoices = 300;
    const MovedPermanently = 301;
    const Found = 302;
    const SeeOther = 303;
    const NotModified = 304;
    const UseProxy = 305;
    const TemporaryRedirect = 306;

    const BadRequest = 400;
    const Unauthorized = 401;
    const PaymentRequire = 402;
    const Forbidden = 403;
    const NotFound =404;
    const MethodNotAllowed = 405;
    const NotAcceptable = 406;
    const ProxyAuthenticationRequired = 407;
    const RequestTimeout = 408;
    const Conflict = 409;
    const Gone = 410;
    const LengthRequired = 411;
    const PreconditionFailed = 412;
    const RequestEntityTooLarge = 413;
    const RequestURITooLong = 414;
    const UnsupportedMediaType = 415;
    const RequestedRangeNotSatisfiable = 416;
    const ExpectationFailed = 417;
    const UnprocessableEntity = 422;
    const UpgradeRequired = 426;
    const PreconditionRequired = 428;
    const TooManyRequests = 429;
    const RequestHeaderFieldsTooLarge = 431;

    const InternalServerError = 500;
    const NotImplemented = 501;
    const BadGateway = 502;
    const ServiceUnavailable = 503;
    const GatewayTimeout = 504;
    const HTTPVersionNotSupported = 505;
    const NetworkAuthenticationRequired = 511;
}

