<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://hustlehero.com.au
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\jwt;

use common\base\helpers\ArrayHelper;
use DateTimeImmutable;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use ReflectionClass;
use yii\base\Component;
use Yii;

/**
 * Class Jwt
 * @property Configuration $configuration
 * @property Builder $builder
 * @package common\base\jwt
 */
class Jwt extends Component
{
    /**
     * @var array Supported algorithms
     */
    public $supportedAlgorithms = [
        'HS512' => Sha512::class,
    ];

    /**
     * @var Key|string $key The key
     */
    public $key;
    /** @var string */
    public $algorithm = 'HS512';
    /** @var string */
    public $issuer = 'https://uat.hustlehero.com.au';
    /** @var string */
    public $audience = 'user';
    /** @var int  */
    public $expiresIn = 3600;

    /**
     * @var Configuration
     */
    private $_configuration;

    public function init()
    {
        parent::init();
        $this->_configuration = Configuration::forSymmetricSigner($this->getSigner(), $this->getSignerKey());
    }

    /**
     * @param array $options
     * @return Token\Plain
     */
    public function issueToken($options = [])
    {
        $now = new DateTimeImmutable();
        $builder = $this->configuration->builder();

        $builder->issuedAt($now);
        $builder->expiresAt($now->modify(sprintf('+%d second', $this->expiresIn)));

        if (!empty($this->issuer)) {
            $builder->issuedBy($this->issuer);
        }

        if (!empty($this->audience)) {
            $builder->permittedFor($this->audience);
        }

        if (!empty($sub = ArrayHelper::getValue($options, ['sub']))) {
            $builder->relatedTo($sub);
        }

        if (!empty($jti = ArrayHelper::getValue($options, ['jti']))) {
            $builder->identifiedBy($jti);
        }

        return $builder->getToken($this->configuration->signer(), $this->configuration->signingKey());
    }

    /**
     * @param string $token
     * @return UnencryptedToken
     */
    public function parseToken($token)
    {
        return $this->configuration->parser()->parse($token);
    }

    /**
     * @param $accessToken
     * @return array|mixed|null
     */
    public function resolveToken($accessToken)
    {
        try {
            $token = $this->parseToken($accessToken);
            if ($token instanceof UnencryptedToken) {
                $constraints = [
                    new IssuedBy($this->issuer),
                    new PermittedFor($this->audience),
                    new SignedWith($this->getSigner(),  $this->getSignerKey()),
                ];

                if ($this->configuration->validator()->validate($token, ...$constraints)) {
                    return [
                        'sub' => $token->claims()->get('sub'),
                        'jti' => $token->claims()->get('jti'),
                    ];
                }
            }
        } catch (RequiredConstraintsViolated $e) {
            Yii::debug($e->violations());
        }catch (\Exception $e) {
            Yii::debug($e);
        }

        return [];
    }

    /**
     * @param string $accessToken
     */
    public function resolveSubject($accessToken)
    {
        try {
            $token = $this->parseToken($accessToken);
            if ($token instanceof UnencryptedToken) {
                $constraints = [
                    new IssuedBy($this->issuer),
                    new PermittedFor($this->audience),
                    new SignedWith($this->getSigner(),  $this->getSignerKey()),
                ];
                if ($this->configuration->validator()->validate($token, ...$constraints)) {
                    return $token->claims()->get('sub');
                }
            }
        } catch (RequiredConstraintsViolated $e) {
            Yii::debug($e->violations());
        }catch (\Exception $e) {
            Yii::debug($e);
        }

        return null;
    }

    /**
     * @return object|Signer
     * @throws \ReflectionException
     */
    protected function getSigner()
    {
        $ref = new ReflectionClass($this->supportedAlgorithms[$this->algorithm]);
        return $ref->newInstance();
    }

    /**
     * @return InMemory
     */
    protected function getSignerKey()
    {
        return InMemory::plainText($this->key);
    }

    /**
     * @return Configuration
     */
    protected function getConfiguration()
    {
        return $this->_configuration;
    }
}