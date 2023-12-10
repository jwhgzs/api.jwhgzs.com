<?
    /* 新宁空间-班级周报 周报详细API */
    
    list($uid, $json) = app_check('xa');
    
    if ($json['aid']) {
        $json['id'] = sql_query1('SELECT parentId FROM xnzx_weekly_article WHERE id = ? LIMIT 1', [$json['aid']])['parentId'];
    }
    
    $data2 = sql_query1('SELECT * FROM xnzx_weekly WHERE id = ?', [intval($json['id'])]);
    $progress = sql_query_count('SELECT id FROM xnzx_weekly_article WHERE parentId = ? AND status >= 1', [$data2['id']]);
    $all = sql_query_count('SELECT id FROM xnzx_weekly_article WHERE parentId = ?', [$data2['id']]);
    $data2['class_'] = $data2['class'];
    $data2['finished_progress'] = intval($progress);
    $data2['full_progress'] = intval($all);
    $data2['finished'] = ($data2['finished_progress'] >= $data2['full_progress'] || $data2['forceFinished']);
    $data2['adminUids'] = app_getAdminTable(false);
    $exceptKeys = ($json['selectAll'] ? [] : ['oriContent']);
    if ($data2['nameInvisible'] && (! in_array($uid, $data2['adminUids']))) {
        $exceptKeys[] = 'author';
        $exceptKeys[] = 'typist';
    }
    $data1 = sql_query('SELECT ' . sql_fieldsExcept('xnzx_weekly_article', $exceptKeys) . ' FROM xnzx_weekly_article WHERE parentId = ? ORDER BY postTime DESC', [intval($data2['id'])]);
    foreach ($data1 as $k => $v) {
        $data1[$k]['author'] = app_xnzx_sid2name($data2['year'], $data2['class'], $v['author']);
        $data1[$k]['typist'] = app_xnzx_sid2name($data2['year'], $data2['class'], $v['typist']);
        $data1[$k]['content'] = explode(PHP_EOL, $v['content']);
    }
    
    api_callback(1, '', ['weeklyDetail' => $data2, 'articleList' => $data1]);
?>