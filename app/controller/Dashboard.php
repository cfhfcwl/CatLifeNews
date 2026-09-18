<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use app\model\Todo;
use app\model\Checkin;
use app\model\Goal;
use app\model\Ledger;

/**
 * 仪表盘控制器
 * 首页聚合数据
 */
class Dashboard extends BaseController
{
    /**
     * 首页聚合数据
     * GET /api/dashboard
     * @return \think\Response
     */
    public function index()
    {
        $userId = $this->request->user->id;
        $today  = date('Y-m-d');
        $month  = date('Y-m');

        // 今日待办（done / total）
        $todoTotal = Todo::where('user_id', $userId)->count();
        $todoDone  = Todo::where('user_id', $userId)->where('done', 1)->count();

        // 今日打卡（done / total）—— 检查 log 中是否包含今天
        $checkins     = Checkin::where('user_id', $userId)->select();
        $checkinTotal = count($checkins);
        $checkinDone  = 0;
        foreach ($checkins as $checkin) {
            $log = $checkin->log;
            if (isset($log[$today])) {
                $checkinDone++;
            }
        }

        // 目标平均进度
        $goals        = Goal::where('user_id', $userId)->select();
        $goalProgress = 0;
        $goalCount    = count($goals);
        if ($goalCount > 0) {
            $totalProgress = 0;
            foreach ($goals as $goal) {
                if ($goal->target > 0) {
                    $totalProgress += min(100, round($goal->current / $goal->target * 100));
                }
            }
            $goalProgress = (int) round($totalProgress / $goalCount);
        }

        // 本月收入 / 支出
        $income  = Ledger::where('user_id', $userId)
            ->where('kind', 'income')
            ->whereLike('date', $month . '%')
            ->sum('amount');
        $expense = Ledger::where('user_id', $userId)
            ->where('kind', 'expense')
            ->whereLike('date', $month . '%')
            ->sum('amount');

        return $this->ok([
            'todo'    => [
                'done'  => $todoDone,
                'total' => $todoTotal,
            ],
            'checkin' => [
                'done'  => $checkinDone,
                'total' => $checkinTotal,
            ],
            'goal'    => [
                'progress' => $goalProgress,
            ],
            'ledger'  => [
                'income'  => (float) $income,
                'expense' => (float) $expense,
            ],
        ]);
    }
}
