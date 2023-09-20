<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: opentelemetry/proto/logs/v1/logs.proto

namespace Opentelemetry\Proto\Logs\V1;

use UnexpectedValueException;

/**
 * LogRecordFlags is defined as a protobuf 'uint32' type and is to be used as
 * bit-fields. Each non-zero value defined in this enum is a bit-mask.
 * To extract the bit-field, for example, use an expression like:
 *   (logRecord.flags & LOG_RECORD_FLAGS_TRACE_FLAGS_MASK)
 *
 * Protobuf type <code>opentelemetry.proto.logs.v1.LogRecordFlags</code>
 */
class LogRecordFlags
{
    /**
     * The zero value for the enum. Should not be used for comparisons.
     * Instead use bitwise "and" with the appropriate mask as shown above.
     *
     * Generated from protobuf enum <code>LOG_RECORD_FLAGS_DO_NOT_USE = 0;</code>
     */
    const LOG_RECORD_FLAGS_DO_NOT_USE = 0;
    /**
     * Bits 0-7 are used for trace flags.
     *
     * Generated from protobuf enum <code>LOG_RECORD_FLAGS_TRACE_FLAGS_MASK = 255;</code>
     */
    const LOG_RECORD_FLAGS_TRACE_FLAGS_MASK = 255;

    private static $valueToName = [
        self::LOG_RECORD_FLAGS_DO_NOT_USE => 'LOG_RECORD_FLAGS_DO_NOT_USE',
        self::LOG_RECORD_FLAGS_TRACE_FLAGS_MASK => 'LOG_RECORD_FLAGS_TRACE_FLAGS_MASK',
    ];

    public static function name($value)
    {
        if (!isset(self::$valueToName[$value])) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no name defined for value %s', __CLASS__, $value));
        }
        return self::$valueToName[$value];
    }


    public static function value($name)
    {
        $const = __CLASS__ . '::' . strtoupper($name);
        if (!defined($const)) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no value defined for name %s', __CLASS__, $name));
        }
        return constant($const);
    }
}
