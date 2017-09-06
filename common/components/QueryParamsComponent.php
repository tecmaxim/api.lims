<?php

namespace common\components;

use Yii;

/**
 * 
 */
class QueryParamsComponent extends \yii\filters\auth\QueryParamAuth
{
   public function authenticate($user, $request, $response)
    {
        print_r(Yii::$app->request->queryParams); exit;
        $accessToken = $request->get($this->tokenParam);
        
        if (is_string($accessToken)) {
            $identity = $user->loginByAccessToken($accessToken, get_class($this));
            if ($identity !== null) {
                return $identity;
            }
        }
        if ($accessToken !== null) {
            $this->handleFailure($response);
        }

        return null;
    }
}
