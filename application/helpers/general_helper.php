<?php

/**
 * htmlspecialcharsのエイリアス
 * @param  string $source 変換する文字列
 * @return string         変換後の文字列
 */
function h($source) {
    return htmlspecialchars($source);
}

function renderSuccess($msg) {
    if($msg) {
        echo '<div class="message alert alert-success">';
        if(is_array($msg)) {
            foreach($msg as $msgText) {
                echo '<div>';
                echo $msgText;
                echo '</div>';
            }
        } else {
            echo $msg;
        }
        echo '</div>';
    }
}

function renderError($msg) {
    if($msg) {
        echo '<div class="message alert alert-danger">';
        if(is_array($msg)) {
            foreach($msg as $msgText) {
                if(is_array($msgText)) {
                    foreach($msgText as $msgTextItem) {
                        echo '<div>';
                        echo $msgTextItem;
                        echo '</div>';
                    }
                } else {
                    echo '<div>';
                    echo $msgText;
                    echo '</div>';
                }
            }
        } else {
            echo $msg;
        }
        echo '</div>';
    }
}

function viewDate($strDate, $strFormat = null) {
    if($strDate) {
        if(!$strFormat) {
            $strFormat = DATE_UI_FORMAT;
        }
        return date_format(date_create($strDate), $strFormat);
    }
    return '';
}

function headerName($aryRow = []) {
    return h($aryRow['what']);
}

function genGraph($aryRow = [], $green, $red) {
    $fromValue = $aryRow['from_value'];
    $toValue = $aryRow['to_value'];
    $fromDate = viewDate($aryRow['from_date']);
    $toDate = viewDate($aryRow['to_date']);

    if($aryRow['type'] == OFF) {
        $jissekiValue = $aryRow['now_value'];
        $diffFromDateTo = (int)$aryRow['diff_from_to'];
        $fromDateToCurrent = $aryRow['work_date'];

        if($toValue > $fromValue) {
            if($jissekiValue <= $fromValue) {
                $diffValue = abs($toValue - ($fromValue));
                $nowValue = abs($fromValue - ($fromValue));
            } elseif($jissekiValue >= $toValue) {
                $diffValue = abs($toValue - ($fromValue));
                $nowValue = abs($toValue - ($fromValue));
            } else {
                $diffValue = abs($toValue - ($fromValue));
                $nowValue = abs($jissekiValue - ($fromValue));
            }
        } elseif($toValue < $fromValue) {
            $diffValue = $toValue - ($fromValue);
            $nowValue = $jissekiValue - ($fromValue);
        }
        // X = Y
        else {
            $diffValue = 0;
            $nowValue = $jissekiValue - ($fromValue);
        }

        // show process jisseki
        if($diffValue) {
            $jissekiPercent = ceil(($nowValue * 100)/ $diffValue);
            if(is_float($jissekiPercent)) {
                $jissekiPercent = round($jissekiPercent, 2);
            } else {
                $jissekiPercent = ceil($jissekiPercent);
            }
        }
        // X = Y
        else {
            $jissekiPercent = 0;
        }

        if($fromDateToCurrent <= 0) {
            $yoteiValue = $fromValue;
            $yoteiPercent = 0;
        } else {
            if($diffValue) {
                $yoteiValue = ($diffFromDateTo <= 0) ? 0 : (($diffValue / $diffFromDateTo) * $fromDateToCurrent);
                $yoteiPercent = 0;
                if($fromDateToCurrent > 0) {
                    $yoteiPercent = ceil(($yoteiValue * 100)/ $diffValue);
                }

                if(is_float($yoteiValue)) {
                    $yoteiValue = round($yoteiValue, 2);
                } else {
                    $yoteiValue = ceil($yoteiValue);
                }

                $yoteiValue = $yoteiValue + ($fromValue);
                if($toValue > $fromValue) {
                    $yoteiValue = min($yoteiValue, $toValue);
                } else {
                    $yoteiValue = max($yoteiValue, $toValue);
                }
            } else {
                $yoteiValue = $toValue;
                $yoteiPercent = 100;
            }
        }
    } else {
        $jissekiValue = $aryRow['now_value'];
        $jissekiPercent = min($jissekiValue ? $jissekiValue : 0, $toValue);
        $yoteiPercent = min($aryRow['yotei_tasedo'] ? $aryRow['yotei_tasedo'] : 0, $toValue);
        $yoteiValue = $yoteiPercent;
        if($yoteiPercent < $fromValue) {
            $yoteiValue = $fromValue;
        } elseif($yoteiPercent > $toValue) {
            $yoteiValue = $toValue;
        }
    }
    $jissekiPercent = min($jissekiPercent, 100);
    $yoteiPercent = min($yoteiPercent, 100);

    $progressPercent = 0;
    if($yoteiPercent) {
        $progressPercent = min($jissekiPercent/$yoteiPercent*100, 100);
        if($progressPercent >= $green) {
            $progressColor = COLOR_GREEN_BLUR;
            $yoteiColor = COLOR_GREEN;
        } elseif($progressPercent < $green && $progressPercent > $red) {
            $progressColor = COLOR_YELLOW_BLUR;
            $yoteiColor = COLOR_YELLOW;
        } else {
            $progressColor = COLOR_RED_BLUR;
            $yoteiColor = COLOR_RED;
        }
    } else {
        $progressColor = COLOR_GREEN_BLUR;
        $yoteiColor = COLOR_GREEN;
    }

    $progressPercent = min($progressPercent, 100);

    return [
        'yotei_value' => $yoteiValue
        ,'yotei_percent' => $yoteiPercent
        ,'jisseki_value' => $jissekiValue
        ,'jisseki_percent' => $jissekiPercent
        ,'from_date' => $fromDate
        ,'to_date' => $toDate
        ,'from_value' => $fromValue
        ,'to_value' => $toValue
        ,'progress_percent' => $progressPercent
        ,'progress_color' => $progressColor
        ,'yotei_color' => $yoteiColor
    ];
}

function workingTieWrap($aryData, $zone_rate_green, $zone_rate_red) {
    $graphValue = genGraph($aryData, $zone_rate_green, $zone_rate_red);
    $yoteiValue = $graphValue['yotei_value'] ? $graphValue['yotei_value'] : 0;
    $yoteiPercent = $graphValue['yotei_percent'];
    $jissekiValue = $graphValue['jisseki_value'] ? $graphValue['jisseki_value'] : 0;
    $jissekiPercent = $graphValue['jisseki_percent'];

    $fromDate = $graphValue['from_date'];
    $toDate = $graphValue['to_date'];
    $fromValue = $graphValue['from_value'];
    $toValue = $graphValue['to_value'];

    $progressPercent = $graphValue['progress_percent'];
    $progressColor = $graphValue['progress_color'];
    $yoteiColor = $graphValue['yotei_color'];

    return
        '<div class="working-tie-wrap">'
            .'<div class="working-tie" style="background-color: ' .  $progressColor .';" progress-percent="' . $progressPercent .'">'
                .'<div class="working-progress-tie-now" jisseki-per="' . $jissekiPercent . '">'
                    .'<span class="working-progress-value-now dis-none">実績 <span class="now-value-label">' . $jissekiValue . '</span></span>'
                .'</div>'
                .'<div class="working-progress-tie-now" style="background-color: '. $yoteiColor . '" yotei-per="' . $yoteiPercent . '">'
                    .'<span class="working-progress-value dis-none">予定 <span class="now-value-label">' . $yoteiValue . '</span></span>'
                .'</div>'
            .'</div>'
            .'<div class="working-label">'
                .'<div class="working-start">'
                    .'<div class="working-from-value">' . $fromValue . '</div>'
                    .'<div><b>' . $fromDate  . '</b></div>'
                . '</div>'
            . '<div class="working-end">'
                . '<div class="working-from-value">' . $toValue  . '</div>'
                . '<div><b>' . $toDate  . '</b></div>'
            . '</div>'
        . '</div>'
    . '</div>';
}
