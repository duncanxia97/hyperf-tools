<?php
/**
 * @author XJ.
 * Date: 2023/7/7 0007
 */

namespace Fatbit\HyperfTools\Enums;

use Fatbit\Enums\Annotations\EnumCase;
use Fatbit\Enums\Interfaces\EnumCaseInterface;
use Fatbit\Enums\Traits\EnumCaseGet;

enum ErrorLevel: string implements EnumCaseInterface
{
    use EnumCaseGet;

    #[EnumCase('error_service')]
    case SERVICE = 'error_service';

    #[EnumCase('error_model')]
    case MODEL = 'error_model';

    #[EnumCase('error_controller')]
    case CONTROLLER = 'error_controller';

    #[EnumCase('error_middleware')]
    case MIDDLEWARE = 'error_middleware';

    #[EnumCase('error_system')]
    case SYSTEM = 'error_system';

    #[EnumCase('error_rpc')]
    case RPC = 'error_rpc';
}
