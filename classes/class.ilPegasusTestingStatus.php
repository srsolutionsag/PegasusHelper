<?php

/**
 * Abstract Class TestStatus as enumeration
 */
abstract class ilPegasusTestingStatus
{
    const T_STATUS_OK = ilLPStatus::LP_STATUS_COMPLETED_NUM;
    const T_STATUS_WARN = ilLPStatus::LP_STATUS_IN_PROGRESS_NUM;
    const T_STATUS_FAIL = ilLPStatus::LP_STATUS_FAILED_NUM;
    const T_STATUS_INCOMPLETE = ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM;

    public static function getStatusText($status)
    {
        switch ($status) {
            case ilPegasusTestingStatus::T_STATUS_OK:
                return "passed";
            case ilPegasusTestingStatus::T_STATUS_WARN:
                return "warning";
            case ilPegasusTestingStatus::T_STATUS_FAIL:
                return "failed";
            case ilPegasusTestingStatus::T_STATUS_INCOMPLETE:
                return "incomplete";
        }
    }

    public static function getStatusImagePath($status)
    {
        return ilLearningProgressBaseGUI::_getImagePathForStatus($status);
    }
}
