<?php
include 'functions.php';

$taskId = $_POST['taskId'];
$uid = $_POST['uid'];
$taskRoot = dirname(__FILE__) . '\task\\';


// 验证task id合法性
if (strlen($taskId) != 32 || !file_exists($taskRoot . $taskId))
	exit('请勿非法调用！');
$taskFile = $taskRoot . $taskId;

// 读取需要爬取的年月份
$dateList = json_decode(file_get_contents($taskFile));

// 爬取所需的轨迹清单
$trackList = array();
foreach ($dateList as $y => $yArr) {
	foreach ($yArr as $m) {
		$tmpArr = json_decode(file_get_contents ( 'http://www.imxingzhe.com/api/v3/user_month_info?user_id=' . $uid . '&year=' . $y . '&month=' . $m), true);
		$trackList = array_merge($trackList, $tmpArr['data']['wo_info']);
	}
}
file_put_contents($taskFile, json_encode($trackList, JSON_UNESCAPED_UNICODE));

/**
 * 按日期时间创建导出文件夹
 */
$root = dirname(__FILE__) . '\gpx';
$folder = $root . '\\' . date('ymd_his');
if (!is_dir($root))
	mkdir($root);
is_dir($folder) ? exit('<script>msg("警告", "任务队列已满，请刷新后再试。")</script>') : mkdir($folder);

echo '<script>var folderName = ' . $folder . '</script>';
eLog('已获取轨迹清单，开始爬取GPX数据。');
?>