<?php
/**
 * Created by PhpStorm.
 * User: carst
 * Date: 19.02.2018
 * Time: 19:05
 */
namespace Icinga\Module\Grafana\Helpers;

use Icinga\Application\Icinga;


class Timeranges
{
     static $timeRanges = array(
        'Minutes' => array(
            '5m' => '5 minutes',
            '15m' => '15 minutes',
            '30m' => '30 minutes',
            '45m' => '45 minutes'
        ),
        'Hours' => array(
            '1h' => '1 hour',
            '3h' => '3 hours',
            '6h' => '6 hours',
            '8h' => '8 hours',
            '12h' => '12 hours',
            '24h' => '24 hours'
        ),
        'Days' => array (
            '2d' => '2 days',
            '7d' => '7 days',
            '14d' => '14 days',
            '30d' => '30 days',
        ),
        'Months' => array (
            '2M' => '2 month',
            '6M' => '6 months',
            '9M' => '9 months'
        ),
        'Years' => array(
            '1y' => '1 year',
            '2y' => '2 years',
            '3y' => '3 years'
        ),
        'Special' => array(
            '1d/d' => 'Yesterday',
            '2d/d' => 'Day b4 yesterday',
            '1w/w' => 'Previous week',
            '1M/M' => 'Previous month',
            '1Y/Y' => 'Previous Year',
        )
    );


    public function __construct(array $array = array(), $link = "")
    {
        $this->array = $array;
        $this->link = $link;
    }
    private function getTimerangeLink($rangeName, $timeRange)
    {
        $this->view = Icinga::app()->getViewRenderer()->view;
        $this->array['timerange'] = $timeRange;

        return $this->view->qlink(
            $rangeName,
            $this->link,
            $this->array,
            array(
                'class' => 'action-link',
                'data-base-target' => '_self',
                'title' => 'Set timerange for graph(s) to ' . $rangeName
            )
        );
    }

    protected function isValidTimeStamp($timestamp)
    {
        return ((string) (int) $timestamp === $timestamp)
            && ($timestamp <= PHP_INT_MAX)
            && ($timestamp >= ~PHP_INT_MAX);
    }

    private function buildTimrangeMenu($timerange = "", $timerangeto = "")
    {
        $menu = '<table class="grafana-table"><tr>';
        $menu .= '<td><div class="grafana-icon"><div class="grafana-clock"></div></div></td>';
        foreach (self::$timeRanges as $key => $mainValue) {
            $menu .= '<td><ul class="grafana-menu-navigation"><a class="main" href="#">' . $key . '</a>';
            $counter = 1;
            foreach ($mainValue as $subkey => $value) {
                $menu .= '<li class="grafana-menu-n'. $counter .'">' . $this->getTimerangeLink($value, $subkey) . '</li>';
                $counter++;
            }
            $menu .= '</ul></td>';
        }

        $timerange = urldecode($timerange);
        $timerangeto = urldecode($timerangeto);

        if($this->isValidTimeStamp($timerange)) {
            $d = new \DateTime();
            $d->setTimestamp($timerange/1000);
            $timerange = $d->format("Y-m-d H:i:s");
        }

        if($this->isValidTimeStamp($timerangeto)) {
            $d = new \DateTime();
            $d->setTimestamp($timerangeto/1000);
            $timerangeto = $d->format("Y-m-d H:i:s");
        }

        unset($this->array['timerange']);
        $form_link = $this->view->url($this->link, $this->array);
        $menu .= '<td>
                    <form method="get" class="grafana-module-tr-form" action="'.$form_link.'">
                        <input type="text" value="'.$timerange.'" placeholder="from" name="tr-from" />
                        <input type="text" value="'.$timerangeto.'" placeholder="to" name="tr-to" />
                        <a href="'.$form_link.'" data-base-target="_self" class="action-link grafana-module-tr-apply">Apply</a>
                    </form>
                  </td>';
        $menu .= '<script type="text/javascript">
$( document ).ready(function() {
    $("a.grafana-module-tr-apply").click(function() {
        var old_href = $(this).attr("href");
        var tr_from = $("input[name=tr-from]").val();
        var tr_to = $("input[name=tr-to]").val();
        
        var d_tr_from = new Date(tr_from);
        var d_tr_to = new Date(tr_to);
                
        if(d_tr_from != "Invalid Date") {
            tr_from = d_tr_from.getTime();
        }
        if(d_tr_to != "Invalid Date") {
            tr_to = d_tr_to.getTime();
        }
        
        var new_href = old_href + "&tr-from=" + encodeURIComponent(tr_from) + "&tr-to=" + encodeURIComponent(tr_to);
        $(this).attr("href", new_href);
    });    
});
</script>';
        $menu .= '</tr></table>';

        return $menu;
    }

    public function getTimerangeMenu($timerange = "", $timerangeto = "")
    {
        return $this->buildTimrangeMenu($timerange, $timerangeto);
    }

    public static function getTimeranges()
    {
        return call_user_func_array('array_merge',self::$timeRanges);
    }
}