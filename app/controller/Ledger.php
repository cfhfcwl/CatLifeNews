<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use app\model\Ledger as LedgerModel;

/**
 * 记账控制器
 * GET/POST /api/ledger；PUT/DELETE /api/ledger/:id；GET /api/ledger/summary
 */
class Ledger extends BaseController
{
    /**
     * 记账列表
     * GET /api/ledger
     */
    public function index()
    {
        $userId = $this->request->user->id;
        $list   = LedgerModel::where('user_id', $userId)
            ->order('date', 'desc')
            ->order('id', 'desc')
            ->select();

        return $this->ok($list);
    }

    /**
     * 新增记账
     * POST /api/ledger
     */
    public function save()
    {
        $userId = $this->request->user->id;
        $data   = $this->request->param();

        $kind = $data['kind'] ?? '';
        if (!in_array($kind, ['income', 'expense'])) {
            return $this->fail('类型必须是 income 或 expense');
        }

        $category = trim($data['category'] ?? '');
        if ($category === '') {
            return $this->fail('分类不能为空');
        }

        $amount = (float) ($data['amount'] ?? 0);
        if ($amount <= 0) {
            return $this->fail('金额必须大于0');
        }

        $date = $data['date'] ?? date('Y-m-d');
        if (!validate_date($date)) {
            return $this->fail('日期格式不正确');
        }

        $ledger = LedgerModel::create([
            'user_id'  => $userId,
            'kind'     => $kind,
            'category' => str_truncate($category, 50),
            'amount'   => $amount,
            'note'     => str_truncate($data['note'] ?? '', 255),
            'date'     => $date,
        ]);

        return $this->ok($ledger, '创建成功');
    }

    /**
     * 更新记账
     * PUT /api/ledger/:id
     */
    public function update($id)
    {
        $userId = $this->request->user->id;
        $ledger = LedgerModel::where('id', $id)->where('user_id', $userId)->find();

        if (!$ledger) {
            return $this->fail('记录不存在');
        }

        $data = $this->request->param();
        unset($data['id'], $data['user_id']);

        $ledger->allowField(['kind', 'category', 'amount', 'note', 'date'])->save($data);

        return $this->ok($ledger, '更新成功');
    }

    /**
     * 删除记账
     * DELETE /api/ledger/:id
     */
    public function delete($id)
    {
        $userId = $this->request->user->id;
        $ledger = LedgerModel::where('id', $id)->where('user_id', $userId)->find();

        if (!$ledger) {
            return $this->fail('记录不存在');
        }

        $ledger->delete();

        return $this->ok([], '删除成功');
    }

    /**
     * 本月记账汇总
     * GET /api/ledger/summary
     * 返回本月收入、支出、结余、各分类支出占比
     */
    public function summary()
    {
        $userId = $this->request->user->id;
        $month  = date('Y-m');

        // 本月收入
        $income = LedgerModel::where('user_id', $userId)
            ->where('kind', 'income')
            ->whereLike('date', $month . '%')
            ->sum('amount');

        // 本月支出
        $expense = LedgerModel::where('user_id', $userId)
            ->where('kind', 'expense')
            ->whereLike('date', $month . '%')
            ->sum('amount');

        // 各分类支出
        $categories = LedgerModel::where('user_id', $userId)
            ->where('kind', 'expense')
            ->whereLike('date', $month . '%')
            ->field('category, sum(amount) as total')
            ->group('category')
            ->select();

        // 计算占比
        $categoryBreakdown = [];
        foreach ($categories as $cat) {
            $total = (float) $cat['total'];
            $categoryBreakdown[] = [
                'category' => $cat['category'],
                'total'    => $total,
                'ratio'    => $expense > 0 ? round($total / $expense * 100, 1) : 0,
            ];
        }

        // 按金额降序排列
        usort($categoryBreakdown, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        return $this->ok([
            'income'     => (float) $income,
            'expense'    => (float) $expense,
            'balance'    => (float) ($income - $expense),
            'categories' => $categoryBreakdown,
        ]);
    }
}
