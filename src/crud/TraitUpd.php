<?php
declare (strict_types=1);

/**
 * Author:
 *
 *   ┏┛ ┻━━━━━┛ ┻┓
 *   ┃　　　━　　  ┃
 *   ┃　┳┛　  ┗┳  ┃
 *   ┃　　　-　　  ┃
 *   ┗━┓　　　┏━━━┛
 *     ┃　　　┗━━━━━━━━━┓
 *     ┗━┓ ┓ ┏━━━┳ ┓ ┏━┛
 *       ┗━┻━┛   ┗━┻━┛
 * DateTime: 2021-12-14 16:37:16
 */

namespace rocket\crud;

use rocket\authdata\Authdata;
use think\facade\Request;
use Exception;

/**
 * Trait更新
 */
trait TraitUpd
{
    /**
     * 更新
     * @throws Exception
     * create_at: 2021-12-14 16:41:19
     * update_at: 2021-12-14 16:41:19
     */
    public function upd()
    {
        // 请求参数
        $originParams = Request::param();
        // 过滤参数
        $filterParams = Request::only($this->attrsUpd);
        // 校验前处理
        $params = $this->bfrUpdVerify($filterParams, $originParams);
        // 验证
        if ($this->validator && !$this->validator->scene('upd')->check($params)) {
            throw new Exception($this->validator->getError());
        }
        // 主键
        $id = $params[$this->pk];
        if (!$id) throw new Exception("`{$this->pk}`" . lang('trait_upd.parameter error, unknown update target'));
        // 数据权限
        $this->dataAuth(Authdata::ENV_UD, self::class, $this->pk, $id);
        // 移除更新数据中的主键
        unset($params[$this->pk]);
        // 更新目标是否存在
        $target = self::where($this->pk, $id)->find();
        if (!$target) throw new Exception( lang('trait_upd.the target does not exist or has been deleted') );
        // 数据更新前
        $params = $this->bfrUpd($id, $params, $originParams);
        // 数据更新
        $upd = self::update($params, [$this->pk => $id]);
        // 数据更新后
        (false !== $upd) && $this->aftUpd($id, $params, $originParams);

        return true;
    }
}
