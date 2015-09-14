<?php
/**
 * @link https://github.com/borodulin/yii2-authclients
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-authclients/blob/master/LICENSE
 */
namespace conquer\authclients;

class Odnoklassniki extends \yii\authclient\OAuth2
{
    public $authUrl = 'http://www.odnoklassniki.ru/oauth/authorize';

    public $tokenUrl = 'http://api.odnoklassniki.ru/oauth/token.do';

    public $apiBaseUrl = 'http://api.odnoklassniki.ru/';

    public $publicKey;

    public $scope = 'get_email';
    
    public $attributeNames = [
        'uid',
        'first_name',
        'last_name',
        'name',
        'gender',
        'email',
    ];
    
    protected function defaultName()
    {
        return 'odnoklassniki';
    }

    protected function defaultTitle()
    {
        return 'Одноклассники';
    }

    protected function initUserAttributes()
    {
        return $this->api('fb.do', 'GET', [
            'method'          => 'users.getCurrentUser',
            'format'          => 'json',
            'fields' => implode(',', $this->attributeNames),
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function apiInternal($accessToken, $url, $method, array $params, array $headers)
    {
        $params["application_key"] = $this->publicKey;
        if (ksort($params)) {
            $requestStr = "";
            foreach($params as $key => $value){
                $requestStr .= $key . "=" . $value;
            }
            $requestStr .= md5($accessToken->getToken() . $this->clientSecret);
            $params['sig'] = md5($requestStr);
        }
        return parent::apiInternal($accessToken, $url, $method, $params, $headers);
    }
}