<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%performance}}".
 *
 * @property string $user_id 用户 ID
 * @property string $company_id 公司 ID
 * @property string $agent_id 代理 ID
 * @property string $agent_company_id 代理公司 ID
 * @property string $city_id 城市代理 ID
 * @property string $order_id 订单号
 * @property string $amount 业绩
 * @property int $is_offline 线下业绩
 * @property int $year 年份
 * @property int $month 月份
 */
class Performance extends \common\models\Performance
{

    public static $person = [
        [
            'max' => 500,
            'rate' => 0.00,
        ],
        [
            'min' => 500,
            'max' => 3000,
            'rate' => 0.35,
        ],
        [
            'min' => 3000,
            'max' => 10000,
            'rate' => 0.45,
        ],
        [
            'min' => 10000,
            'rate' => 0.57,
        ],
    ];

    public static $groupRate = 0.1;
    public static $directCompanyRate = 0.1;
    public static $directCityRate = 0.05;
    public static $allRate = 0.01;

    private static $cache = [];

    public function setAgent($userId)
    {
        $user = User::findOne($userId);

        if (!$user) {
            return false;
        }

        $this->user_id = $user->user_id;
        $this->company_id = $user->company;

        $referee = User::findOne($user->referee);

        if ($referee) {
            $this->agent_id = $referee->user_id;
            $this->agent_company_id = $referee->company;
        }

        return true;
    }

    public static function getRate($amount, $rules)
    {
        foreach ($rules as $rule) {
            if (isset($rule['min']) && $rule['min'] > $amount) {
                continue;
            }

            if (isset($rule['max']) && $rule['max'] <= $amount) {
                continue;
            }

            return $rule['rate'];
        }
    }

    /**
     * 计算个人奖励
     *
     * @param $amount
     *
     * @return float
     */
    public static function calculatePerson($amount)
    {
        return round($amount * self::getRate($amount, self::$person));
    }

    /**
     * 计算团队管理奖励
     *
     * @param $amount
     *
     * @return float
     */
    public static function calculateGroup($amount)
    {
        return round($amount * self::$groupRate, 2);
    }

    /**
     * 计算直属公司奖励
     *
     * @param $amount
     *
     * @return float
     */
    public static function calculateDirectCompany($amount)
    {
        return round($amount * self::$directCompanyRate, 2);
    }

    /**
     * 计算直属市奖励
     *
     * @param $amount
     *
     * @return float
     */
    public static function calculateDirectCity($amount)
    {
        return round($amount * self::$directCityRate, 2);
    }

    /**
     * 计算个人奖励
     *
     * @param $amount
     *
     * @return float
     */
    public static function calculateAll($amount)
    {
        return round($amount * self::$allRate, 2);
    }

    /**
     * 获取全部业绩奖励
     *
     * @param $userId
     * @param $year
     * @param $month
     *
     * @return float
     */
    public static function getRewardForAll($year = null, $month = null)
    {
        return self::calculateAll(self::getTotalForAll($year, $month));
    }

    /**
     * 获取个人业绩奖励
     *
     * @param $userId
     * @param $year
     * @param $month
     *
     * @return float
     */
    public static function getRewardForPerson($userId, $year = null, $month = null)
    {
        return self::calculatePerson(self::getTotalForPerson($userId, $year, $month));
    }

    /**
     * 获取直属个人差额奖励
     *
     * @param $userId
     * @param $amount
     * @param $year
     * @param $month
     *
     * @return float|int
     */
    public static function getRewardForDifference($userId, $year = null, $month = null)
    {
        $personRate = self::getRate(self::getTotalForPerson($userId, $year, $month), self::$person);
        $userIds = User::find()->select('user_id')->where(['referee' => $userId])->column();

        $reward = 0;
        foreach ($userIds as $userId) {
            $self = self::getTotalForPerson($userId, $year, $month);
            $direct = self::getTotalForAgent($userId, $year, $month);

            $directRate = self::getRate($self + $direct, self::$person);

            if ($personRate > $directRate) {
                $reward += round($self * ($personRate - $directRate), 2);
            }
        }

        return $reward;
    }

    /**
     * 获取个人团队管理奖励
     *
     * @param $userId
     * @param $year
     * @param $month
     *
     * @return float
     */
    public static function getRewardForGroup($userId, $year = null, $month = null)
    {
        return self::calculateGroup(self::getTotalForGroup($userId, $year, $month));
    }

    /**
     * 获取公司总业绩奖励
     *
     * @param $userId
     * @param $year
     * @param $month
     *
     * @return float
     */
    public static function getRewardForCompany($userId, $year = null, $month = null)
    {
        $userIds = User::find()->select('user_id')->where(['company' => $userId])->column();

        $userIds[] = $userId;

        $total = 0;
        foreach ($userIds as $userId) {
            $total += self::getTotalForPerson($userId, $year, $month);
        }

        return self::calculateGroup($total);
    }

    /**
     * 获取直属公司总业绩奖励
     *
     * @param $userId
     * @param $year
     * @param $month
     *
     * @return float
     */
    public static function getRewardForDirectCompany($userId, $year = null, $month = null)
    {
        $userIds = User::find()->select('user_id')->where(['referee' => $userId, 'type' => User::TYPE_COMPANY])->column();

        $userIds[] = $userId;

        $total = 0;
        foreach ($userIds as $userId) {
            $total += self::getTotalForCompany($userId, $year, $month);
        }

        return self::calculateDirectCompany($total);
    }

    /**
     * 获取直属城市总业绩奖励
     *
     * @param $userId
     * @param $year
     * @param $month
     *
     * @return float
     */
    public static function getRewardForDirectCity($userId, $year = null, $month = null)
    {
        $userIds = User::find()->select('user_id')->where(['referee' => $userId, 'type' => User::TYPE_CITY])->column();

        $userIds[] = $userId;

        $total = 0;
        foreach ($userIds as $userId) {
            $total += self::getTotalForCity($userId, $year, $month);
        }

        return self::calculateDirectCity($total);
    }

    /**
     * @param $userId
     * @param null $year
     * @param null $month
     *
     * @return float|int
     */
    public static function getRewardForCompanyDifference($userId, $year = null, $month = null)
    {
        $companyRate = 0.57;
        $userIds = User::find()->select('user_id')->where(['company' => $userId, 'type' => User::TYPE_AGENT])->column();

        $reward = 0;
        foreach ($userIds as $userId) {
            $self = self::getTotalForPerson($userId, $year, $month);
            $direct = self::getTotalForAgent($userId, $year, $month);

            $personRate = self::getRate($self + $direct, self::$person);

            $childUserIds = User::find()->select('user_id')->where([
                'and',
                ['in', 'type', [User::TYPE_NORMAL, User::TYPE_AGENT]],
                ['=', 'referee', $userId],
            ])->column();

            foreach ($childUserIds as $childUserId) {
                $self = self::getTotalForPerson($childUserId, $year, $month);
                $direct = self::getTotalForAgent($childUserId, $year, $month);

                $childPersonRate = self::getRate($self + $direct, self::$person);

                if ($personRate >= $childPersonRate) {
                    if ($companyRate > $personRate) {
                        $reward += round($self * ($companyRate - $personRate), 2);
                    }
                }
            }
        }

        return $reward;
    }

    /**
     * 获取团队业绩
     *
     * @param $userId
     * @param $year
     * @param $month
     *
     * @return mixed
     */
    public static function getTotalForGroup($userId, $year = null, $month = null, $noSelf = true)
    {
        $direct = self::getTotalForAgent($userId, $year, $month);

        if (!$noSelf) {
            $self = self::getTotalForPerson($userId, $year, $month);

            return $self + $direct;
        }

        return $direct;
    }

    /**
     * 获取个人业绩
     *
     * @param $userId
     * @param $year
     * @param $month
     *
     * @return mixed
     */
    public static function getTotalForPerson($userId, $year = null, $month = null)
    {
        $key = md5("person" . $userId . ":" . $year . ":" . $month);

        if (!isset(self::$cache[$key])) {
            self::$cache[$key] = self::find()->andFilterWhere([
                'year' => $year,
                'month' => $month,
                'user_id' => $userId,
            ])->sum('amount');
        }

        return self::$cache[$key];
    }

    /**
     * 获取公司业绩
     *
     * @param $userId
     * @param $year
     * @param $month
     *
     * @return mixed
     */
    public static function getTotalForCompany($userId, $year = null, $month = null)
    {
        $key = md5("company" . $userId . ":" . $year . ":" . $month);

        if (!isset(self::$cache[$key])) {
            self::$cache[$key] = self::find()->andFilterWhere([
                'year' => $year,
                'month' => $month,
                'company_id' => $userId,
            ])->sum('amount');
        }

        return self::$cache[$key];
    }

    /**
     * 获取公司业绩
     *
     * @param $userId
     * @param $year
     * @param $month
     *
     * @return mixed
     */
    public static function getTotalForCity($userId, $year = null, $month = null)
    {
        $key = md5("city" . $userId . ":" . $year . ":" . $month);

        if (!isset(self::$cache[$key])) {
            self::$cache[$key] = self::find()->andFilterWhere([
                'year' => $year,
                'month' => $month,
                'city_id' => $userId,
            ])->sum('amount');
        }

        return self::$cache[$key];
    }

    /**
     * 获取直属个人业绩
     *
     * @param $userId
     * @param $year
     * @param $month
     *
     * @return mixed
     */
    public static function getTotalForAgent($userId, $year = null, $month = null)
    {
        $key = md5("agent" . $userId . ":" . $year . ":" . $month);

        if (!isset(self::$cache[$key])) {
            self::$cache[$key] = self::find()->andFilterWhere([
                'year' => $year,
                'month' => $month,
                'agent_id' => $userId,
            ])->sum('amount');
        }

        return self::$cache[$key];
    }

    /**
     * 获取直属公司业绩
     *
     * @param $userId
     * @param $year
     * @param $month
     *
     * @return mixed
     */
    public static function getTotalForAgentCompany($userId, $year = null, $month = null)
    {
        $key = md5("agent-company" . $userId . ":" . $year . ":" . $month);

        if (!isset(self::$cache[$key])) {
            self::$cache[$key] = self::find()->andFilterWhere([
                'year' => $year,
                'month' => $month,
                'agent_company_id' => $userId,
            ])->sum('amount');
        }

        return self::$cache[$key];
    }

    /**
     * 全部业绩
     *
     * @param $userId
     * @param $year
     * @param $month
     *
     * @return mixed
     */
    public static function getTotalForAll($year = null, $month = null)
    {
        $key = md5("all:" . $year . ":" . $month);

        if (!isset(self::$cache[$key])) {
            self::$cache[$key] = self::find()->andFilterWhere([
                'year' => $year,
                'month' => $month,
            ])->sum('amount');
        }

        return self::$cache[$key];
    }
}
