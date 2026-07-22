<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExportReportTime
{
	public static function resolve(ExportFilter $filter)
	{
		$type = $filter->get('type');
		$date_from = $filter->get('date_from');
		$date_to = $filter->get('date_to');
		$start = 0;
		$end = 0;
		$label = 'Tất cả';

		if (!empty($type)) {
			switch ($type) {
				case 'day':
					$start = strtotime(date('Y-m-d 00:00:00'));
					$end = strtotime(date('Y-m-d 23:59:59'));
					$label = 'Hôm nay';
					break;
				case 'week':
					$start = strtotime('monday this week');
					$end = strtotime('sunday this week 23:59:59');
					$label = 'Tuần này';
					break;
				case 'month':
					$start = strtotime(date('Y-m-01 00:00:00'));
					$end = strtotime(date('Y-m-t 23:59:59'));
					$label = 'Tháng này';
					break;
				case 'year':
					$start = strtotime(date('Y-01-01 00:00:00'));
					$end = strtotime(date('Y-12-31 23:59:59'));
					$label = 'Năm nay';
					break;
			}
		} else {
			if (!empty($date_from)) {
				$start = strtotime($date_from . ' 00:00:00');
			}
			if (!empty($date_to)) {
				$end = strtotime($date_to . ' 23:59:59');
			}
			if ($start && $end) {
				$label = date('d/m/Y', $start) . ' – ' . date('d/m/Y', $end);
			}
		}

		if (!$start && !$end) {
			return null;
		}
		return array((int) $start, (int) $end, $label);
	}

	public static function sqlTransactionWhere($start, $end, $alias = '')
	{
		$p = $alias !== '' ? $alias . '.' : '';
		$parts = array($p . 'status = 3');
		if ($start && $end) {
			$parts[] = $p . 'created BETWEEN ' . (int) $start . ' AND ' . (int) $end;
		} elseif ($start) {
			$parts[] = $p . 'created >= ' . (int) $start;
		} elseif ($end) {
			$parts[] = $p . 'created <= ' . (int) $end;
		}
		return implode(' AND ', $parts);
	}
}
